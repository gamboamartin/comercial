<?php
namespace gamboamartin\comercial\models;
use base\orm\_modelo_parent;
use gamboamartin\cat_sat\models\cat_sat_clase_producto;
use gamboamartin\cat_sat\models\cat_sat_conf_imps;
use gamboamartin\cat_sat\models\cat_sat_division_producto;
use gamboamartin\cat_sat\models\cat_sat_grupo_producto;
use gamboamartin\cat_sat\models\cat_sat_obj_imp;
use gamboamartin\cat_sat\models\cat_sat_producto;
use gamboamartin\cat_sat\models\cat_sat_tipo_producto;
use gamboamartin\cat_sat\models\cat_sat_unidad;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class com_producto extends _modelo_parent {

    public function __construct(PDO $link){
        $tabla = 'com_producto';
        $columnas = array($tabla=>false,'cat_sat_obj_imp'=>$tabla,'cat_sat_producto'=>$tabla, 'cat_sat_unidad'=>$tabla,
            'com_tipo_producto'=>$tabla, 'cat_sat_clase_producto' => 'cat_sat_producto',
            'cat_sat_grupo_producto' => 'cat_sat_clase_producto', 'cat_sat_division_producto' => 'cat_sat_grupo_producto',
            'cat_sat_tipo_producto' => 'cat_sat_division_producto','cat_sat_conf_imps'=>$tabla);
        $campos_obligatorios = array('cat_sat_producto_id','cat_sat_unidad_id','cat_sat_obj_imp_id',
            'com_tipo_producto_id','cat_sat_conf_imps_id');

        $campos_view['cat_sat_tipo_producto_id'] = array('type' => 'selects', 'model' => new cat_sat_tipo_producto($link));
        $campos_view['cat_sat_division_producto_id'] = array('type' => 'selects', 'model' => new cat_sat_division_producto($link));
        $campos_view['cat_sat_grupo_producto_id'] = array('type' => 'selects', 'model' => new cat_sat_grupo_producto($link));
        $campos_view['cat_sat_clase_producto_id'] = array('type' => 'selects', 'model' => new cat_sat_clase_producto($link));
        $campos_view['cat_sat_producto_id'] = array('type' => 'selects', 'model' => new cat_sat_producto($link));
        $campos_view['cat_sat_unidad_id'] = array('type' => 'selects', 'model' => new cat_sat_unidad($link));
        $campos_view['cat_sat_obj_imp_id'] = array('type' => 'selects', 'model' => new cat_sat_obj_imp($link));
        $campos_view['cat_sat_conf_imps_id'] = array('type' => 'selects', 'model' => new cat_sat_conf_imps($link));
        $campos_view['com_tipo_producto_id'] = array('type' => 'selects', 'model' => new com_tipo_producto($link));
        $campos_view['codigo'] = array('type' => 'inputs');
        $campos_view['descripcion'] = array('type' => 'inputs');


        $atributos_criticos[] = 'es_automatico';
        $atributos_criticos[] = 'cat_sat_obj_imp_id';
        $atributos_criticos[] = 'cat_sat_producto_id';
        $atributos_criticos[] = 'cat_sat_unidad_id';
        $atributos_criticos[] = 'com_tipo_producto_id';
        $atributos_criticos[] = 'cat_sat_conf_imps_id';

        parent::__construct(link: $link,tabla:  $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, campos_view: $campos_view, atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Producto';
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {

        $integra_tmp = false;
        $cat_sat_producto_data = '';
        if(isset($this->registro['cat_sat_producto'])){
            if(trim($this->registro['cat_sat_producto'] !=='')){

                $existe = $this->existe_cat_sat_producto(cat_sat_producto_codigo: $this->registro['cat_sat_producto']);
                if(errores::$error){
                    return $this->error->error(mensaje: 'Error al validar si existe producto',data: $existe);
                }
                if($existe){
                    $filtro['cat_sat_producto.codigo'] = $this->registro['cat_sat_producto'];
                    $cat_sat_producto = $this->cat_sat_producto(filtro: $filtro);
                    if(errores::$error){
                        return $this->error->error(mensaje: 'Error al obtener cat_sat_producto',data: $cat_sat_producto);
                    }

                    $this->registro['cat_sat_producto_id'] = $cat_sat_producto['cat_sat_producto_id'];
                }
                else{
                    $cat_sat_producto_data = $this->registro['cat_sat_producto'];
                    $integra_tmp = true;
                }
                unset($this->registro['cat_sat_producto']);
            }
        }
        if(!isset($this->registro['cat_sat_producto_id']) || trim($this->registro['cat_sat_producto_id']) === ''){
            $this->registro['cat_sat_producto_id'] = '97999999';
        }


        $this->registro = $this->campos_base(data:$this->registro, modelo: $this);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar campo base',data: $this->registro);
        }

        $this->registro = $this->limpia_campos(registro: $this->registro, campos_limpiar:
            array('cat_sat_tipo_producto_id', 'cat_sat_division_producto_id','cat_sat_grupo_producto_id',
                'cat_sat_clase_producto_id'));
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al limpiar campos', data: $this->registro);
        }

        if(!isset($this->registro['es_automatico'])){
            $this->registro['es_automatico'] = 'inactivo';
        }

        $r_alta_bd =  parent::alta_bd();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar producto', data: $r_alta_bd);
        }

        if($integra_tmp){
            $r_alta_com_tmp = $this->inserta_producto_tmp(cat_sat_producto_data: $cat_sat_producto_data,
                com_producto_id: $r_alta_bd->registro_id);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al insertar producto temporal', data: $r_alta_com_tmp);
            }
        }

        return $r_alta_bd;
    }

    private function cat_sat_producto(array $filtro){
        $r_cat_sat_producto = (new cat_sat_producto(link: $this->link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al al obtener producto',data: $r_cat_sat_producto);
        }
        if((int)$r_cat_sat_producto->n_registros  === 0){
            return $this->error->error(mensaje: 'Error no existe el producto',data: $r_cat_sat_producto);
        }
        if((int)$r_cat_sat_producto->n_registros  > 1){
            return $this->error->error(mensaje: 'Error existe mas de un producto',data: $r_cat_sat_producto);
        }
        return $r_cat_sat_producto->registros[0];
    }

    private function existe_cat_sat_producto(string $cat_sat_producto_codigo){
        $filtro['cat_sat_producto.codigo'] = $cat_sat_producto_codigo;
        $existe = (new cat_sat_producto(link: $this->link))->existe(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar si existe producto',data: $existe);
        }
        return $existe;
    }

    public function get_producto(int $com_producto_id): array|stdClass|int
    {
        $registro = $this->registro(registro_id: $com_producto_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener producto',data:  $registro);
        }

        return $registro;
    }

    private function inserta_producto_tmp(string $cat_sat_producto_data, int $com_producto_id){
        $com_tmp_prod_cs_ins['com_producto_id'] = $com_producto_id;
        $com_tmp_prod_cs_ins['cat_sat_producto'] = $cat_sat_producto_data;
        $r_alta_com_tmp = (new com_tmp_prod_cs(link: $this->link))->alta_registro(registro: $com_tmp_prod_cs_ins);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar producto temporal', data: $r_alta_com_tmp);
        }
        return $r_alta_com_tmp;
    }

    /**
     * Limpia los campos no insertables
     * @param array $registro Registro en proceso
     * @param array $campos_limpiar Campos a limpiar
     * @return array
     * @version 7.14.1
     */
    private function limpia_campos(array $registro, array $campos_limpiar): array
    {
        foreach ($campos_limpiar as $valor) {
            if (isset($registro[$valor])) {
                unset($registro[$valor]);
            }
        }
        return $registro;
    }

    public function modifica_bd(array $registro, int $id, bool $reactiva = false,
                                array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $registro = $this->campos_base(data:$registro, modelo: $this,id: $id);
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