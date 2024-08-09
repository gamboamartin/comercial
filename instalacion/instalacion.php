<?php
namespace gamboamartin\comercial\instalacion;
use base\orm\modelo;
use gamboamartin\administrador\instalacion\_adm;
use gamboamartin\administrador\models\_instalacion;
use gamboamartin\administrador\models\adm_accion;
use gamboamartin\administrador\models\adm_seccion;
use gamboamartin\cat_sat\models\cat_sat_cve_prod;
use gamboamartin\comercial\models\com_cliente;
use gamboamartin\comercial\models\com_medio_prospeccion;
use gamboamartin\comercial\models\com_producto;
use gamboamartin\comercial\models\com_sucursal;
use gamboamartin\comercial\models\com_tipo_cliente;
use gamboamartin\comercial\models\com_tipo_producto;
use gamboamartin\comercial\models\com_tipo_sucursal;
use gamboamartin\comercial\models\com_tmp_prod_cs;
use gamboamartin\direccion_postal\models\dp_calle_pertenece;
use gamboamartin\errores\errores;
use gamboamartin\proceso\models\pr_etapa;
use gamboamartin\proceso\models\pr_etapa_proceso;
use gamboamartin\proceso\models\pr_proceso;
use gamboamartin\proceso\models\pr_tipo_proceso;
use PDO;
use stdClass;


class instalacion
{
    private function _add_com_cliente(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'com_cliente');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;


        $campos = new stdClass();

        $campos->razon_social = new stdClass();
        $campos->rfc = new stdClass();
        $campos->numero_exterior = new stdClass();
        $campos->numero_interior = new stdClass();
        $campos->telefono = new stdClass();
        $campos->pais = new stdClass();
        $campos->estado = new stdClass();
        $campos->municipio = new stdClass();
        $campos->colonia = new stdClass();
        $campos->calle = new stdClass();
        $campos->cp = new stdClass();


        $result = $init->add_columns(campos: $campos,table:  'com_cliente');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar campos', data:  $result);
        }

        $foraneas = array();
        $foraneas['cat_sat_regimen_fiscal_id'] = new stdClass();
        $foraneas['cat_sat_moneda_id'] = new stdClass();
        $foraneas['cat_sat_forma_pago_id'] = new stdClass();
        $foraneas['cat_sat_metodo_pago_id'] = new stdClass();
        $foraneas['cat_sat_uso_cfdi_id'] = new stdClass();
        $foraneas['cat_sat_tipo_de_comprobante_id'] = new stdClass();
        $foraneas['com_tipo_cliente_id'] = new stdClass();
        $foraneas['cat_sat_tipo_persona_id'] = new stdClass();
        $foraneas['dp_municipio_id'] = new stdClass();

        $foraneas = $init->foraneas(foraneas: $foraneas, table: 'com_cliente');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar foraneas', data:  $foraneas);
        }
        $out->foraneas = $foraneas;



        return $out;
    }

    private function _add_com_conf_precio(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'com_conf_precio');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        $foraneas = array();
        $foraneas['com_producto_id'] = new stdClass();
        $foraneas['com_tipo_cliente_id'] = new stdClass();

        $result = $init->foraneas(foraneas: $foraneas,table:  'com_conf_precio');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }

        $out->foraneas = $result;

        $campos = new stdClass();


        $campos->precio = new stdClass();
        $campos->precio->tipo_dato = 'double';
        $campos->precio->default = '0';
        $campos->precio->longitud = '100,2';

        $result = $init->add_columns(campos: $campos,table:  'com_conf_precio');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar campos', data:  $result);
        }

        $out->campos = $result;

        return $out;
    }
    private function _add_com_direccion(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'com_direccion');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        $foraneas = array();
        $foraneas['com_tipo_direccion_id'] = new stdClass();
        $foraneas['dp_calle_pertenece_id'] = new stdClass();
        $foraneas['dp_municipio_id'] = new stdClass();


        $result = $init->foraneas(foraneas: $foraneas,table:  'com_direccion');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }

        $campos = new stdClass();

        $campos->texto_interior = new stdClass();
        $campos->texto_exterior = new stdClass();
        $campos->cp = new stdClass();
        $campos->colonia = new stdClass();
        $campos->calle = new stdClass();


        $result = $init->add_columns(campos: $campos,table:  'com_direccion');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar campos', data:  $result);
        }



        return $out;
    }

    private function _add_com_email_cte(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'com_email_cte');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        $foraneas = array();
        $foraneas['com_cliente_id'] = new stdClass();

        $foraneas = $init->foraneas(foraneas: $foraneas, table: 'com_email_cte');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar foraneas', data:  $foraneas);
        }
        $out->foraneas = $foraneas;


        return $out;
    }

    private function _add_com_contacto(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'com_contacto');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        $foraneas = array();
        $foraneas['com_tipo_contacto_id'] = new stdClass();
        $foraneas['com_cliente_id'] = new stdClass();

        $foraneas = $init->foraneas(foraneas: $foraneas, table: 'com_contacto');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar foraneas', data:  $foraneas);
        }
        $out->foraneas = $foraneas;


        $campos = new stdClass();

        $campos->correo = new stdClass();
        $campos->nombre = new stdClass();
        $campos->ap = new stdClass();
        $campos->am = new stdClass();
        $campos->telefono = new stdClass();

        $result = $init->add_columns(campos: $campos,table:  'com_contacto');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar campos', data:  $result);
        }



        return $out;
    }

    private function _add_com_contacto_user(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'com_contacto_user');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        $foraneas = array();
        $foraneas['com_contacto_id'] = new stdClass();
        $foraneas['adm_usuario_id'] = new stdClass();

        $foraneas = $init->foraneas(foraneas: $foraneas, table: 'com_contacto_user');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar foraneas', data:  $foraneas);
        }
        $out->foraneas = $foraneas;


        return $out;
    }

    private function _add_com_medio_prospeccion(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'com_medio_prospeccion');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        return $out;
    }
    private function _add_com_producto(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'com_producto');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        $columnas = new stdClass();
        $add_colums = $init->add_columns(campos: $columnas,table:  'com_producto');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }
        $out->add_colums_base = $add_colums;


        $foraneas = array();
        $foraneas['cat_sat_producto_id'] = new stdClass();
        $foraneas['cat_sat_unidad_id'] = new stdClass();
        $foraneas['cat_sat_obj_imp_id'] = new stdClass();
        $foraneas['com_tipo_producto_id'] = new stdClass();
        $foraneas['cat_sat_conf_imps_id'] = new stdClass();
        $foraneas['cat_sat_cve_prod_id'] = new stdClass();
        $foraneas['cat_sat_cve_prod_id']->default = '1010101';

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

        return $out;
    }
    private function _add_com_prospecto_etapa(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'com_prospecto_etapa');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        $foraneas = array();
        $foraneas['com_prospecto_id'] = new stdClass();
        $foraneas['pr_etapa_proceso_id'] = new stdClass();

        $foraneas = $init->foraneas(foraneas: $foraneas, table: 'com_prospecto_etapa');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar foraneas', data:  $foraneas);
        }
        $out->foraneas = $foraneas;

        $campos = new stdClass();
        $campos->fecha = new stdClass();
        $campos->fecha->tipo_dato = 'datetime';
        $campos->fecha->default = '1900-01-01';

        $result = $init->add_columns(campos: $campos,table:  'com_prospecto_etapa');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar campos', data:  $result);
        }

        return $out;
    }
    private function _add_com_datos_sistema(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'com_datos_sistema');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        $campos = new stdClass();
        $campos->pagina_oficial = new stdClass();
        $campos->telefonos = new stdClass();
        $campos->domicilio = new stdClass();
        $campos->correos = new stdClass();
        $campos->latitud = new stdClass();
        $campos->longitud = new stdClass();

        $result = $init->add_columns(campos: $campos,table:  'com_datos_sistema');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar campos', data:  $result);
        }

        return $out;
    }
    private function _add_com_rel_prospecto_cte(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'com_rel_prospecto_cte');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        $foraneas = array();
        $foraneas['com_cliente_id'] = new stdClass();
        $foraneas['com_prospecto_id'] = new stdClass();

        $foraneas = $init->foraneas(foraneas: $foraneas, table: 'com_rel_prospecto_cte');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar foraneas', data:  $foraneas);
        }
        $out->foraneas = $foraneas;



        return $out;
    }
    private function _add_com_sucursal(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'com_sucursal');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        $foraneas = array();
        $foraneas['com_tipo_sucursal_id'] = new stdClass();
        $foraneas['com_cliente_id'] = new stdClass();
        $foraneas['dp_municipio_id'] = new stdClass();
        $foraneas['dp_municipio_id']->default = 2469;

        $foraneas = $init->foraneas(foraneas: $foraneas, table: 'com_sucursal');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar foraneas', data:  $foraneas);
        }
        $out->foraneas = $foraneas;

        unset($_SESSION['entidades_bd']);

        $com_sucursal_modelo = new com_sucursal(link: $link);
        $com_sucursal_modelo->transaccion_desde_cliente = true;

        $upds = $this->actualiza_atributos_registro(modelo: $com_sucursal_modelo,foraneas:  $foraneas);
        if (errores::$error) {
            return (new errores())->error(mensaje: 'Error al actualizar sucursales', data: $upds);
        }




        return $out;
    }
    private function _add_com_tipo_cliente(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'com_tipo_cliente');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;



        return $out;
    }
    private function _add_com_tipo_direccion(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'com_tipo_direccion');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;



        return $out;
    }
    private function _add_com_tipo_cambio(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'com_tipo_cambio');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        $columnas = new stdClass();
        $add_colums = $init->add_columns(campos: $columnas,table:  'com_tipo_cambio');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }
        $out->add_colums_base = $add_colums;


        $foraneas = array();
        $foraneas['cat_sat_moneda_id'] = new stdClass();

        $result = $init->foraneas(foraneas: $foraneas,table:  'com_tipo_cambio');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }

        $out->foraneas = $result;

        $campos = new stdClass();

        $campos->monto = new stdClass();
        $campos->monto->default = '1';
        $campos->monto->tipo_dato = 'double';
        $campos->monto->longitud = '100,4';

        $campos->fecha = new stdClass();
        $campos->fecha->default = '1900-01-01';
        $campos->fecha->tipo_dato = 'DATE';
        $campos->fecha->longitud = '';


        $result = $init->add_columns(campos: $campos,table:  'com_tipo_cambio');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar campos', data:  $result);
        }

        $out->campos = $result;

        return $out;
    }
    private function _add_com_tipo_contacto(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'com_tipo_contacto');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;


        return $out;
    }
    private function _add_com_tmp_prod_cs(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'com_tmp_prod_cs');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        $columnas = new stdClass();
        $add_colums = $init->add_columns(campos: $columnas,table:  'com_tmp_prod_cs');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }
        $out->add_colums_base = $add_colums;


        $foraneas = array();
        $foraneas['com_producto_id'] = new stdClass();
        $foraneas['cat_sat_producto_id'] = new stdClass();

        $result = $init->foraneas(foraneas: $foraneas,table:  'com_tmp_prod_cs');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }

        $out->foraneas = $result;

        $campos = new stdClass();

        $campos->cat_sat_producto = new stdClass();
        $campos->cat_sat_producto->default = '01010101';


        $result = $init->add_columns(campos: $campos,table:  'com_tmp_prod_cs');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar campos', data:  $result);
        }

        $out->campos = $result;

        return $out;
    }
    private function _add_com_tipo_sucursal(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'com_tipo_sucursal');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;


        return $out;
    }
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
        $foraneas['dp_municipio_id'] = new stdClass();
        $foraneas['dp_municipio_id']->default = 2469;
        $result = $init->foraneas(foraneas: $foraneas,table:  'com_sucursal');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }
        $out->foraneas_suc = $result;

        //$com_cliente_modelo = new com_cliente(link: $link);


        $result = $init->foraneas(foraneas: $foraneas,table:  __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }
        $out->foraneas = $result;

        $foraneas = array();
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



        $adm_menu_descripcion = 'Clientes';
        $adm_sistema_descripcion = 'comercial';
        $etiqueta_label = 'Clientes';
        $adm_seccion_pertenece_descripcion = 'com_cliente';
        $adm_namespace_descripcion = 'gamboa.martin/comercial';
        $adm_namespace_name = 'gamboamartin/comercial';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__, adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion, etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }


        $alta_accion = (new _adm())->inserta_accion_base(adm_accion_descripcion: 'correo',
            adm_seccion_descripcion: __FUNCTION__, es_view: 'activo', icono: 'bi bi-mailbox',
            link: $link, lista: 'activo', titulo: 'Correos');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar accion',data:  $alta_accion);
        }
        $out->es_automatico = $alta_accion;

        $alta_accion = (new _adm())->inserta_accion_base(adm_accion_descripcion: 'documentos',
            adm_seccion_descripcion: __FUNCTION__, es_view: 'activo', icono: 'bi bi-files',
            link: $link, lista: 'activo', titulo: 'Documentos');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar accion',data:  $alta_accion);
        }
        $out->es_automatico = $alta_accion;

        $alta_accion = (new _adm())->inserta_accion_base(adm_accion_descripcion: 'get_cliente',
            adm_seccion_descripcion: __FUNCTION__, es_view: 'inactivo', icono: 'bi bi-mailbox',
            link: $link, lista: 'inactivo', titulo: 'Get Cliente');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar accion',data:  $alta_accion);
        }
        $out->es_automatico = $alta_accion;

        $alta_accion = (new _adm())->inserta_accion_base(adm_accion_descripcion: 'tipos_documentos',
            adm_seccion_descripcion: __FUNCTION__, es_view: 'inactivo', icono: 'bi bi-mailbox',
            link: $link, lista: 'inactivo', titulo: 'Tipos Doc');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar accion',data:  $alta_accion);
        }
        $out->es_automatico = $alta_accion;

        $alta_accion = (new _adm())->inserta_accion_base(adm_accion_descripcion: 'subir_documento',
            adm_seccion_descripcion: __FUNCTION__, es_view: 'activo', icono: 'bi bi-cloud-upload-fill',
            link: $link, lista: 'activo', titulo: 'Subir Documentos');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar accion',data:  $alta_accion);
        }
        $out->es_automatico = $alta_accion;


        $inserta_campos = (new _instalacion(link: $link))->inserta_adm_campos(
            modelo_integracion: (new com_cliente(link: $link)));
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar adm campos', data:  $inserta_campos);
        }


        return $out;

    }

    private function com_email_cte(PDO $link): array|stdClass
    {

        $out = new stdClass();

        $add = $this->_add_com_email_cte(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add);
        }

        $adm_menu_descripcion = 'Etapas';
        $adm_sistema_descripcion = 'comercial';
        $etiqueta_label = 'Emails Clientes';
        $adm_seccion_pertenece_descripcion = 'com_email_cte';
        $adm_namespace_descripcion = 'gamboa.martin/comercial';
        $adm_namespace_name = 'gamboamartin/comercial';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__, adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion, etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }



        return $out;

    }
    private function com_datos_sistema(PDO $link): array|stdClass
    {

        $out = new stdClass();

        $add = $this->_add_com_datos_sistema(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add);
        }

        $adm_menu_descripcion = 'Datos Sistema';
        $adm_sistema_descripcion = 'comercial';
        $etiqueta_label = 'Datos Sistema';
        $adm_seccion_pertenece_descripcion = 'com_datos_sistema';
        $adm_namespace_descripcion = 'gamboa.martin/comercial';
        $adm_namespace_name = 'gamboamartin/comercial';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__, adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion, etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }



        return $out;

    }

    private function com_contacto(PDO $link): array|stdClass
    {

        $out = new stdClass();

        $add = $this->_add_com_contacto(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add);
        }

        $adm_menu_descripcion = 'Clientes';
        $adm_sistema_descripcion = 'comercial';
        $etiqueta_label = 'Contactos';
        $adm_seccion_pertenece_descripcion = 'com_contacto';
        $adm_namespace_descripcion = 'gamboa.martin/comercial';
        $adm_namespace_name = 'gamboamartin/comercial';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__, adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion, etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }


        $alta_accion = (new _adm())->inserta_accion_base(adm_accion_descripcion: 'genera_usuario',
            adm_seccion_descripcion:  __FUNCTION__, es_view: 'activo', icono: 'bi bi-file-earmark-person',
            link:  $link, lista:  'activo',titulo:  'Genera User', es_status: 'inactivo');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar accion',data:  $alta_accion);
        }
        $alta_accion = (new _adm())->inserta_accion_base(adm_accion_descripcion: 'genera_usuario_bd',
            adm_seccion_descripcion:  __FUNCTION__, es_view: 'inactivo', icono: 'bi bi-file-earmark-person',
            link:  $link, lista:  'inactivo',titulo:  'Genera User');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar accion',data:  $alta_accion);
        }
        $out->es_automatico = $alta_accion;

        return $out;

    }

    private function com_contacto_user(PDO $link): array|stdClass
    {

        $out = new stdClass();

        $add = $this->_add_com_contacto_user(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add);
        }

        $adm_menu_descripcion = 'Clientes';
        $adm_sistema_descripcion = 'comercial';
        $etiqueta_label = 'Usuarios de cliente';
        $adm_seccion_pertenece_descripcion = 'com_contacto_user';
        $adm_namespace_descripcion = 'gamboa.martin/comercial';
        $adm_namespace_name = 'gamboamartin/comercial';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__, adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion, etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }

        $alta_accion = (new _adm())->inserta_accion_base(adm_accion_descripcion: 'envia_acceso',
            adm_seccion_descripcion:  __FUNCTION__, es_view: 'inactivo', icono: 'bi bi-universal-access',
            link:  $link, lista:  'activo',titulo:  'Envia Accesos');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar accion',data:  $alta_accion);
        }
        $out->es_automatico = $alta_accion;


        return $out;

    }
    private function com_precio_cliente(PDO $link): stdClass|array
    {
        $out = new stdClass();

        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

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
        $out = new stdClass();

        $create = $this->_add_com_producto(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->create = $create;

        $create = $this->_add_com_tmp_prod_cs(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create_com_tmp_prod_cs = $create;


        $com_producto_modelo = new com_producto(link: $link);

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
        $com_producto_ins['cat_sat_cve_prod_id'] = '84111506';

        $com_productos_ins[] = $com_producto_ins;


        $com_producto_ins['id'] = '99999999';
        $com_producto_ins['descripcion'] = 'Pago';
        $com_producto_ins['codigo'] = '99999999';
        $com_producto_ins['codigo_bis'] = '99999999';
        $com_producto_ins['cat_sat_producto_id'] = '84111506';
        $com_producto_ins['cat_sat_unidad_id'] = '241';
        $com_producto_ins['cat_sat_obj_imp_id'] = '1';
        $com_producto_ins['com_tipo_producto_id'] = '99999999';
        $com_producto_ins['aplica_predial'] = 'inactivo';
        $com_producto_ins['cat_sat_conf_imps_id'] = '1';
        $com_producto_ins['es_automatico'] = 'inactivo';
        $com_producto_ins['precio'] = '0';
        $com_producto_ins['codigo_sat'] = '84111506';
        $com_producto_ins['cat_sat_cve_prod_id'] = '84111506';

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
            else{

                if((int)$com_producto_ins['id'] === 84111506){
                    $com_producto_r = $com_producto_modelo->registro(registro_id: $com_producto_ins['id']);
                    if (errores::$error) {
                        return (new errores())->error(mensaje: 'Error al obtener producto', data: $com_producto_r);
                    }
                    if((int)$com_producto_r['com_producto_codigo'] !== 84111506){
                        $upd_p['codigo'] = 84111506;
                        $com_producto_upd = $com_producto_modelo->modifica_bd(registro: $upd_p,id: 84111506);
                        if (errores::$error) {
                            return (new errores())->error(mensaje: 'Error al modificar producto', data: $com_producto_upd);
                        }
                    }
                }
            }
        }

        $com_productos = $com_producto_modelo->registros();
        if (errores::$error) {
            return (new errores())->error(mensaje: 'Error al obtener productos', data: $com_productos);
        }

        $upds = array();
        foreach ($com_productos as $com_producto){

            $com_producto_bruto = $com_producto_modelo->registro(registro_id: $com_producto['com_producto_id'],columnas_en_bruto: true);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al verificar com_producto_bruto', data: $com_producto_bruto);
            }

            if($com_producto['com_producto_codigo_sat'] !== 'SIN ASIGNAR'){
                $com_producto_upd = array();
                if(!is_numeric($com_producto['com_producto_codigo_sat'])){
                    continue;
                }
                $com_producto_upd['cat_sat_cve_prod_id'] = $com_producto['com_producto_codigo_sat'];

                $existe_prod = (new cat_sat_cve_prod(link: $link))->existe_by_id(registro_id: $com_producto['com_producto_codigo_sat']);
                if(errores::$error){
                    return (new errores())->error(mensaje: 'Error al verificar si existe', data: $existe_prod);
                }
                if(!$existe_prod){
                    $com_producto_upd['cat_sat_cve_prod_id'] = '1010101';
                    $com_producto_upd['codigo_sat'] = '1010101';
                }

                $upd = $com_producto_modelo->modifica_bd(registro: $com_producto_upd, id: $com_producto['com_producto_id']);
                if (errores::$error) {
                    return (new errores())->error(mensaje: 'Error al actualizar producto', data: $upd);
                }
                $upds[] = $upd;

            }

            if((int)$com_producto_bruto['cat_sat_producto_id'] !== 97999999 && (int)$com_producto_bruto['cat_sat_producto_id'] !== 1){
                $com_producto_upd = array();
                $com_producto_upd['cat_sat_cve_prod_id'] = $com_producto_bruto['cat_sat_producto_id'];
                $upd = $com_producto_modelo->modifica_bd(registro: $com_producto_upd,id:  $com_producto['com_producto_id']);
                if(errores::$error){
                    return (new errores())->error(mensaje: 'Error al actualizar producto', data: $upd);
                }
                $upds[] = $upd;
            }
        }

        $com_tmp_prod_css = (new com_tmp_prod_cs(link: $link))->registros();
        foreach ($com_tmp_prod_css as $com_tmp_prod_cs){
            $com_producto_upd = array();
            $com_producto_id = $com_tmp_prod_cs['com_producto_id'];
            $cat_sat_producto = $com_tmp_prod_cs['com_tmp_prod_cs_cat_sat_producto'];
            if(is_null($com_producto_id)){
                continue;
            }

            $existe_cat_sat = (new cat_sat_cve_prod(link: $link))->existe_by_id(registro_id: $cat_sat_producto);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al obtener producto', data: $existe_cat_sat);
            }

            if(!$existe_cat_sat){

                $cat_sat_producto = '1010101';
            }

            $com_producto_upd['cat_sat_cve_prod_id'] = $cat_sat_producto;
            $upd = $com_producto_modelo->modifica_bd(registro: $com_producto_upd,id:  $com_producto_id);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al actualizar producto', data: $upd);
            }
            $upds[] = $upd;
        }

        $dels = array();
        foreach ($com_tmp_prod_css as $com_tmp_prod_cs){
            $del = (new com_tmp_prod_cs(link: $link))->elimina_bd(id: $com_tmp_prod_cs['com_tmp_prod_cs_id']);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al del com_tmp_prod_cs', data: $del);
            }
            $dels[] = $del;
        }

        $com_productos = $com_producto_modelo->registros();
        if (errores::$error) {
            return (new errores())->error(mensaje: 'Error al obtener productos', data: $com_productos);
        }
        foreach ($com_productos as $com_producto){
            if($com_producto['com_producto_codigo_sat'] === 'SIN ASIGNAR'){

                $com_producto_upd = array();
                $com_producto_upd['cat_sat_cve_prod_id'] = $com_producto['cat_sat_cve_prod_id'];
                $com_producto_upd['codigo_sat'] = $com_producto['cat_sat_cve_prod_id'];



                $upd = $com_producto_modelo->modifica_bd(registro: $com_producto_upd,
                    id:  $com_producto['com_producto_id']);
                if(errores::$error){
                    return (new errores())->error(mensaje: 'Error al actualizar producto', data: $upd);
                }
                $upds[] = $upd;
            }
        }


        $adm_menu_descripcion = 'Productos';
        $adm_sistema_descripcion = 'comercial';
        $etiqueta_label = 'Productos';
        $adm_seccion_pertenece_descripcion = 'comercial';
        $adm_namespace_descripcion = 'gamboa.martin/comercial';
        $adm_namespace_name = 'gamboamartin/comercial';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__, adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion, etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }

        $alta_accion = (new _adm())->inserta_accion_base(adm_accion_descripcion: 'es_automatico',
            adm_seccion_descripcion:  __FUNCTION__, es_view: 'inactivo', icono: 'bi bi-arrow-up-short',
            link:  $link, lista:  'activo',titulo:  'Es Automatico', es_status: 'activo');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar accion',data:  $alta_accion);
        }
        $out->es_automatico = $alta_accion;

        $filtro['adm_accion.descripcion'] = 'nueva_conf_traslado';
        $filtro['adm_seccion.descripcion'] = __FUNCTION__;

        $del = (new adm_accion(link: $link))->elimina_con_filtro_and(filtro: $filtro);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al eliminar', data:  $del);
        }

        $filtro['adm_accion.descripcion'] = 'nueva_conf_retenido';
        $filtro['adm_seccion.descripcion'] = __FUNCTION__;

        $del = (new adm_accion(link: $link))->elimina_con_filtro_and(filtro: $filtro);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al eliminar', data:  $del);
        }


        $inserta_campos = (new _instalacion(link: $link))->inserta_adm_campos(
            modelo_integracion: (new com_producto(link: $link)));
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar adm campos', data:  $inserta_campos);
        }

        $out->upds = $upds;
        $out->dels = $dels;
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

        $columnas->com_medio_prospeccion_id = new stdClass();
        $columnas->com_medio_prospeccion_id->tipo_dato = 'BIGINT';
        $columnas->com_medio_prospeccion_id->longitud = 100;


        $columnas->nombre = new stdClass();
        $columnas->apellido_paterno = new stdClass();

        $columnas->apellido_materno = new stdClass();
        $columnas->apellido_materno->not_null = false;

        $columnas->telefono = new stdClass();
        $columnas->correo = new stdClass();
        $columnas->razon_social = new stdClass();
        $columnas->rfc = new stdClass();
        $columnas->etapa= new stdClass();
        $columnas->etapa->tipo_dato= 'VARCHAR';
        $columnas->etapa->default= 'ALTA';

        $add_colums = $init->add_columns(campos: $columnas,table:  __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }
        $out->add_colums_entidad = $add_colums;


        $foraneas = array();
        $foraneas['com_agente_id'] = new stdClass();
        $foraneas['com_tipo_prospecto_id'] = new stdClass();
        $foraneas['com_medio_prospeccion_id'] = new stdClass();

        $result = $init->foraneas(foraneas: $foraneas,table:  __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }
        $out->foraneas = $result;

        $adm_menu_descripcion = 'Clientes';
        $adm_sistema_descripcion = 'comercial';
        $etiqueta_label = 'Prospecto';
        $adm_seccion_pertenece_descripcion = 'com_prospecto';
        $adm_namespace_descripcion = 'gamboa.martin/comercial';
        $adm_namespace_name = 'gamboamartin/comercial';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__, adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion, etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }

        $adm_seccion_id = (new adm_seccion(link: $link))->adm_seccion_id(descripcion: __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener seccion_id', data:  $adm_seccion_id);
        }

        $alta_accion = (new _adm())->inserta_accion_base(adm_accion_descripcion: 'convierte_en_cliente',
            adm_seccion_descripcion: __FUNCTION__, es_view: 'inactivo', icono: 'bi bi-file-earmark-plus-fill',
            link: $link, lista: 'activo', titulo: 'Convierte en cliente');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar accion',data:  $alta_accion);
        }
        $out->fc_relacion_alta_bd = $alta_accion;

        $alta_accion = (new _adm())->inserta_accion_base(adm_accion_descripcion: 'etapa',
            adm_seccion_descripcion: __FUNCTION__, es_view: 'activo', icono: 'bi bi-card-checklist',
            link: $link, lista: 'activo', titulo: 'Etapas',css: 'info');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar accion',data:  $alta_accion);
        }
        $out->fc_relacion_alta_bd = $alta_accion;

        $alta_accion = (new _adm())->inserta_accion_base(adm_accion_descripcion: 'etapa_bd',
            adm_seccion_descripcion: __FUNCTION__, es_view: 'inactivo', icono: 'bi bi-card-checklist',
            link: $link, lista: 'inactivo', titulo: 'Etapas',css: 'info');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar accion',data:  $alta_accion);
        }
        $out->fc_relacion_alta_bd = $alta_accion;


        $alta_accion = (new _adm())->inserta_accion_base(adm_accion_descripcion: 'alta_direccion',
            adm_seccion_descripcion: __FUNCTION__, es_view: 'inactivo', icono: 'bi bi-card-checklist',
            link: $link, lista: 'inactivo', titulo: 'Direcciones',css: 'info');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar accion',data:  $alta_accion);
        }
        $out->alta_direccion = $alta_accion;


        $modelo_pr_tipo_proceso = new pr_tipo_proceso(link: $link);
        $modelo_pr_etapa_proceso = new pr_etapa_proceso(link: $link);
        $modelo_pr_etapa = new pr_etapa(link: $link);
        $modelo_pr_proceso = new pr_proceso(link: $link);

        $inserta = (new _adm())->genera_pr_etapa_proceso(adm_accion_descripcion: 'alta_bd',
            adm_seccion_descripcion: __FUNCTION__, modelo_pr_etapa: $modelo_pr_etapa, modelo_pr_etapa_proceso: $modelo_pr_etapa_proceso,
            modelo_pr_proceso: $modelo_pr_proceso, modelo_pr_tipo_proceso: $modelo_pr_tipo_proceso,
            pr_etapa_codigo: 'ALTA', pr_proceso_codigo: 'PROSPECCION', pr_tipo_proceso_codigo: 'Control');
        if (errores::$error) {
            return (new errores())->error(mensaje: 'Error al insertar rows', data: $inserta);
        }

        $inserta = (new _adm())->genera_pr_etapa_proceso(adm_accion_descripcion: 'alta_bd',
            adm_seccion_descripcion: __FUNCTION__, modelo_pr_etapa: $modelo_pr_etapa, modelo_pr_etapa_proceso: $modelo_pr_etapa_proceso,
            modelo_pr_proceso: $modelo_pr_proceso, modelo_pr_tipo_proceso: $modelo_pr_tipo_proceso,
            pr_etapa_codigo: 'CONTACTADO', pr_proceso_codigo: 'PROSPECCION', pr_tipo_proceso_codigo: 'Control');
        if (errores::$error) {
            return (new errores())->error(mensaje: 'Error al insertar rows', data: $inserta);
        }

        $inserta = (new _adm())->genera_pr_etapa_proceso(adm_accion_descripcion: 'alta_bd',
            adm_seccion_descripcion: __FUNCTION__, modelo_pr_etapa: $modelo_pr_etapa, modelo_pr_etapa_proceso: $modelo_pr_etapa_proceso,
            modelo_pr_proceso: $modelo_pr_proceso, modelo_pr_tipo_proceso: $modelo_pr_tipo_proceso,
            pr_etapa_codigo: 'CLIENTE POTENCIAL', pr_proceso_codigo: 'PROSPECCION', pr_tipo_proceso_codigo: 'Control');
        if (errores::$error) {
            return (new errores())->error(mensaje: 'Error al insertar rows', data: $inserta);
        }

        $inserta = (new _adm())->genera_pr_etapa_proceso(adm_accion_descripcion: 'alta_bd',
            adm_seccion_descripcion: __FUNCTION__, modelo_pr_etapa: $modelo_pr_etapa, modelo_pr_etapa_proceso: $modelo_pr_etapa_proceso,
            modelo_pr_proceso: $modelo_pr_proceso, modelo_pr_tipo_proceso: $modelo_pr_tipo_proceso,
            pr_etapa_codigo: 'EN NEGOCIACION', pr_proceso_codigo: 'PROSPECCION', pr_tipo_proceso_codigo: 'Control');
        if (errores::$error) {
            return (new errores())->error(mensaje: 'Error al insertar rows', data: $inserta);
        }



        return $out;

    }
    private function com_direccion_prospecto(PDO $link): array|stdClass
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
        $columnas->com_direccion_id = new stdClass();
        $columnas->com_direccion_id->tipo_dato = 'BIGINT';
        $columnas->com_direccion_id->longitud = 100;

        $columnas->com_prospecto_id = new stdClass();
        $columnas->com_prospecto_id->tipo_dato = 'BIGINT';
        $columnas->com_prospecto_id->longitud = 100;

        $add_colums = $init->add_columns(campos: $columnas,table:  __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }
        $out->add_colums_entidad = $add_colums;

        $foraneas = array();
        $foraneas['com_direccion_id'] = new stdClass();
        $foraneas['com_prospecto_id'] = new stdClass();

        $result = $init->foraneas(foraneas: $foraneas,table:  __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }
        $out->foraneas = $result;

        $adm_menu_descripcion = 'Clientes';
        $adm_sistema_descripcion = 'comercial';
        $etiqueta_label = 'Direccion Prospecto';
        $adm_seccion_pertenece_descripcion = 'com_direccion_prospecto';
        $adm_namespace_descripcion = 'gamboa.martin/comercial';
        $adm_namespace_name = 'gamboamartin/comercial';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__, adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion, etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }

        return $out;
    }
    private function com_direccion_cliente(PDO $link): array|stdClass
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
        $columnas->com_direccion_id = new stdClass();
        $columnas->com_direccion_id->tipo_dato = 'BIGINT';
        $columnas->com_direccion_id->longitud = 100;

        $columnas->com_cliente_id = new stdClass();
        $columnas->com_cliente_id->tipo_dato = 'BIGINT';
        $columnas->com_cliente_id->longitud = 100;

        $add_colums = $init->add_columns(campos: $columnas,table:  __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }
        $out->add_colums_entidad = $add_colums;

        $foraneas = array();
        $foraneas['com_direccion_id'] = new stdClass();
        $foraneas['com_cliente_id'] = new stdClass();

        $result = $init->foraneas(foraneas: $foraneas,table:  __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }
        $out->foraneas = $result;

        $adm_menu_descripcion = 'Clientes';
        $adm_sistema_descripcion = 'comercial';
        $etiqueta_label = 'Direccion Cliente';
        $adm_seccion_pertenece_descripcion = 'com_direccion_cliente';
        $adm_namespace_descripcion = 'gamboa.martin/comercial';
        $adm_namespace_name = 'gamboamartin/comercial';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__, adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion, etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }

        $adm_seccion_id = (new adm_seccion(link: $link))->adm_seccion_id(descripcion: __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener seccion_id', data:  $adm_seccion_id);
        }

        $alta_accion = (new _adm())->inserta_accion_base(adm_accion_descripcion: 'convierte_en_cliente',
            adm_seccion_descripcion: __FUNCTION__, es_view: 'inactivo', icono: 'bi bi-file-earmark-plus-fill',
            link: $link, lista: 'activo', titulo: 'Convierte en cliente');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar accion',data:  $alta_accion);
        }
        $out->fc_relacion_alta_bd = $alta_accion;

        $alta_accion = (new _adm())->inserta_accion_base(adm_accion_descripcion: 'etapa',
            adm_seccion_descripcion: __FUNCTION__, es_view: 'activo', icono: 'bi bi-card-checklist',
            link: $link, lista: 'activo', titulo: 'Etapas',css: 'info');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar accion',data:  $alta_accion);
        }
        $out->fc_relacion_alta_bd = $alta_accion;

        $alta_accion = (new _adm())->inserta_accion_base(adm_accion_descripcion: 'etapa_bd',
            adm_seccion_descripcion: __FUNCTION__, es_view: 'inactivo', icono: 'bi bi-card-checklist',
            link: $link, lista: 'inactivo', titulo: 'Etapas',css: 'info');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar accion',data:  $alta_accion);
        }
        $out->fc_relacion_alta_bd = $alta_accion;

        return $out;
    }
    private function com_prospecto_etapa(PDO $link): array|stdClass
    {

        $out = new stdClass();

        $add = $this->_add_com_prospecto_etapa(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add);
        }

        $adm_menu_descripcion = 'Etapas';
        $adm_sistema_descripcion = 'comercial';
        $etiqueta_label = 'Etapas de prospecto';
        $adm_seccion_pertenece_descripcion = 'com_prospecto_etapa';
        $adm_namespace_descripcion = 'gamboa.martin/comercial';
        $adm_namespace_name = 'gamboamartin/comercial';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__, adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion, etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }



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
    private function com_rel_prospecto_cte(PDO $link): array|stdClass
    {

        $out = new stdClass();

        $add = $this->_add_com_rel_prospecto_cte(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add);
        }

        $adm_menu_descripcion = 'Relaciones';
        $adm_sistema_descripcion = 'comercial';
        $etiqueta_label = 'Relacion Cte Prospecto';
        $adm_seccion_pertenece_descripcion = 'com_rel_prospecto_cte';
        $adm_namespace_descripcion = 'gamboa.martin/comercial';
        $adm_namespace_name = 'gamboamartin/comercial';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__, adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion, etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }

        return $out;

    }
    private function com_sucursal(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $add = $this->_add_com_sucursal(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add);
        }



        $columnas = new stdClass();
        $columnas->pais = new stdClass();
        $columnas->estado = new stdClass();
        $columnas->municipio = new stdClass();
        $columnas->colonia = new stdClass();
        $columnas->calle = new stdClass();
        $columnas->cp = new stdClass();
        $columnas->nombre_contacto = new stdClass();
        $columnas->numero_exterior = new stdClass();
        $columnas->numero_interior = new stdClass();
        $columnas->telefono_1 = new stdClass();
        $columnas->telefono_1->valida_pep_8 = false;
        $columnas->telefono_2 = new stdClass();
        $columnas->telefono_2->valida_pep_8 = false;
        $columnas->telefono_3 = new stdClass();
        $columnas->telefono_3->valida_pep_8 = false;

        $add_colums = $init->add_columns(campos: $columnas,table:  __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }


        $com_sucursal_modelo = new com_sucursal(link: $link);
        $com_sucursal_modelo->transaccion_desde_cliente = true;
        $com_sucursales = $com_sucursal_modelo->registros();
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener com_sucursales', data:  $com_sucursales);
        }
        $keys_dom = array('pais','estado','municipio','colonia', 'calle','cp');
        $upds_dom = array();
        foreach ($com_sucursales as $com_sucursal){

            foreach ($keys_dom AS $key_dom) {
                $key_entidad = __FUNCTION__ . "_$key_dom";

                $com_sucursal_bruto = $com_sucursal_modelo->registro(registro_id: $com_sucursal['com_sucursal_id'],
                    columnas_en_bruto: true);
                if (errores::$error) {
                    return (new errores())->error(mensaje: 'Error al obtener el com_sucursal', data: $com_sucursal_bruto);
                }

                if (!isset($com_sucursal[$key_entidad])) {
                    return (new errores())->error(mensaje: 'Error no existe key ' . $key_entidad, data: $com_sucursal);
                }

            }

        }

        $out->upds_dom = $upds_dom;

        $adm_menu_descripcion = 'Clientes';
        $adm_sistema_descripcion = 'comercial';
        $etiqueta_label = 'Sucursales';
        $adm_seccion_pertenece_descripcion = 'comercial';
        $adm_namespace_descripcion = 'gamboa.martin/comercial';
        $adm_namespace_name = 'gamboamartin/comercial';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__, adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion, etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }


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
        //$com_tipo_producto_modelo = new com_tipo_producto(link: $link);

        $out = new stdClass();


        //$campos = new stdClass();
        $create_table = $init->create_table_new(table: __FUNCTION__);
        if (errores::$error) {
            return (new errores())->error(mensaje: 'Error al crear table '.__FUNCTION__, data: $create_table);
        }
        $out->create_table = $create_table;



        return $out;

    }
    private function com_tipo_cambio(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $create = $this->_add_com_tipo_cambio(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->campos = $create;

        return $out;

    }
    private function com_tipo_contacto(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $create = $this->_add_com_tipo_contacto(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->campos = $create;


        $adm_menu_descripcion = 'Clientes';
        $adm_sistema_descripcion = 'comercial';
        $etiqueta_label = 'Tipos de contacto';
        $adm_seccion_pertenece_descripcion = 'comercial';
        $adm_namespace_descripcion = 'gamboa.martin/comercial';
        $adm_namespace_name = 'gamboamartin/comercial';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__, adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion, etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error) {
            return (new errores())->error(mensaje: 'Error al obtener acl', data: $acl);
        }

        return $out;

    }
    private function com_conf_precio(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $create = $this->_add_com_conf_precio(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->campos = $create;

        return $out;

    }
    private function com_medio_prospeccion(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $create = $this->_add_com_medio_prospeccion(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->campos = $create;

        $init = new _instalacion(link: $link);
        $columnas = new stdClass();
        $columnas->es_red_social = new stdClass();
        $columnas->es_red_social->tipo_dato = 'VARCHAR';
        $columnas->es_red_social->default = 'inactivo';

        $add_colums = $init->add_columns(campos: $columnas,table:  __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }

        $adm_menu_descripcion = 'Clientes';
        $adm_sistema_descripcion = 'comercial';
        $etiqueta_label = 'Medio Prospeccion';
        $adm_seccion_pertenece_descripcion = 'com_medio_prospeccion';
        $adm_namespace_name = 'gamboamartin/comercial';
        $adm_namespace_descripcion = 'gamboa.martin/comercial';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__,
            adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion,
            etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }



        $com_medio_prospeccion_ins['id'] = 100;
        $com_medio_prospeccion_ins['descripcion'] = 'PREDETERMINADO';
        $com_medio_prospeccion_ins['status'] = 'activo';
        $com_medio_prospeccion_ins['es_red_social'] = 'inactivo';
        $com_medio_prospeccion_ins['predeterminado'] = 'activo';

        $r_com_medio_prospeccion = (new com_medio_prospeccion(link: $link))->inserta_registro_si_no_existe(
            registro: $com_medio_prospeccion_ins);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar medio_prospeccion', data:  $r_com_medio_prospeccion);
        }

        return $out;

    }
    private function com_tipo_cliente(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $create = $this->_add_com_tipo_cliente(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->campos = $create;

        $adm_menu_descripcion = 'Clientes';
        $adm_sistema_descripcion = 'comercial';
        $etiqueta_label = 'Tipos de Cliente';
        $adm_seccion_pertenece_descripcion = 'com_tipo_cliente';
        $adm_namespace_name = 'gamboamartin/comercial';
        $adm_namespace_descripcion = 'gamboa.martin/comercial';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__,
            adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion,
            etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }

        $inserta_campos = (new _instalacion(link: $link))->inserta_adm_campos(
            modelo_integracion: (new com_tipo_cliente(link: $link)));
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar adm campos', data:  $inserta_campos);
        }

        return $out;

    }
    private function com_tipo_direccion(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $create = $this->_add_com_tipo_direccion(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->campos = $create;

        $adm_menu_descripcion = 'Clientes';
        $adm_sistema_descripcion = 'comercial';
        $etiqueta_label = 'Tipos de Direcciones';
        $adm_seccion_pertenece_descripcion = 'com_tipo_direccion';
        $adm_namespace_name = 'gamboamartin/comercial';
        $adm_namespace_descripcion = 'gamboa.martin/comercial';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__,
            adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion,
            etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }


        return $out;

    }
    private function com_direccion(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $create = $this->_add_com_direccion(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->campos = $create;

        $adm_menu_descripcion = 'Clientes';
        $adm_sistema_descripcion = 'comercial';
        $etiqueta_label = 'Direcciones';
        $adm_seccion_pertenece_descripcion = 'com_direccion';
        $adm_namespace_name = 'gamboamartin/comercial';
        $adm_namespace_descripcion = 'gamboa.martin/comercial';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__,
            adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion,
            etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }

        $alta_accion = (new _adm())->inserta_accion_base(adm_accion_descripcion: 'data_ajax',
            adm_seccion_descripcion: __FUNCTION__, es_view: 'inactivo', icono: 'bi bi-file-earmark-plus-fill',
            link: $link, lista: 'inactivo', titulo: 'data_ajax');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar accion',data:  $alta_accion);
        }
        $out->fc_relacion_alta_bd = $alta_accion;


        return $out;

    }
    private function com_tipo_producto(PDO $link): array|stdClass
    {
        $init = (new _instalacion(link: $link));

        $out = new stdClass();

        $create_table = $init->create_table_new(table: __FUNCTION__);
        if (errores::$error) {
            return (new errores())->error(mensaje: 'Error al crear table '.__FUNCTION__, data: $create_table);
        }
        $out->create_table = $create_table;


        $create = $this->_add_com_producto(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->_add_com_producto = $create;

        $com_tipo_producto_modelo = new com_tipo_producto(link: $link);

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

        $adm_menu_descripcion = 'Tipos de Productos';
        $adm_sistema_descripcion = 'comercial';
        $etiqueta_label = 'Tipo de Productos';
        $adm_seccion_pertenece_descripcion = 'comercial';
        $adm_namespace_name = 'gamboamartin/comercial';
        $adm_namespace_descripcion = 'gamboa.martin/comercial';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__,
            adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion,
            etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }

        $filtro['adm_accion.descripcion'] = 'es_automatico';
        $filtro['adm_seccion.descripcion'] = __FUNCTION__;

        $del = (new adm_accion(link: $link))->elimina_con_filtro_and(filtro: $filtro);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al eliminar', data:  $del);
        }


        $inserta_campos = (new _instalacion(link: $link))->inserta_adm_campos(
            modelo_integracion: (new com_tipo_producto(link: $link)));
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar adm campos', data:  $inserta_campos);
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
    private function com_tmp_prod_cs(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $create = $this->_add_com_tmp_prod_cs(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->campos = $create;

        return $out;

    }
    private function com_tipo_sucursal(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $create = $this->_add_com_cliente(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $create = $this->_add_com_tipo_sucursal(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $create = $this->_add_com_sucursal(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $modelo = new com_tipo_sucursal(link: $link);


        $existe = $modelo->existe_by_codigo(codigo: 'MAT');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error validar si existe', data:  $existe);
        }
        if($existe){
            $com_tipo_sucursal_id = $modelo->get_id_by_codigo(codigo: 'MAT');
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al obtener id', data:  $com_tipo_sucursal_id);
            }

            if($com_tipo_sucursal_id !== 1) {
                $udp_data['codigo'] = 'MATANT';
                $upd = $modelo->modifica_bd(registro: $udp_data, id: $com_tipo_sucursal_id);
                if (errores::$error) {
                    return (new errores())->error(mensaje: 'Error al actualizar', data: $upd);
                }
            }
        }


        $rows = array();
        $ins['id'] = '1';
        $ins['descripcion'] = 'MATRIZ';
        $ins['codigo'] = 'MAT';
        $ins['predeterminado'] = 'activo';

        $rows[] = $ins;

        $ins['id'] = '2';
        $ins['descripcion'] = 'SUCURSAL';
        $ins['codigo'] = 'SUC';
        $ins['predeterminado'] = 'inactivo';

        $rows[] = $ins;

        foreach ($rows as $ins){

            $existe = $modelo->existe_by_id($ins['id']);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al validar si existe row', data:  $existe);
            }
            if(!$existe) {
                $alta = $modelo->alta_registro(registro: $ins);
                if (errores::$error) {
                    return (new errores())->error(mensaje: 'Error al insertar registro', data: $alta);
                }
                $out->com_tipo_producto[] = $alta;
            }

        }

        $out->campos = $create;

        return $out;

    }
    final public function instala(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $com_tipo_direccion = $this->com_tipo_direccion(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_tipo_direccion', data:  $com_tipo_direccion);
        }
        $out->com_tipo_direccion = $com_tipo_direccion;

        $com_direccion = $this->com_direccion(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_direccion', data:  $com_direccion);
        }
        $out->com_direccion = $com_direccion;

        $com_medio_prospeccion = $this->com_medio_prospeccion(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_medio_prospeccion', data:  $com_medio_prospeccion);
        }
        $out->com_medio_prospeccion = $com_medio_prospeccion;

        $com_tipo_cliente = $this->com_tipo_cliente(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_tipo_cliente', data:  $com_tipo_cliente);
        }
        $out->com_tipo_cliente = $com_tipo_cliente;

        $com_tipo_producto = $this->com_tipo_producto(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_tipo_producto', data:  $com_tipo_producto);
        }

        $com_tipo_sucursal = $this->com_tipo_sucursal(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_tipo_sucursal', data:  $com_tipo_sucursal);
        }
        $com_tipo_agente = $this->com_tipo_agente(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_tipo_agente', data:  $com_tipo_agente);
        }
        $com_tipo_tel = $this->com_tipo_tel(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_tipo_tel', data:  $com_tipo_tel);
        }
        
        $com_datos_sistema = $this->com_datos_sistema(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_datos_sistema', data:  $com_datos_sistema);
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


        $com_direccion_prospecto = $this->com_direccion_prospecto(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_direccion_prospecto', data:  $com_direccion_prospecto);
        }
        $out->com_direccion_prospecto = $com_direccion_prospecto;

        $com_tels_agente = $this->com_tels_agente(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_tels_agente', data:  $com_tels_agente);
        }

        $com_rel_agente = $this->com_rel_agente(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_rel_agente', data:  $com_rel_agente);
        }
        $out->com_agente = $com_agente;

        $com_tmp_prod_cs = $this->com_tmp_prod_cs(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_tmp_prod_cs', data:  $com_tmp_prod_cs);
        }
        $out->com_tmp_prod_cs = $com_tmp_prod_cs;

        $com_tipo_cambio = $this->com_tipo_cambio(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_tipo_cambio', data:  $com_tipo_cambio);
        }
        $out->com_tipo_cambio = $com_tipo_cambio;

        $com_conf_precio = $this->com_conf_precio(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_conf_precio', data:  $com_conf_precio);
        }
        $out->com_conf_precio = $com_conf_precio;

        $com_rel_prospecto_cte = $this->com_rel_prospecto_cte(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_rel_prospecto_cte', data:  $com_rel_prospecto_cte);
        }
        $out->com_rel_prospecto_cte = $com_rel_prospecto_cte;

        $com_prospecto_etapa = $this->com_prospecto_etapa(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_prospecto_etapa', data:  $com_prospecto_etapa);
        }
        $out->com_prospecto_etapa = $com_prospecto_etapa;

        $com_tipo_contacto = $this->com_tipo_contacto(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_tipo_contacto', data:  $com_tipo_contacto);
        }
        $out->com_tipo_contacto = $com_tipo_contacto;

        $com_email_cte = $this->com_email_cte(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_email_cte', data:  $com_email_cte);
        }
        $out->com_email_cte = $com_email_cte;

        $com_contacto = $this->com_contacto(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_contacto', data:  $com_contacto);
        }
        $out->com_contacto = $com_contacto;

        $com_contrato_user = $this->com_contacto_user(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_contrato_user', data:  $com_contrato_user);
        }
        $out->com_contrato_user = $com_contrato_user;

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
