<?php
namespace gamboamartin\comercial\models;
use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class com_email_cte extends _modelo_parent{

    public function __construct(PDO $link, array $childrens = array()){
        $tabla = 'com_email_cte';
        $columnas = array($tabla=>false,'com_cliente'=>$tabla);
        $campos_obligatorios = array();



        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, childrens: $childrens);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Emails';

    }



}