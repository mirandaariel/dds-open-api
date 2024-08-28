<?php

/* -----------------------------------------------------------------------------
Metodos:
set
get
busc
procSent
strgTabl
strgClav 
strgUnic
// ---------------------------------------------------------------------------*/

class base {

  // analisis de permiso de acceso a los registros -------------------------INI
  private function procPerm ( $aPara ) {
    //var_dump( $aPara );
    $aPara['bSess'] = ( isset( $aPara['bSess'] ) )? $aPara['bSess'] : false;    // importante desde la logica informa si se usa la session como filtro del query
    $sPerm = "";
    $sEnti = $aPara['aEnti'][0];
    $sClav = $aPara['aClav'][0];                                                // solo para la entidad que registra los usuarios
    if ( ! isset( $_SESSION ) )
      session_start();
    if ( isset( $_SESSION['srolsaco'] ) && isset( $_SESSION['sroliden'] ) &&
      isset( $_SESSION['usuaiden'] ) && 
      $aPara['bSess'] === true )
    {
      if ( isset( $_SESSION['srolsaco'][0]['sacoprop'] ) )
      {
        if ( $_SESSION['srolsaco'][0]['sacoprop'] )                             // se controla si el rol solo permite ver los registros propios. sino es administrador
        {
          $oSent = new sent();
          $oSent->get( array( "sentnomb" => $sEnti ) );                         // se obtiene la configuración de permisos de la entidad.
          if ( ! $oSent->dato( "sentotro" ) )                                   // si la entidad tiene en true sentotro, cualquiera puede ver su contenido.
          {
            $sPerm .= "$sEnti.usuaidec = '".$_SESSION['usuaiden']."'";          // si no es asi, se filtra por el id de usuario que creo el registro.
            if ( $sEnti == "usua" || $sEnti == "usuarios" )                     // para bananacash
              $sPerm .= " OR $sEnti.$sClav = '".$_SESSION['usuaiden']."'";    // cuando se consulta la enti usuarios, el usuario logueado debe poder ver su registro
            // verificar los permisos de la tabla -----------------------------
            if ( $oSent->dato( "sentgrup" ) )                                   // si la entidad tiene en true sentgrup, los usuarios con mismo rol pueden ver los regs.
            {
              $sPerm .= ((trim($sPerm) != "")? " OR " : "").
              "$sEnti.srolidec = '".$_SESSION['sroliden']."'";                  // se filtra por id del rol
            }
          }
        }
      }
    }
    if ( $sPerm != "" && strpos( $sPerm, "OR" ) === false )
      $sPerm = "$sPerm";
    else if ( $sPerm != "" )
      $sPerm = "( $sPerm )";
    return $sPerm;
  }
  // analisis de permiso de acceso a los registros ------------------------FIN*/

  // ------------------------------------------------------------------------INI
  public function procSent ( $sSent, $sChst = "" ) {
    //var_dump( "base.cls" );
    
    $aResu = array(
      "iEsta" => 1,                                                             // codigo de ejecucion exitosa [1:ok|0:error]
      "sEsta" => "",                                                            // string de ejecucion mensaje, cuando hay error.
      "iCant" => 0,                                                             // cantidad de registros afectados
      "aDato" => array(),                                                       // contendra todo los datos del resultado de la consulta
    );
    
    $mysqli = new mysqli( FMWK_BASE_SERV, FMWK_BASE_USUA, FMWK_BASE_PASW, FMWK_BASE_NOMB );
    
    // verificar la conexión
    if ( mysqli_connect_errno() ) {
        printf("Falló la conexión: %s\n", mysqli_connect_error());
        exit();
    }

    /*/ cambiar el conjunto de caracteres a utf8
    if ( ! $mysqli->set_charset( "$sChst" ) ) {
        printf("Error cargando el conjunto de caracteres utf8: %s\n", $mysqli->error);
        exit();
    } else {
        printf("Conjunto de caracteres actual: %s\n", $mysqli->character_set_name());
    }
    //*/

    if ( !$mysqli->set_charset("utf8") )
      printf( "Error cargando el conjunto de caracteres utf8: %s\n", $mysqli->error );

    //echo $sSent."\n";
    //echo $sSent."<br /><br />";
    
    $xResu = $mysqli->query( $sSent );
    
    if ( is_object( $xResu ) )
    {
        $xResu->data_seek(0);
        while ( $fila = $xResu->fetch_assoc() ) {
            $aResu['aDato'][] = $fila;
        }
    }
    
    //var_dump( $xResu );
    if ( is_object( $xResu ) || $xResu ) {
        $aResu['iCant'] = $mysqli->affected_rows;  
        $aResu['iIden'] = $mysqli->insert_id;
    }

    if ( ! $xResu )
      $aResu['iEsta'] = 0;
    
    $mysqli->close();
    return $aResu;
  }
  // ------------------------------------------------------------------------FIN
  
  // ------------------------------------------------------------------------INI
  private function strgTabl ( $aValo ) {
    $sSenT = "";                                                                // string con los nombres de las tablas.
    $sSenJ = " ";                                                               // string con la sintaxis del JOIN
    foreach( $aValo['aEnti'] as $iIden => $sEnti )
    {
      if ( isset( $aValo['aJoin'][ $sEnti ] ) )                                 // si hay elementos dentro del array del JOIN
      {
        $sSenJ .= "LEFT OUTER JOIN `$sEnti` `$sEnti` ON ".$aValo['aJoin'][ $sEnti]." ";
      }
      else
      {
        if ( count( $aValo['aEnti'] ) == 1 )
          $sSenT .= ( ( $sSenT != "" )? ", " : "" )."`$sEnti`";                   // formo string con la/s tabla/s de las sentencias
        else
        {
          $sSenT .= ( ( $sSenT != "" )? ", " : "" )."`$sEnti` `$sEnti`";              // formo string con la/s tabla/s de las sentencias
        }
      }
    }

    return $sSenT.$sSenJ;
  }
  // ------------------------------------------------------------------------FIN
  
  // ------------------------------------------------------------------------INI
  private function strgClav ( $aValo ) {
    $sSenK = "";

    foreach( $aValo['aClav'] as $iIden => $sClav )                              // recorro el array de claves 
    {
      if ( ! isset( $aValo['aDato'][ $sClav ] ) || 
        $aValo['aDato'][ $sClav ] == "" )                                       // si existe pero tiene como valor NULL devolvera false;
        return "";                                                              // si un campo clave no es procesable entonces no se devuelve nada.
      else
        $xValo = "$sClav = '".$aValo['aDato'][ $sClav ]."'";
      $sSenK .= ( ( $sSenK != "" )? " AND " : "" ).$xValo;                      // formo string con clave valor para el where de la sentencia
    }
    return $sSenK;
  }
  // ------------------------------------------------------------------------FIN
  
  // ------------------------------------------------------------------------INI
  private function strgUnic ( $aValo ) {
  
    // 2016-05-30 - La nueva estructura no tenia seteado el campo unico y no se actualizaba el 
    // array viejo. En consecuencia se creo la clave b_unique.    
    $s_entity = $aValo['aEnti'][0];
    $o_entity = new $s_entity(); 
    foreach ( $o_entity->a_property as $s_property => $a_property_configuration )
    {
      if ( isset( $a_property_configuration['b_unique'] ) )
      {
        if ( $a_property_configuration['b_unique']  ) 
          $aValo['aUnic'][] = $s_property;
      }
    }

    $sSenK = "";
    foreach( $aValo['aUnic'] as $iIden => $sClav )                              // recorro el array de claves 
    {
      $xValo = "";
      if ( isset( $aValo['aDato'][ $sClav ] ) )                                 // si existe pero tiene como valor NULL devolvera false;
        $xValo = "$sClav = '".$aValo['aDato'][ $sClav ]."'";
      $sSenK .= ( ( $sSenK != "" )? " AND " : "" ).$xValo;                      // formo string con clave valor para el where de la sentencia
    }
    return $sSenK;
  }
  // ------------------------------------------------------------------------FIN
  
  public function get_string_components ( $a_parameters = null ) {

    $s_entity     = $a_parameters['aEnti'][0];
    $a_tables     = array();
    $a_fields     = array();
    $a_field_name = array();                                                                        // control. repeticion de nombre de campos por join con diferentes tablas
    $a_table_name = array();                                                                        // control. repeticion de nombre de tablas por FK a la misma tabla
    $o_entity     = new $s_entity(); 

    // 2017.02.19 - control - no se comparan los nombres con la entidad principal - INI
    // Si se define una relaacion hacia la misma entidad, se rompe por no tener alias la entidad del
    // join
    $a_table_name[] = $s_entity;
    // 2017.02.19 - control - no se comparan los nombres con la entidad principal - FIN

    foreach ( $o_entity->a_property as $s_property => $a_property_configuration ) 
    {
      // generacion de configuracion de campos
      $a_fields[] = array( "s_entity" => $s_entity, "s_property" => $s_property );

      // obtener datos de relacion que tiene el campo
      $s_relacion_entidad = $a_property_configuration[ 'a_relation' ][ 's_entity'];
      $s_relacion_pk      = $a_property_configuration[ 'a_relation' ][ 's_property'];
      
      // en el caso de existir relacion
      if ( $s_relacion_entidad != "" )
      {
        // el array de tablas del JOIN tiene como indice el nombre del campo que genera la relacion
        // para una ubicacion rapida
        $a_tables[ $s_property ] = array(
          "s_relacion_entidad" => $s_relacion_entidad,
          "s_relacion_pk"      => $s_relacion_pk,
          "s_property"         => $s_property,
        );

        // control para determinar si una tabla del JOIN repite el nombre
        if ( in_array( $s_relacion_entidad, $a_table_name ) )
        {
          $a_name_times = count( array_keys( $a_table_name, $s_relacion_entidad ) );                // veces que se repite el nombre de la tabla
          $a_tables[ $s_property ][ 's_alias' ] = $s_relacion_entidad . "_$a_name_times";           // propiedad alias con el nuevo nombre
        }
        $a_table_name[] = $s_relacion_entidad;

        // si la tabla tiene un alias. el campo debe apuntar al alias y no al nombre de la tabla.
        if ( isset( $a_tables[ $s_property ][ 's_alias' ] ) )
          $s_relacion_entidad = $a_tables[ $s_property ][ 's_alias' ];

        // obtener los campos que reemplazaran a la clave foranea
        $a_replace = $a_property_configuration[ 'a_relation' ][ 'a_replace' ];
        foreach ( $a_replace as $i_replace => $s_replace ) 
        {
          // generacion de configuracion de campos para los campos de otras entidades
          $a_auxi = array( "s_entity" => $s_relacion_entidad, "s_property" => $s_replace );
          
          // control de repeticion de los nombres de los campos
          if ( in_array( $s_replace, $a_field_name ) )
          {
            $a_name_times = count( array_keys( $a_field_name, $s_replace ) );                       // veces que se repite el nombre del campo
            $a_auxi[ 's_alias' ] = $s_replace . "_$a_name_times";                                   // propiedad alias con el nuevo nombre
          }
          $a_field_name[] = $s_replace;                                                             // guardar nombre original para construir nuevos alias

          // el nuevo campo se agrega al array general de campos.
          $a_fields[] = $a_auxi;
        }
      }
      
      $a_field_name[] = $s_property;                                                                // guardar nombre original para construir nuevos alias
    }

    $a_components = array(
      "a_tables" => $a_tables,
      "a_fields" => $a_fields,
    );
    
    return $a_components;
  }
  
  public function get_string_fields ( $a_parameters = null ) {
    
    $s_fields     = "";

    $oBase        = new base();
    $a_components = $oBase->get_string_components( $a_parameters );
    
    // construccion del string de campos a partir del array de campos
    foreach ( $a_components[ 'a_fields' ] as $i_field_position => $a_field_data ) 
    {
      // construccion del string de con los nombres de las tablas, campos y alias sin corresponde      
      $s_field = "`" . $a_field_data[ 's_entity' ]. "`.`" . $a_field_data[ 's_property' ]. "`";     // string nombre campo basico
      if ( isset( $a_field_data[ 's_alias' ] ) )
        $s_field .= " AS `" . $a_field_data[ 's_alias' ]. "`";                                      // agregar string del alias
      
      $s_fields .= ( $s_fields != "" ? ", " : "" ) . $s_field . " \n";                              // concatenar con el string de nombres de campos
    }

    return $s_fields;
  }

  public function get_string_tables ( $a_parameters = null ) {
    //var_dump( "base.get_string_tables" );

    $s_entity     = $a_parameters['aEnti'][0];
    $s_tables     = "`$s_entity`";
    
    $oBase        = new base();
    $a_components = $oBase->get_string_components( $a_parameters );

    foreach ( $a_components['a_tables'] as $i_table_position => $a_table_data ) 
    {
      $s_join  = "\n";
      $s_join .= "LEFT OUTER JOIN `" . $a_table_data['s_relacion_entidad'] . "` "; 
      
      if ( isset( $a_table_data[ 's_alias' ] ) )
        $s_join .= "`" . $a_table_data['s_alias'] . "` "; 
      
      $s_join .= "ON "; 
      
      if ( isset( $a_table_data[ 's_alias' ] ) )
        $s_join .= "`" . $a_table_data['s_alias'] . "`."; 
      else
        $s_join .= "`" . $a_table_data['s_relacion_entidad'] . "`.";
      
      $s_join .= "`" . $a_table_data['s_relacion_pk'] . "` = ";
      $s_join .= "`$s_entity`.`". $a_table_data['s_property'] ."` ";

      $s_tables .= $s_join;
    }

    // 2017.05.25 - procesar join clausula -INI
    if ( ! empty( $a_parameters['aJoin'] ) )
    {
        foreach ( $a_parameters['aJoin'] as $s_join_entity => $s_join_on ) 
        {
          $s_tables .= "LEFT OUTER JOIN `$s_join_entity` ON $s_join_on";       
        }
    }
    // 2017.05.25 - procesar join clausula -FIN

    return $s_tables;
  }

  // ------------------------------------------------------------------------INI
  public static function set( $aValo ) {
    //var_dump( $aValo );

    $s_entity = $aValo['aEnti'][0];
    $o_entity = new $s_entity(); 

    // 2018.05.17 - se incluye en este metodo para poder utilizar la funcion 
    // mysqli_real_escape_string mas adelante. Si este objeto no funciona, no 
    // se actualizan los datos.
    $mysqli = new mysqli( FMWK_BASE_SERV, FMWK_BASE_USUA, FMWK_BASE_PASW, FMWK_BASE_NOMB );

    $oBase = new base();
    $sSenC = "";
    $sSenV = "";
    $sSenK = $oBase->strgClav( $aValo );                                        // formo string con los campos claves del WHERE
    $sSenT = $oBase->strgTabl( $aValo );                                        // formo string con los tablas claves del FROM
    $aResu = base::get( $aValo );                                               // paso los datos para saber si hay una instancia
    //print_r( PHP_VERSION_ID );
    
    $b_resu = ! is_null( $aResu );
    $i_cant = $b_resu ? $aResu['iCant'] : 0;
    $b_create = ! $b_resu || ( $b_resu && $i_cant == 0 );

    //print_r( PHP_VERSION_ID );
    //if ( $aResu['iCant'] == 0 )                                                 // si no existe se crea
    if ( $b_create )                                                            // si no existe se crea
    {
      foreach( $aValo['aDato'] as $sColu => $xValo )
      {

        //print_r( $sColu . "<br> \n" );

        if ( PHP_VERSION_ID >= 50635 )
          $xValo = mysqli_real_escape_string( $mysqli, $xValo );
        else
          $xValo = mysql_real_escape_string( $xValo );
        
        // 2018.05.20 - control campos id
        // en el ambiente de desarrollo (lenovo) con php 5.6.35 y mysql 5.7.21
        // al insertar valor string '' en los campos ids, se producian errores
        // por incompatibilidad de datos.

          // clave primaria
          if ( $sColu == "id" )
            continue;

          // claves foraneas
          if ( strpos( $sColu, "_id" ) !== false )
          {
            if ( strpos( $sColu, "_id" ) == strlen( $sColu ) - 3 )
            {
              if ( trim( $xValo ) == "" )
                $xValo = 0;
            } 
          }

        $sSenC .= ( ( $sSenC != "" )? ", " : "" ).$sColu;

        if ( $o_entity->a_property[ $sColu ]['s_type'] == "int" && $xValo == "" )
          $sSenV .= ( ( $sSenV != "" )? ", " : "" )."0";
        else if ( $o_entity->a_property[ $sColu ]['s_type'] == "tinyint" && $xValo == "" )
          $sSenV .= ( ( $sSenV != "" )? ", " : "" )."0";
        else
          $sSenV .= ( ( $sSenV != "" )? ", " : "" )."'$xValo'";
      }
      $sSent = "INSERT INTO $sSenT ($sSenC) VALUES ($sSenV);";
    }
    else
    {
      foreach( $aValo['aDato'] as $sColu => $xValo )
      {
        if ( PHP_VERSION_ID >= 50635 )
          $xValo = mysqli_real_escape_string( $mysqli, $xValo );
        else
          $xValo = mysql_real_escape_string( $xValo );
        
        /*
        var_dump( $sColu );
        var_dump( $o_entity->a_property[ $sColu ]['s_type'] );
        var_dump( $xValo );
        //*/

        if ( ! in_array( $sColu, $aValo['aClav'] ) )                            // los campos claves no deben incluirse en los campos a actualizar
        {
          if ( $sColu == "usuapass" && $xValo == "" )                           // si el contenido del campo de clave del usuario esta vacio no lo proceso en el update.
            continue;
          
          // control sobre el tipo de dato y el valor
          //var_dump( $o_entity->a_property[ $sColu ]['s_type'] );
          if ( $sColu == "fecha_creacion" && $xValo == "" )
          {
            //$xValo = "null";
            continue;
          }
          else if ( $o_entity->a_property[ $sColu ]['s_type'] == "date" && 
            ( $xValo == "0000-00-00" || $xValo == "" ) )
          {
            $xValo = "null";
          }
          else if ( $o_entity->a_property[ $sColu ]['s_type'] == "datetime" && ( $xValo == "0000-00-00" || $xValo == "" ) )
          {
            $xValo = "null";
          }
          else if ( $o_entity->a_property[ $sColu ]['s_type'] == "time" && $xValo == "" )
          {
            $xValo = "null";
          }
          else if ( $o_entity->a_property[ $sColu ]['s_type'] == "decimal" )
          {
            $xValo = str_replace( ",", ".", $xValo );
            if ( $xValo == "" )
              $xValo = "null";
          }
          else if ( $o_entity->a_property[ $sColu ]['s_type'] == "tinyint" && $xValo == "" )
          {
            $xValo = "'0'";
          }
          else if ( $o_entity->a_property[ $sColu ]['s_type'] == "int" && $xValo == "" )
          {
            $xValo = "'0'";
          }
          else
          {
            $xValo = "'$xValo'";
          }

          // 2018.05.20 - control campos id
          // en el ambiente de desarrollo (lenovo) con php 5.6.35 y mysql 5.7.21
          // al insertar valor string '' en los campos ids, se producian errores
          // por incompatibilidad de datos.

            // claves foraneas
            if ( strpos( $sColu, "_id" ) !== false )
            {
              if ( strpos( $sColu, "_id" ) == strlen( $sColu ) - 3 )
              {
                if ( trim( $xValo ) == "''" )
                  $xValo = "'0'";
              } 
            }

          $sSenC .= ( ( $sSenC != "" )? ", " : "" )."$sColu = $xValo";
        }
      }
      $sSent = "UPDATE $sSenT SET $sSenC WHERE $sSenK;";
      //echo "$sSent <br />";
    }

    if ( ! isset( $aValo['sChst'] ))
      $aValo['sChst'] = "";

    return $oBase->procSent( $sSent, $aValo['sChst'] );
  }
  // ------------------------------------------------------------------------FIN
  
  // ------------------------------------------------------------------------INI
  public static function get ( $aValo ) {
    //var_dump( "framework base.get" );
    //var_dump( $aValo );
    
    $oBase = new base();
    //$s_fields = get_string_fields();
    $sSenK = $oBase->strgClav( $aValo );                                        // formo string con los campos claves del WHERE
    $sSenU = $oBase->strgUnic( $aValo );                                        // formo string con los campos claves del WHERE
    $sSenT = $oBase->strgTabl( $aValo );                                        // formo string con los tablas claves del FROM
    //$s_fields = get_string_fields();
    $sSent = "SELECT * FROM $sSenT WHERE ";
    $sSent .= ( $sSenK != "" )? $sSenK : $sSenU;   
    if ( $sSenK != "" || $sSenU != "" )                                         // para el get se debe tener o los campos claves o algun campo unico.
    {
      // 2017.01.29 - borrado logico - INI
      $s_entity = $aValo['aEnti'][0];
      $o_entity = new $s_entity();
      
      if ( isset( $o_entity->a_property['flag_estado_borrado'] ) )
      {
        $s_flag = "WHERE $s_entity.flag_estado_borrado = 0 AND";
        $sSent  = str_replace( "WHERE", $s_flag, $sSent );
      }

      // 2019.06.17 - borrado logico
      if ( isset( $o_entity->a_property['flag_borrado'] ) )
      {
        $s_flag = "WHERE $s_entity.flag_borrado = 0 AND";
        $sSent  = str_replace( "WHERE", $s_flag, $sSent );
      }
      
      if ( ! isset( $aValo['sChst'] ))
        $aValo['sChst'] = "";
      
      //echo $sSent."<br />";
      return $oBase->procSent( $sSent, $aValo['sChst'] );
    }
  }
  // ------------------------------------------------------------------------FIN

// ------------------------------------------------------------------------INI
  public static function getAll ( $aValo ) {
    //var_dump( "framework base.getAll" );
    //var_dump( $aValo );
    
    $sSenT = $aValo['aEnti'][0];
    $oBase = new base();
    //$s_fields = get_string_fields();
    $sSenO = "ORDER BY";
    $sSent = "SELECT * FROM $sSenT";

    // 2017.01.29 - borrado logico - INI
    $s_entity = $aValo['aEnti'][0];
    $o_entity = new $s_entity();
    if ( isset( $o_entity->a_property['flag_estado_borrado'] ) )
      $sSent .= " WHERE $s_entity.flag_estado_borrado = 0";
    // 2017.01.29 - borrado logico - END

    foreach( $aValo['aOrde'] as $iPosi => $sOrde )
    {
      $sOrde = trim( $sOrde );
      if ( $sOrde != "" )
      {
        $sSenO .= ( ( $sSenO != "ORDER BY" )? ", " : " " )."$sOrde";
      } 
    }        
    $sSent .= ( $sSenO != "ORDER BY" )? " ".$sSenO : "";
    
    return $oBase->procSent( $sSent, $aValo['sChst'] );
  }
  // ------------------------------------------------------------------------FIN

  // ------------------------------------------------------------------------INI
  public static function rem ( $aValo ) {
    //var_dump( "framework - base.rem" );
    //var_dump( $aValo );
    $b_procesar = false;

    $oBase = new base();
    $sSenK = $oBase->strgClav( $aValo );                                                            // formo string con los campos claves del WHERE
    $sSenU = $oBase->strgUnic( $aValo );                                                            // formo string con los campos claves del WHERE
    $sSenT = $oBase->strgTabl( $aValo );                                                            // formo string con los tablas claves del FROM
    $sSent = "DELETE FROM $sSenT WHERE ";
    $sSent .= ( $sSenK != "" )? $sSenK : $sSenU;
    
    // 2016-05-29 - ajuste para que se pueda eliminar pasando cualquier campo de la entidad.
    if ( $sSenK == "" && $sSenU == "" )
    {
      $s_where = "";
      foreach ( $aValo['aDato'] as $s_campo => $x_valor ) 
      {
        $s_where .= $s_where != "" ? "AND " :  "";
        $s_where .= "$s_campo = '$x_valor' ";
      }
      
      if ( $s_where != "" ) 
      {
        $sSent .= $s_where;
        $b_procesar = true;
      }
    }
    
    if ( $sSenK != "" || $sSenU != "" )                                                             // para el get se debe tener o los campos claves o algun campo unico.
        $b_procesar = true;

    //echo $sSent;
    //exit();

    if ( $b_procesar )
      return $oBase->procSent( $sSent, $aValo['sChst'] );
  }
  // ------------------------------------------------------------------------FIN
  
  // ------------------------------------------------------------------------INI
  public static function busc ( $aValo ) {
    //var_dump( "base.busc" );
    //var_dump( $aValo );

    $oInst = $aValo;                                                                                // 20150823 - Se tiene que tener una referencia total del objeto
    $aValo = $oInst->aBase;                                                                         // 20150823 - Para no afectar al codigo existente
    $oBase = new base();                                                                            // objeto base para utilizar metodos
    $sSenT = "";
    $sSenC = $oBase->get_string_fields( $aValo );
    $sSenW = "WHERE";
    $sSenO = "ORDER BY";
    $sSenL = "";
    
    if ( $aValo['aEnti'][0] != "sent" )                                                             // si se consulta la tabla de permisos de las entidades se omite el procPerm
      $aValo['aFilt'][] = $oBase->procPerm( $aValo );                                               // evaluar los permisos para la consulta.
    
    if ( isset( $aValo['bCant'] ) )
    {
      $sSenC .= "COUNT(*) CANT";                                                                    // contador de registros
    }

    $sSenT = $oBase->get_string_tables( $aValo );                                                   // formo string con los tablas claves del FROM
   
    //var_dump( $sSenW );
    //var_dump( $aValo['aFilt'] );
    foreach( $aValo['aFilt'] as $iPosi => $sFilt )
    {
      if ( trim( $sFilt ) != "" )
        $sSenW .= ( ( $sSenW != "WHERE" )? " AND " : " " )."$sFilt";                                // formo string del WHERE con las columnas y valores que se desean filtrar
    }
    //var_dump( $sSenW );

    //var_dump( $aValo['aOrde'] );
    foreach( $aValo['aOrde'] as $iPosi => $sOrde )
    {
      $sOrde = trim( $sOrde );
      if ( $sOrde != "" )
      {
        $sSenO .= ( ( $sSenO != "ORDER BY" )? ", " : " " )."$sOrde";                                // formo string del ORDER con las columnas y valores que se desean ordenar
      } 
    }                              
      
    if ( ! is_null( $aValo['aLimi']['iCant'] ) )
      $sSenL = "LIMIT ".$aValo['aLimi']['iInic'].", ".$aValo['aLimi']['iCant'];

    // 2016-03-08 - No se estaban generando los campos de las entidades.
    if ( $sSenC == "" )
      $sSenC = "*";

    // 2017.01.29 - borrado logico - INI
    $s_entity = $aValo['aEnti'][0];                                                                 // 2017.05.06 - query compleja generaba error al no estar la entidad
    if ( isset( $oInst->a_property['flag_estado_borrado'] ) )
    {
      if ( trim( $sSenW ) != "WHERE" )
        $s_flag = "WHERE $s_entity.flag_estado_borrado = 0 AND";
      else
        $s_flag = "WHERE $s_entity.flag_estado_borrado = 0";
      $sSenW = str_replace( "WHERE", $s_flag, $sSenW );
    }
    // 2017.01.29 - borrado logico - END

    $sSent = "SELECT $sSenC FROM $sSenT";
    $sSent .= ( trim( $sSenW ) != "WHERE" )? " ".$sSenW : "";
    $sSent .= ( $sSenO != "ORDER BY" )? " ".$sSenO : "";
    $sSent .= ( $sSenL != "" )? " ".$sSenL : "";
    //echo $sSent . "<br />";
    $oBase = new base();
    $aBase = $oBase->procSent( $sSent, $aValo['sChst'] );
    return $aBase;
    //*/
  }
  // ------------------------------------------------------------------------FIN

  public static function query_sql ( $aValo ) {
    //var_dump( "base.busc" );
    //var_dump( $aValo );

    $oInst = $aValo;                                                                                // 20150823 - Se tiene que tener una referencia total del objeto
    $aValo = $oInst->aBase;                                                                         // 20150823 - Para no afectar al codigo existente
    $oBase = new base();                                                                            // objeto base para utilizar metodos
    $sSenT = "";
    $sSenC = $oBase->get_string_fields( $aValo );
    $sSenW = "WHERE";
    $sSenO = "ORDER BY";
    $sSenL = "";
    
    if ( $aValo['aEnti'][0] != "sent" )                                                             // si se consulta la tabla de permisos de las entidades se omite el procPerm
      $aValo['aFilt'][] = $oBase->procPerm( $aValo );                                               // evaluar los permisos para la consulta.
    
    if ( isset( $aValo['bCant'] ) )
    {
      $sSenC .= "COUNT(*) CANT";                                                                    // contador de registros
    }

    $sSenT = $oBase->get_string_tables( $aValo );                                                   // formo string con los tablas claves del FROM
   
    //var_dump( $sSenW );
    //var_dump( $aValo['aFilt'] );
    foreach( $aValo['aFilt'] as $iPosi => $sFilt )
    {
      if ( trim( $sFilt ) != "" )
        $sSenW .= ( ( $sSenW != "WHERE" )? " AND " : " " )."$sFilt";                                // formo string del WHERE con las columnas y valores que se desean filtrar
    }
    //var_dump( $sSenW );

    //var_dump( $aValo['aOrde'] );
    foreach( $aValo['aOrde'] as $iPosi => $sOrde )
    {
      $sOrde = trim( $sOrde );
      if ( $sOrde != "" )
      {
        $sSenO .= ( ( $sSenO != "ORDER BY" )? ", " : " " )."$sOrde";                                // formo string del ORDER con las columnas y valores que se desean ordenar
      } 
    }                              
      
    if ( ! is_null( $aValo['aLimi']['iCant'] ) )
      $sSenL = "LIMIT ".$aValo['aLimi']['iInic'].", ".$aValo['aLimi']['iCant'];

    // 2016-03-08 - No se estaban generando los campos de las entidades.
    if ( $sSenC == "" )
      $sSenC = "*";

    // 2017.01.29 - borrado logico - INI
    $s_entity = $aValo['aEnti'][0];                                                                 // 2017.05.06 - query compleja generaba error al no estar la entidad
    if ( isset( $oInst->a_property['flag_estado_borrado'] ) )
    {
      if ( trim( $sSenW ) != "WHERE" )
        $s_flag = "WHERE $s_entity.flag_estado_borrado = 0 AND";
      else
        $s_flag = "WHERE $s_entity.flag_estado_borrado = 0";
      $sSenW = str_replace( "WHERE", $s_flag, $sSenW );
    }
    // 2017.01.29 - borrado logico - END

    $sSent = "SELECT $sSenC FROM $sSenT";
    $sSent .= ( trim( $sSenW ) != "WHERE" )? " ".$sSenW : "";
    $sSent .= ( $sSenO != "ORDER BY" )? " ".$sSenO : "";
    $sSent .= ( $sSenL != "" )? " ".$sSenL : "";
    //echo $sSent . "<br />";
    //$oBase = new base();
    //$aBase = $oBase->procSent( $sSent, $aValo['sChst'] );
    //return $aBase;
    $sSent = str_replace( "`", "", $sSent );
    return $sSent;
    //*/
  }

  public static function get_query_info ( $a_parameters = null ) {
    //var_dump( "base.query_info()" );
    //var_dump( $a_parameters );

    // variables 
      $s_query = $a_parameters;
      $a_query = array();
      
      $a_key_words = array( "SELECT", "FROM", "WHERE", "ORDER" );
      $a_key_position = array();

      foreach ( $a_key_words as $i_key => $s_key )
        if ( strpos( $a_parameters, $s_key ) !== false )
          $a_key_position[ $s_key ] = strpos( $a_parameters, $s_key );

      $a_key_position = array_reverse( $a_key_position );
      
      foreach ( $a_key_position as $s_key => $i_position ) 
      {
        $s_query_part = substr( $s_query, $i_position );
        $s_query = substr( $s_query, 0, $i_position );
        $a_query[ $s_key ] = $s_query_part;
      }

    // procesar parte select
      $a_query_select = self::get_query_select_info( $a_query['SELECT']  );
      $a_query_from   = self::get_query_from_info( $a_query['FROM']  );
      
    // retorno
      $a_result = array(
        "select" => array(
          "base"  => $a_query['SELECT'],
          "field" => $a_query_select,
        ), 
        "form"   => array(
          "base"  => $a_query['FROM'],
          "parts" => $a_query_from,
        ), 
        "where"  => $a_query['WHERE'],
        "order"  => $a_query['ORDER'],
      );

    //var_dump( $a_result );

    return $a_result;
  }

  public static function get_query_select_info ( $a_parameters = null ) {

    // 2020.08.26 - no puede realizar el explode por el caracter coma (,) una expresion del select (col) puede tener 
    // funciones cuyos parametros son seprados por coma.
    // el metodo debe separar las expresiones existentes en el string del SELECT de la consulta

    // debug

      //$a_parameters = "SELECT CONCAT(last_name,', ',first_name) AS full_name";
      //$a_parameters = "SELECT CONCAT(last_name,', ',first_name)";
      //$a_parameters = "SELECT CONCAT(last_name,', ',first_name) AS full_name, CONCAT(last_name,', ',first_name) AS full_name2";

    // variables
      
      $s_select_part = str_replace( "SELECT ", "", $a_parameters );

    // controles
      $a_select_part = self::get_expressions( array(
        "string" => $s_select_part,
        "delimiter" => ","
      ));

    // seaparar cada expresion
      $a_select_field = array();
      foreach ( $a_select_part as $i_field => $s_field ) 
      {
        $s_field = trim( str_replace( "\n", "", $s_field ));
        //var_dump( $s_field );

        $s_field_entity = "";
        $s_field_column = $s_field;
        $s_field_alias  = "";

        // control alias
          $s_field = str_replace( ")AS ", ") AS ", $s_field );
          $s_field = str_replace( " as ", " AS ", $s_field );
          $s_alias_separador = strpos( $s_field, " AS " ) !== false ? " AS " : 
            ( strpos( $s_field, " " ) !== false ? " " : "" );
          
          $b_alias_separador = $s_alias_separador != "";
          if ( $b_alias_separador )
          {
            $a_field = explode( $s_alias_separador, $s_field );
            $s_field_column = $a_field[0];
            $s_field_alias  = str_replace( "'", "", $a_field[1] );
          }

        // control funcion 
          $i_apertura = strpos( $s_field_column, "(" );
          $i_cierre   = strpos( $s_field_column, ")" );
          $b_parentesis = $i_apertura !== false && $i_cierre !== false;
          $b_function = $b_parentesis ? $i_apertura > 0 : false;

        // control separador de campo
          $b_campo_separador = strpos( $s_field_column, "." ) !== false;
          
          if ( ! $b_function && $b_campo_separador )
          {
            $a_field_column = explode( ".", $s_field_column );
            $s_field_entity = $a_field_column[0];
            $s_field_column = $a_field_column[1];
          }

        // control key
          $s_key = $b_alias_separador ? $s_field_alias : 
            ( ! $b_function ? $s_field_column : "" );

        // formateo
          $s_field_entity = str_replace( "\n", "", $s_field_entity );
          $s_field_column = str_replace( "\n", "", $s_field_column );
          $s_field_alias  = str_replace( "'", "", $s_field_alias );

        // item   
          //$a_select_field[ $s_field_column ] = array(
          $a_select_field[ $s_key ] = array(
            "entity" => $s_field_entity,
            "column" => $s_field_column,
            "alias"  => $s_field_alias,
          );
      }

    // devolucion
    return $a_select_field;
  }

  public static function get_query_from_info ( $a_parameters = null ) {

    // debug

    // variables
      
      $s_string = str_replace( "FROM ", "", $a_parameters );
      $a_result = array();
    
    // controles
     
    // seaparar cada expresion
      $a_string = explode( " ", $s_string );

      $a_result = array( 
        "entity" => $a_string[0],
        "alias"  => $a_string[1],
      );

    // devolucion
      return $a_result;
  }

  public static function get_expressions ( $a_parameters = null ) {
    $s_string    = $a_parameters['string'];
    $s_delimiter = $a_parameters['delimiter'];
    $s_control   = $s_string;
    $a_delimiter = array();

    // quitar parentesis y contenido
      do {
        $i_apertura  = strrpos( $s_control, "(", -1 ); 
        $i_cierre    = strpos( $s_control, ")", $i_apertura );
        $s_contenido = substr( $s_control, $i_apertura, $i_cierre - $i_apertura + 1 );
        $i_contenido = strlen( $s_contenido );
        $s_reemplazo = str_repeat( "_", $i_contenido );
        $s_control = str_replace( $s_contenido, $s_reemplazo, $s_control );
        $b_parentesis = $i_apertura !== false && $i_cierre !== false;  
      } while ( $b_parentesis );
    
    // obtener las posiciones de los delimitadores de las expresiones
      do {
        $i_delimitador = strrpos( $s_control, ",", -1 );
        
        if ( $i_delimitador !== false )
        {
          $a_delimiter[] = $i_delimitador;
          $s_control = substr( $s_control, 0, $i_delimitador );
        }
      } while ( $i_delimitador !== false );
    
    // extraer las expresiones
      $a_expresion = array();
      $a_delimiter[] = 0;
      foreach ( $a_delimiter as $i => $v ) 
      {
        $s_expresion = substr( $s_string, $v );
        $s_expresion = trim( str_replace( "\n", "", $s_expresion ));
        if ( strpos( $s_expresion, "," ) === 0 )
          $s_expresion = trim( substr( $s_expresion, 1));
        $a_expresion[] = $s_expresion;
        $s_string = substr( $s_string, 0, $v );
      }
      $a_expresion = array_reverse( $a_expresion );

    // return
      return $a_expresion;
  }
}