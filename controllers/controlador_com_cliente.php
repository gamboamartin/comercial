<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */

namespace gamboamartin\comercial\controllers;

use base\controller\controler;
use controllers\_init_dps;
use gamboamartin\cat_sat\models\cat_sat_forma_pago;
use gamboamartin\cat_sat\models\cat_sat_metodo_pago;
use gamboamartin\cat_sat\models\cat_sat_moneda;
use gamboamartin\cat_sat\models\cat_sat_regimen_fiscal;
use gamboamartin\cat_sat\models\cat_sat_tipo_de_comprobante;
use gamboamartin\cat_sat\models\cat_sat_uso_cfdi;
use gamboamartin\comercial\models\com_cliente;
use gamboamartin\comercial\models\com_tipo_cliente;
use gamboamartin\direccion_postal\models\dp_calle_pertenece;
use gamboamartin\errores\errores;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use html\com_cliente_html;
use PDO;
use stdClass;

class controlador_com_cliente extends _ctl_base
{
    public function __construct(PDO      $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $modelo = new com_cliente(link: $link);
        $html = new com_cliente_html(html: $html);
        $obj_link = new links_menu(link: $link, registro_id: $this->registro_id);

        $datatables = $this->init_datatable();
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al inicializar datatable', data: $datatables);
            print_r($error);
            die('Error');
        }

        parent::__construct(html: $html, link: $link, modelo: $modelo, obj_link: $obj_link, datatables: $datatables,
            paths_conf: $paths_conf);

        $configuraciones = $this->init_configuraciones();
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al inicializar configuraciones', data: $configuraciones);
            print_r($error);
            die('Error');
        }

        $this->parents_verifica[] = (new com_tipo_cliente(link: $this->link));
        $this->parents_verifica[] = (new cat_sat_tipo_de_comprobante(link: $this->link));
        $this->parents_verifica[] = (new cat_sat_uso_cfdi(link: $this->link));
        $this->parents_verifica[] = (new cat_sat_metodo_pago(link: $this->link));
        $this->parents_verifica[] = (new cat_sat_forma_pago(link: $this->link));
        $this->parents_verifica[] = (new cat_sat_moneda(link: $this->link));
        $this->parents_verifica[] = (new cat_sat_regimen_fiscal(link: $this->link));
        $this->parents_verifica[] = (new dp_calle_pertenece(link: $this->link));

        $this->verifica_parents_alta = true;

        $this->childrens_data['com_sucursal']['title'] = 'Sucursal';



    }

    public function alta(bool $header, bool $ws = false): array|string
    {

        $urls_js = (new _init_dps())->init_js(controler: $this);

        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar url js',data:  $urls_js,header: $header,ws: $ws);
        }

        $r_alta = $this->init_alta();
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al inicializar alta', data: $r_alta, header: $header, ws: $ws);
        }

        $keys_selects = $this->init_selects_inputs();
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al inicializar selects', data: $r_alta, header: $header, ws: $ws);
        }

        $inputs = $this->inputs(keys_selects: $keys_selects);
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs', data: $inputs, header: $header, ws: $ws);
        }



        return $r_alta;
    }

    /**
     * Inicializa las configuraciones base del controler
     * @return controler
     * @version 4.32.3
     */
    private function init_configuraciones(): controler
    {
        $this->titulo_lista = 'Registro de Clientes';

        return $this;
    }

    /**
     * @param array $keys_selects
     * @param string $key
     * @param string $label
     * @param int|null $id_selected
     * @param int $cols
     * @param bool $con_registros
     * @param array $filtro
     * @return array
     */
    private function init_selects(array $keys_selects, string $key, string $label, int|null $id_selected = -1, int $cols = 6,
                                  bool  $con_registros = true, array $filtro = array()): array
    {
        $keys_selects = $this->key_select(cols: $cols, con_registros: $con_registros, filtro: $filtro, key: $key,
            keys_selects: $keys_selects, id_selected: $id_selected, label: $label);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        return $keys_selects;
    }

    public function init_selects_inputs(): array{

        $keys_selects = $this->init_selects(keys_selects: array(), key: "com_tipo_cliente_id", label: "Tipo de Cliente",
            cols: 12);

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_regimen_fiscal_id",
            label: "Régimen Fiscal", cols: 12);

        $keys_selects['cat_sat_regimen_fiscal_id']->columns_descripcion_select = array(
            'cat_sat_regimen_fiscal_codigo','cat_sat_regimen_fiscal_descripcion');


        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_pais_id", label: "País");
        $keys_selects['dp_pais_id']->key_descripcion_select = 'dp_pais_descripcion';

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_estado_id", label: "Estado",
            con_registros: false);
        $keys_selects['dp_estado_id']->key_descripcion_select = 'dp_estado_descripcion';

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_municipio_id", label: "Municipio",
            con_registros: false);

        $keys_selects['dp_municipio_id']->key_descripcion_select = 'dp_municipio_descripcion';

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_cp_id", label: "Código Postal",
            con_registros: false);
        $keys_selects['dp_cp_id']->key_descripcion_select = 'dp_cp_descripcion';

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_colonia_postal_id",
            label: "Colonia Postal", con_registros: false);

        $keys_selects['dp_colonia_postal_id']->key_descripcion_select = 'dp_colonia_descripcion';

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_calle_pertenece_id", label: "Calle",
            con_registros: false);
        $keys_selects['dp_calle_pertenece_id']->key_descripcion_select = 'dp_calle_descripcion';

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_uso_cfdi_id", label: "Uso CFDI",
            cols: 12);

        $keys_selects['cat_sat_uso_cfdi_id']->columns_ds = array(
            'cat_sat_uso_cfdi_codigo','cat_sat_uso_cfdi_descripcion');

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_metodo_pago_id",
            label: "Método de Pago");

        $keys_selects['cat_sat_metodo_pago_id']->columns_ds = array(
            'cat_sat_metodo_pago_codigo','cat_sat_metodo_pago_descripcion');

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_forma_pago_id",
            label: "Forma Pago");

        $keys_selects['cat_sat_forma_pago_id']->columns_ds = array(
            'cat_sat_forma_pago_codigo','cat_sat_forma_pago_descripcion');

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_tipo_de_comprobante_id",
            label: "Tipo de Comprobante");

        $keys_selects['cat_sat_tipo_de_comprobante_id']->columns_ds = array(
            'cat_sat_tipo_de_comprobante_codigo','cat_sat_tipo_de_comprobante_descripcion');


        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_moneda_id",
            label: "Moneda");

        $keys_selects['cat_sat_moneda_id']->columns_ds = array(
            'cat_sat_moneda_codigo','cat_sat_moneda_descripcion');

        return $keys_selects;
    }

    /**
     * Inicializa los elementos de lista
     * @return stdClass
     * @version 4.28.3
     */
    private function init_datatable(): stdClass
    {
        $columns["com_cliente_id"]["titulo"] = "Id";
        $columns["com_cliente_codigo"]["titulo"] = "Código";
        $columns["com_cliente_razon_social"]["titulo"] = "Razón Social";
        $columns["com_cliente_rfc"]["titulo"] = "RFC";
        $columns["cat_sat_regimen_fiscal_descripcion"]["titulo"] = "Régimen Fiscal";
        $columns["com_cliente_n_sucursales"]["titulo"] = "Sucursales";

        $filtro = array("com_cliente.id", "com_cliente.codigo", "com_cliente.razon_social", "com_cliente.rfc",
            "cat_sat_regimen_fiscal.descripcion");

        $datatables = new stdClass();
        $datatables->columns = $columns;
        $datatables->filtro = $filtro;

        return $datatables;
    }

    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array('codigo', 'razon_social', 'rfc', 'telefono', 'numero_exterior', 'numero_interior');
        $keys->selects = array();

        $init_data = array();
        $init_data['dp_pais'] = "gamboamartin\\direccion_postal";
        $init_data['dp_estado'] = "gamboamartin\\direccion_postal";
        $init_data['dp_municipio'] = "gamboamartin\\direccion_postal";
        $init_data['dp_cp'] = "gamboamartin\\direccion_postal";
        $init_data['dp_colonia_postal'] = "gamboamartin\\direccion_postal";
        $init_data['dp_calle_pertenece'] = "gamboamartin\\direccion_postal";
        $init_data['cat_sat_regimen_fiscal'] = "gamboamartin\\cat_sat";
        $init_data['cat_sat_moneda'] = "gamboamartin\\cat_sat";
        $init_data['cat_sat_forma_pago'] = "gamboamartin\\cat_sat";
        $init_data['cat_sat_metodo_pago'] = "gamboamartin\\cat_sat";
        $init_data['cat_sat_uso_cfdi'] = "gamboamartin\\cat_sat";
        $init_data['cat_sat_tipo_de_comprobante'] = "gamboamartin\\cat_sat";
        $init_data['com_tipo_cliente'] = "gamboamartin\\comercial";
        $campos_view = $this->campos_view_base(init_data: $init_data, keys: $keys);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar campo view', data: $campos_view);
        }

        return $campos_view;
    }

    protected function key_selects_txt(array $keys_selects): array
    {
        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 4, key: 'codigo',
            keys_selects: $keys_selects, place_holder: 'Cod');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 8, key: 'razon_social',
            keys_selects: $keys_selects, place_holder: 'Razón Social');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'rfc',
            keys_selects: $keys_selects, place_holder: 'RFC');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'telefono',
            keys_selects: $keys_selects, place_holder: 'Teléfono');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new _base())->keys_selects(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        return $keys_selects;
    }

    public function modifica(bool $header, bool $ws = false): array|stdClass
    {

        $urls_js = (new _init_dps())->init_js(controler: $this);

        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar url js',data:  $urls_js,header: $header,ws: $ws);
        }

        $r_modifica = $this->init_modifica();
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al generar salida de template', data: $r_modifica, header: $header, ws: $ws);
        }

        $calle = (new dp_calle_pertenece($this->link))->get_calle_pertenece($this->registro['dp_calle_pertenece_id']);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener calle', data: $calle);
        }

        $keys_selects = $this->init_selects(keys_selects: array(), key: "com_tipo_cliente_id", label: "Tipo de Cliente",
            id_selected: $this->registro['com_tipo_cliente_id'], cols: 12);

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_regimen_fiscal_id",
            label: "Régimen Fiscal", id_selected: $this->registro['cat_sat_regimen_fiscal_id'], cols: 12);

        $keys_selects['cat_sat_regimen_fiscal_id']->columns_ds = array(
            'cat_sat_regimen_fiscal_codigo','cat_sat_regimen_fiscal_descripcion');

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_pais_id", label: "País",
            id_selected: $this->registro['dp_pais_id']);

        $keys_selects['dp_pais_id']->key_descripcion_select = 'dp_pais_descripcion';

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_estado_id", label: "Estado",
            id_selected: $this->registro['dp_estado_id'], filtro: array('dp_pais.id' => $calle['dp_pais_id']));

        $keys_selects['dp_estado_id']->key_descripcion_select = 'dp_estado_descripcion';

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_municipio_id", label: "Municipio",
            id_selected: $this->registro['dp_municipio_id'], filtro: array('dp_estado.id' => $calle['dp_estado_id']));

        $keys_selects['dp_municipio_id']->key_descripcion_select = 'dp_municipio_descripcion';

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_cp_id", label: "Código Postal",
            id_selected: $this->registro['dp_cp_id'], filtro: array('dp_municipio.id' => $calle['dp_municipio_id']));

        $keys_selects['dp_cp_id']->key_descripcion_select = 'dp_cp_descripcion';

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_colonia_postal_id",
            label: "Colonia Postal", id_selected: $this->registro['dp_colonia_postal_id'],
            filtro: array('dp_cp.id' => $calle['dp_cp_id']));

        $keys_selects['dp_colonia_postal_id']->key_descripcion_select = 'dp_colonia_descripcion';


        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_calle_pertenece_id", label: "Calle",
            id_selected: $this->registro['dp_calle_pertenece_id'],
            filtro: array('dp_colonia_postal.id' => $calle['dp_colonia_postal_id']));

        $keys_selects['dp_calle_pertenece_id']->key_descripcion_select = 'dp_calle_descripcion';

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_uso_cfdi_id", label: "Uso CFDI",
            id_selected: $this->registro['cat_sat_uso_cfdi_id'], cols: 12);

        $keys_selects['cat_sat_uso_cfdi_id']->columns_ds = array(
            'cat_sat_uso_cfdi_codigo','cat_sat_uso_cfdi_descripcion');


        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_metodo_pago_id",
            label: "Método de Pago", id_selected: $this->registro['cat_sat_metodo_pago_id']);

        $keys_selects['cat_sat_metodo_pago_id']->columns_ds = array(
            'cat_sat_metodo_pago_codigo','cat_sat_metodo_pago_descripcion');


        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_forma_pago_id",
            label: "Forma Pago", id_selected: $this->registro['cat_sat_forma_pago_id']);

        $keys_selects['cat_sat_forma_pago_id']->columns_ds = array(
            'cat_sat_forma_pago_codigo','cat_sat_forma_pago_descripcion');


        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_tipo_de_comprobante_id",
            label: "Tipo de Comprobante", id_selected: $this->registro['cat_sat_tipo_de_comprobante_id']);

        $keys_selects['cat_sat_tipo_de_comprobante_id']->columns_ds = array(
            'cat_sat_tipo_de_comprobante_codigo','cat_sat_tipo_de_comprobante_descripcion');

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_moneda_id",
            label: "Moneda", id_selected: $this->registro['cat_sat_moneda_id']);

        $keys_selects['cat_sat_moneda_id']->columns_ds = array(
            'cat_sat_moneda_codigo','cat_sat_moneda_descripcion');


        $this->not_actions[] = __FUNCTION__;

        $base = $this->base_upd(keys_selects: $keys_selects,  params: array(), params_ajustados: array());
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar base', data: $base, header: $header, ws: $ws);
        }

        return $r_modifica;
    }



}
