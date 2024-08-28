<?php

/*/ debug

    echo "<pre>"; var_dump( __FILE__ ); echo "</pre>";
    echo "<pre> a_app_request_uri_path: "; var_dump( $a_app_request_uri_path ); echo "</pre>";
    echo "<pre> a_app_request_uri_params: "; var_dump( $a_app_request_uri_params ); echo "</pre>";
    //*/

// logic

    // log only in develop env
    if ( FMWK_AMBI_DESA ) {
        //echo "<pre>"; var_dump( "logs" ); echo "</pre>";

        $filename = SERVER_PATH."public/callback/logs.txt";
        
        $somecontent = "test ".date("YmdHis")."\n";
        foreach( $a_app_request_uri_params as $s_rquest_uri_param_key => $s_rquest_uri_param_value ) {
            $somecontent .= "    ".$s_rquest_uri_param_key." = ".$s_rquest_uri_param_value.";\n";
        }

        $handle = fopen( $filename, "a+");
        if (fwrite($handle, $somecontent) === FALSE) {
            echo "Cannot write to file ($filename)";
            exit;
        }
    }

    // auth0 - authorization code
    $b_auth0_authorization_code = isset( $a_app_request_uri_params['code'] );
    $s_auth0_authorization_code = $b_auth0_authorization_code ? $a_app_request_uri_params['code'] : "";

    // auth0 - ask for tokens
    if ( $b_auth0_authorization_code ) {   
        
        $a_params = array(
            "grant_type"    => "authorization_code",
            "client_id"     => "8oEdipilpYRwuDyNWu5tnfDdkHhkBeZ5",
            "client_secret" => "R31Jg_7C39faVs459cusYSDDaoHttTlPDBIqNXHhGrfjAzKBkPqxppAvMjTA8J8N",
            "code"          => $s_auth0_authorization_code,
            "redirect_uri"  => SERVER_HTTP . "callback",
        );
        
        $s_request_url = "https://dev-dtz8z57wa7gmx24d.us.auth0.com/oauth/token";

        $a_request_params = array(
            "method" => "POST",
            "url"    => $s_request_url,
            "data"   => $a_params,
            "headers" => array(
                "Content-Type: application/x-www-form-urlencoded",
            ),
        );
        
        $a_request_result = request::send( $a_request_params );

        $somecontent = "";
        foreach( $a_request_result['result'] as $s_result_key => $s_result_value ) {
            echo "<pre> $s_result_key: "; print_r( $s_result_value ); echo "</pre>";
            $somecontent .= "    ".$s_result_key." = ".$s_result_value.";\n";
        }

        if ( FMWK_AMBI_DESA ) {
            if (fwrite($handle, $somecontent) === FALSE) {
                echo "Cannot write to file ($filename)";
                exit;
            }
        }
    }