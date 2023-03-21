<?php
namespace gamboamartin\comercial\models;
use base\orm\_defaults;
use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;

class com_tipo_cliente extends _modelo_parent{
    public function __construct(PDO $link, array $childrens = array()){
        $tabla = 'com_tipo_cliente';
        $columnas = array($tabla=>false);
        $campos_obligatorios = array();
        $childrens['com_cliente'] ="gamboamartin\comercial\models";

        $columnas_extra['com_tipo_cliente_n_clientes'] =
            "(SELECT COUNT(*) FROM com_cliente WHERE com_cliente.com_tipo_cliente_id = com_tipo_cliente.id)";

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, childrens: $childrens);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Tipo de cliente';




    }

    public function clientes(int $com_tipo_cliente_id): array
    {
        if($com_tipo_cliente_id <= 0){
            return $this->error->error(mensaje: 'Error com_tipo_cliente_id debe ser mayor a 0',data:  $com_tipo_cliente_id);
        }

        $filtro['com_tipo_cliente.id'] = $com_tipo_cliente_id;

        $data = (new com_cliente($this->link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener clientes',data:  $data);
        }
        return $data->registros;
    }
}