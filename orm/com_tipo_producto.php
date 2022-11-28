<?php
namespace gamboamartin\comercial\models;
use base\orm\_modelo_parent;
use gamboamartin\cat_sat\models\cat_sat_moneda;
use PDO;

class com_tipo_producto extends _modelo_parent{
    public function __construct(PDO $link){
        $tabla = 'com_tipo_producto';
        $columnas = array($tabla=>false,'cat_sat_moneda'=>$tabla,'dp_pais'=>'cat_sat_moneda');
        $campos_obligatorios = array('cat_sat_moneda_id');

        $campos_view['cat_sat_moneda_id'] = array('type' => 'selects', 'model' => new cat_sat_moneda($link));
        $campos_view['codigo'] = array('type' => 'inputs');
        $campos_view['descripcion'] = array('type' => 'inputs');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, campos_view: $campos_view);

        $this->NAMESPACE = __NAMESPACE__;
    }
}