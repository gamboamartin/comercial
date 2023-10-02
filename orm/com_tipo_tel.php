<?php
namespace gamboamartin\comercial\models;
use base\orm\_modelo_parent;
use PDO;

class com_tipo_tel extends _modelo_parent{
    public function __construct(PDO $link, array $childrens = array()){
        $tabla = 'com_tipo_tel';
        $columnas = array($tabla=>false);
        $campos_obligatorios = array();

        $columnas_extra = array();

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, childrens: $childrens);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Tipo de telefono';


    }

}