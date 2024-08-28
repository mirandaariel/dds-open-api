<?php 

class _x_productsBase extends clas {

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
      "id_public" => array(
        "b_key"    => false,
        "b_unique" => false,
        "s_name"   => "id_public",
        "s_type"   => "varchar",
        "s_label"  => "id_public",
        "a_grid"   => array(
            "s_field"        => "id_public",
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
      "id_url" => array(
        "b_key"    => false,
        "b_unique" => false,
        "s_name"   => "id_url",
        "s_type"   => "varchar",
        "s_label"  => "id_url",
        "a_grid"   => array(
            "s_field"        => "id_url",
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
      "product_name" => array(
        "b_key"    => false,
        "b_unique" => false,
        "s_name"   => "product_name",
        "s_type"   => "varchar",
        "s_label"  => "product_name",
        "a_grid"   => array(
            "s_field"        => "product_name",
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
      "product_description" => array(
        "b_key"    => false,
        "b_unique" => false,
        "s_name"   => "product_description",
        "s_type"   => "text",
        "s_label"  => "product_description",
        "a_grid"   => array(
            "s_field"        => "product_description",
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
      "record_create_date" => array(
        "b_key"    => false,
        "b_unique" => false,
        "s_name"   => "record_create_date",
        "s_type"   => "varchar",
        "s_label"  => "record_create_date",
        "a_grid"   => array(
            "s_field"        => "record_create_date",
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
      "record_create_user_id" => array(
        "b_key"    => false,
        "b_unique" => false,
        "s_name"   => "record_create_user_id",
        "s_type"   => "int",
        "s_label"  => "record_create_user_id",
        "a_grid"   => array(
            "s_field"        => "record_create_user_id",
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
      "record_create_user_id_url" => array(
        "b_key"    => false,
        "b_unique" => false,
        "s_name"   => "record_create_user_id_url",
        "s_type"   => "varchar",
        "s_label"  => "record_create_user_id_url",
        "a_grid"   => array(
            "s_field"        => "record_create_user_id_url",
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
      "record_update_date" => array(
        "b_key"    => false,
        "b_unique" => false,
        "s_name"   => "record_update_date",
        "s_type"   => "varchar",
        "s_label"  => "record_update_date",
        "a_grid"   => array(
            "s_field"        => "record_update_date",
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
      "record_update_user_id" => array(
        "b_key"    => false,
        "b_unique" => false,
        "s_name"   => "record_update_user_id",
        "s_type"   => "int",
        "s_label"  => "record_update_user_id",
        "a_grid"   => array(
            "s_field"        => "record_update_user_id",
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
      "record_update_user_id_url" => array(
        "b_key"    => false,
        "b_unique" => false,
        "s_name"   => "record_update_user_id_url",
        "s_type"   => "varchar",
        "s_label"  => "record_update_user_id_url",
        "a_grid"   => array(
            "s_field"        => "record_update_user_id_url",
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
      "record_delete_date" => array(
        "b_key"    => false,
        "b_unique" => false,
        "s_name"   => "record_delete_date",
        "s_type"   => "varchar",
        "s_label"  => "record_delete_date",
        "a_grid"   => array(
            "s_field"        => "record_delete_date",
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
      "record_delete_user_id" => array(
        "b_key"    => false,
        "b_unique" => false,
        "s_name"   => "record_delete_user_id",
        "s_type"   => "int",
        "s_label"  => "record_delete_user_id",
        "a_grid"   => array(
            "s_field"        => "record_delete_user_id",
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
      "record_delete_user_id_url" => array(
        "b_key"    => false,
        "b_unique" => false,
        "s_name"   => "record_delete_user_id_url",
        "s_type"   => "varchar",
        "s_label"  => "record_delete_user_id_url",
        "a_grid"   => array(
            "s_field"        => "record_delete_user_id_url",
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
      "record_delete_flag" => array(
        "b_key"    => false,
        "b_unique" => false,
        "s_name"   => "record_delete_flag",
        "s_type"   => "tinyint",
        "s_label"  => "record_delete_flag",
        "a_grid"   => array(
            "s_field"        => "record_delete_flag",
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
      "id_public", 
      "id_url", 
      "product_name", 
      "product_description", 
      "record_create_date", 
      "record_create_user_id", 
      "record_create_user_id_url", 
      "record_update_date", 
      "record_update_user_id", 
      "record_update_user_id_url", 
      "record_delete_date", 
      "record_delete_user_id", 
      "record_delete_user_id_url", 
      "record_delete_flag", 
    );                                                                                              // columnas de la entidad en la basa de datos
    $this->aTipo = array(

      "int", 
      "varchar", 
      "varchar", 
      "varchar", 
      "text", 
      "varchar", 
      "int", 
      "varchar", 
      "varchar", 
      "int", 
      "varchar", 
      "varchar", 
      "int", 
      "varchar", 
      "tinyint", 
    );                                                                                              // tipo de datos de las columnas. Sirven para los componentes grilla y formulario
    $this->aEtiq = array(

      "id", 
      "id_public", 
      "id_url", 
      "product_name", 
      "product_description", 
      "record_create_date", 
      "record_create_user_id", 
      "record_create_user_id_url", 
      "record_update_date", 
      "record_update_user_id", 
      "record_update_user_id_url", 
      "record_delete_date", 
      "record_delete_user_id", 
      "record_delete_user_id_url", 
      "record_delete_flag", 
    );                                                                                              // etiqueta de los campos. Ser�n mostrados en el componente grilla y formulario
    $this->iGridText = 50;                                                                          // longitud maxima de un campo de texto en la grilla.
    $this->aGrid = array( 
      array( "id", "id", 60, 30, true, "center", false, false ), 
      array( "id_public", "id_public", 60, 30, true, "center", false, false ), 
      array( "id_url", "id_url", 60, 30, true, "center", false, false ), 
      array( "product_name", "product_name", 60, 30, true, "center", false, false ), 
      array( "product_description", "product_description", 60, 30, true, "center", false, false ), 
      array( "record_create_date", "record_create_date", 60, 30, true, "center", false, false ), 
      array( "record_create_user_id", "record_create_user_id", 60, 30, true, "center", false, false ), 
      array( "record_create_user_id_url", "record_create_user_id_url", 60, 30, true, "center", false, false ), 
      array( "record_update_date", "record_update_date", 60, 30, true, "center", false, false ), 
      array( "record_update_user_id", "record_update_user_id", 60, 30, true, "center", false, false ), 
      array( "record_update_user_id_url", "record_update_user_id_url", 60, 30, true, "center", false, false ), 
      array( "record_delete_date", "record_delete_date", 60, 30, true, "center", false, false ), 
      array( "record_delete_user_id", "record_delete_user_id", 60, 30, true, "center", false, false ), 
      array( "record_delete_user_id_url", "record_delete_user_id_url", 60, 30, true, "center", false, false ), 
      array( "record_delete_flag", "record_delete_flag", 60, 30, true, "center", false, false ), 

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
    $this->aBase['aEnti'][] = "x_products";                                                               // nombre de la tabla en la base de datos.
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