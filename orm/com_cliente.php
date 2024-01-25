<?php

namespace gamboamartin\comercial\models;

use base\orm\_modelo_parent;
use gamboamartin\cat_sat\models\_validacion;
use gamboamartin\cat_sat\models\cat_sat_forma_pago;
use gamboamartin\cat_sat\models\cat_sat_metodo_pago;
use gamboamartin\cat_sat\models\cat_sat_moneda;
use gamboamartin\cat_sat\models\cat_sat_regimen_fiscal;
use gamboamartin\cat_sat\models\cat_sat_tipo_de_comprobante;
use gamboamartin\cat_sat\models\cat_sat_uso_cfdi;
use gamboamartin\direccion_postal\models\dp_calle_pertenece;
use gamboamartin\direccion_postal\models\dp_municipio;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class com_cliente extends _modelo_parent
{
    public function __construct(PDO $link)
    {
        $tabla = 'com_cliente';

        $columnas = array($tabla => false, 'cat_sat_moneda' => $tabla, 'cat_sat_regimen_fiscal' => $tabla,
            'dp_municipio' => $tabla, 'dp_estado' => 'dp_municipio', 'dp_pais' => 'dp_estado',
            'com_tipo_cliente' => $tabla, 'cat_sat_uso_cfdi' => $tabla, 'cat_sat_metodo_pago' => $tabla,
            'cat_sat_forma_pago' => $tabla, 'cat_sat_tipo_de_comprobante' => $tabla,'cat_sat_tipo_persona'=>$tabla);

        $campos_obligatorios = array('cat_sat_moneda_id', 'cat_sat_regimen_fiscal_id', 'cat_sat_moneda_id',
            'cat_sat_forma_pago_id', 'cat_sat_uso_cfdi_id', 'cat_sat_tipo_de_comprobante_id', 'cat_sat_metodo_pago_id',
            'telefono','cat_sat_tipo_persona_id','pais','estado','municipio','colonia','calle','cp','dp_municipio_id');

        $columnas_extra['com_cliente_n_sucursales'] =
            "(SELECT COUNT(*) FROM com_sucursal WHERE com_sucursal.com_cliente_id = com_cliente.id)";

        $tipo_campos = array();
        $tipo_campos['rfc'] = 'rfc';

        $atributos_criticos[] = 'cat_sat_tipo_persona_id';

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, tipo_campos: $tipo_campos,
            atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Cliente';
    }

    private function ajusta_key_dom(string $key_dom, array $registro)
    {
        $dp_calle_pertenece = (new dp_calle_pertenece(link: $this->link))->registro(
            registro_id: $registro['dp_calle_pertenece_id']);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener calle', data: $dp_calle_pertenece);
        }

        $registro = $this->integra_key_dom_faltante(dp_calle_pertenece: $dp_calle_pertenece,key_dom:  $key_dom,registro:  $registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al integrar key_dom', data: $registro);
        }
        return $registro;

    }

    private function ajusta_keys_dom(array $registro)
    {
        $keys_dom = array('pais','estado','municipio','colonia','calle','cp');

        foreach ($keys_dom as $key_dom){

            $registro = $this->ajusta_key_dom(key_dom: $key_dom,registro:  $registro);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al integrar key_dom', data: $registro);
            }
        }


        return $registro;

    }

    /**
     * Inserta un cliente
     * @param array $keys_integra_ds  Campos para la integracion de descricpion select
     * @return array|stdClass
     */
    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {


        $this->registro = $this->init_base(data: $this->registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al inicializar campo base', data: $this->registro);
        }
        $this->registro = $this->inicializa_foraneas(data: $this->registro, funcion_llamada: __FUNCTION__);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al inicializar foraneas', data: $this->registro);
        }

        $keys = array('telefono','numero_exterior','razon_social','dp_municipio_id');
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

        $dp_municipio_modelo = new dp_municipio(link: $this->link);
        $dp_municipio = $dp_municipio_modelo->registro(registro_id: $this->registro['dp_municipio_id']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener dp_municipio', data: $dp_municipio);
        }

        $this->registro['pais'] = $dp_municipio['dp_pais_descripcion'];
        $this->registro['estado'] = $dp_municipio['dp_estado_descripcion'];
        $this->registro['municipio'] = $dp_municipio['dp_municipio_descripcion'];


        $this->registro = $this->limpia_campos(registro: $this->registro, campos_limpiar: array('dp_pais_id',
            'dp_estado_id', 'dp_cp_id', 'dp_cp_id', 'dp_colonia_postal_id', 'es_empleado'));
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al limpiar campos', data: $this->registro);
        }

        $valida = (new _validacion())->valida_metodo_pago(link: $this->link, registro: $this->registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar datos', data: $valida);
        }

        $valida = (NEW _validacion())->valida_conf_tipo_persona(link: $this->link,registro:  $this->registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar datos', data: $valida);
        }

        $registro = $this->descripcion(registro: $this->registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al integrar descripcion', data: $registro);
        }
        $this->registro = $registro;


        $registro = $this->ajusta_keys_dom(registro: $this->registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al integrar key_dom', data: $registro);
        }
        $this->registro = $registro;



        $r_alta_bd = parent::alta_bd(keys_integra_ds: $keys_integra_ds);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar cliente', data: $r_alta_bd);
        }

        $data = (new com_sucursal($this->link))->maqueta_data(codigo: $this->registro["codigo"],
            cp: $this->registro['cp'], nombre_contacto: $this->registro["razon_social"],
            com_cliente_id: $r_alta_bd->registro_id, telefono: $this->registro["telefono"],
            dp_calle_pertenece_id: $this->registro["dp_calle_pertenece_id"], dp_municipio_id: $dp_municipio['dp_municipio_id'],
            numero_exterior: $this->registro["numero_exterior"], numero_interior: $this->registro["numero_interior"], es_empleado: $es_empleado);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al maquetar datos de sucursal', data: $data);
        }

        $valida = (new com_sucursal(link: $this->link))->valida_base_sucursal(registro: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar datos para sucursal', data: $valida);
        }

        $alta_sucursal = (new com_sucursal($this->link))->alta_registro(registro: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar sucursal', data: $alta_sucursal);
        }


        return $r_alta_bd;
    }

    /**
     * Obtiene la descripcion de una sucursal
     * @param stdClass $com_cliente Registro de tipo cliente
     * @param array $sucursal Registro de tipo sucursal
     * @return array|string
     * @version 17.16.0
     */
    private function com_sucursal_descripcion(stdClass $com_cliente, array $sucursal): array|string
    {
        $valida = $this->valida_data_sucursal(com_cliente: $com_cliente,sucursal:  $sucursal);;
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al al validar datos', data: $valida);
        }

        $data = array();
        $data['codigo'] = $sucursal['com_sucursal_codigo'];

        $com_sucursal_descripcion = (new com_sucursal(link: $this->link))->ds(
            com_cliente_razon_social: $com_cliente->razon_social,com_cliente_rfc:  $com_cliente->rfc, data: $data);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener com_sucursal_descripcion',
                data: $com_sucursal_descripcion);
        }
        return $com_sucursal_descripcion;
    }

    /**
     * Integra los elementos a actualizar de una sucursal basada en los datos de un cliente
     * @param stdClass $com_cliente Registro de cliente
     * @param int $com_cliente_id Identificador del cliente
     * @param string $com_sucursal_descripcion Descripcion de la sucursal
     * @param array $sucursal Registro de sucursal previo
     * @return array
     * @version 17.16.0
     */
    private function com_sucursal_upd(stdClass $com_cliente, int $com_cliente_id, string $com_sucursal_descripcion,
                                      array $sucursal): array
    {
        $keys = array('com_sucursal_codigo','com_tipo_sucursal_descripcion');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $sucursal);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar sucursal', data: $valida);
        }
        if($com_cliente_id<=0){
            return $this->error->error(mensaje: 'Error com_cliente_id debe ser mayor a 0', data: $com_cliente_id);
        }

        $keys = array('dp_calle_pertenece_id','numero_exterior','telefono');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys, registro: $com_cliente);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar cliente', data: $valida);
        }
        if(!isset($com_cliente->numero_interior)){
            $com_cliente->numero_interior = '';
        }

        $com_sucursal_descripcion = trim($com_sucursal_descripcion);
        if($com_sucursal_descripcion === ''){
            return $this->error->error(mensaje: 'Error com_sucursal_descripcion esta vacia',
                data: $com_sucursal_descripcion);
        }

        $com_sucursal_upd['codigo'] = $sucursal['com_sucursal_codigo'];
        $com_sucursal_upd['descripcion'] = $com_sucursal_descripcion;
        $com_sucursal_upd['com_cliente_id'] = $com_cliente_id;

        if($sucursal['com_tipo_sucursal_descripcion'] === 'MATRIZ') {
            $com_sucursal_upd = $this->com_sucursal_upd_dom(com_cliente: $com_cliente,
                com_sucursal_upd: $com_sucursal_upd);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al maquetar sucursal', data: $com_sucursal_upd);
            }
        }
        return $com_sucursal_upd;
    }

    /**
     * Valida que sean correctos los elementos de la direccion de un cliente
     * @param stdClass $com_cliente Registro de tipo cliente
     * @param array $com_sucursal_upd registro a actualizar de sucursal
     * @return array
     * @version 17.15.0
     */
    private function com_sucursal_upd_dom(stdClass $com_cliente, array $com_sucursal_upd): array
    {
        $keys = array('dp_calle_pertenece_id','numero_exterior','telefono');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys, registro: $com_cliente);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar cliente', data: $valida);
        }
        if(!isset($com_cliente->numero_interior)){
            $com_cliente->numero_interior = '';
        }

        $com_sucursal_upd['dp_calle_pertenece_id'] = trim($com_cliente->dp_calle_pertenece_id);
        $com_sucursal_upd['numero_exterior'] = trim($com_cliente->numero_exterior);
        $com_sucursal_upd['numero_interior'] = trim($com_cliente->numero_interior);
        $com_sucursal_upd['telefono_1'] = trim($com_cliente->telefono);
        $com_sucursal_upd['telefono_2'] = trim($com_cliente->telefono);
        $com_sucursal_upd['telefono_3'] = trim($com_cliente->telefono);
        return $com_sucursal_upd;
    }

    private function descripcion(array $registro): array
    {
        if(!isset($registro['descripcion'])){
            $descripcion = trim($registro['razon_social'].' '.$registro['rfc']);
            $registro['descripcion'] = $descripcion;
        }
        return $registro;

    }


    /**
     * Elimina un cliente mas las sucursales dentro del cliente
     * @param int $id Identificador del cliente
     * @return array|stdClass
     * @version 18.17.0
     */
    final public function elimina_bd(int $id): array|stdClass
    {

        if($id <= 0){
            return $this->error->error(mensaje: 'Error id es menor a 0', data: $id);
        }

        $filtro['com_cliente.id'] = $id;
        $r_com_sucursal = (new com_sucursal(link: $this->link))->elimina_con_filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar sucursales', data: $r_com_sucursal);
        }

        $r_elimina_bd = parent::elimina_bd(id: $id); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar', data: $r_elimina_bd);
        }
        return $r_elimina_bd;
    }

    /**
     * POR DOCUMENTAR EN WIKI
     * Función para inicializar las variables básicas del cliente.
     *
     * Esta función recibe un arreglo asociativo con la información del cliente, si dentro de este arreglo se encuentra el índice
     * 'razon_social' y no se encuentra el índice 'descripcion', asignará el valor de 'razon_social' a 'descripcion'.
     *
     * @param array $data El arreglo asociativo que contiene los datos que serán inicializados.
     * @return array El arreglo asociativo $data después de haber sido procesado por la función.
     *
     * @example Ejemplo de uso:
     * <code>
     *  $cliente = array(
     *      'razon_social' => 'Compañía XY',
     *  );
     *  $cliente = init_base($cliente);
     *  echo $cliente['descripcion']; // Imprime: Compañía XY
     * </code>
     * @version 22.2.0
     */
    final protected function init_base(array $data): array
    {
        if (isset($data['razon_social']) && !isset($data['descripcion'])) {
            $data['descripcion'] = $data['razon_social'];
        }

        return $data;
    }

    /**
     * Inicializa las foraneas base de un cliente siempre y cuando sea ejecutado en alta bd
     * @param array $data Registro
     * @param string $funcion_llamada Funcion base de llamada alta_bd o modifica_bd
     * @return array
     */
    private function inicializa_foraneas(array $data, string $funcion_llamada): array
    {
        $funcion_llamada = trim($funcion_llamada);
        if($funcion_llamada === ''){
            return $this->error->error(mensaje: "Error al funcion_llamada esta vacia" . $this->tabla,
                data: $funcion_llamada);
        }

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

            if($funcion_llamada === 'alta_bd') {
                if (!isset($data[$key]) || $data[$key] === -1) {
                    $predeterminado = ($modelo_pred)->id_predeterminado();
                    if (errores::$error) {
                        return $this->error->error(mensaje: "Error al $key predeterminada en modelo " . $this->tabla,
                            data: $predeterminado);
                    }
                    $data[$key] = $predeterminado;
                }
            }
        }

        return $data;
    }

    private function integra_key_dom(array $dp_calle_pertenece, string $key_dom, array $registro): array
    {
        $registro[$key_dom] = $dp_calle_pertenece['dp_'.$key_dom.'_descripcion'];
        return $registro;

    }

    private function integra_key_dom_faltante(array $dp_calle_pertenece, string $key_dom, array $registro)
    {
        if(!isset($registro[$key_dom])){
            $registro = $this->integra_key_dom(dp_calle_pertenece: $dp_calle_pertenece,key_dom:  $key_dom,registro:  $registro);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al integrar key_dom', data: $registro);
            }
        }
        return $registro;

    }

    /**
     * Limpia elementos no insertables
     * @param array $registro Registro en proceso
     * @param array $campos_limpiar campos a quitar de registro
     * @return array
     * @version 17.6.0
     */
    private function limpia_campos(array $registro, array $campos_limpiar): array
    {
        foreach ($campos_limpiar as $valor) {
            $valor = trim($valor);
            if($valor === ''){
                return $this->error->error(mensaje: "Error valor esta vacio" . $this->tabla, data: $valor);
            }
            if (isset($registro[$valor])) {
                unset($registro[$valor]);
            }
        }
        return $registro;
    }

    /**
     * Modifica un registro de cliente
     * @param array $registro  Datos a actualizar
     * @param int $id Identificador
     * @param bool $reactiva Si reactiva no valida transacciones restrictivas
     * @param array $keys_integra_ds Datos para selects
     * @return array|stdClass
     */
    public function modifica_bd(array $registro, int $id, bool $reactiva = false,
                                array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        if($id<=0){
            return $this->error->error(mensaje: 'Error id debe ser mayor a 0', data: $id);
        }
        $registro_previo = $this->registro(registro_id: $id, columnas_en_bruto: true, retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener registro previo', data: $registro_previo);
        }

        $registro = $this->registro_cliente_upd(registro: $registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al limpiar campos', data: $registro);
        }

        if(!isset($registro['descripcion'])){
            $registro['descripcion'] = $registro_previo->descripcion;;
        }

        $r_modifica_bd = parent::modifica_bd(registro: $registro,id:  $id, reactiva: $reactiva,
            keys_integra_ds:  $keys_integra_ds);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al modificar cliente', data: $r_modifica_bd);
        }

        $valida = (NEW _validacion())->valida_conf_tipo_persona(link: $this->link,
            registro:  (array)$r_modifica_bd->registro_actualizado);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar datos', data: $valida);
        }

        $com_cliente = $this->registro(registro_id: $id, columnas_en_bruto: true, retorno_obj: true);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener cliente', data: $com_cliente);
        }

        $valida = (new _validacion())->valida_metodo_pago(link: $this->link, registro: (array)$com_cliente);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar datos', data: $valida);
        }
        $r_com_sucursal = $this->upd_sucursales(com_cliente:$com_cliente,com_cliente_id:  $id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al modificar sucursales', data: $r_com_sucursal);
        }

        return $r_modifica_bd;
    }

    /**
     * Modifica los datos de domicilio de un cliente
     * @param int $dp_calle_pertenece_id Calle a modificar
     * @param int $id Id de cliente
     * @return array|stdClass
     * @deprecated
     */
    final public function modifica_dp_calle_pertenece(int $dp_calle_pertenece_id, int $id): array|stdClass
    {
        if($id <=0){
            return $this->error->error(mensaje: 'Error id debe ser mayor a 0', data: $id);
        }
        if($dp_calle_pertenece_id <=0){
            return $this->error->error(mensaje: 'Error dp_calle_pertenece_id debe ser mayor a 0',
                data: $dp_calle_pertenece_id);
        }

        $registro_original = $this->registro(registro_id: $id, columnas_en_bruto: true, retorno_obj: true);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener cliente', data: $registro_original);
        }

        $registro['dp_calle_pertenece_id'] = $dp_calle_pertenece_id;
        $registro['descripcion'] = $registro_original->descripcion;
        $r_upd = parent::modifica_bd(registro: $registro,id:  $id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al modificar domicilio', data: $r_upd);
        }
        return $r_upd;
    }

    /**
     * Ajusta los elementos para una modificacion
     * @param array $registro Registro en proceso
     * @return array
     */
    private function registro_cliente_upd(array $registro): array
    {
        $registro = $this->init_base(data: $registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al inicializar campo base', data: $registro);
        }

        $registro = $this->inicializa_foraneas(data: $registro, funcion_llamada: 'modifica_bd');
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

    /**
     * Obtiene el registro a modificar de una sucursal
     * @param stdClass $com_cliente Registro de cliente
     * @param int $com_cliente_id Identificador de cliente
     * @param array $sucursal Sucursal a actualizar
     * @return array
     * @version 17.16.0
     */
    private function row_com_sucursal_upd(stdClass $com_cliente, int $com_cliente_id, array $sucursal): array
    {
        $valida = $this->valida_data_upd_sucursal(com_cliente: $com_cliente,com_cliente_id:  $com_cliente_id,
            sucursal:  $sucursal);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al al validar datos', data: $valida);
        }

        $keys = array('dp_calle_pertenece_id','numero_exterior','telefono');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys, registro: $com_cliente);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar cliente', data: $valida);
        }
        if(!isset($com_cliente->numero_interior)){
            $com_cliente->numero_interior = '';
        }

        $com_sucursal_descripcion = $this->com_sucursal_descripcion(com_cliente: $com_cliente, sucursal: $sucursal);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener com_sucursal_descripcion',
                data: $com_sucursal_descripcion);
        }


        $com_sucursal_upd = $this->com_sucursal_upd(com_cliente: $com_cliente,com_cliente_id:  $com_cliente_id,
            com_sucursal_descripcion:  $com_sucursal_descripcion,sucursal:  $sucursal);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al maquetar row', data: $com_sucursal_upd);
        }
        return $com_sucursal_upd;
    }


    /**
     * Actualiza los datos de las sucursales
     * @param stdClass $com_cliente Registro de cliente
     * @param int $com_cliente_id Identificador de cliente
     * @param array $sucursal Sucursal a modificar
     * @return array|stdClass
     * @version 17.18.0
     */
    private function upd_sucursal(stdClass $com_cliente, int $com_cliente_id, array $sucursal): array|stdClass
    {
        $valida = $this->valida_data_upd_sucursal(com_cliente: $com_cliente,com_cliente_id:  $com_cliente_id,
            sucursal:  $sucursal);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al al validar datos', data: $valida);
        }
        $keys = array('com_sucursal_id');
        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $sucursal);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error validar $sucursal', data: $valida);
        }
        $keys = array('dp_calle_pertenece_id','numero_exterior','telefono');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys, registro: $com_cliente);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar cliente', data: $valida);
        }
        if(!isset($com_cliente->numero_interior)){
            $com_cliente->numero_interior = '';
        }

        $com_sucursal_upd = $this->row_com_sucursal_upd(com_cliente: $com_cliente,com_cliente_id:
            $com_cliente_id,sucursal:  $sucursal);
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

    /**
     * Actualiza las sucursales de un cliente
     * @param stdClass $com_cliente Registro de cliente
     * @param int $com_cliente_id Identificador de cliente
     * @return array
     * @version 17.18.0
     */
    private function upd_sucursales(stdClass $com_cliente, int $com_cliente_id): array
    {
        if ($com_cliente_id <= 0) {
            return $this->error->error(mensaje: 'Error $com_cliente_id debe ser mayor a 0', data: $com_cliente_id);
        }
        $keys = array('dp_calle_pertenece_id','numero_exterior','telefono');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys, registro: $com_cliente);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar cliente', data: $valida);
        }
        if(!isset($com_cliente->numero_interior)){
            $com_cliente->numero_interior = '';
        }


        $r_com_sucursales = array();
        $r_sucursales = (new com_sucursal(link: $this->link))->sucursales(com_cliente_id: $com_cliente_id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener sucursales', data: $r_sucursales);
        }

        $sucursales = $r_sucursales->registros;
        foreach ($sucursales as $sucursal){
            $valida = $this->valida_data_upd_sucursal(com_cliente: $com_cliente,com_cliente_id:  $com_cliente_id,
                sucursal:  $sucursal);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al al validar datos', data: $valida);
            }

            $r_com_sucursal = $this->upd_sucursal(com_cliente: $com_cliente,com_cliente_id:  $com_cliente_id,
                sucursal:  $sucursal);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al modificar sucursales', data: $r_com_sucursal);
            }
            $r_com_sucursales[] = $r_com_sucursal;
        }
        return $r_com_sucursales;
    }

    /**
     * Valida que los datos basicos esten bien integrados para la actualizacion de une sucursal
     * @param array|stdClass $com_cliente Registro de cliente
     * @param array|stdClass $sucursal Registro sucursal
     * @return array|true
     * @version 17.16.0
     */
    private function valida_data_sucursal(array|stdClass $com_cliente, array|stdClass $sucursal): bool|array
    {
        $keys = array('com_sucursal_codigo');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $sucursal);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al al validar sucursal', data: $valida);
        }
        $keys = array('razon_social','rfc');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $com_cliente);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al al validar com_cliente', data: $valida);
        }
        return true;
    }

    /**
     * Valida los elementos para transaccionar sobre una sucursal
     * @param array|stdClass $com_cliente Registro de cliente
     * @param int $com_cliente_id Identificador de cliente
     * @param array|stdClass $sucursal Sucursal a afectar
     * @return array|true
     * @version 17.18.0
     */
    private function valida_data_upd_sucursal(array|stdClass $com_cliente, int $com_cliente_id,
                                              array|stdClass $sucursal): bool|array
    {
        $valida = $this->valida_data_sucursal(com_cliente: $com_cliente,sucursal:  $sucursal);;
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al al validar datos', data: $valida);
        }
        $keys = array('com_sucursal_codigo','com_tipo_sucursal_descripcion');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $sucursal);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar sucursal', data: $valida);
        }
        if($com_cliente_id<=0){
            return $this->error->error(mensaje: 'Error com_cliente_id debe ser mayor a 0', data: $com_cliente_id);
        }
        return true;
    }
}