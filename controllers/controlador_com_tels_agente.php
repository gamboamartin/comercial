<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\comercial\controllers;

use gamboamartin\comercial\models\com_tels_agente;
use gamboamartin\template\html;
use html\com_tels_agente_html;
use PDO;
use stdClass;

class controlador_com_tels_agente extends _base_sin_cod {

    public array|stdClass $keys_selects = array();


    public function __construct(PDO $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new com_tels_agente(link: $link);
        $html_ = new com_tels_agente_html(html: $html);
        parent::__construct(html_: $html_,link:  $link,modelo:  $modelo, paths_conf: $paths_conf);

    }


    public function init_datatable(): stdClass
    {
        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['com_tels_agente_id']['titulo'] = 'Id';
        $datatables->columns['com_tels_agente_descripcion']['titulo'] = 'Tipo Agente';


        $datatables->filtro = array();
        $datatables->filtro[] = 'com_tels_agente.id';
        $datatables->filtro[] = 'com_tels_agente.descripcion';

        return $datatables;
    }


    protected function key_selects_txt(array $keys_selects): array
    {

        return $keys_selects;
    }


}
