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
use config\generales;
use gamboamartin\administrador\models\adm_usuario;
use gamboamartin\comercial\models\com_contacto;
use gamboamartin\comercial\models\com_contacto_user;
use gamboamartin\documento\models\adm_grupo;
use gamboamartin\errores\errores;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use html\adm_grupo_html;
use html\com_contacto_html;
use PDO;
use stdClass;

class controlador_com_contacto extends _ctl_base {

    public array|stdClass $keys_selects = array();
    public controlador_com_sucursal $controlador_com_sucursal;

    public string $button_com_contacto_modifica = '';
    public string $link_com_contacto_user_bd = '';

    public function __construct(PDO      $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $modelo = new com_contacto(link: $link);
        $html = new com_contacto_html(html: $html);
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
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al inicializar controladores',data:  $init_controladores);
            print_r($error);
            die('Error');
        }

        $configuraciones = $this->init_configuraciones();
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al inicializar configuraciones', data: $configuraciones);
            print_r($error);
            die('Error');
        }

    }

    private function adm_grupo_id_input(): array|stdClass
    {
        $generales = new generales();
        $adm_grupo_id = -1;
        if(isset($generales->grupo_contacto_usuario_id)){
            $adm_grupo_id = $generales->grupo_contacto_usuario_id;
        }

        $adm_grupo_id = (new adm_grupo_html(html: $this->html_base))->select_adm_grupo_id(cols: 12,con_registros: true,
            id_selected:  $adm_grupo_id,link:  $this->link,disabled: true);

        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener grupo_id', data: $adm_grupo_id);
        }

        $this->inputs->adm_usuario->adm_grupo_id = $adm_grupo_id;
        return $this->inputs;

    }
    public function alta(bool $header, bool $ws = false): array|string
    {
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

    private function am_input(stdClass $com_contacto): array|stdClass
    {
        $adm_usuario_df = new stdClass();
        $am_df = strtoupper(trim($com_contacto->com_contacto_am));
        $adm_usuario_df->am = $am_df;
        $am = $this->html->input_text_required(cols: 6, disabled: true, name: 'am',
            place_holder: 'AM', row_upd: $adm_usuario_df, value_vacio: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar am', data: $am_df);
        }
        $this->inputs->adm_usuario->am = $am;
        return $this->inputs;

    }

    private function ap_input(stdClass $com_contacto): array|stdClass
    {
        $adm_usuario_df = new stdClass();
        $ap_df = strtoupper(trim($com_contacto->com_contacto_ap));
        $adm_usuario_df->ap = $ap_df;
        $ap = $this->html->input_text_required(cols: 6, disabled: true, name: 'ap',
            place_holder: 'AP', row_upd: $adm_usuario_df, value_vacio: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener ap', data: $ap);
        }
        $this->inputs->adm_usuario->ap = $ap;
        return $this->inputs;

    }

    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array('codigo', 'nombre', 'ap', 'am');
        $keys->telefonos = array('telefono');
        $keys->emails = array('correo');
        $keys->selects = array();

        $init_data = array();
        $init_data['com_tipo_contacto'] = "gamboamartin\\comercial";
        $init_data['com_cliente'] = "gamboamartin\\comercial";
        $campos_view = $this->campos_view_base(init_data: $init_data, keys: $keys);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar campo view', data: $campos_view);
        }

        return $campos_view;
    }



    private function data_form(): array|stdClass
    {
        $keys_selects = $this->init_selects_inputs();
        if (errores::$error) {return $this->errores->error(mensaje: 'Error al inicializar selects', data: $keys_selects);
        }

        $inputs = $this->inputs(keys_selects: $keys_selects);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener inputs', data: $inputs);
        }

        return $inputs;
    }

    private function data_view_genera_usuario(): stdClass
    {
        $data_view = new stdClass();
        $data_view->names = array('Id', 'Usuario', 'Contacto', 'Acciones');
        $data_view->keys_data = array('adm_usuario_id', 'adm_usuario_user', 'com_contacto_descripcion');
        $data_view->key_actions = 'acciones';
        $data_view->namespace_model = 'gamboamartin\\comercial\\models';
        $data_view->name_model_children = 'com_contacto_user';

        return $data_view;

    }

    private function email_input(stdClass $com_contacto): array|stdClass
    {
        $adm_usuario_df = new stdClass();
        $email_df = strtolower(trim($com_contacto->com_contacto_correo));

        $adm_usuario_df->email = $email_df;


        $email = $this->html->input_text_required(cols: 12, disabled: true, name: 'email',
            place_holder: 'Email', row_upd: $adm_usuario_df, value_vacio: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar email', data: $email);
        }
        $this->inputs->adm_usuario->email = $email;

        return $this->inputs;

    }

    public function genera_usuario(bool $header, bool $ws = false): array|string
    {
        $this->accion_titulo = 'Genera Usuario';

        $r_modifica = $this->init_modifica();
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al generar salida de template', data: $r_modifica, header: $header, ws: $ws);
        }

        $button =  $this->html->button_href(accion: 'modifica', etiqueta: 'Ir a Contacto',
            registro_id: $this->registro_id, seccion: $this->tabla, style: 'warning', params: array());
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al generar link', data: $button);
        }

        $this->button_com_contacto_modifica = $button;

        $data_view = $this->data_view_genera_usuario();
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al generar data view', data: $data_view, header: $header, ws: $ws);
        }

        $contenido_table = $this->contenido_children(data_view: $data_view, next_accion: __FUNCTION__,
            not_actions: array());
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al obtener tbody', data: $contenido_table, header: $header, ws: $ws);
        }

        $inputs = $this->inputs_genera_usuario(com_contacto: $this->registro);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener inputs', data: $inputs, header: $header, ws: $ws);
        }

        $link_genera_usuario = $this->obj_link->link_con_id(accion:'genera_usuario_bd',link: $this->link,
            registro_id: $this->registro_id,seccion: $this->seccion);
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al obtener link', data: $link_genera_usuario, header: $header, ws: $ws);
        }

        $this->link_com_contacto_user_bd = $link_genera_usuario;

        return $contenido_table;
    }

    public function genera_usuario_bd(bool $header, bool $ws = false): array|string
    {
        $r_com_contacto_user = (new com_contacto(link: $this->link))->inserta_com_contacto_user(
            com_contacto_id: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al insertar r_com_contacto_user',data:  $r_com_contacto_user,
                header: $header,ws:  $ws,class: __CLASS__,file: __FILE__,function: __FUNCTION__,line: __LINE__);
        }
        $out = $this->out_alta(header: $header,id_retorno:  $this->registro_id,r_alta_bd:  $r_com_contacto_user,
            seccion_retorno:  $this->tabla,siguiente_view:  'genera_usuario',ws:  $ws);
        if(errores::$error){
            print_r($out);
            die('Error');
        }

        return $out;

    }

    private function init_configuraciones(): controler
    {
        $this->titulo_lista = 'Registro de Contactos';

        return $this;
    }

    private function init_controladores(stdClass $paths_conf): controler
    {
        return $this;
    }

    private function init_selects(array $keys_selects, string $key, string $label, int|null $id_selected = -1, int $cols = 6,
                                  bool  $con_registros = true, array $filtro = array(), array $columns_ds =  array()): array
    {
        $keys_selects = $this->key_select(cols: $cols, con_registros: $con_registros, filtro: $filtro, key: $key,
            keys_selects: $keys_selects, id_selected: $id_selected, label: $label, columns_ds: $columns_ds);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        return $keys_selects;
    }

    public function init_selects_inputs(): array{

        $keys_selects = $this->init_selects(keys_selects: array(), key: "com_contacto_id", label: "Contacto",
            cols: 12);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al integrar selector',data:  $keys_selects);
        }

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "adm_usuario_id", label: "Usuario",
            cols: 12,columns_ds: array('adm_usuario_user'));
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al integrar selector',data:  $keys_selects);
        }

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "com_tipo_contacto_id", label: "Tipo Contacto",
            cols: 6,columns_ds: array('com_tipo_contacto_descripcion'));
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al integrar selector',data:  $keys_selects);
        }

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "com_cliente_id", label: "Cliente",
            cols: 6,columns_ds: array('com_cliente_razon_social'));
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al integrar selector',data:  $keys_selects);
        }

        return $keys_selects;
    }

    public function init_datatable(): stdClass
    {
        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['com_contacto_id']['titulo'] = 'Id';
        $datatables->columns['com_tipo_contacto_descripcion']['titulo'] = 'Tipo';
        $datatables->columns['com_contacto_nombre']['titulo'] = 'Contacto';
        $datatables->columns['com_cliente_razon_social']['titulo'] = 'Cliente';
        $datatables->columns['com_contacto_nombre']['campos'] = array('com_contacto_nombre', 'com_contacto_ap', 'com_contacto_am');
        $datatables->columns['com_contacto_telefono']['titulo'] = 'Teléfono ';
        $datatables->columns['com_contacto_correo']['titulo'] = 'Correo';

        $datatables->filtro = array();
        $datatables->filtro[] = 'com_contacto.id';
        $datatables->filtro[] = 'com_tipo_contacto.descripcion';
        $datatables->filtro[] = 'com_contacto.nombre';
        $datatables->filtro[] = 'com_contacto.ap';
        $datatables->filtro[] = 'com_contacto.am';
        $datatables->filtro[] = 'com_cliente.razon_social';

        return $datatables;
    }

    private function inputs_genera_usuario(stdClass|array $com_contacto): array|stdClass
    {
        if(is_array($com_contacto)){
            $com_contacto =(object)$com_contacto;
        }

        $this->inputs = new stdClass();

        $user = $this->user_input(com_contacto: $com_contacto);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener user', data: $user);
        }

        $password = $this->password_input();
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener password', data: $password);
        }
        $email = $this->email_input(com_contacto: $com_contacto);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener email', data: $email);
        }
        $adm_grupo_id = $this->adm_grupo_id_input();
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener grupo_id', data: $adm_grupo_id);
        }

        $telefono = $this->telefono_input(com_contacto: $com_contacto);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener telefono', data: $telefono);
        }

        $nombre = $this->nombre_input(com_contacto: $com_contacto);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener nombre', data: $nombre);
        }
        $ap = $this->ap_input(com_contacto: $com_contacto);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener ap', data: $ap);
        }

        $am = $this->am_input(com_contacto: $com_contacto);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener am', data: $am);
        }

        return $this->inputs;

    }

    protected function key_selects_txt(array $keys_selects): array
    {
        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 4, key: 'codigo',
            keys_selects: $keys_selects, place_holder: 'Cod');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'telefono',
            keys_selects: $keys_selects, place_holder: 'Teléfono');
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

        $keys_selects['com_tipo_contacto_id']->id_selected = $this->registro['com_tipo_contacto_id'];
        $keys_selects['com_cliente_id']->id_selected = $this->registro['com_cliente_id'];

        $base = $this->base_upd(keys_selects: $keys_selects, params: array(), params_ajustados: array());
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar base', data: $base, header: $header, ws: $ws);
        }

        return $r_modifica;
    }

    private function nombre_input(stdClass $com_contacto): array|stdClass
    {
        $adm_usuario_df = new stdClass();
        $nombre_df = strtoupper(trim($com_contacto->com_contacto_nombre));
        $adm_usuario_df->nombre = $nombre_df;
        $nombre = $this->html->input_text_required(cols: 6, disabled: true, name: 'nombre',
            place_holder: 'Nombre', row_upd: $adm_usuario_df, value_vacio: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al generar nombre', data: $nombre);
        }
        $this->inputs->adm_usuario->nombre = $nombre;
        return $this->inputs;

    }







    private function password_input(): array|stdClass
    {
        $adm_usuario_df = new stdClass();
        $password_df = (new _pass())->password_df();
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener password_df', data: $password_df);
        }

        $adm_usuario_df->password = $password_df;

        $password = $this->html->input_text_required(cols: 6, disabled: false, name: 'password',
            place_holder: 'Password', row_upd:  $adm_usuario_df, value_vacio: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener password', data: $password_df);
        }
        $this->inputs->adm_usuario->password = $password;

        return $this->inputs;

    }

    private function telefono_input(stdClass$com_contacto): array|stdClass
    {
        $adm_usuario_df = new stdClass();
        $telefono_df = strtolower(trim($com_contacto->com_contacto_telefono));
        $adm_usuario_df->telefono = $telefono_df;
        $telefono = $this->html->input_text_required(cols: 6, disabled: true, name: 'telefono',
            place_holder: 'Telefono', row_upd: $adm_usuario_df, value_vacio: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener telefono', data: $telefono_df);
        }
        $this->inputs->adm_usuario->telefono = $telefono;
        return $this->inputs;

    }

    private function user_input(stdClass $com_contacto): array|stdClass
    {
        $adm_usuario_df = new stdClass();

        $user_df = $this->user_df(com_contacto: $com_contacto);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener user_df', data: $user_df);
        }

        $existe_user = (new adm_usuario(link: $this->link))->existe_user(user: $user_df);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al validar si existe user', data: $user_df);
        }
        while($existe_user) {
            $user_df = $user_df . mt_rand(1,9);
            $existe_user = (new adm_usuario(link: $this->link))->existe_user(user: $user_df);
            if (errores::$error) {
                return $this->errores->error(mensaje: 'Error al validar si existe user', data: $user_df);
            }
        }

        $adm_usuario_df->user = $user_df;
        $this->inputs->adm_usuario = new stdClass();
        $user = $this->html->input_text_required(cols: 6, disabled: false,name: 'user',place_holder: 'User',
            row_upd: $adm_usuario_df,value_vacio: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener user', data: $user);
        }

        $this->inputs->adm_usuario->user = $user;
        return $this->inputs;

    }

    private function user_df(stdClass $com_contacto): string
    {
        $user_df = strtolower(trim($com_contacto->com_contacto_nombre));
        $user_df .= ".".strtolower(trim($com_contacto->com_contacto_ap));
        return trim(str_replace('..', '', $user_df));

    }


}
