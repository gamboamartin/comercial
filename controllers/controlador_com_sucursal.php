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
use controllers\_init_dps;
use gamboamartin\comercial\models\com_cliente;
use gamboamartin\comercial\models\com_sucursal;
use gamboamartin\comercial\models\com_tipo_sucursal;
use gamboamartin\direccion_postal\models\dp_calle_pertenece;
use gamboamartin\direccion_postal\models\dp_municipio;
use gamboamartin\errores\errores;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use html\com_sucursal_html;
use PDO;
use stdClass;

class controlador_com_sucursal extends _base_comercial {

    public array|stdClass $keys_selects = array();

    public function __construct(PDO $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new com_sucursal(link: $link);
        $html = new com_sucursal_html(html: $html);
        $obj_link = new links_menu(link: $link,registro_id: $this->registro_id);

        $columns["com_sucursal_id"]["titulo"] = "Id";
        $columns["com_sucursal_codigo"]["titulo"] = "Código";
        $columns["com_sucursal_descripcion"]["titulo"] = "Sucursal";
        $columns["com_cliente_descripcion"]["titulo"] = "Cliente";
        $columns["com_sucursal_nombre_contacto"]["titulo"] = "Contacto";

        $filtro = array("com_sucursal.id","com_sucursal.codigo", "com_sucursal.descripcion", "com_sucursal.nombre_contacto",
            "com_cliente.descripcion");

        $datatables = new stdClass();
        $datatables->columns = $columns;
        $datatables->filtro = $filtro;

        parent::__construct(html:$html, link: $link,modelo:  $modelo, obj_link: $obj_link, datatables: $datatables,
            paths_conf: $paths_conf);

        $this->titulo_lista = 'Sucursal';

        $propiedades = $this->inicializa_propiedades();
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al inicializar propiedades',data:  $propiedades);
            print_r($error);
            die('Error');
        }

        $this->parents_verifica[] = (new com_tipo_sucursal(link: $this->link));
        $this->parents_verifica[] = (new dp_calle_pertenece(link: $this->link));
        $this->parents_verifica[] = (new com_cliente(link: $this->link));
       //this->parents_verifica[] = (new tg_tipo_provision(link: $this->link));
        $this->verifica_parents_alta = true;


    }


    /**
     * Asigna las propiedades de la entidad para frontend
     * @return array
     */
    private function inicializa_propiedades(): array
    {
        $identificador = "dp_pais_id";
        $propiedades = array("label" => "Pais",'key_descripcion_select'=>'dp_pais_descripcion');
        $prop = $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);
        if(errores::$error){
            return $this->errores->error(mensaje:  'Error al generar propiedad',data: $prop);
        }

        $identificador = "dp_estado_id";
        $propiedades = array("label" => "Estado", "con_registros" => false,
            'key_descripcion_select'=>'dp_estado_descripcion');
        $prop =$this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);
        if(errores::$error){
            return $this->errores->error(mensaje:  'Error al generar propiedad',data: $prop);
        }

        $identificador = "dp_municipio_id";
        $propiedades = array("label" => "Municipio", "con_registros" => false,
            'key_descripcion_select'=>'dp_municipio_descripcion');
        $prop =$this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);
        if(errores::$error){
            return $this->errores->error(mensaje:  'Error al generar propiedad',data: $prop);
        }


        $identificador = "com_cliente_id";
        $propiedades = array("label" => "Cliente", "cols" => 12, "extra_params_keys" => array("dp_pais_id",
            "dp_estado_id", "dp_municipio_id","dp_cp_id", "dp_colonia_postal_id","dp_calle_pertenece_id"));
        $prop =$this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);
        if(errores::$error){
            return $this->errores->error(mensaje:  'Error al generar propiedad',data: $prop);
        }

        $identificador = "cp";
        $propiedades = array("place_holder" => "CP", "cols" => 6);
        $prop =$this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);
        if(errores::$error){
            return $this->errores->error(mensaje:  'Error al generar propiedad',data: $prop);
        }

        $identificador = "colonia";
        $propiedades = array("place_holder" => "Colonia", "cols" => 6);
        $prop =$this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);
        if(errores::$error){
            return $this->errores->error(mensaje:  'Error al generar propiedad',data: $prop);
        }

        $identificador = "calle";
        $propiedades = array("place_holder" => "Calle", "cols" => 6);
        $prop =$this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);
        if(errores::$error){
            return $this->errores->error(mensaje:  'Error al generar propiedad',data: $prop);
        }

        $identificador = "com_tipo_sucursal_id";
        $propiedades = array("label" => "Tipo Sucursal", "cols" => 12);
        $prop =$this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);
        if(errores::$error){
            return $this->errores->error(mensaje:  'Error al generar propiedad',data: $prop);
        }


        $identificador = "codigo";
        $propiedades = array("place_holder" => "Código", "cols" => 4);
        $prop =$this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);
        if(errores::$error){
            return $this->errores->error(mensaje:  'Error al generar propiedad',data: $prop);
        }

        $identificador = "nombre_contacto";
        $propiedades = array("place_holder" => "Contacto", "cols" => 8);
        $prop =$this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);
        if(errores::$error){
            return $this->errores->error(mensaje:  'Error al generar propiedad',data: $prop);
        }

        $identificador = "telefono_1";
        $propiedades = array("place_holder" => "Teléfono 1", "cols" => 4);
        $prop =$this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);
        if(errores::$error){
            return $this->errores->error(mensaje:  'Error al generar propiedad',data: $prop);
        }

        $identificador = "telefono_2";
        $propiedades = array("place_holder" => "Teléfono 2", "cols" => 4, "required"=>false);
        $prop =$this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);
        if(errores::$error){
            return $this->errores->error(mensaje:  'Error al generar propiedad',data: $prop);
        }

        $identificador = "telefono_3";
        $propiedades = array("place_holder" => "Teléfono 3", "cols" => 4, "required"=>false);
        $prop =$this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);
        if(errores::$error){
            return $this->errores->error(mensaje:  'Error al generar propiedad',data: $prop);
        }

        $identificador = "numero_exterior";
        $propiedades = array("place_holder" => "Num Ext");
        $prop =$this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);
        if(errores::$error){
            return $this->errores->error(mensaje:  'Error al generar propiedad',data: $prop);
        }

        $identificador = "numero_interior";
        $propiedades = array("place_holder" => "Num Int", "required"=>false);
        $prop =$this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);
        if(errores::$error){
            return $this->errores->error(mensaje:  'Error al generar propiedad',data: $prop);
        }

        return $this->keys_selects;
    }

    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array('codigo','nombre_contacto','numero_interior','numero_exterior','telefono_1','telefono_2',
            'telefono_3','cp','colonia','calle');
        $keys->selects = array();

        $init_data = array();
        $init_data['com_cliente'] = "gamboamartin\\comercial";
        $init_data['dp_pais'] = "gamboamartin\\direccion_postal";
        $init_data['dp_estado'] = "gamboamartin\\direccion_postal";
        $init_data['dp_municipio'] = "gamboamartin\\direccion_postal";
        $init_data['dp_cp'] = "gamboamartin\\direccion_postal";
        $init_data['dp_colonia_postal'] = "gamboamartin\\direccion_postal";
        $init_data['dp_calle_pertenece'] = "gamboamartin\\direccion_postal";
        $init_data['com_tipo_sucursal'] = "gamboamartin\\comercial";
        $init_data['tg_tipo_provision'] = "gamboamartin\\tg_nomina";
        $campos_view = $this->campos_view_base(init_data: $init_data,keys:  $keys);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar campo view',data:  $campos_view);
        }

        return $campos_view;
    }

    public function key_selects_txt(array $keys_selects): array
    {

        $r_alta = $this->init_alta();
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar alta',data:  $r_alta);
        }

        $keys_selects = (new init())->key_select_txt(cols: 4,key: 'codigo',
            keys_selects:$keys_selects, place_holder: 'Cod');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = $this->key_select(cols:12, con_registros: true,filtro:  array(), key: 'com_cliente_id',
            keys_selects: $keys_selects, id_selected: -1, label: 'Cliente');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = $this->key_select(cols:12, con_registros: true,filtro:  array(), key: 'com_tipo_sucursal_id',
            keys_selects: $keys_selects, id_selected: -1, label: 'Tipo Sucursal');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = $this->key_select(cols:6, con_registros: false,filtro:  array(), key: 'dp_calle_pertenece_id',
            keys_selects: $keys_selects, id_selected: -1, label: 'Calle');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }


        $keys_selects = $this->key_select(cols:6, con_registros: false,filtro:  array(), key: 'dp_estado_id',
            keys_selects: $keys_selects, id_selected: -1, label: 'Estado');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects['dp_estado_id']->key_descripcion_select = 'dp_estado_descripcion';

        $keys_selects = $this->key_select(cols:6, con_registros: false,filtro:  array(), key: 'dp_municipio_id',
            keys_selects: $keys_selects, id_selected: -1, label: 'Municipio');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects['dp_municipio_id']->key_descripcion_select = 'dp_municipio_descripcion';

        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'dp_pais_id',
            keys_selects: $keys_selects, id_selected: -1, label: 'Pais');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects['dp_pais_id']->key_descripcion_select = 'dp_pais_descripcion';

        $keys_selects = (new init())->key_select_txt(cols: 4,key: 'cp', keys_selects:$keys_selects,
            place_holder: 'CP');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 4,key: 'colonia', keys_selects:$keys_selects,
            place_holder: 'Colonia');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 4,key: 'calle', keys_selects:$keys_selects,
            place_holder: 'Calle');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 8,key: 'nombre_contacto',
            keys_selects:$keys_selects, place_holder: 'Contacto');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 4,key: 'telefono_1',
            keys_selects:$keys_selects, place_holder: 'Teléfono 1');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 4,key: 'telefono_2',
            keys_selects:$keys_selects, place_holder: 'Teléfono 2', required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 4,key: 'telefono_3',
            keys_selects:$keys_selects, place_holder: 'Teléfono 3', required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new _base())->keys_selects(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }



        return $keys_selects;
    }

    public function modifica(bool $header, bool $ws = false): array|stdClass
    {
        $urls_js = (new _init_dps())->init_js(controler: $this);

        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar url js',data:  $urls_js,header: $header,ws: $ws);
        }

        $r_modifica =  parent::modifica(header: false);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar template',data:  $r_modifica, header: $header,ws:$ws);
        }

        $municipio = (new dp_municipio($this->link))->registro($this->row_upd->dp_municipio_id);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener municipio',data:  $municipio);
        }

        $identificador = "dp_pais_id";
        $propiedades = array("id_selected" => $municipio['dp_pais_id']);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "dp_estado_id";
        $propiedades = array("id_selected" => $municipio['dp_estado_id'], "con_registros" => true,
            "filtro" => array('dp_pais.id' => $municipio['dp_pais_id']));
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "dp_municipio_id";
        $propiedades = array("id_selected" => $municipio['dp_municipio_id'], "con_registros" => true,
            "filtro" => array('dp_estado.id' => $municipio['dp_estado_id']));
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);


        $identificador = "com_cliente_id";
        $propiedades = array("id_selected" => $this->row_upd->com_cliente_id);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "com_tipo_sucursal_id";
        $propiedades = array("id_selected" => $this->row_upd->com_tipo_sucursal_id);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);



        $inputs = $this->genera_inputs(keys_selects:  $this->keys_selects);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar inputs',data:  $inputs);
        }

        return $r_modifica;
    }




}
