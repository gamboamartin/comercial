<?php
namespace gamboamartin\comercial\test;
use base\orm\modelo_base;
use gamboamartin\cat_sat\models\cat_sat_conf_imps;
use gamboamartin\cat_sat\models\cat_sat_forma_pago;
use gamboamartin\cat_sat\models\cat_sat_metodo_pago;
use gamboamartin\cat_sat\models\cat_sat_moneda;
use gamboamartin\cat_sat\models\cat_sat_obj_imp;
use gamboamartin\cat_sat\models\cat_sat_producto;
use gamboamartin\cat_sat\models\cat_sat_regimen_fiscal;
use gamboamartin\cat_sat\models\cat_sat_tipo_de_comprobante;
use gamboamartin\cat_sat\models\cat_sat_tipo_persona;
use gamboamartin\cat_sat\models\cat_sat_unidad;
use gamboamartin\cat_sat\models\cat_sat_uso_cfdi;
use gamboamartin\comercial\models\com_agente;
use gamboamartin\comercial\models\com_cliente;
use gamboamartin\comercial\models\com_producto;
use gamboamartin\comercial\models\com_prospecto;
use gamboamartin\comercial\models\com_sucursal;
use gamboamartin\comercial\models\com_tipo_agente;
use gamboamartin\comercial\models\com_tipo_cambio;
use gamboamartin\comercial\models\com_tipo_cliente;
use gamboamartin\comercial\models\com_tipo_producto;
use gamboamartin\comercial\models\com_tipo_prospecto;
use gamboamartin\comercial\models\com_tipo_sucursal;
use gamboamartin\direccion_postal\models\dp_calle_pertenece;
use gamboamartin\errores\errores;
use PDO;

class base_test{

    public function alta_cat_sat_conf_imps(PDO $link, int $id): array|\stdClass
    {

        $alta = (new \gamboamartin\cat_sat\tests\base_test())->alta_cat_sat_conf_imps(link: $link, id: $id);
        if(errores::$error){
            return (new errores())->error('Error al insertar', $alta);
        }

        return $alta;
    }

    public function alta_cat_sat_tipo_persona(PDO $link, int $id): array|\stdClass
    {

        $alta = (new \gamboamartin\cat_sat\tests\base_test())->alta_cat_sat_tipo_persona(link: $link, id: $id);
        if(errores::$error){
            return (new errores())->error('Error al insertar', $alta);
        }

        return $alta;
    }
    public function alta_cat_sat_forma_pago(PDO $link): array|\stdClass
    {

        $alta = (new \gamboamartin\cat_sat\tests\base_test())->alta_cat_sat_forma_pago(link: $link,codigo: '01',
            descripcion: 'Efectivo');
        if(errores::$error){
            return (new errores())->error('Error al insertar', $alta);
        }

        return $alta;
    }
    public function alta_cat_sat_metodo_pago(PDO $link, string $codigo = 'PUE', int $id = 1,
                                             string $predeterminado = 'inactivo'): array|\stdClass
    {

        $alta = (new \gamboamartin\cat_sat\tests\base_test())->alta_cat_sat_metodo_pago(link: $link, codigo: $codigo,
            descripcion: 'Pago en una sola exhibición', id: $id, predeterminado: $predeterminado);
        if(errores::$error){
            return (new errores())->error('Error al insertar', $alta);
        }

        return $alta;
    }

    public function alta_cat_sat_moneda(PDO $link, int $id = 1, string $predeterminado = 'inactivo'): array|\stdClass
    {

        $alta = (new \gamboamartin\cat_sat\tests\base_test())->alta_cat_sat_moneda(link: $link,id: $id,
            predeterminado: $predeterminado);
        if(errores::$error){
            return (new errores())->error('Error al insertar', $alta);
        }

        return $alta;
    }

    public function alta_cat_sat_obj_imp(PDO $link, int $id): array|\stdClass
    {

        $alta = (new \gamboamartin\cat_sat\tests\base_test())->alta_cat_sat_obj_imp(link: $link, id: $id);
        if(errores::$error){
            return (new errores())->error('Error al insertar', $alta);
        }

        return $alta;
    }

    public function alta_cat_sat_producto(PDO $link, string $codigo = '01010101', int $id = 1): array|\stdClass
    {

        $alta = (new \gamboamartin\cat_sat\tests\base_test())->alta_cat_sat_producto(link: $link,
            codigo: $codigo, id: $id);
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

    public function alta_cat_sat_unidad(PDO $link, int $id): array|\stdClass
    {

        $alta = (new \gamboamartin\cat_sat\tests\base_test())->alta_cat_sat_unidad(link: $link, id: $id);
        if(errores::$error){
            return (new errores())->error('Error al insertar', $alta);
        }

        return $alta;
    }

    public function alta_cat_sat_conf_reg_tp(PDO $link, int $cat_sat_regimen_fiscal_id = 1,
                                             int $cat_sat_tipo_persona_id = 1, int $id = 1): array|\stdClass
    {

        $alta = (new \gamboamartin\cat_sat\tests\base_test())->alta_cat_sat_conf_reg_tp(link: $link,
            cat_sat_regimen_fiscal_id: $cat_sat_regimen_fiscal_id,
            cat_sat_tipo_persona_id: $cat_sat_tipo_persona_id, id: $id);
        if(errores::$error){
            return (new errores())->error('Error al insertar', $alta);
        }

        return $alta;
    }

    public function alta_com_agente(PDO $link, int $adm_grupo_id = 2, string $apellido_paterno = 'AP1',
                                    int $com_tipo_agente_id = 1, string $email = 'a@a.com', int $id = 1,
                                    string $nombre = 'NOMBRE 1', string $password = 'PASS1',
                                    string $telefono = '1234567890', string $user = 'USER 1'): array|\stdClass
    {

        $existe = (new com_tipo_agente($link))->existe_by_id(registro_id: $com_tipo_agente_id);
        if(errores::$error){
            return (new errores())->error('Error al verificar si existe', $existe);
        }

        if(!$existe) {
            $alta = (new base_test())->alta_com_tipo_agente($link);
            if(errores::$error){
                return (new errores())->error('Error al insertar', $alta);
            }
        }


        $registro['id'] = $id;
        $registro['nombre'] = $nombre;
        $registro['apellido_paterno'] = $apellido_paterno;
        $registro['user'] = $user;
        $registro['password'] = $password;
        $registro['email'] = $email;
        $registro['telefono'] = $telefono;
        $registro['adm_grupo_id'] = $adm_grupo_id;
        $registro['com_tipo_agente_id'] = $com_tipo_agente_id;

        $alta = (new com_agente($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error('Error al dar de alta ', $alta);

        }
        return $alta;
    }

    public function alta_com_cliente(PDO $link, int $cat_sat_forma_pago_id = 3,
                                     string $cat_sat_metodo_pago_codigo = 'PUE', int $cat_sat_metodo_pago_id = 1,
                                     int $cat_sat_moneda_id = 161, int $cat_sat_regimen_fiscal_id = 601,
                                     int $cat_sat_tipo_de_comprobante_id = 1, int $cat_sat_tipo_persona_id = 4,
                                     int $cat_sat_uso_cfdi_id = 1, string $codigo = '1', int $com_tipo_cliente_id = 1,
                                     string $descripcion = 'YADIRA MAGALY MONTAÑEZ FELIX',
                                     int $dp_calle_pertenece_id = 1, int $dp_municipio_id = 230,
                                     int $id = 1): array|\stdClass
    {


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

        $existe = (new dp_calle_pertenece($link))->existe_by_id(registro_id: $dp_calle_pertenece_id);
        if(errores::$error){
            return (new errores())->error('Error al verificar si existe', $existe);
        }

        if(!$existe) {
            $alta = (new base_test())->alta_dp_calle_pertenece(link: $link, id: $dp_calle_pertenece_id);
            if (errores::$error) {
                return (new errores())->error('Error al insertar', $alta);
            }
        }

        $existe = (new cat_sat_tipo_persona($link))->existe_by_id(registro_id: $cat_sat_tipo_persona_id);
        if(errores::$error){
            return (new errores())->error('Error al verificar si existe', $existe);
        }

        if(!$existe) {
            $alta = (new base_test())->alta_cat_sat_tipo_persona(link: $link, id: $cat_sat_tipo_persona_id);
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
        $registro['cat_sat_tipo_persona_id'] = $cat_sat_tipo_persona_id;
        $registro['id'] = $id;
        $registro['codigo'] = $codigo;
        $registro['descripcion'] = $descripcion;
        $registro['razon_social'] = 'YADIRA MAGALY MONTAÑEZ FELIX';
        $registro['rfc'] = 'MOFY900516NL1';
        $registro['telefono'] = '3333333333';
        $registro['numero_exterior'] = '3333333333';
        $registro['dp_municipio_id'] = $dp_municipio_id;

        $alta = (new com_cliente($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error('Error al dar de alta ', $alta);

        }
        return $alta;
    }

    public function alta_com_producto(PDO $link, string $aplica_cuenta_predial = 'inactivo',
                                      int $cat_sat_conf_imps_id = 1, int $cat_sat_obj_imp_id = 1,
                                      int $cat_sat_cve_prod_id = 1010101, int $cat_sat_producto_id = 1,
                                      int $cat_sat_unidad_id = 1, int $codigo = 1, int $com_tipo_producto_id = 1,
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

        $existe = (new cat_sat_producto($link))->existe_by_id(registro_id: $cat_sat_producto_id);
        if(errores::$error){
            return (new errores())->error('Error al verificar si existe', $existe);
        }

        if(!$existe) {
            $alta = (new base_test())->alta_cat_sat_producto(link: $link, id: $cat_sat_producto_id);
            if (errores::$error) {
                return (new errores())->error('Error al insertar', $alta);
            }
        }

        $existe = (new cat_sat_unidad($link))->existe_by_id(registro_id: $cat_sat_unidad_id);
        if(errores::$error){
            return (new errores())->error('Error al verificar si existe', $existe);
        }

        if(!$existe) {
            $alta = (new base_test())->alta_cat_sat_unidad(link: $link, id: $cat_sat_unidad_id);
            if (errores::$error) {
                return (new errores())->error('Error al insertar', $alta);
            }
        }

        $existe = (new cat_sat_obj_imp($link))->existe_by_id(registro_id: $cat_sat_obj_imp_id);
        if(errores::$error){
            return (new errores())->error('Error al verificar si existe', $existe);
        }

        if(!$existe) {
            $alta = (new base_test())->alta_cat_sat_obj_imp(link: $link, id: $cat_sat_obj_imp_id);
            if (errores::$error) {
                return (new errores())->error('Error al insertar', $alta);
            }
        }

        $existe = (new cat_sat_conf_imps($link))->existe_by_id(registro_id: $cat_sat_conf_imps_id);
        if(errores::$error){
            return (new errores())->error('Error al verificar si existe', $existe);
        }

        if(!$existe) {
            $alta = (new base_test())->alta_cat_sat_conf_imps(link: $link, id: $cat_sat_conf_imps_id);
            if (errores::$error) {
                return (new errores())->error('Error al insertar', $alta);
            }
        }

        $registro = array();
        $registro['id'] = $id;
        $registro['codigo'] = $codigo;
        $registro['descripcion'] = 1;
        $registro['cat_sat_producto_id'] = $cat_sat_producto_id;
        $registro['cat_sat_unidad_id'] = $cat_sat_unidad_id;
        $registro['cat_sat_obj_imp_id'] = $cat_sat_obj_imp_id;
        $registro['com_tipo_producto_id'] = $com_tipo_producto_id;
        $registro['aplica_cuenta_predial'] = $aplica_cuenta_predial;
        $registro['cat_sat_conf_imps_id'] = $cat_sat_conf_imps_id;
        $registro['cat_sat_cve_prod_id'] = $cat_sat_cve_prod_id;


        $alta = (new com_producto($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error('Error al dar de alta ', $alta);

        }
        return $alta;
    }

    public function alta_com_prospecto(PDO $link, int $com_agente_id = 1, int $com_tipo_prospecto_id = 1,
                                       int $com_medio_prospeccion_id = 100, string $apellido_paterno = 'AP1',
                                       int $id = 1, string $nombre = 'NOMBRE 1', string $razon_social = 'RAZON SOCIAL',
                                       string $telefono = '1234567890'): array|\stdClass
    {


        $registro['id'] = $id;
        $registro['nombre'] = $nombre;
        $registro['apellido_paterno'] = $apellido_paterno;
        $registro['com_tipo_prospecto_id'] = $com_tipo_prospecto_id;
        $registro['com_agente_id'] = $com_agente_id;
        $registro['telefono'] = $telefono;
        $registro['razon_social'] = $razon_social;
        $registro['com_medio_prospeccion_id'] = $com_medio_prospeccion_id;

        $alta = (new com_prospecto($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error('Error al dar de alta ', $alta);

        }
        return $alta;
    }


    public function alta_com_sucursal(PDO $link, string $calle = 'CALLE', int $cat_sat_forma_pago_id = 3,
                                      string $cat_sat_metodo_pago_codigo = 'PUE', int $cat_sat_metodo_pago_id = 1,
                                      string $colonia = 'COLONIA', int $com_cliente_id = 1,
                                      int $cat_sat_regimen_fiscal_id = 601, int $cat_sat_tipo_persona_id = 4,
                                      int $com_tipo_sucursal_id = 1, string $cp = '1', int $dp_municipio_id = 230,
                                      int $id = 1): array|\stdClass
    {

        $existe = (new com_cliente($link))->existe_by_id(registro_id: $com_cliente_id);
        if(errores::$error){
            return (new errores())->error('Error al verificar si existe', $existe);
        }

        if(!$existe) {
            $alta = (new base_test())->alta_com_cliente(link: $link, cat_sat_forma_pago_id: $cat_sat_forma_pago_id,
                cat_sat_metodo_pago_codigo: $cat_sat_metodo_pago_codigo,
                cat_sat_metodo_pago_id: $cat_sat_metodo_pago_id, cat_sat_regimen_fiscal_id: $cat_sat_regimen_fiscal_id,
                cat_sat_tipo_persona_id: $cat_sat_tipo_persona_id, id: $com_cliente_id);
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
        $registro['numero_exterior'] = 1;
        $registro['dp_municipio_id'] = $dp_municipio_id;
        $registro['cp'] = $cp;
        $registro['colonia'] = $colonia;
        $registro['calle'] = $calle;

        $alta = (new com_sucursal($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error('Error al dar de alta ', $alta);

        }
        return $alta;
    }

    public function alta_com_tipo_agente(PDO $link, string $descripcion = 'TIPO AGENTE 1', int $id = 1): array|\stdClass
    {



        $registro['id'] = $id;
        $registro['descripcion'] = $descripcion;


        $alta = (new com_tipo_agente($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error('Error al dar de alta ', $alta);

        }
        return $alta;
    }

    public function alta_com_tipo_cambio(PDO $link, int $cat_sat_moneda_id = 161, string $codigo = '1', string $fecha = '2020-01-01',
                                         int $id = 1): array|\stdClass
    {

        $registro = array();
        $registro['id'] = $id;
        $registro['codigo'] = $codigo;
        $registro['descripcion'] = 1;
        $registro['cat_sat_moneda_id'] = $cat_sat_moneda_id;
        $registro['monto'] = 1;
        $registro['fecha'] = $fecha;


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
        $registro['descripcion'] = 'TEST';


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

    public function alta_com_tipo_prospecto(PDO $link, string $descripcion = 'TIPO 1', int $id = 1): array|\stdClass
    {

        $registro = array();
        $registro['id'] = $id;
       // $registro['codigo'] = 1;
        $registro['descripcion'] = $descripcion;


        $alta = (new com_tipo_prospecto($link))->alta_registro($registro);
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

    public function alta_dp_calle_pertenece(PDO $link, int $id): array|\stdClass
    {

        $alta = (new \gamboamartin\direccion_postal\tests\base_test())->alta_dp_calle_pertenece(link: $link, id: $id);
        if(errores::$error){
            return (new errores())->error('Error al insertar', $alta);
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

        $del = (new base_test())->del_com_cliente($link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar', $del);
            print_r($error);
            exit;
        }

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

    public function del_cat_sat_producto(PDO $link): array
    {
        $del = (new base_test())->del_com_producto($link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar', $del);
            print_r($error);
            exit;
        }

        $del =(new \gamboamartin\cat_sat\tests\base_test())->del_cat_sat_producto($link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }



    public function del_com_agente(PDO $link): array
    {
        $del = $this->del_com_tels_agente($link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }

        $del = $this->del_com_rel_agente($link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        $del = $this->del_com_prospecto($link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }

        $del = $this->del($link, 'gamboamartin\\comercial\\models\\com_agente');
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
        $del = $this->del_com_email_cte($link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }


        $del = $this->del_com_precio_cliente($link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        $del = $this->del_com_rel_prospecto_cte($link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        $del = $this->del_com_contacto($link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }

        $del = $this->del_com_cliente_documento($link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }

        $del = $this->del($link, 'gamboamartin\\comercial\\models\\com_cliente');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_com_direccion_prospecto(PDO $link): array
    {
        $del = $this->del($link, 'gamboamartin\\comercial\\models\\com_direccion_prospecto');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }


    public function del_com_precio_cliente(PDO $link): array
    {


        $del = $this->del($link, 'gamboamartin\\comercial\\models\\com_precio_cliente');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_com_prospecto(PDO $link): array
    {
        $del = $this->del_com_rel_agente($link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        $del = $this->del_com_rel_prospecto_cte($link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        $del = $this->del_com_prospecto_etapa($link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }

        $del = $this->del_com_direccion_prospecto($link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }

        $del = $this->del($link, 'gamboamartin\\comercial\\models\\com_prospecto');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_com_email_cte(PDO $link): array
    {

        $del = $this->del($link, 'gamboamartin\\comercial\\models\\com_email_cte');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_com_producto(PDO $link): array
    {
        $del = $this->del_com_tmp_prod_cs($link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        $del = $this->del_com_conf_precio($link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }

        $del = $this->del($link, 'gamboamartin\\comercial\\models\\com_producto');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }
    public function del_com_conf_precio(PDO $link): array
    {

        $del = $this->del($link, 'gamboamartin\\comercial\\models\\com_conf_precio');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_com_rel_agente(PDO $link): array
    {

        $del = $this->del($link, 'gamboamartin\\comercial\\models\\com_rel_agente');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }



    public function del_com_rel_prospecto_cte(PDO $link): array
    {

        $del = $this->del($link, 'gamboamartin\\comercial\\models\\com_rel_prospecto_cte');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_com_cliente_documento(PDO $link): array
    {

        $del = $this->del($link, 'gamboamartin\\comercial\\models\\com_cliente_documento');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_com_contacto(PDO $link): array
    {

        $del = $this->del_com_contacto_user($link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }

        $del = $this->del($link, 'gamboamartin\\comercial\\models\\com_contacto');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_com_contacto_user(PDO $link): array
    {

        $del = $this->del($link, 'gamboamartin\\comercial\\models\\com_contacto_user');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_com_prospecto_etapa(PDO $link): array
    {

        $del = $this->del($link, 'gamboamartin\\comercial\\models\\com_prospecto_etapa');
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

    public function del_com_tels_agente(PDO $link): array
    {

        $del = $this->del($link, 'gamboamartin\\comercial\\models\\com_tels_agente');
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

    public function del_com_tipo_prospecto(PDO $link): array
    {

        $del = $this->del_com_prospecto($link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }

        $del = $this->del($link, 'gamboamartin\\comercial\\models\\com_tipo_prospecto');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_com_tmp_prod_cs(PDO $link): array
    {

        $del = $this->del($link, 'gamboamartin\\comercial\\models\\com_tmp_prod_cs');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }



}
