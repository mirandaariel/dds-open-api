<?php 

class _media_fileBase extends clas {
    
  public $a_view = [];
  public $a_property = [];
  public $aProp = [];
  public $aTipo = [];
  public $aEtiq = [];
  public $iGridText = 0;
  public $aGrid = [];
  public $aFilt = [];
  public $aBase = [];
  
  // ------------------------------------------------------------------------------------------- INI
  public function __construct () {
    $this->a_view = array(
        "a_list" => array(
            "a_image" => array(),                                                                   // cada elemento es un lugar para la imagen en el item
            "a_line"  => array(),                                                                   // cada elemento es una linea de texto en el item
        ),
        "a_grid" => array(
            "b_multiple_seleccion" => true,
            "b_agrupacion"         => false,
            "a_agrupacion"         => array(
                "groupField"      => array(), //["codigo"],
                "groupColumnShow" => array(), //[false],
                "groupText"       => array(), //["<b>Reserva {0}</b>"],
                "groupOrder"      => array(), //["asc"],
                "groupSummary"    => array(), //[true],
                "groupCollapse"   => false,   //false
            ),
        ),
    );
    $this->a_property = array(
      "id" => array(
        "b_key"    => true,
        "b_unique" => false,
        "s_name"   => "id",
        "s_type"   => "int",
        "s_label"  => "id",
        "a_grid"   => array(
            "s_field"        => "id",
            "s_column_label" => "",
            "s_align"        => "left",
            "i_width"        => 100,
            "i_height"       => 30,
            "b_sortable"     => false,
            "b_hidden"       => false,
            "b_frozen"       => false,
        ),
        "a_form"  => array(
            "s_mask"     => "",
            "s_label"    => "",
            "b_hidden"   => false,
            "b_required" => false,
            "b_create"   => false,
        ),
        "a_relation" => array(
            "s_entity"   => "",
            "s_property" => "",
            "a_replace"  => array(),
        ),
      ),
      "nombre" => array(
        "b_key"    => false,
        "b_unique" => false,
        "s_name"   => "nombre",
        "s_type"   => "varchar",
        "s_label"  => "nombre",
        "a_grid"   => array(
            "s_field"        => "nombre",
            "s_column_label" => "",
            "s_align"        => "left",
            "i_width"        => 100,
            "i_height"       => 30,
            "b_sortable"     => false,
            "b_hidden"       => false,
            "b_frozen"       => false,
        ),
        "a_form"  => array(
            "s_mask"     => "",
            "s_label"    => "",
            "b_hidden"   => false,
            "b_required" => false,
            "b_create"   => false,
        ),
        "a_relation" => array(
            "s_entity"   => "",
            "s_property" => "",
            "a_replace"  => array(),
        ),
      ),
      "link" => array(
        "b_key"    => false,
        "b_unique" => false,
        "s_name"   => "link",
        "s_type"   => "varchar",
        "s_label"  => "link",
        "a_grid"   => array(
            "s_field"        => "link",
            "s_column_label" => "",
            "s_align"        => "left",
            "i_width"        => 100,
            "i_height"       => 30,
            "b_sortable"     => false,
            "b_hidden"       => false,
            "b_frozen"       => false,
        ),
        "a_form"  => array(
            "s_mask"     => "",
            "s_label"    => "",
            "b_hidden"   => false,
            "b_required" => false,
            "b_create"   => false,
        ),
        "a_relation" => array(
            "s_entity"   => "",
            "s_property" => "",
            "a_replace"  => array(),
        ),
      ),
      "descripcion" => array(
        "b_key"    => false,
        "b_unique" => false,
        "s_name"   => "descripcion",
        "s_type"   => "varchar",
        "s_label"  => "descripcion",
        "a_grid"   => array(
            "s_field"        => "descripcion",
            "s_column_label" => "",
            "s_align"        => "left",
            "i_width"        => 100,
            "i_height"       => 30,
            "b_sortable"     => false,
            "b_hidden"       => false,
            "b_frozen"       => false,
        ),
        "a_form"  => array(
            "s_mask"     => "",
            "s_label"    => "",
            "b_hidden"   => false,
            "b_required" => false,
            "b_create"   => false,
        ),
        "a_relation" => array(
            "s_entity"   => "",
            "s_property" => "",
            "a_replace"  => array(),
        ),
      ),
      "clase_foranea" => array(
        "b_key"    => false,
        "b_unique" => false,
        "s_name"   => "clase_foranea",
        "s_type"   => "varchar",
        "s_label"  => "clase_foranea",
        "a_grid"   => array(
            "s_field"        => "clase_foranea",
            "s_column_label" => "",
            "s_align"        => "left",
            "i_width"        => 100,
            "i_height"       => 30,
            "b_sortable"     => false,
            "b_hidden"       => false,
            "b_frozen"       => false,
        ),
        "a_form"  => array(
            "s_mask"     => "",
            "s_label"    => "",
            "b_hidden"   => false,
            "b_required" => false,
            "b_create"   => false,
        ),
        "a_relation" => array(
            "s_entity"   => "",
            "s_property" => "",
            "a_replace"  => array(),
        ),
      ),
      "clave_foranea" => array(
        "b_key"    => false,
        "b_unique" => false,
        "s_name"   => "clave_foranea",
        "s_type"   => "int",
        "s_label"  => "clave_foranea",
        "a_grid"   => array(
            "s_field"        => "clave_foranea",
            "s_column_label" => "",
            "s_align"        => "left",
            "i_width"        => 100,
            "i_height"       => 30,
            "b_sortable"     => false,
            "b_hidden"       => false,
            "b_frozen"       => false,
        ),
        "a_form"  => array(
            "s_mask"     => "",
            "s_label"    => "",
            "b_hidden"   => false,
            "b_required" => false,
            "b_create"   => false,
        ),
        "a_relation" => array(
            "s_entity"   => "",
            "s_property" => "",
            "a_replace"  => array(),
        ),
      ),
      "campo_foraneo" => array(
        "b_key"    => false,
        "b_unique" => false,
        "s_name"   => "campo_foraneo",
        "s_type"   => "varchar",
        "s_label"  => "campo_foraneo",
        "a_grid"   => array(
            "s_field"        => "campo_foraneo",
            "s_column_label" => "",
            "s_align"        => "left",
            "i_width"        => 100,
            "i_height"       => 30,
            "b_sortable"     => false,
            "b_hidden"       => false,
            "b_frozen"       => false,
        ),
        "a_form"  => array(
            "s_mask"     => "",
            "s_label"    => "",
            "b_hidden"   => false,
            "b_required" => false,
            "b_create"   => false,
        ),
        "a_relation" => array(
            "s_entity"   => "",
            "s_property" => "",
            "a_replace"  => array(),
        ),
      ),
      "ruta_upload" => array(
        "b_key"    => false,
        "b_unique" => false,
        "s_name"   => "ruta_upload",
        "s_type"   => "varchar",
        "s_label"  => "ruta_upload",
        "a_grid"   => array(
            "s_field"        => "ruta_upload",
            "s_column_label" => "",
            "s_align"        => "left",
            "i_width"        => 100,
            "i_height"       => 30,
            "b_sortable"     => false,
            "b_hidden"       => false,
            "b_frozen"       => false,
        ),
        "a_form"  => array(
            "s_mask"     => "",
            "s_label"    => "",
            "b_hidden"   => false,
            "b_required" => false,
            "b_create"   => false,
        ),
        "a_relation" => array(
            "s_entity"   => "",
            "s_property" => "",
            "a_replace"  => array(),
        ),
      ),
      "batch_id" => array(
        "b_key"    => false,
        "b_unique" => false,
        "s_name"   => "batch_id",
        "s_type"   => "varchar",
        "s_label"  => "batch_id",
        "a_grid"   => array(
            "s_field"        => "batch_id",
            "s_column_label" => "",
            "s_align"        => "left",
            "i_width"        => 100,
            "i_height"       => 30,
            "b_sortable"     => false,
            "b_hidden"       => false,
            "b_frozen"       => false,
        ),
        "a_form"  => array(
            "s_mask"     => "",
            "s_label"    => "",
            "b_hidden"   => false,
            "b_required" => false,
            "b_create"   => false,
        ),
        "a_relation" => array(
            "s_entity"   => "",
            "s_property" => "",
            "a_replace"  => array(),
        ),
      ),
      "fecha_creacion" => array(
        "b_key"    => false,
        "b_unique" => false,
        "s_name"   => "fecha_creacion",
        "s_type"   => "varchar",
        "s_label"  => "fecha_creacion",
        "a_grid"   => array(
            "s_field"        => "fecha_creacion",
            "s_column_label" => "",
            "s_align"        => "left",
            "i_width"        => 100,
            "i_height"       => 30,
            "b_sortable"     => false,
            "b_hidden"       => false,
            "b_frozen"       => false,
        ),
        "a_form"  => array(
            "s_mask"     => "",
            "s_label"    => "",
            "b_hidden"   => false,
            "b_required" => false,
            "b_create"   => false,
        ),
        "a_relation" => array(
            "s_entity"   => "",
            "s_property" => "",
            "a_replace"  => array(),
        ),
      ),

    );
    $this->aProp = array(

      "id", 
      "nombre", 
      "link", 
      "descripcion", 
      "clase_foranea", 
      "clave_foranea", 
      "campo_foraneo", 
      "ruta_upload", 
      "batch_id", 
      "fecha_creacion", 
    );                                                                                              // columnas de la entidad en la basa de datos
    $this->aTipo = array(

      "int", 
      "varchar", 
      "varchar", 
      "varchar", 
      "varchar", 
      "int", 
      "varchar", 
      "varchar", 
      "varchar", 
      "varchar", 
    );                                                                                              // tipo de datos de las columnas. Sirven para los componentes grilla y formulario
    $this->aEtiq = array(

      "id", 
      "nombre", 
      "link", 
      "descripcion", 
      "clase_foranea", 
      "clave_foranea", 
      "campo_foraneo", 
      "ruta_upload", 
      "batch_id", 
      "fecha_creacion", 
    );                                                                                              // etiqueta de los campos. Ser�n mostrados en el componente grilla y formulario
    $this->iGridText = 50;                                                                          // longitud maxima de un campo de texto en la grilla.
    $this->aGrid = array( 
      array( "id", "id", 60, 30, true, "center", false, false ), 
      array( "nombre", "nombre", 60, 30, true, "center", false, false ), 
      array( "link", "link", 60, 30, true, "center", false, false ), 
      array( "descripcion", "descripcion", 60, 30, true, "center", false, false ), 
      array( "clase_foranea", "clase_foranea", 60, 30, true, "center", false, false ), 
      array( "clave_foranea", "clave_foranea", 60, 30, true, "center", false, false ), 
      array( "campo_foraneo", "campo_foraneo", 60, 30, true, "center", false, false ), 
      array( "ruta_upload", "ruta_upload", 60, 30, true, "center", false, false ), 
      array( "batch_id", "batch_id", 60, 30, true, "center", false, false ), 
      array( "fecha_creacion", "fecha_creacion", 60, 30, true, "center", false, false ), 

      // field, label, width, height, sortable, align, hide, frozen
      //array( "columna", "colunomb", 100, 30, true, 'right', false, false ),                       // se debe setear la etiqueta y el id del campo porque corresponde a otra tabla
    );                                                                                              // configuraci�n del componenete grilla.
    $this->aFilt = array(
      "aText" => array(
        //array( "colu_nomb" => "etiq" ),
      ),
      "aComb" => array(
        //array( "colu_nomb" => array(
        //  array( " " => "etiq" ),
        //  array( "valo_codi" => "valo_desc" ),
        //)),
      ),
      "aFech" => array(
        //array( "colu_nomb" => "etiq" ),
      ),
    );
    $this->aBase['aEnti'][] = "media_file";                                                               // nombre de la tabla en la base de datos.
    $this->aBase['aClav'][] = "id";                                                                 // campos que representan la clave primaria
    $this->aBase['aUnic'][] = "";                                                                   // campos que son unique.
    $this->aBase['aRela'] = array(
      //"otra_enti" => array( 
      //  "otra_enti.otra_enti_colu = enti_nomb.enti_colu",
      //),
    );
    $this->aBase['aAgre'] = array(                                                                  // cuando estoy utilizando la grilla para mostrar las instancias del objeto
      //"enti_colu" => array( 
      //  "otra_enti" => "otra_enti_colu",
      //),      
    );
    $this->aBase['aForm'] = array(                                                                  // componentes tipo select por ejemplo
      //"enti_colu" => array(                                                        
      //  "clas_nomb" => array( "clas_camp_valo" => "clas_camp_etiq" ),   
      //),
    );
  }
  // ------------------------------------------------------------------------------------------- FIN  
}