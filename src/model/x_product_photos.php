<?php 

class x_product_photos extends _x_product_photosBase {
    public $oYApp = null;
    public $aLabels = [];
    public $a_relation = [];

    public function __construct () {
        global $oYApp;
        $this->aLabels = array(
            "title"         => "x_product_photo",
            "title-default" => "Entidad x_product_photo",
            "plural"        => "x_product_photo",
            "singular"      => "x_product_photo",
        );
        $this->oYApp = $oYApp;
        parent::__construct();

        $this->a_property['id']            ['s_label'] = "ID";
        $this->a_property['id_public']     ['s_label'] = "ID Público";
        $this->a_property['id_url']        ['s_label'] = "ID URL";
        $this->a_property['photo_name']    ['s_label'] = "Nombre";
        $this->a_property['photo_url']     ['s_label'] = "URL";
        $this->a_property['photo_order']   ['s_label'] = "Orden";
        $this->a_property['x_producto_id'] ['s_label'] = "Producto ID";
        $this->a_property['x_producto_id_url'] ['s_label'] = "Producto ID URL";
        
        //$this->a_property['id'] ['a_grid']['b_frozen'] = true;
        //$this->a_property['id'] ['a_grid']['b_hidden'] = true;
       
        //$this->a_property['usuario_id']['a_relation']['s_entity']    = "usuario";
        //$this->a_property['usuario_id']['a_relation']['s_property']  = "id";
        //$this->a_property['usuario_id']['a_relation']['a_replace'][] = "nombre";
        
        //$this->a_property['ph_establecimiento_id']['a_list'] = array( "show" => false, );
        //$this->a_property['id_public']            ['a_list'] = array( "label" => "Public ID", );
        
        $this->aBase['a_dependencies'] = array();

        $this->a_relation = array(
            //"ph_establecimiento_id" => array( "id_public", "nombre", ),
        );
    }

    public function obteRela ( $aValo = null ) {
        $aInst = $aValo;
        
        /*
        foreach( $aInst as $iInstPosi => $aInstValo )
        {
            // obtener las imagenes
            $oInst = new media_imagen();
            $oInst->aBase['aFilt'][] = "media_imagen.clase_foranea = 'content_client'";
            $oInst->aBase['aFilt'][] = "media_imagen.clave_foranea = '" . $aInstValo[ 'id' ] . "'";
            $aInstValo['media_imagen'] = $oInst->find();

            $aInst[ $iInstPosi ] = $aInstValo;
        }
        */
        return $aInst;
    }

    public function output_format ( $a_parameters = null ) {
        
        $a_parameters = parent::output_format( $a_parameters );
        
        $aInst = $a_parameters;
        
        /*
        foreach( $aInst as $iInstPosi => $aInstValo )
        {   
            $aInst[ $iInstPosi ] = $aInstValo;
        }
        */
        
        return $aInst;
    }

    public function create( $a_parameters = null ) {
        //var_dump( "x_product_photo.create" );
        //var_dump( $a_parameters );
        
        $a_parameters['record_create_date']        = date("Y-m-d H:i:s");
        $a_parameters['record_create_user_id']     = 0;
        $a_parameters['record_create_user_id_url'] = "";

        $a_parameters = parent::create( $a_parameters );

        // controlar la existencia de una keyword
        $i_id = $a_parameters['iIden'];
        
        // generacion codigo aleatorio con control valor unico
        $this->oYApp->instancia_codigo_aleatorio( array(
            "codigo_longitud" => 32,
            "entidad_nombre"  => "x_product_photos",
            "entidad_campo"   => "id_url",
            "instancia_id"    => $i_id,
        ) );

        $this->oYApp->instancia_codigo_aleatorio( array(
            "codigo_longitud" => 16,
            "entidad_nombre"  => "x_product_photos",
            "entidad_campo"   => "id_public",
            "instancia_id"    => $i_id,
        ) );

        return $a_parameters;
    }

    public function update( $a_parameters = null ) {
        //var_dump( "x_product_photos.create" );
        //var_dump( $a_parameters );
        
        $a_parameters['record_update_date']        = date("Y-m-d H:i:s");
        $a_parameters['record_update_user_id']     = 0;
        $a_parameters['record_update_user_id_url'] = "";

        $a_parameters = parent::update( $a_parameters );

        return $a_parameters;
    }

    public function delete( $a_parameters = null ) {
        //var_dump( "x_product_photos.create" );
        //var_dump( $a_parameters );
        
        $o_base = new base();

        $i_param_id = (int) $a_parameters['id'];

        $s_record_delete_date        = date("Y-m-d H:i:s");
        $s_record_update_user_id     = 0;
        $s_record_update_user_id_url = "";
        
        $s_query = "UPDATE x_product_photos SET
            record_delete_date = '$s_record_delete_date'
            , record_delete_user_id = $s_record_update_user_id
            , record_delete_user_id_url = '$s_record_update_user_id_url'
            , record_delete_flag = 1
            WHERE id = $i_param_id;";
        $o_base->procSent( $s_query );

        //$a_parameters = parent::update( $a_parameters );

        return $a_parameters;
    }


    static public function get_crud_actions( $a_parameters = null ) {
        
        $o = new x_product_photo();
        
        $s_entiy_singular = $o->aLabels['singular'];

        $s_href_update_default = FMWK_CLIE_SERV . "crud/form/";
        $s_href_delete_default = FMWK_CLIE_SERV . "crud/_actions/delete/";

        $b_href_create = isset( $a_parameters['href_create'] );
        $s_href_create = $b_href_create ? $a_parameters['href_create'] : $s_href_update_default;

        $b_href_update = isset( $a_parameters['href_update'] );
        $s_href_update = $b_href_update ? $a_parameters['href_update'] : $s_href_update_default;

        if ( ! $b_href_update && $b_href_create )
            $s_href_update = $a_parameters['href_create'];

        $b_href_delete = isset( $a_parameters['href_delete'] );
        $s_href_delete = $b_href_delete ? $a_parameters['href_delete'] : $s_href_delete_default;

        return array(
            "show"  => true,
            "items" => array(
                "create" => array(
                    "label"   => "Crear nuevo $s_entiy_singular",
                    "href"    => $s_href_create,
                    "image"   => "",
                    "divider" => false,
                ),
                "update" => array(
                    "label"   => "Editar",
                    "href"    => $s_href_update,
                ),
                "delete" => array(
                    "label"   => "Eliminar",
                    "href"    => $s_href_delete,
                ),
            ),
        );
    }

    static public function get_list_config( $a_parameters = null ) {
        $o = new x_product_photo();
        $a_head = array();

        $a_crud_actions = x_product_photo::get_crud_actions( $a_parameters );
        
        foreach ( $o->a_property as $s_property_name => $a_property_config ) 
            $a_head[ $s_property_name ] = $a_property_config['a_list'];
    
        //echo "<pre>"; var_dump( $a_head ); echo "</pre>"; 
        //exit();

        $a_head["nombre"]['label'] ="Establecimiento";

        $a_list_config = array(
            "head" => $a_head,
            "body_actions" => array(
                "update" => $a_crud_actions['items']['update'],
                "delete" => $a_crud_actions['items']['delete'],
                /*
                "Descuento" => array(
                    "label" => "Clientes",
                    "href"  => $a_parameters['module_http'] . "main/x_product_photo_cliente?place=",
                ),
                */
            ),
            "data" => array(
                "entity" => "x_product_photo",
            ),
            "info" => array(
                "entity" => array(
                    "singular" => $o->aLabels['singular'],
                    "plural"   => $o->aLabels['plural'],
                ),
            ),
        );

        return $a_list_config;
    }

    public static function get_all( $a_parameters = null ) {
        
        $b_order = isset( $a_parameters['order'] );
        $a_order = $b_order ? $a_parameters['order'] : array();

        $o_cliente = new x_product_photo();
        //$o_cliente->aBase['aFilt'][] = "x_cliente.flag_habilitado = 1";
        //$o_cliente->aBase['aFilt'][] = "x_cliente.flag_borrado = 0";
        //$o_cliente->aBase['aFilt'][] = "x_cliente.flag_test_user = 0";

        if ( $b_order )
            $o_cliente->aBase['aOrde'] = $a_order;

        $a_cliente = $o_cliente->find();
        return $a_cliente;
    }

    public static function get_dropdown_config_data( $a_parameters = null ) {
        $a_form_clientes = array();

        $b_control_key = isset( $a_parameters['control'] );
        $b_data_key    = isset( $a_parameters['data'] );

        $a_control_param = $b_control_key ? $a_parameters['control'] : array();
        $a_data_param    = $b_data_key ? $a_parameters['data'] : $a_parameters;
    
        $b_option_all = $b_control_key && isset( $a_parameters['control']['all'] ) ?
            $a_parameters['control']['all'] : true;

        if ( count( $a_data_param ) > 1 && $b_option_all )
            $a_form_clientes[] = array(
                "s_label"    => "Todos",
                "s_value"    => 0,
                "s_selected" => "selected"
            );
        
        foreach ( $a_data_param as $i_instancia => $a_instancia ) 
            $a_form_clientes[] = array(
                "s_label" => self::get_instance_label( $a_instancia ),
                "s_value" => $a_instancia['id'],
            );
        
        return $a_form_clientes; 
    }

    public static function get_instance_label( $a_parameters = null ) {
        $s_label = "";
        
        $s_label = $a_parameters['nombre'];
        
        return $s_label;
    }

    public static function report_data ( $a_parameters = null ) {
        //echo "<pre>"; print_r( "ph_cliente::report_data()" ); echo "</pre>";
        //echo "<pre> a_parameters: "; var_dump( $a_parameters ); echo "</pre>";
        //exit();

        $o_base = new base();

        $a_function_result = array(
            "data"    => array(),
            "control" => array(),
        );

        $a_function_ctrl_items = array();
        $a_function_ctrl_ids   = array();
        
        // ordenes de compra version actual
        $s_query = "SELECT
            x_product_photos.*
            , x_products.id AS product_id 
            , x_products.product_name 
            FROM x_product_photos
            LEFT OUTER JOIN x_products ON x_products.id = x_product_photos.x_product_id
            WHERE x_product_photos.record_delete_flag = 0
            ORDER BY x_product_photos.id ASC";

        $a_base = $o_base->procSent( $s_query );
        $a_clients = $a_base['aDato'];
        $b_clients = ! empty( $a_clients );

        foreach( $a_clients as $i_record => $a_record )
        {
            $s_id_url = $a_record['id_url'];
            
            $a_record['record_id'] = $s_id_url;

            $a_function_ctrl_items[] = $a_record;

            if ( ! in_array( $i_ctrl_client_id, $a_function_ctrl_ids ))
                $a_function_ctrl_ids[] = $i_ctrl_client_id;
        }

        // devolucion
        $a_functio_result['data']['items'] = $a_function_ctrl_items;
        $a_functio_result['data']['ids']   = $a_function_ctrl_ids;
        
        return $a_functio_result;
    }

    public static function report_items ( $a_parameters = null ) {
        //echo "<pre>"; print_r( "ph_cliente::report_items()" ); echo "</pre>";
        //echo "<pre> a_parameters: "; var_dump( $a_parameters ); echo "</pre>";
        //exit();

        $o_base = new base();

        $a_function_result = array(
            "data"    => array(),
            "control" => array(),
        );

        $a_function_ctrl_items = array();
        
        $a_script_report_orders_data = self::report_data();
        $a_orders = $a_script_report_orders_data['data']['items'];

        foreach( $a_orders as $i_order_pos => $a_order_record )
        {
            $b_discount_ctrl_global = $a_order_record['discount_ctrl_global'] == "1";
            $s_place_name = $b_discount_ctrl_global ? "Global" : $a_order_record['place_name'];

            $s_discount_rule_items = $a_order_record['discount_rule_items_flag'] == "1" ? "Si" : "No";
            $s_discount_percentage = $a_order_record['discount_percentage_flag'] == "1" ? "Si" : "No";
            $s_discount_amount     = $a_order_record['discount_amount_flag'] == "1" ? "Si" : "No";

            /*
            $b_ctrl_order_paid        = $a_order_record['order_paid'] == "1";
            $b_ctrl_order_download    = $a_order_record['order_download'] == "1";
            $b_ctrl_order_disabled    = $a_order_record['order_disabled_flag'] == "1";
            
            $f_ctrl_order_price_total = (float) $a_order_record['order_price_total'];
            $s_ctrl_order_price_total = number_format( $f_ctrl_order_price_total, 2, ".", "," );
            
            $s_ctrl_order_download = $b_ctrl_order_download ? "Si" : "No";
            
            $s_ctrl_order_paid     = $b_ctrl_order_paid ? "Pagada" : "Pendiente";
            if ( $b_ctrl_order_disabled )
                $s_ctrl_order_paid = "Anulada";

            $a_order_record['order_download_text']    = $s_ctrl_order_download;
            $a_order_record['order_paid_text']        = $s_ctrl_order_paid;
            $a_order_record['order_price_total_text'] = $s_ctrl_order_price_total;
            */

            $a_order_record['place_name'] = $s_place_name;
            $a_order_record['discount_rule_items_text'] = $s_discount_rule_items;
            $a_order_record['discount_percentage_text'] = $s_discount_percentage;
            $a_order_record['discount_amount_text']     = $s_discount_amount;

            $a_function_ctrl_items[] = $a_order_record;
        }

        // devolucion
        $a_functio_result['data']['items'] = $a_function_ctrl_items;

        return $a_functio_result;
    }
}