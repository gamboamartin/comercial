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
use gamboamartin\documento\models\doc_documento;
use gamboamartin\errores\errores;
use gamboamartin\plugins\files;
use gamboamartin\plugins\Importador;
use gamboamartin\template\html;
use gamboamartin\validacion\validacion;
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

    private function campo_valida(array $adm_campos, string $campo_db)
    {
        $campo_valida = array();
        foreach ($adm_campos as $adm_campo){
            if($adm_campo['adm_campo_descripcion'] === $campo_db){
                $campo_valida = $adm_campo;
                break;
            }
        }
        return $campo_valida;

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

    private function doc_tipos_doc(array $rows_xls): array
    {
        $doc_tipos_doc = array();
        foreach ($rows_xls as $row){
            $doc_tipo_documento = array();
            foreach ($_POST as $campo_db=>$campo_xls) {
                $doc_tipo_documento[$campo_db] = $row->$campo_xls;
            }
            $doc_tipos_doc[] = $doc_tipo_documento;
        }
        return $doc_tipos_doc;

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

        $modelo_doc_documento = (new doc_documento(link: $this->link));

        $doc_documento_ins = array();
        $doc_documento_ins['doc_tipo_documento_id'] = 10;
        $alta_doc = $modelo_doc_documento->alta_documento(registro: $doc_documento_ins,file: $_FILES['doc_origen']);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al insertar documento', data: $alta_doc, header: $header,
                ws: $ws, class: __CLASS__, file: __FILE__, function: __FUNCTION__, line: __LINE__);
        }



        $this->columnas_entidad = $this->modelo->data_columnas->columnas_parseadas;

        $ruta = $alta_doc->registro_obj->doc_documento_ruta_absoluta;
        $extension = $alta_doc->registro_obj->doc_extension_codigo;

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
        $columnas_xls = array();

        $adm_campos = $modelo_am_campo->campos_by_seccion(adm_seccion_descripcion: $this->tabla);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener adm_campos',data:  $adm_campos,
                header: $header,ws:  $ws);
        }

        foreach ($adm_campos as $indice=>$adm_campo){
            if($adm_campo['adm_campo_descripcion'] === 'usuario_alta_id'){
                unset($adm_campos[$indice]);
            }
            if($adm_campo['adm_campo_descripcion'] === 'usuario_update_id'){
                unset($adm_campos[$indice]);
            }
            if($adm_campo['adm_campo_descripcion'] === 'fecha_alta'){
                unset($adm_campos[$indice]);
            }
            if($adm_campo['adm_campo_descripcion'] === 'fecha_update'){
                unset($adm_campos[$indice]);
            }
            if($adm_campo['adm_campo_descripcion'] === 'predeterminado'){
                unset($adm_campos[$indice]);
            }

        }


        $columnas_calc_def = array();
        foreach ($columnas_calc as $columna_cal){
            $columna_cal_del = array();
            $columna_cal_del['value'] = $columna_cal;
            $columna_cal_del['descripcion_select'] = $columna_cal;
            $columnas_calc_def[] = $columna_cal_del;
        }


        foreach ($adm_campos as $adm_campo){


            $input = $this->html->select_catalogo(cols: 12, con_registros: false, id_selected: $adm_campo['adm_campo_descripcion'],
                modelo: $modelo_am_campo, aplica_default: false, key_descripcion_select: 'descripcion_select',
                key_value_custom: 'value', label: $adm_campo['adm_campo_descripcion'], name: $adm_campo['adm_campo_descripcion'], registros: $columnas_calc_def);

            if(errores::$error){
                return $this->retorno_error(mensaje: 'Error al generar input', data: $input, header: $header, ws: $ws,
                    class: __CLASS__, file: __FILE__, function: __FUNCTION__, line: __LINE__);
            }
            $columnas_xls[$adm_campo['adm_campo_descripcion']] = $input;

        }


        $this->columnas_calc = $columnas_xls;

        $this->link_importa_previo_muestra.='&doc_documento_id='.$alta_doc->registro_id;

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

        $columns = (new Importador())->primer_row(celda_inicio: 'A1',ruta_absoluta:  $doc_documento->ruta_absoluta);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener columns',data:  $columns, header: $header,ws:  $ws);
        }

        $rows = (new Importador())->leer_registros(ruta_absoluta:  $doc_documento->ruta_absoluta, columnas: $columns);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener rows',data:  $rows, header: $header,ws:  $ws);
        }


        unset($_POST['btn_action_next']);


        $doc_tipos_doc = $this->doc_tipos_doc(rows_xls: $rows);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener tipos de doc ',data:  $doc_tipos_doc,
                header: $header,ws:  $ws);
        }


        $modelo_adm_campo = new adm_campo(link: $this->link);

        $adm_campos = $modelo_adm_campo->campos_by_seccion(adm_seccion_descripcion: $this->tabla);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener adm_campos',data:  $adm_campos,
                header: $header,ws:  $ws);
        }
        foreach ($adm_campos as $indice=>$adm_campo){
            if($adm_campo['adm_campo_descripcion'] === 'usuario_alta_id'){
                unset($adm_campos[$indice]);
            }
            if($adm_campo['adm_campo_descripcion'] === 'usuario_update_id'){
                unset($adm_campos[$indice]);
            }
            if($adm_campo['adm_campo_descripcion'] === 'fecha_alta'){
                unset($adm_campos[$indice]);
            }
            if($adm_campo['adm_campo_descripcion'] === 'fecha_update'){
                unset($adm_campos[$indice]);
            }
            if($adm_campo['adm_campo_descripcion'] === 'predeterminado'){
                unset($adm_campos[$indice]);
            }

        }

        $tipos_doc_final = array();
        foreach ($doc_tipos_doc as $key=>$doc_tipo_doc){
            $tipos_doc_final[$key] = array();

            foreach ($doc_tipo_doc as $campo_db=>$value) {
                $tipos_doc_final[$key][$campo_db]['value'] = $value;
                $tipo_dato = $this->tipo_dato_valida(adm_campos: $adm_campos,campo_db:  $campo_db);
                if(errores::$error){
                    return $this->retorno_error(mensaje: 'Error al obtener tipo_dato',data:  $tipo_dato,
                        header: $header,ws:  $ws);
                }

                if($tipo_dato === 'BIGINT'){

                    $valida = (new validacion())->id(txt: $value);
                    $mensaje_error = 'Critico debe ser un entero positivo 1-999999999';

                    $tipos_doc_final = $this->integra_tipo_doc_final(campo_db: $campo_db, contexto_error: 'danger', key: $key,
                        mensaje: $mensaje_error, tipos_doc_final: $tipos_doc_final, valida: $valida);
                    if(errores::$error){
                        return $this->retorno_error(mensaje: 'Error al integrar tipos_doc_final',data:  $tipos_doc_final,
                            header: $header,ws:  $ws);
                    }

                }
                if($tipo_dato === 'INT'){
                    $valida = (new validacion())->id(txt: $value);
                    $mensaje_error = 'Critico debe ser un entero positivo 1-999999999';
                    $tipos_doc_final = $this->integra_tipo_doc_final(campo_db: $campo_db,contexto_error: 'danger',key:  $key,mensaje:  $mensaje_error,
                        tipos_doc_final:  $tipos_doc_final,valida:  $valida);
                    if(errores::$error){
                        return $this->retorno_error(mensaje: 'Error al integrar tipos_doc_final',data:  $tipos_doc_final,
                            header: $header,ws:  $ws);
                    }

                }
                if($tipo_dato === 'VARCHAR'){
                    $valida = $value!=='';
                    $mensaje_error = 'Posible error po campo vacio';
                    $tipos_doc_final = $this->integra_tipo_doc_final(campo_db: $campo_db,contexto_error: 'warning',key:  $key,mensaje:  $mensaje_error,
                        tipos_doc_final:  $tipos_doc_final,valida:  $valida);
                    if(errores::$error){
                        return $this->retorno_error(mensaje: 'Error al integrar tipos_doc_final',data:  $tipos_doc_final,
                            header: $header,ws:  $ws);
                    }

                }
                if($tipo_dato === 'TIMESTAMP'){
                    $valida = $value!=='';
                    $mensaje_error = 'Posible error po campo vacio';
                    $tipos_doc_final = $this->integra_tipo_doc_final(campo_db: $campo_db,contexto_error: 'warning',
                        key:  $key,mensaje:  $mensaje_error,
                        tipos_doc_final:  $tipos_doc_final,valida:  $valida);
                    if(errores::$error){
                        return $this->retorno_error(mensaje: 'Error al integrar tipos_doc_final',data:  $tipos_doc_final,
                            header: $header,ws:  $ws);
                    }
                }
                if($tipo_dato === 'TIMESTAMP'){
                    $valida = (new validacion())->valida_pattern(key: 'fecha', txt: $value);

                    $mensaje_error = 'Error formato fecha';
                    $tipos_doc_final = $this->integra_tipo_doc_final(campo_db: $campo_db,contexto_error: 'danger',
                        key:  $key,mensaje:  $mensaje_error,
                        tipos_doc_final:  $tipos_doc_final,valida:  $valida);
                    if(errores::$error){
                        return $this->retorno_error(mensaje: 'Error al integrar tipos_doc_final',data:  $tipos_doc_final,
                            header: $header,ws:  $ws);
                    }
                }

            }
        }

        $headers = array();
        foreach ($adm_campos as $adm_campo){
            $headers[] = $adm_campo['adm_campo_descripcion'];
        }


        $this->registros = $tipos_doc_final;
        $this->ths = $headers;

        return $this->inputs;
    }

    private function init_tipo_doc_final(string $campo_db, string $key, array $tipos_doc_final, bool $valida): array
    {
        $tipos_doc_final[$key][$campo_db]['exito'] = $valida;
        $tipos_doc_final[$key][$campo_db]['mensaje'] = 'valido';
        $tipos_doc_final[$key][$campo_db]['contexto'] = 'success';
        return $tipos_doc_final;

    }

    private function integra_tipo_doc_final(string $campo_db, string $contexto_error, string $key, string $mensaje, array $tipos_doc_final, bool $valida): array
    {
        $tipos_doc_final = $this->init_tipo_doc_final(campo_db: $campo_db,key:  $key,tipos_doc_final:  $tipos_doc_final,valida:  $valida);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al integrar tipos_doc_final',data:  $tipos_doc_final);
        }
        if(!$valida){
            $tipos_doc_final[$key][$campo_db]['mensaje'] = $mensaje;
            $tipos_doc_final[$key][$campo_db]['contexto'] = $contexto_error;
        }
        return $tipos_doc_final;

    }

    private function tipo_dato_valida(array $adm_campos, string $campo_db): array|string
    {
        $campo_valida = $this->campo_valida(adm_campos: $adm_campos,campo_db:  $campo_db);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener campo_valida',data:  $campo_valida);
        }

        return trim($campo_valida['adm_tipo_dato_codigo']);

    }
}
