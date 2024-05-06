<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\comercial\controllers;

use gamboamartin\comercial\models\com_tipo_prospecto;
use gamboamartin\errores\errores;
use gamboamartin\template\html;
use html\com_tipo_prospecto_html;
use PDO;
use stdClass;

class controlador_com_tipo_prospecto extends _base_sin_cod {

    public array|stdClass $keys_selects = array();
    public controlador_com_prospecto $controlador_com_prospecto;

    public string $link_com_prospecto_alta_bd = '';

    public function __construct(PDO $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new com_tipo_prospecto(link: $link);
        $html_ = new com_tipo_prospecto_html(html: $html);
        parent::__construct(html_: $html_,link:  $link,modelo:  $modelo, paths_conf: $paths_conf);

        $init_links = $this->init_links();
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al inicializar links',data:  $init_links);
            print_r($error);
            die('Error');
        }

        $this->controlador_com_prospecto= new controlador_com_prospecto(link:$this->link, paths_conf: $paths_conf);

        $this->childrens_data['com_prospecto']['title'] = 'Prospectos';
    }


    public function init_datatable(): stdClass
    {
        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['com_tipo_prospecto_id']['titulo'] = 'Id';
        $datatables->columns['com_tipo_prospecto_descripcion']['titulo'] = 'Tipo Prospecto';
        $datatables->columns['com_tipo_prospecto_n_prospectos']['titulo'] = 'Prospectos';

        $datatables->filtro = array();
        $datatables->filtro[] = 'com_tipo_prospecto.id';
        $datatables->filtro[] = 'com_tipo_prospecto.descripcion';

        return $datatables;
    }

    private function init_links(): array|string
    {
        $this->obj_link->genera_links(controler: $this);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al generar links para tipo cliente',data:  $this->obj_link);
        }

        $this->link_com_prospecto_alta_bd = $this->obj_link->link_alta_bd(link: $this->link, seccion: 'com_agente');
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

        $row = new stdClass();

        $row->com_tipo_agente_id = $this->registro_id;
        $disableds[] = 'com_tipo_agente_id';

        $keys_selects = $this->controlador_com_prospecto->init_selects_inputs(disableds: $disableds, row: $row);
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
        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 12, key: 'descripcion',
            keys_selects: $keys_selects, place_holder: 'Descripción');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        return $keys_selects;
    }

    public function agentes(bool $header = true, bool $ws = false): array|string
    {
        $data_view = new stdClass();
        $data_view->names = array('Id','Cod','Prospecto','Acciones');
        $data_view->keys_data = array('com_prospecto_id','com_prospecto_codigo','com_prospecto_descripcion');
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
}
