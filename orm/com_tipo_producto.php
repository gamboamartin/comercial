<?php
namespace gamboamartin\comercial\models;
use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;

class com_tipo_producto extends _modelo_parent{
    public function __construct(PDO $link, array $childrens = array()){
        $tabla = 'com_tipo_producto';
        $columnas = array($tabla=>false);
        $campos_obligatorios = array();
        $childrens['com_producto'] ="gamboamartin\comercial\models";

        $columnas_extra['com_tipo_producto_n_productos'] =
            "(SELECT COUNT(*) FROM com_producto WHERE com_producto.com_tipo_producto_id = com_tipo_producto.id)";

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, childrens: $childrens);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Tipo de producto';

    }


    /**
     * Obtiene los productos de un tipo de producto
     * @param int $com_tipo_producto_id identificador Tipo de producto
     * @return array
     * @version
     */
    public function productos(int $com_tipo_producto_id): array
    {
        if($com_tipo_producto_id <= 0){
            return $this->error->error(mensaje: 'Error com_tipo_producto_id debe ser mayor a 0',data:  $com_tipo_producto_id);
        }

        $filtro['com_tipo_producto.id'] = $com_tipo_producto_id;

        $data = (new com_producto($this->link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener productos',data:  $data);
        }
        return $data->registros;
    }
}