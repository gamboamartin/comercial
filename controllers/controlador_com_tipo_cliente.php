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
use gamboamartin\comercial\models\com_tipo_cliente;
use gamboamartin\documento\models\doc_documento;
use gamboamartin\errores\errores;
use gamboamartin\plugins\Importador;
use gamboamartin\system\_importador\_campos;
use gamboamartin\system\_importador\_doc;
use gamboamartin\system\_importador\_xls;
use gamboamartin\template\html;
use gamboamartin\validacion\validacion;
use html\com_tipo_cliente_html;
use PDO;
use stdClass;

class controlador_com_tipo_cliente extends _base_sin_cod {

    public array|stdClass $keys_selects = array();
    public controlador_com_cliente $controlador_com_cliente;
    private doc_documento $modelo_doc_documento;

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

        $this->modelo_doc_documento = new doc_documento(link: $link);

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





    private function doc_importa(): array|stdClass
    {
        $alta_doc = (new _doc())->genera_doc_importa(doc_tipo_documento_id:10, modelo_doc_documento: $this->modelo_doc_documento);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al insertar documento', data: $alta_doc);
        }
        $ruta = $alta_doc->registro_obj->doc_documento_ruta_absoluta;
        $extension = $alta_doc->registro_obj->doc_extension_codigo;

        $data = new stdClass();
        $data->ruta = $ruta;
        $data->extension = $extension;
        $data->doc_documento_id = $alta_doc->registro_id;

        return $data;

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





    public function importa_previo(bool $header = true, bool $ws = false): array|stdClass
    {

        $doc = $this->doc_importa();
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar documento', data: $doc, header: $header,
                ws: $ws, class: __CLASS__, file: __FILE__, function: __FUNCTION__, line: __LINE__);
        }

        $valida = (new _campos())->valida_doc_importa(extension: $doc->extension);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al validar documento', data: $valida, header: $header,
                ws: $ws, class: __CLASS__, file: __FILE__, function: __FUNCTION__, line: __LINE__);
        }

        $columnas_xls = (new _xls())->columnas_xls(ruta: $doc->ruta, html_controler: $this->html, link: $this->link, tabla: $this->tabla);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar columnas_xls', data: $columnas_xls, header: $header, ws: $ws,
                class: __CLASS__, file: __FILE__, function: __FUNCTION__, line: __LINE__);
        }
        $this->columnas_calc = $columnas_xls;
        $this->link_importa_previo_muestra.='&doc_documento_id='.$doc->doc_documento_id;

        return $columnas_xls;
    }

    public function importa_previo_muestra(bool $header = true, bool $ws = false): array|stdClass
    {

        $doc_documento = (new doc_documento(link: $this->link))->registro(registro_id: $_GET['doc_documento_id'],
            columnas_en_bruto: true, retorno_obj: true);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener documento',data:  $doc_documento,
                header: $header,ws:  $ws);
        }


        $datos_calc = (new Importador())->leer(ruta_absoluta: $doc_documento->ruta_absoluta);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al leer archivo',data:  $datos_calc, header: $header,ws:  $ws);
        }

        unset($_POST['btn_action_next']);


        $doc_tipos_doc = (new _campos())->rows_importa(controler: $this, rows_xls: $datos_calc->rows);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener tipos de doc ',data:  $doc_tipos_doc,
                header: $header,ws:  $ws);
        }

        $input_params_importa = $this->html->hidden(name:'params_importa',value: $this->params_importa);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar input',data:  $input_params_importa,
                header: $header,ws:  $ws);
        }

        $this->input_params_importa = $input_params_importa;


        $adm_campos = (new _campos())->adm_campos(link: $this->link, tabla: $this->tabla);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener adm_campos',data:  $adm_campos,
                header: $header,ws:  $ws);
        }




        $tipos_doc_final = array();
        foreach ($doc_tipos_doc as $key=>$doc_tipo_doc){
            $tipos_doc_final[$key] = array();

            foreach ($doc_tipo_doc as $campo_db=>$value) {

                $tipos_doc_final[$key][$campo_db]['value'] = $value;
                $tipo_dato = (new _campos())->tipo_dato_valida(adm_campos: $adm_campos,campo_db:  $campo_db);
                if(errores::$error){
                    return $this->retorno_error(mensaje: 'Error al obtener tipo_dato',data:  $tipo_dato,
                        header: $header,ws:  $ws);
                }


                if($tipo_dato === 'BIGINT'){

                    $valida = (new validacion())->id(txt: $value);
                    $mensaje_error = 'Critico debe ser un entero positivo 1-999999999';

                    $tipos_doc_final = (new _campos())->integra_row_final(campo_db: $campo_db, contexto_error: 'danger', key: $key,
                        mensaje: $mensaje_error, rows_finals: $tipos_doc_final, valida: $valida);
                    if(errores::$error){
                        return $this->retorno_error(mensaje: 'Error al integrar tipos_doc_final',data:  $tipos_doc_final,
                            header: $header,ws:  $ws);
                    }

                }
                if($tipo_dato === 'INT'){
                    $valida = (new validacion())->id(txt: $value);
                    $mensaje_error = 'Critico debe ser un entero positivo 1-999999999';
                    $tipos_doc_final = (new _campos())->integra_row_final(campo_db: $campo_db,contexto_error: 'danger',key:  $key,mensaje:  $mensaje_error,
                        rows_finals:  $tipos_doc_final,valida:  $valida);
                    if(errores::$error){
                        return $this->retorno_error(mensaje: 'Error al integrar tipos_doc_final',data:  $tipos_doc_final,
                            header: $header,ws:  $ws);
                    }

                }
                if($tipo_dato === 'VARCHAR'){
                    $valida = $value!=='';
                    $mensaje_error = 'Posible error po campo vacio';
                    $tipos_doc_final = (new _campos())->integra_row_final(campo_db: $campo_db,contexto_error: 'warning',key:  $key,mensaje:  $mensaje_error,
                        rows_finals:  $tipos_doc_final,valida:  $valida);
                    if(errores::$error){
                        return $this->retorno_error(mensaje: 'Error al integrar tipos_doc_final',data:  $tipos_doc_final,
                            header: $header,ws:  $ws);
                    }

                }
                if($tipo_dato === 'TIMESTAMP'){
                    $valida = $value!=='';
                    $mensaje_error = 'Posible error po campo vacio';
                    $tipos_doc_final = (new _campos())->integra_row_final(campo_db: $campo_db,contexto_error: 'warning',
                        key:  $key,mensaje:  $mensaje_error,
                        rows_finals:  $tipos_doc_final,valida:  $valida);
                    if(errores::$error){
                        return $this->retorno_error(mensaje: 'Error al integrar tipos_doc_final',data:  $tipos_doc_final,
                            header: $header,ws:  $ws);
                    }
                }
                if($tipo_dato === 'TIMESTAMP'){
                    $valida = (new validacion())->valida_pattern(key: 'fecha', txt: $value);

                    $mensaje_error = 'Error formato fecha';
                    $tipos_doc_final = (new _campos())->integra_row_final(campo_db: $campo_db,contexto_error: 'danger',
                        key:  $key,mensaje:  $mensaje_error,
                        rows_finals:  $tipos_doc_final,valida:  $valida);
                    if(errores::$error){
                        return $this->retorno_error(mensaje: 'Error al integrar tipos_doc_final',data:  $tipos_doc_final,
                            header: $header,ws:  $ws);
                    }
                }

                if($campo_db === 'id'){
                    $existe = (new com_tipo_cliente(link: $this->link))->existe_by_id(registro_id: $value);
                    if(errores::$error){
                        return $this->retorno_error(mensaje: 'Error al validar si existe elemento',data:  $existe,
                            header: $header,ws:  $ws);
                    }
                    $valida = !$existe;

                    $mensaje_error = 'Identificador duplicado';

                    $tipos_doc_final = (new _campos())->integra_row_final(campo_db: $campo_db, contexto_error: 'danger', key: $key,
                        mensaje: $mensaje_error, rows_finals: $tipos_doc_final, valida: $valida);
                    if(errores::$error){
                        return $this->retorno_error(mensaje: 'Error al integrar tipos_doc_final',data:  $tipos_doc_final,
                            header: $header,ws:  $ws);
                    }

                }

                if($campo_db === 'codigo'){
                    $existe = (new com_tipo_cliente(link: $this->link))->existe_by_codigo(codigo: $value);
                    if(errores::$error){
                        return $this->retorno_error(mensaje: 'Error al validar si existe elemento',data:  $existe,
                            header: $header,ws:  $ws);
                    }
                    $valida = !$existe;

                    $mensaje_error = 'Codigo duplicado';

                    $tipos_doc_final = (new _campos())->integra_row_final(campo_db: $campo_db, contexto_error: 'danger', key: $key,
                        mensaje: $mensaje_error, rows_finals: $tipos_doc_final, valida: $valida);
                    if(errores::$error){
                        return $this->retorno_error(mensaje: 'Error al integrar tipos_doc_final',data:  $tipos_doc_final,
                            header: $header,ws:  $ws);
                    }

                }

                if($campo_db === 'codigo_bis'){
                    $filtro = array();
                    $filtro['com_tipo_cliente.codigo_bis'] = $value;
                    $existe = (new com_tipo_cliente(link: $this->link))->existe(filtro: $filtro);
                    if(errores::$error){
                        return $this->retorno_error(mensaje: 'Error al validar si existe elemento',data:  $existe,
                            header: $header,ws:  $ws);
                    }
                    $valida = !$existe;

                    $mensaje_error = 'Codigo Bis Duplicado';

                    $tipos_doc_final = (new _campos())->integra_row_final(campo_db: $campo_db, contexto_error: 'danger', key: $key,
                        mensaje: $mensaje_error, rows_finals: $tipos_doc_final, valida: $valida);
                    if(errores::$error){
                        return $this->retorno_error(mensaje: 'Error al integrar tipos_doc_final',data:  $tipos_doc_final,
                            header: $header,ws:  $ws);
                    }

                }

            }
        }

        foreach ($tipos_doc_final as $indice=>$tipo_doc){
            $input = "<input type='checkbox' name=row[$indice]>";
            $tipos_doc_final[$indice]['selecciona'] = $input;
        }


        $headers = array();
        foreach ($adm_campos as $adm_campo){
            $headers[] = $adm_campo['adm_campo_descripcion'];
        }
        $headers[] = 'Selecciona';


        $this->registros = $tipos_doc_final;
        $this->ths = $headers;


        $this->link_importa_previo_muestra_bd.='&doc_documento_id='.$_GET['doc_documento_id'];

        return $this->inputs;
    }

    public function importa_previo_muestra_bd(bool $header = true, bool $ws = false): array|stdClass
    {

        $doc_documento = (new doc_documento(link: $this->link))->registro(registro_id: $_GET['doc_documento_id'],
            columnas_en_bruto: true, retorno_obj: true);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener documento',data:  $doc_documento,
                header: $header,ws:  $ws);
        }

        $datos = (new Importador())->leer(ruta_absoluta: $doc_documento->ruta_absoluta);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener datos',data:  $datos,
                header: $header,ws:  $ws);
        }
        $params = $_POST['params_importa'];
        $params = base64_decode($params);
        $params = unserialize($params);

        $indices_rows = $_POST['row'];
        $rows_a_importar = array();
        foreach ($indices_rows as $indice=>$status){
            if($status === 'on'){
                $rows_a_importar[] = $datos->rows[$indice];
            }
        }

        $rows_a_importar_db = array();
        foreach ($rows_a_importar as $row_xls){
            $row_importar = array();
            foreach ($params as $campo_bd=>$campo_xls){
                $row_importar[$campo_bd] = $row_xls->$campo_xls;
            }
           $rows_a_importar_db[] = $row_importar;
        }

        $altas = array();

        foreach ($rows_a_importar_db as $row){
            $r_alta = (new com_tipo_cliente(link: $this->link))->alta_registro(registro: $row);
            if(errores::$error){
                return $this->retorno_error(mensaje: 'Error al inserta registro', data: $r_alta, header: $header,
                    ws: $ws, class: __CLASS__, file: __FILE__, function: __FUNCTION__, line: __LINE__);
            }
            $altas[] = $r_alta;
        }

        return $altas;
    }





}
