<?php
namespace gamboamartin\comercial\models;
use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;

class com_rel_prospecto_cte extends _modelo_parent{
    public function __construct(PDO $link, array $childrens = array()){
        $tabla = 'com_rel_prospecto_cte';
        $columnas = array($tabla=>false,'com_cliente'=>$tabla,'com_prospecto'=>$tabla);
        $campos_obligatorios = array();
        $columnas_extra = array();


        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, childrens: $childrens);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Relacion Cliente Prospecto';


    }

}