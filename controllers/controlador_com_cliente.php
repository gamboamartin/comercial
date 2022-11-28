<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\comercial\controllers;
use base\controller\controler;
use gamboamartin\comercial\models\com_cliente;
use gamboamartin\direccion_postal\models\dp_calle_pertenece;
use gamboamartin\errores\errores;
use gamboamartin\system\links_menu;
use gamboamartin\system\system;
use gamboamartin\template\html;
use html\com_cliente_html;
use PDO;
use stdClass;

class controlador_com_cliente extends system {

    public array $keys_selects = array();

    public function __construct(PDO $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new com_cliente(link: $link);
        $html = new com_cliente_html(html: $html);
        $obj_link = new links_menu(link: $link,registro_id: $this->registro_id);

        $datatables = $this->init_datatable();
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al inicializar datatable',data: $datatables);
            print_r($error);
            die('Error');
        }

        parent::__construct(html:$html, link: $link,modelo:  $modelo, obj_link: $obj_link, datatables: $datatables,
            paths_conf: $paths_conf);

        $configuraciones = $this->init_configuraciones();
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al inicializar configuraciones',data: $configuraciones);
            print_r($error);
            die('Error');
        }

        $inputs = $this->init_inputs();
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al inicializar inputs',data:  $inputs);
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

    private function init_configuraciones(): controler
    {
        $this->seccion_titulo = 'Clientes';
        $this->titulo_lista = 'Registro de Clientes';

        return $this;
    }

    public function init_datatable(): stdClass
    {
        $columns["com_cliente_id"]["titulo"] = "Id";
        $columns["com_cliente_codigo"]["titulo"] = "Código";
        $columns["com_cliente_razon_social"]["titulo"] = "Razón Social";
        $columns["com_cliente_rfc"]["titulo"] = "RFC";
        $columns["cat_sat_regimen_fiscal_descripcion"]["titulo"] = "Régimen Fiscal";

        $filtro = array("com_cliente.id","com_cliente.codigo", "com_cliente.razon_social", "com_cliente.rfc",
            "cat_sat_regimen_fiscal.descripcion");

        $datatables = new stdClass();
        $datatables->columns = $columns;
        $datatables->filtro = $filtro;

        return $datatables;
    }

    private function init_inputs(): array
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

        $identificador = "cat_sat_regimen_fiscal_id";
        $propiedades = array("label" => "Régimen Fiscal", "cols" => 12);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "cat_sat_moneda_id";
        $propiedades = array("label" => "Moneda");
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "cat_sat_forma_pago_id";
        $propiedades = array("label" => "Forma Pago");
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "cat_sat_uso_cfdi_id";
        $propiedades = array("label" => "Uso CFDI", "cols" => 12);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "cat_sat_metodo_pago_id";
        $propiedades = array("label" => "Método de Pago");
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "cat_sat_tipo_de_comprobante_id";
        $propiedades = array("label" => "Tipo de Comprobante");
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "com_tipo_cliente_id";
        $propiedades = array("label" => "Tipo de Cliente", "cols" => 12);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "codigo";
        $propiedades = array("place_holder" => "Código", "cols" => 4);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "rfc";
        $propiedades = array("place_holder" => "RFC");
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "razon_social";
        $propiedades = array("place_holder" => "Razón Social", "cols" => 8);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "telefono";
        $propiedades = array("place_holder" => "Teléfono ");
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "numero_exterior";
        $propiedades = array("place_holder" => "Num Ext");
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "numero_interior";
        $propiedades = array("place_holder" => "Num Int");
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        return $this->keys_selects;
    }

    private function init_modifica(): array|stdClass
    {
        $r_modifica =  parent::modifica(header: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al generar template',data:  $r_modifica);
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

        $identificador = "cat_sat_regimen_fiscal_id";
        $propiedades = array("id_selected" => $this->row_upd->cat_sat_regimen_fiscal_id);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "cat_sat_regimen_fiscal_id";
        $propiedades = array("id_selected" => $this->row_upd->cat_sat_regimen_fiscal_id);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "cat_sat_moneda_id";
        $propiedades = array("id_selected" => $this->row_upd->cat_sat_moneda_id);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "cat_sat_forma_pago_id";
        $propiedades = array("id_selected" => $this->row_upd->cat_sat_forma_pago_id);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "cat_sat_uso_cfdi_id";
        $propiedades = array("id_selected" => $this->row_upd->cat_sat_uso_cfdi_id);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "cat_sat_metodo_pago_id";
        $propiedades = array("id_selected" => $this->row_upd->cat_sat_metodo_pago_id);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "cat_sat_tipo_de_comprobante_id";
        $propiedades = array("id_selected" => $this->row_upd->cat_sat_tipo_de_comprobante_id);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "com_tipo_cliente_id";
        $propiedades = array("id_selected" => $this->row_upd->com_tipo_cliente_id);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $inputs = $this->genera_inputs(keys_selects:  $this->keys_selects);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar inputs',data:  $inputs);
        }

        $data = new stdClass();
        $data->template = $r_modifica;
        $data->inputs = $inputs;

        return $data;
    }

    public function modifica(bool $header, bool $ws = false): array|stdClass
    {
        $base = $this->init_modifica();
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar datos',data:  $base,
                header: $header,ws:$ws);
        }

        return $base->template;
    }
}
