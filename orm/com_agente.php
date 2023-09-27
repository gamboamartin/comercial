<?php
namespace gamboamartin\comercial\models;
use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;

class com_agente extends _modelo_parent{
    public function __construct(PDO $link, array $childrens = array()){
        $tabla = 'com_agente';
        $columnas = array($tabla=>false);
        $campos_obligatorios = array();
        $childrens['com_prospecto'] ="gamboamartin\comercial\models";

        $columnas_extra['com_agente_n_prospectos'] =
            "(SELECT COUNT(*) FROM com_prospecto WHERE com_prospecto.com_agente_id = com_agente.id)";

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, childrens: $childrens);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Agentes';


    }

    public function prospectos(int $com_agente_id): array
    {
        if($com_agente_id <= 0){
            return $this->error->error(mensaje: 'Error com_agente_id debe ser mayor a 0',data:  $com_agente_id);
        }

        $filtro['com_agente.id'] = $com_agente_id;

        $data = (new com_prospecto($this->link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener clientes',data:  $data);
        }
        return $data->registros;
    }
}