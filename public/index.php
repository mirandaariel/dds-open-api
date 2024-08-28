<?php

/*/ debug

    echo "<pre>"; var_dump( __FILE__ ); echo "</pre>";
    echo "<pre> a_app_request_uri_path: "; var_dump( $a_app_request_uri_path ); echo "</pre>";
    echo "<pre> a_app_request_uri_params: "; var_dump( $a_app_request_uri_params ); echo "</pre>";
    //*/

// requeries

    declare(strict_types=1);

    use Auth0\SDK\Auth0;
    use Auth0\SDK\Configuration\SdkConfiguration;
    use Auth0\SDK\Exception\StateException;
    use Auth0\SDK\Utility\PKCE;

    
// variables
    
    $o_app_core_api = new api();    


// get data

        
    $s_request_method = $o_app_core_api->get_request_method();

    $s_request_resource = str_replace( "-", "_", $a_app_request_uri_path[0] );
    $b_request_resource = method_exists( $o_app_core_api, $s_request_resource );

    $b_request_id = isset( $a_app_request_uri_path[1] );
    $s_request_id = $b_request_id ? $a_app_request_uri_path[1] : "";

    // auth token
    function getBearerToken() {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { // Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
    $token = getBearerToken();

    /*
    echo "<pre> s_request_method: ";   var_dump( $s_request_method );   echo "</pre>";        
    echo "<pre> s_request_resource: "; var_dump( $s_request_resource ); echo "</pre>";        
    echo "<pre> b_request_resource: "; var_dump( $b_request_resource ); echo "</pre>";        
    echo "<pre> s_request_id: ";       var_dump( $s_request_id );       echo "</pre>";        
    echo "<pre> _FILES: ";             var_dump( $_FILES );             echo "</pre>";        
    exit();
    //*/

// logic

    // Auth0
    $configuration = new SdkConfiguration(
        strategy: SdkConfiguration::STRATEGY_API,
        domain: 'dev-dtz8z57wa7gmx24d.us.auth0.com',
        clientId: 'Ol8ft2iuYujZvb0PmTePifiwn0gYQfza',
        clientSecret: '4Qzz8SpEoE9ml4getps-LJgB9Xbz5XCh49AJs4OdpAa6HHsJHWJ345WA13tgkHlj',
        audience: ['https://dev-dtz8z57wa7gmx24d.us.auth0.com/api/v2/']
    );

    $auth0 = new Auth0($configuration);

    $jwt = $token;

    $b_token_decode = false;
    if ($jwt !== null) {
    
        $jwt = trim($jwt);

        // Remove the 'Bearer ' prefix
        if (substr($jwt, 0, 7) === 'Bearer ') {
            $jwt = substr($jwt, 7);
        }

        // decode the token
        try {
            $token_decode   = $auth0->decode($jwt, null, null, null, null, null, null, \Auth0\SDK\Token::TYPE_TOKEN);
            $b_token_decode = true;
        } catch (\Auth0\SDK\Exception\InvalidTokenException $exception) {
            //die($exception->getMessage());
            $o_app_core_api->send_400();
        }
    }
    
    define('ENDPOINT_AUTHORIZED', $b_token_decode );
    
    //echo "<pre> b_token_decode: ";   var_dump( $b_token_decode );   echo "</pre>";
    //echo "<pre> auth0: ";   var_dump( $auth0 );   echo "</pre>";
    //echo "<pre> jwt: ";   var_dump( $jwt );   echo "</pre>";
    //echo "<pre> token_decode: ";   var_dump( $token_decode );   echo "</pre>";
    //echo "<pre> session: ";   var_dump( $session );   echo "</pre>";
    //exit(); 

    // resource - send request
    if ( $b_request_resource && ENDPOINT_AUTHORIZED )
        $o_app_core_api->$s_request_resource( array(
            "request_id"     => $s_request_id,
            "request_params" => $a_app_request_uri_params,
            "request_files"  => $_FILES,
        ));

    // error
    if ( ! $b_request_resource || ! ENDPOINT_AUTHORIZED )
        $o_app_core_api->send_400();