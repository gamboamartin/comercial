<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\comercial\controllers;

use gamboamartin\cat_sat\models\cat_sat_producto;
use gamboamartin\comercial\models\com_producto;
use gamboamartin\errores\errores;
use gamboamartin\system\links_menu;
use gamboamartin\system\system;
use gamboamartin\template\html;
use html\com_producto_html;
use PDO;
use stdClass;

class controlador_com_producto extends system {

    public array $keys_selects = array();

    public function __construct(PDO $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new com_producto(link: $link);
        $html = new com_producto_html(html: $html);
        $obj_link = new links_menu(link: $link,registro_id: $this->registro_id);

        $columns["com_producto_id"]["titulo"] = "Id";
        $columns["com_producto_codigo"]["titulo"] = "Código";
        $columns["cat_sat_producto_descripcion"]["titulo"] = "SAT Producto";
        $columns["cat_sat_unidad_descripcion"]["titulo"] = "SAT Unidad";
        $columns["cat_sat_obj_imp_descripcion"]["titulo"] = "SAT ObjetoImp";
        $columns["com_producto_descripcion"]["titulo"] = "Producto";

        $filtro = array("com_producto.id","com_producto.codigo", "com_producto.descripcion");

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
        $identificador = "cat_sat_tipo_producto_id";
        $propiedades = array("label" => "Tipo");
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "cat_sat_division_producto_id";
        $propiedades = array("label" => "División", "con_registros" => false);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "cat_sat_grupo_producto_id";
        $propiedades = array("label" => "Grupo", "con_registros" => false);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "cat_sat_clase_producto_id";
        $propiedades = array("label" => "Clase", "con_registros" => false);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "cat_sat_producto_id";
        $propiedades = array("label" => "SAT Producto", "con_registros" => false, "cols" => 12);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "cat_sat_unidad_id";
        $propiedades = array("label" => "Unidad");
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "cat_sat_obj_imp_id";
        $propiedades = array("label" => "Objeto del Impuesto");
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);


        $identificador = "codigo";
        $propiedades = array("place_holder" => "Código", "cols" => 4);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "descripcion";
        $propiedades = array("place_holder" => "Producto", "cols" => 8);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        return $this->keys_selects;
    }

    public function modifica(bool $header, bool $ws = false): array|stdClass
    {
        $r_modifica =  parent::modifica(header: false);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar template',data:  $r_modifica, header: $header,ws:$ws);
        }

        $producto = (new cat_sat_producto($this->link))->get_producto($this->row_upd->cat_sat_producto_id);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener $producto',data:  $producto);
        }

        $identificador = "cat_sat_tipo_producto_id";
        $propiedades = array("id_selected" => $producto['cat_sat_tipo_producto_id']);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "cat_sat_division_producto_id";
        $propiedades = array("id_selected" => $producto['cat_sat_division_producto_id'], "con_registros" => true,
            "filtro" => array('cat_sat_tipo_producto.id' => $producto['cat_sat_tipo_producto_id']));
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "cat_sat_grupo_producto_id";
        $propiedades = array("id_selected" => $producto['cat_sat_grupo_producto_id'], "con_registros" => true,
            "filtro" => array('cat_sat_division_producto.id' => $producto['cat_sat_division_producto_id']));
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "cat_sat_clase_producto_id";
        $propiedades = array("id_selected" => $producto['cat_sat_clase_producto_id'], "con_registros" => true,
            "filtro" => array('cat_sat_grupo_producto.id' => $producto['cat_sat_grupo_producto_id']));
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "cat_sat_producto_id";
        $propiedades = array("id_selected" => $this->row_upd->cat_sat_producto_id, "con_registros" => true,
            "filtro" => array('cat_sat_clase_producto.id' => $producto['cat_sat_clase_producto_id']));
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "cat_sat_unidad_id";
        $propiedades = array("id_selected" => $this->row_upd->cat_sat_unidad_id);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "cat_sat_obj_imp_id";
        $propiedades = array("id_selected" => $this->row_upd->cat_sat_obj_imp_id);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $inputs = $this->genera_inputs(keys_selects:  $this->keys_selects);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar inputs',data:  $inputs);
        }

        return $r_modifica;
    }

}
