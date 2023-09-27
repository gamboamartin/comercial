<?php
namespace gamboamartin\comercial\models;
use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;

class com_tipo_agente extends _modelo_parent{
    public function __construct(PDO $link, array $childrens = array()){
        $tabla = 'com_tipo_agente';
        $columnas = array($tabla=>false);
        $campos_obligatorios = array();
        $childrens['com_agente'] ="gamboamartin\comercial\models";

        $columnas_extra['com_tipo_agente_n_agentes'] =
            "(SELECT COUNT(*) FROM com_agente WHERE com_agente.com_tipo_agente_id = com_tipo_agente.id)";

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, childrens: $childrens);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Tipo de agente';


    }

    public function agentes(int $com_tipo_agente_id): array
    {
        if($com_tipo_agente_id <= 0){
            return $this->error->error(mensaje: 'Error com_tipo_agente_id debe ser mayor a 0',data:  $com_tipo_agente_id);
        }

        $filtro['com_tipo_agente.id'] = $com_tipo_agente_id;

        $data = (new com_agente($this->link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener clientes',data:  $data);
        }
        return $data->registros;
    }
}