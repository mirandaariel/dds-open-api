<?php 

require SERVER_PATH.'vendor/autoload.php';

use Google\Cloud\Storage\StorageClient;

class storage {

    function __construct ( $a_parameters = null ) {}

    static public function create( $a_parameters = null ) {
        //echo "<pre>"; print_r( "storage.create()" ); echo "</pre>";
        //echo "<pre> a_parameters: "; var_dump( $a_parameters ); echo "</pre>";
        //exit();
       
        $a_method_result = array(
            "data"    => array(),
            "control" => array(),
        );

        $a_method_result = CLOUD_PLATFORM_STORAGE_FLAG 
            ? self::create_cloud( $a_parameters )
            : self::create_local( $a_parameters );

        return $a_method_result;
    } 

    static public function create_local( $a_parameters = null ) {
        //echo "<pre>"; print_r( "storage.create_local()" ); echo "</pre>";
        //echo "<pre> a_parameters: "; var_dump( $a_parameters ); echo "</pre>";
        //exit();
        
        $b_param_request_files = ! is_null( $a_parameters );
        $a_param_request_files = $b_param_request_files ? $a_parameters : array();
       
        $a_method_result = array(
            "data"    => array(),
            "control" => array(),
        );

        $a_media['nombre'] = $a_param_request_files['file']['name'];
        $a_media['aFile']  = $a_param_request_files;
        
        $o_media = new media_file();
        $a_media_create = $o_media->create( $a_media );
        $a_media = $o_media->read( array( "id" => $a_media_create['iIden'] ) );
        
        $a_method_result['data']['record_class'] = "media_file";
        $a_method_result['data']['record_id']    = $a_media_create['iIden'];
        $a_method_result['data']['file_name']    = $a_media[0]['nombre'];
        $a_method_result['data']['file_http']    = SERVER_HTTP . $a_media[0]['ruta_upload'] . $a_media[0]['nombre'];

        $a_method_result['control']['success'] = true;
        $a_method_result['control']['error'] = false;
        
        return $a_method_result;
    } 

    static public function create_cloud( $a_parameters = null ) {
        //echo "<pre>"; print_r( "storage.create_cloud()" ); echo "</pre>";
        //echo "<pre> a_parameters: "; var_dump( $a_parameters ); echo "</pre>";
        //exit();
        
        $b_param_request_files = ! is_null( $a_parameters );
        $a_param_request_files = $b_param_request_files ? $a_parameters : array();
       
        $a_method_result = array(
            "data"    => array(),
            "control" => array(),
        );

        $s_file_name = $a_param_request_files['file']['name'];
        $s_file_http = "private/".$s_file_name;

        // Google Cloud Platform - Cloud Storage
        // Crea una instancia del cliente de Storage
        $storage = new StorageClient();

        // Especifica el nombre de tu bucket
        $bucketName = 'open-api-test-env-01';
        $bucket = $storage->bucket($bucketName);
        $cloudPath = $s_file_http;

        // Subir un archivo
        $bucket->upload(
            fopen( $a_parameters['file']['tmp_name'], 'r'),
            [
                'name' => $cloudPath
            ]
        );

        // return
        $a_method_result['data']['file_name']    = $s_file_name;
        $a_method_result['data']['file_http']    = $s_file_http;
        
        $a_method_result['control']['success'] = true;
        $a_method_result['control']['error'] = false;
        
        return $a_method_result;
    } 

    static public function link( $a_parameters = null ) {
        //echo "<pre>"; print_r( "storage.link()" ); echo "</pre>";
        //echo "<pre> a_parameters: "; var_dump( $a_parameters ); echo "</pre>";
        //exit();
       
        $o_base = new base();

        $a_method_result = array(
            "data"    => array(),
            "control" => array(),
        );

        if ( ! CLOUD_PLATFORM_STORAGE_FLAG ) {
            $s_record_id            = $a_parameters['record_id'];
            $s_record_foreign_class = $a_parameters['foreign_class'];
            $s_record_foreign_key   = $a_parameters['foreign_key'];

            $s_query = "UPDATE media_file SET
                clase_foranea = '$s_record_foreign_class',
                clave_foranea = $s_record_foreign_key
                WHERE id = $s_record_id";
            $o_base->procSent( $s_query );
        }
    } 

}