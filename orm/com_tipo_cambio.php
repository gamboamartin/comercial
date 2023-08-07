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

        $alta_moneda = $this->tipo_cambio_hoy_ins(cat_sat_moneda_codigo: 'MXN');
        if(errores::$error){
            $error = $this->error->error(mensaje: 'Error al insertar tipo de cambio', data: $alta_moneda);
            print_r($error);
            exit;
        }

        $alta_moneda = $this->tipo_cambio_hoy_ins(cat_sat_moneda_codigo: 'XXX');
        if(errores::$error){
            $error = $this->error->error(mensaje: 'Error al insertar tipo de cambio', data: $alta_moneda);
            print_r($error);
            exit;
        }

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

    private function alta_tipo_cambio_default(string $cat_sat_moneda_codigo, string $hoy){
        $com_tipo_cambio_ins = $this->genera_com_tipo_cambio_ins(cat_sat_moneda_codigo: $cat_sat_moneda_codigo,hoy:  $hoy);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener tipo de cambio', data: $com_tipo_cambio_ins);
        }

        $alta_moneda = $this->alta_registro(registro: $com_tipo_cambio_ins);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar tipo de cambio', data: $alta_moneda);
        }
        return $alta_moneda;
    }

    private function com_tipo_cambio_ins(array $cat_sat_moneda, string $hoy): array
    {
        $com_tipo_cambio_ins['fecha'] = $hoy;
        $com_tipo_cambio_ins['cat_sat_moneda_id'] = $cat_sat_moneda['cat_sat_moneda_id'];
        $com_tipo_cambio_ins['monto'] = 1;
        return $com_tipo_cambio_ins;
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

    /**
     * Valida si existe la moneda default
     * @param string $cat_sat_moneda_codigo Codigo de moneda
     * @return array|bool
     *
     */
    private function existe_default_hoy(string $cat_sat_moneda_codigo): bool|array
    {
        $hoy = date('Y-m-d');

        $filtro['com_tipo_cambio.fecha'] = $hoy;
        $filtro['cat_sat_moneda.codigo'] = $cat_sat_moneda_codigo;

        $existe = $this->existe(filtro: $filtro);
        if(errores::$error){
           return $this->error->error(mensaje: 'Error al validar si existe', data: $existe);
        }
        return $existe;
    }

    private function existe_moneda(string $cat_sat_moneda_codigo){
        $filtro = array();
        $filtro['cat_sat_moneda.codigo'] = $cat_sat_moneda_codigo;

        $existe = (new cat_sat_moneda(link: $this->link))->existe(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar moneda', data: $existe);
        }
        return $existe;
    }

    private function genera_com_tipo_cambio_ins(string $cat_sat_moneda_codigo, string $hoy){
        $cat_sat_moneda = (new cat_sat_moneda(link: $this->link))->registro_by_codigo(codigo: $cat_sat_moneda_codigo);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener moneda', data: $cat_sat_moneda);
        }

        $com_tipo_cambio_ins = $this->com_tipo_cambio_ins(cat_sat_moneda: $cat_sat_moneda,hoy:  $hoy);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener tipo de cambio', data: $com_tipo_cambio_ins);
        }
        return $com_tipo_cambio_ins;
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

    private function inserta_tipo_cambio(string $cat_sat_moneda_codigo, string $hoy){
        $alta_moneda = new stdClass();
        $existe = $this->existe_moneda(cat_sat_moneda_codigo: $cat_sat_moneda_codigo);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar moneda', data: $existe);
        }
        if($existe){
            $alta_moneda = $this->alta_tipo_cambio_default(cat_sat_moneda_codigo: $cat_sat_moneda_codigo,hoy:  $hoy);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al insertar tipo de cambio', data: $alta_moneda);

            }
        }
        return $alta_moneda;
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

    /**
     * Obtiene el tipo de cambio de una moneda por dia
     * @param int $cat_sat_moneda_id Moneda a verificar tipo de cambio
     * @param string $fecha Fecha de tipo de cambio
     * @return array
     * @version 2.12.1
     */
    final public function tipo_cambio(int $cat_sat_moneda_id, string $fecha): array
    {
        if($cat_sat_moneda_id <= 0){
            return $this->error->error(mensaje: 'Error cat_sat_moneda_id debe ser mayor a 0',data:  $cat_sat_moneda_id);
        }

        $fecha = trim($fecha);
        if($fecha === ''){
            return $this->error->error(mensaje: 'Error fecha esta vacia',data:  $fecha);
        }
        $cat_sat_moneda = (new cat_sat_moneda(link: $this->link))->registro(registro_id:$cat_sat_moneda_id );
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener moneda',data:  $cat_sat_moneda);
        }

        $filtro_tc['com_tipo_cambio.fecha'] = $fecha;
        $filtro_tc['cat_sat_moneda.codigo'] = $cat_sat_moneda['cat_sat_moneda_codigo'];

        $r_com_tipo_cambio = $this->filtro_and(filtro: $filtro_tc);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener tipo de cambio',data:  $r_com_tipo_cambio);
        }

        if($r_com_tipo_cambio->n_registros > 1){
            return $this->error->error(mensaje: 'Error existe mas de un tipo de cambio',data:  $r_com_tipo_cambio);
        }
        if($r_com_tipo_cambio->n_registros === 0){
            return $this->error->error(mensaje: 'Error no existe tipo de cambio',data:  $r_com_tipo_cambio);
        }

        return $r_com_tipo_cambio->registros[0];
    }

    private function tipo_cambio_hoy_ins(string $cat_sat_moneda_codigo){
        $alta_moneda = new stdClass();
        $hoy = date('Y-m-d');

        $existe = $this->existe_default_hoy(cat_sat_moneda_codigo: $cat_sat_moneda_codigo);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar si existe', data: $existe);
        }

        if(!$existe){
            $alta_moneda = $this->inserta_tipo_cambio(cat_sat_moneda_codigo: $cat_sat_moneda_codigo,hoy:  $hoy);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al insertar tipo de cambio', data: $alta_moneda);
            }
        }

        return $alta_moneda;
    }
}