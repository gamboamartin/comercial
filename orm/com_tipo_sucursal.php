<?php
namespace gamboamartin\comercial\models;
use base\orm\_defaults;
use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;

class com_tipo_sucursal extends _modelo_parent{

    public function __construct(PDO $link, array $childrens = array()){
        $tabla = 'com_tipo_sucursal';
        $columnas = array($tabla=>false);
        $campos_obligatorios = array();
        $childrens['com_sucursal'] ="gamboamartin\comercial\models";

        $columnas_extra['com_tipo_sucursal_n_sucursales'] =
            "(SELECT COUNT(*) FROM com_sucursal WHERE com_sucursal.com_tipo_sucursal_id = com_tipo_sucursal.id)";

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, childrens: $childrens);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Tipo de sucursal';



    }

    public function sucursales(int $com_tipo_sucursal_id): array
    {
        if($com_tipo_sucursal_id <= 0){
            return $this->error->error(mensaje: 'Error com_tipo_sucursal_id debe ser mayor a 0',data:  $com_tipo_sucursal_id);
        }

        $filtro['com_tipo_sucursal.id'] = $com_tipo_sucursal_id;

        $data = (new com_sucursal($this->link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener sucursales',data:  $data);
        }
        return $data->registros;
    }

}