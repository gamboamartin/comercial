<?php

namespace gamboamartin\comercial\models;

use base\orm\_modelo_parent;
use base\orm\modelo;
use gamboamartin\errores\errores;
use gamboamartin\gastos\models\_base_auto_soli;
use PDO;
use stdClass;

class com_direccion_prospecto extends _modelo_parent
{
    public function __construct(PDO $link)
    {
        $tabla = 'com_direccion_prospecto';
        $columnas = array($tabla => false, 'com_direccion' => $tabla, 'com_prospecto' => $tabla, 'com_tipo_direccion' => 'com_direccion',
            'dp_calle_pertenece' => 'com_direccion', 'dp_colonia_postal' => 'dp_calle_pertenece',
            'dp_cp' => 'dp_colonia_postal', 'dp_municipio' => 'dp_cp', 'dp_estado' => 'dp_municipio',
            'dp_pais' => 'dp_estado');
        $campos_obligatorios = array();

        $no_duplicados = array();

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, no_duplicados: $no_duplicados);

        $this->NAMESPACE = __NAMESPACE__;
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $this->registro = $this->inicializa_campos($this->registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al inicializar campo base', data: $this->registro);
        }

        $r_alta_bd = parent::alta_bd($keys_integra_ds);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar autorizante', data: $r_alta_bd);
        }
        return $r_alta_bd;
    }

    protected function inicializa_campos(array $registros): array
    {
        $keys = array('com_direccion_id', 'com_prospecto_id');
        $valida = $this->validacion->valida_ids(keys: $keys, registro: $registros);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error validar campos', data: $valida);
        }

        $registros['codigo'] = $this->get_codigo_aleatorio();
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error generar codigo', data: $registros);
        }

        $registros['codigo'] .= $registros['com_direccion_id'] . $registros['com_prospecto_id'];
        $registros['descripcion'] = $registros['codigo'];

        return $registros;
    }

    public function elimina_bd(int $id): array|stdClass
    {
        $registro = $this->registro(registro_id: $id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error obtener registro', data: $registro);
        }

        $elimina = parent::elimina_bd($id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error eliminar relación', data: $elimina);
        }

        $elimina_direccion = (new com_direccion($this->link))->elimina_bd(id: $registro['com_direccion_id']);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error eliminar direccion', data: $elimina_direccion);
        }

        return $elimina;
    }

}