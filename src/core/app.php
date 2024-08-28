<?php 

//var_dump( __FILE__ );

//include_once( dirname(__FILE__)."/frameworkConnection.cls" );
//include_once( dirname(__FILE__)."/yLogicEngine.cls" );
//include_once( dirname(__FILE__)."/yLearningEngine.cls" );

class app {
    
    // declaracion
    public $bHome = false;
    public $aGet_ = array();
    public $ROOT_DISTANCE = "";

    public $bLgin        = false;
    public $iUsrt        = null; // id de usuario temporal                                                                                                        
    public $iVist        = 0;
    public $s_app_name   = "";
    public $s_session_id = "";
    
    public $aPost  = array();
    public $aHist  = array();
    public $aPath  = array();
    public $a_view = array();
    public $a_data = array();
    public $a_containers = array();
    public $a_filters = array();

    function __construct( $aPara = null )
    {
        //var_dump( "yAppDefault.__construct <br /> \n" );
        
        $this->bLgin          = false;
        $this->iUsrt          = null; // id de usuario temporal                                                                                                        
        $this->iVist          = 0;
        $this->s_app_name     = "";
        $this->s_session_id   = "";
        
        $this->aGet_     = array();
        $this->aPost     = array();
        $this->aHist     = array();
        $this->aPath     = array();
        $this->a_view    = array(
            "b_iframe"            => false,
            "s_name"              => "",
            "s_file"              => "",
            "s_folder"            => "",
            "s_tag_title"         => "",
            "s_app_title"         => "",
            "a_data"              => array(),
            "a_type_view"         => array(), // 2017.06.03
            "a_configuration"     => array(), // 2017.06.08
            "a_temporary_storage" => array(), // 2017.06.11
            "a_components"        => array(), // 2018.03.09
        );
        $this->a_data = array();

        $this->a_containers = array(
            "s_filters_query_where" => "",
        );

        $this->a_filters = array(
            "a_in_use" => array(),
        );


        //$this->oFrameworkConnection = null;
        //$this->oYLogicEngine        = null;
        //$this->oYLearningEngine     = null;

        //$this->setLinkToFramework();                                                                                              // la aplicacion setea la conexion con el framework
    }

    public function setLinkToFramework ( $aPara = null ) {
        //var_dump( "yAppDefault.setLinkToFramework <br /> \n" );
        
        if ( is_null( $this->oFrameworkConnection ) )                                                                                                     // es nulo cuando pasa por el constructor
            $this->oFrameworkConnection = new frameworkConnection();
        else
            $this->oFrameworkConnection->setLinkToFramework();                                                                                        // la instancia de conexion se encuentra creada en el objeto oYApp serializado en la sesion

        // 2017.05.23 - control de instancias de clases guardadas en yApp - INI
        // cuando se serializa oYApp y contiene otros objetos, al deserializarlo las definiciones de 
        // esos objetos deben estar activas, es decir, deben estar disponible la carga de los archivos 
        // de las clases de esos objetos.
        if ( isset( $this->a_view['a_config']['view-card']['a_entities_class_instances'] ) )
        {
            $o_this_auxi = unserialize( $_SESSION['a_app'][ FMWK_CLIE_NAME ] );
            $this->a_view['a_config']['view-card']['a_entities_class_instances'] = 
                $o_this_auxi->a_view['a_config']['view-card']['a_entities_class_instances'];
        }
        // 2017.05.23 - control de instancias de clases guardadas en yApp - FIN
    }

    public function init ( $a_parameters = null ) {
        //var_dump( "yAppDefault.init - INI" );
        //session_start();
        
        $this->s_app_name           = FMWK_CLIE_NAME;
        //$this->oYLogicEngine        = new yLogicEngine ();
        //$this->oYLearningEngine     = new yLearningEngine ();

        // 2023.08.07 - EN DESARROLLO
        /*/ controlar si existe una instancia de yApp
        if ( isset( $_SESSION['a_app'][ $this->s_app_name ] ) ) 
        {
            $oYAppSess = unserialize( $_SESSION['a_app'][ $this->s_app_name ] );
            //var_dump( $oYAppSess->a_type_view );
            $this->bLgin = $oYAppSess->bLgin;
            $this->iUsrt = $oYAppSess->iUsrt;
            $this->iVist = $oYAppSess->iVist;
            $this->aHist = $oYAppSess->aHist;
            
            if ( isset( $oYAppSess->aPath ) )
                $this->aPath = $oYAppSess->aPath;

            if ( isset( $oYAppSess->a_data ) )
                $this->a_data = $oYAppSess->a_data;

            // 2017.06.03
            if ( isset( $oYAppSess->a_type_view ) )
                $this->a_type_view = $oYAppSess->a_type_view;

            // 2017.06.08
            if ( isset( $oYAppSess->a_configuration ) )
                $this->a_configuration = $oYAppSess->a_configuration;

            // 2017.06.11
            if ( isset( $oYAppSess->a_temporary_storage ) )
                $this->a_temporary_storage = $oYAppSess->a_temporary_storage;
            
            // 2019.07.16 - pruebas.
            //if ( isset( $oYAppSess->oYLogicEngine ) )
            //    $this->oYLogicEngine = $oYAppSess->oYLogicEngine;
            
            //if ( isset( $oYAppSess->oYLearningEngine ) )
            //    $this->oYLearningEngine = $oYAppSess->oYLearningEngine;
            
            // estan los datos de login y del login guardados en la session
        }
        */
        
        // 2018.04.28 - despues de revisar, se comentó porque no se utiliza.
        //$this->oYLogicEngine->init();
        //$this->oYLearningEngine->init();

        $this->aGet_ = $_GET;
        $this->aPost = $_POST;

        $this->a_view['s_name']     = $_GET['s_view_name'];
        $this->a_view['s_file']     = $_GET['s_view_file'];
        $this->a_view['s_folder']   = $_GET['s_view_folder'];

        // actualizar esta instancia en la variable de sesion
        $this->save();
        //var_dump( "yAppDefault.init - END" );
    }

    public function logout( $aValo = null ) {
        //var_dump( "yAppDefault.logout" );
        session_destroy();
        session_start();
        //var_dump( $_SESSION );
    }

    public function login( $a_parameters = null ) {
        //var_dump( "yAppDefault.login" );
        //var_dump( $a_parameters );
        //exit();

        // 2019.09.14 - recibe (o deberia) la password encriptada.

        $b_procesar = false;
        $aResu = array(
            "aDato" => array(),
            'bProc' => false,
            'sProc' => "Debe ingresar una dirección de correo electrónico.", 
        );
        
        // 2016-05-30 - Control sobre los datos del formulario porque ahora, de acuerdo con la nueva
        // estructura de los nombres de los campos en las tablas de la base de datos, es mas dificil
        // que el framework reconozca la entidad. Aun no esta implementado.
        if ( isset( $a_parameters['entities']['usuario'] ) )
            $a_usuario = $a_parameters['entities']['usuario'][0];
        else if ( isset( $a_parameters['entities']['undefined'] ) )
            $a_usuario = $a_parameters['entities']['undefined'][0];
        else 
            $a_usuario = $a_parameters['data'];

        if ( $a_usuario['email'] != "" )
            $b_procesar = true;

        if ( $b_procesar )
        {
            $oUsua = new usuario();
            $oUsua ->enable_relations();
            $oUsua ->aBase['aFilt'][] = "usuario.flag_borrado = 0";
            $oUsua ->aBase['aFilt'][] = "usuario.email = '" . $a_usuario['email'] . "'";
            $aUsua = $oUsua->find();
            //$aUsua = $oUsua->read( $a_usuario );
            //print_r( $aUsua );

            $s_error     = "";
            $b_error     = false;
            $b_legajo    = false;
            $b_usuario = empty( $aUsua ) ? false : true;
            
            // controlar existencia de usuario por email
            $b_error     = ! $b_usuario ? true : false;
            $s_error     = $b_error ? "email" : "";
            //var_dump( $b_error );
            //var_dump( $s_error );

            // controlar existencia de usuario por numero de legajo
            if ( class_exists( "app_usuario" ) )
            {

                // controlar que el valor dentro del campo email no tenga el arroba
                if ( strpos( $a_usuario['email'], "@" ) === false )
                {
                    
                    $o_app_usuario = new app_usuario();
                    $o_app_usuario ->enable_relations();
                    $o_app_usuario ->aBase['aFilt'][] = "app_usuario.empresa_legajo = '" . $a_usuario['email'] . "'";
                    $a_app_usuario = $o_app_usuario->find();
                    //var_dump( $a_app_usuario );

                    $b_legajo = empty( $a_app_usuario ) ? false : true;
                    
                    // obtener los datos del usuario
                    if ( $b_legajo )
                    {
                        $b_usuario = true;
                        $aUsua = $oUsua->read( array( "id" => $a_app_usuario[0]['usuario_id'] ) );
                    }
                    
                    // controlar existencia de usuario por email
                    $b_error     = ! $b_usuario ? true : false;
                    $s_error     = $b_error ? "legajo" : "";

                }
            }

            // validar password
            if ( $b_usuario )
            {
                //var_dump( $a_usuario );
                //var_dump( $aUsua );

                $b_password = false;

                // controlar password
                if ( $a_usuario['password'] == $aUsua[0]['password']    )
                    $b_password = true;

                // controlar para mensaje de error
                $b_error = ! $b_password ? true : false;
                $s_error = $b_error ? "password" : "";

                // controlar codigo personal
                if ( $b_legajo )
                {
                    $b_password = false;
                    if ( $a_usuario['password'] == md5( $a_app_usuario[0]['credito_codigo'] ) )
                        $b_password = true;

                    // controlar para mensaje de error
                    $b_error = ! $b_password ? true : false;
                    $s_error = $b_error ? "codigo_personal" : "";
                }                

                // procesar ante password correcta
                //var_dump( $b_password );
                if ( $b_password )
                {
                    // variables de sesion
                    $this->bLgin = true;
                    $this->iVist = 0;
                    $this->a_data['usuario'] = $aUsua;
                    $aResu['bProc'] = true;    
                }

            }

            // mensajes
            //print_r( $b_error );
            //print_r( $s_error );
            if ( $b_error )
            {
                $aResu['bProc'] = false;
                switch ( $s_error ) 
                {
                        case 'email':
                            $aResu['sProc'] = "El email ".$a_usuario['email']." ingresado no pertenece a un usuario registrado.";    
                            break;
                        case 'legajo':
                            $aResu['sProc'] = "El legajo ".$a_usuario['email']." ingresado no pertenece a un usuario registrado.";    
                            break;
                        case 'password':
                            $aResu['sProc'] = "La contraseña ingresada no es valida.";    
                            break;
                        case 'codigo_personal':
                            $aResu['sProc'] = "La código personal ingresado no es valido.";    
                            break;
                        default:
                            $aResu['sProc'] = "Se produjo un error que no podemos identifcar. Por favor, envianos un correo electrónico a " . FMWK_CLIE_MAIL;    
                            break;
                    }    
            }

            // si el usurio se ha logueado exitosamente controlar actividad de usuario temporal.
            if ( $this->bLgin && ! is_null( $this->iUsrt ) )
            {
                // ubicar las instancias relacionadas al usuario temporal
                $this->iUsrt = null;

                //var_dump( $this );
            }

            $_SESSION['a_app'][ $this->s_app_name ] = serialize( $this );

            // 2018.05.27 - se guarda la sesion luego de procesar el login. Si fue exitoso, es necesario las siguientes
            // lineas para persistir el estado de usuario logueado.
            $this->save();
            $this->save_in_file();
        }

        // registrar el log de inicio de sesion si corresponde
        if ( $aResu['bProc'] && class_exists( "log_login" ) )
        {
            $a_log_instance = array(
                "ip"         => $this->get_user_ip(), //"0.0.0.0",
                "usuario_id" => $aUsua[0]['id'],
            );

            $o_log_login = new log_login();
            $a_log_login = $o_log_login->create( $a_log_instance );
        }

        return $aResu;
    }

    public function obtenerVariableDeSistema ( $aValo ) {
        //var_dump( "yAppDefault.obtenerVariableDeSistema deprecado" );
    }
    
    public function setVariableDeSistema ( $aValo ) {
        //var_dump( "yAppDefault.setVariableDeSistema deprecado" );
    }

    public function obtenerContenidoVista( $aValo = null ) {
        //var_dump( "yAppDefault.obtenerContenidoVista deprecado" );
    }

    public function obtenerUsuario ( $aValo = null ) {
        //var_dump( "yAppDefault.obtenerUsuario deprecado" );
    }

    public function obtenerMensajes ( $aValo = null ) {
        //var_dump( "yAppDefault.obtenerMensajes" );
    }

    public function obtenerPath ( $aValo = null ) {
        //var_dump( "yAppDefault.obtenerPath deprecado" );
    } 

    public function obtenerPermisosDeVista ( $aValo = null ) {
        //var_dump( "yAppDefault.obtenerPermisosDeVista deprecado" );    
    }
    
    public function guardarEnSesion ( $aValo = null ) {
        //var_dump( "yAppDefault.guardarEnSesion deprecado" );
        $this->save( $aValo );
    }
    
    public function controlModuloActual ( $aParameters = null ) {
        //var_dump( "yAppDefault.controlModuloActual deprecado" );
    }

    public function save ( $a_parameters = null ) {
        //var_dump( "yAppDefault.save" );

        // consultar si esta creado el array de aplicaciones
        if( ! isset( $_SESSION['a_app'] ) )
            $_SESSION['a_app'] = array();

        $_SESSION['a_app'][ $this->s_app_name ] = serialize( $this );
    }

    public function save_in_file ( $a_parameters = NULL ) 
    {
        return false;
        /*
        //print_r( "yAppDefault.save_in_file() <br /> \n" );
        //print_r( __FILE__ . "<br /> \n" );
        //print_r( FMWK_CLIE_DIRE . "<br /> \n" );
        //print_r( $this->a_view['a_components'] );
        //error_reporting(1);

        // 2018.04.28 - nombre del archivo
            // en base a la utilizacion de un codigo de sesion que esta asociado con el usuario (logueado o no) es que 
            // se crea el nombre del archivo
            $s_session_object = "app_session_object.txt"; 
            $s_session_object = "app_session_object_" . $this->s_session_id . ".txt"; 

        // crear el archivo
        //$fp = fopen( FMWK_CLIE_DIRE . 'project/php/core/app_session_object.txt', 'w');
        $fp = fopen( FMWK_CLIE_DIRE . 'project/php/core/' . $s_session_object, 'w');
        fwrite( $fp, $_SESSION['a_app'][ $this->s_app_name ] );
        fclose( $fp );
        */
    }

    public function open_session_object ( $a_parameters = null ) {
        //print_r( "yAppDefault.open_session_object() <br /> \n" );
        //print_r( $_POST );

        // 2018.03.11 - la funcion tiene que venir luego de incluir yApp.php porque solo de esa forma, reconoce
        // las clases de los componentes que se encuentran serializados.

        // 2019.04.25 - controlar variables de sesion
            $b_sesion_clave  = isset( $_SESSION['a_app'] ) ? true : false;
            $b_sesion_app    = isset( $b_sesion_clave ) ?    ( isset( $_SESSION['a_app'][ FMWK_CLIE_NAME ] ) ? true : false ) : false;
            $b_sesion_objeto = isset( $b_sesion_app ) ?      ( is_string( $_SESSION['a_app'][ FMWK_CLIE_NAME ] ) ? true : false ) : false;
            $b_file_content  = false;

        // evaluar parametro de id de sesion
            if ( ! isset( $_POST['app'] ) )
                $_POST['app'] = ""; 

        // evaluar parametro de ruta del projecto
            $s_app_dire = "";
            if ( isset( $_POST['sClie'] ) )
              $s_app_dire = $_POST['sClie'];
            else if ( isset( $_POST['FMWK_CLIE_DIRE'] ) )
              $s_app_dire = $_POST['FMWK_CLIE_DIRE'];

        // obtener contenido del archivo de session

            // 2019.04.25 - PENDIENTE DE CONTROL EN USO AMPLIO DE LA APLICACION
            // Se busca obtener el contenido del archivo porque desde ahi no se producce el error de clases incompletas o error de definicion
            // hay que probar que la aplicacion no rompe cuando se obtiene el objeto serializado desde la sesion.
            // hay que probar en llamadas AJAX si esto produce un bug y si en ese caso es realmente necesario obtener la sesion desde el archivo
          

            // 2019.04.27 - como no se pueden conservar las actualizaciones de sesion en los scripts
            // de las vistas del contenido de los paneles. Entonces siempre se debe cargar el objeto
            // de sesion desde el archivo en php/core 
            //if ( ! $b_sesion_objeto )
            /*
            if ( true )
            {            
                //var_dump( "if b_sesion_objeto" );

                $s_session_id     = $_POST['app'] == "" ? $this->s_session_id : $_POST['app'];
                $s_session_object = "app_session_object_" . $s_session_id . ".txt"; 
                $s_file_session   = $s_app_dire . "project/php/core/" . $s_session_object;
                
                if ( file_exists( $s_file_session ) )
                {
                    //var_dump( "if file_exists" );

                    $o_gestor         = fopen( $s_file_session, "rb" );
                    $s_contenido      = fread( $o_gestor , filesize( $s_file_session ));
                    fclose( $o_gestor );

                    // reestablecer el contenido en la sesion actual
                    $_SESSION['a_app'][ FMWK_CLIE_NAME ] = $s_contenido;

                    $b_file_content = true;
                }
                else
                {
                    header( "location: " . FMWK_CLIE_SERV );
                }
            }
            */
        
         // crear el objeto para poder devolverlo al script que incovo este metodo
            $o = unserialize( $_SESSION['a_app'][ FMWK_CLIE_NAME ] );
            
            $o->a_components['flag_from_file'] = $b_file_content;
        
        return $o;
    }
    
    public function controlAccesoVista ( $aValo = null ) {
        //var_dump( "yAppDefault.controlAccesoVista deprecado" );
    }
    
    public function enviarAlerta ( $aValo = null ) {
        //var_dump( "yAppDefault.enviarAlerta deprecado" ); 
    }

    public function setModule ( $aParameters = null ) {
        //var_dump( "yAppDefault.setModule deprecado" );
    }
    
    // 2017.02.27 - control_workflow - Version 20 - INI
        public function control_workflow ( $a_parameters = null ) {
            // 2017.03.13. los parametros que recibe son de la instancia soporte por lo que esta funcion 
            // se encarga de procesar los soportes y en base a eso crear las etapas.
            //var_dump( "yApp.control_workflow" );
            //var_dump( $a_parameters );

            $o_base = new base();

            $o_instancia = $a_parameters['o_instancia'];
            $a_instancia = $a_parameters['a_instancia'];
            $i_instancia = $a_instancia['id'];
            $s_entidad     = $o_instancia->aBase['aEnti'][0];
        
            // obtener todos los soportes relacionados con la instancia
            $s_query = "SELECT 
                wf_soporte.wf_proceso_id,
                wf_proceso.nombre,
                wf_proceso.flag_habilitado,
                wf_soporte.wf_etapa_id,
                wf_soporte.wf_objetivo_id,
                wf_objetivo.entidad_nombre 'wf_objetivo_entidad',
                wf_objetivo.filtro             'wf_objetivo_filtro',
                wf_objetivo.flag_filtro        'wf_objetivo_flag_filtro',
                wf_objetivo.prioridad,
                wf_etapa.nombre,
                wf_etapa.orden,
                wf_etapa.flag_habilitada,
                wf_soporte.id,
                wf_soporte.entidad_nombre,
                wf_soporte.filtro,
                wf_soporte.flag_filtro
                FROM wf_soporte
                LEFT OUTER JOIN wf_etapa ON wf_etapa.id = wf_soporte.wf_etapa_id
                LEFT OUTER JOIN wf_proceso ON wf_proceso.id = wf_soporte.wf_proceso_id
                LEFT OUTER JOIN wf_objetivo ON wf_objetivo.id = wf_soporte.wf_objetivo_id
                WHERE wf_soporte.entidad_nombre = '[entidad_nombre]'
                    AND wf_etapa.flag_habilitada= 1
                    AND wf_proceso.flag_habilitado = 1
                ORDER BY wf_proceso.id ASC, wf_etapa.orden ASC";
            
            $s_query     = str_replace( "[entidad_nombre]", $s_entidad, $s_query );
            $a_base        = $o_base->procSent( $s_query );
            $a_soporte = $a_base['aDato'];
            
            // realizar una busqueda por cada soporte y controlar si la instancia se encuentra dentro de los
            // resultados
            foreach ( $a_soporte as $i_for_soporte => $a_for_soporte ) 
            {
                // creo una instancia nueva de clase o entidad
                $o_entidad = new $s_entidad();
                
                // asigno los filtros del soporte si tiene
                if ( $a_for_soporte['flag_filtro'] == "1" )
                    $o_entidad ->aBase['aFilt'][] = $a_for_soporte['filtro']; 
                
                // asigno el id de la instancia para ver si la nueva instancia corresponde con los filtros
                $o_entidad ->aBase['aFilt'][] = "$s_entidad.id = $i_instancia"; 
                $a_entidad = $o_entidad->find();
                //var_dump( $o_entidad ->aBase['aFilt'] );
                //var_dump( $a_entidad );

                $b_soporte = empty( $a_entidad ) ? false : true;

                if ( $b_soporte )
                {
                    $s_objetivo_entidad = $a_for_soporte['wf_objetivo_entidad'];
                    $s_objetivo_clave     = 0;

                    // al tener la entidad del objetivo principal del proceso, lo unico que hay que hacer 
                    // es controlar que la entidad soporte tenga este campo como clave foranea
                    // 2017.03.13 - o que el soporte sea igual al objetivo
                    if ( isset( $a_entidad[0][ $s_objetivo_entidad . "_id" ] ) || $s_objetivo_entidad == $s_entidad )
                    {
                        $s_proceso_id         = $a_for_soporte['wf_proceso_id'];
                        if ( $s_objetivo_entidad == $s_entidad )
                            $s_objetivo_clave = $a_entidad[0]['id'];
                        else
                            $s_objetivo_clave = $a_entidad[0][ $s_objetivo_entidad . "_id" ];
                     
                        // obtener la instancia del proceso
                        $o_instancia_proceso = new wf_instancia();
                        $o_instancia_proceso ->aBase['aFilt'][] = "wf_instancia.entidad_nombre = '$s_objetivo_entidad'";
                        $o_instancia_proceso ->aBase['aFilt'][] = "wf_instancia.entidad_clave = '$s_objetivo_clave'";
                        $o_instancia_proceso ->aBase['aFilt'][] = "wf_instancia.wf_proceso_id = '$s_proceso_id'";
                        $o_instancia_proceso ->aBase['aFilt'][] = "wf_instancia.flag_terminado = 0";
                        $a_instancia_proceso = $o_instancia_proceso->find();
            
                        // obtener proceso para crear la etapa
                        $o_proceso = new wf_proceso();
                        $o_proceso ->read( array( "id" => $a_instancia_proceso[0]['wf_proceso_id'] ) );
                        $o_proceso ->crear_etapa(
                            array(
                                "wf_instancia"        => $a_instancia_proceso,
                                "wf_soporte"            => $a_for_soporte,
                                "entidad_soporte" => $a_entidad,
                            )
                        );
                    }
                }
            }
        }
    // 2017.02.27 - control_workflow - Version 20 - FIN

    // 2016.04.21 - Nueva estructura - INI
        // 2015.09.13 - Nueva estructura - INI
    
        public function is_associative ( $aParameters ) {
            $arr = $aParameters['data'];
            return array_keys($arr) !== range(0, count($arr) - 1);
        }
        
        // se encarga de construir el historial de navegacion     
        public function setPath ( $aParameters = null ) {
            //var_dump( "yAppDefault.setPath - INI" );

            $sView    = is_null( $aParameters )        ? "" : $aParameters['data']['sView'];
            $iHist    = count( $this->aHist ) == 0 ? 0    : count( $this->aHist ) - 1;
            
            // 2016-03-27 - Control de asignacion del valor de la vista
            if ( $sView == "" )
            {
                if ( is_null( $this->a_view['s_name'] ) || $this->a_view['s_name'] == "" )
                    $this->a_view['s_name'] = $this->a_view['s_folder'];
            } 
            $sView = $this->a_view['s_name'];
            
            // 2016-03-27 - cuando el usuario llega a la vista home el path debe volver a cero.
            if ( $sView == "home" )
                $this->aPath = array();

            // 2016-03-27 - construccion del nuevo elemento para los array aHist y aPath
            //$s_urlf = str_replace( FMWK_CLIE_ROOT, "", $this->aGet_['s_complete'] ); 
            
            // 2017.04.04 - si el proyecto se encuentra en produccion hay que tener cuidado con el valor de
            // FMWK_CLIE_ROOT ya que al realizar el replace borra las barras de la url.
            $s_urlf = $this->aGet_['s_complete']; 
            if ( FMWK_CLIE_ROOT != "/" )
                $s_urlf = str_replace( FMWK_CLIE_ROOT, "", $s_urlf ); 
            $s_urlf = FMWK_CLIE_SERV . $s_urlf;
            $s_urlf = str_replace( FMWK_CLIE_SERV . "/", FMWK_CLIE_SERV, $s_urlf ); 
            
            $a_item = array(
                "s_view" => $sView,
                "s_urlf" => $s_urlf,                                                                                                                                                    // es mas univoco, se aplico para cuando se dan los saltos a otras entidades
                "a_data" => $this->aGet_,
            ); 
            
            // 2016-07-21 - controlar si es un script que se ejecuta en un iframe
            $b_iframe = false;
            if ( strpos( $s_urlf, "iframe=1" ) !== false )
                $b_iframe = true;

            // 2016-03-27 - popular y controlar el array del camino de las vistas
            $a_path_view = array();
            foreach ( $this->aPath as $i_path => $a_path_inst ) 
                $a_path_view[] = $a_path_inst['s_urlf'];
            
            if ( ! in_array( $s_urlf, $a_path_view ) )
            {
                if ( ! $b_iframe )
                    $this->aPath[] = $a_item;
            }
            else 
            {
                // 2016-03-27 - controlar si la vista actual
                $i_item = array_search( $s_urlf, $a_path_view );
                $this->aPath = array_slice( $this->aPath, 0, $i_item + 1 );
            }
            
            // 2017.05.13 - control de las ultimas dos posiciones - INI
            // cuando se refresca la vista (f5) se debe controlar la ultima posicion y la anterior a la 
            // ultima porque existen vistas con el iframe que generan dos entradas en el historial
            $i_historial_cantidad = count( $this->aHist );
            if ( $this->aHist[ $i_historial_cantidad - 1 ] != $s_urlf && 
                $this->aHist[ $i_historial_cantidad - 2 ] != $s_urlf )
                $this->aHist[] = $s_urlf;
            // 2017.05.13 - control de las ultimas dos posiciones - FIN
            
            //var_dump( "yAppDefault.setPath - END" );
            $this->save();
        }
        
        public function getEvent ( $aParameters = null ) {
            //var_dump( "yAppDefault.getEvent - INI" );

            $bEvnt = false;
            $aEvnt = array(
                array(
                    "_sisevntiden" => "1",
                    "_sisevntnomb" => "vista-carga",
                ),
            );

            if ( isset( $this->a_view['a_data']['_sisevnt'] ) )
                $bEvnt = true;

            if ( $bEvnt )
                $aEvnt = $this->a_view['a_data']['_sisevnt'];
            else
                $this->a_view['a_data']['_sisevnt'] = $aEvnt;

            //var_dump( "yAppDefault.getEvent - END" );
            return $aEvnt;
        }
            
        public function getCallType ( $aParameters = null ) {
            $bCall = false;
            $aCall = array(
                array(
                    "_siscalltypeiden" => "1",
                    "_siscalltypenomb" => "default",
                ),
            );

            if ( isset( $this->a_view['a_data']['_siscalltype'] ) )
                $bCall = true;

            if ( $bCall )
                $aCall = $this->a_view['a_data']['_siscalltype'];
            else
                $this->a_view['a_data']['_siscalltype'] = $aCall;

            return $aCall;
        }
        
        public function getModule( $a_parameters = null ) {
            //var_dump( "yAppDefault.getModule" );
            //var_dump( FMWK_MODU_NAME );

            $a_module = array();

            if ( FMWK_CLIE_MODU )
            {
                $o_module = new _defmodu();
                $o_module ->aBase['aFilt'][] = "_defmodu._defmodusufijo = '". FMWK_MODU_NAME ."'";
                $a_module = $o_module ->find();
                //var_dump( $a_module );

                $this->a_data['_defmodu'] = $a_module;
            }     

            return $a_module;
        }

        public function getView ( $aParameters = null ) {
            //var_dump( "yAppDefault.getView - INI" );
            //var_dump( $this );

            $sUrl_ = $this->a_view['s_name'];                                                                                                                                 // obtener la url relativa de la vista
            //var_dump( $sUrl_ );
            
            $a_module = $this->getModule();
            
            $oView = new _sisview ();
            $oView->aBase['aFilt'][] = "_sisview._sisviewurlf = '$sUrl_'";
            $aView = $oView->find();
            
            $oView = new _defview ();
            $oView->aBase['aFilt'][] = "_defview._defviewurlf = '$sUrl_'";
            if ( FMWK_CLIE_MODU )
                $oView->aBase['aFilt'][] = "_defview._defview_defmodu = " . $a_module[0]['_defmoduiden'];
            else
                $oView->aBase['aFilt'][] = "( _defview._defview_defmodu IS NULL OR _defview_defmodu = 0 )";

            $aView_def = $oView->find();
            //var_dump( FMWK_CLIE_MODU );
            //var_dump( $a_module[0]['_defmoduiden'] );
            //var_dump( $aView );
            //var_dump( $aView_def );

            $this->a_view['a_data']['_sisview'] = $aView;                                                                                                     // asignar los valores de la instancia al modulo
            $this->a_view['a_data']['_defview'] = $aView_def;
            $this->iVist++;                                                                                                                                                                 // PENDIENTE. Cantidad de veces que se visualizo la vista solo funciona para los refresh
            
            //var_dump( "yAppDefault.getView - END" );
            return $aView;
        }
        
        public function getUser ( $aParameters = null ) {
            //var_dump( "yAppDefault.getUser - INI" );
            
            $aUsua = array();
            $oUsua = new usuario(); 

            // si no hay usuario loguea se genera uno
            if ( ! $this->bLgin && is_null( $this->iUsrt ) )
            {

                // 2018.09.11 - email temporario
                $s_email_temp = date("YmdHis") . "@temp";

                $aBase    = $oUsua->create( array( 
                    "id"    => "",
                    "email" => $s_email_temp,
                ) );
                $aUsua[0] = array( "id" => $aBase['iIden'] );
                
                $this->a_data['usuario'] = $aUsua;
                $this->iUsrt = $aUsua[0]['id']; 
                $this->save();
            }

            $aUsua = $this->a_data['usuario'];                                                                                                                            // se obtiene desde los datos de la app

            //var_dump( "yAppDefault.getUser - END" );
            return $aUsua;
        }
        
        public function getViewSnapshot ( $aParameters = null ) {
            //////var_dump( "yAppDefault.getViewSnapshot" );
            $aLand = array();                                                                                                                                                             // instancia de un landscape para comparar contra la vista actual.
            return $this->oYLogicEngine->getViewSnapshot( $aLand );
        }
        
        public function getLandscape ( $aParameters = null ) {
            //var_dump( "yAppDefault.getLandscape - INI" );
            
            if ( $this->oYLogicEngine == null )
                $this->oYLogicEngine = new yLogicEngine();

            $this->oYLogicEngine->init();

            return $this->oYLogicEngine->getLandscape();
            //var_dump( "yAppDefault.getLandscape - END" );
        }
        
        public function controlParameters ( $aParameters = null ) {
            //var_dump( "yAppDefault.controlParameters - INI" );
            // 2018.04.28 - esta en desuso.
            
            $aEnti     = array();
            $aPostProp = array();                                                                                                                                                     // guardara las claves de aParameters que pueden ser propiedades
            $aPostEnti = array();                                                                                                                                                     // guardara las claves de aParameters que pueden ser entidades
            global $aFmwkMode;

            $aItfcTemp = array(
                "data"            => ! isset( $aParameters['data'] )            ? array() : $aParameters['data'],
                "event"         => ! isset( $aParameters['event'] )         ? array() : $aParameters['event'],
                "links"         => ! isset( $aParameters['links'] )         ? array() : $aParameters['links'],
                "inputs"        => ! isset( $aParameters['inputs'] )        ? array() : $aParameters['inputs'],
                "content"     => ! isset( $aParameters['content'] )     ? array() : $aParameters['content'],
                "control"     => ! isset( $aParameters['control'] )     ? array() : $aParameters['control'],
                "entities"    => ! isset( $aParameters['entities'] )    ? array() : $aParameters['entities'],
                "landscape" => ! isset( $aParameters['landscape'] ) ? array() : $aParameters['landscape'],
            );                                                                                                                                                                                            // template de la interface de comunicacion

            if ( is_null( $aParameters ) )
                $aParameters = $_POST;                                                                                                                                                // por defecto se asgina el contenido del array POST

            if ( $this->oYLearningEngine == null )
                $this->oYLearningEngine = new yLearningEngine();

            $aMape = $this->oYLearningEngine->getMapping();                                                                                                 // obtener los mapeo inputs con propiedades de entidades
            
            // separar el contenido de la clave data en lo que se suponen propiedades y entidades
            foreach ( $aParameters['data'] as $sClav => $xValo ) 
            {
                $sClav = str_replace( "[", "", $sClav );                                                                                                            // error ocasionado por el js de framework en inputs con valor de name="name[]"
                if ( is_array( $xValo ) )
                    $aPostEnti[ $sClav ] = $xValo;
                else 
                    $aPostProp[ $sClav ] = $xValo;
            }

            // controlar las entidades que provienen del post
            foreach ( $aPostEnti as $sEnti => $aValo ) 
            {
                $bAsoc = false;                                                                                                                                                             // flag determina que aValo es asociativo, no contiene instancias 
                $sClav = "undefined";                                                                                                                                                 // valor de la clave de array que representa a las entidades no identificadas

                if ( $this->is_associative( array( "data" => $aValo ) ) )                                                                                        // controlar si aValo es asociativo
                    $bAsoc = true;

                if ( in_array( $sEnti, $aFmwkMode ) )                                                                                                                 // controlar si la entidad es conocida dentro de al app
                    $sClav = $sEnti;

                if ( ! isset( $aItfcTemp['entities'][ $sClav ] ) )                                                                                        // si no existe la clave
                    $aItfcTemp['entities'][ $sClav ] = array();                                                                                                 // se crea

                if ( $bAsoc )
                    $aItfcTemp['entities'][ $sClav ][] = $aValo;                                                                                                // se guarda aValo como un instancia unica
                else
                    $aItfcTemp['entities'][ $sClav ] = array_merge( $aItfcTemp['entities'][ $sClav ], $aValo ); // se guarda aValo como un array de instancias
            }

            // controlar las propiedades que provienen del si pueden pertenecer a una clase
            $aNombLong = array( 8, 4 );                                                                                                                                                 // longitud posible del nombre de una clase. Al poner la longitud mas larga se evitan validaciones
            foreach ( $aPostProp as $sClav => $xValo )                                                                                                                    // control sobre el array de los valores post que son considerados propiedades
            {
                $bEnti = false;                                                                                                                                                                     // flag que determina si la propiedad pertenece a una entidad
                
                foreach ( $aNombLong as $iPosi => $iNombLong )                                                                                                        // iteracion por cada posible longitud que puede tener el nombre de una clase
                {
                    $sEnti = substr( $sClav, 0, $iNombLong );                                                                                                             // separacion del nombre de la propiedad el nombre de la entidad (acoplado a forma de desarrollo interno )
                    if ( in_array( $sEnti, $aFmwkMode ) && strlen( $sEnti ) < strlen( $sClav ) )
                    {
                        if( ! isset( $aEnti[ $sEnti ] ) )
                            $aEnti[ $sEnti ] = array();
                        $aEnti[ $sEnti ][ $sClav ] = $xValo;
                        $bEnti = true;                                                                                                                                                                // asignacion de match con una entidad
                        if ( $bEnti )
                            break;                                                                                                                                                                            // como se ha realizado un match con la longitud mas larga, se debe salir de la iteracion
                    }
                }
                
                if ( ! $bEnti )                                                                                                                                                                     // si no existe relacion con alguna entidad conocidad
                    $aEnti['undefined'][ $sClav ] = $xValo;                                                                                                                 // la propiedad se guarda en un array por defecto para propiedades de entidad no definida            
            }
            foreach ( $aEnti as $sEntiNomb => $aEntiInst )
                $aItfcTemp['entities'][ $sEntiNomb ][] = $aEntiInst;

            // MAPEO controlar el mapeo de propiedades no identificadas
            foreach (    $aItfcTemp['entities'] as $sEntiNomb => $aEntiInst )
            {
                foreach ( $aEntiInst as $iInstPosi => $aInstData ) 
                {
                    foreach ( $aInstData as $sPropNomb => $xPropValo ) 
                    {
                        if ( array_key_exists( $sPropNomb, $aMape ) )                                                                                                 // control sobre el mapeo para no perder la relacion de las instancias
                        {
                            $xMapeInst = $aMape[ $sPropNomb ];                                                                                                                    // se obtiene la instancia de mapeo que aun se desconoce su tipo
                            
                            // la funcion debe poder manipular el valor que se asigna a la propiedad, por eso se pasan
                            // por referencia los valores del array
                            if ( gettype( $xMapeInst ) == "object" )                                                                                                        // control si es una funcion
                                $aMapeInst = $xMapeInst( array( &$sPropNomb, &$xPropValo ) );                                                         // se ejecuta la funcion y la devolucion debe ser un array con los string de mapeo
                            else if ( ! is_array( $xMapeInst ) )                                                                                                                // control si no es un conjunto de valores
                                $aMapeInst = array( $xMapeInst );                                                                                                                 // se armar como un array de indices para que sea procesado por el siguiente codigo
                            else
                                $aMapeInst = $xMapeInst;                                                                                                                                    // es un array
                            
                            // PENDIENTE. controlar que aMapeInst sea una array con strings que puedan separarse

                            foreach ( $aMapeInst as $iMape => $sMape ) 
                            {
                                $aConj = explode( ".", $sMape );

                                if ( $sEntiNomb == "undefined" )                                                                                                            // al encontrarse dentro de la entrada undefined
                                    $aItfcTemp['entities'][ $aConj[0] ][ 0 ][ $aConj[1] ] = 
                                        is_array( $xPropValo ) ? $xPropValo[ $iMape ] : $xPropValo;                                             // se debe guardar en al entrada de la entidad que indica el mapeo
                                else
                                    $aItfcTemp['entities'][ $aConj[0] ][ $iInstPosi ][ $aConj[1] ] = 
                                        is_array( $xPropValo ) ? $xPropValo[ $iMape ] : $xPropValo;
                            }
                            
                            unset( $aItfcTemp['entities'][ $sEntiNomb ][ $iInstPosi ][ $sPropNomb ] );                                            // se la elimina de la entidad a la que fue asociada
                        }            
                    }
                    if ( empty( $aItfcTemp['entities'][ $sEntiNomb ][ $iInstPosi ] ) )
                        unset( $aItfcTemp['entities'][ $sEntiNomb ][ $iInstPosi ] );
                }
                if ( empty( $aItfcTemp['entities'][ $sEntiNomb ] ) )
                    unset( $aItfcTemp['entities'][ $sEntiNomb ] );
            }

            // controlar las propiedades de las entidades identificadas
            foreach (    $aItfcTemp['entities'] as $sEntiNomb => $aEntiInst )
            {
                if ( $sEntiNomb == "undefined" )
                    continue;
                
                $oEnti = new $sEntiNomb ();                                                                                                                                             // instanciar la clase para poder acceder a las propiedades de la clase
                
                foreach ( $aEntiInst as $iInstPosi => $aInstData ) 
                {
                    $aProp = array();
                    
                    foreach ( $aInstData as $sPropNomb => $xPropValo ) 
                    {
                        if ( ! in_array( $sPropNomb, $oEnti->aProp ) )                                                                                                // si la propiedad que se relaciono con una entidad, no existe en ella
                        {
                            $aProp[ $sPropNomb ] = $xPropValo;                                                                                                                    // se la identifica como no perteneciente a una entidad no definida
                            unset( $aItfcTemp['entities'][ $sEntiNomb ][ $iInstPosi ][ $sPropNomb ] );                                            // se la elimina de la entidad a la que fue asociada
                        }
                    }
                    
                    if ( ! empty( $aProp ) )
                        $aItfcTemp['entities']['undefined'][] = $aProp;     

                    if ( empty( $aItfcTemp['entities'][ $sEntiNomb ][ $iInstPosi ] ) )
                        unset( $aItfcTemp['entities'][ $sEntiNomb ][ $iInstPosi ] );
                }

                if ( empty( $aItfcTemp['entities'][ $sEntiNomb ] ) )
                        unset( $aItfcTemp['entities'][ $sEntiNomb ] );
            }

            // preparar interfase de comunicacion
            $aItfcTemp['data'] = $_POST['data'];
            $aItfcTemp['event'] = $_POST['event'];

            //var_dump( "yAppDefault.controlParameters - END" );
            return $aItfcTemp;
        }
        
        public function executeLogic ( $aParameters = null ) {
            //var_dump( "yAppDefault.executeLogic - INI" );
            // 2018.04.28 - esta en desuso
            
            $this->setPath();
            
            $aResu = $this->getEvent();
            $aResu = $this->getCalltype();
            $aResu = $this->getView();
            $aResu = $this->getUser();
            
            $aParameters['landscape'] = $this->getLandscape();                                                                                            // identificar escenario
            //var_dump( $aParameters['landscape'] );

            // ejecutar logica asociada al escenario
            $aParameters = $this->oYLogicEngine->executeLogic( $aParameters );

            //var_dump( "yAppDefault.executeLogic - END" );
            return $aParameters;
        }
    // 2016.04.21 - Nueva estructura - INI

    public function userResgistration ( $aParameters = null ) {
        //var_dump( "yAppDefault.userResgistration" );
        //var_dump( $aParameters );
        //exit();

        $aResu['aDato'] = array();
        $aQuer = array(
            "sSele" => "SELECT * FROM usuario WHERE email = '[usuamail]' and flag_borrado = 0;",
        );
        $oBase = new base();
        $oUsua = new usuario();

        // 2019.09.14 - la vista de registracion envia este dato sin encriptacion. Hay que guardarlo
        // en la base encriptado.
        $aParameters['data']['password'] = md5( $aParameters['data']['password'] );
        //$aPara['password'] = md5($aPara['password']);
        //var_dump( $aParameters );

        $aPara     = $aParameters['data'];
        $sUsuanomb = $aPara['nombre'];
        $sUsuamail = $aPara['email'];
        $sUsuapass = $aPara['password'];
        
        // controlar los datos recibidos.
        //if ( $sUsuapass != $sPassauxi )
        //    $sMjse = "La contraseña no concuerda con la confirmación.";

        //if ( $sPassauxi == "" )
        //    $sMjse = "Debe ingresar la confirmación de la contraseña.";

        if ( $sUsuapass == "" )
            $sMjse = "Debe ingresar una contraseña.";

        if ( $sUsuamail == "" )
            $sMjse = "Debe ingresar un email.";
        else if ( strpos( $sUsuamail, "@" ) === false )
            $sMjse = "Debe ingresar un email valido.";

        if ( $sUsuanomb == "" )
            $sMjse = "Debe ingresar su nombre.";

        if ( $sMjse != "" )
            $bErro = true;

        if ( ! $bErro )
        {
            // controlar si existe la usuario
            $sQuer = $aQuer['sSele'];
            $sQuer = str_replace( "[usuamail]", $sUsuamail, $sQuer );
            $aBase = $oBase->procSent( $sQuer );
            //var_dump( $sQuer );
            //var_dump( $aBase );

            if ( $aBase['iCant'] > 0 ) 
            {
                $bErro = true;
                $sMjse = "El email ingresado pertenece a un usuario registrado.";    
            }
        }

        if ( ! $bErro )
        {
            if ( isset( $this->a_data['usuario'] ) )
            {
                //print_r( "yAppDefaults.userResgistration.update() <br> \n" );
                $aPara['id'] = $this->a_data['usuario'][0]['id'];
                $aR = $oUsua->update( $aPara );                                                                                                                         // crear el registro del nuevo usuario
            }
            else
            {
                //print_r( "yAppDefaults.userResgistration.create() <br> \n" );
                //var_dump( $aPara );
                $aR = $oUsua->create( $aPara ); 
                //var_dump( $aR );                                                                                                                        // crear el registro del nuevo usuario
            }
            
            // loguear al nuevo usuario
            $aValo = array(
                "usuamail" => $aPara['email'],
                "usuapass" => $aPara['password'],
            );
            $aResu = $this->login( $aParameters );
        }
        else
        {
            // devolucion a la vista
            $aResu['bProc'] = false;    
            $aResu['sProc'] = $sMjse;
        }

        // 2019.04.23 - Envio de email de alerta por la registracion exitosa
            //var_dump( $aResu );
            if ( $aResu['bProc'] )
            {
                $o_usuario = new usuario();
                $o_usuario ->aBase['aFilt'][] = "usuario.email = '$sUsuamail'";
                $a_usuario = $o_usuario->find();

                $id_aleatorio = $a_usuario[0]['id_aleatorio'];

                $FORM_REGISTRACION_REFERIDO_URL = FMWK_CLIE_SERV . "form/C4Y14E1Z?ref=[ref]";
                $FORM_REGISTRACION_REFERIDO_URL = str_replace( "[ref]", $id_aleatorio, $FORM_REGISTRACION_REFERIDO_URL );

                $a_mail = array();
                $a_mail['usuario'] = $a_usuario;
                $a_mail['empresa'] = array(
                    array( 
                        "empresa_nombre" => FMWK_CLIE_TITU, 
                        "FMWK_CLIE_SERV" => FMWK_CLIE_SERV,
                        "FORM_REGISTRACION_REFERIDO_URL" => $FORM_REGISTRACION_REFERIDO_URL,
                    ),
                );

                //var_dump( $a_mail );

                $o_mail = new mail();
                $s_mail = $o_mail ->send( array(
                  "aDest" => array( $a_usuario[0]['email'] ),                                
                  "sTitu" => FMWK_CLIE_TITU . " - Creación de cuenta",
                  "sTemp" => "registracion.php",
                  "aDato" => $a_mail,
                  "bDbug" => true,
                ) );
                //echo $s_mail;
            }

        //print_r( "yAppDefault.userResgistration --- FIN <br> <br> \n\n" );
        return $aResu;
    }

    public function component( $a_parameters, $i_index = null ) 
    {
            if ( is_null( $i_index ) )
                    $i_index = 0;

            $a_instancia = $a_parameters[ $i_index ];

            include( $a_instancia['file'] );
    }        

    // 2018.01.14 - metodo filter_set_value - INI
        /*  este metodo se encarga de analizar la fuente de datos actual (consulta SQL) y establecer
            los valores del filtro dentro de ella.
        */
        public function filter_set_value ( $a_parameters = null ) {
            //print_r( "yAppDefault.filter_set_value() <br /> \n" );   
            //print_r( $a_parameters );

            $_f = array();

            $b_component = isset( $a_parameters['a_post']['component'] );
            if ( $b_component )
                $b_component = $a_parameters['a_post']['component'] != "";

            if ( ! $b_component )
                return false;

            $s_component = "\\" . $a_parameters['a_post']['component'];
            $s_action= $a_parameters['a_post']['action'];
            $a_campo_busqueda = $a_parameters['a_campo_busqueda'];

            // 2018.05.27 - Obtener contenedor/es
                $_b = array();

                // obtener el componente filtro para conocer el formulario al que pertenece y asi obtener el contenedor
                // de datos que sera afectado con la actualizacion del filtro.

                // obtener filtro
                    $_b['s_filtro'] = $s_action;
                    $_b['o_filtro'] = $this->a_components[ $_b['s_filtro'] ];
                    //print_r( $_b['o_filtro']->a_config );

                // obtener formulario
                    $_b['s_form'] = $_b['o_filtro']->a_config['form'];
                    $_b['o_form'] = $this->a_components[ $_b['s_form'] ];
                    //print_r( $_b['o_form']->a_config );

                // obtener contenedor/res
                    $_b['a_container'] = $_b['o_form']->a_config['container'];
                    $_f['a_container'] = $_b['a_container'];

            // 2018.05.27 - Obtener fuente de datos desde el/los contenedor/es

                // historico: cuando los datos de la consulta definidos en la vista se guardaban en este espacio
                //$a_source = $this->a_view['a_config']['view-data']['a_entity_custom']['a_source'];
                
                // obtener el componente contenedor
                    $_f['s_container'] = $_f['a_container'][0];
                    $_f['o_container'] = $this->a_components[ $_f['s_container'] ];
                    $_f['s_sql']       = $_f['o_container']->a_config['data_source']['query'];
                    //print_r(  );

            // separar el filtro (where) de la consulta sql - INI
                //$s_sql = $a_source['s_sql_query'];
                $s_sql = $_f['s_sql'];
                $i_pos = strpos( $s_sql, "WHERE" );
                
                $i_pos = $i_pos !== false ? $i_pos : strlen( $s_sql );

                $s_qr1 = substr( $s_sql, 0, $i_pos );
                $s_qr2 = str_replace( $s_qr1, "", $s_sql );
                    
                // remover el ultimo caracter de retorno de carro
                $s_qr1 = trim( $s_qr1 );
                $i_qr1 = strrpos( $s_qr1, "\n" );

                // controlar que el retorno de carro sea el ultimo caracter. Sino quita partes del query
                if ( strlen( $s_qr1 ) - 1 === $i_qr1 )
                    $s_qr1 = substr( $s_qr1, 0, $i_qr1 );

                // consulta - parte where. dividirla clausula where
                $a_qr2 = explode( "\n AND ", $s_qr2 );    

                //print_r( "separar el filtro (where) de la consulta sql ----------------------- \n");
                //print_r( "s_sql: $s_sql \n" );
                //print_r( "s_qr1: $s_qr1 \n" );
                //print_r( "s_qr2: $s_qr2 \n" );
                //print_r( $a_qr2 );
                //print_r( "separar el filtro (where) de la consulta sql ----------------------- \n\n");
            // separar el filtro (where) de la consulta sql - FIN

            // consulta - parte where. analizar cada clausula where - INI
                $a_qr2_de_base = array();                                                               // las clausalas que no pertenecen a los filtros del script
                $a_qr2_where   = array();                                                               // las clausukas que si pertenecen

                //print_r( $a_qr2 );
                foreach ( $a_qr2 as $i_qr2_posicion => $s_qr2_clausula ) 
                {
                    // controlar si la clausula corresponde a alguno de los filtros del script
                    foreach ( $a_campo_busqueda as $s_filtro_config => $a_filtro_config ) 
                    {
                        $b_es_filtro = strpos( $s_qr2_clausula, $a_filtro_config['where_campo'] ) === false ? 
                            false : true;

                        // si es filtro se guarda en el array del where
                        if ( $b_es_filtro )
                        {
                            $s_qr2_clausula = trim( str_replace( "WHERE", "", $s_qr2_clausula ) );      // quitar el where porque sino se repite en cada iteracion
                            //break 1;

                            // 2018.02.09 - controlar si el valor existe en el array. Lo que significa
                            // que otros filtros comparten el mismo/los mismos campos. Esto sucede en 
                            // silvestris:registro:latitud
                            $x_qr2_search = array_search( $s_qr2_clausula, $a_qr2_where );   
                            $b_qr2_search = $x_qr2_search === false ? false : true;

                            if ( ! $b_qr2_search )
                            {
                                $a_qr2_where[ $s_filtro_config ] = $s_qr2_clausula;
                            }
                            else
                            {
                                if ( $x_qr2_search != $s_action )
                                {
                                    unset( $a_qr2_where[ $x_qr2_search ] );
                                    $a_qr2_where[ $s_filtro_config ] = $s_qr2_clausula;
                                }
                            }
                        }
                    }
                    
                    // 2018.02.15 - si la clausula no pertenece a un filtro se guarda en el array de
                    // las clausulas que pertenecen a la consulta base.
                    // pero como el for anterior debe ser recorrido completamente para determinar 
                    // que dos filtros no comparten la misma clausula, es que se debe controlar el
                    // valor de la clausula dentro del array y no la bandera
                    //if ( ! $b_es_filtro )
                    //    $a_qr2_de_base[] = $s_qr2_clausula;
                    if ( ! in_array( $s_qr2_clausula, $a_qr2_where ) )
                        $a_qr2_de_base[] = $s_qr2_clausula;

                }

                //print_r( "consulta - parte where. analizar cada clausula where --------------- \n");
                //print_r( $a_qr2_de_base );
                //print_r( $a_qr2_where );
                //print_r( "consulta - parte where. analizar cada clausula where --------------- \n\n");
            // consulta - parte where. analizar cada clausula where - FIN

            // consulta - re construir la consulta base - INI
                $s_qr2_de_base = "";
                
                foreach ( $a_qr2_de_base as $i_clausula => $s_clausula ) 
                {
                    $s_clausula = str_replace( "\n", "", $s_clausula );
                    $s_clausula = trim( $s_clausula );

                    if ( $s_clausula == "" )
                        continue;

                    $s_clausula = strtolower( $s_clausula );
                    if ( strpos( $s_clausula, "where" ) === 0 )
                        $s_clausula = trim( substr( $s_clausula, 5 ) ); //( "where", "", $s_clausula );

                    $s_qr2_de_base .= ( $i_clausula == 0 ? "WHERE " : " AND " ) . $s_clausula;
                }

                if ( $s_qr2_de_base != "" )
                {
                    // controlar si tiene retorno de carro al principio.
                    $i_qr2_de_base = strpos( trim( $s_qr2_de_base ), "\n" );

                    if ( $i_qr2_de_base !== 0 )
                        $s_qr2_de_base = "\n" . $s_qr2_de_base;
                }
            // consulta - re construir la consulta base - FIN

            // consulta - procesar filtro actual - INI
                
                //var_dump( $s_component );
                $s_post_value = $s_component::get_where_clause( $a_parameters );
                //var_dump( $s_post_value );

                $s_filtro_campo    = $a_campo_busqueda[ $s_action ]['where_campo'];
                $s_filtro_clausula = $a_campo_busqueda[ $s_action ]['where_clausula'];
                $s_filtro_clausula = str_replace( "[campo_busqueda]", $s_filtro_campo, $s_filtro_clausula );
                $s_filtro_clausula = str_replace( "[post_value]",     $s_post_value,   $s_filtro_clausula );
                $a_qr2_where[ $s_action ] = $s_filtro_clausula;

                //print_r( "s_filtro_clausula: " . $s_filtro_clausula . "\n" );
            // consulta - procesar filtro actual - FIN

            // control del valor - INI
                //print_r( "control del valor - INI ------------------------------------------ \n");
                //print_r( "s_post_value: $s_post_value \n" );
                //print_r( "s_action: $s_action \n" );
                //print_r( $a_qr2_where );

                // si el valor es vacio se quita la clave del array para la construccion del WHERE
                if ( trim( $s_post_value == "" ) || trim( $s_post_value == "''" )  )
                    unset( $a_qr2_where[ $s_action ] );

                if ( array_key_exists( $s_post_value, $a_campo_busqueda[ $s_action ]['value'] ) )
                {
                    $b_eliminar_clausula = $a_campo_busqueda[ $s_action ]['value'][ $s_post_value ]['flag_eliminar_clausula'];

                    if ( $b_eliminar_clausula )
                        unset( $a_qr2_where[ $s_action ] );
                }
                
                //print_r( $a_qr2_where );
                //print_r( "control del valor - FIN ------------------------------------------ \n\n");
            // control del valor - FIN

            // consulta - re construir la consulta where de los filtros - INI
                
                // controlar si existe la palabra clave WHERE en la consulta
                $b_keyword_where_exists = strpos( $s_qr2_de_base, "WHERE" );
                $b_keyword_where_exists = $b_keyword_where_exists !== false ? true : false;

                $i_qr2_where = 0;
                $s_qr2_where = "";
                
                foreach ( $a_qr2_where as $s_filtro => $s_clausula ) 
                {
                    // 2019.04.19 - 2do chequeo de valores del filtro. Si esta vacio no se considera
                        if ( $a_parameters['a_post']['action'] == $s_filtro )
                        {
                            if ( ! isset( $a_parameters['a_post']['value'] ) )
                                continue;
                        }

                    $s_qr2_where .= "\n";
                    
                    if ( $i_qr2_where == 0 ) 
                        $s_qr2_where .= $b_keyword_where_exists ? " AND " : " WHERE ";
                    else
                        $s_qr2_where .= " AND ";
                            
                    $s_qr2_where .= $s_clausula;
                    $i_qr2_where++;
                }
            // consulta - re construir la consulta where de los filtros - FIN
            
            $s_sql = $s_qr1 . $s_qr2_de_base . $s_qr2_where;
            //print_r( $s_sql );

            $this->a_components[ $_f['s_container'] ]->a_config['data_source']['query'] = $s_sql ;
            $this->a_containers['s_filters_query_where'] = $s_qr2_where;                            // 2019.04.19

            // filtros: almacenamiento de id y valor/es ingresados.
            // puede ser utilizado para mantener los filtros entre otras vistas y/o al actualizar

                $x_filtro_value = null;
                $s_filtro_id    = $a_parameters['a_post']['action'];
                
                if ( isset( $a_parameters['a_post']['value'] ) )
                    $x_filtro_value = $a_parameters['a_post']['value'];
        
                $this->a_filters['a_in_use'][ $s_filtro_id ] = $x_filtro_value;            
             
            $this->save();
            $this->save_in_file();
        }
    // 2018.01.14 - metodo filter_set_value - FIN

    // 2018.01.14 - metodo get_input_load_data - INI
        public function get_input_load_data ( $a_parameters = null ) {

            $a_post = $a_parameters['a_post'];
            $a_target_component_sql = $a_parameters['a_target_component_sql'];

            $o_base = new base();
            $s_source_component = $a_post['source_component'];
            $a_result = array();

            $s_source_value = "";
            $a_source_value = $a_post['source_value'];
            foreach ( $a_source_value as $i_value => $s_value )
                $s_source_value .= ( $i_value > 0 ? ", " : "" ) . 
                    ( is_numeric( $s_value ) ? "$s_value" : "'$s_value'" );

            foreach ( $a_post['target_component'] as $i_target_component => $s_target_component ) 
            {

                // 2018.02.05 - se implementa este control de esta forma para lograr compatibilidad 
                // hacia atras. Caso contrario se deben cambiar todos los valores del array.
                //if ( isset( $a_target_component_sql[ $s_target_component ][ $s_source_component ] ) )
                if ( is_array( $a_target_component_sql[ $s_target_component ] ) )
                    $s_sql = $a_target_component_sql[ $s_target_component ][ $s_source_component ];
                else
                    $s_sql = $a_target_component_sql[ $s_target_component ];
                
                $s_sql = str_replace( "[source_value]", $s_source_value, $s_sql );

                $a_base = $o_base->procSent( $s_sql );
                $a_data = $a_base['aDato'];

                $a_result[ $s_target_component ] = $a_data;
            }

            return $a_result;
        }
    // 2018.01.14 - metodo get_input_load_data - FIN

    // 2018.03.10 - metodo mem - INI
        public function mem ( $a_parameters = null ) {
            print_r( "yAppDefault.mem() <br /> \n" );
            print_r( $a_parameters );

            $s_propiedad = $a_parameters['property'];
            $s_clave     = $a_parameters['key'];
            
            // 2018.01.03 - obtener la session desde archivo - INI
                $s_file_session = FMWK_CLIE_DIRE . "project/php/core/app_session_object.txt";
                $o_gestor       = fopen( $s_file_session, "rb" );
                $s_contenido    = fread( $o_gestor , filesize( $s_file_session ));
                fclose( $o_gestor );
                //print_r( $s_contenido );

                //session_start();
                $_SESSION['a_app']['silvestris'] = $s_contenido;
            // 2018.01.03 - obtener la session desde archivo - FIN

            $oYAppSess = unserialize( $_SESSION['a_app'][ $this->s_app_name ] );
            print_r( count( $oYAppSess->a_components ) );
        }
    // 2018.03.10 - metodo mem - FIN

    // 2018.03.13 - metodo view_snapshot_save() - INI
        public function view_snapshot_save ( $a_parameters = null ) {
            //print_r( "yAppDefault.view_snapshot_save() <br /> \n" );
            //print_r( $a_parameters ); 

            $a_view_snapshot = array();
            $a_snapshot_data = array();

            // crear array de view_snapshot
            foreach ( $this->a_components as $s_component_id => $o_component_class ) 
            {
                //print_r( $s_component_id . "<br> \n" );
                //print_r( $o_component_class );

                $b_snapshot = isset( $o_component_class->b_snapshot ) ? $o_component_class->b_snapshot : false;
                //print_r( $b_snapshot . "<br> \n" );
                
                if( $b_snapshot )
                {
                    $a_component_snapshot = $o_component_class->a_snapshot;
                    $s_component_snapshot = json_encode( $a_component_snapshot );
                    //print_r( $a_component_snapshot );

                    // cambio global de los tipos de datos dentro del json
                    $s_component_snapshot = $this->json_properties_parser( array(
                        "s_json_data"  => $s_component_snapshot,
                        "s_return_key" => "s_json_data",
                    ));
                    
                    // cambio especifico de los tipos de datos dentro del json segun el componente
                    $s_component_snapshot = $o_component_class->json_properties_parser( array(
                        "s_json_data"  => $s_component_snapshot,
                        "s_return_key" => "s_json_data",
                    ));

                    // se vuelve a crear el array porque se guarda un json con todos los componentes
                    // de la vista
                    $a_component_snapshot = json_decode( $s_component_snapshot, true );

                    // se guarda en el array del snapshot del componente en el array del snapshot 
                    // global
                    $a_snapshot_data[ $s_component_id ] = $a_component_snapshot;
                }
            }

            $s_snapshot_data = json_encode( $a_snapshot_data );
            //print_r( $s_snapshot_data );

            // guardar en la base de datos el array del snapshot.
            $a_view_snapshot = array(
                "usuario_id" => 1,
                //"nombre"     => "test-" . date( "YmdHis" ),
                "nombre"     => $a_parameters['name'],
                "data"       => $s_snapshot_data,
            );
            
            $o_app_view_snapshot = new app_view_snapshot();
            $o_app_view_snapshot ->create( $a_view_snapshot );
        }
    // 2018.03.13 - metodo view_snapshot_save() - FIN

    // 2018.03.13 - metodo view_snapshot_load() - INI
        public function view_snapshot_load ( $a_parameters = null ) {
            //print_r( "yAppDefault.view_snapshot_load() <br /> \n" );
            //print_r( $a_parameters ); 

            // obtener el view snapshot
            $o_app_view_snapshot = new app_view_snapshot();
            $o_app_view_snapshot ->aBase['aFilt'][] = "app_view_snapshot.usuario_id = 1";
            $o_app_view_snapshot ->aBase['aOrde'][] = "app_view_snapshot.id DESC";
            $o_app_view_snapshot ->aBase['aLimi']['iInic'] = 0;
            $o_app_view_snapshot ->aBase['aLimi']['iCant'] = 1;
            $a_app_view_snapshot = $o_app_view_snapshot->find();
            //print_r( $a_app_view_snapshot );

            // guardarlo en el objeto de sesion
            $a_snapshot_data = json_decode( $a_app_view_snapshot[0]['data'], true );
            
            $this->a_view_snapshot = $a_snapshot_data;
            $this->save();
            $this->save_in_file();
        }
    // 2018.03.13 - metodo view_snapshot_load() - FIN

    // 2018.03.20 - metodo js_class_request() - INI
        public function js_class_request ( $a_parameters = null ) {
            //print_r( "yAppDefault.js_class_request()" . "<br /> \n" );
            //print_r( $a_parameters );

            switch ( $_POST['method'] ) 
            {
                case 'view_snapshot_save':
                    $this->view_snapshot_save( $_POST );
                    break;
                case 'view_snapshot_load':
                    $this->view_snapshot_load();
                    break;
                case 'get_handlers':
                    $a_result = $this->get_handlers();
                    //print_r( $a_result );
                    return $a_result;
                    break;
                case 'event_handler':
                    $a_result = $this->event_handler( $a_parameters );
                    //print_r( $a_result );
                    return $a_result;
                    break;
                default:
                    # code...
                    break;
            }
        }
    // 2018.03.20 - metodo js_class_request() - FIN

    // 2018.03.20 - metodo json_properties_parser() - INI
        public function json_properties_parser ( $a_parameters = null ) {
            //print_r( "yAppDefault.json_properties_parser() <br /> \n" );
            //print_r( $a_parameters ); 
            
            $b_return_key = isset( $a_parameters['s_return_key'] ) ? true : false;
            $b_config     = isset( $a_parameters['a_config'] ) ? true : false;
            $s_return_key = $b_return_key ? $a_parameters['s_return_key'] : "";
            $s_json_data  = $a_parameters['s_json_data'];
            $a_config     = $b_config ? $a_parameters['a_config'] : array();

            if ( ! $b_config )
            {
                // se procesa un cambio global en los tipos de datos del json recibido

                // primera serie de cambio de valores
                $s_json_data = str_replace( ':""',      ":null",  $s_json_data );
                $s_json_data = str_replace( ':"false"', ":false", $s_json_data );
                $s_json_data = str_replace( ':"true"',  ":true",  $s_json_data );

                // quitar comillas a los valores numericos
                preg_match_all( '/\"[0-9.]+\"/', $s_json_data, $output_array );
                foreach ( $output_array[0] as $key => $value ) 
                {
                    $s_number    = $value;
                    $i_number    = str_replace( '"', '', $s_number );
                    $s_json_data = str_replace( $s_number, $i_number, $s_json_data );
                }
            }
            else
            {

                // se procesa la configuracion enviada por el componente
                foreach ( $a_config as $s_config => $a_config_data ) 
                {
                    foreach ( $a_config_data['a_properties'] as $i_property => $s_property ) 
                    {
                        $s_reg_exp = $a_config_data['s_reg_exp'];
                        $s_reg_exp = str_replace( "[property]", $s_property, $s_reg_exp );
                        
                        preg_match_all( $s_reg_exp, $s_json_data, $output_array );
                        
                        $s_search  = '"' . $s_property . $a_config_data['a_replace'][0];
                        $s_replace = '"' . $s_property . $a_config_data['a_replace'][1];
                        
                        foreach ( $output_array[0] as $key => $value ) 
                        {
                            $s_value  = $value;
                            $s_value  = str_replace( $s_search, $s_replace, $s_value );
                            $s_value .= $a_config_data['s_final'];
                            
                            $s_json_data = str_replace( $value, $s_value, $s_json_data );    
                        }
                    }
                }
            }
            
            // guardar el resultado en el array pasado como parametro
            $a_parameters['s_json_data'] = $s_json_data;

            // devolver los valores especificados o todo el array pasado como parametro
            $x_return = $b_return_key ? $a_parameters[ $s_return_key ] : $a_parameters; 
            return $x_return;            
        }
    // 2018.03.20 - metodo json_properties_parser() - FIN

    // 2018.03.31 - metodo set_root_distance() - INI
        public function set_root_distance ( $a_parameters = null ) {
            //var_dump( "yAppDefault.set_root_distance <br /> \n" );

            if ( $this->ROOT_DISTANCE == "" ) 
            {
                $s_file_http = FMWK_CLIE_SERV . $this->a_view['s_file'];
                $i_folders = substr_count( $s_file_http, "/" ) - 4; // dos del http 
                $s_root_distance = str_repeat( "../", $i_folders );

                $this->ROOT_DISTANCE = $s_root_distance;
            }

            return $this->ROOT_DISTANCE;
        }
    // 2018.03.31 - metodo set_root_distance() - FIN

    // 2018.05.10 - metodo set_handler()
        public function set_handler ( $a_parameters = null ) {
            //print_r( "yAppDefault.set_handler()" . "<br /> \n" );
            //print_r( $a_parameters );

            $s_element_html_id = $a_parameters['id'];

            if ( ! isset( $this->a_components['handler_cache'] ) )
                $this->a_components['handler_cache'] = array();

            $this->a_components['handler_cache'][ $s_element_html_id ] = $a_parameters;

            $this->save();
            $this->save_in_file();
        }

    // 2018.05.10 - metodo set_handler()
        public function get_handlers ( $a_parameters = null ) {
            //print_r( "yAppDefault.get_handlers()" . "<br /> \n" );
            //print_r( $a_parameters );

            if ( ! isset( $this->a_components['handler_cache'] ) )
                $this->a_components['handler_cache'] = array();

            //print_r( $this->a_components['handler_cache'] );

            return $this->a_components['handler_cache'];
        }

    // 2018.05.10 - metodo event_handler()
        public function event_handler ( $a_parameters = null ) {
            //print_r( "yAppDefault.event_handler()" . "<br /> \n" );
            //print_r( $a_parameters );

            $b_custom = isset( $a_parameters['data']['custom'] ) ? true : false;
            $a_result = array();

            if ( $b_custom )
            {
                include( $a_parameters['data']['custom'] );

                $s_name = $a_parameters['data']['id'];
                
                if ( ! class_exists( $s_name ) ) 
                    $s_name = $a_parameters['data']['css_class'];

                $a_result = $s_name::event_click( $a_parameters );   
                //print_r( $a_result );
            }

            return $a_result;
            
        }

    // 2018.11.10 - generar codigo QR - incluye archivo PNG

        public function qr ( $a_parameters = null ) {
            //var_dump( "yAppDefault.qr" );
            //var_dump( $a_parameters );

            // se le pone este nombre porque en el futuro esto puede ser resuelto por una clase.

            /*/ debug

                $a_parameters = array(
                    "qr_data" => "https://www.google.com",
                    //"qr_folder_path" => "",
                    //"qr_file_name" => "qr_" . date( "Ymdhis" ),
                );
                //*/

            // variables
                
                $s_vendor_api_url = "http://api.qrserver.com/v1/create-qr-code/?data=[data]&size=[width]x[height]";
                
                $b_qr_file_create = false;
                
                $s_qr_data        = isset( $a_parameters['qr_data'] )        ? $a_parameters['qr_data'] : "";
                $s_qr_folder_path = isset( $a_parameters['qr_folder_path'] ) ? $a_parameters['qr_folder_path'] : FMWK_CLIE_DIRE . "project/upld/qr/";
                $s_qr_file_name   = isset( $a_parameters['qr_file_name'] )   ? $a_parameters['qr_file_name'] : "qr_default_name_" . date( "Ymdhis" );
                $i_qr_width       = isset( $a_parameters['qr_width'] )       ? $a_parameters['qr_width'] : 300;
                $i_qr_height      = isset( $a_parameters['qr_height'] )      ? $a_parameters['qr_height'] : 300;
                
                $s_qr_file_name  .= ".png";
                $s_qr_file_path   = $s_qr_folder_path . $s_qr_file_name;
                $s_qr_file_http   = str_replace( FMWK_CLIE_DIRE, FMWK_CLIE_SERV, $s_qr_file_path );

                //var_dump( $s_qr_file_path );

            // controlar existencia del archivo            

                $b_qr_file_create = ! file_exists( $s_qr_file_path );

            // ejecutar el request a la api del vendor

                if ( $b_qr_file_create )
                {
                    $s_vendor_api_url = str_replace( "[data]",   $s_qr_data,   $s_vendor_api_url );
                    $s_vendor_api_url = str_replace( "[width]",  $i_qr_width,  $s_vendor_api_url );
                    $s_vendor_api_url = str_replace( "[height]", $i_qr_height, $s_vendor_api_url );
                    //var_dump( $s_vendor_api_url );

                    //Get the file
                    $content = file_get_contents( $s_vendor_api_url );
                    //$fp = fopen( FMWK_CLIE_DIRE . "project/upld/qr/$s_file_name", "w");
                    $fp = fopen( $s_qr_file_path, "w");
                    fwrite( $fp, $content );
                    fclose( $fp );
                }

            // devolver informacion

                $a_parameters['qr_file_create'] = $b_qr_file_create;
                $a_parameters['qr_file_path']   = $s_qr_file_path;
                $a_parameters['qr_file_http']   = $s_qr_file_http;

            return $a_parameters;
        }
        //*/

    // 2019.01.31 - generar codigo aletario y controlar la existencia en la tabla, campo definido

        public function instancia_codigo_aleatorio ( $a_parameters = null ) {
            //var_dump( "yAppDefault.instancia_codigo_aleatorio" );
            //var_dump( $a_parameters );

            // PENDIENTE: se debe analizar la longitud del campo al cual se le quiere asignar el
            // codigo aleatorio.

            // generacion codigo aleatorio con control valor unico
            
            // variables 
                $o_base = new base();
                
                $s_codigo_aleatorio = "";

                $i_codigo_longitud  = $a_parameters['codigo_longitud']; //16;
                $s_control_entidad  = $a_parameters['entidad_nombre'];  //"usuario";
                $s_control_campo    = $a_parameters['entidad_campo'];   //"id_aleatorio";
                
                $b_instancia_id = isset( $a_parameters['instancia_id'] );
                $s_instancia_id = $b_instancia_id ? $a_parameters['instancia_id'] : 0;    //11;

                // parametro persistir: por defecto true para que no afecte al codigo donde se ha implementado el metodo
                $b_persistir = isset( $a_parameters['persistir'] ) ? $a_parameters['persistir'] : true;

            // controlar si el campo tiene valor
                
                $b_procesar = true;

                if ( $b_instancia_id )
                {
                    $s_sql = "SELECT * FROM $s_control_entidad WHERE id = '$s_instancia_id'";
                    $a_sql = $o_base->procSent( $s_sql );
                    
                    $a_instancia = $a_sql['aDato'];
                    $b_instancia = ! empty( $a_instancia );

                    if( $b_instancia )
                        $b_procesar = $a_instancia[0][ $s_control_campo ] == "" ? true : false;
                }

            if ( $b_procesar )
            {
                // generacion codigo aleatorio con control valor unico

                    do 
                    {
                        $s_codigo_aleatorio = $this->generate_random_code( array( "iLong"=> $i_codigo_longitud ) );

                        // controlar si existe
                            $s_sql = "SELECT * FROM $s_control_entidad WHERE $s_control_campo = '$s_codigo_aleatorio'";
                            $a_sql = $o_base->procSent( $s_sql );
                        
                    } while ( ! empty( $a_sql['aDato'] ) );

                // persistir

                    if ( $b_persistir )
                    {
                        $s_query_sql_update = "UPDATE $s_control_entidad SET $s_control_campo = '$s_codigo_aleatorio' WHERE id = $s_instancia_id;";
                        $o_base->procSent( $s_query_sql_update );
                    }
            }

            return $s_codigo_aleatorio;
        }

    // 2020.12.23 - refresh session object
        
        public function refresh_session_object ( $a_parameters = null ) {
            //print_r( "yAppDefault.refresh_open_session_object() <br /> \n" );
            //print_r( $a_parameters );

            // 2020-12-23 - limpiar el array de componentes
            // es el que incrementa hasta alcanzar el limite de variable de PHP
            // crear un nuevo objeto no funciona porque al ir a otras vistas, no se puede acceder a clases

            $this->a_components = array();
            $this->save();
        }

    // 2022.06.30 - obtener ip del visitante

        public function get_user_ip()
        {
            // Recogemos la IP de la cabecera de la conexión
            if (!empty($_SERVER['HTTP_CLIENT_IP']))   
            {
                $ipAdress = $_SERVER['HTTP_CLIENT_IP'];
            }
            // Caso en que la IP llega a través de un Proxy
            elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))  
            {
                $ipAdress = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            // Caso en que la IP lleva a través de la cabecera de conexión remota
            else
            {
                $ipAdress = $_SERVER['REMOTE_ADDR'];
            }
            return $ipAdress;
        }

    // 2024.08.21 - generar codigo aleatorio segun longitud requerida
        
        public function generate_random_code ( $aValo ) {
            $cantChar = $aValo['iLong'];
            for( $l = 0; $l < $cantChar; $l++ )
            $vNS[] = mt_rand(0, 1) ? chr(mt_rand(65, 90)) : chr(mt_rand(48, 57));
            return implode( "", $vNS );
        }
}