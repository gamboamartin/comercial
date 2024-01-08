<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\comercial\controllers;

use gamboamartin\comercial\models\com_conf_asigna_agente;
use gamboamartin\errores\errores;
use gamboamartin\template\html;
use html\com_conf_asigna_agente_html;
use PDO;
use stdClass;

class controlador_com_conf_asigna_agente extends _base_sin_cod {

    public array|stdClass $keys_selects = array();

    public string $link_com_agente_alta_bd = '';

    public function __construct(PDO $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new com_conf_asigna_agente(link: $link);
        $html_ = new com_conf_asigna_agente_html(html: $html);
        parent::__construct(html_: $html_,link:  $link,modelo:  $modelo, paths_conf: $paths_conf);

        $init_links = $this->init_links();
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al inicializar links',data:  $init_links);
            print_r($error);
            die('Error');
        }



    }


    public function init_datatable(): stdClass
    {
        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['com_conf_asigna_agente_id']['titulo'] = 'Id';
        $datatables->columns['com_conf_asigna_agente_descripcion']['titulo'] = 'Conf';
        $datatables->columns['com_tipo_agente_descripcion']['titulo'] = 'Tipo Agente';

        $datatables->filtro = array();
        $datatables->filtro[] = 'com_conf_asigna_agente.id';
        $datatables->filtro[] = 'com_conf_asigna_agente.descripcion';
        $datatables->filtro[] = 'com_tipo_agente.descripcion';

        return $datatables;
    }

    private function init_links(): array|string
    {
        $this->obj_link->genera_links(controler: $this);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al generar links para tipo cliente',data:  $this->obj_link);
        }

        $this->link_com_agente_alta_bd = $this->obj_link->link_alta_bd(link: $this->link, seccion: 'com_agente');
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al obtener link',data:  $this->link_com_agente_alta_bd);
            print_r($error);
            exit;
        }

        return $this->link_com_agente_alta_bd;
    }

    protected function key_selects_txt(array $keys_selects): array
    {
        return $keys_selects;
    }

}
