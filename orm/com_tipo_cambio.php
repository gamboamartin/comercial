<?php
namespace gamboamartin\comercial\models;
use base\orm\modelo;
use PDO;

class com_tipo_cambio extends modelo{
    public function __construct(PDO $link){
        $tabla = 'com_tipo_cambio';
        $columnas = array($tabla=>false,'cat_sat_moneda'=>$tabla,'dp_pais'=>'cat_sat_moneda');
        $campos_obligatorios = array('cat_sat_moneda_id');

        parent::__construct(link: $link,tabla:  $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas);

        $this->NAMESPACE = __NAMESPACE__;
    }
}