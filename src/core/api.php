<?php

class api {
    
    public $id = null;
    public $action = null;
    public $class_folder = null;

    public $a_status_code_group_key = array("base", "header");

    public $a_status_code = array(
        "200" => array(
            "base" => array(
                "Type"        => "Success",
                "Status"      => 200,
                "Message"     => "Ok",
                "Description" => "HTTP response success",
                "data"        => array(),
                "meta"        => array(),
                "endpoints"   => array(),
                "control"     => array(),
                "pagination"  => array(),
            ),
            "header" => array(),
        ),
        "400" => array(
            "base" => array(
                "Type"        => "Client Error",
                "Status"      => 400,
                "Message"     => "Bad Request",
                "Description" => "HTTP response for the request by the client was not processed, as the server could not understand what the client is asking for",
                "Required"    => array(),            ),
            "header" => array(),
        ),
        "404" => array(
            "base" => array(
                "Type"        => "Client Error",
                "Status"      => 404,
                "Message"     => "Not Found",
                "Description" => "HTTP response for the requested resource is not available to access",
                "control"     => "",
            ),
            "header" => array(),
        ),
        "405" => array(
            "base" => array(
                "Type"        => "Client Error",
                "Status"      => 405,
                "Message"     => "Method Not Allowed",
                "Description" => "The request method is known by the server but is not supported by the target resource.",
            ),
            "header" => array(
                "Allow" => "GET, POST, HEAD",
            ),
        )
    );

    public function __construct() {}
    
    public function test() {
        
        $s_request_method = $this->get_request_method();

        if ( $s_request_method == "GET" ) 
        {
            $a_response_data    = array(
                "title" => "Title Test",
                "description" => "Description test"
            );
            
            $a_response_control = array(
                "request_method" => $s_request_method,
            );

            $this->send_200( array( 
                "data"    => $a_response_data,
                "control" => $a_response_control 
            )); 
        }
        else
        {
            $a_response_control = array(
                "request_method" => $s_request_method,
            );

            $this->send_404( array( 
                "control" => $a_response_control 
            ));
        }
    }

    public function products( $a_parameters = null ) {
        
        //echo "<pre> a_parameters: "; print_r( $a_parameters ); echo "<pre>";
        //exit();

        $s_request_method = $this->get_request_method();

        $b_param_request_id = isset( $a_parameters['request_id'] ) ? trim( $a_parameters['request_id'] ) != "" : false;
        $s_param_request_id = $b_param_request_id ? trim( $a_parameters['request_id'] ) : "";

        $b_param_request_params = isset( $a_parameters['request_params'] ) ? ! empty( $a_parameters['request_params'] ) : false;
        $a_param_request_params = $b_param_request_params ? $a_parameters['request_params'] : array();
        //echo "<pre> a_param_request_params: "; var_dump( $a_param_request_params ); echo "<pre>";

        $a_response_default = array(
            "request_method" => $s_request_method,
        );

        $a_request_result = \APP\resource\products::request( array(
            "request_method" => $s_request_method,
            "request_id"     => $s_param_request_id,
            "request_params" => $a_param_request_params,
        ));
        //echo "<pre> a_request_result: "; var_dump( $a_request_result ); echo "<pre>";

        if ( $a_request_result['control']['response_code'] == 200 )
            $this->send_200( $a_request_result );         
        else
            $this->send_404( array( 
                "control" => $a_response_default 
            ));
    }

    public function product_photos( $a_parameters = null ) {
        
        //echo "<pre> a_parameters: "; print_r( $a_parameters ); echo "<pre>";
        //exit();

        $s_request_method = $this->get_request_method();

        $b_param_request_id = isset( $a_parameters['request_id'] ) ? trim( $a_parameters['request_id'] ) != "" : false;
        $s_param_request_id = $b_param_request_id ? trim( $a_parameters['request_id'] ) : "";
        
        $b_param_request_files = isset( $a_parameters['request_files'] ) ? ! empty( $a_parameters['request_files']  ) : false;
        $a_param_request_files = $b_param_request_files ? $a_parameters['request_files'] : array();

        $a_response_default = array(
            "request_method" => $s_request_method,
        );

        $a_request_result = \APP\resource\product_photos::request( array(
            "request_method" => $s_request_method,
            "request_id"     => $s_param_request_id,
            "request_files"  => $a_param_request_files,
        ));
        //echo "<pre> a_request_result: "; var_dump( $a_request_result ); echo "<pre>";

        if ( $a_request_result['control']['response_code'] == 200 )
            $this->send_200( $a_request_result ); 
        else
            $this->send_404( array( 
                "control" => $a_response_default 
            ));
    }

    public function send( $x_code, $a_parameters ) {
        // get default status code data
        // verify status code data from $a_parameters
        $a_status_code = $this->update_status_code($x_code, $a_parameters);

        // send header to the response
        $this->output_headers($a_status_code);

        // send JSON to the resonse
        $this->output_json($a_status_code);

        exit();
    }

    public function send_200( $a_parameters = null ) {
        $this->send(200, $a_parameters);
    }

    public function send_400( $a_parameters = null ) {
        $this->send(400, $a_parameters);
    }

    public function send_404( $a_parameters = null ) {
        $this->send(404, $a_parameters);
    }

    public function send_405( $a_parameters = null ) {
        $this->send(405, $a_parameters);
    }

    function send_request( $a_parameters = null ){
		
        // variables
        $s_result  = "";
        $a_result  = array();
        
        $s_url      = isset( $a_parameters['url'] ) ? $a_parameters['url'] : "";
        $s_method   = isset( $a_parameters['method'] ) ? $a_parameters['method'] : "";
        $s_function = isset( $a_parameters['function'] ) ? $a_parameters['function'] : "";
        $a_data     = isset( $a_parameters['data'] ) ? $a_parameters['data'] : array();
        $a_headers  = isset( $a_parameters['headers'] ) ? $a_parameters['headers'] : array();
        
        $b_headers = ! empty( $a_headers );
        
        //var_dump( $s_url );
        //var_dump( $a_data );

        //$s_data     = json_encode( $a_data );
        
        //Initiate cURL.
        $ch = curl_init( $s_url );
    
        //Tell cURL that we want to send a POST request.
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $s_method);
        curl_setopt($ch, CURLOPT_POSTREDIR, 3);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    
        //Attach our encoded JSON string to the POST fields.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $a_data);
    
        //Set the content type to application/json
        if ( $b_headers )
            curl_setopt($ch, CURLOPT_HTTPHEADER, $a_headers);
    
        //Execute the request
        $s_result = curl_exec($ch);
    
        if( $s_result === false)
        {
            var_dump( $s_result );
            echo 'Curl error: ' . curl_error($ch) . "<br/>";
        }
        
        $a_result = json_decode($s_result, true );
        
        //var_dump( $s_result );
        //var_dump( $a_result );
        //var_dump( $_COOKIE );
    
        return $a_result;
    }

    public function output_headers( $a_status_code ) {
        
        // prepare header response
        $s_status  = $a_status_code['base']['Status'];
        $s_message = $a_status_code['base']['Message'];

        header("HTTP/1.0 $s_status $s_message");
        header('Content-type: application/json');

        foreach( $a_status_code['header'] as $s_key => $s_value )
            header("$s_key: $s_value");
    }

    public function output_json( $a_status_code ) {
        $a_result = array_merge( $a_status_code['base'], $a_status_code['header'] );
        $s_result = json_encode( $a_result );

        echo $s_result;
    }

    public function update_status_code ( $x_code, $a_parameters ) {

        // get default status code data
        $a_status_code = $this->get_status_code_data( $x_code );

        // verify status code data from $a_parameters
        foreach( $this->a_status_code_group_key as $i_group_key => $s_group_key )
        {   
            foreach( $a_status_code[ $s_group_key ] as $s_key => $s_value )
            {
                if ( isset( $a_parameters[ $s_key ] ) )
                    $a_status_code[ $s_group_key ][ $s_key ] = $a_parameters[ $s_key ];
            }
        }
        
        return $a_status_code;
    }

    public function get_status_code_data ( $x_code ) {
        $s_code = is_int( $x_code ) ? "$x_code" : $x_code;
        return $this->a_status_code[ $s_code ];
    }

    public function get_request_method() {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function get_request_data() {
        //var_dump("get_request_data");
        /*
        var_dump( $_POST );
        var_dump( file_get_contents('php://input') );
        $putdata = file_get_contents("php://input");
        parse_str($putdata, $putParams);
        var_dump(  $putParams );
        $product_name = isset($putParams['product_name']) ? $putParams['product_name'] : null;
        var_dump(  $product_name );
        */

        $a_data = array();
        
        $s_request_method = $this->get_request_method();
        
        if ( ! is_null( $this->id ) && $s_request_method == "GET" )
        {
            //var_dump( 1 );
            // Method GET
            $a_data = array( "id" => $this->id );
        }
        if ( ! is_null( $this->id ) && $s_request_method == "POST" )
        {
            //var_dump( 2 );
            $a_data = $_POST;
            $a_data['id'] = $this->id;
        }
        else if ( ! empty( $_POST ) )
        {
            //var_dump( 3 );
            // Request Header Content-Type: application/x-www-form-urlencoded
            $a_data = $_POST;
        }
        else
        {
            //var_dump( 4 );
            
            // Request Header Content-Type: application/json
            $s_data_json = file_get_contents('php://input');
            //var_dump( $s_data_json );
            //var_dump( $s_request_method );

            if ( $s_request_method == "POST" ) {
                $a_data = json_decode( $s_data_json, true );
                //echo "<pre>";var_dump($a_data);echo "</pre>";
            } else {

                $a_data_json = explode("&", $s_data_json );
                //var_dump( $a_data_json );

                foreach( $a_data_json as $s_key_value )
                {
                    $a_key_value = explode("=", $s_key_value);
                    
                    if ( isset( $a_data[ $a_key_value[0] ] ) )
                        $a_data[ $a_key_value[0] ] = $a_key_value[1];
                }
            }
        }
        //var_dump( $a_data );
        return $a_data;
    }
}