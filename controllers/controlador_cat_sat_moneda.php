<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\comercial\controllers;

use gamboamartin\errores\errores;
use html\cat_sat_moneda_html;
use html\com_tipo_cambio_html;
use html\dp_pais_html;
use PDO;
use stdClass;

class controlador_cat_sat_moneda extends \gamboamartin\cat_sat\controllers\controlador_cat_sat_moneda {
    public string $link_com_tipo_cambio_alta_bd = '';

    public function __construct(PDO $link, stdClass $paths_conf = new stdClass())
    {
        parent::__construct(link: $link,paths_conf:  $paths_conf);

        $link_com_tipo_cambio_alta_bd = $this->obj_link->link_alta_bd(link: $link, seccion: 'com_tipo_cambio');
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al obtener link',data:  $link_com_tipo_cambio_alta_bd);
            print_r($error);
            exit;
        }
        $this->link_com_tipo_cambio_alta_bd = $link_com_tipo_cambio_alta_bd;

        $this->childrens_data['com_cliente']['title'] = 'Cliente';
    }

    public function tipos_de_cambio(bool $header = true, bool $ws = false): array|string
    {


        $data_view = new stdClass();
        $data_view->names = array('Id','Pais', 'Moneda','Fecha','Monto','Acciones');
        $data_view->keys_data = array('cat_sat_moneda_id','dp_pais_descripcion','cat_sat_moneda_codigo',
            'com_tipo_cambio_fecha','com_tipo_cambio_monto');
        $data_view->key_actions = 'acciones';
        $data_view->namespace_model = 'gamboamartin\\comercial\\models';
        $data_view->name_model_children = 'com_tipo_cambio';


        $contenido_table = $this->contenido_children(data_view: $data_view, next_accion: __FUNCTION__, not_actions: $this->not_actions);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener tbody',data:  $contenido_table, header: $header,ws:  $ws);
        }

        return $contenido_table;


    }

    protected function inputs_children(stdClass $registro): stdClass|array
    {

        $select_dp_pais_id = (new dp_pais_html(html: $this->html_base))->select_dp_pais_id(
            cols:6,con_registros: true,id_selected:  $registro->dp_pais_id,link:  $this->link, disabled: true);

        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener select_dp_pais_id',data:  $select_dp_pais_id);
        }


        $select_cat_sat_moneda_id = (new cat_sat_moneda_html(html: $this->html_base))->select_cat_sat_moneda_id(
            cols:6,con_registros: true,id_selected:  $this->registro_id,link:  $this->link, disabled: true);

        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener select_cat_sat_moneda_id',data:  $select_cat_sat_moneda_id);
        }

        $com_tipo_cambio_fecha = (new com_tipo_cambio_html(html: $this->html_base))->input_fecha(
            cols:6,row_upd:  new stdClass(),value_vacio:  false, value: date('Y-m-d'));
        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener com_tipo_cambio_fecha',data:  $com_tipo_cambio_fecha);
        }

        $com_tipo_cambio_monto = (new com_tipo_cambio_html(html: $this->html_base))->input_monto(
            cols:6,row_upd:  new stdClass(),value_vacio:  false);
        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener com_tipo_cambio_monto',data:  $com_tipo_cambio_monto);
        }



        $this->inputs = new stdClass();
        $this->inputs->select = new stdClass();
        $this->inputs->select->dp_pais_id = $select_dp_pais_id;
        $this->inputs->select->cat_sat_moneda_id = $select_cat_sat_moneda_id;
        $this->inputs->com_tipo_cambio_fecha = $com_tipo_cambio_fecha;
        $this->inputs->com_tipo_cambio_monto = $com_tipo_cambio_monto;

        return $this->inputs;
    }


}
