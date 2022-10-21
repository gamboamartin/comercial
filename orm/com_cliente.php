<?php
namespace gamboamartin\comercial\models;
use base\orm\modelo;
use gamboamartin\cat_sat\models\cat_sat_forma_pago;
use gamboamartin\cat_sat\models\cat_sat_metodo_pago;
use gamboamartin\cat_sat\models\cat_sat_moneda;
use gamboamartin\cat_sat\models\cat_sat_regimen_fiscal;
use gamboamartin\cat_sat\models\cat_sat_tipo_de_comprobante;
use gamboamartin\cat_sat\models\cat_sat_uso_cfdi;
use gamboamartin\direccion_postal\models\dp_calle_pertenece;
use gamboamartin\direccion_postal\models\dp_colonia;
use gamboamartin\direccion_postal\models\dp_cp;
use gamboamartin\direccion_postal\models\dp_estado;
use gamboamartin\direccion_postal\models\dp_municipio;
use gamboamartin\direccion_postal\models\dp_pais;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class com_cliente extends modelo{
    public function __construct(PDO $link){
        $tabla = 'com_cliente';
        $columnas = array($tabla=>false,'cat_sat_moneda'=>$tabla,'dp_pais'=>'cat_sat_moneda');
        $campos_obligatorios = array('cat_sat_moneda_id','cat_sat_regimen_fiscal_id','cat_sat_moneda_id',
            'cat_sat_forma_pago_id','cat_sat_uso_cfdi_id','cat_sat_tipo_de_comprobante_id','cat_sat_metodo_pago_id');

        $tipo_campos = array();
        $tipo_campos['rfc'] = 'rfc';

        $campos_view = array();
        $campos_view['dp_pais_id']['type'] = 'selects';
        $campos_view['dp_pais_id']['model'] = (new dp_pais($link));

        $campos_view['dp_estado_id']['type'] = 'selects';
        $campos_view['dp_estado_id']['model'] = (new dp_estado($link));

        $campos_view['dp_municipio_id']['type'] = 'selects';
        $campos_view['dp_municipio_id']['model'] = (new dp_municipio($link));

        $campos_view['dp_cp_id']['type'] = 'selects';
        $campos_view['dp_cp_id']['model'] = (new dp_cp($link));

        $campos_view['dp_colonia_id']['type'] = 'selects';
        $campos_view['dp_colonia_id']['model'] = (new dp_colonia($link));

        $campos_view['dp_calle_pertenece_id']['type'] = 'selects';
        $campos_view['dp_calle_pertenece_id']['model'] = (new dp_calle_pertenece($link));

        $campos_view['cat_sat_regimen_fiscal_id']['type'] = 'selects';
        $campos_view['cat_sat_regimen_fiscal_id']['model'] = (new cat_sat_regimen_fiscal($link));

        $campos_view['cat_sat_moneda_id']['type'] = 'selects';
        $campos_view['cat_sat_moneda_id']['model'] = (new cat_sat_moneda($link));

        $campos_view['cat_sat_forma_pago_id']['type'] = 'selects';
        $campos_view['cat_sat_forma_pago_id']['model'] = (new cat_sat_forma_pago($link));

        $campos_view['cat_sat_metodo_pago_id']['type'] = 'selects';
        $campos_view['cat_sat_metodo_pago_id']['model'] = (new cat_sat_metodo_pago($link));

        $campos_view['cat_sat_uso_cfdi_id']['type'] = 'selects';
        $campos_view['cat_sat_uso_cfdi_id']['model'] = (new cat_sat_uso_cfdi($link));

        $campos_view['cat_sat_tipo_de_comprobante_id']['type'] = 'selects';
        $campos_view['cat_sat_tipo_de_comprobante_id']['model'] = (new cat_sat_tipo_de_comprobante($link));

        $campos_view['razon_social']['type'] = 'inputs';
        $campos_view['rfc']['type'] = 'inputs';
        $campos_view['numero_exterior']['type'] = 'inputs';
        $campos_view['numero_interior']['type'] = 'inputs';
        $campos_view['telefono']['type'] = 'inputs';


        parent::__construct(link: $link,tabla:  $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, tipo_campos: $tipo_campos, campos_view: $campos_view);

        $this->NAMESPACE = __NAMESPACE__;
    }
    public function alta_bd(): array|stdClass
    {

        $registro = $this->descripcion_select(registro: $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al asignar descripcion select', data: $registro);
        }

        if(!isset($registro['cat_sat_moneda_id']) || $registro['cat_sat_moneda_id'] === -1){
            $id_predeterminado = (new cat_sat_moneda($this->link))->id_predeterminado();
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener moneda predeterminada', data: $id_predeterminado);
            }
            $registro['cat_sat_moneda_id'] = $id_predeterminado;
        }

        if(!isset($registro['dp_calle_pertenece_id']) || $registro['dp_calle_pertenece_id'] === -1){
            $id_predeterminado = (new dp_calle_pertenece($this->link))->id_predeterminado();
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener dp_calle_pertenece predeterminada', data: $id_predeterminado);
            }
            $registro['dp_calle_pertenece_id'] = $id_predeterminado;
        }

        if(!isset($registro['cat_sat_regimen_fiscal_id']) || $registro['cat_sat_regimen_fiscal_id'] === -1){
            $id_predeterminado = (new cat_sat_regimen_fiscal($this->link))->id_predeterminado();
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener cat_sat_regimen_fiscal predeterminada', data: $id_predeterminado);
            }
            $registro['cat_sat_regimen_fiscal_id'] = $id_predeterminado;
        }

        if(!isset($registro['cat_sat_forma_pago_id']) || $registro['cat_sat_forma_pago_id'] === -1){
            $id_predeterminado = (new cat_sat_forma_pago($this->link))->id_predeterminado();
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener cat_sat_forma_pago_id predeterminada', data: $id_predeterminado);
            }
            $registro['cat_sat_forma_pago_id'] = $id_predeterminado;
        }

        if(!isset($registro['cat_sat_uso_cfdi_id']) || $registro['cat_sat_uso_cfdi_id'] === -1){
            $id_predeterminado = (new cat_sat_uso_cfdi($this->link))->id_predeterminado();
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener cat_sat_uso_cfdi predeterminada', data: $id_predeterminado);
            }
            $registro['cat_sat_uso_cfdi_id'] = $id_predeterminado;
        }
        if(!isset($registro['cat_sat_tipo_de_comprobante_id']) || $registro['cat_sat_tipo_de_comprobante_id'] === -1){
            $id_predeterminado = (new cat_sat_tipo_de_comprobante($this->link))->id_predeterminado();
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener cat_sat_tipo_de_comprobante predeterminada', data: $id_predeterminado);
            }
            $registro['cat_sat_tipo_de_comprobante_id'] = $id_predeterminado;
        }

        if(!isset($registro['cat_sat_metodo_pago_id']) || $registro['cat_sat_metodo_pago_id'] === -1){
            $id_predeterminado = (new cat_sat_metodo_pago($this->link))->id_predeterminado();
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener cat_sat_metodo_pago predeterminada', data: $id_predeterminado);
            }
            $registro['cat_sat_metodo_pago_id'] = $id_predeterminado;
        }



        $this->registro = $registro;

        $r_alta_bd = parent::alta_bd(); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al dar de alta registro', data: $r_alta_bd);
        }
        return $r_alta_bd;
    }

    /**
     * Ajusta la descripcion select
     * @param array $registro Registro en proceso de alta
     * @return array
     * @version 0.28.6
     */
    private function descripcion_select(array $registro): array
    {
        $keys = array('codigo','razon_social','rfc');
        $valida = $this->validacion->valida_existencia_keys(keys:$keys, registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro', data: $valida);
        }
        if(!isset($registro['descripcion_select']) || $registro['descripcion_select'] === ''){
            $registro['descripcion_select'] = $registro['codigo'];
            $registro['descripcion_select'] .= ' '.$registro['rfc'];
            $registro['descripcion_select'] .= ' '.$registro['razon_social'];
        }
        return $registro;
    }
}