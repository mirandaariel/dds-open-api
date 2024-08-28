<?php


/* -----------------------------------------------------------------------------
Metodos:
set: es el equivalente de persistir. Si la instancia existe hace UPDATE.
get: es el equivalente de instanciar
conf: inicializacion de los valores necesarios para la clase base.
busc: busca y devuelve instancias de la clase.
// ---------------------------------------------------------------------------*/

//include_once( FMWK_YOBI_SERV."php/core/base.cls" );
//include_once( FMWK_YOBI_SERV."php/func/fmwkimag.cls" );

class clas {
    
    public $b_with_relations = false;
    
    protected $bInst = false;
    protected $aInst = array();
    public $aProp = array();                                                                                                            // columnas de la base
    public $aEtiq = array();                                                                                                            // Labels de los campos
    public $aTipo = array(); // tipo de dato de las columnas de la base
    public $aBase = array(
        "aEnti" => array(),
        "aClav" => array(),                                                                                                                 // campos claves
        "aUnic" => array(),                                                                                                                 // campos unicos
        "aColu" => array(),
        "aJoin" => array(),
        "aFilt" => array(),
        "aLimi" => array( 
            "iInic" => 0, 
            "iCant" => null 
        ),
        "aOrde" => array(),
        "aDato" => array(),
        "sChst" => "",// definir el charset ejemplo utf8
    );
    
    public $flag_show_as_dependency_entity = true;

    function control_parameters ( $a_parameters = null ) {
        //var_dump( "framework clas.control_parameters" );
        //var_dump( $a_parameters );
        
        // controlar si algun elemento del array contiene un array y eliminarlo
        foreach ( $a_parameters as $s_clave_name => $x_item_value ) {
            if ( is_array( $x_item_value ) && $s_clave_name != "aFile" )
                unset( $a_parameters[ $s_clave_name ] );
        }

        return $a_parameters;
    }

    function control_persistence ( $a_parameters = null ) {
        //var_dump( "framework clas.control_persistence" );
        //var_dump( $a_parameters );

        $b_file = isset( $a_parameters['aDato']['aFile'] );
        $a_file = $b_file ? $a_parameters['aDato']['aFile'] : array();
        
        // 2022.08.24 - Control porque no se logra identificar porque se debe controlar la existencia de 
        // archivos en entidades que no tienen relacion con archivos.
        if ( ! $b_file )
            if ( $a_parameters['iEsta'] == 1 )
                $b_file = true;

        $aResu = $a_parameters;
        if ( $aResu['iEsta'] == 1 && $b_file )                                                                 // ejecucion exitosa de la sentencia
        {
            if ( $aResu['iCant'] == 0 )                                                             // no se afectaron registros.                
            {
                $aResu['sProc'] = "No se ha afectado a algun registro.";
                $aResu['bProc'] = true;
            }
            else
            {
                $aResu['sProc'] = "Se ha completado el proceso exitosamente.";
                $aResu['bProc'] = true;
                
                // variables
                    $sClas = "";
                    $sAgru = "";
                    $sInst = "";

                // obtener ruta de la carpeta de la clase
                    $sRoot    = $_SERVER['DOCUMENT_ROOT'].FMWK_CLIE_ROOT;
                    $s_modulo = FMWK_CLIE_MODU ? "../../" : ""; 
                    $sClas    = $sRoot . $s_modulo . "upld/".$this->aBase['aEnti'][0]."/";
                
                // obtener ruta de la carpeta de la instancia y del grupo de segun id de la instancia
                    $aValo = $a_parameters['aDato'];
                
                    foreach( $this->aBase['aClav'] as $iIden => $sCamp )                               
                    {
                        if ( isset( $aValo[ $sCamp ] ) )
                        {
                            if ( $aValo[ $sCamp ] == "" )                                                   // al estar vacio se trata de un registro que se ha insertado
                                $aValo[ $sCamp ] = $aResu['iIden'];                                         // el indice iIden devuelve el ultimo id utilizado.
                            $sInst .= ( ( $sInst != "" )? "." : "" ).$aValo[ $sCamp ];
                        }
                    }
                    $sAgru = $sClas.substr( $sInst, -1, 1 )."/"; // carpeta que agrupara segun el ultimo caracter de la instancia.
                    $sInst = $sAgru.$sInst."/";                  // se define el directorio de archivos para la instancia de la clase

                // control existencia de la carpetas

                    $b_clas   = file_exists( $sClas );
                    $b_agru   = file_exists( $sAgru );
                    
                    // 2024.08.23 - control plataform storage
                    if ( ! CLOUD_PLATFORM_STORAGE_FLAG )
                    {
                        if ( ! $b_clas ) mkdir( $sClas );
                        if ( ! $b_agru ) mkdir( $sAgru );
                    }
                
                    // debug
                    //echo "<pre> sClas: ";  var_dump( $sClas ); echo "</pre>";
                    //echo "<pre> sAgru: ";  var_dump( $sAgru ); echo "</pre>";
                    //echo "<pre> sInst: ";  var_dump( $sInst ); echo "</pre>";
                
                // 2017.01.18 - Inclusion de los indices en $aFile porque se generaba un error.
                // como se pasa de aun archivo funciona. Si se llega a pasar mas de uno en el mismo
                // nombre de campo, puede ser que no procese al resto.
                
                // 2024.08.23 - control plataform storage
                if ( ! CLOUD_PLATFORM_STORAGE_FLAG )
                {
                    if ( isset( $aValo['aFile'] ) )
                    {
                        //var_dump( $aValo['aFile'] );
                        foreach( $aValo['aFile'] as $sCamp => $aFile )                                  // se recorren el array proveniente de $_FILES
                        {
                            // 2017.01.23 - controlar si el array aFile tiene o no indices para las 
                            // claves de la carga del archivo, si no tiene es mas facil llegarlo a que
                            // tenga
                            if ( ! isset( $aFile['error'][0] ) && isset( $aFile['error'] ) )
                            {
                                $aFile['error']    = array( $aFile['error'] );
                                $aFile['tmp_name'] = array( $aFile['tmp_name'] );
                                $aFile['name']     = array( $aFile['name'] );
                                $aFile['type']     = array( $aFile['type'] );
                                $aFile['size']     = array( $aFile['size'] );
                            }
                            
                            if ( isset( $aValo[$sCamp.'_elim'] ) || $aFile['error'][0] == UPLOAD_ERR_OK )
                            {
                                foreach ( glob( $sInst.$sCamp."*.*" ) as $sFile )                       // se verifica la existencia de archivos anteriores para el campo de la clase
                                {
                                    unlink( $sFile );                                                                                                     // se elimina el archivo de referencia
                                    unlink( str_replace( $sCamp."_", "", $sFile ) );                    // se elimina el archivo anteriormente cargado
                                }
                            }

                            if ( $aFile['error'][0] == UPLOAD_ERR_OK )                                     // se controla que la carga haya sido exitossa
                            {    
                                if ( ! file_exists( $sInst ) )                                          // verificar si existe la carpeta de la instancia de la clase
                                    mkdir( $sInst );
                                
                                // 2017.02.13 - control para la subida directa proveniente de una carga
                                // o indirecta, si se copia un archivo y se quiere actualizar el registro
                                // a traves del framework.
                                $s_archivo_origen  = $aFile['tmp_name'][0];
                                $s_archivo_destino = "$sInst".$aFile['name'][0];
                                $b_archivo_origen  = file_exists( $s_archivo_origen );
                            
                                // carga directa
                                $b_move = move_uploaded_file( $s_archivo_origen, $s_archivo_destino );    // se mueve el archivo enviado
                                
                                // copia
                                if ( ! $b_move && $b_archivo_origen )
                                    $b_move = rename( $s_archivo_origen , $s_archivo_destino );

                                fclose( fopen( "$sInst".$sCamp."_".$aFile['name'][0], "w" ) );                 // se crea el archivo de referencia
                            }
                        }
                        fmwkimag::procImag( $aValo, $this );
                    }
                }
            }
        }
        else 
        {
            $aResu['sProc'] = "Error en la base de datos.";
            $aResu['bProc'] = false;
        }
        return $aResu;
    }

    function output_format ( $a_parameters = null ) {
        //print_r( "fmwk.clas.output_format" );
        //var_dump( $a_parameters );

        $aInst     = $a_parameters;
        
        //$oFmwkconf = new fmwkconf();
        //$aImagConf = $oFmwkconf->obteImag();
        
        $a_media_atributo   = array( "imagen_portada", "imagen_perfil", "vista_logo_claro",
            "vista_logo_oscuro", "parallax_background", "parallax_objeto", );
        $a_parrafo_atributo = array( "contenido", "descripcion" );
        

        foreach( $aInst as $iInstPosi => $aInstValo )
        {   
            $a_media_por_ratio = array();
            
            /*/var_dump( $aInstValo );
            // esta seteada la entidad relacionada
            if ( isset( $aInstValo['media_imagen'] ) )
            {
                // obtener todass las rutas de los archivos de imagen de portada y de perfil, para
                // todas las dimensiones que se encuentran configuradas ( $oFmwkconf->obteImag ) en 
                // la app.
                foreach ( $aInstValo['media_imagen'] as $i_media_imagen => $a_media_imagen ) 
                {
                    // 2017.07.14 - creo la clave que representa al atributo de la entidad foranea 
                    // que corresponde al input de un archivo de imagen.
                    $s_imagen_campo = $a_media_imagen['mimgfatt'];
                    $s_imagen_ratio = $a_media_imagen['mimgfrat'];
                    if ( ! isset( $a_media_por_ratio[ $s_imagen_campo ] ) )
                        $a_media_por_ratio[ $s_imagen_campo ] = array();

                    // campos de la entidad que son configurados como imagenes.
                    foreach ( $a_media_atributo as $i_media_atributo => $s_media_atributo) 
                    {
                        // controlo que la instancia de media se corresponda con el campo de imagen
                        // de la entidad.
                        if ( $a_media_imagen['mimgfatt'] == $s_media_atributo )
                        {
                            // primero se guardan las direcciones del archivo original que fue subido.
                            $s_instancia_imagen_campo = $s_media_atributo . '_origfile';
                            $aInstValo[ $s_instancia_imagen_campo ] = $a_media_imagen[ 'mimgnomb_origfile' ];

                            // 2017.07.14 - dentro el array auxiliar por ratio asigno las diferentes
                            // configuraciones de demiensiones seteadas en el app para los campos de
                            // la entidad
                            $a_media_por_ratio[ $s_imagen_campo ][ $s_imagen_ratio ] = array(
                                $s_instancia_imagen_campo => $aInstValo[ $s_instancia_imagen_campo ],
                            );

                            // luego obtienen las direcciones de las otras dimensiones configuradas
                            // en la app.
                            foreach ( $aImagConf['media_imagen']['mimgnomb'] as $s_imagen_dimension => $a_imagen_dimension) 
                            {
                                //var_dump( $s_imagen_dimension );
                                //var_dump( $a_imagen_dimension );
                                $s_instancia_imagen_campo = $s_media_atributo . '_' . $s_imagen_dimension;
                                //if ( isset( $aInstValo[ $s_instancia_imagen_campo ] ) )
                                $aInstValo[ $s_instancia_imagen_campo ] = $a_media_imagen[ 'mimgnomb_' . $s_imagen_dimension ];

                                // 2017.07.14 - dentro el array auxiliar por ratio asigno las diferentes dimensiones
                                $a_media_por_ratio[ $s_imagen_campo ][ $s_imagen_ratio ][ $s_instancia_imagen_campo ] =
                                     $aInstValo[ $s_instancia_imagen_campo ];
                            }
                        }
                    }

                    $aInstValo[ $s_imagen_campo ] = $a_media_por_ratio[ $s_imagen_campo ];
                    //var_dump( $aInstValo[ $s_imagen_campo ] );
                }
            }
            //*/

            $aInst[ $iInstPosi ] = $aInstValo;
        }
        
        //var_dump( $a_media_por_ratio );

        return $aInst;
    }

    public function enable_relations ( $a_parameters = null ) {
        $this->b_with_relations = true;
    }

    public function disable_relations ( $a_parameters = null ) {
        $this->b_with_relations = false;
    }

    public function control_workflow ( $a_parameters = null ) {
        //print_r( "fmwk.clas.control_workflow <br> \n" );
        //var_dump( $a_parameters );
        //exit();

        // variables
            $o_base    = new base(); 
            $i_entidad = 0;
            $s_entidad = $this->aBase['aEnti'][0]; // se compara con wf_proceso:instancia_entidad
           
            $a_control = array(
                "entidad_valida" => false,
            );

            // 2017.03.15 - obtener el id de la entidad
            if ( isset( $this->aBase['aDato']['id'] ) )
                $i_entidad = $this->aBase['aDato']['id'];

            // 2018.07.08 - control parametros
                $b_control_workflow = false;
                $a_control_workflow = array();

                if ( ! is_null( $a_parameters ) )
                {
                    $a_control_workflow = $a_parameters;
                    $b_control_workflow = true;
                }            

        // control sobre el nombre de la entidad
            
            // 2017.03.16 - controlar que la entidad que se crea no pertenezca al modelo de datos de la
            // funcionalidad workflow y que este definida la clase wf_proceso
            $b_procesar = strpos( $s_entidad, "wf_" ) === false ? true : false;
            $b_procesar = $b_procesar && class_exists( "wf_proceso" ) ? true : false;
            $a_control['entidad_valida'] = $b_procesar;

            //var_dump( $a_control );

        // control si la entidad se encuentra asignada a algun proceso.
            
            //print_r( "entidad_valida <br> \n" );
            if ( $a_control['entidad_valida'] )
            {
                // consulta sql que busca los procesos que contengan esta entidad
                    $s_sql = "SELECT *
                        FROM wf_proceso 
                        WHERE wf_proceso.instancia_entidad = '$s_entidad'
                          AND wf_proceso.flag_habilitado = 1";
                    $a_base = $o_base->procSent( $s_sql );
                    
                    $a_procesos = $a_base['aDato'];
                    //print_r( "<br>" . $s_sql . "<br> \n" );
                    //print_r( $a_procesos );

                    foreach ( $a_procesos as $i_proceso => $a_proceso ) 
                    {
                        // debug
                            //print_r( $a_proceso );

                        // variables
                            $b_workflow_crear_instancia = false;

                        // 2018.07.08 - instancia criterio - separar en campo/valor

                            // separar las clausulas del SQL where
                            $s_instancia_criterio = $a_proceso['instancia_criterio'];
                            $s_instancia_criterio = str_replace( " AND ", ",", $s_instancia_criterio );
                            $a_instancia_criterio = explode( ",", $s_instancia_criterio );

                            // controlar cada clausula
                            $a_instancia_criterio_auxi = array();
                            foreach ( $a_instancia_criterio as $i_criterio => $s_criterio ) 
                            {
                                // separar el campo y el valor
                                $s_criterio = str_replace( " = ", ",", $s_criterio );
                                $a_criterio = explode( ",", $s_criterio );

                                // separar el nombre del campo del alias de la tabla
                                $s_criterio_campo = $a_criterio[0];
                                $a_criterio_campo = explode( ".", $s_criterio_campo ); 

                                $a_instancia_criterio_auxi[ $a_criterio_campo[1] ] = $a_criterio[1];
                            }
                            $a_instancia_criterio = $a_instancia_criterio_auxi;

                        // 2017.03.15 - controlar si la instancia se corresponde con el criterio
                            $o_entidad = new $s_entidad();
                            $o_entidad ->aBase['aFilt'][] = "$s_entidad.id = $i_entidad";
                            $o_entidad ->aBase['aFilt'][] = $a_proceso['instancia_criterio'];
                            $a_entidad = $o_entidad->find();
                                
                            if ( ! empty( $a_entidad ) ) $b_workflow_crear_instancia = true;

                        //var_dump( $o_entidad );
                        //var_dump( $a_entidad );
                        //var_dump( $b_workflow_crear_instancia );

                        // 2018.07.08 - para definir que la instancia concuerda con el criterio, los valores de la instancia 
                        // que se persiste debe corresponder a un cambio actual, es decir, si la instancia ya tenia el valor 
                        // del criterio, no se debe crear la instancia del proceso.

                            // si el metodo recibio datos de configuracion a traves de los parametros.
                            if ( $b_control_workflow )
                            {
                                // si el metodo que invoca es el metodo update.
                                if ( $a_control_workflow['method'] == "update" )
                                {
                                    // si la instancia cumple con el criterio
                                    if ( ! empty( $a_entidad ) )
                                    {
                                        // recorro el array del criterio desglozado en campo/valor
                                        foreach ( $a_instancia_criterio as $s_criterio_campo => $s_criterio_valor ) 
                                        {
                                            // controlar si el campo del criterio existe en el registro de la instancia
                                            if ( $a_control_workflow['values_pre'][0][ $s_criterio_campo ] )
                                            {
                                                $s_value_pre = $a_control_workflow['values_pre'][0][ $s_criterio_campo ];
                                                
                                                // si el valor previo de la instancia es igual al valor del criterio
                                                // no se debe crear la instancia.
                                                if ( $s_value_pre == $s_criterio_valor ) 
                                                    $b_workflow_crear_instancia = false;
                                            }
                                        }
                                    }                                
                                }
                            }

                        //if ( ! empty( $a_entidad ) )
                        //var_dump( $b_workflow_crear_instancia );
                        //exit();

                        if ( $b_workflow_crear_instancia )
                        {
                            // 2017.03.15 - si la entidad corresponde con la configuracion del filtro
                            // obtener la definicion del proceso para crear la instancia de dicho proceso
                            $o_proceso = new wf_proceso();
                            $o_proceso->crear_instancia( 
                                array(
                                    "a_proceso" => $a_proceso,
                                    "o_entidad" => $this,
                                ));
                        }
                    }
            }
    }

    public function create ( $a_parameters = null ) {
        //print_r( "framework clas.create <br> \n" );
        
        // control parametros de interes
            if ( isset( $this->a_property['fecha_creacion'] ) )
                $a_parameters['fecha_creacion'] = date( "Y-m-d H:i:s" );
            
            if ( isset( $this->a_property['fecha_modificacion'] ) )
                $a_parameters['fecha_modificacion'] = date( "Y-m-d H:i:s" );
        
            $a_parameters = $this->control_parameters( $a_parameters );
        
        // persistir instancia
            $this->conf( $a_parameters );
            $aResu = base::set( $this->aBase );
        
        // logica post creacion
            $i_instancia = $aResu['iIden'];
            $s_entity    = $this->aBase['aEnti'][0];
            $s_pk        = $this->aBase['aClav'][0];

            // 2018.09.09 - asignar ruta de carpeta de la entidad para la posible carga de archivos
                $s_ruta = "";
                if ( isset( $this->a_property['ruta_upload'] ) )
                {
                    $s_ruta =  "upld/$s_entity/";
                    $s_ruta .= substr( $i_instancia, -1, 1 )."/";
                    $s_ruta .= $i_instancia."/";

                    $s_sql = "UPDATE $s_entity SET ruta_upload = '$s_ruta' WHERE id = $i_instancia;";

                    $o_base = new base();
                    $o_base->procSent( $s_sql );
                }

            // 2017.01.18 - carga de archivos
                
                // Paso los valores de la instancia para que se puda realizar la carga de los
                // archivos si es que se sube.
                
                $a_parameters['id']          = $i_instancia;
                $a_parameters['ruta_upload'] = $s_ruta;
                
                $aResu['aDato'] = $a_parameters;
                
                $a_parameters = $this->control_persistence( $aResu );
        
            // 2017.03.13 - Procesos de negocios - INI
                $a_instancia = $this->read( array( "id" => $i_instancia ) ); 

                $this->control_workflow( array(
                    "method"     => "create",
                    "parameters" => $a_instancia[0],
                    "values_pre" => array( array() ),
                ));
        
        return $a_parameters;
    }

    public function read ( $a_parameters = null ) {
        //print_r( "fmwk:clas.cls:read <br> \n" );
        
        // el metodo debe devolver registros especificos identificados a traves de los valores del 
        // array parametro o debe devolver todas las instancias de la entidad si este es nulo.

        $b_all = is_null( $a_parameters ) ? true : false;

        if ( ! $b_all )
            $this->conf( $a_parameters );

        if ( $b_all )
            $aResu = base::getAll( $this->aBase );
        else
            $aResu = base::get( $this->aBase );
        
        //var_dump( $aResu );
        //exit();

        if ( $aResu['iEsta'] == 1 )                                                                                                 // Si la ejecucion OK
        {
            $this->bInst = true;                                                                                                            // seteo que esta instanciada
            //$aResu['aDato'] = $this->geneArchRuta( $aResu['aDato'] );                                        // guardo los datos de la instancia.
            $this->aInst = $aResu['aDato'];
        }

        if ( $this->b_with_relations )
        {
            $aResu['aDato'] = $this->obteRela( $aResu['aDato'] );
            // 2017.02.02 - se incluyo para poder obtener las instancias de las clases relaciondas
            // desde adentro, cuando se esta trabajando dentro del objeto instanciado.
            $this->aInst = $aResu['aDato'];
        }

        $aResu['aDato'] = $this->output_format( $aResu['aDato'] );
        //var_dump( $aResu['aDato'] );
        //exit();
        return $aResu['aDato'];
    }
    
    public function update ( $a_parameters = null ) {
        //print_r( "framework clas.update <br> \n" );
        //var_dump( $a_parameters );

        // variables 
            $a_instancia = array(); // guarda los valores de la instancia previo a la actualizacion
            
            if ( isset( $this->a_property['fecha_modificacion'] ) )
                $a_parameters['fecha_modificacion'] = date( "Y-m-d H:i:s" );

            // es necesario obtener la instancia actual
            if ( isset( $a_parameters['id'] ) )
                $a_instancia = $this->read( array( "id" => $a_parameters['id'] ) ); 
            
        // control de parametros
            $a_parameters = $this->control_parameters( $a_parameters );
        
        // persistir en la base de datos
            $this->conf( $a_parameters );
            $aResu = base::set( $this->aBase );

        // 2017.02.16 - SOLUCION BUGS - merge de arrays 
            //porque la edición de entidades no funciona. Despues de guardar
            // los cambios de una instancia no vuelve al formulario, se queda en el script 
            // view/entidad/persistir.php
            if ( $aResu['iEsta'] == 1 )                                                                 // ejecucion exitosa de la sentencia
            {
                if ( $aResu['iCant'] == 0 )                                                             // no se afectaron registros.                
                {
                    $aResu['sProc'] = "No se ha afectado a algun registro.";
                    $aResu['bProc'] = true;
                }
                else
                {
                    $aResu['sProc'] = "Se ha completado el proceso exitosamente.";
                    $aResu['bProc'] = true;
                }
            }
            else 
            {
                $aResu['sProc'] = "Error en la base de datos.";
                $aResu['bProc'] = false;
            }
            $a_parameters = array_merge( $a_parameters, $aResu );
        
        // 2017.03.13 - Procesos de negocios - INI
            
            // array de configuracion del metodo control_workflow
            $a_control_workflow = array(
                "method"     => "update",
                "parameters" => $a_parameters,
                "values_pre" => $a_instancia
            );

            // 2018.04.14 - Control de la version de la base de dato. Porque en versiones anteriores
            // a la 22 el modelo de datos es diferente y la logica de las clases del motor de 
            // procesos cambia.

            // 2018.07.08 - se les pasa parametros porque se debe controlar si los valores del criterio corresponden
            // a un cambio actual.
            if ( FMWK_BASE_VERS >= 22 )
                $this->control_workflow( $a_control_workflow );
            //*/
        
        return $a_parameters;
    }

    public function delete ( $a_parameters = null ) {
        //var_dump( "framework clas.delete");
        //var_dump( $a_parameters );
        
        $aValo = $a_parameters;
        $this->conf( $aValo );
        $aResu = base::rem( $this->aBase );
        if ( $aResu['iEsta'] == 1 && $aResu['iCant'] == 1 )                                         // Si la ejecucion OK y hay registros afectados
        {
            $this->bInst = true;                                                                    // seteo que esta instanciada
            $this->aInst = $aResu['aDato'];                                                         // guardo los datos de la instancia.
            // eliminar los archivos de la instancia de la clase
            $sRoot = $_SERVER['DOCUMENT_ROOT'].FMWK_CLIE_ROOT;                                      // se define el directorio root de la aplicacion.
            $sClas = $sRoot."upld/".$this->aBase['aEnti'][0]."/";                           // se define el directorio de archivos para la clase
            $sInst = "";
            foreach( $this->aBase['aClav'] as $iIden => $sCamp )                                    // se construye el nombre del directorio de la instancia de la clase
                $sInst .= ( ( $sInst != "" )? "." : "" ).$aValo[ $sCamp ];
            $sAgru = $sClas.substr( $sInst, -1, 1 )."/";                                            // carpeta que agrupara segun el ultimo caracter de la instancia.
            //$sInst = $sClas.$sInst."/";                                                           // se define el directorio de archivos para la instancia de la clase
            $sInst = $sAgru.$sInst."/";                                                             // se define el directorio de archivos para la instancia de la clase
            if ( file_exists( $sInst ) )                                                            // verificar si existe la carpeta de la instancia de la clase
            {
                foreach ( glob( $sInst."*.*" ) as $sFile )                                                            
                    unlink( $sFile );
                rmdir( $sInst );
            }
        }
    }

    public function find ( $a_parameters = null ) {
        //var_dump( "clas.find" );
        //var_dump( $a_parameters );

        $aValo = $a_parameters;

        if ( isset( $this->aBase['aDato']['pagiCant'] ) )
        {
            if ( $this->aBase['aDato']['pagiCant'] > 0 )
            {
                $this->aBase['aLimi']['iCant'] = $this->aBase['aDato']['pagiCant'];
                $this->aBase['aLimi']['iInic'] = $this->aBase['aDato']['pagiInic'];
            }
        }
        if ( ! is_null( $aValo ) )
            $this->conf( $aValo );

        $aResu = base::busc( $this );                                                               // 20150823 - se pasa una referencia de la instancia de la clase
        $this->aInst = $aResu['aDato'];                                                             // guardo el resultado de la consulta, puede tener una o mas instancias.
        
        // 2016-05-31 - obtener las rutas de archivos, correctamente formadas si corresponde
        if ( $aResu['iEsta'] == 1 )                                                                 // Si la ejecucion OK
        {
            //$aResu['aDato'] = $this->geneArchRuta( $aResu['aDato'] );                               // guardo los datos de la instancia.
            $this->aInst = $aResu['aDato'];
        }

        if ( $this->b_with_relations )
            $aResu['aDato'] = $this->obteRela( $aResu['aDato'] );
        
        $aResu['aDato'] = $this->output_format( $aResu['aDato'] );
        return $aResu['aDato'];        
    }

    public function query_sql ( $a_parameters = null ) {
        //var_dump( "clas.find" );
        //var_dump( $a_parameters );

        $aValo = $a_parameters;

        if ( isset( $this->aBase['aDato']['pagiCant'] ) )
        {
            if ( $this->aBase['aDato']['pagiCant'] > 0 )
            {
                $this->aBase['aLimi']['iCant'] = $this->aBase['aDato']['pagiCant'];
                $this->aBase['aLimi']['iInic'] = $this->aBase['aDato']['pagiInic'];
            }
        }
        if ( ! is_null( $aValo ) )
            $this->conf( $aValo );

        $s_query_sql = base::query_sql( $this );                                                               // 20150823 - se pasa una referencia de la instancia de la clase
        /*
        $this->aInst = $aResu['aDato'];                                                             // guardo el resultado de la consulta, puede tener una o mas instancias.
        
        // 2016-05-31 - obtener las rutas de archivos, correctamente formadas si corresponde
        if ( $aResu['iEsta'] == 1 )                                                                 // Si la ejecucion OK
        {
            $aResu['aDato'] = $this->geneArchRuta( $aResu['aDato'] );                               // guardo los datos de la instancia.
            $this->aInst = $aResu['aDato'];
        }

        if ( $this->b_with_relations )
            $aResu['aDato'] = $this->obteRela( $aResu['aDato'] );
        
        $aResu['aDato'] = $this->output_format( $aResu['aDato'] );
        return $aResu['aDato'];        
        */

        return $s_query_sql;
    }

    public function get_input_data ( $a_parameters = null ){
        //var_dump( "clas.cls -> get_input_data" );
        //var_dump( $a_parameters );
        
        // 2017.02.19 - se actualizan los datos que vienen en el parametro porque cuando se utilizan
        // filtros y se quiere poner un valor dinamico, por ejemplo, el valor de algun campo de la 
        // instancia que se esta procesando, es necesario tener los valores de la instancia.
        $o_instancia  = $a_parameters['o_instancia'];
        $a_instancia  = $o_instancia->aInst;
        $a_parameters = $a_parameters['a_relation'];

        $s_orden  = "";
        $a_input  = array();
        
        // 2016.11.29 - Implementacion de codigo que evalue los campos de relacion definidos en la
        // clase para la construccion de los valores del combo.
        $s_entity   =  $a_parameters['s_entity'];
        $s_pk       = $a_parameters['s_property'];
        
        foreach ( $a_parameters['a_replace'] as $i_field_replace => $s_field_replace ) 
            $s_orden .= ( $s_orden != "" ? ", " : "" ) . $s_entity . "." . $s_field_replace . " ASC";

        $this->aBase['aOrde'][] = "$s_orden";
        
        // obtener informacion
        if ( isset( $a_parameters['a_filter'] ) )
        {
            foreach ( $a_parameters['a_filter'] as $i_filter => $s_filter ) 
            {
                // 2017.02.19 - reemplazar tag de campo por valor dentro de la instancia
                foreach ( $a_instancia[0] as $s_campo => $s_valor ) 
                {
                    if ( ! is_array( $s_valor ) )
                        $s_filter = str_replace( "[$s_campo]", "'$s_valor'", $s_filter );
                }

                $this->aBase['aFilt'][] = $s_filter;
            }
            $a_data = $this->find();
        }   
        else
        {
            $a_data = $this->read();
        }
        
        foreach ( $a_data as $i_instancia => $a_instancia )
        {

            $s_label = "";

            // 2016.11.29 - construccion del label
            foreach ( $a_parameters['a_replace'] as $i_field_replace => $s_field_replace ) 
            {
                if ( $a_instancia[ $s_field_replace ] != "" && ! is_null( $a_instancia[ $s_field_replace ] ) )
                    $s_label .= ( $s_label != "" ? ", " : "" ) . $a_instancia[ $s_field_replace ];
            }

            if ( $s_label == "" )
                $s_label = $s_entity . " - ID: " . $a_instancia[ $s_pk ];

            $a_input[] = array(
                "s_value"    => $a_instancia[ $s_pk ],
                "s_label"    => $s_label,
                "s_selected" => "",
            );
        }

        $a_parameters = array();
        $a_parameters['data']['input'] = $a_input;
        return $a_parameters;
    }

    public function get_list_view_values ( $a_parameters = null ) {
        
        $a_item        = array(
            "s_id"   => "",
            "a_line" => array(),
        );
        $a_lista       = array();
        $a_campos      = $this->a_view['a_list']['a_line'];
        $i_item_lineas = count( $a_campos );
        
        foreach ( $this->aInst as $i_instancia => $a_instancia ) 
        {
          $a_lista_item = array();
          foreach ( $a_campos as $i_campo => $x_campo_valor ) 
          {
            $s_linea = "";
            if ( is_array( $x_campo_valor ) )
            {
              foreach ( $x_campo_valor as $i_posicion => $s_campo ) 
              {
                $s_linea .= $s_linea != "" ? " " : "";
                $s_linea .= $a_instancia[ $s_campo ];
              }
            }
            else
            {
              $s_linea .= $a_instancia[ $x_campo_valor ];
            }

            $a_lista_item[ $i_campo ] = $s_linea;
          }

          $a_item['s_id']   = $a_instancia['id'];
          $a_item['a_line'] = $a_lista_item;
          
          $a_lista[] = $a_item;
        }

        return $a_lista;
    }

    // 2017.03.05 - actualizacion sobre como se pasan los parametros. Dentro de la clase en el 
    // metodo output_format, se definen los tags y la ruta del template dentro del array a_card_view
    // de la instancia.
    public function get_card_view_content ( $a_parameters = null ) {
        $s_template_auxi = "";
        $s_template_file = STRUCTURE_DEFAULT_PATH . "view/bloques/card-general.html.php";           // card template por defecto
        
        // primera carga del template
        $o_template_file = fopen( $s_template_file, "r" );
        $s_card_template = stream_get_contents( $o_template_file );
        $s_template_auxi = $s_card_template;

        foreach ( $a_parameters as $i_instance => $a_instance ) 
        {
            $b_card_template    = false;                                                            // determina si la configuracion tiene template
            $a_card_view_config = $a_instance['a_card_view'];                                       // obtener la configuracion de cada instancia

            if ( ! isset( $a_card_view_config['card-template-url'] ) )
                $a_card_view_config['card-template-url'] = "";
            
            if ( $a_card_view_config['card-template-url'] != "" )
                $b_card_template = true;

            if ( $a_card_view_config['card-template-url'] != $s_template_file && $b_card_template )
            {
                // cargar el template de la instancia si no es igual al anterior
                $s_template_file = $a_card_view_config['card-template-url'];
                $o_template_file = fopen( $s_template_file, "r" );
                $s_card_template = stream_get_contents( $o_template_file );
                $s_template_auxi = $s_card_template;
            }

            // procesar el template
            $s_card_template = $s_template_auxi;
            foreach ( $a_card_view_config as $s_tag_nombre => $s_tag_valor ) 
                $s_card_template = str_replace( "[$s_tag_nombre]", $s_tag_valor, $s_card_template );

            // guardar la informacion
            $a_parameters[ $i_instance ] = $s_card_template;
        }

        return $a_parameters;
    }

    // 2017.04.12 - actualizacion sobre como se pasan los parametros.
    public function get_image_view_content ( $a_parameters = null ) {
        //var_dump( "bio_especie.get_image_view_content" );
        //var_dump( $a_parameters );

        $s_template_auxi = "";
        $s_template_file = STRUCTURE_DEFAULT_PATH . "view/bloques/view-image-item-1.html.php";           // card template por defecto
        
        // primera carga del template
        $o_template_file = fopen( $s_template_file, "r" );
        $s_card_template = stream_get_contents( $o_template_file );
        $s_template_auxi = $s_card_template;

        foreach ( $a_parameters as $i_instance => $a_instance ) 
        {
            $b_card_template    = false;                                                            // determina si la configuracion tiene template
            $a_card_view_config = $a_instance['a_image_view'];                                       // obtener la configuracion de cada instancia

            if ( ! isset( $a_card_view_config['image-template-url'] ) )
                $a_card_view_config['image-template-url'] = "";
            
            if ( $a_card_view_config['image-template-url'] != "" )
                $b_card_template = true;

             if ( $a_card_view_config['image-template-url'] != $s_template_file && $b_card_template )
            {
                // cargar el template de la instancia si no es igual al anterior
                $s_template_file = $a_card_view_config['image-template-url'];
                $o_template_file = fopen( $s_template_file, "r" );
                $s_card_template = stream_get_contents( $o_template_file );
                $s_template_auxi = $s_card_template;
            }

            // procesar el template
            $s_card_template = $s_template_auxi;
            foreach ( $a_card_view_config as $s_tag_nombre => $s_tag_valor ) 
                $s_card_template = str_replace( "[$s_tag_nombre]", $s_tag_valor, $s_card_template );

            // guardar la informacion
            $a_parameters[ $i_instance ] = $s_card_template;
        }

        return $a_parameters;
    }

    public function get_list_view_content ( $a_parameters = null ) {
        //var_dump( "bio_especie.get_image_view_content" );
        //var_dump( $a_parameters );

        // obtener template
        //$s_template_file = STRUCTURE_DEFAULT_PATH . "view/bloques/view-list-item-3.html.php";
        $s_template_file = STRUCTURE_DEFAULT_PATH . "view/bloques/view-list-item-4.html.php";
        $o_template_file = fopen( $s_template_file, "r" );
        $s_card_template = stream_get_contents( $o_template_file );
        $s_auxi_template = $s_card_template;
        $s_template_file = "";

        foreach ( $a_parameters as $i_instance => $a_instance ) 
        {
            
            $i_description     = 74; //56;
            $s_title_field     = isset( $a_instance['nombre_cientifico'] ) ? "nombre_cientifico" : "nombre";
            $s_description     = ! isset( $a_instance['descripcion'] ) ? "" : trim( $a_instance['descripcion'] );
            $b_description     = $s_description == "" ? false : true;
            $b_list_config     = isset( $a_instance['a_list_view'] ) ? true :  false; 
            $s_link_label      = isset( $a_instance['link_label'] ) ? $a_instance['link_label'] : "Leer más";
            
            if ( $b_list_config )
            {
                $s_entity_image = "";
                $s_entity_title = "";

                if ( isset( $a_instance['a_list_view']['s_entity_image'] ) )
                    $s_entity_image = $a_instance['a_list_view']['s_entity_image'];
                
                if ( isset( $a_instance['a_list_view']['s_entity_title'] ) )
                    $s_entity_title = $a_instance['a_list_view']['s_entity_title'];

                if ( $s_entity_image != "" && $s_entity_title != "" )
                {
                    $s_template_file = STRUCTURE_DEFAULT_PATH . "view/bloques/view-list-item-3.html.php";
                    $i_description   = 56;
                }
            }
            
            if ( $b_description && strlen( $s_description ) > $i_description )
                $s_description = substr( $s_description, 0, $i_description ) . "...";

            if ( $s_template_file != "" && $i_instance == 0 )
            {
                $o_template_file = fopen( $s_template_file, "r" );
                $s_card_template = stream_get_contents( $o_template_file );
                $s_auxi_template = $s_card_template;                   
            }

            $s_card_template  = $s_auxi_template;

            $s_card_template = str_replace( "[instance_image]",       $a_instance['imagen_portada_card']                    , $s_card_template );
            $s_card_template = str_replace( "[entity_image]",         $s_entity_image                                       , $s_card_template );
            $s_card_template = str_replace( "[entity_title]",         $s_entity_title                                       , $s_card_template );
            $s_card_template = str_replace( "[instance_name]",        $a_instance[ $s_title_field ]                         , $s_card_template );
            $s_card_template = str_replace( "[instance_description]", $s_description                                        , $s_card_template );
            $s_card_template = str_replace( "[instance-link-label]",  $s_link_label                                         , $s_card_template );
            
            if ( isset( $a_instance['link'] ) )
                $s_card_template = str_replace( "[instance-link-url]",  $a_instance['link'], $s_card_template );
            else
                $s_card_template = str_replace( "[instance-link-url]",    FMWK_CLIE_SERV . "specie/" . $a_instance['id']        , $s_card_template );
            
            $a_parameters[ $i_instance ] = trim($s_card_template)."\n";
        }

        return $a_parameters;
    }

    public function geneArchRuta ( $aPara = null ) {
        //imagenes - esto se tiene que ir al framework
        //var_dump( $aPara );
        
        $aInst = $aPara;                                                                                                                                                                // array de las instancias de la clase.
        $oFmwkconf = new fmwkconf();
        $aValoDefe = $oFmwkconf->valodefe();                                                                                                                        // obtener de la configuracion los valores por defecto
        $aImagConf = $oFmwkconf->obteImag();                                                                                                                        // obtener de la configuracion las dimensiones de las imagenes que corresponden a campos
        
        $sImagRuta = "project/img/";
        $sEntiNomb = $this->aBase['aEnti'][0];

        // 2016-03-19
        $s_fmwk_clie_dire = FMWK_CLIE_DIRE;
        $s_fmwk_clie_serv = FMWK_CLIE_SERV;

        if ( FMWK_CLIE_MODU )
        {
            $s_fmwk_clie_dire = FMWK_CLIE_DIRE . "../../";
            $s_fmwk_clie_serv = FMWK_CLIE_SERV . "../../";
        }

        foreach( $aInst as $iInstPosi => $aInstValo )                                                                                                     // se recorre cada instancia
        {
            //var_dump( $aInstValo );
            if ( isset( $aImagConf[ $sEntiNomb ] ) )
            {
                //var_dump( $aImagConf[ $sEntiNomb ] );
                foreach( $aImagConf[ $sEntiNomb ] as $sImagCamp => $aImagInst )
                {
                    $sInstImagNomb = $aInstValo[ $sImagCamp ];
                    $sInstImagRuta = $aInstValo[ 'ruta_upload' ];
                    
                    if ( is_null( $sInstImagNomb ) || $sInstImagNomb == "" )
                    {
                        $sInstImagRuta = $sImagRuta;
                        $sInstImagNomb = $aValoDefe['aImag'][ $sEntiNomb ][ $sImagCamp ];
                    }
                    
                    foreach( $aImagInst as $sImagInstNomb => $aImagInstSize )
                    {
                        $sAuxiNomb = $sImagInstNomb."_".$sInstImagNomb;
                        $sAuxiNomb = $sInstImagRuta.$sAuxiNomb;
                        $aInstValo[ $sImagCamp."_".$sImagInstNomb ] = $s_fmwk_clie_serv . $sAuxiNomb;
                        //var_dump( $sImagCamp."_".$sImagInstNomb ); 
                        //var_dump( $aInstValo[ $sImagCamp."_".$sImagInstNomb ] ); 
                    }

                    $aInstValo[ $sImagCamp."_origfile" ] = $s_fmwk_clie_serv . $sInstImagRuta . $sInstImagNomb;

                }
            }
            
            // 20150701 - controlar ------------------------------------------------------------------ INI
            $aInstValo = $this->controlarArchivosExistencia( $aInstValo );
            //var_dump( $aInstValo );

            $aInst[ $iInstPosi ] = $aInstValo;     

        }
        return $aInst;
    }
    
    public function controlarArchivosExistencia ( $aValo = null ) {

        $sEnti = $this->aBase['aEnti'][0];                                                                                                                            // obtener el nombre de la entidad
        $aCamp = array();                                                                                                                                                             // guardara los nombres de los campos que se encuentran relacionados con archivos
        $aConf = fmwkconf::obteImag();                                                                                                                                    // obtengo la configuracion de archivos en la aplicacion
        $aInstValo = $aValo;                                                                                                                                                        // instancia de la entidad

        // separar los nombres de los campos de la entidad asociados con archivos.
        if ( isset( $aConf[ $sEnti ] ))
        {
            foreach ( $aConf[ $sEnti ] as $sCamp => $aData )
                $aCamp[] = $sCamp;
        }

        if ( ! empty( $aCamp ) )                                                                                                                                                // solo si hay al menos un campo de la entidad que se encuentre relacionada con archivos
        {
            foreach ( $aInstValo as $sCampNomb => $sCampValo )                                                                                        // recorro todos los campos de la instancia.
            {
                foreach ( $aCamp as $iCampPosi => $sCampPref )                                                                                            // por cada campo de la instancia se controla si corresponde a un campo relacionado con archivo
                {
                    $sCampPref .= "_";
                    if ( strpos( $sCampNomb, $sCampPref ) !== false )
                    {
                        $sRuta = str_replace( FMWK_CLIE_SERV, "", $sCampValo );
                        
                        // 2016-03-19
                        $s_fmwk_clie_dire = FMWK_CLIE_DIRE;
                        $s_fmwk_clie_serv = FMWK_CLIE_SERV;
                        $sRuta = $s_fmwk_clie_dire . $sRuta;
                        
                        if ( ! file_exists( $sRuta ) )
                        {
                            // 2018.09.18
                            $s_file_path = $sRuta;

                            // 2016-03-19
                            if ( FMWK_CLIE_MODU )
                                $sRuta = $s_fmwk_clie_dire . "../../project/img/" . $sCampNomb . "_default.jpg";
                            else
                                $sRuta = $s_fmwk_clie_dire . "project/img/" . $sCampNomb . "_default.jpg";
                            
                            if ( ! file_exists( $sRuta ) )
                            {
                                //$sRuta = STRUCTURE_DEFAULT_ROOT ."img/".$sCampNomb."_default.jpg";                                    // 20160222 - si no tienen los proyecto debe responder el default
                                $sRuta = STRUCTURE_DEFAULT_HTTP ."img/".$sCampNomb."_default.jpg";                                    // 20160222 - si no tienen los proyecto debe responder el default
                            }
                            else
                            {
                                // 2016-03-19
                                if ( FMWK_CLIE_MODU )
                                    $sRuta = $s_fmwk_clie_serv ."../../project/img/".$sCampNomb."_default.jpg";
                                else
                                    $sRuta = $s_fmwk_clie_serv ."project/img/".$sCampNomb."_default.jpg";                         // cuando se controla tiene que ser fisica, cuando se pasa a la app tiene que tener http
                            }

                            // 2018.09.18 - control configuracion constante de ruta de la carpeta de archivos cargados
                            // si se encuentra dentro del if es porque el valor de s_file_path no existe.

                            if ( defined( "FMWK_CLIE_UPLD_HTTP" ) ) 
                            {
                                if ( FMWK_CLIE_UPLD_HTTP != "" )
                                {
                                    $s_file_path = str_replace( $s_fmwk_clie_dire."upld/", FMWK_CLIE_UPLD_HTTP, $s_file_path );

                                    $sRuta = $s_file_path;
                                }
                            }

                            $aInstValo[ $sCampNomb ] = $sRuta;
                        }
                    }
                }
            }
        }
        return $aInstValo;
    }

    public function obtenerRelacionesBD ( $aValo = null ) {
        
        $sEnti = $this->aBase['aEnti'][0];                                                                                                                            // nombre de la entidad
        $aAuxi = array(
            "aCamp" => array(),
        );                                                                                                                                                             // array auxiliar
        $aProp = $this->aProp;                                                                                                                                                    // array con las propiedades de la entidad
        $aEtiq = $this->aEtiq;                                                                                                                                                    // array con las etiquetas de las propiedades de la entidad

        // controlar y agregar todos los campos agregados por relaciones de FK
        foreach ( $this->aBase['aAgre'] as $sEntiCamp => $aCampAgre )
        {
            foreach ( $aCampAgre as $sCampAgreClav => $sCampAgreValo )
            {
                $aAuxi['aCamp'][ $sEntiCamp ] = array(
                    "sEnti" => $sCampAgreClav,
                    "sCamp" => $sCampAgreValo,
                    "sSQL_" => $sCampAgreClav.".".$sCampAgreValo,
                    "sJoin" => "INNER JOIN `$sCampAgreClav` `$sCampAgreClav` ON $sCampAgreClav.$sCampAgreClav"."iden = $sEnti.$sEntiCamp",
                    "sEtiq" => "",
                ); 

                // obtener la etiqueta de los campos agregados de la configuracion de la grilla
                foreach ( $this->aGrid as $iColuPosi => $aColuData ) 
                {
                    if ( $aColuData[1] == $sCampAgreValo )
                    {
                        $aAuxi['aCamp'][ $sEntiCamp ]['sEtiq'] = $aColuData[0];
                        Break 1;
                    }
                }
            } 
        }

        // controlar y agregar los campos restantes propios de la entidad
        foreach ( $aProp as $iProp => $sProp ) 
            if ( ! array_key_exists( $sProp, $aAuxi['aCamp'] ) && 
                $sProp != $sEnti."iden" )
                $aAuxi['aCamp'][ $sProp ] = array(
                    "sEnti" => $sEnti,
                    "sCamp" => $sProp,
                    "sSQL_" => $sEnti.".".$sProp,
                    "sEtiq" => $aEtiq[ $iProp ],
                );

        // controlar y agregar las etiquetas de los campos agregados


        //var_dump( $aAuxi );

        return $aAuxi;
    }

    public function control_pos_persistir ( $a_parameters = null ) {
        //var_dump( "clas.control_pos_persistir" );
        return $a_parameters;
    }

    // evaluacion pendiente ---------------------------------------------------------------------INI
    
    public function exis ( $aValo, $bCrea ) {
        
        $sEnti = $this->aBase['aEnti'][0];
        $aRtrn = array(
            "bProc" => false,                                                                                                                                                         // para respetar el formato del set
            "bExis" => true,                                                                                                                                                            // define si la instancia existe
            "bCrea" => false,                                                                                                                                                         // define si la instancia fue creada.
        );

        // buscar segun los valores pasados
        foreach ( $aValo as $sCamp => $sValo )
        {
            if ( ! is_array( $sValo ) && $sValo != "" && ! is_null( $sValo ) )
                $this->aBase['aFilt'][] = "$sEnti.$sCamp = '$sValo' "; 
        }
        $this->find(); 
        //var_dump( $this->aInst );

        if ( count( $this->aInst ) == 0 && $bCrea )
        {
            $aResu = $this->set( $aValo );
            $aValo[ $this->aBase['aClav'][0] ] = $aResu[ 'iIden' ];
            $this->read( $aValo );
            $aRtrn = $aResu;                                                                                                                                                            // devuelve el iIden si es nuevo
            $aRtrn['bExis'] = false;
            $aRtrn['bCrea'] = true;
        }
        else if ( count( $this->aInst ) == 0 && ! $bCrea )
        {
            $aRtrn['bProc'] = false;
            $aRtrn['bExis'] = false;
            $aRtrn['bCrea'] = false;
        }
        else if ( count( $this->aInst ) == 1 )                                                                                                                    // 2016-03-17
        {
            $aRtrn['iIden'] = $this->aInst[0][ $this->aBase['aClav'][0] ];
        }
        return $aRtrn;
    }

    public function cant () {
        $aBase = $this->aBase;                                                                                                            // copio el array original
        $aBase['bCant'] = true;                                                                                                         // flag que le avisa a base::busc que se esta procesando una relacion
        $aBase['aLimi']['iCant'] = null;
        $aResu = base::busc( $aBase );
        return $aResu['aDato'][0]['CANT'];
    }
    
    public function conf ( $aValo ) {
        $aDato = array();
        $this->aBase['aColu'] = $this->aProp;                                                       // asigno el array de propiedades de la clase al array de columnas para la consulta
        foreach( $aValo as $sIden => $xValo )                                                       // recorro el array de valores pasados como parametros
        {
            if ( in_array( $sIden, $this->aProp ) )                                                 // solos valores que se correspondan con las propiedades de la clase
                $aDato[ $sIden ] = $xValo;                                                          // seran guardados como datos.
        }
        $this->aBase['aDato'] = $aDato;
    }

    public function grid () {
        $iSalt = 0;                                                                                                                                 // cuando se muestren columnas de otras entidades el salto corregira la posicion
        $aAuxi = array();                                                                                                                     // array auxiliar donde se guardara la configuracion de las columnas de la grilla
        for( $iCeld = 0; $iCeld < count( $this->aGrid ); $iCeld++ )                                 // recorro el array de la grilla.
        {
            $aCeld = $this->aGrid[$iCeld];                                                                                        // array con los datos que corresponden a un campo
            $iPosi = $iCeld - $iSalt;                                                                                                 // indice para buscar en los array del objeto aProp y aEtiq
            if ( $aCeld[0] == "" )                                                                                                        // si no se a configurado una etiqueta para la grilla
                $aCeld[0] = $this->aEtiq[$iPosi];                                                                             // utilizo la que va en el formulario.
            $aCeld[1] = $this->aProp[$iPosi];                                                                                 // obtengo el nombre del campo
            $aAuxi[$iCeld] = $aCeld;                                                                                                    // guardo el array actualizado de los datos del campo
        }
        return $aAuxi;
    }
    
    public function resu() {
        //var_dump( "clas.resu()" );
        //$this->aInst = $this->geneArchRuta( $this->aInst );
        return $this->aInst;
    }

    public function filt () {    
        return $this->aFilt;
    }
    
    // evaluacion pendiente ---------------------------------------------------------------------FIN
    
    public function procSali ( $aValo = null ) {
        var_dump( "framework clas.procSali - deprecado" );
        $aInst = $aValo;
        return $aInst;
    }

    public function obteRela ( $aValo = null ) {
        //var_dump( "framework clas.obteRela - deprecado" );
        $aInst    = $aValo;
        return $aInst;
    }

    public function resuConRela ( $aValo = null ) {
        var_dump( "framework clas.resuConRela - deprecado" );
        $this->aInst = $this->obteRela( $this->aInst );
        $this->aInst = $this->procSali( $this->aInst );
        return $this->resu();
    }

    public function set ( $aValo ) {
        var_dump( "framework clas.set - deprecado" );
    }

    public function rem ( $aValo ) {
        var_dump( "framework clas.rem - deprecado" );
    }
   
    public function get ( $aValo ) {
        var_dump( $_SERVER['SCRIPT_FILENAME'] );
        var_dump( "framework clas.get - deprecado" );
    }
    
    public function getAll ( $aValo = null ) {
        var_dump( "framework clas.getAll - deprecado" );
    }
    
    public function busc ( $aValo = null ) {
        var_dump( "framework clas.busc - deprecado" );
    }
    
    public function dato( $sCamp = null, $xValo = null ) {
        var_dump( "framework clas.dato - deprecado" ); 
    }
    
    public function borr( $aCamp, $sEnti = null ) {
        var_dump( "framework clas.borr - deprecado" ); 
    }

    public function contMail ( $aValo = null ) {
        var_dump( "framework clas.contMail - deprecado" ); 
    }
    
    function resuComoFilt( $aValo = null ) {
        var_dump( "framework clas.resuComoFilt - deprecado" ); 
        return $this->get_input_data();
    }

    public function rela ( $sEnti, $aValo = null ) {
        var_dump( "framework clas.rela - deprecado" ); 
    }

    public function get_query_info( $a_parameters ) {
        //var_dump( "clas.get_query_info()" );
        //var_dump( $a_parameters );

        $a_query_info = base::get_query_info( $a_parameters );

        return $a_query_info;
    }
}