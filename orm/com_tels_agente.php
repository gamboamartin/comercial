<?php
namespace gamboamartin\comercial\models;
use base\orm\_modelo_parent;
use PDO;

class com_tels_agente extends _modelo_parent{
    public function __construct(PDO $link, array $childrens = array()){
        $tabla = 'com_tels_agente';
        $columnas = array($tabla=>false,'com_tipo_tel'=>$tabla,'com_agente'=>$tabla,'com_tipo_agente'=>'com_agente');
        $campos_obligatorios = array('com_tipo_tel_id','com_agente_id');

        $columnas_extra = array();

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, childrens: $childrens);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Telefono';


    }

}