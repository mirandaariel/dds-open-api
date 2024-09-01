<?php


// includes
require 'vendor/autoload.php';
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable( __DIR__ );
$dotenv->load();

// enviorement definition
$s_FMWK_AMBI_DESA = false;
$s_FMWK_AMBI_CALI = false;
$s_FMWK_AMBI_PROD = false;

if ( $_SERVER["SERVER_NAME"] == "localhost" )
{
    $s_FMWK_AMBI_DESA = true;

    // server - app
    define( "SERVER_NAME", "localhost");
    define( "SERVER_PATH", 'C:/data/wamp64/www/labs/dds-open-api/');
    define( "SERVER_HTTP", 'http://localhost/labs/dds-open-api/');
    define( "SERVER_ROOT", '/labs/dds-open-api/');

    define( "FMWK_CLIE_HTTP", "http" );
    define( "FMWK_CLIE_NAME", "ddsopenapi" );
    define( "FMWK_CLIE_SERV", SERVER_HTTP );
    define( "FMWK_CLIE_ROOT", SERVER_ROOT );
    define( "FMWK_CLIE_DIRE", SERVER_PATH );
    define( "FMWK_CLIE_PATH", SERVER_PATH );
    
    define( "FMWK_CLIE_MODU", false );    
    define( "FMWK_BASE_VERS", 24 );

    // database
    define( "FMWK_BASE_CODE", "local-mysql" );
    define( "FMWK_BASE_SERV", "localhost" );
    define( "FMWK_BASE_NOMB", "dds_open_api_desarrollo_v01" );
    define( "FMWK_BASE_USUA", "root" );
    define( "FMWK_BASE_PASW", "" );

    // cloud platform
    define( "CLOUD_PLATFORM_CODE", "" );
    define( "CLOUD_PLATFORM_STORAGE_FLAG", false );
    define( "CLOUD_PLATFORM_STORAGE_CODE", "" );

    // auth0
    define( "AUTH0_DOMAIN", $_ENV['AUTH0_DOMAIN'] );
    define( "AUTH0_CLIENT_ID", $_ENV['AUTH0_CLIENT_ID'] );
    define( "AUTH0_CLIENT_SECRET", $_ENV['AUTH0_CLIENT_SECRET'] );
    define( "AUTH0_AUDIENCE", $_ENV['AUTH0_AUDIENCE'] );
}
else if ( $_SERVER["SERVER_NAME"] == "dds-open-api-fi7pve5u3q-uc.a.run.app" )
{
    $s_FMWK_AMBI_CALI = true;

    // server - app
    define( "SERVER_NAME", "dds-open-api-fi7pve5u3q-uc.a.run.app");
    define( "SERVER_PATH", '/var/www/html/');
    define( "SERVER_HTTP", 'https://dds-open-api-fi7pve5u3q-uc.a.run.app/');
    define( "SERVER_ROOT", '/');

    define( "FMWK_CLIE_HTTP", "http" );
    define( "FMWK_CLIE_NAME", "ddsopenapi" );
    define( "FMWK_CLIE_SERV", SERVER_HTTP );
    define( "FMWK_CLIE_ROOT", SERVER_ROOT );
    define( "FMWK_CLIE_DIRE", SERVER_PATH );
    define( "FMWK_CLIE_PATH", SERVER_PATH );
    
    define( "FMWK_CLIE_MODU", false );    
    define( "FMWK_BASE_VERS", 24 );

    // database
    define( "FMWK_BASE_CODE", "local-mysql" );
    define( "FMWK_BASE_SERV", "10.148.60.3" );
    define( "FMWK_BASE_NOMB", "dds_open_api_calidad_v01" );
    define( "FMWK_BASE_USUA", "admin" );
    define( "FMWK_BASE_PASW", "N}9[_56Z*<Qk_l@*" );

    // cloud platform
    define( "CLOUD_PLATFORM_CODE", "gcp" );
    define( "CLOUD_PLATFORM_STORAGE_FLAG", true );
    define( "CLOUD_PLATFORM_STORAGE_CODE", "gcp-cloud-storage" );

    // auth0
    define( "AUTH0_DOMAIN", getenv( 'AUTH0_DOMAIN' ) );
    define( "AUTH0_CLIENT_ID", getenv( 'AUTH0_CLIENT_ID' ) );
    define( "AUTH0_CLIENT_SECRET", getenv( 'AUTH0_CLIENT_SECRET' ) );
    define( "AUTH0_AUDIENCE", getenv( 'AUTH0_AUDIENCE' ) );
}

// caracteristicas del ambiente

define( "FMWK_AMBI_DESA", $s_FMWK_AMBI_DESA ); 
define( "FMWK_AMBI_CALI", $s_FMWK_AMBI_CALI );
define( "FMWK_AMBI_PROD", $s_FMWK_AMBI_PROD ); 