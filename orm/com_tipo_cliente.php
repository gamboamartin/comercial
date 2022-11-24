<?php
namespace gamboamartin\comercial\models;
use base\orm\modelo;
use PDO;

class com_tipo_cliente extends modelo{
    public function __construct(PDO $link){
        $tabla = 'com_tipo_cliente';
        $columnas = array();
        $campos_obligatorios = array();

        parent::__construct(link: $link,tabla:  $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas);

        $this->NAMESPACE = __NAMESPACE__;
    }
}