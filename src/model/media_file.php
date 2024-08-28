<?php 

class media_file extends _media_fileBase {
    public $oYApp = null;
    public $aLabels = [];
    public $a_relation = [];

    public function __construct () {
        global $oYApp;
        $this->aLabels = array(
            "title"         => "media_file",
            "title-default" => "Entidad media_file",
            "plural"        => "media_file",
            "singular"      => "media_file",
        );
        $this->oYApp = $oYApp;
        parent::__construct();

        $this->a_property['id']            ['s_label'] = "ID";
        $this->a_property['nombre']        ['s_label'] = "Nombre";
        $this->a_property['link']          ['s_label'] = "Link";
        $this->a_property['descripcion']   ['s_label'] = "Descripción";
        $this->a_property['clase_foranea'] ['s_label'] = "Entidad Nombre";
        $this->a_property['clave_foranea'] ['s_label'] = "Entidad ID";
        $this->a_property['campo_foraneo'] ['s_label'] = "Entidad Campo";
        $this->a_property['ruta_upload']   ['s_label'] = "Ruta";
        $this->a_property['batch_id']      ['s_label'] = "Batch ID";
        $this->a_property['fecha_creacion']['s_label'] = "F. Creación";

        $this->aBase['a_dependencies'] = array();
    }

    public function obteRela ( $aValo = null ) {
        $aInst = $aValo;
        
        //foreach( $aInst as $iInstPosi => $aInstValo )
        //    $aInst[ $iInstPosi ] = $aInstValo;
        
        return $aInst;
    }

    public function output_format ( $a_parameters = null ) {
        
        $a_parameters = parent::output_format( $a_parameters );
        
        $aInst = $a_parameters;
        
        //foreach( $aInst as $iInstPosi => $aInstValo )
        //    $aInst[ $iInstPosi ] = $aInstValo;
        
        return $aInst;
    }
}