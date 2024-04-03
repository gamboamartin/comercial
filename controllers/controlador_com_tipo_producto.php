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
use gamboamartin\comercial\models\com_tipo_producto;
use gamboamartin\documento\models\doc_documento;
use gamboamartin\errores\errores;

use gamboamartin\template\html;

use html\com_tipo_producto_html;
use PDO;
use stdClass;

class controlador_com_tipo_producto extends _base_sin_cod {

    public array|stdClass $keys_selects = array();
    public controlador_com_producto $controlador_com_producto;

    public string $link_com_producto_alta_bd = '';

    public function __construct(PDO $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new com_tipo_producto(link: $link);
        $html_ = new com_tipo_producto_html(html: $html);


        parent::__construct(html_: $html_,link:  $link,modelo:  $modelo, paths_conf: $paths_conf);



        $init_links = $this->init_links();
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al inicializar links',data:  $init_links);
            print_r($error);
            die('Error');
        }

        $this->childrens_data['com_producto']['title'] = 'Productos';

        $this->modelo_doc_documento = new doc_documento(link: $link);

        $this->doc_tipo_documento_id = 11;
    }

    private function init_controladores(stdClass $paths_conf): controler
    {
        $this->controlador_com_producto= new controlador_com_producto(link:$this->link, paths_conf: $paths_conf);

        return $this;
    }

    public function init_datatable(): stdClass
    {
        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['com_tipo_producto_id']['titulo'] = 'Id';
        $datatables->columns['com_tipo_producto_descripcion']['titulo'] = 'Tipo Producto';
        $datatables->columns['com_tipo_producto_n_productos']['titulo'] = 'Productos';

        $datatables->filtro = array();
        $datatables->filtro[] = 'com_tipo_producto.id';
        $datatables->filtro[] = 'com_tipo_producto.descripcion';

        return $datatables;
    }

    private function init_links(): array|string
    {
        $this->obj_link->genera_links($this);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al generar links para tipo producto',data:  $this->obj_link);
        }

        $this->link_com_producto_alta_bd = $this->obj_link->link_alta_bd(link: $this->link, seccion: 'com_producto');
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al obtener link',data:  $this->link_com_producto_alta_bd);
            print_r($error);
            exit;
        }

        return $this->link_com_producto_alta_bd;
    }

    protected function inputs_children(stdClass $registro): array|stdClass{

        $this->keys_selects = $this->controlador_com_producto->key_selects_txt($this->keys_selects);

        $inputs = $this->controlador_com_producto->inputs(keys_selects: $this->keys_selects);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener inputs',data:  $inputs);
        }

        $this->inputs = new stdClass();
        $this->inputs->select = new stdClass();
        $this->inputs->select->cat_sat_tipo_producto_id = $inputs->cat_sat_tipo_producto_id;
        $this->inputs->select->cat_sat_division_producto_id = $inputs->cat_sat_division_producto_id;
        $this->inputs->select->cat_sat_grupo_producto_id = $inputs->cat_sat_grupo_producto_id;
        $this->inputs->select->cat_sat_clase_producto_id = $inputs->cat_sat_clase_producto_id;
        $this->inputs->select->cat_sat_producto_id = $inputs->cat_sat_producto_id;
        $this->inputs->select->com_tipo_producto_id = $inputs->com_tipo_producto_id;
        $this->inputs->com_producto_codigo = $inputs->codigo;
        $this->inputs->com_producto_descripcion = $inputs->descripcion;
        $this->inputs->select->cat_sat_unidad_id = $inputs->cat_sat_unidad_id;
        $this->inputs->select->cat_sat_obj_imp_id = $inputs->cat_sat_obj_imp_id;

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
            keys_selects:$keys_selects, place_holder: 'Tipo Producto');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        return $keys_selects;
    }

    public function productos(bool $header = true, bool $ws = false): array|string
    {
        $data_view = new stdClass();
        $data_view->names = array('Id','Cod','Producto','Acciones');
        $data_view->keys_data = array('com_producto_id','com_producto_codigo','com_producto_descripcion');
        $data_view->key_actions = 'acciones';
        $data_view->namespace_model = 'gamboamartin\\comercial\\models';
        $data_view->name_model_children = 'com_producto';

        $contenido_table = $this->contenido_children(data_view: $data_view, next_accion: __FUNCTION__,
            not_actions: $this->not_actions);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener tbody',data:  $contenido_table, header: $header,ws:  $ws);
        }

        return $contenido_table;
    }
}
