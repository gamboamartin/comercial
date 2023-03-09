<?php

namespace gamboamartin\comercial\models;

use base\orm\_defaults;
use base\orm\_modelo_parent;
use base\orm\modelo;
use gamboamartin\cat_sat\models\cat_sat_moneda;
use gamboamartin\direccion_postal\models\dp_pais;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class com_tipo_cambio extends _modelo_parent
{
    public function __construct(PDO $link)
    {
        $tabla = 'com_tipo_cambio';
        $columnas = array($tabla => false, 'cat_sat_moneda' => $tabla, 'dp_pais' => 'cat_sat_moneda');
        $campos_obligatorios = array('cat_sat_moneda_id', 'monto', 'fecha');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Tipo de Cambio';

        /*
        if(!isset($_SESSION['init'][$tabla])) {
            $codigo = 'MXN';
            if(isset($_SESSION['init']['cat_sat_moneda'])){
                unset($_SESSION['init']['cat_sat_moneda']);
            }

            $cat_sat_moneda = (new cat_sat_moneda(link: $this->link))->registro_by_codigo(codigo: $codigo);
            if (errores::$error) {
                $error = $this->error->error(mensaje: 'Error al obtener cat_sat_moneda', data: $cat_sat_moneda);
                print_r($error);
                exit;
            }

            $catalago = array();
            $catalago[] = array('codigo'=>'MXN '.date('Y-m-d'),'cat_sat_moneda_id' => $cat_sat_moneda['cat_sat_moneda_id'],
                'fecha'=>date('Y-m-d'),'monto'=>1);

            $r_alta_bd = (new _defaults())->alta_defaults(catalago: $catalago, entidad: $this);
            if (errores::$error) {
                $error = $this->error->error(mensaje: 'Error al insertar', data: $r_alta_bd);
                print_r($error);
                exit;
            }
            $_SESSION['init'][$tabla] = true;
        }
        */

    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $this->registro = $this->init_data(data: $this->registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener data_upd', data: $this->registro);
        }

        $campos_limpiar[] = "dp_pais_id";
        $this->registro = $this->limpia_campos_extras(registro: $this->registro, campos_limpiar: $campos_limpiar);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al limpiar campos', data: $this->registro);
        }

        $r_alta_bd = parent::alta_bd($keys_integra_ds);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al dar de alta tipo cambio', data: $r_alta_bd);
        }

        return $r_alta_bd;
    }


    private function data_upd(int $com_tipo_cambio_id, array $data): array
    {
        $registro_previo = $this->registro(registro_id: $com_tipo_cambio_id, retorno_obj: true);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener com_tipo_cambio', data: $registro_previo);
        }
        if (!isset($data['cat_sat_moneda_id'])) {

            $data['cat_sat_moneda_id'] = $registro_previo->cat_sat_moneda_id;
        }

        if (!isset($data['fecha'])) {

            $data['fecha'] = $registro_previo->com_tipo_cambio_fecha;
        }
        return $data;
    }

    private function descripcion(array $cat_sat_moneda, array $data): string
    {
        $descripcion = $cat_sat_moneda['dp_pais_codigo'] . ' ';
        $descripcion .= $cat_sat_moneda['cat_sat_moneda_codigo'] . ' ';
        $descripcion .= $data['fecha'];
        return trim($descripcion);
    }

    private function genera_descripcion_base(array $data): array|string
    {
        $cat_sat_moneda = (new cat_sat_moneda(link: $this->link))->registro(registro_id: $data['cat_sat_moneda_id']);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener moneda', data: $cat_sat_moneda);
        }

        $descripcion = $this->descripcion(cat_sat_moneda: $cat_sat_moneda, data: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener descripcion', data: $descripcion);
        }
        return $descripcion;
    }

    private function init_data(array $data, int $com_tipo_cambio_id = -1): array
    {
        if ($com_tipo_cambio_id > 0) {
            $data = $this->data_upd(com_tipo_cambio_id: $com_tipo_cambio_id, data: $data);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al obtener data_upd', data: $data);
            }
        }
        if (!isset($data['descripcion'])) {
            $descripcion = $this->genera_descripcion_base(data: $data);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al obtener descripcion', data: $descripcion);
            }
            $data['descripcion'] = $descripcion;
        }
        return $data;
    }

    public function modifica_bd(array $registro, int $id, bool $reactiva = false,
                                array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $registro = $this->init_data(data: $registro, com_tipo_cambio_id: $id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener data_upd', data: $registro);
        }

        $campos_limpiar[] = "dp_pais_id";
        $registro = $this->limpia_campos_extras(registro: $registro, campos_limpiar: $campos_limpiar);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al limpiar campos', data: $registro);
        }

        $r_modifica_bd = parent::modifica_bd($registro, $id, $reactiva, $keys_integra_ds);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al dar modificar tipo cambio', data: $r_modifica_bd);
        }

        return $r_modifica_bd;
    }
}