<?php
namespace gamboamartin\comercial\test;
use base\orm\modelo_base;
use gamboamartin\cat_sat\models\cat_sat_forma_pago;
use gamboamartin\cat_sat\models\cat_sat_metodo_pago;
use gamboamartin\cat_sat\models\cat_sat_moneda;
use gamboamartin\cat_sat\models\cat_sat_regimen_fiscal;
use gamboamartin\cat_sat\models\cat_sat_tipo_de_comprobante;
use gamboamartin\cat_sat\models\cat_sat_uso_cfdi;
use gamboamartin\comercial\models\com_cliente;
use gamboamartin\comercial\models\com_producto;
use gamboamartin\comercial\models\com_sucursal;
use gamboamartin\comercial\models\com_tipo_cambio;
use gamboamartin\comercial\models\com_tipo_cliente;
use gamboamartin\comercial\models\com_tipo_producto;
use gamboamartin\comercial\models\com_tipo_sucursal;
use gamboamartin\direccion_postal\models\dp_calle_pertenece;
use gamboamartin\errores\errores;
use PDO;

class base_test{

    public function alta_cat_sat_forma_pago(PDO $link): array|\stdClass
    {

        $alta = (new \gamboamartin\cat_sat\tests\base_test())->alta_cat_sat_forma_pago(link: $link,codigo: '01',
            descripcion: 'Efectivo');
        if(errores::$error){
            return (new errores())->error('Error al insertar', $alta);
        }

        return $alta;
    }
    public function alta_cat_sat_metodo_pago(PDO $link, int $id = 1): array|\stdClass
    {

        $alta = (new \gamboamartin\cat_sat\tests\base_test())->alta_cat_sat_metodo_pago(link: $link, codigo: 'PUE',
            descripcion: 'Pago en una sola exhibición', id: $id);
        if(errores::$error){
            return (new errores())->error('Error al insertar', $alta);
        }

        return $alta;
    }

    public function alta_cat_sat_moneda(PDO $link): array|\stdClass
    {

        $alta = (new \gamboamartin\cat_sat\tests\base_test())->alta_cat_sat_moneda($link);
        if(errores::$error){
            return (new errores())->error('Error al insertar', $alta);
        }

        return $alta;
    }

    public function alta_cat_sat_regimen_fiscal(PDO $link): array|\stdClass
    {

        $alta = (new \gamboamartin\cat_sat\tests\base_test())->alta_cat_sat_regimen_fiscal($link);
        if(errores::$error){
            return (new errores())->error('Error al insertar', $alta);
        }

        return $alta;
    }

    public function alta_cat_sat_tipo_de_comprobante(PDO $link): array|\stdClass
    {

        $alta = (new \gamboamartin\cat_sat\tests\base_test())->alta_cat_sat_tipo_de_comprobante($link);
        if(errores::$error){
            return (new errores())->error('Error al insertar', $alta);
        }

        return $alta;
    }

    public function alta_cat_sat_uso_cfdi(PDO $link): array|\stdClass
    {

        $alta = (new \gamboamartin\cat_sat\tests\base_test())->alta_cat_sat_uso_cfdi($link);
        if(errores::$error){
            return (new errores())->error('Error al insertar', $alta);
        }

        return $alta;
    }

    public function alta_com_cliente(PDO $link, int $cat_sat_forma_pago_id = 1, int $cat_sat_metodo_pago_id = 2,
                                     int $cat_sat_moneda_id = 1, int $cat_sat_regimen_fiscal_id = 1,
                                     int $cat_sat_tipo_de_comprobante_id = 1, int $cat_sat_uso_cfdi_id = 1,
                                     int $com_tipo_cliente_id = 1, int $dp_calle_pertenece_id = 1,
                                     int $id = 1): array|\stdClass
    {

        $existe = (new cat_sat_moneda($link))->existe_by_id(registro_id: $cat_sat_moneda_id);
        if(errores::$error){
            return (new errores())->error('Error al verificar si existe', $existe);
        }

        if(!$existe) {
            $alta = (new base_test())->alta_cat_sat_moneda($link);
            if(errores::$error){
                return (new errores())->error('Error al insertar', $alta);
            }
        }

        $existe = (new cat_sat_tipo_de_comprobante($link))->existe_by_id(registro_id: $cat_sat_tipo_de_comprobante_id);
        if(errores::$error){
            return (new errores())->error('Error al verificar si existe', $existe);
        }

        if(!$existe) {
            $alta = (new base_test())->alta_cat_sat_tipo_de_comprobante($link);
            if(errores::$error){
                return (new errores())->error('Error al insertar', $alta);
            }
        }

        $existe = (new cat_sat_metodo_pago($link))->existe_by_id(registro_id: $cat_sat_metodo_pago_id);
        if(errores::$error){
            return (new errores())->error('Error al verificar si existe', $existe);
        }

        if(!$existe) {
            $alta = (new base_test())->alta_cat_sat_metodo_pago(link: $link, id: $cat_sat_metodo_pago_id);
            if (errores::$error) {
                return (new errores())->error('Error al insertar', $alta);
            }
        }

        $existe = (new com_tipo_cliente($link))->existe_by_id(registro_id: $com_tipo_cliente_id);
        if(errores::$error){
            return (new errores())->error('Error al verificar si existe', $existe);
        }

        if(!$existe) {
            $alta = (new base_test())->alta_com_tipo_cliente($link);
            if (errores::$error) {
                return (new errores())->error('Error al insertar', $alta);
            }
        }

        $existe = (new cat_sat_uso_cfdi($link))->existe_by_id(registro_id: $cat_sat_uso_cfdi_id);
        if(errores::$error){
            return (new errores())->error('Error al verificar si existe', $existe);
        }

        if(!$existe) {
            $alta = (new base_test())->alta_cat_sat_uso_cfdi($link);
            if (errores::$error) {
                return (new errores())->error('Error al insertar', $alta);
            }
        }

        $existe = (new cat_sat_regimen_fiscal($link))->existe_by_id(registro_id: $cat_sat_regimen_fiscal_id);
        if(errores::$error){
            return (new errores())->error('Error al verificar si existe', $existe);
        }

        if(!$existe) {
            $alta = (new base_test())->alta_cat_sat_regimen_fiscal($link);
            if (errores::$error) {
                return (new errores())->error('Error al insertar', $alta);
            }
        }

        $existe = (new cat_sat_forma_pago($link))->existe_by_id(registro_id: $cat_sat_forma_pago_id);
        if(errores::$error){
            return (new errores())->error('Error al verificar si existe', $existe);
        }

        if(!$existe) {
            $alta = (new base_test())->alta_cat_sat_forma_pago($link);
            if (errores::$error) {
                return (new errores())->error('Error al insertar', $alta);
            }
        }


        $registro['dp_calle_pertenece_id'] = $dp_calle_pertenece_id;
        $registro['cat_sat_moneda_id'] = $cat_sat_moneda_id;
        $registro['cat_sat_metodo_pago_id'] = $cat_sat_metodo_pago_id;
        $registro['cat_sat_tipo_de_comprobante_id'] = $cat_sat_tipo_de_comprobante_id;
        $registro['com_tipo_cliente_id'] = $com_tipo_cliente_id;
        $registro['cat_sat_regimen_fiscal_id'] = $cat_sat_regimen_fiscal_id;
        $registro['cat_sat_uso_cfdi_id'] = $cat_sat_uso_cfdi_id;
        $registro['cat_sat_forma_pago_id'] = $cat_sat_forma_pago_id;
        $registro['id'] = $id;
        $registro['codigo'] = 1;
        $registro['descripcion'] = 'YADIRA MAGALY MONTAÑEZ FELIX';
        $registro['razon_social'] = 'YADIRA MAGALY MONTAÑEZ FELIX';
        $registro['rfc'] = 'MOFY900516NL1';
        $registro['telefono'] = '3333333333';
        $registro['numero_exterior'] = '3333333333';

        $alta = (new com_cliente($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error('Error al dar de alta ', $alta);

        }
        return $alta;
    }

    public function alta_com_producto(PDO $link, int $cat_sat_obj_imp_id = 1, int $cat_sat_producto_id = 1,
                                      int $cat_sat_unidad_id = 1, int $com_tipo_producto_id = 1,
                                      int $id = 1): array|\stdClass
    {

        $existe = (new com_tipo_producto($link))->existe_by_id(registro_id: $com_tipo_producto_id);
        if(errores::$error){
            return (new errores())->error('Error al verificar si existe', $existe);
        }

        if(!$existe) {
            $alta = (new base_test())->alta_com_tipo_producto($link);
            if (errores::$error) {
                return (new errores())->error('Error al insertar', $alta);
            }
        }

        $registro = array();
        $registro['id'] = $id;
        $registro['codigo'] = 1;
        $registro['descripcion'] = 1;
        $registro['cat_sat_producto_id'] = $cat_sat_producto_id;
        $registro['cat_sat_unidad_id'] = $cat_sat_unidad_id;
        $registro['cat_sat_obj_imp_id'] = $cat_sat_obj_imp_id;
        $registro['com_tipo_producto_id'] = $com_tipo_producto_id;


        $alta = (new com_producto($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error('Error al dar de alta ', $alta);

        }
        return $alta;
    }


    public function alta_com_sucursal(PDO $link, int $com_cliente_id = 1, int $com_tipo_sucursal_id = 1, int $id = 1): array|\stdClass
    {

        $existe = (new com_cliente($link))->existe_by_id(registro_id: $com_cliente_id);
        if(errores::$error){
            return (new errores())->error('Error al verificar si existe', $existe);
        }

        if(!$existe) {
            $alta = (new base_test())->alta_com_cliente(link: $link, id: $com_cliente_id);
            if(errores::$error){
                return (new errores())->error('Error al insertar', $alta);
            }
        }

        $existe = (new com_tipo_sucursal($link))->existe_by_id(registro_id: $com_tipo_sucursal_id);
        if(errores::$error){
            return (new errores())->error('Error al verificar si existe', $existe);
        }

        if(!$existe) {
            $alta = (new base_test())->alta_com_tipo_sucursal(link: $link, id: $com_tipo_sucursal_id);
            if(errores::$error){
                return (new errores())->error('Error al insertar', $alta);
            }
        }

        $del = $this->del_com_sucursal($link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }


        $registro = array();
        $registro['id'] = $id;
        $registro['codigo'] = 1;
        $registro['descripcion'] = 1;
        $registro['com_cliente_id'] = $com_cliente_id;
        $registro['com_tipo_sucursal_id'] = $com_tipo_sucursal_id;
        $registro['dp_calle_pertenece_id'] = 1;
        $registro['numero_exterior'] = 1;

        $alta = (new com_sucursal($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error('Error al dar de alta ', $alta);

        }
        return $alta;
    }

    public function alta_com_tipo_cambio(PDO $link, int $id = 1): array|\stdClass
    {

        $registro = array();
        $registro['id'] = $id;
        $registro['codigo'] = 1;
        $registro['descripcion'] = 1;
        $registro['cat_sat_moneda_id'] = 1;
        $registro['monto'] = 1;
        $registro['fecha'] = '2020-01-01';


        $alta = (new com_tipo_cambio($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error('Error al dar de alta ', $alta);

        }
        return $alta;
    }

    public function alta_com_tipo_cliente(PDO $link, int $id = 1): array|\stdClass
    {

        $registro = array();
        $registro['id'] = $id;
        $registro['codigo'] = 1;
        $registro['descripcion'] = 1;


        $alta = (new com_tipo_cliente($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error('Error al dar de alta ', $alta);

        }
        return $alta;
    }

    public function alta_com_tipo_producto(PDO $link, int $id = 1): array|\stdClass
    {

        $registro = array();
        $registro['id'] = $id;
        $registro['codigo'] = 1;
        $registro['descripcion'] = 1;


        $alta = (new com_tipo_producto($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error('Error al dar de alta ', $alta);

        }
        return $alta;
    }

    public function alta_com_tipo_sucursal(PDO $link, int $id = 1): array|\stdClass
    {

        $registro = array();
        $registro['id'] = $id;
        $registro['codigo'] = 1;
        $registro['descripcion'] = 1;


        $alta = (new com_tipo_sucursal($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error('Error al dar de alta ', $alta);

        }
        return $alta;
    }



    public function del(PDO $link, string $name_model): array
    {
        $model = (new modelo_base($link))->genera_modelo(modelo: $name_model);
        $del = $model->elimina_todo();
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al eliminar '.$name_model, data: $del);
        }
        return $del;
    }

    public function elimina_registro(PDO $link, string $name_model, int $id): array
    {
        $model = (new modelo_base($link))->genera_modelo(modelo: $name_model);
        $del = $model->elimina_bd(id: $id);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al eliminar '.$name_model, data: $del);
        }
        return $del;
    }


    public function del_cat_sat_metodo_pago(PDO $link): array
    {


        $del =(new \gamboamartin\cat_sat\tests\base_test())->del_cat_sat_metodo_pago($link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }


    public function del_cat_sat_moneda(PDO $link): array
    {

        $del = (new base_test())->del_com_tipo_cambio($link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar', $del);
            print_r($error);
            exit;
        }

        $del = (new base_test())->del_com_cliente($link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar', $del);
            print_r($error);
            exit;
        }

        $del =(new \gamboamartin\cat_sat\tests\base_test())->del_cat_sat_moneda($link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_com_cliente(PDO $link): array
    {

        $del = $this->del_com_sucursal($link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }

        $del = $this->del($link, 'gamboamartin\\comercial\\models\\com_cliente');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_com_producto(PDO $link): array
    {

        $del = $this->del($link, 'gamboamartin\\comercial\\models\\com_producto');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_com_producto_id(PDO $link, int $id): array
    {
        $del = $this->elimina_registro($link, 'gamboamartin\\comercial\\models\\com_producto',id: $id);
        if(errores::$error){
            return (new errores())->error('Error al eliminar producto', $del);
        }

        return $del;
    }


    public function del_com_sucursal(PDO $link): array
    {

        $del = $this->del($link, 'gamboamartin\\comercial\\models\\com_sucursal');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_com_tipo_cambio(PDO $link): array
    {

        $del = $this->del($link, 'gamboamartin\\comercial\\models\\com_tipo_cambio');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_com_tipo_producto(PDO $link): array
    {

        $del = $this->del_com_producto($link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }

        $del = $this->del($link, 'gamboamartin\\comercial\\models\\com_tipo_producto');
        if(errores::$error){
            return (new errores())->error('Error al eliminar tipo producto', $del);
        }
        return $del;
    }



}
