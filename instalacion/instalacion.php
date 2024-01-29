<?php
namespace gamboamartin\comercial\instalacion;
use base\orm\modelo;
use gamboamartin\administrador\models\_instalacion;
use gamboamartin\administrador\models\adm_menu;
use gamboamartin\administrador\models\adm_namespace;
use gamboamartin\administrador\models\adm_seccion;
use gamboamartin\administrador\models\adm_seccion_pertenece;
use gamboamartin\administrador\models\adm_sistema;
use gamboamartin\comercial\models\com_cliente;
use gamboamartin\comercial\models\com_producto;
use gamboamartin\comercial\models\com_sucursal;
use gamboamartin\comercial\models\com_tipo_producto;
use gamboamartin\direccion_postal\models\dp_calle_pertenece;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class instalacion
{

    private function actualiza_atributos_registro(modelo $modelo, array $foraneas): array
    {
        $atributos = $modelo->atributos;

        $upds = array();
        foreach ($atributos as $campo_name=>$atributo){
            $upds = $this->actualiza_foraneas_registro(campo_name: $campo_name,modelo:  $modelo,foraneas:  $foraneas);
            if (errores::$error) {
                return (new errores())->error(mensaje: 'Error al actualizar clientes', data: $upds);
            }
        }

        return $upds;

    }
    private function actualiza_foraneas_registro(string $campo_name, modelo $modelo, array $foraneas): array
    {
        $upds = array();
        foreach ($foraneas as $campo_validar=>$atributo_validar){

            if($campo_validar === $campo_name){

                if(isset($atributo_validar->default)) {

                    $upds = $this->actualiza_registros(atributo_validar: $atributo_validar,
                        campo_validar: $campo_validar, modelo: $modelo);
                    if (errores::$error) {
                        return (new errores())->error(mensaje: 'Error al actualizar registros', data: $upds);
                    }
                }

            }

        }
        return $upds;

    }
    private function actualiza_registros(stdClass $atributo_validar, string $campo_validar, modelo $modelo): array
    {
        $value_default = $atributo_validar->default;

        $com_clientes = $modelo->registros(columnas_en_bruto: true,return_obj: true);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener clientes', data:  $com_clientes);
        }

        $upds = $this->upd_row_default(campo_validar: $campo_validar, modelo:  $modelo,
            registros:  $com_clientes, value_default:  $value_default);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al actualizar registros', data:  $upds);
        }
        return $upds;

    }

    private function com_agente(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        $columnas = new stdClass();
        $add_colums = $init->add_columns(campos: $columnas,table:  __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }
        $out->add_colums_base = $add_colums;

        $columnas = new stdClass();
        $columnas->com_tipo_agente_id = new stdClass();
        $columnas->com_tipo_agente_id->tipo_dato = 'BIGINT';
        $columnas->com_tipo_agente_id->longitud = 100;

        $columnas->adm_usuario_id = new stdClass();
        $columnas->adm_usuario_id->tipo_dato = 'BIGINT';
        $columnas->adm_usuario_id->longitud = 100;

        $columnas->nombre = new stdClass();
        $columnas->apellido_paterno = new stdClass();

        $columnas->apellido_materno = new stdClass();
        $columnas->apellido_materno->not_null = false;

        $add_colums = $init->add_columns(campos: $columnas,table:  __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }
        $out->add_colums_entidad = $add_colums;

        $foraneas = array();
        $foraneas['com_tipo_agente_id'] = new stdClass();
        $foraneas['adm_usuario_id'] = new stdClass();

        $result = $init->foraneas(foraneas: $foraneas,table:  __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }
        $out->foraneas = $result;


        return $out;

    }
    private function com_cliente(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));
        $foraneas = array();
        $foraneas['dp_calle_pertenece_id'] = new stdClass();
        $foraneas['cat_sat_regimen_fiscal_id'] = new stdClass();
        $foraneas['cat_sat_moneda_id'] = new stdClass();
        $foraneas['cat_sat_forma_pago_id'] = new stdClass();
        $foraneas['cat_sat_metodo_pago_id'] = new stdClass();
        $foraneas['cat_sat_uso_cfdi_id'] = new stdClass();
        $foraneas['cat_sat_tipo_de_comprobante_id'] = new stdClass();
        $foraneas['com_tipo_cliente_id'] = new stdClass();
        $foraneas['cat_sat_tipo_persona_id'] = new stdClass();
        $foraneas['cat_sat_tipo_persona_id']->default = 6;
        $foraneas['dp_municipio_id'] = new stdClass();
        $foraneas['dp_municipio_id']->default = 2469;

        $com_cliente_modelo = new com_cliente(link: $link);


        $result = $init->foraneas(foraneas: $foraneas,table:  'com_cliente');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }
        $out->foraneas = $result;

        $columnas = new stdClass();
        $columnas->pais = new stdClass();

        $columnas->estado = new stdClass();
        $columnas->municipio = new stdClass();
        $columnas->colonia = new stdClass();
        $columnas->calle = new stdClass();
        $columnas->cp = new stdClass();

        $add_colums = $init->add_columns(campos: $columnas,table:  __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }


        $upds = $this->actualiza_atributos_registro(modelo: $com_cliente_modelo,foraneas:  $foraneas);
        if (errores::$error) {
            return (new errores())->error(mensaje: 'Error al actualizar clientes', data: $upds);
        }

        $com_clientes = $com_cliente_modelo->registros();
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener clientes', data:  $com_clientes);
        }

        $keys_dom = array('pais','estado','municipio','colonia', 'calle','cp');

        $upds_dom = array();
        foreach ($com_clientes as $com_cliente){

            foreach ($keys_dom AS $key_dom){
                $key_entidad = __FUNCTION__."_$key_dom";
                $key_integra = 'dp_'.$key_dom.'_descripcion';

                $com_cliente_bruto = $com_cliente_modelo->registro(registro_id: $com_cliente['com_cliente_id'],
                    columnas_en_bruto: true);
                if(errores::$error){
                    return (new errores())->error(mensaje: 'Error al obtener el cliente',data:  $com_cliente_bruto);
                }
                $dp_calle_pertenece = (new dp_calle_pertenece(link: $link))->registro(
                    registro_id: $com_cliente_bruto['dp_calle_pertenece_id']);
                if(errores::$error){
                    return (new errores())->error(mensaje: 'Error al obtener dp_calle_pertenece',data:  $dp_calle_pertenece);
                }
                if(!isset($com_cliente[$key_entidad])){
                    return (new errores())->error(mensaje: 'Error no existe key '.$key_entidad,data:  $com_cliente);
                }
                if(!isset($dp_calle_pertenece[$key_integra])){
                    return (new errores())->error(mensaje: 'Error no existe key '.$key_integra,data:  $dp_calle_pertenece);
                }

                if(trim($com_cliente[$key_entidad]) === ''){
                    $r_upd = $com_cliente_modelo->modifica_bd(registro: [$key_dom => $dp_calle_pertenece[$key_integra]],
                        id: $com_cliente['com_cliente_id']);
                    if(errores::$error){
                        return (new errores())->error(mensaje: 'Error al modificar cliente', data:  $r_upd);
                    }
                    $upds_dom[] = $r_upd;
                }

            }
        }
        $out->upds_dom = $upds_dom;


        return $out;

    }
    private function com_precio_cliente(PDO $link)
    {
        $init = (new _instalacion(link: $link));
        $foraneas = array();
        $foraneas['com_producto_id'] = new stdClass();
        $foraneas['com_cliente_id'] = new stdClass();
        $foraneas['cat_sat_conf_imps_id'] = new stdClass();

        $result = $init->foraneas(foraneas: $foraneas,table:  __FUNCTION__);

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }


        $campos = new stdClass();


        $campos->precio = new stdClass();
        $campos->precio->tipo_dato = 'double';
        $campos->precio->default = '0';
        $campos->precio->longitud = '100,2';

        $result = $init->add_columns(campos: $campos,table:  __FUNCTION__);

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }
        return $result;

    }
    private function com_producto(PDO $link): array|stdClass
    {
        $init = (new _instalacion(link: $link));
        $com_producto_modelo = new com_producto(link: $link);

        $out = new stdClass();

        $foraneas = array();
        $foraneas['cat_sat_producto_id'] = new stdClass();
        $foraneas['cat_sat_unidad_id'] = new stdClass();
        $foraneas['cat_sat_obj_imp_id'] = new stdClass();
        $foraneas['com_tipo_producto_id'] = new stdClass();
        $foraneas['cat_sat_conf_imps_id'] = new stdClass();

        $result = $init->foraneas(foraneas: $foraneas,table:  'com_producto');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }

        $out->foraneas = $result;

        $campos = new stdClass();

        $campos->aplica_predial = new stdClass();
        $campos->aplica_predial->default = 'inactivo';

        $campos->es_automatico = new stdClass();
        $campos->es_automatico->default = 'inactivo';

        $campos->precio = new stdClass();
        $campos->precio->tipo_dato = 'double';
        $campos->precio->default = '0';
        $campos->precio->longitud = '100,2';

        $campos->codigo_sat = new stdClass();
        $campos->codigo_sat->default = 'POR DEFINIR';

        $result = $init->add_columns(campos: $campos,table:  'com_producto');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar campos', data:  $result);
        }

        $out->campos = $result;

        $com_productos_ins = array();
        $com_producto_ins['id'] = '84111506';
        $com_producto_ins['descripcion'] = 'Servicios de facturación';
        $com_producto_ins['codigo'] = '84111506D';
        $com_producto_ins['codigo_bis'] = '84111506D';
        $com_producto_ins['cat_sat_producto_id'] = '84111506';
        $com_producto_ins['cat_sat_unidad_id'] = '241';
        $com_producto_ins['cat_sat_obj_imp_id'] = '1';
        $com_producto_ins['com_tipo_producto_id'] = '99999999';
        $com_producto_ins['aplica_predial'] = 'inactivo';
        $com_producto_ins['cat_sat_conf_imps_id'] = '1';
        $com_producto_ins['es_automatico'] = 'inactivo';
        $com_producto_ins['precio'] = '0';
        $com_producto_ins['codigo_sat'] = '84111506';

        $com_productos_ins[] = $com_producto_ins;

        foreach ($com_productos_ins as $com_producto_ins){
            $existe = $com_producto_modelo->existe_by_id($com_producto_ins['id']);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al validar si existe com_tipo_producto', data:  $existe);
            }
            if(!$existe) {
                $alta = $com_producto_modelo->alta_registro(registro: $com_producto_ins);
                if (errores::$error) {
                    return (new errores())->error(mensaje: 'Error al insertar producto', data: $alta);
                }
                $out->productos[] = $alta;
            }


        }


        return $out;

    }
    private function com_prospecto(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        $columnas = new stdClass();
        $add_colums = $init->add_columns(campos: $columnas,table:  __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }
        $out->add_colums_base = $add_colums;


        $columnas = new stdClass();
        $columnas->com_agente_id = new stdClass();
        $columnas->com_agente_id->tipo_dato = 'BIGINT';
        $columnas->com_agente_id->longitud = 100;

        $columnas->com_tipo_prospecto_id = new stdClass();
        $columnas->com_tipo_prospecto_id->tipo_dato = 'BIGINT';
        $columnas->com_tipo_prospecto_id->longitud = 100;

        $columnas->nombre = new stdClass();
        $columnas->apellido_paterno = new stdClass();

        $columnas->apellido_materno = new stdClass();
        $columnas->apellido_materno->not_null = false;

        $columnas->telefono = new stdClass();
        $columnas->correo = new stdClass();
        $columnas->razon_social = new stdClass();
        $columnas->rfc = new stdClass();

        $add_colums = $init->add_columns(campos: $columnas,table:  __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }
        $out->add_colums_entidad = $add_colums;


        $foraneas = array();
        $foraneas['com_agente_id'] = new stdClass();
        $foraneas['com_tipo_prospecto_id'] = new stdClass();

        $result = $init->foraneas(foraneas: $foraneas,table:  __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }
        $out->foraneas = $result;


        return $out;

    }
    private function com_rel_agente(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        $columnas = new stdClass();
        $add_colums = $init->add_columns(campos: $columnas,table:  __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }
        $out->add_colums_base = $add_colums;

        $columnas = new stdClass();
        $columnas->com_agente_id = new stdClass();
        $columnas->com_agente_id->tipo_dato = 'BIGINT';
        $columnas->com_agente_id->longitud = 100;

        $columnas->com_prospecto_id = new stdClass();
        $columnas->com_prospecto_id->tipo_dato = 'BIGINT';
        $columnas->com_prospecto_id->longitud = 100;


        $foraneas = array();
        $foraneas['com_agente_id'] = new stdClass();
        $foraneas['com_prospecto_id'] = new stdClass();

        $result = $init->foraneas(foraneas: $foraneas,table:  __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }
        $out->foraneas = $result;


        return $out;

    }
    private function com_sucursal(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $columnas = new stdClass();
        $columnas->dp_municipio_id = new stdClass();
        $columnas->dp_municipio_id->tipo_dato = 'BIGINT';

        $add_colums = $init->add_columns(campos: $columnas,table:  __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }
        //exit;

        $foraneas = array();
        $foraneas['dp_calle_pertenece_id'] = new stdClass();
        $foraneas['com_tipo_sucursal_id'] = new stdClass();
        $foraneas['com_cliente_id'] = new stdClass();
        $foraneas['dp_municipio_id'] = new stdClass();
        $foraneas['dp_municipio_id']->default = 2469;

        $com_sucursal_modelo = new com_sucursal(link: $link);
        $com_sucursal_modelo->transaccion_desde_cliente = true;

        $upds = $this->actualiza_atributos_registro(modelo: $com_sucursal_modelo,foraneas:  $foraneas);
        if (errores::$error) {
            return (new errores())->error(mensaje: 'Error al actualizar sucursales', data: $upds);
        }


        $result = $init->foraneas(foraneas: $foraneas,table:  __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }
        $out->foraneas = $result;

        $columnas = new stdClass();
        $columnas->pais = new stdClass();
        $columnas->estado = new stdClass();
        $columnas->municipio = new stdClass();
        $columnas->colonia = new stdClass();
        $columnas->calle = new stdClass();
        $columnas->cp = new stdClass();

        $add_colums = $init->add_columns(campos: $columnas,table:  __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }

        $com_sucursales = $com_sucursal_modelo->registros();
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener com_sucursales', data:  $com_sucursales);
        }
        $keys_dom = array('pais','estado','municipio','colonia', 'calle','cp');
        $upds_dom = array();
        foreach ($com_sucursales as $com_sucursal){

            foreach ($keys_dom AS $key_dom) {
                $key_entidad = __FUNCTION__ . "_$key_dom";
                $key_integra = 'dp_' . $key_dom . '_descripcion';

                $com_sucursal_bruto = $com_sucursal_modelo->registro(registro_id: $com_sucursal['com_sucursal_id'], columnas_en_bruto: true);
                if (errores::$error) {
                    return (new errores())->error(mensaje: 'Error al obtener el com_sucursal', data: $com_sucursal_bruto);
                }
                $dp_calle_pertenece = (new dp_calle_pertenece(link: $link))->registro(registro_id: $com_sucursal_bruto['dp_calle_pertenece_id']);
                if (errores::$error) {
                    return (new errores())->error(mensaje: 'Error al obtener dp_calle_pertenece', data: $dp_calle_pertenece);
                }
                if (!isset($com_sucursal[$key_entidad])) {
                    return (new errores())->error(mensaje: 'Error no existe key ' . $key_entidad, data: $com_sucursal);
                }
                if (!isset($dp_calle_pertenece[$key_integra])) {
                    return (new errores())->error(mensaje: 'Error no existe key ' . $key_integra, data: $dp_calle_pertenece);
                }

                if (trim($com_sucursal[$key_entidad]) === '') {
                    $r_upd = $com_sucursal_modelo->modifica_bd(registro: [$key_dom => $dp_calle_pertenece[$key_integra]],
                        id: $com_sucursal['com_sucursal_id']);
                    if (errores::$error) {
                        return (new errores())->error(mensaje: 'Error al modificar com_sucursal', data: $r_upd);
                    }
                    $upds_dom[] = $r_upd;
                }
            }

        }

        $out->upds_dom = $upds_dom;

        $adm_menu_descripcion = 'Clientes';
        $adm_menu_modelo = new adm_menu(link: $link);

        $row_ins = array();
        $row_ins['descripcion'] = $adm_menu_descripcion;
        $row_ins['etiqueta_label'] = 'Clientes';
        $row_ins['icono'] = 'SI';
        $row_ins['titulo'] = 'Clientes';

        $adm_menu_id = $init->data_adm(descripcion: $adm_menu_descripcion,modelo:  $adm_menu_modelo, row_ins: $row_ins);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener adm_menu_id', data:  $adm_menu_id);
        }

        $out->adm_menu_id = $adm_menu_id;


        $adm_namespace_descripcion = 'gamboa.martin/comercial';
        $adm_namespace_modelo = new adm_namespace(link: $link);

        $row_ins = array();
        $row_ins['descripcion'] = $adm_namespace_descripcion;
        $row_ins['name'] = 'gamboamartin/comercial';


        $adm_namespace_id = $init->data_adm(descripcion: $adm_namespace_descripcion,modelo:  $adm_namespace_modelo, row_ins: $row_ins);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener adm_namespace_id', data:  $adm_namespace_id);
        }
        $out->adm_namespace_id = $adm_namespace_id;

        $adm_seccion_descripcion = __FUNCTION__;
        $adm_seccion_modelo = new adm_seccion(link: $link);

        $row_ins = array();
        $row_ins['descripcion'] = $adm_seccion_descripcion;
        $row_ins['etiqueta_label'] = 'Sucursal de Cliente';
        $row_ins['adm_menu_id'] = $adm_menu_id;
        $row_ins['adm_namespace_id'] = $adm_namespace_id;


        $adm_seccion_id = $init->data_adm(descripcion: $adm_seccion_descripcion,modelo:  $adm_seccion_modelo, row_ins: $row_ins);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener adm_seccion_id', data:  $adm_seccion_id);
        }

        $out->adm_seccion_id = $adm_seccion_id;

        $adm_sistema_descripcion = 'comercial';
        $adm_sistema_modelo = new adm_sistema(link: $link);

        $row_ins = array();
        $row_ins['descripcion'] = $adm_sistema_descripcion;

        $adm_sistema_id = $init->data_adm(descripcion: $adm_sistema_descripcion,modelo:  $adm_sistema_modelo, row_ins: $row_ins);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener adm_sistema_id', data:  $adm_sistema_id);
        }

        $out->adm_sistema_id = $adm_sistema_id;

        $adm_seccion_pertenece_descripcion = 'comercial';
        $adm_seccion_pertenece_modelo = new adm_seccion_pertenece(link: $link);

        $row_ins = array();
        $row_ins['adm_sistema_id'] = $adm_sistema_id;
        $row_ins['adm_seccion_id'] = $adm_seccion_id;

        $filtro['adm_seccion.id'] = $adm_seccion_id;
        $filtro['adm_sistema.id'] = $adm_sistema_id;

        $adm_seccion_pertenece_id = $init->data_adm(descripcion: $adm_seccion_pertenece_descripcion,
            modelo:  $adm_seccion_pertenece_modelo, row_ins: $row_ins, filtro: $filtro);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener adm_seccion_pertenece_id', data:  $adm_seccion_pertenece_id);
        }

        $out->adm_seccion_pertenece_id = $adm_seccion_pertenece_id;


        return $out;

    }
    private function com_tels_agente(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        $columnas = new stdClass();
        $add_colums = $init->add_columns(campos: $columnas,table:  __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }
        $out->add_colums_base = $add_colums;

        $columnas = new stdClass();
        $columnas->com_agente_id = new stdClass();
        $columnas->com_agente_id->tipo_dato = 'BIGINT';
        $columnas->com_agente_id->longitud = 100;

        $columnas->com_tipo_tel_id = new stdClass();
        $columnas->com_tipo_tel_id->tipo_dato = 'BIGINT';
        $columnas->com_tipo_tel_id->longitud = 100;


        $foraneas = array();
        $foraneas['com_agente_id'] = new stdClass();
        $foraneas['com_tipo_tel_id'] = new stdClass();

        $result = $init->foraneas(foraneas: $foraneas,table:  __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }
        $out->foraneas = $result;


        return $out;

    }
    private function com_tipo_agente(PDO $link): array|stdClass
    {
        $init = (new _instalacion(link: $link));
        $com_tipo_producto_modelo = new com_tipo_producto(link: $link);

        $out = new stdClass();


        $campos = new stdClass();
        $create_table = $init->create_table_new(table: __FUNCTION__);
        if (errores::$error) {
            return (new errores())->error(mensaje: 'Error al crear table '.__FUNCTION__, data: $create_table);
        }
        $out->create_table = $create_table;


        return $out;

    }
    private function com_tipo_producto(PDO $link): array|stdClass
    {
        $init = (new _instalacion(link: $link));
        $com_tipo_producto_modelo = new com_tipo_producto(link: $link);

        $out = new stdClass();

        $existe_entidad = $init->existe_entidad(table: __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al verificar table', data:  $existe_entidad);
        }
        $out->existe_entidad = $existe_entidad;


        if(!$existe_entidad) {

            $campos = new stdClass();
            $create_table = $init->create_table(campos: $campos, table: __FUNCTION__);
            if (errores::$error) {
                return (new errores())->error(mensaje: 'Error al crear table '.__FUNCTION__, data: $create_table);
            }
            $out->create_table = $create_table;
        }


        $com_tipo_productos_ins = array();
        $com_tipo_producto_ins['id'] = '99999999';
        $com_tipo_producto_ins['descripcion'] = 'Servicios de facturación';
        $com_tipo_producto_ins['codigo'] = '99999999';

        $com_tipo_productos_ins[] = $com_tipo_producto_ins;

        foreach ($com_tipo_productos_ins as $com_tipo_producto_ins){

            $existe = $com_tipo_producto_modelo->existe_by_id($com_tipo_producto_ins['id']);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al validar si existe com_tipo_producto', data:  $existe);
            }
            if(!$existe) {
                $alta = $com_tipo_producto_modelo->alta_registro(registro: $com_tipo_producto_ins);
                if (errores::$error) {
                    return (new errores())->error(mensaje: 'Error al insertar com_tipo_producto_ins', data: $alta);
                }
                $out->com_tipo_producto[] = $alta;
            }

        }

        return $out;

    }

    private function com_tipo_prospecto(PDO $link): array|stdClass
    {
        $init = (new _instalacion(link: $link));
        $com_tipo_producto_modelo = new com_tipo_producto(link: $link);

        $out = new stdClass();


        $campos = new stdClass();
        $create_table = $init->create_table_new(table: __FUNCTION__);
        if (errores::$error) {
            return (new errores())->error(mensaje: 'Error al crear table '.__FUNCTION__, data: $create_table);
        }
        $out->create_table = $create_table;


        return $out;

    }
    private function com_tipo_tel(PDO $link): array|stdClass
    {
        $init = (new _instalacion(link: $link));
        $com_tipo_producto_modelo = new com_tipo_producto(link: $link);

        $out = new stdClass();


        $campos = new stdClass();
        $create_table = $init->create_table_new(table: __FUNCTION__);
        if (errores::$error) {
            return (new errores())->error(mensaje: 'Error al crear table '.__FUNCTION__, data: $create_table);
        }
        $out->create_table = $create_table;


        return $out;

    }
    final public function instala(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $com_tipo_producto = $this->com_tipo_producto(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_tipo_producto', data:  $com_tipo_producto);
        }
        $com_tipo_agente = $this->com_tipo_agente(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_tipo_agente', data:  $com_tipo_agente);
        }
        $com_tipo_tel = $this->com_tipo_tel(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_tipo_tel', data:  $com_tipo_tel);
        }
        $com_tipo_prospecto = $this->com_tipo_prospecto(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_tipo_prospecto', data:  $com_tipo_prospecto);
        }
        $out->com_tipo_prospecto = $com_tipo_prospecto;

        $com_cliente = $this->com_cliente(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_cliente', data:  $com_cliente);
        }
        $out->com_cliente = $com_cliente;

        $com_sucursal = $this->com_sucursal(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_sucursal', data:  $com_sucursal);
        }
        $out->com_sucursal = $com_sucursal;

        $com_producto = $this->com_producto(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_producto', data:  $com_producto);
        }
        $out->com_producto = $com_producto;

        $com_precio_cliente = $this->com_precio_cliente(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_precio_cliente', data:  $com_precio_cliente);
        }
        $out->com_precio_cliente = $com_precio_cliente;

        $com_agente = $this->com_agente(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_agente', data:  $com_agente);
        }
        $out->com_agente = $com_agente;

        $com_prospecto = $this->com_prospecto(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_prospecto', data:  $com_prospecto);
        }
        $out->com_prospecto = $com_prospecto;

        $com_tels_agente = $this->com_tels_agente(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_tels_agente', data:  $com_tels_agente);
        }

        $com_rel_agente = $this->com_rel_agente(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_rel_agente', data:  $com_rel_agente);
        }
        $out->com_agente = $com_agente;

        return $out;

    }
    private function upd_default(string $campo_validar, stdClass $registro, modelo $modelo,
                                 string $value_default): array|stdClass
    {
        $row_upd[$campo_validar] = $value_default;

        $upd = $modelo->modifica_bd(registro: $row_upd,id:  $registro->id);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al actualizar cliente', data:  $upd);
        }

        return $upd;

    }
    private function upd_row_default(string $campo_validar, modelo $modelo, array $registros, string $value_default): array
    {
        $upds = array();
        foreach ($registros as $registro){

            if(isset($registro->$campo_validar)){
                $identificador_validar = (int)trim($registro->$campo_validar);

                if($identificador_validar === 0){

                    $upd = $this->upd_default(campo_validar: $campo_validar,registro:  $registro,
                        modelo:  $modelo,value_default:  $value_default);
                    if(errores::$error){
                        return (new errores())->error(mensaje: 'Error al actualizar registro', data:  $upd);
                    }
                    $upds[] = $upd;
                }

            }
        }
        return $upds;

    }

}
