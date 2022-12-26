<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\comercial\controllers;

use gamboamartin\comercial\models\com_tipo_producto;
use gamboamartin\errores\errores;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\_ctl_parent_sin_codigo;
use gamboamartin\system\links_menu;

use gamboamartin\template\html;

use html\com_producto_html;
use html\com_tipo_producto_html;
use PDO;
use stdClass;

class controlador_com_tipo_producto extends _ctl_parent_sin_codigo {

    public array $keys_selects = array();

    public function __construct(PDO $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new com_tipo_producto(link: $link);
        $html_ = new com_tipo_producto_html(html: $html);
        $obj_link = new links_menu(link: $link,registro_id: $this->registro_id);

        $datatables = $this->init_datatable();
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al inicializar datatable',data: $datatables);
            print_r($error);
            die('Error');
        }

        parent::__construct(html:$html_, link: $link,modelo:  $modelo, obj_link: $obj_link, datatables: $datatables,
            paths_conf: $paths_conf);
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

    protected function inputs_children(stdClass $registro): array|stdClass{
        $select_com_tipo_producto_id = (new com_tipo_producto_html(html: $this->html_base))->select_com_tipo_producto_id(
            cols:12,con_registros: true,id_selected:  $registro->com_tipo_producto_id,link:  $this->link, disabled: true);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener select_com_tipo_producto_id',data:  $select_com_tipo_producto_id);
        }

        $com_producto_descripcion = (new com_producto_html(html: $this->html_base))->input_descripcion(
            cols:12,row_upd:  new stdClass(), value_vacio: true, place_holder: 'Producto');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener com_producto_descripcion',
                data:  $com_producto_descripcion);
        }


        $this->inputs = new stdClass();
        $this->inputs->select = new stdClass();
        $this->inputs->select->com_tipo_producto_id = $select_com_tipo_producto_id;
        $this->inputs->com_producto_descripcion = $com_producto_descripcion;

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


    public function productos(bool $header = true, bool $ws = false): array|stdClass|string
    {
        $data_view = new stdClass();
        $data_view->names = array('Id','Cod','Producto','Acciones');
        $data_view->keys_data = array('com_producto_id','com_producto_codigo','com_producto_descripcion');
        $data_view->key_actions = 'acciones';
        $data_view->namespace_model = 'gamboamartin\\comercial\\models';
        $data_view->name_model_children = 'com_producto';


        $contenido_table = $this->contenido_children(data_view: $data_view, next_accion: __FUNCTION__);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener tbody',data:  $contenido_table, header: $header,ws:  $ws);
        }

        return $contenido_table;
    }


}
