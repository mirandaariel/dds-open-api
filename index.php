<?php

// debug gcp deploy
  
  //$files1 = scandir( dirname( __FILE__ ) );
  //echo "<pre>"; var_dump( __FILE__ ); echo "</pre>";
  //echo "<pre> dir: "; var_dump( $dir ); echo "</pre>";
  //echo "<pre> files1: "; var_dump( $files1   ); echo "</pre>";
  //echo "<pre>"; var_dump( $_SERVER ); echo "</pre>";
  //phpinfo();
  //echo "<pre> a_app_request_uri_path: "; var_dump( $a_app_request_uri_path ); echo "</pre>";
  //exit();
  //*/


// includes

  // config file
  $s_settings_file_path = dirname( __FILE__ ) . "/structure_settings.php";
  $b_settings_file_path = file_exists( $s_settings_file_path );
  //echo "<pre> s_settings_file_path: "; var_dump( $s_settings_file_path ); echo "</pre>";
  //echo "<pre> b_settings_file_path: "; var_dump( $b_settings_file_path ); echo "</pre>";

  include_once( $s_settings_file_path );
  //echo "<pre> SERVER_PATH: "; var_dump( SERVER_PATH ); echo "</pre>";
  
  // core classes
  include_once( SERVER_PATH . "src/core/conf.php" );
  include_once( SERVER_PATH . "src/core/fmwkconf.php" );
  include_once( SERVER_PATH . "src/core/fmwkimag.php" );
  include_once( SERVER_PATH . "src/core/base.php" );
  include_once( SERVER_PATH . "src/core/clas.php" );
  include_once( SERVER_PATH . "src/core/app.php" );
  include_once( SERVER_PATH . "src/core/api.php" );
  include_once( SERVER_PATH . "src/core/storage.php" );
  include_once( SERVER_PATH . "src/core/request.php" );
  
  // model
  include_once( SERVER_PATH . "src/model/basemodel/_media_fileBase.php" );
  include_once( SERVER_PATH . "src/model/basemodel/_x_productsBase.php" );
  include_once( SERVER_PATH . "src/model/basemodel/_x_product_photosBase.php" );
  include_once( SERVER_PATH . "src/model/media_file.php" );
  include_once( SERVER_PATH . "src/model/x_products.php" );
  include_once( SERVER_PATH . "src/model/x_product_photos.php" );
  
  // resource classes
  include_once( SERVER_PATH . "src/resource/products.php" );
  include_once( SERVER_PATH . "src/resource/product_photos.php" );
  

// variables

  if ( SERVER_ROOT != "/" )
    $s_app_request_uri = str_replace( SERVER_ROOT, "", $_SERVER['REQUEST_URI'] );
  else 
    $s_app_request_uri = substr( $_SERVER['REQUEST_URI'], 1 );
  
  $a_app_request_uri = explode( "?", $s_app_request_uri );

  $s_app_request_uri_path = $a_app_request_uri[0];
  $a_app_request_uri_path = explode( "/", $s_app_request_uri_path );
  $b_app_request_uri_path = ! empty( $a_app_request_uri_path );
  $i_app_request_uri_path = count( $a_app_request_uri_path );

  $b_app_request_uri_params = isset( $a_app_request_uri[1] );
  $s_app_request_uri_params = $b_app_request_uri_params ? $a_app_request_uri[1] : "";
  $a_app_request_uri_pairs  = $b_app_request_uri_params ? explode( "&", $s_app_request_uri_params ) : array();
  
  $a_app_request_uri_params = array();
  foreach( $a_app_request_uri_pairs as $i_pair_pos => $s_pair_value ) {
    $a_pair_value = explode( "=", $s_pair_value );
    $a_app_request_uri_params[ $a_pair_value[0] ] = $a_pair_value[1];
  }
  
  $i_app_request_uri_params = count( $a_app_request_uri_params );

  /*
  echo "<pre> s_app_request_uri: "; var_dump( $s_app_request_uri ); echo "</pre>";
  echo "<pre> a_app_request_uri: "; var_dump( $a_app_request_uri ); echo "</pre>";
  echo "<pre> s_app_request_uri_path: "; var_dump( $s_app_request_uri_path ); echo "</pre>";
  echo "<pre> a_app_request_uri_path: "; var_dump( $a_app_request_uri_path ); echo "</pre>";
  echo "<pre> s_app_request_uri_params: "; var_dump( $s_app_request_uri_params ); echo "</pre>";
  echo "<pre> a_app_request_uri_pairs: "; var_dump( $a_app_request_uri_pairs ); echo "</pre>";
  echo "<pre> a_app_request_uri_params: "; var_dump( $a_app_request_uri_params ); echo "</pre>";
  //exit();
  //*/

  // app
  $oYApp = new app();


// view control

  $b_ctrl_view_path = false;
  $a_ctrl_view_from_uri_path = $a_app_request_uri_path;
  do {

    $s_ctrl_view_from_uri_path = implode( "/", $a_ctrl_view_from_uri_path );
    
    $s_ctrl_view_path = SERVER_PATH."public/".$s_ctrl_view_from_uri_path;
    $b_ctrl_view_path = file_exists( $s_ctrl_view_path );
    
    $a_ctrl_view_from_uri_path = array_slice( $a_ctrl_view_from_uri_path, 0, count( $a_ctrl_view_from_uri_path ) - 1 );

    if ( $b_ctrl_view_path )
      break;

  } while ( ! empty( $a_ctrl_view_from_uri_path ));
  
  $s_app_view_path = $b_ctrl_view_path ? $s_ctrl_view_path : SERVER_PATH . "public/";
  $s_app_view_file = str_replace( "//", "/", $s_app_view_path."/" ) . "index.php";
 
  //echo "<pre> s_app_view_path: "; var_dump( $s_app_view_path ); echo "</pre>";
  //echo "<pre> s_app_view_file: "; var_dump( $s_app_view_file ); echo "</pre>";


// default view

  include_once( $s_app_view_file );