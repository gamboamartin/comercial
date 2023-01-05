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
use gamboamartin\comercial\models\com_cliente;
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
    }

    public function alta(bool $header, bool $ws = false): array|string
    {
        $r_alta = $this->init_alta();
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al inicializar alta', data: $r_alta, header: $header, ws: $ws);
        }

        $keys_selects = $this->init_selects(keys_selects: array(), key: "com_tipo_cliente_id", label: "Tipo de Cliente",
            cols: 12);
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_regimen_fiscal_id",
            label: "Régimen Fiscal", cols: 12);
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_pais_id", label: "País");
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_estado_id", label: "Estado",
            con_registros: false);
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_municipio_id", label: "Municipio",
            con_registros: false);
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_cp_id", label: "Código Postal",
            con_registros: false);
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_colonia_postal_id",
            label: "Colonia Postal", con_registros: false);
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_calle_pertenece_id", label: "Calle",
            con_registros: false);
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_uso_cfdi_id", label: "Uso CFDI",
            cols: 12);
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_metodo_pago_id",
            label: "Método de Pago");
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_forma_pago_id",
            label: "Forma Pago");
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_tipo_de_comprobante_id",
            label: "Tipo de Comprobante");
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_moneda_id",
            label: "Moneda");

        $inputs = $this->inputs(keys_selects: $keys_selects);
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs', data: $inputs, header: $header, ws: $ws);
        }

        return $r_alta;
    }

    private function init_configuraciones(): controler
    {
        $this->seccion_titulo = 'Clientes';
        $this->titulo_lista = 'Registro de Clientes';

        return $this;
    }

    private function init_selects(array $keys_selects, string $key, string $label, int $id_selected = -1, int $cols = 6,
                                  bool  $con_registros = true, array $filtro = array()): array
    {
        $keys_selects = $this->key_select(cols: $cols, con_registros: $con_registros, filtro: $filtro, key: $key,
            keys_selects: $keys_selects, id_selected: $id_selected, label: $label);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        return $keys_selects;
    }

    private function init_datatable(): stdClass
    {
        $columns["com_cliente_id"]["titulo"] = "Id";
        $columns["com_cliente_codigo"]["titulo"] = "Código";
        $columns["com_cliente_razon_social"]["titulo"] = "Razón Social";
        $columns["com_cliente_rfc"]["titulo"] = "RFC";
        $columns["cat_sat_regimen_fiscal_descripcion"]["titulo"] = "Régimen Fiscal";

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

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'numero_interior',
            keys_selects: $keys_selects, place_holder: 'Num Int');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'numero_exterior',
            keys_selects: $keys_selects, place_holder: 'Num Ext');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        return $keys_selects;
    }

    public function modifica(bool $header, bool $ws = false): array|stdClass
    {
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
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_pais_id", label: "País",
            id_selected: $this->registro['dp_pais_id']);
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_estado_id", label: "Estado",
            id_selected: $this->registro['dp_estado_id'], filtro: array('dp_pais.id' => $calle['dp_pais_id']));
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_municipio_id", label: "Municipio",
            id_selected: $this->registro['dp_municipio_id'], filtro: array('dp_estado.id' => $calle['dp_estado_id']));
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_cp_id", label: "Código Postal",
            id_selected: $this->registro['dp_cp_id'], filtro: array('dp_municipio.id' => $calle['dp_municipio_id']));
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_colonia_postal_id",
            label: "Colonia Postal", id_selected: $this->registro['dp_colonia_postal_id'],
            filtro: array('dp_cp.id' => $calle['dp_cp_id']));
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_calle_pertenece_id", label: "Calle",
            id_selected: $this->registro['dp_calle_pertenece_id'],
            filtro: array('dp_colonia_postal.id' => $calle['dp_colonia_postal_id']));
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_uso_cfdi_id", label: "Uso CFDI",
            id_selected: $this->registro['cat_sat_uso_cfdi_id'], cols: 12);
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_metodo_pago_id",
            label: "Método de Pago", id_selected: $this->registro['cat_sat_metodo_pago_id']);
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_forma_pago_id",
            label: "Forma Pago", id_selected: $this->registro['cat_sat_forma_pago_id']);
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_tipo_de_comprobante_id",
            label: "Tipo de Comprobante", id_selected: $this->registro['cat_sat_tipo_de_comprobante_id']);
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_moneda_id",
            label: "Moneda", id_selected: $this->registro['cat_sat_moneda_id']);


        $base = $this->base_upd(keys_selects: $keys_selects, not_actions: array(__FUNCTION__), params: array(), params_ajustados: array());
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar base', data: $base, header: $header, ws: $ws);
        }

        return $r_modifica;
    }


    /*
        public function alta(bool $header, bool $ws = false): array|string
        {
            $r_alta =  parent::alta(header: false);
            if(errores::$error){
                return $this->retorno_error(mensaje: 'Error al generar template',data:  $r_alta, header: $header,ws:$ws);
            }

            $inputs = $this->genera_inputs(keys_selects:  $this->keys_selects);
            if(errores::$error){
                $error = $this->errores->error(mensaje: 'Error al generar inputs',data:  $inputs);
                print_r($error);
                die('Error');
            }

            return $r_alta;
        }

        public function asignar_propiedad(string $identificador, mixed $propiedades)
        {
            if (!array_key_exists($identificador,$this->keys_selects)){
                $this->keys_selects[$identificador] = new stdClass();
            }

            foreach ($propiedades as $key => $value){
                $this->keys_selects[$identificador]->$key = $value;
            }
        }





        private function init_inputs(): array
        {
            $identificador = "dp_pais_id";
            $propiedades = array("label" => "Pais");
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $identificador = "dp_estado_id";
            $propiedades = array("label" => "Estado", "con_registros" => false);
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $identificador = "dp_municipio_id";
            $propiedades = array("label" => "Municipio", "con_registros" => false);
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $identificador = "dp_cp_id";
            $propiedades = array("label" => "Código Postal", "con_registros" => false);
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $identificador = "dp_colonia_postal_id";
            $propiedades = array("label" => "Colonia Postal", "con_registros" => false);
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $identificador = "dp_calle_pertenece_id";
            $propiedades = array("label" => "Calle", "con_registros" => false);
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $identificador = "cat_sat_regimen_fiscal_id";
            $propiedades = array("label" => "Régimen Fiscal", "cols" => 12);
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $identificador = "cat_sat_moneda_id";
            $propiedades = array("label" => "Moneda");
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $identificador = "cat_sat_forma_pago_id";
            $propiedades = array("label" => "Forma Pago");
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $identificador = "cat_sat_uso_cfdi_id";
            $propiedades = array("label" => "Uso CFDI", "cols" => 12);
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $identificador = "cat_sat_metodo_pago_id";
            $propiedades = array("label" => "Método de Pago");
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $identificador = "cat_sat_tipo_de_comprobante_id";
            $propiedades = array("label" => "Tipo de Comprobante");
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $identificador = "com_tipo_cliente_id";
            $propiedades = array("label" => "Tipo de Cliente", "cols" => 12);
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $identificador = "codigo";
            $propiedades = array("place_holder" => "Código", "cols" => 4);
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $identificador = "rfc";
            $propiedades = array("place_holder" => "RFC");
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $identificador = "razon_social";
            $propiedades = array("place_holder" => "Razón Social", "cols" => 8);
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $identificador = "telefono";
            $propiedades = array("place_holder" => "Teléfono ");
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $identificador = "numero_exterior";
            $propiedades = array("place_holder" => "Num Ext");
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $identificador = "numero_interior";
            $propiedades = array("place_holder" => "Num Int");
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            return $this->keys_selects;
        }




        private function init_modifica2(): array|stdClass
        {
            $r_modifica =  parent::modifica(header: false);
            if(errores::$error){
                return $this->errores->error(mensaje: 'Error al generar template',data:  $r_modifica);
            }

            $calle = (new dp_calle_pertenece($this->link))->get_calle_pertenece($this->row_upd->dp_calle_pertenece_id);
            if(errores::$error){
                return $this->errores->error(mensaje: 'Error al obtener calle',data:  $calle);
            }

            $identificador = "dp_pais_id";
            $propiedades = array("id_selected" => $calle['dp_pais_id']);
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $identificador = "dp_estado_id";
            $propiedades = array("id_selected" => $calle['dp_estado_id'], "con_registros" => true,
                "filtro" => array('dp_pais.id' => $calle['dp_pais_id']));
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $identificador = "dp_municipio_id";
            $propiedades = array("id_selected" => $calle['dp_municipio_id'], "con_registros" => true,
                "filtro" => array('dp_estado.id' => $calle['dp_estado_id']));
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $identificador = "dp_cp_id";
            $propiedades = array("id_selected" => $calle['dp_cp_id'], "con_registros" => true,
                "filtro" => array('dp_estado.id' => $calle['dp_estado_id']));
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $identificador = "dp_colonia_postal_id";
            $propiedades = array("id_selected" => $calle['dp_colonia_postal_id'], "con_registros" => true,
                "filtro" => array('dp_cp.id' => $calle['dp_cp_id']));
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $identificador = "dp_calle_pertenece_id";
            $propiedades = array("id_selected" => $this->row_upd->dp_calle_pertenece_id, "con_registros" => true,
                "filtro" => array('dp_colonia_postal.id' => $calle['dp_colonia_postal_id']));
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $identificador = "cat_sat_regimen_fiscal_id";
            $propiedades = array("id_selected" => $this->row_upd->cat_sat_regimen_fiscal_id);
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $identificador = "cat_sat_regimen_fiscal_id";
            $propiedades = array("id_selected" => $this->row_upd->cat_sat_regimen_fiscal_id);
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $identificador = "cat_sat_moneda_id";
            $propiedades = array("id_selected" => $this->row_upd->cat_sat_moneda_id);
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $identificador = "cat_sat_forma_pago_id";
            $propiedades = array("id_selected" => $this->row_upd->cat_sat_forma_pago_id);
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $identificador = "cat_sat_uso_cfdi_id";
            $propiedades = array("id_selected" => $this->row_upd->cat_sat_uso_cfdi_id);
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $identificador = "cat_sat_metodo_pago_id";
            $propiedades = array("id_selected" => $this->row_upd->cat_sat_metodo_pago_id);
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $identificador = "cat_sat_tipo_de_comprobante_id";
            $propiedades = array("id_selected" => $this->row_upd->cat_sat_tipo_de_comprobante_id);
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $identificador = "com_tipo_cliente_id";
            $propiedades = array("id_selected" => $this->row_upd->com_tipo_cliente_id);
            $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

            $inputs = $this->genera_inputs(keys_selects:  $this->keys_selects);
            if(errores::$error){
                return $this->errores->error(mensaje: 'Error al inicializar inputs',data:  $inputs);
            }

            $data = new stdClass();
            $data->template = $r_modifica;
            $data->inputs = $inputs;

            return $data;
        }

        public function modifica(bool $header, bool $ws = false): array|stdClass
        {
            $base = $this->init_modifica2();
            if(errores::$error){
                return $this->retorno_error(mensaje: 'Error al maquetar datos',data:  $base,
                    header: $header,ws:$ws);
            }

            return $base->template;
        }*/
}
