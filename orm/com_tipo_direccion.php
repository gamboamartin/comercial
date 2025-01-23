<?php

namespace gamboamartin\comercial\models;

use base\orm\_modelo_parent_sin_codigo;
use PDO;

class com_tipo_direccion extends _modelo_parent_sin_codigo {
    public function __construct(PDO $link){
        $tabla = 'com_tipo_direccion';
        $columnas = array($tabla=>false);
        $campos_obligatorios = array();

        $no_duplicados = array();


        parent::__construct(link: $link,tabla:  $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas,no_duplicados: $no_duplicados);

        $this->NAMESPACE = __NAMESPACE__;
    }
}