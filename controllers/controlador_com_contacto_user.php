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
use gamboamartin\comercial\models\com_contacto_user;
use gamboamartin\errores\errores;
use gamboamartin\notificaciones\controllers\_plantilla;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use html\com_contacto_user_html;
use PDO;
use stdClass;

class controlador_com_contacto_user extends _ctl_base {

    public array|stdClass $keys_selects = array();

    public function __construct(PDO      $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $modelo = new com_contacto_user(link: $link);
        $html = new com_contacto_user_html(html: $html);
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

    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array('codigo', 'nombre', 'ap', 'am');
        $keys->telefonos = array('telefono');
        $keys->emails = array('correo');
        $keys->selects = array();

        $init_data = array();
        $init_data['com_contacto'] = "gamboamartin\\comercial";
        $init_data['adm_usuario'] = "gamboamartin\\administrador";
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

    final public function envia_acceso(bool $header, bool $ws = false)
    {
        $com_contacto_user = (new com_contacto_user(link: $this->link))->registro(registro_id: $this->registro_id,
            columnas_en_bruto: true,retorno_obj: true);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener contactor usuario', data: $com_contacto_user,
                header: $header, ws: $ws);
        }

        $envia = (new _plantilla())->envia_mensaje_accesos(adm_usuario_id: $com_contacto_user->adm_usuario_id,
            link:  $this->link);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al enviar accesos', data: $envia,
                header: $header, ws: $ws);
        }

        $out = $this->retorno_base(registro_id: $this->registro_id, result: $envia, siguiente_view: 'lista', ws: $ws);
        if(errores::$error){
            print_r($out);
            die('Error');
        }

        return $envia;



    }

    private function init_configuraciones(): controler
    {
        $this->titulo_lista = 'Registro de Usuarios de clientes';

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

        return $keys_selects;
    }

    public function init_datatable(): stdClass
    {
        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['com_contacto_user_id']['titulo'] = 'Id';
        $datatables->columns['com_contacto_nombre']['titulo'] = 'Contacto';
        $datatables->columns['com_contacto_ap']['titulo'] = 'AP';
        $datatables->columns['com_contacto_am']['titulo'] = 'AM';
        $datatables->columns['com_contacto_correo']['titulo'] = 'Correo';
        $datatables->columns['com_cliente_razon_social']['titulo'] = 'Cliente';
        $datatables->columns['adm_usuario_user']['titulo'] = 'User ';
        $datatables->columns['adm_grupo_descripcion']['titulo'] = 'Grupo ';

        $datatables->filtro = array();
        $datatables->filtro[] = 'com_contacto_user.id';
        $datatables->filtro[] = 'com_contacto.nombre';
        $datatables->filtro[] = 'com_contacto.ap';
        $datatables->filtro[] = 'com_contacto.am';
        $datatables->filtro[] = 'com_contacto.correo';
        $datatables->filtro[] = 'com_cliente.razon_social';
        $datatables->filtro[] = 'adm_usuario.user';
        $datatables->filtro[] = 'adm_grupo.descripcion';

        return $datatables;
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

        $keys_selects['com_contacto_id']->id_selected = $this->registro['com_contacto_id'];
        $keys_selects['adm_usuario_id']->id_selected = $this->registro['adm_usuario_id'];

        $base = $this->base_upd(keys_selects: $keys_selects, params: array(), params_ajustados: array());
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar base', data: $base, header: $header, ws: $ws);
        }

        return $r_modifica;
    }


}
