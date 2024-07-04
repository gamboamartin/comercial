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
use gamboamartin\cat_sat\models\cat_sat_forma_pago;
use gamboamartin\cat_sat\models\cat_sat_metodo_pago;
use gamboamartin\cat_sat\models\cat_sat_moneda;
use gamboamartin\cat_sat\models\cat_sat_regimen_fiscal;
use gamboamartin\cat_sat\models\cat_sat_tipo_de_comprobante;
use gamboamartin\cat_sat\models\cat_sat_tipo_persona;
use gamboamartin\cat_sat\models\cat_sat_uso_cfdi;
use gamboamartin\comercial\models\com_cliente;
use gamboamartin\comercial\models\com_email_cte;
use gamboamartin\comercial\models\com_rel_agente_cliente;
use gamboamartin\comercial\models\com_tipo_cliente;
use gamboamartin\comercial\models\com_tmp_cte_dp;
use gamboamartin\direccion_postal\controllers\_init_dps;
use gamboamartin\direccion_postal\models\dp_calle_pertenece;
use gamboamartin\direccion_postal\models\dp_municipio;
use gamboamartin\errores\errores;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\actions;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use html\com_cliente_html;
use html\com_email_cte_html;
use html\com_tmp_cte_dp_html;
use PDO;
use stdClass;
use Throwable;

class controlador_com_cliente extends _ctl_base
{
    public string $link_com_email_cte_alta_bd = '';
    public string $button_com_cliente_correo = '';

    public bool $existe_dom_tmp = false;

    public controlador_com_email_cte $controlador_com_email_cte;

    public string $link_com_rel_agente_cliente_bd = '';

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

        $init_controladores = $this->init_controladores(paths_conf: $paths_conf);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al inicializar controladores', data: $init_controladores);
            print_r($error);
            die('Error');
        }

        $configuraciones = $this->init_configuraciones();
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al inicializar configuraciones', data: $configuraciones);
            print_r($error);
            die('Error');
        }

        $init_links = $this->init_links();
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al inicializar links', data: $init_links);
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
        $this->parents_verifica[] = (new cat_sat_tipo_persona(link: $this->link));

        $this->verifica_parents_alta = true;

        $this->childrens_data['com_sucursal']['title'] = 'Sucursal';

        $link_com_email_cte_alta_bd = $this->obj_link->link_alta_bd(link: $this->link, seccion: 'com_email_cte');
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al obtener link', data: $link_com_email_cte_alta_bd);
            print_r($error);
            exit;
        }
        $this->link_com_email_cte_alta_bd = $link_com_email_cte_alta_bd;


    }

    public function alta(bool $header, bool $ws = false): array|string
    {

        $urls_js = (new _init_dps())->init_js(controler: $this);

        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al generar url js', data: $urls_js, header: $header, ws: $ws);
        }

        $r_alta = $this->init_alta();
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al inicializar alta', data: $r_alta, header: $header, ws: $ws);
        }

        $inputs = $this->data_form();
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener inputs', data: $inputs, header: $header, ws: $ws);
        }

        return $r_alta;
    }

    public function asigna_agente(bool $header, bool $ws = false, array $not_actions = array()): array|string
    {
        $this->accion_titulo = 'Asignar agente';

        $r_modifica = $this->init_modifica();
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al generar salida de template', data: $r_modifica, header: $header, ws: $ws);
        }

        $keys_selects = $this->init_selects_inputs();
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al inicializar selects', data: $keys_selects, header: $header,
                ws: $ws);
        }

        $base = $this->base_upd(keys_selects: $keys_selects, params: array(), params_ajustados: array());
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar base', data: $base, header: $header, ws: $ws);
        }

        $data_view = new stdClass();
        $data_view->names = array('Id', 'Tipo', 'Agente', 'Usuario', 'Acciones');
        $data_view->keys_data = array('com_agente_id', 'com_tipo_agente_descripcion', 'com_agente_descripcion',
            'adm_usuario_descripcion');
        $data_view->key_actions = 'acciones';
        $data_view->namespace_model = 'gamboamartin\\comercial\\models';
        $data_view->name_model_children = 'com_rel_agente_cliente';

        $contenido_table = $this->contenido_children(data_view: $data_view, next_accion: __FUNCTION__,
            not_actions: $not_actions);
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al obtener tbody', data: $contenido_table, header: $header, ws: $ws);
        }

        return $contenido_table;
    }

    public function asigna_agente_bd(bool $header, bool $ws = false): array|stdClass
    {
        $this->link->beginTransaction();

        $siguiente_view = (new actions())->init_alta_bd();
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener siguiente view', data: $siguiente_view,
                header: $header, ws: $ws);
        }

        if (isset($_POST['btn_action_next'])) {
            unset($_POST['btn_action_next']);
        }

        $registro['com_agente_id'] = $_POST['com_agente_id'];
        $registro['com_cliente_id'] = $this->registro_id;

        $proceso = (new com_rel_agente_cliente($this->link, array('com_agente')))->alta_registro(registro: $registro);
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al dar de alta relacion', data: $proceso, header: $header,
                ws: $ws);
        }

        $this->link->commit();

        if ($header) {
            $this->retorno_base(registro_id: $this->registro_id, result: $proceso,
                siguiente_view: "asigna_agente", ws: $ws);
        }
        if ($ws) {
            header('Content-Type: application/json');
            echo json_encode($proceso, JSON_THROW_ON_ERROR);
            exit;
        }
        $proceso->siguiente_view = "asigna_agente";

        return $proceso;
    }


    protected function init_links(): array|string
    {
        $links = $this->obj_link->genera_links(controler: $this);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al generar links', data: $links);
            print_r($error);
            exit;
        }

        $link = $this->obj_link->get_link(seccion: "com_cliente", accion: "asigna_agente_bd");
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al recuperar link autoriza_bd', data: $link);
            print_r($error);
            exit;
        }
        $this->link_com_rel_agente_cliente_bd = $link;

        return $link;
    }


    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array('codigo', 'razon_social', 'rfc', 'telefono', 'numero_exterior', 'numero_interior',
            'cp', 'colonia', 'calle');
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
        $init_data['cat_sat_tipo_persona'] = "gamboamartin\\cat_sat";
        $init_data['com_agente'] = "gamboamartin\\comercial";
        $campos_view = $this->campos_view_base(init_data: $init_data, keys: $keys);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar campo view', data: $campos_view);
        }

        return $campos_view;
    }


    public function correo(bool $header, bool $ws = false): array|stdClass
    {

        $row_upd = $this->modelo->registro(registro_id: $this->registro_id, columnas_en_bruto: true, retorno_obj: true);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener registro', data: $row_upd);
        }


        $this->inputs = new stdClass();
        $com_cliente_id = (new com_cliente_html(html: $this->html_base))->select_com_cliente_id(cols: 12,
            con_registros: true, id_selected: $this->registro_id, link: $this->link,
            disabled: true, filtro: array('com_cliente.id' => $this->registro_id));
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar input', data: $com_cliente_id);
        }

        $this->inputs->com_cliente_id = $com_cliente_id;

        $com_cliente_rfc = (new com_cliente_html(html: $this->html_base))->input_rfc(cols: 12, row_upd: $row_upd,
            value_vacio: false, disabled: true);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar input', data: $com_cliente_rfc);
        }

        $this->inputs->com_cliente_rfc = $com_cliente_rfc;

        $com_cliente_razon_social = (new com_cliente_html(html: $this->html_base))->input_razon_social(cols: 12,
            row_upd: $row_upd, value_vacio: false, disabled: true);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar input', data: $com_cliente_rfc);
        }

        $this->inputs->com_cliente_razon_social = $com_cliente_razon_social;

        $com_email_cte_descripcion = (new com_email_cte_html(html: $this->html_base))->input_email(cols: 12,
            row_upd: new stdClass(), value_vacio: false, name: 'descripcion');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar input', data: $com_email_cte_descripcion);
        }

        $this->inputs->com_email_cte_descripcion = $com_email_cte_descripcion;

        $hidden_row_id = $this->html->hidden(name: 'com_cliente_id', value: $this->registro_id);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar input', data: $hidden_row_id);
        }

        $hidden_seccion_retorno = $this->html->hidden(name: 'seccion_retorno', value: $this->tabla);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar input', data: $hidden_seccion_retorno);
        }
        $hidden_id_retorno = $this->html->hidden(name: 'id_retorno', value: $this->registro_id);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar input', data: $hidden_id_retorno);
        }

        $this->inputs->hidden_row_id = $hidden_row_id;
        $this->inputs->hidden_seccion_retorno = $hidden_seccion_retorno;
        $this->inputs->hidden_id_retorno = $hidden_id_retorno;

        $filtro['com_cliente.id'] = $this->registro_id;

        $r_email_cte = (new com_email_cte(link: $this->link))->filtro_and(filtro: $filtro);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener correos', data: $r_email_cte);
        }

        $emails_ctes = $r_email_cte->registros;

        foreach ($emails_ctes as $indice => $email_cte) {
            $params = $this->params_button_partida(com_cliente_id: $this->registro_id);
            if (errores::$error) {
                return $this->errores->error(mensaje: 'Error al generar params', data: $params);
            }

            $link_elimina = $this->html->button_href(accion: 'elimina_bd', etiqueta: 'Eliminar',
                registro_id: $email_cte['com_email_cte_id'],
                seccion: 'com_email_cte', style: 'danger', icon: 'bi bi-trash',
                muestra_icono_btn: true, muestra_titulo_btn: false, params: $params);
            if (errores::$error) {
                return $this->errores->error(mensaje: 'Error al generar link elimina_bd para partida', data: $link_elimina);
            }
            $emails_ctes[$indice]['elimina_bd'] = $link_elimina;
        }


        $this->registros['emails_ctes'] = $emails_ctes;


        $button_com_cliente_correo = $this->html->button_href(accion: 'modifica', etiqueta: 'Ir a Cliente',
            registro_id: $this->registro_id,
            seccion: 'com_cliente', style: 'warning', params: array());
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al generar link', data: $button_com_cliente_correo);
        }

        $this->button_com_cliente_correo = $button_com_cliente_correo;
        return $this->inputs;
    }

    private function data_form(): array|stdClass
    {
        $keys_selects = $this->init_selects_inputs();
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar selects', data: $keys_selects);
        }

        $data_extra_cat_sat_metodo_pago[] = 'cat_sat_metodo_pago_codigo';
        $keys_selects['cat_sat_metodo_pago_id']->extra_params_keys = $data_extra_cat_sat_metodo_pago;

        $data_extra_cat_sat_forma_pago[] = 'cat_sat_forma_pago_codigo';
        $keys_selects['cat_sat_forma_pago_id']->extra_params_keys = $data_extra_cat_sat_forma_pago;


        $inputs = $this->inputs(keys_selects: $keys_selects);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener inputs', data: $inputs);
        }


        return $inputs;
    }

    public function get_cliente(bool $header, bool $ws = true): array|stdClass
    {
        $keys['com_cliente'] = array('id', 'descripcion', 'codigo', 'rfc');

        $salida = $this->get_out(header: $header, keys: $keys, ws: $ws);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al generar salida', data: $salida, header: $header, ws: $ws);
        }

        return $salida;
    }

    /**
     * Inicializa las configuraciones base del controler
     * @return controler
     */
    private function init_configuraciones(): controler
    {
        $this->titulo_lista = 'Registro de Clientes';

        return $this;
    }


    /**
     * Inicializa los controladores a utilizar
     * @param stdClass $paths_conf Archivos de rutas de configuracion
     * @return controler
     */
    private function init_controladores(stdClass $paths_conf): controler
    {
        $this->controlador_com_email_cte = new controlador_com_email_cte(link: $this->link, paths_conf: $paths_conf);

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

    public function init_selects_inputs(): array
    {

        $keys_selects = $this->init_selects(keys_selects: array(), key: "com_tipo_cliente_id", label: "Tipo de Cliente",
            cols: 12);

        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al integrar selector', data: $keys_selects);
        }

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "com_agente_id", label: "Agente",
            cols: 12);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al integrar selector', data: $keys_selects);
        }

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_regimen_fiscal_id",
            label: "Régimen Fiscal", cols: 12);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al integrar selector', data: $keys_selects);
        }

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_tipo_persona_id",
            label: "Tipo Persona", cols: 12);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al integrar selector', data: $keys_selects);
        }

        $keys_selects['cat_sat_regimen_fiscal_id']->columns_descripcion_select = array(
            'cat_sat_regimen_fiscal_codigo', 'cat_sat_regimen_fiscal_descripcion');


        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_pais_id", label: "País");
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al integrar selector', data: $keys_selects);
        }

        $keys_selects['dp_pais_id']->key_descripcion_select = 'dp_pais_descripcion';


        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_estado_id", label: "Estado",
            con_registros: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al integrar selector', data: $keys_selects);
        }

        $keys_selects['dp_estado_id']->key_descripcion_select = 'dp_estado_descripcion';


        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_municipio_id", label: "Municipio",
            con_registros: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al integrar selector', data: $keys_selects);
        }

        $keys_selects['dp_municipio_id']->key_descripcion_select = 'dp_municipio_descripcion';

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_uso_cfdi_id", label: "Uso CFDI",
            cols: 12);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al integrar selector', data: $keys_selects);
        }

        $keys_selects['cat_sat_uso_cfdi_id']->columns_ds = array(
            'cat_sat_uso_cfdi_codigo', 'cat_sat_uso_cfdi_descripcion');

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_metodo_pago_id",
            label: "Método de Pago");
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al integrar selector', data: $keys_selects);
        }

        $keys_selects['cat_sat_metodo_pago_id']->columns_ds = array(
            'cat_sat_metodo_pago_codigo', 'cat_sat_metodo_pago_descripcion');

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_forma_pago_id",
            label: "Forma Pago");
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al integrar selector', data: $keys_selects);
        }

        $keys_selects['cat_sat_forma_pago_id']->columns_ds = array(
            'cat_sat_forma_pago_codigo', 'cat_sat_forma_pago_descripcion');

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_tipo_de_comprobante_id",
            label: "Tipo de Comprobante");
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al integrar selector', data: $keys_selects);
        }

        $keys_selects['cat_sat_tipo_de_comprobante_id']->columns_ds = array(
            'cat_sat_tipo_de_comprobante_codigo', 'cat_sat_tipo_de_comprobante_descripcion');


        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_moneda_id",
            label: "Moneda");
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al integrar selector', data: $keys_selects);
        }

        $keys_selects['cat_sat_moneda_id']->columns_ds = array(
            'cat_sat_moneda_codigo', 'cat_sat_moneda_descripcion');

        return $keys_selects;
    }

    /**
     * Este método se utiliza para inicializar un objeto de tipo stdClass que contiene las configuraciones
     * especificas para un objeto DataTable.
     *
     * @return stdClass Este método retorna un objeto de tipo stdClass con las siguientes propiedades:
     * - columns: Es un array que contiene las columnas del DataTable.
     * - filtro: Es un array que contiene los campos que se utilizarán como filtros en el DataTable.
     * @version 20.2.0
     * @por_documentar_wiki
     */
    protected function init_datatable(): stdClass
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

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'cp',
            keys_selects: $keys_selects, place_holder: 'CP');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'colonia',
            keys_selects: $keys_selects, place_holder: 'Colonia');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'calle',
            keys_selects: $keys_selects, place_holder: 'Calle');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new _base())->keys_selects(keys_selects: $keys_selects);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        return $keys_selects;
    }

    public function modifica(bool $header, bool $ws = false): array|stdClass
    {

        $urls_js = (new _init_dps())->init_js(controler: $this);

        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al generar url js', data: $urls_js, header: $header, ws: $ws);
        }

        $r_modifica = $this->init_modifica();
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al generar salida de template', data: $r_modifica, header: $header, ws: $ws);
        }

        $dp_municipio = (new dp_municipio($this->link))->get_municipio($this->registro['dp_municipio_id']);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener dp_municipio', data: $dp_municipio);
        }

        $keys_selects = $this->init_selects(keys_selects: array(), key: "com_tipo_cliente_id", label: "Tipo de Cliente",
            id_selected: $this->registro['com_tipo_cliente_id'], cols: 12);

        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener keys_selects', data: $keys_selects);
        }

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_regimen_fiscal_id",
            label: "Régimen Fiscal", id_selected: $this->registro['cat_sat_regimen_fiscal_id'], cols: 12);

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_tipo_persona_id",
            label: "Tipo Persona", id_selected: $this->registro['cat_sat_tipo_persona_id'], cols: 12);

        $keys_selects['cat_sat_regimen_fiscal_id']->columns_ds = array(
            'cat_sat_regimen_fiscal_codigo', 'cat_sat_regimen_fiscal_descripcion');

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_pais_id", label: "País",
            id_selected: $this->registro['dp_pais_id']);

        $keys_selects['dp_pais_id']->key_descripcion_select = 'dp_pais_descripcion';

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_estado_id", label: "Estado",
            id_selected: $this->registro['dp_estado_id'], filtro: array('dp_pais.id' => $dp_municipio['dp_pais_id']));

        $keys_selects['dp_estado_id']->key_descripcion_select = 'dp_estado_descripcion';

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_municipio_id", label: "Municipio",
            id_selected: $this->registro['dp_municipio_id'], filtro: array('dp_estado.id' => $dp_municipio['dp_estado_id']));

        $keys_selects['dp_municipio_id']->key_descripcion_select = 'dp_municipio_descripcion';


        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_uso_cfdi_id", label: "Uso CFDI",
            id_selected: $this->registro['cat_sat_uso_cfdi_id'], cols: 12);

        $keys_selects['cat_sat_uso_cfdi_id']->columns_ds = array(
            'cat_sat_uso_cfdi_codigo', 'cat_sat_uso_cfdi_descripcion');


        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_metodo_pago_id",
            label: "Método de Pago", id_selected: $this->registro['cat_sat_metodo_pago_id']);

        $keys_selects['cat_sat_metodo_pago_id']->columns_ds = array(
            'cat_sat_metodo_pago_codigo', 'cat_sat_metodo_pago_descripcion');


        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_forma_pago_id",
            label: "Forma Pago", id_selected: $this->registro['cat_sat_forma_pago_id']);

        $keys_selects['cat_sat_forma_pago_id']->columns_ds = array(
            'cat_sat_forma_pago_codigo', 'cat_sat_forma_pago_descripcion');


        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_tipo_de_comprobante_id",
            label: "Tipo de Comprobante", id_selected: $this->registro['cat_sat_tipo_de_comprobante_id']);

        $keys_selects['cat_sat_tipo_de_comprobante_id']->columns_ds = array(
            'cat_sat_tipo_de_comprobante_codigo', 'cat_sat_tipo_de_comprobante_descripcion');

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_moneda_id",
            label: "Moneda", id_selected: $this->registro['cat_sat_moneda_id']);

        $keys_selects['cat_sat_moneda_id']->columns_ds = array(
            'cat_sat_moneda_codigo', 'cat_sat_moneda_descripcion');


        $this->not_actions[] = __FUNCTION__;

        $base = $this->base_upd(keys_selects: $keys_selects, params: array(), params_ajustados: array());
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar base', data: $base, header: $header, ws: $ws);
        }

        $this->link->beginTransaction();
        $data_tmp = (new com_tmp_cte_dp(link: $this->link))->genera_datos(com_cliente_id: $this->registro_id);
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(
                mensaje: 'Error al obtener com_tmp', data: $data_tmp, header: $header, ws: $ws);
        }
        $this->link->commit();


        $this->existe_dom_tmp = $data_tmp->existe_dom_tmp;


        $dp_estado = (new com_tmp_cte_dp_html(html: $this->html_base))->input_dp_estado(cols: 4, row_upd: $data_tmp->com_tmp_cte_dp, value_vacio: false);
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs', data: $dp_estado, header: $header, ws: $ws);
        }

        $this->inputs->dp_estado = $dp_estado;

        $dp_municipio = (new com_tmp_cte_dp_html(html: $this->html_base))->input_dp_municipio(cols: 4, row_upd: $data_tmp->com_tmp_cte_dp, value_vacio: false);
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs', data: $dp_estado, header: $header, ws: $ws);
        }

        $this->inputs->dp_municipio = $dp_municipio;

        $dp_cp = (new com_tmp_cte_dp_html(html: $this->html_base))->input_dp_cp(cols: 4, row_upd: $data_tmp->com_tmp_cte_dp, value_vacio: false, required: true);
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs', data: $dp_cp, header: $header, ws: $ws);
        }

        $this->inputs->dp_cp = $dp_cp;

        $dp_colonia = (new com_tmp_cte_dp_html(html: $this->html_base))->input_dp_colonia(cols: 6, row_upd: $data_tmp->com_tmp_cte_dp, value_vacio: false);
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs', data: $dp_colonia, header: $header, ws: $ws);
        }

        $this->inputs->dp_colonia = $dp_colonia;

        $dp_calle = (new com_tmp_cte_dp_html(html: $this->html_base))->input_dp_calle(cols: 6, row_upd: $data_tmp->com_tmp_cte_dp, value_vacio: false);
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs', data: $dp_calle, header: $header, ws: $ws);
        }

        $this->inputs->dp_calle = $dp_calle;

        return $r_modifica;
    }

    private function params_button_partida(int $com_cliente_id): array
    {
        $params = array();
        $params['seccion_retorno'] = 'com_cliente';
        $params['accion_retorno'] = 'correo';
        $params['id_retorno'] = $com_cliente_id;
        return $params;
    }

    public function regenera_dom(bool $header, bool $ws = false)
    {

        $this->link->beginTransaction();
        $data_tmp = (new com_tmp_cte_dp(link: $this->link))->genera_datos(com_cliente_id: $this->registro_id);
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(
                mensaje: 'Error al obtener com_tmp', data: $data_tmp, header: $header, ws: $ws);
        }
        $this->link->commit();

        if ($header) {

            $this->retorno_base(registro_id: $this->registro_id, result: $data_tmp, siguiente_view: 'modifica',
                ws: $ws, seccion_retorno: $this->tabla);
        }
        if ($ws) {
            header('Content-Type: application/json');
            try {
                echo json_encode($data_tmp, JSON_THROW_ON_ERROR);
            } catch (Throwable $e) {
                $error = (new errores())->error(mensaje: 'Error al maquetar JSON', data: $e);
                print_r($error);
            }
            exit;
        }

        return $data_tmp;


    }


}
