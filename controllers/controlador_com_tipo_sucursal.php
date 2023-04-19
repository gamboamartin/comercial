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
use gamboamartin\comercial\models\com_tipo_sucursal;
use gamboamartin\errores\errores;
use gamboamartin\system\_ctl_parent_sin_codigo;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use html\com_tipo_sucursal_html;
use PDO;
use stdClass;

class controlador_com_tipo_sucursal extends _base_sin_cod {

    public array|stdClass $keys_selects = array();
    public controlador_com_sucursal $controlador_com_sucursal;
    public string $link_com_sucursal_alta_bd = '';

    public function __construct(PDO $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new com_tipo_sucursal(link: $link);
        $html_ = new com_tipo_sucursal_html(html: $html);

        parent::__construct(html_: $html_,link:  $link,modelo:  $modelo, paths_conf: $paths_conf);

        $init_links = $this->init_links();
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al inicializar links',data:  $init_links);
            print_r($error);
            die('Error');
        }

        $this->childrens_data['com_sucursal']['title'] = 'Sucursales';
    }

    private function init_controladores(stdClass $paths_conf): controler
    {
        $this->controlador_com_sucursal= new controlador_com_sucursal(link:$this->link, paths_conf: $paths_conf);

        return $this;
    }

    public function init_datatable(): stdClass
    {
        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['com_tipo_sucursal_id']['titulo'] = 'Id';
        $datatables->columns['com_tipo_sucursal_descripcion']['titulo'] = 'Tipo Sucursal';
        $datatables->columns['com_tipo_sucursal_n_sucursales']['titulo'] = 'Sucursales';

        $datatables->filtro = array();
        $datatables->filtro[] = 'com_tipo_sucursal.id';
        $datatables->filtro[] = 'com_tipo_sucursal.descripcion';

        return $datatables;
    }

    private function init_links(): array|string
    {
        $this->obj_link->genera_links($this);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al generar links para tipo producto',data:  $this->obj_link);
        }

        $this->link_com_sucursal_alta_bd = $this->obj_link->link_alta_bd(link: $this->link, seccion: 'com_sucursal');
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al obtener link',data:  $this->link_com_sucursal_alta_bd);
            print_r($error);
            exit;
        }

        return $this->link_com_sucursal_alta_bd;
    }

    protected function inputs_children(stdClass $registro): array|stdClass{


        $keys_selects['com_tipo_sucursal_id'] = new stdClass();
        $keys_selects['com_tipo_sucursal_id']->id_selected = $this->registro_id;
        $keys_selects['com_tipo_sucursal_id']->disabled = true;

        $keys_selects = $this->controlador_com_sucursal->key_selects_txt($keys_selects);
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al obtener keys_selects',data:  $keys_selects);
            print_r($error);
            exit;
        }

        $this->keys_selects = $keys_selects;


        $inputs = $this->controlador_com_sucursal->inputs(keys_selects: $this->keys_selects);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener inputs',data:  $inputs);
        }

        $this->inputs = new stdClass();
        $this->inputs->select = new stdClass();
        $this->inputs->select->com_cliente_id = $inputs->com_cliente_id;
        $this->inputs->select->dp_pais_id = $inputs->dp_pais_id;
        $this->inputs->select->dp_estado_id = $inputs->dp_estado_id;
        $this->inputs->select->dp_municipio_id = $inputs->dp_municipio_id;
        $this->inputs->select->dp_cp_id = $inputs->dp_cp_id;
        $this->inputs->select->dp_colonia_postal_id = $inputs->dp_colonia_postal_id;
        $this->inputs->select->dp_calle_pertenece_id = $inputs->dp_calle_pertenece_id;
        $this->inputs->select->com_tipo_sucursal_id = $inputs->com_tipo_sucursal_id;
        $this->inputs->com_sucursal_codigo = $inputs->codigo;
        $this->inputs->com_sucursal_numero_interior = $inputs->numero_interior;
        $this->inputs->com_sucursal_numero_exterior = $inputs->numero_exterior;
        $this->inputs->com_sucursal_nombre_contacto = $inputs->nombre_contacto;
        $this->inputs->com_sucursal_telefono_1 = $inputs->telefono_1;
        $this->inputs->com_sucursal_telefono_2 = $inputs->telefono_2;
        $this->inputs->com_sucursal_telefono_3 = $inputs->telefono_3;

        return $this->inputs;
    }

    protected function key_selects_txt(array $keys_selects): array
    {
        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 4,key: 'codigo',
            keys_selects:$keys_selects, place_holder: 'Cod');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 8,key: 'descripcion',
            keys_selects:$keys_selects, place_holder: 'Tipo Sucursal');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        return $keys_selects;
    }

    public function sucursales(bool $header = true, bool $ws = false): array|string
    {
        $data_view = new stdClass();
        $data_view->names = array('Id','Cod','Sucursal','Acciones');
        $data_view->keys_data = array('com_sucursal_id','com_sucursal_codigo','com_sucursal_descripcion');
        $data_view->key_actions = 'acciones';
        $data_view->namespace_model = 'gamboamartin\\comercial\\models';
        $data_view->name_model_children = 'com_sucursal';

        $contenido_table = $this->contenido_children(data_view: $data_view, next_accion: __FUNCTION__, not_actions: array());
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener tbody',data:  $contenido_table, header: $header,ws:  $ws);
        }

        return $contenido_table;
    }
}
