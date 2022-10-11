<?php
namespace gamboamartin\comercial\test;
use base\orm\modelo_base;
use gamboamartin\cat_sat\models\cat_sat_metodo_pago;
use gamboamartin\cat_sat\models\cat_sat_moneda;
use gamboamartin\comercial\models\com_cliente;
use gamboamartin\comercial\models\com_sucursal;
use gamboamartin\direccion_postal\models\dp_calle_pertenece;
use gamboamartin\errores\errores;
use PDO;

class base_test{

    public function alta_com_cliente(PDO $link, int $cat_sat_metodo_pago_id = -1, int $cat_sat_moneda_id = -1,
                                     int $dp_calle_pertenece_id = -1, bool $predeterminado = false): array|\stdClass
    {
        $registro = array();

        if($dp_calle_pertenece_id === -1) {



            $existe = (new dp_calle_pertenece($link))->existe_predeterminado();
            if (errores::$error) {
                return (new errores())->error('Error al validar si existe', $existe);

            }

            if(!$existe) {
                $alta = (new \gamboamartin\direccion_postal\tests\base_test())->alta_dp_calle_pertenece(link: $link, predeterminado: true);
                if (errores::$error) {
                    return (new errores())->error('Error al dar de alta', $alta);

                }
            }

        }
        if($dp_calle_pertenece_id > 0){

            $registro['dp_calle_pertenece_id'] = $dp_calle_pertenece_id;

            $existe = (new dp_calle_pertenece($link))->existe_by_id(registro_id: $dp_calle_pertenece_id);
            if (errores::$error) {
                return (new errores())->error('Error al validar si existe', $existe);
            }


            if(!$existe) {
                $alta = (new \gamboamartin\direccion_postal\tests\base_test())->alta_dp_calle_pertenece(link: $link, id: $dp_calle_pertenece_id);
                if (errores::$error) {
                    return (new errores())->error('Error al dar de alta', $alta);
                }
            }

        }

        if($cat_sat_moneda_id === -1) {



            $existe = (new cat_sat_moneda($link))->existe_predeterminado();
            if (errores::$error) {
                return (new errores())->error('Error al validar si existe', $existe);

            }
            if(!$existe) {

                $alta = (new \gamboamartin\cat_sat\tests\base_test())->alta_cat_sat_moneda(link: $link, predeterminado: true);
                if (errores::$error) {
                    return (new errores())->error('Error al dar de alta', $alta);

                }
            }

        }
        if($cat_sat_moneda_id > 0){

            $registro['cat_sat_moneda_id'] = $cat_sat_moneda_id;

            $existe = (new cat_sat_moneda($link))->existe_by_id(registro_id: $cat_sat_moneda_id);
            if (errores::$error) {
                return (new errores())->error('Error al validar si existe', $existe);
            }

            if(!$existe) {
                $alta = (new \gamboamartin\cat_sat\tests\base_test())->alta_cat_sat_moneda(link: $link, id: $cat_sat_moneda_id);
                if (errores::$error) {
                    return (new errores())->error('Error al dar de alta', $alta);
                }

            }

        }

        if($cat_sat_metodo_pago_id === -1) {



            $existe = (new cat_sat_metodo_pago($link))->existe_predeterminado();
            if (errores::$error) {
                return (new errores())->error('Error al validar si existe', $existe);

            }

            if(!$existe) {
                $alta = (new \gamboamartin\cat_sat\tests\base_test())->alta_cat_sat_metodo_pago(link: $link, predeterminado: true);
                if (errores::$error) {
                    return (new errores())->error('Error al dar de alta', $alta);

                }
            }

        }
        if($cat_sat_metodo_pago_id > 0){

            $registro['cat_sat_metodo_pago_id'] = $cat_sat_metodo_pago_id;

            $existe = (new cat_sat_metodo_pago($link))->existe_by_id(registro_id: $cat_sat_metodo_pago_id);
            if (errores::$error) {
                return (new errores())->error('Error al validar si existe', $existe);
            }


            if(!$existe) {
                $alta = (new \gamboamartin\cat_sat\tests\base_test())->alta_cat_sat_metodo_pago(link: $link, id: $cat_sat_metodo_pago_id);
                if (errores::$error) {
                    return (new errores())->error('Error al dar de alta', $alta);
                }
            }

        }



        $registro['id'] = 1;
        $registro['codigo'] = 1;
        $registro['descripcion'] = 1;
        $registro['razon_social'] = 1;
        $registro['rfc'] = 'AAA010101ABC';

        $alta = (new com_cliente($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error('Error al dar de alta ', $alta);

        }
        return $alta;
    }


    public function alta_com_sucursal(PDO $link): array|\stdClass
    {

        $registro = array();
        $registro['id'] = 1;
        $registro['codigo'] = 1;
        $registro['descripcion'] = 1;
        $registro['com_cliente_id'] = 1;
        $registro['dp_calle_pertenece_id'] = 1;
        $registro['numero_exterior'] = 1;

        $alta = (new com_sucursal($link))->alta_registro($registro);
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



}
