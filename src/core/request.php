<?php 

/*
    Cuando se pase datos para una peticion POST dentro del array data los valores de las clases no 
    pueden ser array. En tal caso se puede pasar un string que represente un JSON que debera ser 
    parseado a array en el script destino.
    
    $a_request_params = array(
        "method" => "POST",
        "url"    => $s_request_url.$s_method,
        "data"   => array(
            "params" => $s_params,
        ),
    );
*/

class request {

    static public function send( $a_parameters = null ){
        //echo "<pre>"; print_r( "request::send()" ); echo "</pre>";
        //echo "<pre>"; var_dump( $a_parameters ); echo "</pre>";

        // variables
        $s_result  = "";
        $a_result  = array(
            "error"  => null,
            "result" => null,
        );

        $b_error = false;
        $a_error = array(
            "flag"        => $b_error,
            "description" => "",
        );
        
        $s_url      = isset( $a_parameters['url'] ) ? $a_parameters['url'] : "";
        $s_method   = isset( $a_parameters['method'] ) ? $a_parameters['method'] : "";
        $a_data     = isset( $a_parameters['data'] ) ? $a_parameters['data'] : array();
        $a_headers  = isset( $a_parameters['headers'] ) ? $a_parameters['headers'] : array();
        
        $s_return_type = isset( $a_parameters['return_type'] ) ? $a_parameters['return_type'] : "JSON";

        $b_send_type = isset( $a_parameters['send_type'] );
        $s_send_type = $b_send_type ? $a_parameters['send_type'] : "";

        $b_headers = ! empty( $a_headers );

        // controlar post data
        if ( $s_method == "POST" )
        {
            $a_post_data = $a_data;

            if ( $b_send_type )
            {
                if ( $s_send_type == "JSON" )
                    $a_post_data = json_encode( $a_data );
            }
        }
        
        // controlar si el array de cabeceras es asociativo
        if ( array_keys( $a_headers ) !== range(0, count( $a_headers ) - 1))
        {   
            $a_headers_auxi = array();
            foreach( $a_headers as $s_header_key => $x_header_value)
                $a_headers_auxi[] = $s_header_key.": ".$x_header_value;
            $a_headers = $a_headers_auxi;
        }
        
        // GET construir los parametros en la url
        $a_url_params = array();
        $b_url_params = $s_method == "GET" && !empty( $a_data );
        if ( $b_url_params )
            foreach ( $a_data as $s_key => $s_value )
                $a_url_params[] = $s_key."=".$s_value;
        $s_url_params = $b_url_params ? "?".implode("&", $a_url_params) : "";
        $s_url .= $s_url_params; 

        //Initiate cURL.
        $ch = curl_init( $s_url );

        if ( FMWK_AMBI_DESA ) {
            // Añade estas líneas antes de curl_exec($ch)
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
    
        //Tell cURL that we want to send a POST request.
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $s_method);
        curl_setopt($ch, CURLOPT_POSTREDIR, 3);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    
        // SSL isues
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        //Attach our encoded JSON string to the POST fields.
        if ( $s_method == "POST" )
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($a_post_data));
            //curl_setopt($ch, CURLOPT_POSTFIELDS, $a_post_data);
    
        //Set the content type to application/json
        if ( $b_headers )
            curl_setopt($ch, CURLOPT_HTTPHEADER, $a_headers);
        
        // controlar si el retorno es un archivo
        if ( $s_return_type == "file" )
        {
            $fp = fopen ( $a_parameters['return_file'] , 'w+');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        }
        else if ( $s_return_type == "file_zip" )
        {
            $fp = fopen ( $a_parameters['return_file'] , 'w+');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            // la linea anterior indica que se guarde directamente en el archivo y no en memoria.
            //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        }

        //Execute the request
        $s_result = curl_exec($ch);
        //echo "<pre>"; var_dump( $s_result ); echo "</pre>";
        
        if( $s_result === false)
        {
            //var_dump( $s_result );
            //echo 'Curl error: ' . curl_error($ch) . "<br/>";

            $b_error = true;
            $a_error['flag'] = $b_error;
            $a_error['description'] = curl_error($ch);
        }
        
        if ( ! $b_error )
        {   
            // 2023.11.07 - file_raw se utiliza para descargas por ejemplo en dropbox
            // pero no ha surgido el problema de interrupcion del script por error fatal causa del 
            // consumo de memoria que puede ser solucionado con el tipo file_zip
            if ( $s_return_type == "file_raw" )
            {
                file_put_contents( $a_parameters['return_file'], $s_result );
            }

            if ( $s_return_type == "JSON")
                $a_result['result'] = json_decode($s_result, true );
            else if ( $s_return_type == "file")
                $a_result['result'] = "file";
            else
                $a_result['result'] = $s_result;
        }
        
        $a_result['error'] = $a_error;

        return $a_result;
    }
}