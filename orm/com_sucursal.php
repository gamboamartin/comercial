<?php

namespace gamboamartin\comercial\models;

use base\orm\modelo;
use gamboamartin\direccion_postal\models\dp_calle_pertenece;
use gamboamartin\direccion_postal\models\dp_colonia_postal;
use gamboamartin\direccion_postal\models\dp_pais;
use gamboamartin\direccion_postal\models\dp_cp;
use gamboamartin\direccion_postal\models\dp_municipio;
use gamboamartin\direccion_postal\models\dp_estado;

use gamboamartin\errores\errores;
use PDO;
use stdClass;

class com_sucursal extends modelo
{

    public function __construct(PDO $link)
    {
        $tabla = 'com_sucursal';
        $columnas = array($tabla => false, 'com_cliente' => $tabla, 'dp_calle_pertenece' => $tabla,
            'dp_colonia_postal' => 'dp_calle_pertenece', 'dp_cp' => 'dp_colonia_postal',
            'cat_sat_regimen_fiscal' => 'com_cliente', 'dp_municipio' => 'dp_cp', 'dp_estado' => 'dp_municipio',
            'com_tipo_sucursal' => $tabla, 'com_tipo_cliente'=>'com_cliente');

        $campos_obligatorios = array('descripcion', 'codigo', 'descripcion_select', 'alias', 'codigo_bis',
            'numero_exterior', 'com_cliente_id', 'dp_calle_pertenece_id','com_tipo_sucursal_id');

        $tipo_campos = array();

        $campos_view['dp_pais_id'] = array('type' => 'selects', 'model' => new dp_pais($link));
        $campos_view['dp_estado_id'] = array('type' => 'selects', 'model' => new dp_estado($link));
        $campos_view['dp_municipio_id'] = array('type' => 'selects', 'model' => new dp_municipio($link));
        $campos_view['dp_cp_id'] = array('type' => 'selects', 'model' => new dp_cp($link));
        $campos_view['dp_colonia_postal_id'] = array('type' => 'selects', 'model' => new dp_colonia_postal($link));
        $campos_view['dp_calle_pertenece_id'] = array('type' => 'selects', 'model' => new dp_calle_pertenece($link));
        $campos_view['com_cliente_id'] = array('type' => 'selects', 'model' => new com_cliente($link));
        $campos_view['com_tipo_sucursal_id'] = array('type' => 'selects', 'model' => new com_tipo_sucursal($link));
        $campos_view['codigo'] = array('type' => 'inputs');
        $campos_view['nombre_contacto'] = array('type' => 'inputs');
        $campos_view['numero_exterior'] = array('type' => 'inputs');
        $campos_view['numero_interior'] = array('type' => 'inputs');
        $campos_view['telefono_1'] = array('type' => 'inputs');
        $campos_view['telefono_2'] = array('type' => 'inputs');
        $campos_view['telefono_3'] = array('type' => 'inputs');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, campos_view: $campos_view, tipo_campos: $tipo_campos);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Sucursal';

    }

    /**
     * Asigna alias a data para update y alta
     * @param array $data Registro en proceso
     * @return array
     * @version 17.5.0
     *
     */
    private function alias(array $data): array
    {
        $keys = array('codigo');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $data);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar data', data: $valida);
        }
        if (!isset($data['alias'])) {
            $data['alias'] = $data['codigo'];
        }
        return $data;
    }

    /**
     * Inserta una sucursal
     * @return array|stdClass
     * @version 17.17.0
     */
    public function alta_bd(): array|stdClass
    {
        $keys = array('com_cliente_id', 'dp_calle_pertenece_id');
        $valida = $this->validacion->valida_ids(keys: $keys, registro: $this->registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar registro', data: $valida);
        }

        $valida = $this->valida_base_sucursal(registro: $this->registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar datos', data: $valida);
        }

        $this->registro = $this->init_base(data: $this->registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al inicializar campo base', data: $this->registro);
        }

        $this->registro = $this->limpia_campos(registro: $this->registro, campos_limpiar: array('dp_pais_id',
            'dp_estado_id', 'dp_municipio_id', 'dp_cp_id', 'dp_cp_id', 'dp_colonia_postal_id'));
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al limpiar campos', data: $this->registro);
        }

        $ins_pred = (new com_tipo_sucursal(link: $this->link))->inserta_predeterminado(codigo: 'MAT',
            descripcion: 'MATRIZ');
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar predeterminado', data: $ins_pred);
        }


        $r_alta_bd = parent::alta_bd();
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar sucursal', data: $r_alta_bd);
        }
        return $r_alta_bd;
    }

    /**
     * Integra un codigo bis si no existe
     * @param string $com_cliente_rfc RFC del cliente
     * @param array $data Datos de sucursal
     * @return array
     * @version 17.17.0
     */
    private function codigo_bis(string $com_cliente_rfc, array $data): array
    {
        $keys = array('codigo');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $data);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar data',data:  $valida);
        }
        $com_cliente_rfc = trim($com_cliente_rfc);
        if($com_cliente_rfc === ''){
            return $this->error->error(mensaje: 'Error com_cliente_rfc esta vacio',data:  $com_cliente_rfc);
        }

        if (!isset($data['codigo_bis'])) {
            $data['codigo_bis'] = $data['codigo'].$com_cliente_rfc;
        }
        return $data;
    }

    /**
     * Integra una descripcion
     * @param string $com_cliente_razon_social Razon social del cliente
     * @param string $com_cliente_rfc Rfc del cliente
     * @param array $data Datos previos de carga
     * @return array|bool
     * @version 17.17.0
     */
    private function descripcion(string $com_cliente_razon_social, string $com_cliente_rfc, array $data): array|bool
    {

        $valida = $this->valida_data_descripciones(com_cliente_razon_social: $com_cliente_razon_social,
            com_cliente_rfc: $com_cliente_rfc, data: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar datos', data: $valida);
        }

        if (!isset($data['descripcion'])) {

            $ds = $this->ds(com_cliente_razon_social: $com_cliente_razon_social, com_cliente_rfc: $com_cliente_rfc,
                data: $data);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al obtener descripcion', data: $ds);
            }
            $data['descripcion'] = $ds;
        }
        return $data;
    }

    /**
     * Integra una descripcion select
     * @param string $com_cliente_razon_social Razon social del cliente
     * @param string $com_cliente_rfc Rfc del cliente
     * @param array $data Datos de sucursal
     * @return array
     * @version 17.17.0
     */
    private function descripcion_select_sc(string $com_cliente_razon_social, string $com_cliente_rfc,
                                           array $data): array
    {
        $keys = array('codigo');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar codigo', data: $valida);
        }
        $valida = $this->valida_data_descripciones(com_cliente_razon_social: $com_cliente_razon_social,
            com_cliente_rfc: $com_cliente_rfc, data: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar datos', data: $valida);
        }

        if (!isset($data['descripcion_select'])) {

            $ds = $this->ds(com_cliente_razon_social: $com_cliente_razon_social, com_cliente_rfc: $com_cliente_rfc,
                data: $data);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al obtener descripcion', data: $ds);
            }

            $data['descripcion_select'] = $ds;
        }
        return $data;
    }

    /**
     * Obtiene la descripcion select de un registro transaccionado
     * @param string $com_cliente_razon_social Razon social del cliente
     * @param string $com_cliente_rfc Rfc del cliente
     * @param array $data Datos previos cargados
     * @return string|array
     * @version 17.3.0
     */
    final public function ds(string $com_cliente_razon_social, string $com_cliente_rfc, array $data): string|array
    {
        $valida = $this->valida_data_descripciones(com_cliente_razon_social: $com_cliente_razon_social,
            com_cliente_rfc: $com_cliente_rfc, data: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar datos', data: $valida);
        }

        $ds = $data['codigo'];
        $ds .= ' '.$com_cliente_rfc;
        $ds .= ' '.$com_cliente_razon_social;
        $ds = trim($ds);
        return trim($ds);
    }

    /**
     * Inicializa los datos para una transaccion de sucursal
     * @param array $data Datos de sucursal
     * @return array
     * @version 17.17.0
     */
    private function init_base(array $data): array
    {

        $valida = $this->valida_base_sucursal(registro: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar datos', data: $valida);
        }

        $com_cliente =(new com_cliente(link: $this->link))->registro(registro_id: $data['com_cliente_id']);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener com_cliente', data: $com_cliente);
        }

        $com_cliente_rfc = $com_cliente['com_cliente_rfc'];
        $com_cliente_razon_social = $com_cliente['com_cliente_razon_social'];


        $data = $this->descripcion(com_cliente_razon_social: $com_cliente_razon_social,
            com_cliente_rfc: $com_cliente_rfc,data:  $data);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al asignar descripcion', data: $data);
        }

        $data = $this->codigo_bis(com_cliente_rfc: $com_cliente_rfc,data:  $data);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al asignar codigo_bis', data: $data);
        }

        $data = $this->descripcion_select_sc(com_cliente_razon_social: $com_cliente_razon_social,
            com_cliente_rfc: $com_cliente_rfc,data:  $data);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al asignar descripcion', data: $data);
        }

        $data = $this->alias(data:  $data);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al asignar alias', data: $data);
        }

        return $data;
    }

    /**
     * Limpia los campos de una sucursal
     * @param array $registro registro a limpiar campos
     * @param array $campos_limpiar Campos a limpiar
     * @return array
     * @version 17.17.0
     */
    private function limpia_campos(array $registro, array $campos_limpiar): array
    {
        foreach ($campos_limpiar as $valor) {
            if (isset($registro[$valor])) {
                unset($registro[$valor]);
            }
        }
        return $registro;
    }

    /*
     * REVISAR
     */

    public function em_empleado_by_sucursal(int $com_sucursal_id): array
    {

        $filtro['com_sucursal.id'] = $com_sucursal_id;
        $r_tg_em_empleado_sucursal = (new nom_rel_empleado_sucursal($this->link))->filtro_and(filtro: $filtro);
        if (errores::$error) {

            return $this->error->error(mensaje: 'Error al limpiar datos', data: $r_tg_em_empleado_sucursal);
        }
        return $r_tg_em_empleado_sucursal->registros;

    }

    /**
     * Maqueta un registro de tipo sucursal
     * @param string $codigo Codigo de cliente
     * @param string $nombre_contacto Nombre de contacto
     * @param int $com_cliente_id Id de cliente
     * @param string $telefono Telefono de cliente
     * @param int $dp_calle_pertenece_id calle
     * @param string $numero_exterior ext
     * @param string $numero_interior int
     * @param bool $es_empleado si es empleado da de alta empleado
     * @return array
     * @version 18.14.0
     */
    final public function maqueta_data(string $codigo, string $nombre_contacto, int $com_cliente_id, string $telefono,
                                 int    $dp_calle_pertenece_id, string $numero_exterior, string $numero_interior,
                                 bool $es_empleado = false): array
    {

        $com_tipo_sucursal= (new com_tipo_sucursal($this->link))->inserta_predeterminado(codigo: 'MAT',
            descripcion: 'MATRIZ');
        if (errores::$error) {
            return $this->error->error(mensaje: "Error al obtener el id predeterminado", data: $com_tipo_sucursal);
        }

        $com_tipo_sucursal_id = (new com_tipo_sucursal(link: $this->link))->id_predeterminado();
        if (errores::$error) {
            return $this->error->error(mensaje: "Error al obtener el id predeterminado", data: $com_tipo_sucursal);
        }

        if ($es_empleado){
            (new com_tipo_sucursal(link: $this->link))->modifica_bd(registro: array("es_empleado" => "activo"),
                id: $com_tipo_sucursal_id);
            if (errores::$error) {
                return $this->error->error(mensaje: "Error asignar es_empleado a tipo sucursal", data: $com_tipo_sucursal);
            }
        }

        $data['com_tipo_sucursal_id'] = $com_tipo_sucursal_id;
        $data['codigo'] = $codigo;
        $data['descripcion'] = $nombre_contacto;
        $data['nombre_contacto'] = $nombre_contacto;
        $data['com_cliente_id'] = $com_cliente_id;
        $data['dp_calle_pertenece_id'] = $dp_calle_pertenece_id;
        $data['numero_exterior'] = $numero_exterior;
        $data['numero_interior'] = $numero_interior;
        $data['telefono_1'] = $telefono;
        $data['telefono_2'] = $telefono;
        $data['telefono_3'] = $telefono;

        return $data;
    }

    /**
     * Modifica una sucursal
     * @param array $registro Registro en proceso
     * @param int $id Identificador de sucursal
     * @param bool $reactiva Si reactiva no valida transacciones de etapa
     * @return array|stdClass
     * @version 17.18.0
     */
    public function modifica_bd(array $registro, int $id, bool $reactiva = false): array|stdClass
    {

        if($id<=0){
            return $this->error->error(mensaje: 'Error id debe ser mayor a 0', data: $id);
        }

        $registro_previo = $this->registro(registro_id: $id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener registro previo', data: $registro_previo);
        }

        if(!isset($registro['com_cliente_id'])){
            $registro['com_cliente_id'] = $registro_previo['com_cliente_id'];
        }
        if(!isset($registro['codigo'])){
            $registro['codigo'] = $registro_previo['com_sucursal_codigo'];
        }

        $valida = $this->valida_base_sucursal(registro: $registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar registro', data: $valida);
        }

        $registro = $this->init_base(data: $registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al inicializar campo base', data: $registro);
        }

        $registro = $this->limpia_campos(registro: $registro, campos_limpiar: array('dp_pais_id', 'dp_estado_id',
            'dp_municipio_id', 'dp_cp_id', 'dp_colonia_postal_id'));
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al limpiar campos', data: $registro);
        }

        $r_modifica_bd = parent::modifica_bd(registro: $registro,id:  $id,reactiva:  $reactiva);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al modificar producto', data: $r_modifica_bd);
        }

        if($r_modifica_bd->registro_actualizado->com_tipo_sucursal_descripcion === 'MATRIZ'){
            $modifica_cliente = (new com_cliente(link: $this->link))->modifica_dp_calle_pertenece(
                dp_calle_pertenece_id: $r_modifica_bd->registro_actualizado->dp_calle_pertenece_id,
                id: $r_modifica_bd->registro_actualizado->com_cliente_id);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al modificar cliente ', data: $modifica_cliente);
            }
        }


        return $r_modifica_bd;
    }

    /**
     * Obtiene las sucursales de un cliente
     * @param int $com_cliente_id Cliente para obtencion de sucursales
     * @return array|stdClass
     * @version 17.12.0
     */
    final public function sucursales(int $com_cliente_id): array|stdClass
    {
        if ($com_cliente_id <= 0) {
            return $this->error->error(mensaje: 'Error $com_cliente_id debe ser mayor a 0', data: $com_cliente_id);
        }
        $filtro['com_cliente.id'] = $com_cliente_id;
        $r_com_sucursal = $this->filtro_and(filtro: $filtro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener sucursales', data: $r_com_sucursal);
        }
        return $r_com_sucursal;
    }

    final public function sucursales_by_tipo_cliente(int $com_tipo_cliente_id){
        $filtro['com_tipo_cliente.id'] = $com_tipo_cliente_id;
        $r_com_sucursal = $this->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener clientes',data:  $r_com_sucursal);
        }
        return $r_com_sucursal->registros;
    }

    /**
     * Valida los elementos base para actualizar inicializa una sucursal
     * @param array $registro Registro en proceso
     * @return array|true
     * @version 17.17.0
     */
    final public function valida_base_sucursal(array $registro): bool|array
    {
        $keys[] = 'com_cliente_id';

        $valida = $this->validacion->valida_ids(keys: $keys, registro: $registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar $data', data: $valida);
        }

        $keys[] = 'codigo';
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar codigo', data: $valida);
        }
        return true;
    }

    /**
     * Valida los elementos de una descripcion sean correctos
     * @param string $com_cliente_razon_social Razon social del cliente
     * @param string $com_cliente_rfc Rfc del cliente
     * @param array $data Datos de sucursal
     * @return array|true
     * @version 17.17.0
     */
    private function valida_data_descripciones(string $com_cliente_razon_social, string $com_cliente_rfc, array $data): bool|array
    {
        $keys = array('codigo');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar codigo', data: $valida);
        }
        $com_cliente_rfc = trim($com_cliente_rfc);
        if($com_cliente_rfc === ''){
            return $this->error->error(mensaje: 'Error com_cliente_rfc esta vacio', data: $com_cliente_rfc);
        }
        $com_cliente_razon_social = trim($com_cliente_razon_social);
        if($com_cliente_razon_social === ''){
            return $this->error->error(mensaje: 'Error com_cliente_razon_social esta vacio',
                data: $com_cliente_razon_social);
        }
        return true;
    }
}