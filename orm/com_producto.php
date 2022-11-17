<?php
namespace gamboamartin\comercial\models;
use base\orm\modelo;
use gamboamartin\cat_sat\models\cat_sat_clase_producto;
use gamboamartin\cat_sat\models\cat_sat_division_producto;
use gamboamartin\cat_sat\models\cat_sat_factor;
use gamboamartin\cat_sat\models\cat_sat_grupo_producto;
use gamboamartin\cat_sat\models\cat_sat_obj_imp;
use gamboamartin\cat_sat\models\cat_sat_producto;
use gamboamartin\cat_sat\models\cat_sat_tipo_factor;
use gamboamartin\cat_sat\models\cat_sat_tipo_producto;
use gamboamartin\cat_sat\models\cat_sat_unidad;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class com_producto extends modelo{

    public function __construct(PDO $link){
        $tabla = 'com_producto';
        $columnas = array($tabla=>false,'cat_sat_factor'=>$tabla,'cat_sat_obj_imp'=>$tabla,'cat_sat_producto'=>$tabla,
            'cat_sat_unidad'=>$tabla,'cat_sat_tipo_factor'=>$tabla);
        $campos_obligatorios = array();

        $campos_view['cat_sat_tipo_producto_id'] = array('type' => 'selects', 'model' => new cat_sat_tipo_producto($link));
        $campos_view['cat_sat_division_producto_id'] = array('type' => 'selects', 'model' => new cat_sat_division_producto($link));
        $campos_view['cat_sat_grupo_producto_id'] = array('type' => 'selects', 'model' => new cat_sat_grupo_producto($link));
        $campos_view['cat_sat_clase_producto_id'] = array('type' => 'selects', 'model' => new cat_sat_clase_producto($link));
        $campos_view['cat_sat_producto_id'] = array('type' => 'selects', 'model' => new cat_sat_producto($link));
        $campos_view['cat_sat_unidad_id'] = array('type' => 'selects', 'model' => new cat_sat_unidad($link));
        $campos_view['cat_sat_obj_imp_id'] = array('type' => 'selects', 'model' => new cat_sat_obj_imp($link));
        $campos_view['cat_sat_tipo_factor_id'] = array('type' => 'selects', 'model' => new cat_sat_tipo_factor($link));
        $campos_view['cat_sat_factor_id'] = array('type' => 'selects', 'model' => new cat_sat_factor($link));
        $campos_view['codigo'] = array('type' => 'inputs');
        $campos_view['descripcion'] = array('type' => 'inputs');

        parent::__construct(link: $link,tabla:  $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, campos_view: $campos_view);

        $this->NAMESPACE = __NAMESPACE__;
    }

    public function alta_bd(): array|stdClass
    {
        $this->registro = $this->campos_base(data:$this->registro, modelo: $this);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar campo base',data: $this->registro);
        }

        $this->registro = $this->limpia_campos(registro: $this->registro, campos_limpiar: array('cat_sat_tipo_producto_id',
            'cat_sat_division_producto_id','cat_sat_grupo_producto_id','cat_sat_clase_producto_id'));
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al limpiar campos', data: $this->registro);
        }

        $r_alta_bd =  parent::alta_bd();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar producto', data: $r_alta_bd);
        }
        return $r_alta_bd;
    }

    protected function campos_base(array $data,modelo $modelo, int $id = -1): array
    {
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

    public function modifica_bd(array $registro, int $id, bool $reactiva = false): array|stdClass
    {
        $registro = $this->campos_base(data:$registro, modelo: $this);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar campo base',data: $registro);
        }

        $registro = $this->limpia_campos(registro: $registro, campos_limpiar: array('cat_sat_tipo_producto_id',
            'cat_sat_division_producto_id','cat_sat_grupo_producto_id','cat_sat_clase_producto_id'));
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al limpiar campos', data: $registro);
        }

        $r_modifica_bd = parent::modifica_bd($registro, $id, $reactiva);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar producto',data:  $r_modifica_bd);
        }

        return $r_modifica_bd;
    }
}