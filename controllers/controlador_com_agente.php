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
use gamboamartin\comercial\models\com_agente;
use gamboamartin\errores\errores;
use gamboamartin\template\html;
use html\com_agente_html;
use PDO;
use stdClass;

class controlador_com_agente extends _base_sin_cod {

    public array|stdClass $keys_selects = array();
    public controlador_com_prospecto $controlador_com_prospecto;

    public string $link_com_prospecto_alta_bd = '';

    public function __construct(PDO $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new com_agente(link: $link);
        $html_ = new com_agente_html(html: $html);
        parent::__construct(html_: $html_,link:  $link,modelo:  $modelo, paths_conf: $paths_conf);

        $init_links = $this->init_links();
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al inicializar links',data:  $init_links);
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
        $keys->inputs = array('nombre','apellido_paterno','apellido_materno','user','password','email','telefono');
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

    private function data_form(stdClass $row): array|stdClass
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

    private function init_controladores(stdClass $paths_conf): controler
    {
        $this->controlador_com_prospecto= new controlador_com_prospecto(link:$this->link, paths_conf: $paths_conf);

        return $this;
    }

    public function init_datatable(): stdClass
    {
        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['com_agente_id']['titulo'] = 'Id';
        $datatables->columns['com_agente_descripcion']['titulo'] = 'Agente';
        $datatables->columns['com_tipo_agente_descripcion']['titulo'] = 'Tipo';
        $datatables->columns['adm_usuario_user']['titulo'] = 'Usuario';
        $datatables->columns['com_agente_n_prospectos']['titulo'] = 'N Prospectos';

        $datatables->filtro = array();
        $datatables->filtro[] = 'com_agente.id';
        $datatables->filtro[] = 'com_agente.descripcion';
        $datatables->filtro[] = 'adm_usuario.user';

        return $datatables;
    }

    private function init_links(): array|string
    {
        $this->obj_link->genera_links($this);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al generar links para tipo cliente',data:  $this->obj_link);
        }

        $this->link_com_prospecto_alta_bd = $this->obj_link->link_alta_bd(link: $this->link, seccion: 'com_prospecto');
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al obtener link',data:  $this->link_com_prospecto_alta_bd);
            print_r($error);
            exit;
        }

        return $this->link_com_prospecto_alta_bd;
    }

    protected function inputs_children(stdClass $registro): array|stdClass{

        $r_template = $this->controlador_com_prospecto->alta(header:false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener template',data:  $r_template);
        }

        $keys_selects = $this->controlador_com_prospecto->init_selects_inputs();
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar selects',data:  $keys_selects);
        }

        $inputs = $this->controlador_com_prospecto->inputs(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener inputs',data:  $inputs);
        }

        $this->inputs = $inputs;

        return $this->inputs;
    }

    protected function key_selects_txt(array $keys_selects): array
    {
        $keys_selects = (new init())->key_select_txt(cols: 4,key: 'codigo',
            keys_selects:$keys_selects, place_holder: 'Cod');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }


        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'apellido_paterno',
            keys_selects:$keys_selects, place_holder: 'Apellido Paterno');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'nombre',
            keys_selects:$keys_selects, place_holder: 'Nombre');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'apellido_materno',
            keys_selects:$keys_selects, place_holder: 'Apellido Materno');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'user',
            keys_selects:$keys_selects, place_holder: 'User');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'password',
            keys_selects:$keys_selects, place_holder: 'Pass');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'email',
            keys_selects:$keys_selects, place_holder: 'Email');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'telefono',
            keys_selects:$keys_selects, place_holder: 'Tel');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        return $keys_selects;
    }

    public function agentes(bool $header = true, bool $ws = false): array|string
    {
        $data_view = new stdClass();
        $data_view->names = array('Id','Cod','Agente','Acciones');
        $data_view->keys_data = array('com_agente_id','com_agente_codigo','com_agente_descripcion');
        $data_view->key_actions = 'acciones';
        $data_view->namespace_model = 'gamboamartin\\comercial\\models';
        $data_view->name_model_children = 'com_prospecto';

        $contenido_table = $this->contenido_children(data_view: $data_view, next_accion: __FUNCTION__,
            not_actions: $this->not_actions);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener tbody',data:  $contenido_table, header: $header,ws:  $ws);
        }

        return $contenido_table;
    }

    private function init_selects(string $key, array $keys_selects, string $label, int $cols = 6,
                                  bool  $con_registros = true, bool $disabled = false,  array $filtro = array(),
                                  int|null $id_selected = -1): array
    {
        $keys_selects = $this->key_select(cols: $cols, con_registros: $con_registros, filtro: $filtro, key: $key,
            keys_selects: $keys_selects, id_selected: $id_selected, label: $label,disabled: $disabled);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        return $keys_selects;
    }
    public function init_selects_inputs(array $disableds,stdClass $row): array{
        $modelo_preferido = $this->modelo;

        if(!isset($row->com_tipo_agente_id)){
            $id_selected = $modelo_preferido->id_preferido_detalle(entidad_preferida: 'com_tipo_agente');
            if (errores::$error) {
                return $this->errores->error(mensaje: 'Error al maquetar id_selected', data: $id_selected);
            }
            $row->com_tipo_agente_id = $id_selected;
        }

        if(!isset($row->adm_grupo_id)){
            $id_selected = $modelo_preferido->id_preferido_detalle(entidad_preferida: 'adm_usuario');
            if (errores::$error) {
                return $this->errores->error(mensaje: 'Error al maquetar id_selected', data: $id_selected);
            }
            $row->adm_grupo_id = $id_selected;
        }

        $disabled = false;
        if(in_array('com_tipo_agente_id',$disableds)){
            $disabled = true;
        }

        $keys_selects = $this->init_selects(key: "com_tipo_agente_id", keys_selects: array(), label: "Tipo de Agente",
            cols: 12, disabled: $disabled, id_selected: $row->com_tipo_agente_id);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = $this->init_selects(key: "adm_grupo_id", keys_selects: $keys_selects, label: "Grupo de Permisos",
            cols: 12, disabled: $disabled, id_selected: $row->adm_grupo_id);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }


        return $keys_selects;
    }

    public function modifica(bool $header, bool $ws = false, array $keys_selects = array()): array|stdClass
    {
        $r_modifica = parent::modifica(header: $header,ws:  $ws,keys_selects:  $keys_selects); // TODO: Change the autogenerated stub
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener inputs', data: $r_modifica, header: $header, ws: $ws);
        }
        //$row = new stdClass();

        $inputs = $this->data_form(row: $this->row_upd);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener inputs', data: $inputs, header: $header, ws: $ws);
        }

        return $r_modifica;

    }
}