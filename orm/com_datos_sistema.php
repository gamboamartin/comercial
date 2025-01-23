<?php

namespace gamboamartin\comercial\models;

use base\orm\_modelo_parent;
use PDO;


class com_datos_sistema extends _modelo_parent{
    public function __construct(PDO $link)
    {
        $tabla = 'com_datos_sistema';
        $columnas = array($tabla=>false);

        $columnas_extra= array();
        $renombres= array();


        parent::__construct(link: $link, tabla: $tabla, columnas: $columnas, columnas_extra: $columnas_extra,
            renombres: $renombres);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Datos Sistema';
    }


}