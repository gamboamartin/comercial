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
use gamboamartin\comercial\models\_email;
use gamboamartin\comercial\models\com_cliente;
use gamboamartin\comercial\models\com_cliente_documento;
use gamboamartin\comercial\models\com_conf_tipo_doc_cliente;
use gamboamartin\comercial\models\com_contacto;
use gamboamartin\comercial\models\com_email_cte;
use gamboamartin\comercial\models\com_rel_agente_cliente;
use gamboamartin\comercial\models\com_tipo_cliente;
use gamboamartin\direccion_postal\controllers\_init_dps;
use gamboamartin\direccion_postal\models\dp_calle_pertenece;
use gamboamartin\direccion_postal\models\dp_municipio;
use gamboamartin\errores\errores;
use gamboamartin\notificaciones\models\not_mensaje;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\actions;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use html\com_cliente_html;
use html\com_email_cte_html;
use html\doc_tipo_documento_html;
use PDO;
use stdClass;

class controlador_com_cliente extends _ctl_base
{
    public string $link_com_email_cte_alta_bd = '';
    public string $button_com_cliente_correo = '';

    public controlador_com_email_cte $controlador_com_email_cte;

    public string $link_com_rel_agente_cliente_bd = '';
    public string $link_asigna_contacto_bd = '';

    public string $button_com_cliente_modifica = '';

    public string $link_com_cliente_documento_alta_bd = '';

    public string $link_envia_documentos = '';

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

        $this->lista_get_data = true;

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

        $documento = $this->html->input_file(cols: 12, name: 'documento', row_upd: new stdClass(), value_vacio: false,
            place_holder: 'CIF', required: false);
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs', data: $documento, header: $header, ws: $ws);
        }

        $this->inputs->documento = $documento;

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

        $agentes_asignados = (new com_rel_agente_cliente(link: $this->link))->filtro_and(columnas: array('com_agente_id'),
            filtro: array('com_cliente_id' => $this->registro_id));
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener agentes asignados', data: $agentes_asignados,
                header: $header, ws: $ws);
        }

        $agentes_asignados = $agentes_asignados->registros;
        $agentes_asignados = call_user_func_array('array_merge', array_map('array_values', $agentes_asignados));

        $keys_selects['com_agente_id']->not_in['llave'] = 'com_agente.id';
        $keys_selects['com_agente_id']->not_in['values'] = $agentes_asignados;

        $base = $this->base_upd(keys_selects: $keys_selects, params: array(), params_ajustados: array());
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar base', data: $base, header: $header, ws: $ws);
        }

        $button = $this->html->button_href(accion: 'modifica', etiqueta: 'Ir a Cliente',
            registro_id: $this->registro_id, seccion: $this->tabla, style: 'warning', params: array());
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al generar link', data: $button);
        }

        $this->button_com_cliente_modifica = $button;

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

    public function asigna_contacto(bool $header, bool $ws = false, array $not_actions = array()): array|string
    {
        $this->accion_titulo = 'Asignar contacto';

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

        $this->row_upd->telefono = '';

        $base = $this->base_upd(keys_selects: $keys_selects, params: array(), params_ajustados: array());
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar base', data: $base, header: $header, ws: $ws);
        }

        $button = $this->html->button_href(accion: 'modifica', etiqueta: 'Ir a Cliente',
            registro_id: $this->registro_id, seccion: $this->tabla, style: 'warning', params: array());
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al generar link', data: $button);
        }

        $this->button_com_cliente_modifica = $button;

        $data_view = new stdClass();
        $data_view->names = array('Id', 'Tipo', 'Contacto', 'Teléfono', 'Correo', 'Acciones');
        $data_view->keys_data = array('com_contacto_id', 'com_tipo_contacto_descripcion', 'com_contacto_descripcion',
            'com_contacto_telefono', 'com_contacto_correo');
        $data_view->key_actions = 'acciones';
        $data_view->namespace_model = 'gamboamartin\\comercial\\models';
        $data_view->name_model_children = 'com_contacto';

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

        $com_rel_agente_cliente = new com_rel_agente_cliente($this->link, array('com_agente'));
        $com_rel_agente_cliente->registro['com_agente_id'] = $_POST['com_agente_id'];
        $com_rel_agente_cliente->registro['com_cliente_id'] = $this->registro_id;

        $proceso = $com_rel_agente_cliente->alta_bd();
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

    public function asigna_contacto_bd(bool $header, bool $ws = false): array|stdClass
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

        $registro['com_tipo_contacto_id'] = $_POST['com_tipo_contacto_id'];
        $registro['com_cliente_id'] = $this->registro_id;
        $registro['nombre'] = $_POST['nombre'];
        $registro['ap'] = $_POST['ap'];
        $registro['am'] = $_POST['am'];
        $registro['telefono'] = $_POST['telefono'];
        $registro['correo'] = $_POST['correo'];

        $com_contacto = new com_contacto($this->link);
        $com_contacto->registro = $registro;
        $proceso = $com_contacto->alta_bd();
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al dar de alta relacion', data: $proceso, header: $header,
                ws: $ws);
        }

        $this->link->commit();

        if ($header) {
            $this->retorno_base(registro_id: $this->registro_id, result: $proceso,
                siguiente_view: "asigna_contacto", ws: $ws);
        }
        if ($ws) {
            header('Content-Type: application/json');
            echo json_encode($proceso, JSON_THROW_ON_ERROR);
            exit;
        }
        $proceso->siguiente_view = "asigna_contacto";

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
            $error = $this->errores->error(mensaje: 'Error al recuperar link asigna_agente_bd', data: $link);
            print_r($error);
            exit;
        }
        $this->link_com_rel_agente_cliente_bd = $link;

        $link = $this->obj_link->get_link(seccion: "com_cliente", accion: "asigna_contacto_bd");
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al recuperar link asigna_contacto_bd', data: $link);
            print_r($error);
            exit;
        }
        $this->link_asigna_contacto_bd = $link;

        $this->link_envia_documentos = $this->obj_link->link_con_id(accion: "envia_documentos", link: $this->link,
            registro_id: $this->registro_id, seccion: "com_cliente");
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al obtener link',
                data: $this->link_envia_documentos);
            print_r($error);
            exit;
        }

        return $link;
    }


    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array('codigo', 'razon_social', 'rfc', 'numero_exterior', 'numero_interior',
            'cp', 'colonia', 'calle', 'nombre', 'ap', 'am', 'asunto', 'mensaje', 'receptor', 'cc', 'cco');
        $keys->telefonos = array('telefono');
        $keys->emails = array('correo');
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
        $init_data['com_tipo_contacto'] = "gamboamartin\\comercial";
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

    protected function data_form(): array|stdClass
    {
        $keys_selects = $this->init_selects_inputs();
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar selects', data: $keys_selects);
        }

        $data_extra_cat_sat_metodo_pago[] = 'cat_sat_metodo_pago_codigo';
        $keys_selects['cat_sat_metodo_pago_id']->extra_params_keys = $data_extra_cat_sat_metodo_pago;

        $data_extra_cat_sat_forma_pago[] = 'cat_sat_forma_pago_codigo';
        $keys_selects['cat_sat_forma_pago_id']->extra_params_keys = $data_extra_cat_sat_forma_pago;

        $com_cliente_rfc = (new com_cliente_html(html: $this->html_base))->input_rfc(cols: 6, row_upd: $this->row_upd,
            value_vacio: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar input', data: $com_cliente_rfc);
        }

        $this->inputs->com_cliente_rfc = $com_cliente_rfc;


        $inputs = $this->inputs(keys_selects: $keys_selects);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener inputs', data: $inputs);
        }


        return $inputs;
    }

    final public function documentos(bool $header, bool $ws = false): array|stdClass
    {
        $template = $this->modifica(header: false);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar base', data: $template, header: $header, ws: $ws);
        }


        $keys_selects = $this->init_selects_inputs();
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar selects', data: $keys_selects);
        }

        $keys_selects['com_tipo_cliente_id']->id_selected = $this->registro['com_tipo_cliente_id'];
        $keys_selects['com_tipo_cliente_id']->disabled = true;

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 4, key: 'codigo',
            keys_selects: $keys_selects, place_holder: 'Cod');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects['codigo']->disabled = true;

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 8, key: 'razon_social',
            keys_selects: $keys_selects, place_holder: 'Razón Social');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects['razon_social']->disabled = true;

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'rfc',
            keys_selects: $keys_selects, place_holder: 'RFC');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects['rfc']->disabled = true;

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'telefono',
            keys_selects: $keys_selects, place_holder: 'Teléfono');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects['telefono']->disabled = true;

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 12, key: 'receptor',
            keys_selects: $keys_selects, place_holder: 'Receptor');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 12, key: 'asunto',
            keys_selects: $keys_selects, place_holder: 'Asunto');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 12, key: 'mensaje',
            keys_selects: $keys_selects, place_holder: 'Mensaje');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 12, key: 'cc',
            keys_selects: $keys_selects, place_holder: 'CC', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 12, key: 'cco',
            keys_selects: $keys_selects, place_holder: 'CCO', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $this->row_upd->asunto = "Envío de documentos";
        $this->row_upd->mensaje = "Se envían documentos";

        $base = $this->base_upd(keys_selects: $keys_selects, params: array(), params_ajustados: array());
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar base', data: $base, header: $header, ws: $ws);
        }

        return $template;
    }

    public function valida_campos(array $campos): array
    {
        $campos_validos = array('documentos', 'receptor', 'asunto', 'mensaje');
        $campos_faltantes = array_diff($campos_validos, array_keys($campos));
        if (!empty($campos_faltantes)) {
            $mensaje_error = 'Faltan los siguientes campos: ' . implode(', ', $campos_faltantes);
            return $this->errores->error(mensaje: $mensaje_error, data: $campos_faltantes);
        }

        return $campos;
    }

    function separar_correos(string $correos): array
    {
        if (trim($correos) === "") {
            return [];
        }

        return preg_split('/[;,]/', $correos);
    }

    public function valida_receptor(array $correos): array|bool
    {
        foreach ($correos as $receptor) {
            $validacion = (new _email($this->link))->validar_correo(correo: $receptor);
            if (!$validacion) {
                $mensaje_error = sprintf(_email::ERROR_CORREO_NO_VALIDO, $receptor);
                return $this->errores->error(mensaje: $mensaje_error, data: $mensaje_error);
            }
        }

        return true;
    }

    final public function envia_documentos(bool $header, bool $ws = false): array|stdClass
    {
        $campos_necesarios = $this->valida_campos($_POST);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al validar campos', data: $campos_necesarios,
                header: $header, ws: $ws);
        }

        $correos = $this->separar_correos(correos: $campos_necesarios['receptor']);
        if (empty($correos)) {
            $mensaje_error = 'No se encontraron correos válidos';
            return $this->errores->error(mensaje: $mensaje_error, data: $correos);
        }

        $cc = $this->separar_correos(correos: $campos_necesarios['cc'] ?? "");
        if (empty($correos)) {
            $mensaje_error = 'No se encontraron correos válidos';
            return $this->errores->error(mensaje: $mensaje_error, data: $correos);
        }

        $cco = $this->separar_correos(correos: $campos_necesarios['cco'] ?? "");
        if (empty($correos)) {
            $mensaje_error = 'No se encontraron correos válidos';
            return $this->errores->error(mensaje: $mensaje_error, data: $correos);
        }

        $valida_correos = $this->valida_receptor(correos: $correos);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al validar correos', data: $valida_correos,
                header: $header, ws: $ws);
        }

        $this->link->beginTransaction();

        $siguiente_view = (new actions())->init_alta_bd();
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener siguiente view', data: $siguiente_view,
                header: $header, ws: $ws);
        }

        $emisor = (new _email($this->link))->emisor(correo: 'factura@efacturacion.com.mx');
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener emisor', data: $emisor,
                header: $header, ws: $ws);
        }

        $mensaje = (new _email($this->link))->mensaje(asunto: $campos_necesarios['asunto'],
            mensaje: $campos_necesarios['mensaje'], emisor: $emisor['not_emisor_id']);
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener mensaje', data: $mensaje,
                header: $header, ws: $ws);
        }

        foreach ($correos as $correo) {
            $receptor = (new _email($this->link))->receptor(correo: $correo);
            if (errores::$error) {
                $this->link->rollBack();
                return $this->retorno_error(mensaje: 'Error al obtener receptor', data: $receptor,
                    header: $header, ws: $ws);
            }

            $mensaje_receptor = (new _email($this->link))->mensaje_receptor(mensaje: $mensaje['not_mensaje_id'],
                receptor: $receptor['not_receptor_id']);
            if (errores::$error) {
                $this->link->rollBack();
                return $this->retorno_error(mensaje: 'Error al obtener mensaje receptor', data: $mensaje_receptor,
                    header: $header, ws: $ws);
            }
        }

        $documentos_seleccionados = explode(',', $campos_necesarios['documentos']);
        $documentos = array();

        foreach ($documentos_seleccionados as $documento) {
            $registro = (new com_cliente_documento($this->link))->registro(registro_id: $documento, columnas: ['doc_documento_id']);
            if (errores::$error) {
                $this->link->rollBack();
                return $this->retorno_error(mensaje: 'Error al obtener documento', data: $registro,
                    header: $header, ws: $ws);
            }
            $documentos[] = $registro['doc_documento_id'];
        }

        $r_alta_doc_etapa = new stdClass();

        $mensaje_adjuntos = (new _email($this->link))->adjuntos(mensaje: $mensaje['not_mensaje_id'],
            documentos: $documentos);
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener adjuntos', data: $mensaje_adjuntos,
                header: $header, ws: $ws);
        }

        $mensaje_enviado = (new not_mensaje($this->link))->envia_mensaje(not_mensaje_id: $mensaje['not_mensaje_id'],
            cc: $cc, cco: $cco);
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al enviar mensaje', data: $mensaje_enviado,
                header: $header, ws: $ws);
        }

        $this->link->commit();

        if ($header) {
            $this->retorno_base(registro_id: $this->registro_id, result: $r_alta_doc_etapa,
                siguiente_view: "documentos", ws: $ws);
        }
        if ($ws) {
            header('Content-Type: application/json');
            echo json_encode($r_alta_doc_etapa, JSON_THROW_ON_ERROR);
            exit;
        }
        $r_alta_doc_etapa->siguiente_view = "documentos";

        return $r_alta_doc_etapa;
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

    public function error_especifico($error_array): string
    {
        while (isset($error_array['data']) && is_array($error_array['data']) && isset($error_array['data']['mensaje'])) {
            $error_array = $error_array['data'];
        }
        return $error_array['mensaje_limpio'] ?? 'No se encontró un mensaje de error';
    }

    public function leer_qr(bool $header, bool $ws = false): array
    {
        $registros = (new com_cliente($this->link))->leer_codigo_qr();
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al leer el código QR del documento PDF', data: $registros,
                header: $header, ws: $ws);
        }

        $salida['draw'] = count($registros);
        $salida['recordsTotal'] = count($registros);
        $salida['recordsFiltered'] = count($registros);
        $salida['data'] = $registros;

        header('Content-Type: application/json');
        echo json_encode($salida);
        exit;
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
    protected function init_selects(array $keys_selects, string $key, string $label, int|null $id_selected = -1,
                                    int   $cols = 6, bool $con_registros = true, array $filtro = array()): array
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

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "com_tipo_contacto_id", label: "Tipo de Contacto",
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
        $datatables->menu_active = true;

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

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'correo',
            keys_selects: $keys_selects, place_holder: 'Correo');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 4, key: 'nombre',
            keys_selects: $keys_selects, place_holder: 'Nombre');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 4, key: 'ap',
            keys_selects: $keys_selects, place_holder: 'Apellido Paterno');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 4, key: 'am',
            keys_selects: $keys_selects, place_holder: 'Apellido Materno', required: false);
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

        $com_cliente_rfc = (new com_cliente_html(html: $this->html_base))->input_rfc(cols: 6, row_upd: $this->row_upd,
            value_vacio: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar input', data: $com_cliente_rfc);
        }

        $this->inputs->com_cliente_rfc = $com_cliente_rfc;


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

    public function tipos_documentos(bool $header, bool $ws = false): array
    {
        $documentos = (new com_cliente($this->link))->integra_documentos(controler: $this);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar buttons', data: $documentos, header: $header, ws: $ws);
        }

        $salida['draw'] = count($documentos);
        $salida['recordsTotal'] = count($documentos);
        $salida['recordsFiltered'] = count($documentos);
        $salida['data'] = $documentos;

        header('Content-Type: application/json');
        echo json_encode($salida);
        exit;
    }

    final public function subir_documento(bool $header, bool $ws = false)
    {
        $this->inputs = new stdClass();

        $filtro['com_cliente.id'] = $this->registro_id;
        $com_cliente_id = (new com_cliente_html(html: $this->html_base))->select_com_cliente_id(
            cols: 12, con_registros: true, id_selected: $this->registro_id, link: $this->link, filtro: $filtro);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al generar input', data: $com_cliente_id, header: $header, ws: $ws);
        }
        $this->inputs->com_cliente_id = $com_cliente_id;

        $_doc_tipo_documento_id = -1;
        $filtro = array();
        if (isset($_GET['doc_tipo_documento_id'])) {
            $_doc_tipo_documento_id = $_GET['doc_tipo_documento_id'];
            $filtro['doc_tipo_documento.id'] = $_GET['doc_tipo_documento_id'];
        }

        $doc_tipo_documento_id = (new doc_tipo_documento_html(html: $this->html_base))->select_doc_tipo_documento_id(
            cols: 12, con_registros: true, id_selected: $_doc_tipo_documento_id, link: $this->link, filtro: $filtro);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al generar input', data: $doc_tipo_documento_id, header: $header, ws: $ws);
        }
        $this->inputs->doc_tipo_documento_id = $doc_tipo_documento_id;

        $documento = $this->html->input_file(cols: 12, name: 'documento', row_upd: new stdClass(), value_vacio: false);
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs', data: $documento, header: $header, ws: $ws);
        }

        $this->inputs->documento = $documento;

        $link_alta_doc = $this->obj_link->link_alta_bd(link: $this->link, seccion: 'com_cliente_documento');
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al generar link', data: $link_alta_doc, header: $header, ws: $ws);
        }

        $this->link_com_cliente_documento_alta_bd = $link_alta_doc;

        $btn_action_next = $this->html->hidden('btn_action_next', value: 'documentos');
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al generar btn_action_next', data: $btn_action_next, header: $header, ws: $ws);
        }

        $id_retorno = $this->html->hidden('id_retorno', value: $this->registro_id);
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al generar btn_action_next', data: $btn_action_next, header: $header, ws: $ws);
        }

        $seccion_retorno = $this->html->hidden('seccion_retorno', value: $this->seccion);
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al generar btn_action_next', data: $btn_action_next, header: $header, ws: $ws);
        }

        $this->inputs->btn_action_next = $btn_action_next;
        $this->inputs->id_retorno = $id_retorno;
        $this->inputs->seccion_retorno = $seccion_retorno;
    }
}
