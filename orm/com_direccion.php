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
        $columnas = array($tabla=>false, 'com_tipo_direccion'=>$tabla, 'dp_calle_pertenece'=>$tabla,
            'dp_colonia_postal' =>'dp_calle_pertenece', 'dp_cp' =>'dp_colonia_postal', 'dp_municipio' =>'dp_cp',
            'dp_estado' =>'dp_municipio', 'dp_pais' =>'dp_estado');

        $campos_obligatorios = array('dp_calle_pertenece_id');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, childrens: $childrens);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Agentes';
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $this->registro = $this->inicializa_campos($this->registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al inicializar campo base', data: $this->registro);
        }

        $r_alta_bd = parent::alta_bd($keys_integra_ds);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar direccion', data: $r_alta_bd);
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