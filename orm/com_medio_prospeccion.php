<?php
namespace gamboamartin\comercial\models;
use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;

class com_medio_prospeccion extends _modelo_parent{
    public function __construct(PDO $link, array $childrens = array()){
        $tabla = 'com_medio_prospeccion';
        $columnas = array($tabla=>false);

        parent::__construct(link: $link, tabla: $tabla, columnas: $columnas, childrens: $childrens);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Tipo de Prospecto';


    }


}
