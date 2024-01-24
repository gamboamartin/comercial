<?php
namespace gamboamartin\comercial\instalacion;
use gamboamartin\administrador\models\_instalacion;
use gamboamartin\comercial\models\com_cliente;
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

    private function com_producto(PDO $link)
    {
        $init = (new _instalacion(link: $link));
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
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }
        return $result;

    }
    final public function instala(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $com_cliente = $this->com_cliente(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_cliente', data:  $com_cliente);
        }
        $out->com_cliente = $com_cliente;

        $com_producto = $this->com_producto(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_cliente', data:  $com_cliente);
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
