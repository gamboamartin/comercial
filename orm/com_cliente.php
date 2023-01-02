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
        $columnas = array($tabla=>false,'cat_sat_moneda'=>$tabla, 'cat_sat_regimen_fiscal' => $tabla,
            'dp_calle_pertenece' => $tabla, 'dp_colonia_postal' => 'dp_calle_pertenece', 'dp_cp' => 'dp_colonia_postal',
            'dp_municipio' => 'dp_cp','dp_estado' => 'dp_municipio','dp_pais' => 'dp_estado','com_tipo_cliente'=>$tabla);
        $campos_obligatorios = array('cat_sat_moneda_id','cat_sat_regimen_fiscal_id','cat_sat_moneda_id',
            'cat_sat_forma_pago_id','cat_sat_uso_cfdi_id','cat_sat_tipo_de_comprobante_id','cat_sat_metodo_pago_id');

        $tipo_campos = array();
        $tipo_campos['rfc'] = 'rfc';

        $campos_view['dp_pais_id'] = array('type' => 'selects', 'model' => new dp_pais($link));
        $campos_view['dp_estado_id'] = array('type' => 'selects', 'model' => new dp_estado($link));
        $campos_view['dp_municipio_id'] = array('type' => 'selects', 'model' => new dp_municipio($link));
        $campos_view['dp_cp_id'] = array('type' => 'selects', 'model' => new dp_cp($link));
        $campos_view['dp_colonia_postal_id'] = array('type' => 'selects', 'model' => new dp_colonia_postal($link));
        $campos_view['dp_calle_pertenece_id'] = array('type' => 'selects', 'model' => new dp_calle_pertenece($link));
        $campos_view['cat_sat_regimen_fiscal_id'] = array('type' => 'selects', 'model' => new cat_sat_regimen_fiscal($link));
        $campos_view['cat_sat_moneda_id'] = array('type' => 'selects', 'model' => new cat_sat_moneda($link));
        $campos_view['cat_sat_forma_pago_id'] = array('type' => 'selects', 'model' => new cat_sat_forma_pago($link));
        $campos_view['cat_sat_uso_cfdi_id'] = array('type' => 'selects', 'model' => new cat_sat_uso_cfdi($link));
        $campos_view['cat_sat_metodo_pago_id'] = array('type' => 'selects', 'model' => new cat_sat_metodo_pago($link));
        $campos_view['cat_sat_tipo_de_comprobante_id'] = array('type' => 'selects', 'model' => new cat_sat_tipo_de_comprobante($link));
        $campos_view['com_tipo_cliente_id'] = array('type' => 'selects', 'model' => new com_tipo_cliente($link));
        $campos_view['codigo'] = array('type' => 'inputs');
        $campos_view['razon_social'] = array('type' => 'inputs');
        $campos_view['rfc'] = array('type' => 'inputs');
        $campos_view['numero_exterior'] = array('type' => 'inputs');
        $campos_view['numero_interior'] = array('type' => 'inputs');
        $campos_view['telefono'] = array('type' => 'inputs');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, campos_view: $campos_view, tipo_campos: $tipo_campos);

        $this->NAMESPACE = __NAMESPACE__;
    }
    public function alta_bd(): array|stdClass
    {
        $this->registro = $this->init_base(data:$this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar campo base',data: $this->registro);
        }

        $this->registro = $this->inicializa_foraneas(data:$this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar foraneas',data: $this->registro);
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
            $data['descripcion'] =  $data['razon_social'];
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

    private function inicializa_foraneas(array $data): array
    {
        $foraneas['cat_sat_moneda_id'] = new cat_sat_moneda($this->link);
        $foraneas['dp_calle_pertenece_id'] = new dp_calle_pertenece($this->link);
        $foraneas['cat_sat_regimen_fiscal_id'] = new cat_sat_regimen_fiscal($this->link);
        $foraneas['cat_sat_forma_pago_id'] = new cat_sat_forma_pago($this->link);
        $foraneas['cat_sat_uso_cfdi_id'] = new cat_sat_tipo_de_comprobante($this->link);
        $foraneas['cat_sat_tipo_de_comprobante_id'] = new cat_sat_uso_cfdi($this->link);
        $foraneas['cat_sat_metodo_pago_id'] = new cat_sat_metodo_pago($this->link);
        $foraneas['com_tipo_cliente_id'] = new com_tipo_cliente($this->link);

        foreach ($foraneas as $key => $valor){
            if(!isset($data[$key]) || $data[$key] === -1){
                $predeterminado = ($valor)->id_predeterminado();
                if(errores::$error){
                    return $this->error->error(mensaje: "Error al $key predeterminada", data: $predeterminado);
                }
                $data[$key] = $predeterminado;
            }
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

    public function modifica_bd(array $registro, int $id, bool $reactiva = false): array|stdClass
    {
        $registro = $this->init_base(data:$registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar campo base',data: $registro);
        }

        $registro = $this->inicializa_foraneas(data:$registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar foraneas',data: $registro);
        }

        $registro = $this->limpia_campos(registro: $registro, campos_limpiar: array('dp_pais_id', 'dp_estado_id',
            'dp_municipio_id','dp_cp_id','dp_cp_id','dp_colonia_postal_id'));
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al limpiar campos', data: $registro);
        }

        $r_modifica_bd = parent::modifica_bd($registro, $id, $reactiva);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar cliente',data:  $r_modifica_bd);
        }

        return $r_modifica_bd;
    }
}