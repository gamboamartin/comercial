<?php
namespace gamboamartin\comercial\models;
use base\orm\_modelo_parent;
use PDO;

class com_tipo_cliente extends _modelo_parent{
    public function __construct(PDO $link){
        $tabla = 'com_tipo_cliente';
        $columnas = array($tabla=>false);
        $campos_obligatorios = array();

        $campos_view['codigo'] = array('type' => 'inputs');
        $campos_view['descripcion'] = array('type' => 'inputs');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, campos_view: $campos_view);

        $this->NAMESPACE = __NAMESPACE__;
    }




}