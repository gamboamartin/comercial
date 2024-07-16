<?php

namespace gamboamartin\comercial\models;

use base\orm\_modelo_parent_sin_codigo;
use PDO;

class com_contacto_user extends _modelo_parent_sin_codigo
{

    public function __construct(PDO $link, array $childrens = array())
    {
        $tabla = 'com_contacto_user';
        $columnas = array($tabla => false, 'com_contacto' => $tabla, 'adm_usuario' => $tabla,
            'com_cliente'=>'com_contacto','adm_grupo'=>'adm_usuario');
        $campos_obligatorios = array('com_contacto_id','adm_usuario_id');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, childrens: $childrens);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Usuarios de cliente';
    }



}