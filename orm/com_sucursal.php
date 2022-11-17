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
use gamboamartin\direccion_postal\models\dp_colonia_postal;
use gamboamartin\direccion_postal\models\dp_pais;
use gamboamartin\direccion_postal\models\dp_colonia;
use gamboamartin\direccion_postal\models\dp_cp;
use gamboamartin\direccion_postal\models\dp_municipio;
use gamboamartin\direccion_postal\models\dp_estado;

use gamboamartin\errores\errores;
use PDO;
use stdClass;

class com_sucursal extends modelo{

    public function __construct(PDO $link){
        $tabla = 'com_sucursal';
        $columnas = array($tabla=>false,'com_cliente'=>$tabla,'dp_calle_pertenece'=>$tabla,
            'dp_colonia_postal'=>'dp_calle_pertenece','dp_cp'=>'dp_colonia_postal',
            'cat_sat_regimen_fiscal'=>'com_cliente','dp_municipio'=>'dp_cp');
        $campos_obligatorios = array('descripcion','codigo','descripcion_select','alias','codigo_bis',
            'numero_exterior','com_cliente_id', 'dp_calle_pertenece_id');

        $tipo_campos = array();

        $campos_view['dp_pais_id'] = array('type' => 'selects', 'model' => new dp_pais($link));
        $campos_view['dp_estado_id'] = array('type' => 'selects', 'model' => new dp_estado($link));
        $campos_view['dp_municipio_id'] = array('type' => 'selects', 'model' => new dp_municipio($link));
        $campos_view['dp_cp_id'] = array('type' => 'selects', 'model' => new dp_cp($link));
        $campos_view['dp_colonia_postal_id'] = array('type' => 'selects', 'model' => new dp_colonia_postal($link));
        $campos_view['dp_calle_pertenece_id'] = array('type' => 'selects', 'model' => new dp_calle_pertenece($link));
        $campos_view['com_cliente_id'] = array('type' => 'selects', 'model' => new com_cliente($link));
        $campos_view['codigo'] = array('type' => 'inputs');
        $campos_view['nombre_contacto'] = array('type' => 'inputs');
        $campos_view['numero_exterior'] = array('type' => 'inputs');
        $campos_view['numero_interior'] = array('type' => 'inputs');
        $campos_view['telefono_1'] = array('type' => 'inputs');
        $campos_view['telefono_2'] = array('type' => 'inputs');
        $campos_view['telefono_3'] = array('type' => 'inputs');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, campos_view: $campos_view, tipo_campos: $tipo_campos);

        $this->NAMESPACE = __NAMESPACE__;

    }

    public function alta_bd(): array|stdClass
    {
        $keys = array('com_cliente_id','dp_calle_pertenece_id');
        $valida = $this->validacion->valida_ids(keys:$keys,registro:  $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }

        $this->registro = $this->init_base(data:$this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar campo base',data: $this->registro);
        }

        $this->registro = $this->limpia_campos(registro: $this->registro, campos_limpiar: array('dp_pais_id',
            'dp_estado_id','dp_municipio_id','dp_cp_id','dp_cp_id','dp_colonia_postal_id'));
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al limpiar campos', data: $this->registro);
        }

        $r_alta_bd =  parent::alta_bd();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar cliente', data: $r_alta_bd);
        }
        return $r_alta_bd;
    }

    protected function init_base(array $data): array
    {
        if(!isset($data['descripcion'])){
            $data['descripcion'] =  $data['nombre_contacto'];
        }

        if(!isset($data['codigo_bis'])){
            $data['codigo_bis'] =  $data['codigo'];
        }

        if(!isset($data['descripcion_select'])){
            $ds = str_replace("_"," ",$data['descripcion']);
            $ds = ucwords($ds);
            $data['descripcion_select'] =  "{$data['codigo']} - {$ds}";
        }

        if(!isset($data['alias'])){
            $data['alias'] = $data['codigo'];
        }
        return $data;
    }

    private function limpia_campos(array $registro, array $campos_limpiar): array
    {
        foreach ($campos_limpiar as $valor) {
            if (isset($registro[$valor])) {
                unset($registro[$valor]);
            }
        }
        return $registro;
    }

    /*
     * REVISAR
     */

    public function em_empleado_by_sucursal(int $com_sucursal_id): array
    {

        $filtro['com_sucursal.id'] = $com_sucursal_id;
        $r_tg_em_empleado_sucursal = (new nom_rel_empleado_sucursal($this->link))->filtro_and(filtro:$filtro);
        if(errores::$error){

            return $this->error->error(mensaje: 'Error al limpiar datos',data:  $r_tg_em_empleado_sucursal);
        }
        return $r_tg_em_empleado_sucursal->registros;

    }

    public function modifica_bd(array $registro, int $id, bool $reactiva = false): array|stdClass
    {
        $registro = $this->init_base(data:$registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar campo base',data: $registro);
        }

        $registro = $this->limpia_campos(registro: $registro, campos_limpiar: array('dp_pais_id', 'dp_estado_id',
            'dp_municipio_id','dp_cp_id','dp_cp_id','dp_colonia_postal_id'));
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al limpiar campos', data: $registro);
        }

        $r_modifica_bd = parent::modifica_bd($registro, $id, $reactiva);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar producto',data:  $r_modifica_bd);
        }

        return $r_modifica_bd;
    }

    public function sucursales(int $com_cliente_id): array|stdClass
    {
        if($com_cliente_id <=0){
            return $this->error->error(mensaje: 'Error $com_cliente_id debe ser mayor a 0', data: $com_cliente_id);
        }
        $filtro['com_cliente.id'] = $com_cliente_id;
        $r_com_sucursal = $this->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener sucursales', data: $r_com_sucursal);
        }
        return $r_com_sucursal;
    }
}