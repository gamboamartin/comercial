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
use gamboamartin\comercial\models\com_direccion;
use gamboamartin\direccion_postal\models\dp_calle_pertenece;
use gamboamartin\errores\errores;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use html\com_direccion_html;

use PDO;
use stdClass;

class controlador_com_direccion extends _ctl_base {

    public function __construct(PDO      $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $modelo = new com_direccion(link: $link);
        $html_ = new com_direccion_html(html: $html);
        $obj_link = new links_menu(link: $link, registro_id: $this->registro_id);

        $datatables = $this->init_datatable();
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al inicializar datatable', data: $datatables);
            print_r($error);
            die('Error');
        }

        parent::__construct(html: $html_, link: $link, modelo: $modelo, obj_link: $obj_link, datatables: $datatables,
            paths_conf: $paths_conf);

        $configuraciones = $this->init_configuraciones();
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al inicializar configuraciones', data: $configuraciones);
            print_r($error);
            die('Error');
        }

        $this->lista_get_data = true;
        $this->path_vendor_views = 'gamboa.martin/comercial';

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

    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array('codigo', 'descripcion', 'texto_exterior', 'texto_interior');
        $keys->telefonos = array();
        $keys->fechas = array();
        $keys->selects = array();

        $init_data = array();
        $init_data['dp_pais'] = "gamboamartin\\direccion_postal";
        $init_data['dp_estado'] = "gamboamartin\\direccion_postal";
        $init_data['dp_municipio'] = "gamboamartin\\direccion_postal";
        $init_data['com_tipo_direccion'] = "gamboamartin\\comercial";

        $campos_view = $this->campos_view_base(init_data: $init_data, keys: $keys);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar campo view', data: $campos_view);
        }

        return $campos_view;
    }

    private function init_configuraciones(): controler
    {
        $this->seccion_titulo = 'Direcciones';
        $this->titulo_lista = 'Registro de direcciones';

        $this->lista_get_data = true;

        return $this;
    }

    protected function init_datatable(): stdClass
    {
        $columns["com_direccion_id"]["titulo"] = "Id";
        $columns["com_tipo_direccion_descripcion"]["titulo"] = "Tipo";
        $columns["com_direccion_cp"]["titulo"] = "CP";
        $columns["com_direccion_colonia"]["titulo"] = "Col";
        $columns["com_direccion_calle"]["titulo"] = "Calle";
        $columns["com_direccion_texto_exterior"]["titulo"] = "Exterior";
        $columns["com_direccion_texto_interior"]["titulo"] = "Interior";

        $filtro = array("com_direccion.id","com_direccion.calle", "com_direccion.texto_exterior", "com_direccion.texto_interior");

        $datatables = new stdClass();
        $datatables->columns = $columns;
        $datatables->filtro = $filtro;

        return $datatables;
    }

    private function init_selects(array $keys_selects, string $key, string $label, int $id_selected = -1, int $cols = 6,
                                  bool  $con_registros = true, array $filtro = array()): array
    {
        $keys_selects = $this->key_select(cols: $cols, con_registros: $con_registros, filtro: $filtro, key: $key,
            keys_selects: $keys_selects, id_selected: $id_selected, label: $label);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        return $keys_selects;
    }

    public function init_selects_inputs(): array
    {
        $keys_selects = $this->init_selects(keys_selects: array(), key: "com_tipo_direccion_id", label: "Tipo Dirección", cols: 12);
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_pais_id", label: "País");
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_estado_id", label: "Estado",
            con_registros: false);
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_municipio_id", label: "Municipio",
            con_registros: false);
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_cp_id", label: "CP",
            con_registros: false);
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_colonia_postal_id", label: "Colonia",
            con_registros: false);
        return  $this->init_selects(keys_selects: $keys_selects, key: "dp_calle_pertenece_id", label: "Calle",
            con_registros: false);
    }

    protected function key_selects_txt(array $keys_selects): array
    {
        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 4, key: 'codigo',
            keys_selects: $keys_selects, place_holder: 'Código');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 8, key: 'descripcion',
            keys_selects: $keys_selects, place_holder: 'Descripción');
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

        return $keys_selects;
    }

    public function modifica(bool $header, bool $ws = false): array|stdClass
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


        $dp_calle_pertenece = (new dp_calle_pertenece($this->link))->registro($this->row_upd->dp_calle_pertenece_id);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener direccion', data: $dp_calle_pertenece,header: $header,
                ws: $ws);
        }

        $keys_selects['com_tipo_direccion_id']->id_selected =  $this->row_upd->com_tipo_direccion_id;

        $keys_selects['dp_pais_id']->con_registros =  true;
        $keys_selects['dp_pais_id']->id_selected =  $dp_calle_pertenece["dp_pais_id"];

        $keys_selects['dp_estado_id']->con_registros =  true;
        $keys_selects['dp_estado_id']->filtro =  array('dp_pais.id' => $dp_calle_pertenece["dp_pais_id"]);
        $keys_selects['dp_estado_id']->id_selected =  $dp_calle_pertenece["dp_estado_id"];

        $keys_selects['dp_municipio_id']->con_registros =  true;
        $keys_selects['dp_municipio_id']->filtro =  array('dp_estado.id' => $dp_calle_pertenece["dp_estado_id"]);
        $keys_selects['dp_municipio_id']->id_selected =  $dp_calle_pertenece["dp_municipio_id"];

        $keys_selects['dp_cp_id']->con_registros =  true;
        $keys_selects['dp_cp_id']->filtro =  array('dp_municipio.id' => $dp_calle_pertenece["dp_municipio_id"]);
        $keys_selects['dp_cp_id']->id_selected =  $dp_calle_pertenece["dp_cp_id"];

        $keys_selects['dp_colonia_postal_id']->con_registros =  true;
        $keys_selects['dp_colonia_postal_id']->filtro =  array('dp_cp.id' => $dp_calle_pertenece["dp_cp_id"]);
        $keys_selects['dp_colonia_postal_id']->id_selected =  $dp_calle_pertenece["dp_colonia_postal_id"];

        $keys_selects['dp_calle_pertenece_id']->con_registros =  true;
        $keys_selects['dp_calle_pertenece_id']->filtro = array('dp_colonia_postal.id' => $dp_calle_pertenece["dp_colonia_postal_id"]);
        $keys_selects['dp_calle_pertenece_id']->id_selected = $this->registro['dp_calle_pertenece_id'];

        $base = $this->base_upd(keys_selects: $keys_selects, params: array(), params_ajustados: array());
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar base', data: $base, header: $header, ws: $ws);
        }

        return $r_modifica;
    }

}
