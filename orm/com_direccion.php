<?php
namespace gamboamartin\comercial\models;
use base\orm\_modelo_parent;
use gamboamartin\administrador\models\adm_usuario;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class com_direccion extends _modelo_parent{
    public function __construct(PDO $link, array $childrens = array()){
        $tabla = 'com_direccion';
        $columnas = array($tabla=>false, 'com_tipo_direccion'=>$tabla, 'dp_municipio'=>$tabla,
            'dp_estado' =>'dp_municipio','dp_pais'=>'dp_estado');

        $campos_obligatorios = array('dp_municipio_id','cp','colonia','calle','com_tipo_direccion_id');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, childrens: $childrens);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Agentes';
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $this->registro = $this->inicializa_campos(registros: $this->registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al inicializar campo base', data: $this->registro);
        }

        if(!isset($this->registro['dp_calle_pertenece_id'])){
            $this->registro['dp_calle_pertenece_id'] = 1;
        }

        $com_prospecto_id = -1;
        if(isset($this->registro['com_prospecto_id'])){
            $com_prospecto_id = (int)$this->registro['com_prospecto_id'];
            unset($this->registro['com_prospecto_id']);
        }

        $r_alta_bd = parent::alta_bd(keys_integra_ds: $keys_integra_ds);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar direccion', data: $r_alta_bd);
        }

        if($com_prospecto_id > 0){
            $com_direccion_prospecto_ins['com_prospecto_id'] = $com_prospecto_id;
            $com_direccion_prospecto_ins['com_direccion_id'] = $r_alta_bd->registro_id;
            $r_alta_com_direccion_prospecto = (new com_direccion_prospecto(link: $this->link))->alta_registro(registro: $com_direccion_prospecto_ins);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al insertar com_direccion_prospecto', data: $r_alta_com_direccion_prospecto);
            }
        }

        return $r_alta_bd;
    }

    protected function inicializa_campos(array $registros): array
    {
        if (!isset($registros['codigo'])){
            $registros['codigo'] = $this->get_codigo_aleatorio();
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error generar codigo', data: $registros);
            }
        }

        $registros['descripcion'] = $registros['codigo'];

        return $registros;
    }

}