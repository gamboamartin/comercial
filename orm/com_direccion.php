<?php
namespace gamboamartin\comercial\models;
use base\orm\_modelo_parent;
use gamboamartin\administrador\models\adm_usuario;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class com_direccion extends _modelo_parent{
    public function __construct(PDO $link, array $childrens = array()){
        $tabla = 'com_direccion';
        $columnas = array($tabla=>false,'dp_calle_pertenece'=>$tabla);



        $campos_obligatorios = array('dp_calle_pertenece');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, childrens: $childrens);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Agentes';
    }

}