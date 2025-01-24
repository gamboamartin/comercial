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
use gamboamartin\comercial\models\com_direccion;
use gamboamartin\comercial\models\com_direccion_prospecto;
use gamboamartin\comercial\models\com_prospecto;
use gamboamartin\comercial\models\com_prospecto_etapa;
use gamboamartin\comercial\models\com_rel_agente;
use gamboamartin\errores\errores;
use gamboamartin\proceso\html\pr_etapa_proceso_html;
use gamboamartin\system\actions;
use gamboamartin\template\html;
use gamboamartin\validacion\validacion;
use html\com_prospecto_html;
use PDO;
use stdClass;
use Throwable;

class controlador_com_prospecto extends _base_sin_cod
{

    public array|stdClass $keys_selects = array();
    public string $link_alta_etapa = '';
    public string $link_alta_direccion = '';
    public string $link_alta_relacion = '';
    public array $etapas = array();

    public string $link_com_rel_agente_prospecto_bd = '';
    public string $button_com_prospecto_modifica = '';

    public string $hora_inicio = '';
    public string $hora_fin = '';

    public function __construct(PDO      $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $modelo = new com_prospecto(link: $link);
        $html_ = new com_prospecto_html(html: $html);
        parent::__construct(html_: $html_, link: $link, modelo: $modelo, paths_conf: $paths_conf);

        $init_links = $this->init_links();
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al inicializar links', data: $init_links);
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

        $keys_selects = $this->init_selects_inputs();
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al inicializar selects', data: $keys_selects, header: $header,
                ws: $ws);
        }

        $inputs = $this->inputs(keys_selects: $keys_selects);
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs', data: $inputs, header: $header, ws: $ws);
        }

        return $r_alta;
    }

    public function asigna_agente(bool $header, bool $ws = false, array $not_actions = array()): array|string
    {
        $this->accion_titulo = 'Asignar agente';

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

        $agentes_asignados = (new com_rel_agente(link: $this->link))->filtro_and(columnas: array('com_agente_id'),
            filtro: array('com_prospecto_id' => $this->registro_id));
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener agentes asignados', data: $agentes_asignados,
                header: $header, ws: $ws);
        }

        $agentes_asignados = $agentes_asignados->registros;
        $agentes_asignados = call_user_func_array('array_merge', array_map('array_values', $agentes_asignados));

        $keys_selects['com_agente_id']->not_in['llave'] = 'com_agente.id';
        $keys_selects['com_agente_id']->not_in['values'] = $agentes_asignados;

        $base = $this->base_upd(keys_selects: $keys_selects, params: array(), params_ajustados: array());
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar base', data: $base, header: $header, ws: $ws);
        }

        $button =  $this->html->button_href(accion: 'modifica', etiqueta: 'Ir a Prospecto',
            registro_id: $this->registro_id, seccion: $this->tabla, style: 'warning', params: array());
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al generar link', data: $button);
        }

        $this->button_com_prospecto_modifica = $button;

        $data_view = new stdClass();
        $data_view->names = array('Id', 'Tipo', 'Agente', 'Usuario', 'Acciones');
        $data_view->keys_data = array('com_agente_id', 'com_tipo_agente_descripcion', 'com_agente_descripcion',
            'adm_usuario_descripcion');
        $data_view->key_actions = 'acciones';
        $data_view->namespace_model = 'gamboamartin\\comercial\\models';
        $data_view->name_model_children = 'com_rel_agente';

        $contenido_table = $this->contenido_children(data_view: $data_view, next_accion: __FUNCTION__,
            not_actions: $not_actions);
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al obtener tbody', data: $contenido_table, header: $header, ws: $ws);
        }

        return $contenido_table;
    }

    public function asigna_agente_bd(bool $header, bool $ws = false): array|stdClass
    {
        $this->link->beginTransaction();

        $siguiente_view = (new actions())->init_alta_bd();
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener siguiente view', data: $siguiente_view,
                header: $header, ws: $ws);
        }

        if (isset($_POST['btn_action_next'])) {
            unset($_POST['btn_action_next']);
        }

        $com_rel_agente_prospecto = new com_rel_agente($this->link, array('com_agente'));
        $com_rel_agente_prospecto->registro['com_agente_id'] = $_POST['com_agente_id'];
        $com_rel_agente_prospecto->registro['com_prospecto_id'] = $this->registro_id;

        $proceso = $com_rel_agente_prospecto->alta_bd();
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al dar de alta relacion', data: $proceso, header: $header,
                ws: $ws);
        }

        $this->link->commit();

        if ($header) {
            $this->retorno_base(registro_id: $this->registro_id, result: $proceso,
                siguiente_view: "asigna_agente", ws: $ws);
        }
        if ($ws) {
            header('Content-Type: application/json');
            echo json_encode($proceso, JSON_THROW_ON_ERROR);
            exit;
        }
        $proceso->siguiente_view = "asigna_agente";

        return $proceso;
    }

    protected function campos_view(array $inputs = array()): array
    {
        $keys = new stdClass();
        $keys->inputs = array('codigo', 'descripcion', 'nombre', 'apellido_paterno', 'apellido_materno', 'telefono',
            'correo', 'razon_social', 'texto_exterior', 'texto_interior', 'titulo', 'zona_horaria');
        $keys->fechas = array('fecha_inicio', 'fecha_fin');
        $keys->selects = array();

        $init_data = array();
        $init_data['com_tipo_prospecto'] = "gamboamartin\\comercial";
        $init_data['com_agente'] = "gamboamartin\\comercial";
        $init_data['dp_pais'] = "gamboamartin\\direccion_postal";
        $init_data['dp_estado'] = "gamboamartin\\direccion_postal";
        $init_data['dp_municipio'] = "gamboamartin\\direccion_postal";
        $init_data['com_tipo_direccion'] = "gamboamartin\\comercial";
        $init_data['com_direccion'] = "gamboamartin\\comercial";
        $init_data['pr_etapa_proceso'] = "gamboamartin\\proceso";
        $init_data['adm_tipo_evento'] = "gamboamartin\\administrador";

        $campos_view = $this->campos_view_base(init_data: $init_data, keys: $keys);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar campo view', data: $campos_view);
        }

        return $campos_view;
    }

    public function convierte_en_cliente(bool $header, bool $ws = false): array|stdClass
    {

        $convierte_en_cliente = (new com_prospecto(link: $this->link))->convierte_en_cliente(com_prospecto_id: $this->registro_id);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al convertir en cliente', data: $convierte_en_cliente, header: $header, ws: $ws);
        }
        if ($header) {

            $this->retorno_base(registro_id: -1, result: $convierte_en_cliente, siguiente_view: 'lista',
                ws: $ws, seccion_retorno: $this->seccion, valida_permiso: true);
        }
        if ($ws) {
            header('Content-Type: application/json');
            try {
                echo json_encode($convierte_en_cliente, JSON_THROW_ON_ERROR);
            } catch (Throwable $e) {
                $error = (new errores())->error(mensaje: 'Error al maquetar JSON', data: $e);
                print_r($error);
            }
            exit;
        }

        return $convierte_en_cliente;
    }

    public function etapa(bool $header, bool $ws = false): array|stdClass
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

        $this->row_upd->descripcion = "";

        date_default_timezone_set('America/Mexico_City');

        $horaInicio = new \DateTime();
        $this->hora_inicio = $horaInicio->format('H:i');

        $horaFin = clone $horaInicio;
        $horaFin->modify('+1 hour');
        $this->hora_fin = $horaFin->format('H:i');

        $keys_selects['pr_etapa_proceso_id']->filtro = array('pr_proceso.descripcion' => 'PROSPECCION');
        $keys_selects['adm_tipo_evento_id']->required = false;

        $hoy = date('Y-m-d');
        $fecha = $this->html->input_fecha(cols: 12, row_upd: new stdClass(), value_vacio: false, value: $hoy);
        if (errores::$error) {
            $this->retorno_error(mensaje: 'Error al generar input fecha', data: $fecha, header: $header, ws: $ws);
        }

        $this->inputs->fecha = $fecha;

        $observaciones = $this->html->input_text(cols: 12, disabled: false, name: 'observaciones', place_holder: 'Observaciones',
            row_upd: new stdClass(), value_vacio: false, required: false);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener input',data:  $observaciones,  header: $header, ws: $ws);
        }

        $this->inputs->observaciones = $observaciones;

        $link_alta_etapa = $this->obj_link->link_con_id(
            accion: 'etapa_bd', link: $this->link, registro_id: $this->registro_id, seccion: $this->tabla);
        if (errores::$error) {
            $this->retorno_error(mensaje: 'Error al generar link', data: $link_alta_etapa, header: $header, ws: $ws);
        }

        $this->link_alta_etapa = $link_alta_etapa;

        $etapas = (new com_prospecto(link: $this->link))->etapas(com_prospecto_id: $this->registro_id);
        if (errores::$error) {
            $this->retorno_error(mensaje: 'Error al obtener etapas', data: $etapas, header: $header, ws: $ws);
        }

        $this->etapas = $etapas;

        $base = $this->base_upd(keys_selects: $keys_selects, params: array(), params_ajustados: array());
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar base', data: $base, header: $header, ws: $ws);
        }

        return $r_modifica;
    }

    public function etapa_bd(bool $header, bool $ws = false): array|stdClass
    {

        $this->link->beginTransaction();


        $com_prospecto_etapa_ins['com_prospecto_id'] = $this->registro_id;
        $com_prospecto_etapa_ins['pr_etapa_proceso_id'] = $_POST['pr_etapa_proceso_id'];
        $com_prospecto_etapa_ins['fecha'] = $_POST['fecha'];
        $com_prospecto_etapa_ins['observaciones'] = $_POST['observaciones'];

        $r_alta_com_prospecto_etapa = (new com_prospecto_etapa(link: $this->link))->alta_registro(registro: $com_prospecto_etapa_ins);
        if (errores::$error) {
            $this->link->rollBack();
            $this->retorno_error(mensaje: 'Error al insertar com_prospecto_etapa', data: $r_alta_com_prospecto_etapa, header: $header, ws: $ws);
        }
        $this->link->commit();

        if ($header) {

            $this->retorno_base(registro_id: $this->registro_id, result: $r_alta_com_prospecto_etapa, siguiente_view: 'etapa',
                ws: $ws, seccion_retorno: $this->seccion, valida_permiso: true);
        }
        if ($ws) {
            header('Content-Type: application/json');
            try {
                echo json_encode($r_alta_com_prospecto_etapa, JSON_THROW_ON_ERROR);
            } catch (Throwable $e) {
                $error = (new errores())->error(mensaje: 'Error al maquetar JSON', data: $e);
                print_r($error);
            }
            exit;
        }


        return $r_alta_com_prospecto_etapa;
    }

    public function init_datatable(): stdClass
    {
        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['com_prospecto_id']['titulo'] = 'Id';
        $datatables->columns['com_prospecto_descripcion']['titulo'] = 'Prospecto';
        $datatables->columns['com_prospecto_etapa']['titulo'] = 'Etapa';

        $datatables->filtro = array();
        $datatables->filtro[] = 'com_prospecto.id';
        $datatables->filtro[] = 'com_prospecto.descripcion';

        return $datatables;
    }

    private function init_selects(array $keys_selects, string $key, string $label, int $id_selected = -1, int $cols = 6,
                                  bool  $con_registros = true, array $filtro = array(), array $columns_ds = array()): array
    {
        $keys_selects = $this->key_select(cols: $cols, con_registros: $con_registros, filtro: $filtro, key: $key,
            keys_selects: $keys_selects, id_selected: $id_selected, label: $label,columns_ds: $columns_ds);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        return $keys_selects;
    }

    public function init_selects_inputs(): array
    {
        $keys_selects = $this->init_selects(keys_selects: array(), key: "com_tipo_prospecto_id", label: "Tipo Propecto");
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "com_agente_id", label: "Agente");
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "com_tipo_direccion_id", label: "Tipo Dirección", cols: 12);
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_pais_id", label: "País");
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_estado_id", label: "Estado",
            con_registros: false);
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_municipio_id", label: "Municipio",
            con_registros: false);

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "pr_etapa_proceso_id ", label: "Etapa",
            cols: 12, columns_ds: array('pr_etapa_descripcion'));

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "adm_tipo_evento_id", label: "Tipo Evento",
            cols: 12, columns_ds: array('adm_tipo_evento_descripcion'));

        return $this->init_selects(keys_selects: $keys_selects, key: "com_direccion_id", label: "Dirección", cols: 12);
    }

    protected function init_links(): array|string
    {
        $links = $this->obj_link->genera_links(controler: $this);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al generar links', data: $links);
            print_r($error);
            exit;
        }

        $link = $this->obj_link->get_link(seccion: "com_prospecto", accion: "alta_direccion");
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al recuperar link alta_direccion', data: $link);
            print_r($error);
            exit;
        }
        $this->link_alta_direccion = $link;

        $link = $this->obj_link->get_link(seccion: "com_prospecto", accion: "alta_relacion");
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al recuperar link alta_direccion', data: $link);
            print_r($error);
            exit;
        }
        $this->link_alta_relacion = $link;

        $link = $this->obj_link->get_link(seccion: "com_prospecto", accion: "asigna_agente_bd");
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al recuperar link asigna_agente_bd', data: $link);
            print_r($error);
            exit;
        }
        $this->link_com_rel_agente_prospecto_bd = $link;

        return $link;
    }


    protected function key_selects_txt(array $keys_selects): array
    {
        $keys_selects = (new init())->key_select_txt(cols: 4, key: 'codigo',
            keys_selects: $keys_selects, place_holder: 'Cod');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'descripcion',
            keys_selects: $keys_selects, place_holder: 'Descripción', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'nombre',
            keys_selects: $keys_selects, place_holder: 'Nombre');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'apellido_paterno',
            keys_selects: $keys_selects, place_holder: 'AP');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'apellido_materno',
            keys_selects: $keys_selects, place_holder: 'AM');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'telefono',
            keys_selects: $keys_selects, place_holder: 'Tel');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $validacion =  new validacion();
        $keys_selects['telefono']->regex = $validacion->patterns['telefono_mx_html'];

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'correo',
            keys_selects: $keys_selects, place_holder: 'Correo');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects['correo']->regex = $validacion->patterns['correo_html_base'];

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'razon_social',
            keys_selects: $keys_selects, place_holder: 'Razon Social');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'texto_exterior',
            keys_selects: $keys_selects, place_holder: 'Exterior');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'texto_interior',
            keys_selects: $keys_selects, place_holder: 'Interior');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'titulo',
            keys_selects: $keys_selects, place_holder: 'Titulo', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'zona_horaria',
            keys_selects: $keys_selects, place_holder: 'Zona Horaria', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'fecha_inicio',
            keys_selects: $keys_selects, place_holder: 'Fecha Inicio', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'fecha_fin',
            keys_selects: $keys_selects, place_holder: 'Fecha Fin', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        return $keys_selects;
    }

    public function modifica(bool $header, bool $ws = false, array $keys_selects = array()): array|stdClass
    {
        $template = parent::modifica(header: false, keys_selects: $keys_selects); // TODO: Change the autogenerated stub
        if (errores::$error) {
            $this->retorno_error(mensaje: 'Error al generar template', data: $template, header: $header, ws: $ws);
        }

        $keys_selects = $this->init_selects_inputs();
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al inicializar selects', data: $keys_selects, header: $header,
                ws: $ws);
        }

        $keys_selects['com_tipo_prospecto_id']->id_selected = $this->row_upd->com_tipo_prospecto_id;
        $keys_selects['com_agente_id']->id_selected = $this->row_upd->com_agente_id;

        $base = $this->base_upd(keys_selects: $keys_selects, params: array(), params_ajustados: array());
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar base', data: $base, header: $header, ws: $ws);
        }

        $cp = $this->html->input_text_required(cols: 6,disabled: false,name: 'cp',place_holder: 'CP',row_upd: $this->row_upd,value_vacio: false);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar cp', data: $cp, header: $header, ws: $ws);
        }

        $this->inputs->cp = $cp;

        $colonia = $this->html->input_text_required(cols: 12,disabled: false,name: 'colonia',place_holder: 'Col',row_upd: $this->row_upd,value_vacio: false);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar colonia', data: $colonia, header: $header, ws: $ws);
        }

        $this->inputs->colonia = $colonia;

        $calle = $this->html->input_text_required(cols: 12,disabled: false,name: 'calle',place_holder: 'Calle',row_upd: $this->row_upd,value_vacio: false);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar calle', data: $calle, header: $header, ws: $ws);
        }

        $this->inputs->calle = $calle;

        $com_prospecto_id = $this->html->hidden(name: 'com_prospecto_id', value: $this->registro_id);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar com_prospecto_id', data: $com_prospecto_id, header: $header, ws: $ws);
        }

        $this->inputs->com_prospecto_id = $com_prospecto_id;
        return $template;
    }

    public function alta_direccion(bool $header, bool $ws = false): array|stdClass
    {
        $this->link->beginTransaction();

        $siguiente_view = (new actions())->init_alta_bd();
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener siguiente view', data: $siguiente_view,
                header: $header, ws: $ws);
        }

        if (isset($_POST['btn_action_next'])) {
            unset($_POST['btn_action_next']);
        }

        $alta = (new com_direccion($this->link))->alta_registro(registro: $_POST);
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al dar de alta direccion', data: $alta,
                header: $header, ws: $ws);
        }

        $this->link->commit();

        if ($header) {
            $this->retorno_base(registro_id: $this->registro_id, result: $alta,
                siguiente_view: "modifica", ws: $ws);
        }
        if ($ws) {
            header('Content-Type: application/json');
            echo json_encode($alta, JSON_THROW_ON_ERROR);
            exit;
        }
        $alta->siguiente_view = "modifica";
        $alta->registro_id = $this->registro_id;

        return $alta;
    }

    public function alta_relacion(bool $header, bool $ws = false): array|stdClass
    {
        $this->link->beginTransaction();

        $siguiente_view = (new actions())->init_alta_bd();
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener siguiente view', data: $siguiente_view,
                header: $header, ws: $ws);
        }

        if (isset($_POST['btn_action_next'])) {
            unset($_POST['btn_action_next']);
        }

        $registros = array();
        $registros['com_prospecto_id'] = $this->registro_id;
        $registros['com_direccion_id'] = $_POST['com_direccion_id'];
        $alta = (new com_direccion_prospecto($this->link))->alta_registro(registro: $registros);
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al dar de alta direccion prospecto', data: $alta,
                header: $header, ws: $ws);
        }

        $this->link->commit();

        if ($header) {
            $this->retorno_base(registro_id: $this->registro_id, result: $alta,
                siguiente_view: "modifica", ws: $ws);
        }
        if ($ws) {
            header('Content-Type: application/json');
            echo json_encode($alta, JSON_THROW_ON_ERROR);
            exit;
        }
        $alta->siguiente_view = "modifica";
        $alta->registro_id = $this->registro_id;

        return $alta;
    }
}
