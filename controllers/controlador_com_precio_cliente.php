<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\comercial\controllers;

use gamboamartin\comercial\models\com_precio_cliente;
use gamboamartin\errores\errores;
use gamboamartin\template\html;
use html\cat_sat_conf_imps_html;
use html\com_cliente_html;
use html\com_conf_precio_html;
use html\com_producto_html;
use PDO;
use stdClass;

class controlador_com_precio_cliente extends _base_sin_cod {

    public array|stdClass $keys_selects = array();

    public function __construct(PDO $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new com_precio_cliente(link: $link);
        $html_ = new com_conf_precio_html(html: $html);
        parent::__construct(html_: $html_,link:  $link,modelo:  $modelo, paths_conf: $paths_conf);


    }

    public function alta(bool $header, bool $ws = false): array|string
    {
        $r_alta =  parent::alta(header: $header,ws:  $ws); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar template', data: $r_alta, header: $header,ws:  $ws);
        }

        $com_producto_id = (new com_producto_html(html: $this->html_base))->select_com_producto_id(
            cols: 6,con_registros: true,id_selected: -1,link:  $this->link);

        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar input', data: $com_producto_id, header: $header,ws:  $ws);
        }

        $this->inputs->com_producto_id = $com_producto_id;

        $com_cliente_id = (new com_cliente_html(html: $this->html_base))->select_com_cliente_id(
            cols: 6,con_registros: true,id_selected: -1,link:  $this->link);

        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar input', data: $com_cliente_id, header: $header,ws:  $ws);
        }

        $this->inputs->com_cliente_id = $com_cliente_id;


        $cat_sat_conf_imps_id = (new cat_sat_conf_imps_html(html: $this->html_base))->select_cat_sat_conf_imps_id(
            cols: 12,con_registros: true,id_selected: -1,link:  $this->link);

        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar input', data: $cat_sat_conf_imps_id, header: $header,ws:  $ws);
        }

        $this->inputs->cat_sat_conf_imps_id = $cat_sat_conf_imps_id;


        $precio = $this->html->input_monto(cols: 12, row_upd: new stdClass(), value_vacio: false, name: 'precio', place_holder: 'Precio', value: 1);

        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar input', data: $precio, header: $header,ws:  $ws);
        }

        $this->inputs->precio = $precio;


        return $r_alta;
    }

    public function init_datatable(): stdClass
    {
        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['com_precio_cliente_id']['titulo'] = 'Id';
        $datatables->columns['com_producto_descripcion']['titulo'] = 'Producto';
        $datatables->columns['com_cliente_razon_social']['titulo'] = 'Cliente';
        $datatables->columns['com_precio_cliente_precio']['titulo'] = 'Precio';

        $datatables->filtro = array();
        $datatables->filtro[] = 'com_precio_cliente.id';
        $datatables->filtro[] = 'com_producto.descripcion';
        $datatables->filtro[] = 'com_cliente.razon_social';

        return $datatables;
    }

    public function modifica(bool $header, bool $ws = false, array $keys_selects = array()): array|stdClass
    {
        $r_modifica = parent::modifica(header: $header,ws:  $ws, keys_selects: $keys_selects); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar template', data: $r_modifica, header: $header,ws:  $ws);
        }

        $com_producto_id = (new com_producto_html(html: $this->html_base))->select_com_producto_id(
            cols: 6,con_registros: true,id_selected: $this->row_upd->com_producto_id,link:  $this->link);

        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar input', data: $com_producto_id, header: $header,ws:  $ws);
        }

        $this->inputs->com_producto_id = $com_producto_id;

        $com_cliente_id = (new com_cliente_html(html: $this->html_base))->select_com_cliente_id(
            cols: 6,con_registros: true,id_selected: $this->row_upd->com_cliente_id,link:  $this->link);

        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar input', data: $com_cliente_id, header: $header,ws:  $ws);
        }

        $this->inputs->com_cliente_id = $com_cliente_id;

        $cat_sat_conf_imps_id = (new cat_sat_conf_imps_html(html: $this->html_base))->select_cat_sat_conf_imps_id(
            cols: 12,con_registros: true,id_selected:$this->row_upd->cat_sat_conf_imps_id,link:  $this->link);

        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar input', data: $cat_sat_conf_imps_id, header: $header,ws:  $ws);
        }

        $this->inputs->cat_sat_conf_imps_id = $cat_sat_conf_imps_id;

        $precio = $this->html->input_monto(cols: 12, row_upd: $this->row_upd, value_vacio: false, name: 'precio',
            place_holder: 'Precio', value: $this->row_upd->precio);

        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar input', data: $precio, header: $header,ws:  $ws);
        }

        $this->inputs->precio = $precio;

        return $r_modifica;

    }


}
