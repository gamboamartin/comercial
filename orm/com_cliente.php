<?php

namespace gamboamartin\comercial\models;

use base\orm\_modelo_parent;
use gamboamartin\cat_sat\models\cat_sat_forma_pago;
use gamboamartin\cat_sat\models\cat_sat_metodo_pago;
use gamboamartin\cat_sat\models\cat_sat_moneda;
use gamboamartin\cat_sat\models\cat_sat_regimen_fiscal;
use gamboamartin\cat_sat\models\cat_sat_tipo_de_comprobante;
use gamboamartin\cat_sat\models\cat_sat_uso_cfdi;
use gamboamartin\direccion_postal\models\dp_calle_pertenece;
use gamboamartin\direccion_postal\models\dp_cp;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class com_cliente extends _modelo_parent
{
    public function __construct(PDO $link)
    {
        $tabla = 'com_cliente';

        $columnas = array($tabla => false, 'cat_sat_moneda' => $tabla, 'cat_sat_regimen_fiscal' => $tabla,
            'dp_calle_pertenece' => $tabla, 'dp_colonia_postal' => 'dp_calle_pertenece', 'dp_cp' => 'dp_colonia_postal',
            'dp_municipio' => 'dp_cp', 'dp_estado' => 'dp_municipio', 'dp_pais' => 'dp_estado', 'com_tipo_cliente' => $tabla,
            'cat_sat_uso_cfdi' => $tabla, 'cat_sat_metodo_pago' => $tabla, 'cat_sat_forma_pago' => $tabla,
            'cat_sat_tipo_de_comprobante' => $tabla);

        $campos_obligatorios = array('cat_sat_moneda_id', 'cat_sat_regimen_fiscal_id', 'cat_sat_moneda_id',
            'cat_sat_forma_pago_id', 'cat_sat_uso_cfdi_id', 'cat_sat_tipo_de_comprobante_id', 'cat_sat_metodo_pago_id',
            'telefono');

        $columnas_extra['com_cliente_n_sucursales'] =
            "(SELECT COUNT(*) FROM com_sucursal WHERE com_sucursal.com_cliente_id = com_cliente.id)";

        $tipo_campos = array();
        $tipo_campos['rfc'] = 'rfc';

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, tipo_campos: $tipo_campos);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Cliente';
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {

        $keys_tmp = array('dp_estado','dp_municipio','dp_cp','dp_colonia','dp_calle');
        $row_tmp = array();
        foreach ($keys_tmp as $key){
            if(isset($this->registro[$key])){
                $value = trim($this->registro[$key]);
                if($value !== ''){
                    $row_tmp[$key] = $value;
                }
                unset($this->registro[$key]);
            }
        }

        if(isset($this->registro['dp_cp_id']) && trim($this->registro['dp_cp_id']) !=='' && (int)$this->registro['dp_cp_id'] !== 11){
            $dp_cp = (new dp_cp(link: $this->link))->registro(registro_id:$this->registro['dp_cp_id'], retorno_obj: true);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al obtener dp_cp', data: $dp_cp);
            }

            $row_tmp['dp_cp_id'] = $dp_cp->dp_cp_id;
            $row_tmp['dp_cp'] = $dp_cp->dp_cp_descripcion;
        }


        $this->registro = $this->init_base(data: $this->registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al inicializar campo base', data: $this->registro);
        }


        $this->registro = $this->inicializa_foraneas(data: $this->registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al inicializar foraneas', data: $this->registro);
        }

        $keys = array('telefono','numero_exterior');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys, registro: $this->registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar registro', data: $valida);
        }

        if(!isset($this->registro["numero_interior"])){
            $this->registro['numero_interior'] = '';
        }

        $es_empleado = false;

        if(isset($this->registro["es_empleado"])){
            $es_empleado = $this->registro["es_empleado"];
        }

        $this->registro = $this->limpia_campos(registro: $this->registro, campos_limpiar: array('dp_pais_id',
            'dp_estado_id', 'dp_municipio_id', 'dp_cp_id', 'dp_cp_id', 'dp_colonia_postal_id', 'es_empleado'));
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al limpiar campos', data: $this->registro);
        }

        $r_alta_bd = parent::alta_bd($keys_integra_ds);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar cliente', data: $r_alta_bd);
        }

        $data = (new com_sucursal($this->link))->maqueta_data(codigo: $this->registro["codigo"],
            nombre_contacto: $this->registro["razon_social"], com_cliente_id: $r_alta_bd->registro_id,
            telefono: $this->registro["telefono"], dp_calle_pertenece_id: $this->registro["dp_calle_pertenece_id"],
            numero_exterior: $this->registro["numero_exterior"], numero_interior: $this->registro["numero_interior"],
            es_empleado: $es_empleado);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al maquetar datos de sucursal', data: $data);
        }

        $alta_sucursal = (new com_sucursal($this->link))->alta_registro(registro: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar sucursal', data: $alta_sucursal);
        }

        if(count($row_tmp)>0){
            $row_tmp['com_cliente_id'] = $r_alta_bd->registro_id;
            $alta_tmp_dom = (new com_tmp_cte_dp(link: $this->link))->alta_registro(registro: $row_tmp);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al insertar tmp dom', data: $alta_tmp_dom);
            }

        }

        return $r_alta_bd;
    }

    private function com_sucursal_descripcion(stdClass $com_cliente, array $sucursal){
        $data = array();
        $data['codigo'] = $sucursal['com_sucursal_codigo'];

        $com_sucursal_descripcion = (new com_sucursal(link: $this->link))->ds(
            com_cliente_razon_social: $com_cliente->razon_social,com_cliente_rfc:  $com_cliente->rfc, data: $data);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener com_sucursal_descripcion', data: $com_sucursal_descripcion);
        }
        return $com_sucursal_descripcion;
    }

    private function com_sucursal_upd(stdClass $com_cliente, int $com_cliente_id, string $com_sucursal_descripcion, array $sucursal): array
    {
        $com_sucursal_upd['codigo'] = $sucursal['com_sucursal_codigo'];
        $com_sucursal_upd['descripcion'] = $com_sucursal_descripcion;
        $com_sucursal_upd['com_cliente_id'] = $com_cliente_id;

        if($sucursal['com_tipo_sucursal_descripcion'] === 'MATRIZ') {
            $com_sucursal_upd = $this->com_sucursal_upd_dom(com_cliente: $com_cliente,com_sucursal_upd: $com_sucursal_upd);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al maquetar sucursal', data: $com_sucursal_upd);
            }
        }
        return $com_sucursal_upd;
    }

    private function com_sucursal_upd_dom(stdClass $com_cliente, array $com_sucursal_upd): array
    {
        $keys = array('dp_calle_pertenece_id','numero_exterior','telefono');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys, registro: $com_cliente);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar cliente', data: $valida);
        }

        $com_sucursal_upd['dp_calle_pertenece_id'] = $com_cliente->dp_calle_pertenece_id;
        $com_sucursal_upd['numero_exterior'] = $com_cliente->numero_exterior;
        $com_sucursal_upd['numero_interior'] = $com_cliente->numero_interior;
        $com_sucursal_upd['telefono_1'] = $com_cliente->telefono;
        $com_sucursal_upd['telefono_2'] = $com_cliente->telefono;
        $com_sucursal_upd['telefono_3'] = $com_cliente->telefono;
        return $com_sucursal_upd;
    }

    /**
     * Elimina un cliente mas las sucursales dentro del cliente
     * @param int $id Identificador del cliente
     * @return array|stdClass
     */
    final public function elimina_bd(int $id): array|stdClass
    {

        $filtro['com_cliente.id'] = $id;
        $r_com_sucursal = (new com_sucursal(link: $this->link))->elimina_con_filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar sucursales', data: $r_com_sucursal);
        }

        $r_elimina_bd = parent::elimina_bd($id); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar', data: $r_elimina_bd);
        }
        return $r_elimina_bd;
    }

    /**
     * Inicializa los elementos base de un cliente
     * @param array $data Datos de registro
     * @return array
     * @version 7.15.1
     */
    final protected function init_base(array $data): array
    {
        if (isset($data['razon_social']) && !isset($data['descripcion'])) {
            $data['descripcion'] = $data['razon_social'];
        }

        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    private function inicializa_foraneas(array $data): array
    {
        if (isset($data['status'])) {
            return $data;
        }

        $foraneas['cat_sat_moneda_id'] = new cat_sat_moneda($this->link);
        $foraneas['dp_calle_pertenece_id'] = new dp_calle_pertenece($this->link);
        $foraneas['cat_sat_regimen_fiscal_id'] = new cat_sat_regimen_fiscal($this->link);
        $foraneas['cat_sat_forma_pago_id'] = new cat_sat_forma_pago($this->link);
        $foraneas['cat_sat_uso_cfdi_id'] = new cat_sat_uso_cfdi($this->link);
        $foraneas['cat_sat_tipo_de_comprobante_id'] = new cat_sat_tipo_de_comprobante($this->link);
        $foraneas['cat_sat_metodo_pago_id'] = new cat_sat_metodo_pago($this->link);
        $foraneas['com_tipo_cliente_id'] = new com_tipo_cliente($this->link);

        foreach ($foraneas as $key => $modelo_pred) {

            $codigo = 'PRED';
            if($key ==='cat_sat_moneda_id') {
                $codigo = 'XXX';
            }
            if($key ==='cat_sat_regimen_fiscal_id') {
                $codigo = '999';
            }

            $ins_pred = $modelo_pred->inserta_predeterminado(codigo: $codigo);
            if (errores::$error) {
                return $this->error->error(mensaje: "Error al insertar predeterminada en modelo ".$this->tabla, data: $ins_pred);
            }

            if (!isset($data[$key]) || $data[$key] === -1) {
                $predeterminado = ($modelo_pred)->id_predeterminado();
                if (errores::$error) {
                    return $this->error->error(mensaje: "Error al $key predeterminada en modelo ".$this->tabla, data: $predeterminado);
                }
                $data[$key] = $predeterminado;
            }
        }

        return $data;
    }

    private function limpia_campos(array $registro, array $campos_limpiar): array
    {
        foreach ($campos_limpiar as $valor) {
            if (isset($registro[$valor])) {
                unset($registro[$valor]);
            }
        }
        return $registro;
    }

    public function modifica_bd(array $registro, int $id, bool $reactiva = false,
                                array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {

        $registro = $this->registro_cliente_upd(registro: $registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al limpiar campos', data: $registro);
        }

        $r_modifica_bd = parent::modifica_bd(registro: $registro,id:  $id, reactiva: $reactiva,
            keys_integra_ds:  $keys_integra_ds);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al modificar cliente', data: $r_modifica_bd);
        }

        $com_cliente = $this->registro(registro_id: $id, columnas_en_bruto: true, retorno_obj: true);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener cliente', data: $com_cliente);
        }

        $r_com_sucursal = $this->upd_sucursales(com_cliente:$com_cliente,com_cliente_id:  $id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al modificar sucursales', data: $r_com_sucursal);
        }


        return $r_modifica_bd;
    }

    private function registro_cliente_upd(array $registro){
        $registro = $this->init_base(data: $registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al inicializar campo base', data: $registro);
        }

        $registro = $this->inicializa_foraneas(data: $registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al inicializar foraneas', data: $registro);
        }

        $registro = $this->limpia_campos(registro: $registro, campos_limpiar: array('dp_pais_id', 'dp_estado_id',
            'dp_municipio_id', 'dp_cp_id', 'dp_cp_id', 'dp_colonia_postal_id'));
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al limpiar campos', data: $registro);
        }
        return $registro;
    }

    private function row_com_sucursal_upd(stdClass $com_cliente, int $com_cliente_id, array $sucursal){
        $com_sucursal_descripcion = $this->com_sucursal_descripcion(com_cliente: $com_cliente, sucursal: $sucursal);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener com_sucursal_descripcion', data: $com_sucursal_descripcion);
        }


        $com_sucursal_upd = $this->com_sucursal_upd(com_cliente: $com_cliente,com_cliente_id:  $com_cliente_id,
            com_sucursal_descripcion:  $com_sucursal_descripcion,sucursal:  $sucursal);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al maquetar row', data: $com_sucursal_upd);
        }
        return $com_sucursal_upd;
    }

    private function upd_sucursal(stdClass $com_cliente, int $com_cliente_id, array $sucursal){
        $com_sucursal_upd = $this->row_com_sucursal_upd(com_cliente: $com_cliente,com_cliente_id:  $com_cliente_id,sucursal:  $sucursal);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al maquetar row', data: $com_sucursal_upd);
        }

        $r_com_sucursal = (new com_sucursal(link: $this->link))->modifica_bd(registro: $com_sucursal_upd,
            id:  $sucursal['com_sucursal_id']);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al modificar sucursales', data: $r_com_sucursal);
        }
        return $r_com_sucursal;
    }

    private function upd_sucursales(stdClass $com_cliente, int $com_cliente_id){
        $r_com_sucursales = array();
        $r_sucursales = (new com_sucursal(link: $this->link))->sucursales(com_cliente_id: $com_cliente_id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener sucursales', data: $r_sucursales);
        }

        $sucursales = $r_sucursales->registros;
        foreach ($sucursales as $sucursal){
            $r_com_sucursal = $this->upd_sucursal(com_cliente: $com_cliente,com_cliente_id:  $com_cliente_id,sucursal:  $sucursal);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al modificar sucursales', data: $r_com_sucursal);
            }
            $r_com_sucursales[] = $r_com_sucursal;
        }
        return $r_com_sucursales;
    }
}