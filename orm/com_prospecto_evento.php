<?php

namespace gamboamartin\comercial\models;

use base\orm\_modelo_parent;
use base\orm\modelo;
use gamboamartin\errores\errores;
use gamboamartin\gastos\models\_base_auto_soli;
use PDO;
use stdClass;

class com_prospecto_evento extends _modelo_parent
{
    public function __construct(PDO $link)
    {
        $tabla = 'com_prospecto_evento';
        $columnas = array($tabla => false, 'com_prospecto' => $tabla, 'adm_evento' => $tabla);
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
        if (isset($registros['status'])) {
            return $registros;
        }

        $keys = array('com_prospecto_id', 'adm_evento_id');
        $valida = $this->validacion->valida_ids(keys: $keys, registro: $registros);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error validar campos', data: $valida);
        }

        $registros['codigo'] = $this->get_codigo_aleatorio();
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error generar codigo', data: $registros);
        }

        if (!isset($registros['descripcion'])) {
            $registros['descripcion'] = $registros['codigo'];
            $registros['descripcion'] .= $registros['com_prospecto_id'] . $registros['adm_evento_id'];

        }

        return $registros;
    }

}