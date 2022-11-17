<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\comercial\controllers;

use gamboamartin\comercial\models\com_sucursal;
use gamboamartin\direccion_postal\models\dp_calle_pertenece;
use gamboamartin\errores\errores;
use gamboamartin\system\links_menu;
use gamboamartin\system\system;
use gamboamartin\template\html;
use html\com_sucursal_html;
use PDO;
use stdClass;

class controlador_com_sucursal extends system {

    public array $keys_selects = array();

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
    }

    public function alta(bool $header, bool $ws = false): array|string
    {
        $r_alta =  parent::alta(header: false);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar template',data:  $r_alta, header: $header,ws:$ws);
        }

        $inputs = $this->genera_inputs(keys_selects:  $this->keys_selects);
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al generar inputs',data:  $inputs);
            print_r($error);
            die('Error');
        }

        return $r_alta;
    }

    public function asignar_propiedad(string $identificador, mixed $propiedades)
    {
        if (!array_key_exists($identificador,$this->keys_selects)){
            $this->keys_selects[$identificador] = new stdClass();
        }

        foreach ($propiedades as $key => $value){
            $this->keys_selects[$identificador]->$key = $value;
        }
    }

    private function inicializa_propiedades(): array
    {
        $identificador = "dp_pais_id";
        $propiedades = array("label" => "Pais");
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "dp_estado_id";
        $propiedades = array("label" => "Estado", "con_registros" => false);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "dp_municipio_id";
        $propiedades = array("label" => "Municipio", "con_registros" => false);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "dp_cp_id";
        $propiedades = array("label" => "Código Postal", "con_registros" => false);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "dp_colonia_postal_id";
        $propiedades = array("label" => "Colonia Postal", "con_registros" => false);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "dp_calle_pertenece_id";
        $propiedades = array("label" => "Calle", "con_registros" => false);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "com_cliente_id";
        $propiedades = array("label" => "Cliente","extra_params_keys" => array("dp_pais_id","dp_estado_id",
            "dp_municipio_id","dp_cp_id","dp_colonia_postal_id","dp_calle_pertenece_id"));
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "codigo";
        $propiedades = array("place_holder" => "Código");
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "nombre_contacto";
        $propiedades = array("place_holder" => "Contacto", "cols" => 12);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "telefono_1";
        $propiedades = array("place_holder" => "Teléfono 1", "cols" => 4);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "telefono_2";
        $propiedades = array("place_holder" => "Teléfono 2", "cols" => 4);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "telefono_3";
        $propiedades = array("place_holder" => "Teléfono 3", "cols" => 4);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "numero_exterior";
        $propiedades = array("place_holder" => "Num Ext");
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "numero_interior";
        $propiedades = array("place_holder" => "Num Int");
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        return $this->keys_selects;
    }

    public function modifica(bool $header, bool $ws = false): array|stdClass
    {
        $r_modifica =  parent::modifica(header: false);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar template',data:  $r_modifica, header: $header,ws:$ws);
        }

        $calle = (new dp_calle_pertenece($this->link))->get_calle_pertenece($this->row_upd->dp_calle_pertenece_id);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener calle',data:  $calle);
        }

        $identificador = "dp_pais_id";
        $propiedades = array("id_selected" => $calle['dp_pais_id']);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "dp_estado_id";
        $propiedades = array("id_selected" => $calle['dp_estado_id'], "con_registros" => true,
            "filtro" => array('dp_pais.id' => $calle['dp_pais_id']));
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "dp_municipio_id";
        $propiedades = array("id_selected" => $calle['dp_municipio_id'], "con_registros" => true,
            "filtro" => array('dp_estado.id' => $calle['dp_estado_id']));
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "dp_cp_id";
        $propiedades = array("id_selected" => $calle['dp_cp_id'], "con_registros" => true,
            "filtro" => array('dp_estado.id' => $calle['dp_estado_id']));
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "dp_colonia_postal_id";
        $propiedades = array("id_selected" => $calle['dp_colonia_postal_id'], "con_registros" => true,
            "filtro" => array('dp_cp.id' => $calle['dp_cp_id']));
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "dp_calle_pertenece_id";
        $propiedades = array("id_selected" => $this->row_upd->dp_calle_pertenece_id, "con_registros" => true,
            "filtro" => array('dp_colonia_postal.id' => $calle['dp_colonia_postal_id']));
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "com_cliente_id";
        $propiedades = array("id_selected" => $this->row_upd->com_cliente_id);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $inputs = $this->genera_inputs(keys_selects:  $this->keys_selects);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar inputs',data:  $inputs);
        }

        return $r_modifica;
    }




}
