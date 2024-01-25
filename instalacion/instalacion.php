<?php
namespace gamboamartin\comercial\instalacion;
use gamboamartin\administrador\models\_instalacion;
use gamboamartin\comercial\models\com_cliente;
use gamboamartin\comercial\models\com_producto;
use gamboamartin\comercial\models\com_tipo_producto;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class instalacion
{

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

        $com_cliente_modelo = new com_cliente(link: $link);

        $atributos = $com_cliente_modelo->atributos;


        foreach ($atributos as $campo_name=>$atributo){
            foreach ($foraneas as $campo_validar=>$atributo_validar){

                if($campo_validar === $campo_name){

                    if(isset($atributo_validar->default)){
                        $value_default = $atributo_validar->default;

                        $com_clientes = $com_cliente_modelo->registros(columnas_en_bruto: true,return_obj: true);
                        if(errores::$error){
                            return (new errores())->error(mensaje: 'Error al obtener clientes', data:  $com_clientes);
                        }
                        foreach ($com_clientes as $com_cliente){

                            if(isset($com_cliente->$campo_validar)){
                                $identificador_validar = (int)trim($com_cliente->$campo_validar);
                                if($identificador_validar === 0){

                                    $com_cliente_upd[$campo_validar] = $value_default;
                                    $upd = $com_cliente_modelo->modifica_bd(registro: $com_cliente_upd,id:  $com_cliente->id);
                                    if(errores::$error){
                                        return (new errores())->error(mensaje: 'Error al actualizar cliente', data:  $upd);
                                    }
                                    $out->upds[] = $upd;
                                }

                            }

                        }

                    }

                }

            }

        }

        $result = $init->foraneas(foraneas: $foraneas,table:  'com_cliente');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }
        $out->foraneas = $result;

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
        $com_producto_ins['codigo'] = '84111506';
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
    final public function instala(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $com_tipo_producto = $this->com_tipo_producto(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_tipo_producto', data:  $com_tipo_producto);
        }


        $com_cliente = $this->com_cliente(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_cliente', data:  $com_cliente);
        }
        $out->com_cliente = $com_cliente;

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

        return $out;

    }

}
