<?php

namespace gamboamartin\comercial\controllers;

use base\controller\controler;
use base\orm\modelo;
use gamboamartin\errores\errores;
use gamboamartin\system\_ctl_parent_sin_codigo;
use gamboamartin\system\html_controler;
use gamboamartin\system\links_menu;
use PDO;
use stdClass;

class _base_sin_cod extends _ctl_parent_sin_codigo{
    public function __construct(html_controler $html_,PDO $link, modelo $modelo, stdClass $paths_conf = new stdClass())
    {
        $obj_link = new links_menu(link: $link,registro_id: $this->registro_id);

        $datatables = $this->init_datatable();
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al inicializar datatable',data: $datatables);
            print_r($error);
            die('Error');
        }

        parent::__construct(html:$html_, link: $link,modelo:  $modelo, obj_link: $obj_link, datatables: $datatables,
            paths_conf: $paths_conf);

        $init_controladores = $this->init_controladores(paths_conf: $paths_conf);
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al inicializar controladores',data:  $init_controladores);
            print_r($error);
            die('Error');
        }
    }


    /**
     * La funcion debe existir en el controlador que hereda
     * @param stdClass $paths_conf
     * @return controler
     */
    private function init_controladores(stdClass $paths_conf): controler
    {

        return $this;
    }

    /**
     * La funcion debe existir en el controlador que hereda
     * @return stdClass
     */
    public function init_datatable(): stdClass
    {
        return new stdClass();
    }
}
