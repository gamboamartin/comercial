<?php
namespace gamboamartin\comercial\instalacion;
use gamboamartin\administrador\models\_instalacion;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class instalacion
{
    final public function instala(PDO $link)
    {
        $init = (new _instalacion(link: $link));
        $foraneas = array();
        $foraneas[] = 'dp_calle_pertenece_id';
        $foraneas[] = 'cat_sat_regimen_fiscal_id';
        $foraneas[] = 'cat_sat_moneda_id';
        $foraneas[] = 'cat_sat_forma_pago_id';
        $foraneas[] = 'cat_sat_metodo_pago_id';
        $foraneas[] = 'cat_sat_uso_cfdi_id';
        $foraneas[] = 'cat_sat_tipo_de_comprobante_id';
        $foraneas[] = 'com_tipo_cliente_id';
        $foraneas[] = 'cat_sat_tipo_persona_id';

        $result = $init->foraneas(foraneas: $foraneas,table:  'com_cliente');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }


        $init = (new _instalacion(link: $link));
        $foraneas = array();
        $foraneas[] = 'cat_sat_producto_id';
        $foraneas[] = 'cat_sat_unidad_id';
        $foraneas[] = 'cat_sat_obj_imp_id';
        $foraneas[] = 'com_tipo_producto_id';
        $foraneas[] = 'cat_sat_conf_imps_id';

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

}