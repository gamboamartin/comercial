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
use gamboamartin\comercial\models\com_prospecto;
use gamboamartin\errores\errores;
use gamboamartin\template\html;
use html\com_prospecto_html;
use PDO;
use stdClass;

class controlador_com_prospecto extends _base_sin_cod {

    public array|stdClass $keys_selects = array();

    public function __construct(PDO $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new com_prospecto(link: $link);
        $html_ = new com_prospecto_html(html: $html);
        parent::__construct(html_: $html_,link:  $link,modelo:  $modelo, paths_conf: $paths_conf);


    }
    protected function campos_view(array $inputs = array()): array
    {
        $keys = new stdClass();
        $keys->inputs = array('codigo','descripcion','nombre','apellido_paterno','apellido_materno','telefono',
            'correo','razon_social');
        $keys->selects = array();

        $init_data = array();
        $init_data['com_tipo_prospecto'] = "gamboamartin\\comercial";
        $init_data['com_agente'] = "gamboamartin\\comercial";
        $campos_view = $this->campos_view_base(init_data: $init_data,keys:  $keys);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar campo view',data:  $campos_view);
        }

        return $campos_view;
    }



    public function init_datatable(): stdClass
    {
        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['com_prospecto_id']['titulo'] = 'Id';
        $datatables->columns['com_prospecto_descripcion']['titulo'] = 'Prospecto';


        $datatables->filtro = array();
        $datatables->filtro[] = 'com_prospecto.id';
        $datatables->filtro[] = 'com_prospecto.descripcion';

        return $datatables;
    }


    protected function key_selects_txt(array $keys_selects): array
    {
        $keys_selects = (new init())->key_select_txt(cols: 4,key: 'codigo',
            keys_selects:$keys_selects, place_holder: 'Cod');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 8,key: 'descripcion',
            keys_selects:$keys_selects, place_holder: 'Tipo Agente');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'nombre',
            keys_selects:$keys_selects, place_holder: 'Nombre');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'apellido_paterno',
            keys_selects:$keys_selects, place_holder: 'AP');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'apellido_materno',
            keys_selects:$keys_selects, place_holder: 'AM');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'telefono',
            keys_selects:$keys_selects, place_holder: 'Tel');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'correo',
            keys_selects:$keys_selects, place_holder: 'Correo');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'razon_social',
            keys_selects:$keys_selects, place_holder: 'Razon Social');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        return $keys_selects;
    }


}
