<?php
namespace gamboamartin\comercial\models;
use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class com_rel_prospecto_cte extends _modelo_parent{
    public function __construct(PDO $link, array $childrens = array()){
        $tabla = 'com_rel_prospecto_cte';
        $columnas = array($tabla=>false,'com_cliente'=>$tabla,'com_prospecto'=>$tabla);
        $campos_obligatorios = array();
        $columnas_extra = array();


        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, childrens: $childrens);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Relacion Cliente Prospecto';


    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {

        $tiene_relacion =$this->tiene_relacion(registro: $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar si existe relacion',data:  $tiene_relacion);
        }
        if($tiene_relacion){
            return $this->error->error(mensaje: 'Error este registro ya tiene relacion',data:  $tiene_relacion);
        }

        if(!isset($this->registro['descripcion'])){
            $descripcion = 'Cliente '.$this->registro['com_cliente_id'].' Prospecto '.$this->registro['com_prospecto_id'];
            $this->registro['descripcion'] = $descripcion;
        }

        $r_alta_bd = parent::alta_bd();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al dar de alta relacion',data:  $r_alta_bd);
        }
        return $r_alta_bd;

    }

    final public function tiene_relacion(array $registro)
    {
        $tiene_prospecto = (new com_cliente(link: $this->link))->tiene_prospecto(
            com_cliente_id: $registro['com_prospecto_id']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar si existe cliente',data:  $tiene_prospecto);
        }
        $tiene_cliente = (new com_prospecto(link: $this->link))->tiene_cliente(
            com_prospecto_id: $registro['com_prospecto_id']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar si existe cliente',data:  $tiene_cliente);
        }
        $data = new stdClass();
        $data->tiene_prospecto = $tiene_prospecto;
        $data->tiene_cliente = $tiene_cliente;
        if($tiene_prospecto && !$tiene_cliente){
            return $this->error->error(mensaje: 'Error de integridad en relacion',data:  $data);
        }
        if(!$tiene_prospecto && $tiene_cliente){
            return $this->error->error(mensaje: 'Error de integridad en relacion',data:  $data);
        }

        $tiene_relacion = false;
        if($tiene_cliente || $tiene_prospecto){
            $tiene_relacion = true;
        }

        return $tiene_relacion;

    }

}