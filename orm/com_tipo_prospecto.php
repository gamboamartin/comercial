<?php
namespace gamboamartin\comercial\models;
use base\orm\_modelo_parent;
use base\orm\_modelo_parent_sin_codigo;
use gamboamartin\errores\errores;
use PDO;

class com_tipo_prospecto extends _modelo_parent_sin_codigo{
    public function __construct(PDO $link, array $childrens = array()){
        $tabla = 'com_tipo_prospecto';
        $columnas = array($tabla=>false);
        $campos_obligatorios = array();
        $childrens['com_prospecto'] ="gamboamartin\comercial\models";

        $columnas_extra['com_tipo_prospecto_n_prospectos'] =
            "(SELECT COUNT(*) FROM com_prospecto WHERE com_prospecto.com_tipo_prospecto_id = com_tipo_prospecto.id)";

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, childrens: $childrens);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Tipo de Prospecto';


    }

    public function prospectos(int $com_tipo_prospecto_id): array
    {
        if($com_tipo_prospecto_id <= 0){
            return $this->error->error(mensaje: 'Error com_tipo_prospecto_id debe ser mayor a 0',data:  $com_tipo_prospecto_id);
        }

        $filtro['com_tipo_prospecto.id'] = $com_tipo_prospecto_id;

        $data = (new com_prospecto($this->link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener clientes',data:  $data);
        }
        return $data->registros;
    }
}