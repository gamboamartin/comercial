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
use base\controller\init;
use gamboamartin\administrador\models\adm_usuario;
use gamboamartin\comercial\models\com_agente;
use gamboamartin\documento\models\adm_grupo;
use gamboamartin\errores\errores;
use gamboamartin\template\html;
use html\com_agente_html;
use PDO;
use stdClass;

class controlador_com_agente extends _base_sin_cod
{

    public array|stdClass $keys_selects = array();
    public controlador_com_prospecto $controlador_com_prospecto;

    public string $link_com_prospecto_alta_bd = '';

    public function __construct(PDO      $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $modelo = new com_agente(link: $link);
        $html_ = new com_agente_html(html: $html);
        parent::__construct(html_: $html_, link: $link, modelo: $modelo, paths_conf: $paths_conf);

        $init_links = $this->init_links();
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al inicializar links', data: $init_links);
            print_r($error);
            die('Error');
        }

        $this->childrens_data['com_prospecto']['title'] = 'Prospectos';
    }

    public function alta(bool $header, bool $ws = false): array|string
    {


        $r_alta = $this->init_alta();
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al inicializar alta', data: $r_alta, header: $header, ws: $ws);
        }

        $row = new stdClass();

        $inputs = $this->data_form(row: $row);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener inputs', data: $inputs, header: $header, ws: $ws);
        }

        return $r_alta;
    }

    protected function campos_view(array $inputs = array()): array
    {
        $keys = new stdClass();
        $keys->inputs = array('nombre', 'apellido_paterno', 'apellido_materno', 'user');
        $keys->passwords = array('password');
        $keys->telefonos = array('telefono');
        $keys->emails = array('email');
        $keys->selects = array();

        $init_data = array();
        $init_data['com_tipo_agente'] = "gamboamartin\\comercial";
        $init_data['adm_grupo'] = "gamboamartin\\administrador";

        $campos_view = $this->campos_view_base(init_data: $init_data, keys: $keys);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar campo view', data: $campos_view);
        }

        return $campos_view;
    }

    protected function data_form(stdClass $row): array|stdClass
    {

        $keys_selects = $this->init_selects_inputs(disableds: array(), row: $row);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar selects', data: $keys_selects);
        }

        $inputs = $this->inputs(keys_selects: $keys_selects);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener inputs', data: $inputs);
        }


        return $inputs;
    }

    protected function init_controladores(stdClass $paths_conf): controler
    {
        $this->controlador_com_prospecto = new controlador_com_prospecto(link: $this->link, paths_conf: $paths_conf);

        return $this;
    }

    public function init_datatable(): stdClass
    {
        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['com_agente_id']['titulo'] = 'Id';
        $datatables->columns['com_tipo_agente_descripcion']['titulo'] = 'Tipo';
        $datatables->columns['com_agente_descripcion']['titulo'] = 'Agente';
        $datatables->columns['adm_usuario_user']['titulo'] = 'Usuario';
        $datatables->columns['adm_usuario_telefono']['titulo'] = 'Teléfono';
        $datatables->columns['adm_usuario_email']['titulo'] = 'Correo';
        $datatables->columns['com_agente_n_prospectos']['titulo'] = 'N Prospectos';

        $datatables->filtro = array();
        $datatables->filtro[] = 'com_agente.id';
        $datatables->filtro[] = 'com_agente.descripcion';
        $datatables->filtro[] = 'adm_usuario.user';
        $datatables->filtro[] = 'adm_usuario.email';

        return $datatables;
    }

    protected function init_links(): array|string
    {
        $this->obj_link->genera_links($this);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al generar links para tipo cliente', data: $this->obj_link);
        }

        $this->link_com_prospecto_alta_bd = $this->obj_link->link_alta_bd(link: $this->link, seccion: 'com_prospecto');
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al obtener link', data: $this->link_com_prospecto_alta_bd);
            print_r($error);
            exit;
        }

        return $this->link_com_prospecto_alta_bd;
    }

    protected function inputs_children(stdClass $registro): array|stdClass
    {

        $r_template = $this->controlador_com_prospecto->alta(header: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener template', data: $r_template);
        }

        $keys_selects = $this->controlador_com_prospecto->init_selects_inputs();
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar selects', data: $keys_selects);
        }

        $inputs = $this->controlador_com_prospecto->inputs(keys_selects: $keys_selects);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener inputs', data: $inputs);
        }

        $this->inputs = $inputs;

        return $this->inputs;
    }

    protected function key_selects_txt(array $keys_selects): array
    {
        $keys_selects = (new init())->key_select_txt(cols: 4, key: 'codigo',
            keys_selects: $keys_selects, place_holder: 'Cod');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'apellido_paterno',
            keys_selects: $keys_selects, place_holder: 'Apellido Paterno');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'nombre',
            keys_selects: $keys_selects, place_holder: 'Nombre');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'apellido_materno',
            keys_selects: $keys_selects, place_holder: 'Apellido Materno');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects['apellido_materno']->required = false;

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'user',
            keys_selects: $keys_selects, place_holder: 'User');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'password',
            keys_selects: $keys_selects, place_holder: 'Pass');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'email',
            keys_selects: $keys_selects, place_holder: 'Email');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'telefono',
            keys_selects: $keys_selects, place_holder: 'Tel');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        return $keys_selects;
    }

    public function agentes(bool $header = true, bool $ws = false): array|string
    {
        $data_view = new stdClass();
        $data_view->names = array('Id', 'Cod', 'Agente', 'Acciones');
        $data_view->keys_data = array('com_agente_id', 'com_agente_codigo', 'com_agente_descripcion');
        $data_view->key_actions = 'acciones';
        $data_view->namespace_model = 'gamboamartin\\comercial\\models';
        $data_view->name_model_children = 'com_prospecto';

        $contenido_table = $this->contenido_children(data_view: $data_view, next_accion: __FUNCTION__,
            not_actions: $this->not_actions);
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al obtener tbody', data: $contenido_table, header: $header, ws: $ws);
        }

        return $contenido_table;
    }

    protected function init_selects(string   $key, array $keys_selects, string $label, int $cols = 6,
                                    bool     $con_registros = true, bool $disabled = false, array $filtro = array(),
                                    int|null $id_selected = -1, array $columns_ds =  array()): array
    {
        $keys_selects = $this->key_select(cols: $cols, con_registros: $con_registros, filtro: $filtro, key: $key,
            keys_selects: $keys_selects, id_selected: $id_selected, label: $label, columns_ds: $columns_ds, disabled: $disabled);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        return $keys_selects;
    }

    public function init_selects_inputs(array $disableds, stdClass $row): array
    {
        $modelo_preferido = $this->modelo;

        if (!isset($row->com_tipo_agente_id)) {
            $id_selected = $modelo_preferido->id_preferido_detalle(entidad_preferida: 'com_tipo_agente');
            if (errores::$error) {
                return $this->errores->error(mensaje: 'Error al maquetar id_selected', data: $id_selected);
            }
            $row->com_tipo_agente_id = $id_selected;
        }

        if (!isset($row->adm_usuario_id) &&  !isset($row->adm_grupo_id)) {
            $id_selected = (new adm_grupo(link: $this->link))->id_preferido_detalle(entidad_preferida: 'adm_grupo');
            if (errores::$error) {
                return $this->errores->error(mensaje: 'Error al maquetar id_selected', data: $id_selected);
            }
            $row->adm_grupo_id = $id_selected;
        } else {
            $adm_usuario = (new adm_usuario(link: $this->link))->registro(registro_id: $row->adm_usuario_id);
            if (errores::$error) {
                return $this->errores->error(mensaje: 'Error al obtener usuario', data: $adm_usuario);
            }
            $row->adm_grupo_id = $adm_usuario['adm_grupo_id'];
        }



        $disabled = false;
        if (in_array('com_tipo_agente_id', $disableds)) {
            $disabled = true;
        }

        $keys_selects = $this->init_selects(key: "com_tipo_agente_id", keys_selects: array(), label: "Tipo de Agente",
            cols: 12, disabled: $disabled, id_selected: $row->com_tipo_agente_id,columns_ds: array('com_tipo_agente_descripcion'));
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = $this->init_selects(key: "adm_grupo_id", keys_selects: $keys_selects, label: "Grupo de Permisos",
            cols: 12, disabled: $disabled, id_selected: $row->adm_grupo_id,columns_ds: array('adm_grupo_descripcion'));
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }


        return $keys_selects;
    }

    public function modifica(bool $header, bool $ws = false, array $keys_selects = array()): array|stdClass
    {
        $r_modifica = $this->init_modifica();
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al generar salida de template', data: $r_modifica, header: $header, ws: $ws);
        }

        $keys_selects = $this->init_selects_inputs(disableds: array(), row: $this->row_upd);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al inicializar selects', data: $keys_selects, header: $header,
                ws: $ws);
        }

        $usuario = (new adm_usuario(link: $this->link))->registro(registro_id: $this->row_upd->adm_usuario_id);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener usuario', data: $usuario, header: $header, ws: $ws);
        }

        $this->row_upd->user = $usuario['adm_usuario_user'];
        $this->row_upd->password = $usuario['adm_usuario_password'];
        $this->row_upd->telefono = $usuario['adm_usuario_telefono'];
        $this->row_upd->email = $usuario['adm_usuario_email'];

        $base = $this->base_upd(keys_selects: $keys_selects, params: array(), params_ajustados: array());
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar base', data: $base, header: $header, ws: $ws);
        }

        return $r_modifica;

    }

    final public function regenera_descripcion_select(bool $header, bool $ws = false)
    {
        $this->link->beginTransaction();
        $com_agentes = (new com_agente(link: $this->link))->registros(return_obj: true);
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener agentes', data: $com_agentes, header: $header, ws: $ws);
        }

        foreach ($com_agentes as $com_agente) {
            $regenera = (new com_agente(link: $this->link))->regenera_descripcion_select(com_agente_id: $com_agente->com_agente_id);
            if (errores::$error) {
                $this->link->rollBack();
                return $this->retorno_error(mensaje: 'Error al regenerar agentes', data: $regenera, header: $header, ws: $ws);
            }
            print_r($regenera);
            echo "<br>";
        }
        $this->link->commit();
        exit;


    }
}
