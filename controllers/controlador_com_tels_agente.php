<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\comercial\controllers;

use base\controller\init;
use gamboamartin\comercial\models\com_tels_agente;
use gamboamartin\errores\errores;
use gamboamartin\template\html;
use html\com_tels_agente_html;
use PDO;
use stdClass;

class controlador_com_tels_agente extends _base_sin_cod {

    public array|stdClass $keys_selects = array();


    public function __construct(PDO $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new com_tels_agente(link: $link);
        $html_ = new com_tels_agente_html(html: $html);
        parent::__construct(html_: $html_,link:  $link,modelo:  $modelo, paths_conf: $paths_conf);

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
        $keys->inputs = array('descripcion');
        $keys->selects = array();

        $init_data = array();
        $init_data['com_agente'] = "gamboamartin\\comercial";
        $init_data['com_tipo_tel'] = "gamboamartin\\comercial";

        $campos_view = $this->campos_view_base(init_data: $init_data, keys: $keys);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar campo view', data: $campos_view);
        }

        return $campos_view;
    }

    private function data_form(stdClass $row): array|stdClass
    {

        $keys_selects = $this->init_selects_inputs( row: $row);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar selects', data: $keys_selects);
        }

        $inputs = $this->inputs(keys_selects: $keys_selects);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener inputs', data: $inputs);
        }


        return $inputs;
    }


    public function init_datatable(): stdClass
    {
        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['com_tels_agente_id']['titulo'] = 'Id';
        $datatables->columns['com_agente_descripcion']['titulo'] = 'Agente';
        $datatables->columns['com_tipo_agente_descripcion']['titulo'] = 'Tipo Agente';
        $datatables->columns['com_tipo_tel_descripcion']['titulo'] = 'Tipo Tel';


        $datatables->filtro = array();
        $datatables->filtro[] = 'com_tels_agente.id';
        $datatables->filtro[] = 'com_agente.descripcion';
        $datatables->filtro[] = 'com_tipo_agente.descripcion';
        $datatables->filtro[] = 'com_tipo_tel.descripcion';

        return $datatables;
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


    public function init_selects_inputs(stdClass $row): array{
        $modelo_preferido = $this->modelo;

        if(!isset($row->com_agente_id)){
            $id_selected = $modelo_preferido->id_preferido_detalle(entidad_preferida: 'com_agente');
            if (errores::$error) {
                return $this->errores->error(mensaje: 'Error al maquetar id_selected', data: $id_selected);
            }
            $row->com_agente_id = $id_selected;
        }

        if(!isset($row->com_tipo_tel_id)){
            $id_selected = $modelo_preferido->id_preferido_detalle(entidad_preferida: 'com_tipo_tel');
            if (errores::$error) {
                return $this->errores->error(mensaje: 'Error al maquetar id_selected', data: $id_selected);
            }
            $row->com_tipo_tel_id = $id_selected;
        }



        $keys_selects = $this->init_selects(key: "com_agente_id", keys_selects: array(), label: 'Agente',
            cols: 12, id_selected: $row->com_agente_id);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = $this->init_selects(key: "com_tipo_tel_id", keys_selects: $keys_selects, label: "Tipo Tel",
            cols: 12, id_selected: $row->com_tipo_tel_id);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }


        return $keys_selects;
    }


    protected function key_selects_txt(array $keys_selects): array
    {

        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'descripcion',
            keys_selects:$keys_selects, place_holder: 'Tel');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
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
