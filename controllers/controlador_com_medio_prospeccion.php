<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\comercial\controllers;

use gamboamartin\comercial\models\com_medio_prospeccion;
use gamboamartin\errores\errores;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use html\com_medio_prospeccion_html;
use PDO;
use stdClass;

class controlador_com_medio_prospeccion extends _base_comercial {

    public array|stdClass $keys_selects = array();
    public controlador_com_prospecto $controlador_com_prospecto;

    public string $link_com_prospecto_alta_bd = '';

    public function __construct(PDO $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new com_medio_prospeccion(link: $link);
        $html_ = new com_medio_prospeccion_html(html: $html);
        $obj_link = new links_menu(link: $link,registro_id: $this->registro_id);

        $datatables = $this->init_datatable();
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al inicializar datatable',data: $datatables);
            print_r($error);
            die('Error');
        }

        parent::__construct(html:$html_, link: $link,modelo:  $modelo, obj_link: $obj_link, datatables: $datatables,
            paths_conf: $paths_conf);

        $init_links = $this->init_links();
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al inicializar links',data:  $init_links);
            print_r($error);
            die('Error');
        }

        $this->childrens_data['com_prospecto']['title'] = 'Prospectos';


    }


    public function init_datatable(): stdClass
    {
        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['com_tipo_prospecto_id']['titulo'] = 'Id';
        $datatables->columns['com_tipo_prospecto_descripcion']['titulo'] = 'Tipo Prospecto';
        $datatables->columns['com_tipo_prospecto_n_prospectos']['titulo'] = 'Prospectos';

        $datatables->filtro = array();
        $datatables->filtro[] = 'com_tipo_prospecto.id';
        $datatables->filtro[] = 'com_tipo_prospecto.descripcion';

        return $datatables;
    }

    private function init_links(): array|string
    {
        $this->obj_link->genera_links(controler: $this);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al generar links para tipo cliente',data:  $this->obj_link);
        }

        $this->link_com_prospecto_alta_bd = $this->obj_link->link_alta_bd(link: $this->link, seccion: 'com_agente');
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al obtener link',data:  $this->link_com_prospecto_alta_bd);
            print_r($error);
            exit;
        }

        return $this->link_com_prospecto_alta_bd;
    }

    protected function key_selects_txt(array $keys_selects): array
    {

        return $keys_selects;
    }

    public function get_medio_prospeccion(bool $header, bool $ws = true): array|stdClass
    {
        $keys['com_medio_prospeccion'] = array('id','descripcion','codigo','codigo_bis');

        $salida = $this->get_out(header: $header,keys: $keys, ws: $ws);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar salida',data:  $salida,header: $header,ws: $ws);
        }

        return $salida;
    }
}
