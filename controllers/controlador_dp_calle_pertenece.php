<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\comercial\controllers;

use PDO;
use stdClass;

class controlador_dp_calle_pertenece extends \gamboamartin\direccion_postal\controllers\controlador_dp_calle_pertenece {
    public function __construct(PDO $link, stdClass $paths_conf = new stdClass())
    {
        parent::__construct(link: $link,paths_conf:  $paths_conf);
        $this->childrens_data['com_cliente']['title'] = 'Clientes';
        $this->childrens_data['com_sucursal']['title'] = 'Sucursales';
    }
}
