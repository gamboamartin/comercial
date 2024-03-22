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
use gamboamartin\administrador\models\adm_campo;
use gamboamartin\comercial\models\com_tipo_cliente;
use gamboamartin\errores\errores;
use gamboamartin\plugins\files;
use gamboamartin\plugins\Importador;
use gamboamartin\template\html;
use html\com_tipo_cliente_html;
use PDO;
use stdClass;

class controlador_com_tipo_cliente extends _base_sin_cod {

    public array|stdClass $keys_selects = array();
    public controlador_com_cliente $controlador_com_cliente;

    public string $link_com_cliente_alta_bd = '';

    public function __construct(PDO $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new com_tipo_cliente(link: $link);
        $html_ = new com_tipo_cliente_html(html: $html);
        parent::__construct(html_: $html_,link:  $link,modelo:  $modelo, paths_conf: $paths_conf);

        $init_links = $this->init_links();
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al inicializar links',data:  $init_links);
            print_r($error);
            die('Error');
        }

        $this->childrens_data['com_cliente']['title'] = 'Clientes';
    }

    private function init_controladores(stdClass $paths_conf): controler
    {
        $this->controlador_com_cliente= new controlador_com_cliente(link:$this->link, paths_conf: $paths_conf);

        return $this;
    }

    public function init_datatable(): stdClass
    {
        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['com_tipo_cliente_id']['titulo'] = 'Id';
        $datatables->columns['com_tipo_cliente_descripcion']['titulo'] = 'Tipo Cliente';
        $datatables->columns['com_tipo_cliente_n_clientes']['titulo'] = 'Clientes';

        $datatables->filtro = array();
        $datatables->filtro[] = 'com_tipo_cliente.id';
        $datatables->filtro[] = 'com_tipo_cliente.descripcion';

        return $datatables;
    }

    private function init_links(): array|string
    {
        $this->obj_link->genera_links($this);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al generar links para tipo cliente',data:  $this->obj_link);
        }

        $this->link_com_cliente_alta_bd = $this->obj_link->link_alta_bd(link: $this->link, seccion: 'com_cliente');
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al obtener link',data:  $this->link_com_cliente_alta_bd);
            print_r($error);
            exit;
        }

        return $this->link_com_cliente_alta_bd;
    }

    protected function inputs_children(stdClass $registro): array|stdClass{

        $r_template = $this->controlador_com_cliente->alta(header:false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener template',data:  $r_template);
        }

        $keys_selects = $this->controlador_com_cliente->init_selects_inputs();
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar selects',data:  $keys_selects);
        }

        $inputs = $this->controlador_com_cliente->inputs(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener inputs',data:  $inputs);
        }

        $this->inputs = $inputs;

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
            keys_selects:$keys_selects, place_holder: 'Tipo Cliente');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        return $keys_selects;
    }

    public function clientes(bool $header = true, bool $ws = false): array|string
    {
        $data_view = new stdClass();
        $data_view->names = array('Id','Cod','Cliente','Acciones');
        $data_view->keys_data = array('com_cliente_id','com_cliente_codigo','com_cliente_descripcion');
        $data_view->key_actions = 'acciones';
        $data_view->namespace_model = 'gamboamartin\\comercial\\models';
        $data_view->name_model_children = 'com_cliente';

        $contenido_table = $this->contenido_children(data_view: $data_view, next_accion: __FUNCTION__,
            not_actions: $this->not_actions);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener tbody',data:  $contenido_table, header: $header,ws:  $ws);
        }

        return $contenido_table;
    }

    public function importa(bool $header = true, bool $ws = false): array|stdClass
    {
        $this->inputs = new stdClass();
        $input_file = $this->html->input_file(cols: 12,name:  'doc_origen',row_upd:  new stdClass(),value_vacio:  false);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar input',data:  $input_file, header: $header,ws:  $ws);
        }

        $this->inputs->input_file = $input_file;

        return $this->inputs;
    }

    public function importa_previo(bool $header = true, bool $ws = false): array|stdClass
    {
        $doc_origen = $_FILES['doc_origen'];
        $name = $doc_origen['name'];
        $extension = (new files())->extension(archivo: $name);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener extension',data:  $extension, header: $header,ws:  $ws);
        }

        $this->columnas_entidad = $this->modelo->data_columnas->columnas_parseadas;

        $ruta = $doc_origen['tmp_name'];
        $extensiones_permitidas = array('csv','ods','xls','xlsx');


        if(!in_array($extension, $extensiones_permitidas)){
            return $this->retorno_error(mensaje: 'Error el documento no tiene una extension permitida',
                data:  $extension, header: $header,ws:  $ws);
        }

        $columnas_calc = (new Importador())->primer_row(celda_inicio: 'A1',ruta_absoluta: $ruta);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener columnas_calc',data:  $columnas_calc,
                header: $header,ws:  $ws);
        }

        $modelo_am_campo = new adm_campo(link: $this->link);
        $filtro['adm_seccion.descripcion'] = $this->tabla;
        $columns_ds[] ='adm_campo_descripcion';
        $columnas_xls = array();

        $adm_campos = $modelo_am_campo->campos_by_seccion(adm_seccion_descripcion: $this->tabla);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener adm_campos',data:  $adm_campos,
                header: $header,ws:  $ws);
        }

        $columnas_calc_def = array();
        foreach ($columnas_calc as $columna_cal){
            $columna_cal_del = array();
            $columna_cal_del['value'] = $columna_cal;
            $columna_cal_del['descripcion_select'] = $columna_cal;
            $columnas_calc_def[] = $columna_cal_del;
        }

        //print_r($columnas_calc_def);exit;

        foreach ($adm_campos as $adm_campo){


            $input = $this->html->select_catalogo(cols: 12, con_registros: false, id_selected: $adm_campo['adm_campo_descripcion'],
                modelo: $modelo_am_campo, aplica_default: false, key_descripcion_select: 'descripcion_select',
                key_value_custom: 'value', label: $adm_campo['adm_campo_descripcion'], registros: $columnas_calc_def);

            if(errores::$error){
                return $this->retorno_error(mensaje: 'Error al generar input', data: $input, header: $header, ws: $ws,
                    class: __CLASS__, file: __FILE__, function: __FUNCTION__, line: __LINE__);
            }
            $columnas_xls[$adm_campo['adm_campo_descripcion']] = $input;

        }

        /*foreach ($columnas_calc as $columna){
            $id_selected = -1;
            foreach ($adm_campos as $adm_campo){
                if($adm_campo['adm_campo_descripcion'] === $columna){
                    $id_selected = $adm_campo['adm_campo_id'];
                    break;
                }
            }
            $input = $this->html->select_catalogo(cols: 12, con_registros: true, id_selected: $id_selected,
                modelo: $modelo_am_campo, columns_ds: $columns_ds, filtro: $filtro, label: $columna);

            if(errores::$error){
                return $this->retorno_error(mensaje: 'Error al generar input',data:  $input, header: $header,ws:  $ws);
            }
            $columnas_xls[$columna] = $input;

        }*/

        $this->columnas_calc = $columnas_xls;

        return $columnas_xls;
    }
}
