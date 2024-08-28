<?php 

namespace APP\resource;

// listado de clases outside del namespace
use base;
//use media_file;
use api;
use x_products;
use x_product_photos;

class products {
    public $oYApp = null;

    public function __construct () {
        global $oYApp;
        $this->oYApp = $oYApp;
    }
    
    static public function get_stage_response_template ( $a_parameters = null ) {
        return array(
            "data" => array(),
            "view" => array(
                "redirect" => array(
                    "flag"  => false,
                    "value" => "",
                ),
            ),
        );
    }

    static public function request( $a_parameters = null ) {
        //echo "<pre>"; print_r( "products.request()" ); echo "</pre>";
        //echo "<pre> a_parameters: "; var_dump( $a_parameters ); echo "</pre>";
        
        $b_param_request_method = isset( $a_parameters['request_method'] ) ? trim( $a_parameters['request_method'] ) != "" : false;
        $s_param_request_method = $b_param_request_method ? trim( $a_parameters['request_method'] ) : "";

        $b_param_request_id = isset( $a_parameters['request_id'] ) ? trim( $a_parameters['request_id'] ) != "" : false;
        $s_param_request_id = $b_param_request_id ? trim( $a_parameters['request_id'] ) : "";

        $b_ctrl_request_info = $s_param_request_method == "GET" && $b_param_request_id 
            ? $s_param_request_id  == "info" : false;

        $a_method_result = array(
            "data" => array(),
            "control" => array(
                "error_flag" => false,
            ),
        );

        if (  $b_ctrl_request_info )
            $s_request_method_handler = "request_get_info";
        else if ( $s_param_request_method == "GET" )
            $s_request_method_handler = "request_get";
    
        if ( $s_param_request_method == "GET" )
            $a_method_result = self::$s_request_method_handler( $a_parameters );
        else if ( $s_param_request_method == "POST" )
            $a_method_result = self::request_post( $a_parameters );
        else if ( $s_param_request_method == "DELETE" )
            $a_method_result = self::request_delete( $a_parameters );
        else
            $a_method_result['control']['error_flag'] = true;

        return $a_method_result;
    }

    static public function request_get( $a_parameters = null ) {
        //echo "<pre>"; print_r( "products.request_get()" ); echo "</pre>";
        //echo "<pre> a_parameters: "; var_dump( $a_parameters ); echo "</pre>";
        //exit();
        
        $b_param_request_method = isset( $a_parameters['request_method'] ) ? trim( $a_parameters['request_method'] ) != "" : false;
        $s_param_request_method = $b_param_request_method ? trim( $a_parameters['request_method'] ) : "";

        $b_param_request_id = isset( $a_parameters['request_id'] ) ? trim( $a_parameters['request_id'] ) != "" : false;
        $s_param_request_id = $b_param_request_id ? trim( $a_parameters['request_id'] ) : "";

        $b_param_request_params = isset( $a_parameters['request_params'] ) ? ! empty( $a_parameters['request_params'] )  : false;
        $a_param_request_params = $b_param_request_params ? $a_parameters['request_params'] : array();
        
        $b_param_request_params_page = isset( $a_param_request_params['page'] ) ? trim( $a_param_request_params['page'] ) != "" : false;
        $i_param_request_params_page = $b_param_request_params_page ? (int) trim( $a_param_request_params['page'] ) : 1;

        $a_method_result = array(
            "data" => array(),
            "control" => array(
                "error_flag"    => false,
                "response_code" => 0,
            ),
        );
    
        $o_base = new base();

        if ( $b_param_request_id )
        {
            $s_query = "SELECT * FROM x_products WHERE id_url = '$s_param_request_id'";
        }
        else
        {
            $s_query = "SELECT COUNT(*) AS total_items FROM x_products LIMIT 0, 100";
            $a_base  = $o_base->procSent( $s_query );
            $i_total_items = (int) $a_base['aDato'][0]['total_items'];

            $i_current_page   = $b_param_request_params_page ? $i_param_request_params_page : 1;
            $i_items_per_page = 100;
            $i_total_pages    = ceil( $i_total_items / $i_items_per_page );

            $a_result_pagination = array(
                "current_page" => $i_current_page,
                "per_page"     => $i_items_per_page,
                "total_items"  => $i_total_items,
                "total_pages"  => $i_total_pages,
            );

            $i_query_limit_start = ( $i_current_page * 100 ) - 100;
            $s_query = "SELECT * FROM x_products LIMIT $i_query_limit_start, 100";
        }
        
        $a_base  = $o_base->procSent( $s_query );
        $a_products = $a_base['aDato'];
        $b_products = ! empty( $a_products );

        foreach( $a_products as $i_product_record => $a_product_record ) {
            $i_product_id = (int) $a_product_record['id'];
            
            // photos
            $s_query = "SELECT * FROM x_product_photos 
                WHERE x_product_id = $i_product_id
                ORDER BY x_product_photos.photo_order ASC";
            $a_base  = $o_base->procSent( $s_query );
            $a_photos = $a_base['aDato'];

            $a_product_record['photos'] = $a_photos;

            // quitar campos
            unset( $a_product_record['id'] );
            unset( $a_product_record['record_create_user_id'] );
            unset( $a_product_record['record_update_user_id'] );
            unset( $a_product_record['record_delete_user_id'] );

            $a_products[ $i_product_record ] = $a_product_record;
        }

        $a_method_result['data'] = $a_products;

        $a_method_result['control']['response_code'] = 200;

        if ( ! $b_param_request_id )
            $a_method_result['pagination'] = $a_result_pagination;
        
        return $a_method_result;
    }

    static public function request_post( $a_parameters = null ) {
        //echo "<pre>"; print_r( "products.request_post()" ); echo "</pre>";
        //echo "<pre> a_parameters: "; var_dump( $a_parameters ); echo "</pre>";
        //exit();
        
        $b_param_request_method = isset( $a_parameters['request_method'] ) ? trim( $a_parameters['request_method'] ) != "" : false;
        $s_param_request_method = $b_param_request_method ? trim( $a_parameters['request_method'] ) : "";

        $b_param_request_id = isset( $a_parameters['request_id'] ) ? trim( $a_parameters['request_id'] ) != "" : false;
        $s_param_request_id = $b_param_request_id ? trim( $a_parameters['request_id'] ) : "";

        $o_base = new base();
        $o_api = new api();
        $a_request_data = $o_api->get_request_data();
        $b_request_data = ! empty( $a_request_data );

        $a_method_result = array(
            "data" => array(),
            "control" => array(
                "error_flag"    => false,
                "response_code" => 0,
            ),
        );
        
        // producto - obtener registro
        if ( $b_param_request_id ) {
            $s_query = "SELECT * FROM x_products WHERE id_url = '$s_param_request_id'";
            $a_base  = $o_base->procSent( $s_query );
            $a_record = $a_base['aDato'];
            $b_record = ! empty( $a_record );
            
            $b_param_request_id = $b_record;

            if ( $b_record )
                $a_request_data['id'] = $a_record[0]['id'];
        }

        // crear registro
        $s_response_data_id_url = "";
        if ( $b_request_data ) {
            $o_product = new x_products();

            if ( $b_param_request_id )
                $a_method_create = $o_product->update( $a_request_data );
            else
                $a_method_create = $o_product->create( $a_request_data );

            $i_record_id = $b_param_request_id ? $a_request_data['id'] : $a_method_create['iIden'];

            // obtener nuevo registro
            if ( $a_method_create['bProc'] ) {
                $a_product = $o_product->read( array( "id" => $i_record_id  ));
                $s_response_data_id_url = $a_product[0]['id_url'];
            }
        }

        unset( $a_request_data['app'] );
        unset( $a_request_data['id'] );
        
        $a_method_result['data']['id_url'] = $s_response_data_id_url;

        $a_method_result['control']['response_code']  = 200;
        $a_method_result['control']['request_method'] = $s_param_request_method;
        $a_method_result['control']['request_data']   = $a_request_data;

        return $a_method_result;
    }

    static public function request_delete( $a_parameters = null ) {
        //echo "<pre>"; print_r( "products.request_delete()" ); echo "</pre>";
        //echo "<pre> a_parameters: "; var_dump( $a_parameters ); echo "</pre>";
        //exit();
        
        $b_param_request_method = isset( $a_parameters['request_method'] ) ? trim( $a_parameters['request_method'] ) != "" : false;
        $s_param_request_method = $b_param_request_method ? trim( $a_parameters['request_method'] ) : "";

        $b_param_request_id = isset( $a_parameters['request_id'] ) ? trim( $a_parameters['request_id'] ) != "" : false;
        $s_param_request_id = $b_param_request_id ? trim( $a_parameters['request_id'] ) : "";

        $a_method_result = array(
            "data" => array(),
            "control" => array(
                "error_flag"    => false,
                "response_code" => 0,
            ),
        );
    
        global $oYApp;

        $o_base = new base();
        
        $s_record_delete_date        = date("Y-m-d H:i:s");
        $s_record_delete_user_id     = 0;
        $s_record_delete_user_id_url = "";
        
        $s_query = "UPDATE x_products SET
            record_delete_date = '$s_record_delete_date'
            , record_delete_user_id = $s_record_delete_user_id
            , record_delete_user_id_url = '$s_record_delete_user_id_url'
            , record_delete_flag = 1
            WHERE id_url = '$s_param_request_id'";
        $o_base->procSent( $s_query );
        
        $a_method_result['control']['response_code'] = 200;

        return $a_method_result;
    }

    static public function request_get_info( $a_parameters = null ) {
        //echo "<pre>"; print_r( "products.request_get_info()" ); echo "</pre>";
        //echo "<pre> a_parameters: "; var_dump( $a_parameters ); echo "</pre>";
        
        $b_param_request_method = isset( $a_parameters['request_method'] ) ? trim( $a_parameters['request_method'] ) != "" : false;
        $s_param_request_method = $b_param_request_method ? trim( $a_parameters['request_method'] ) : "";

        $b_param_request_id = isset( $a_parameters['request_id'] ) ? trim( $a_parameters['request_id'] ) != "" : false;
        $s_param_request_id = $b_param_request_id ? trim( $a_parameters['request_id'] ) : "";

        $a_method_result = array(
            "data" => array(),
            "control" => array(
                "error_flag"    => false,
                "response_code" => 0,
            ),
        );
    
        $a_method_result_data =  array(
            "id_public" => "K6OS85B4IA4VS371",
            "id_url" => "0CV9XXW1R4L8NX5FI8I39VAN43C68016",
            "product_name" => "Ibuprofeno",
            "product_description" => "Antiinflamatorio no esteroideo",
            "record_create_date" => "2024-07-18 19:49:37",
            "record_create_user_id_url" => "880JWG3WRS4T737BXKKLTC8982NE5F81",
            "record_update_date" => "2024-07-18 19:50:24",
            "record_update_user_id_url" => "880JWG3WRS4T737BXKKLTC8982NE5F81",
            "record_delete_date" => null,
            "record_delete_user_id_url" => null,
            "record_delete_flag" => "0",
            "photos" => array(
                array(
                    "id_public" => "K6OS85B4IA4VS371",
                    "id_url" => "0CV9XXW1R4L8NX5FI8I39VAN43C68016",
                    "photo_name" => "mermaid-diagram.jpg",
                    "photo_url" => "http://localhost/labs/dds-client/project/upld/media_file/5/3285/mermaid-diagram.jpg",
                    "photo_order" => "1",
                    "x_product_id" => "18",
                    "record_create_date" => "2024-07-18 19:49:37",
                    "record_create_user_id" => "229",
                    "record_update_date" => "2024-07-18 19:50:24",
                    "record_update_user_id" => "229",
                    "record_delete_date" => "null",
                    "record_delete_user_id" => "null",
                    "record_delete_flag" => "0"
                )
            ),
        );

        $a_method_result_metadata =  array(
            "id_public" => array(
                "type"        => "varchar(16)",
                "value"       => "alfanummérico aleatorio",
                "description" => "Valor público user-friendly.",
                "create_date" => "2024-07-19 12:00:00",
                "update_date" => null,
                "delete_date" => null,
            ),
            "id_url" => array(
                "type"        => "varchar(32)",
                "value"       => "alfanummérico aleatorio",
                "description" => "Valor público con el que se identifica la instancia del recurso.",
                "create_date" => "2024-07-19 12:00:00",
                "update_date" => null,
                "delete_date" => null,
            ),
            "product_name" => array(
                "type"        => "varchar(255)",
                "value"       => "",
                "description" => "Nombre del producto.",
                "required"    => true,   
                "create_date" => "2024-07-19 12:00:00",
                "update_date" => null,
                "delete_date" => null,
            ),
            "product_description" => array(
                "type"        => "text",
                "value"       => "",
                "description" => "Descripción del producto.",
                "create_date" => "2024-07-19 12:00:00",
                "update_date" => null,
                "delete_date" => null,
            ),
            "record_create_date" => array(
                "type"        => "datetime",
                "value"       => "[YYYY-MM-DD HH:MM:SS]",
                "description" => "Fecha de creación de la instancia. Ejemplo: 2024-07-18 19:49:37.",
                "create_date" => "2024-07-19 12:00:00",
                "update_date" => null,
                "delete_date" => null,
            ), 
            "record_create_user_id_url" => array(
                "type"        => "varchar(32)",
                "value"       => "alfanummérico aleatorio",
                "description" => "Valor público con el que se identifica la instancia del recurso que creó el registro.",
                "create_date" => "2024-07-19 12:00:00",
                "update_date" => null,
                "delete_date" => null,
            ), 
            "record_update_date" => array(
                "type"        => "datetime",
                "value"       => "[YYYY-MM-DD HH:MM:SS]",
                "description" => "Fecha de actualización de la instancia. Ejemplo: 2024-07-18 19:49:37.",
                "create_date" => "2024-07-19 12:00:00",
                "update_date" => null,
                "delete_date" => null,
            ), 
            "record_update_user_id_url" => array(
                "type"        => "varchar(32)",
                "value"       => "alfanummérico aleatorio",
                "description" => "Valor público con el que se identifica la instancia del recurso que actualizó el registro.",
                "create_date" => "2024-07-19 12:00:00",
                "update_date" => null,
                "delete_date" => null,
            ), 
            "record_delete_date" => array(
                "type"        => "datetime",
                "value"       => "[YYYY-MM-DD HH:MM:SS]",
                "description" => "Fecha de borrado de la instancia. Ejemplo: 2024-07-18 19:49:37.",
                "create_date" => "2024-07-19 12:00:00",
                "update_date" => null,
                "delete_date" => null,
            ), 
            "record_delete_user_id_url" => array(
                "type"        => "varchar(32)",
                "value"       => "alfanummérico aleatorio",
                "description" => "Valor público con el que se identifica la instancia del recurso que borró el registro.",
                "create_date" => "2024-07-19 12:00:00",
                "update_date" => null,
                "delete_date" => null,
            ),
            "record_delete_flag" => array(
                "type"        => "tinyint",
                "value"       => "[0|1]",
                "description" => "Flag que indica el estado del borrado lógico de la instancia. 0 por default para indicar estado no borrado.",
                "create_date" => "2024-07-19 12:00:00",
                "update_date" => null,
                "delete_date" => null,
            ),
            "photos" => array(
                "type"          => "array",
                "value"         => "Objetos JSON con la estructura del recurso product-photos",
                "description"   => "Instancias de fotografías asociadas al producto",
                "documentation" => SERVER_HTTP . "product-photos/info",
                "create_date" => "2024-07-19 12:00:00",
                "update_date" => null,
                "delete_date" => null,
            ),
        );

        $a_method_result_endpoints = array(
            "GET" => array(
                "all" => array(
                    "url" => SERVER_HTTP . "products?page=1",
                ),
                "single" => array(
                    "url" => SERVER_HTTP . "products/{product_id_url}",
                ),
                "info" => array(
                    "url" => SERVER_HTTP . "products/info",
                ),
            ),
        );

        $a_method_result['data'] = $a_method_result_data;
        $a_method_result['meta'] = $a_method_result_metadata;
        $a_method_result['endpoints'] = $a_method_result_endpoints;

        $a_method_result['control']['response_code'] = 200;
        
        $a_method_result['control']['message'] = "Documentación del producto.";
        
        return $a_method_result;
    }
}