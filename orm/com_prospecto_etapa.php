<?php
namespace gamboamartin\comercial\models;
use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use gamboamartin\proceso\models\pr_etapa_proceso;
use PDO;

class com_prospecto_etapa extends _modelo_parent{
    public function __construct(PDO $link){
        $tabla = 'com_prospecto_etapa';
        $columnas = array($tabla=>false);
        $campos_obligatorios = array();
        $childrens =array();

        $columnas_extra = array();

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, childrens: $childrens);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Etapas de prospecto';


    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|\stdClass
    {
        $r_alta = parent::alta_bd();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al dar de alta prospecto etapa',data:  $r_alta);
        }

        $pr_etapa_proceso = (new pr_etapa_proceso(link: $this->link))->registro(
            registro_id: $this->registro['pr_etapa_proceso_id'], retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener pr_etapa_proceso',data:  $pr_etapa_proceso);
        }
        $com_prospecto = (new com_prospecto(link: $this->link))->registro(registro_id: $this->registro['com_prospecto_id'],
            retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener com_prospecto',data:  $com_prospecto);
        }
        $row_entidad_base['etapa'] = $pr_etapa_proceso->pr_etapa_descripcion;
        $upd = (new com_prospecto(link: $this->link))->modifica_bd(registro: $row_entidad_base,
            id:  $this->registro['com_prospecto_id']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al actualizar com_prospecto',data:  $upd);
        }

        return $r_alta;

    }

}