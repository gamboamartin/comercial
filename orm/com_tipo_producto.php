<?php
namespace gamboamartin\comercial\models;
use base\orm\_modelo_parent;
use gamboamartin\cat_sat\models\cat_sat_moneda;
use PDO;

class com_tipo_producto extends _modelo_parent{
    public function __construct(PDO $link){
        $tabla = 'com_tipo_producto';
        $columnas = array($tabla=>false);
        $campos_obligatorios = array();

        $campos_view['codigo'] = array('type' => 'inputs');
        $campos_view['descripcion'] = array('type' => 'inputs');

        $columnas_extra['com_tipo_producto_n_productos'] = /** @lang sql */
            "(SELECT COUNT(*) FROM com_producto WHERE com_producto.com_tipo_producto_id = com_tipo_producto.id)";

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, campos_view: $campos_view, columnas_extra: $columnas_extra);

        $this->NAMESPACE = __NAMESPACE__;
    }
}