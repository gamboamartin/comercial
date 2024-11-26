<?php
namespace gamboamartin\comercial\models;
use base\orm\_modelo_parent;
use gamboamartin\cat_sat\models\cat_sat_conf_imps;
use gamboamartin\cat_sat\models\cat_sat_cve_prod;
use gamboamartin\cat_sat\models\cat_sat_obj_imp;
use gamboamartin\cat_sat\models\cat_sat_producto;
use gamboamartin\cat_sat\models\cat_sat_unidad;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class com_producto extends _modelo_parent {


    public function __construct(PDO $link){
        $tabla = 'com_producto';
        $columnas = array($tabla=>false,'cat_sat_obj_imp'=>$tabla, 'cat_sat_unidad'=>$tabla,
            'com_tipo_producto'=>$tabla,'cat_sat_conf_imps'=>$tabla, 'cat_sat_cve_prod'=>$tabla);

        $campos_obligatorios = array('cat_sat_unidad_id','cat_sat_obj_imp_id', 'com_tipo_producto_id',
            'cat_sat_conf_imps_id','cat_sat_cve_prod_id');


        $campos_view['cat_sat_unidad_id'] = array('type' => 'selects', 'model' => new cat_sat_unidad($link));
        $campos_view['cat_sat_obj_imp_id'] = array('type' => 'selects', 'model' => new cat_sat_obj_imp($link));
        $campos_view['cat_sat_conf_imps_id'] = array('type' => 'selects', 'model' => new cat_sat_conf_imps($link));
        $campos_view['com_tipo_producto_id'] = array('type' => 'selects', 'model' => new com_tipo_producto($link));
        $campos_view['codigo'] = array('type' => 'inputs');
        $campos_view['descripcion'] = array('type' => 'inputs');
        $campos_view['precio'] = array('type' => 'inputs');


        $atributos_criticos[] = 'es_automatico';
        $atributos_criticos[] = 'cat_sat_obj_imp_id';
        $atributos_criticos[] = 'cat_sat_unidad_id';
        $atributos_criticos[] = 'com_tipo_producto_id';
        $atributos_criticos[] = 'cat_sat_conf_imps_id';
        $atributos_criticos[] = 'precio';
        $atributos_criticos[] = 'codigo_sat';

        parent::__construct(link: $link,tabla:  $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, campos_view: $campos_view, atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Producto';
    }

    private function actualiza_producto_ejecucion(int $com_producto_id){
        $producto_ejecutado = $this->registro(registro_id: $com_producto_id, columnas_en_bruto: true, retorno_obj: true);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener producto',data:  $producto_ejecutado);
        }
        $upd = $this->ajusta_temporal(
            codigo_sat: $producto_ejecutado->codigo_sat,com_producto_id: $com_producto_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al actualizar',data:  $upd);
        }
        $upd->producto_ejecutado = $producto_ejecutado;
        return $upd;
    }

    private function actualiza_producto_sat(int $cat_sat_producto_id){
        $row_upd['cat_sat_producto_id'] = $cat_sat_producto_id;
        $upd = (new com_producto(link: $this->link))->modifica_bd(registro: $row_upd,id:  $this->registro_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al actualizar',data:  $upd);
        }
        return $upd;
    }


    private function ajusta_temporal(string $codigo_sat, int $com_producto_id){
        $upd = $this->modifica_cat_sat_producto(codigo_sat: $codigo_sat);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al actualizar',data:  $upd);
        }

        $del_tmp = $this->elimina_temporal(com_producto_id: $com_producto_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar temporal',data:  $del_tmp);
        }
        $data = new stdClass();
        $data->upd = $upd;
        $data->del_tmp = $del_tmp;
        return $data;
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {

        if(isset($this->registro['cat_sat_producto_radio'])){
            //$this->registro['cat_sat_producto'] = $this->registro['cat_sat_producto_radio'];
            //unset($this->registro['cat_sat_producto_radio']);
            $this->registro['cat_sat_cve_prod_id'] = $this->registro['cat_sat_producto_radio'];
        }


        
        if(isset($this->registro['cat_sat_producto'])){
            if(trim($this->registro['cat_sat_producto'] !=='')){

                $existe = $this->existe_cat_sat_cve_prod(cat_sat_cve_prod_codigo: $this->registro['cat_sat_producto']);
                if(errores::$error){
                    return $this->error->error(mensaje: 'Error al validar si existe producto',data: $existe);
                }
                if($existe){
                    $filtro['cat_sat_cve_prod.codigo'] = $this->registro['cat_sat_producto'];
                    $cat_sat_producto = $this->cat_sat_producto(filtro: $filtro);
                    if(errores::$error){
                        return $this->error->error(mensaje: 'Error al obtener cat_sat_producto',data: $cat_sat_producto);
                    }

                    $this->registro['cat_sat_producto_id'] = '97999999';
                    $this->registro['codigo_sat'] = $this->registro['cat_sat_producto'];
                    $this->registro['cat_sat_cve_prod_id'] = $cat_sat_producto['cat_sat_cve_prod_id'];

                }
                else{
                    return $this->error->error(mensaje: 'Error el producto no existe',data: $this->registro);
                }

                unset($this->registro['cat_sat_producto']);
            }

            $this->registro['cat_sat_producto_id'] = '97999999';

            $filtro = array();
            $filtro['cat_sat_producto.codigo'] = '97999999';
            $existe = (new cat_sat_producto(link: $this->link))->existe(filtro: $filtro);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al validar si existe',data: $existe);
            }
            if(!$existe){
                $cat_sat_producto_ins['id'] = '97999999';
                $cat_sat_producto_ins['descripcion'] = 'PREDETERMINADO';
                $cat_sat_producto_ins['codigo'] = '97999999';
                $cat_sat_producto_ins['cat_sat_clase_producto_id'] = '979999';

                $alta = (new cat_sat_producto(link: $this->link))->alta_registro(registro: $cat_sat_producto_ins);
                if(errores::$error){
                    return $this->error->error(mensaje: 'Error al insertar cat_sat_producto',data: $alta);
                }

            }

        }




        if(!isset($this->registro['cat_sat_conf_imps_id'])){
            $this->registro['cat_sat_conf_imps_id'] = 1;
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
        if(!isset($this->registro['precio'])){
            $this->registro['precio'] = 1;
        }

        $r_alta_bd =  parent::alta_bd();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar producto', data: $r_alta_bd);
        }


        return $r_alta_bd;
    }

    /**
     * Obtiene un producto del catalogo sat
     * @param array $filtro filtro para obtener producto
     * @return array
     */
    private function cat_sat_producto(array $filtro): array
    {
        $r_cat_sat_producto = (new cat_sat_cve_prod(link: $this->link))->filtro_and(filtro: $filtro);
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

    private function com_conf_precio(int $com_producto_id, int $com_tipo_cliente_id){
        $filtro['com_producto.id'] = $com_producto_id;
        $filtro['com_tipo_cliente.id'] = $com_tipo_cliente_id;
        $r_com_conf_precio = (new com_conf_precio(link: $this->link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener r_com_conf_precio',data:  $r_com_conf_precio);
        }
        if($r_com_conf_precio->n_registros > 1){
            return $this->error->error(mensaje: 'Error existe mas de un precio',data:  $r_com_conf_precio);
        }
        if($r_com_conf_precio->n_registros === 0){
            return $this->error->error(mensaje: 'Error no existe precio',data:  $r_com_conf_precio);
        }
        $precio = $r_com_conf_precio->registros[0]['com_conf_precio_precio'];
        return round($precio,2);
    }

    private function com_precio_cliente(int $com_cliente_id, int $com_producto_id){

        $com_precio_cliente_row = $this->com_precio_cliente_row(com_cliente_id: $com_cliente_id,
            com_producto_id:  $com_producto_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener com_precio_cliente',data:  $com_precio_cliente_row);
        }

        $precio = $com_precio_cliente_row['com_precio_cliente_precio'];
        return round($precio,2);
    }

    private function com_precio_cliente_row(int $com_cliente_id, int $com_producto_id){
        $filtro['com_cliente.id'] = $com_cliente_id;
        $filtro['com_producto.id'] = $com_producto_id;
        $r_com_precio_cliente = (new com_precio_cliente(link: $this->link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener r_com_precio_cliente',data:  $r_com_precio_cliente);
        }
        if($r_com_precio_cliente->n_registros > 1){
            return $this->error->error(mensaje: 'Error existe mas de un precio',data:  $r_com_precio_cliente);
        }
        if($r_com_precio_cliente->n_registros === 0){
            return $this->error->error(mensaje: 'Error no existe precio',data:  $r_com_precio_cliente);
        }
        return $r_com_precio_cliente->registros[0];
    }

    final public function ejecuta_upd_tmp(int $com_producto_id){

        $tmp_transaccion = new stdClass();
        $filtro['com_producto.id'] = $com_producto_id;

        $existe_tmp = (new com_tmp_prod_cs(link: $this->link))->existe(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar si existe',data:  $existe_tmp);
        }

        if($existe_tmp){

            $tmp_transaccion = $this->transacciona_tmp(com_producto_id: $com_producto_id);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al actualizar',data:  $tmp_transaccion);
            }

        }
        return $tmp_transaccion;
    }

    /**
     * Elimina un registro de tipo producto elimina sus dependientes com_precio_cliente
     * @param int $id Identificador del producto
     * @return array|stdClass
     */
    public function elimina_bd(int $id): array|stdClass
    {
        $filtro['com_producto.id'] = $id;
        $del = (new com_precio_cliente(link: $this->link))->elimina_con_filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar com_precio_cliente', data: $del);
        }

        $r_elimina_bd = parent::elimina_bd(id: $id); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar producto', data: $r_elimina_bd);
        }
        return $r_elimina_bd;
    }

    private function elimina_temporal(int $com_producto_id){

        $filtro['com_producto.id'] = $com_producto_id;
        $del_tmp = (new com_tmp_prod_cs(link: $this->link))->elimina_con_filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar temporal',data:  $del_tmp);
        }
        return $del_tmp;
    }

    /**
     * Verifica si existe un producto por el codigo
     * @param string $cat_sat_cve_prod_codigo
     * @return array|bool
     */
    private function existe_cat_sat_cve_prod(string $cat_sat_cve_prod_codigo): bool|array
    {
        $cat_sat_cve_prod_codigo = trim($cat_sat_cve_prod_codigo);
        if($cat_sat_cve_prod_codigo === ''){
            return $this->error->error(mensaje: 'Error cat_sat_cve_prod_codigo esta vacio',
                data: $cat_sat_cve_prod_codigo);
        }


        $filtro['cat_sat_cve_prod.codigo'] = $cat_sat_cve_prod_codigo;
        $existe = (new cat_sat_cve_prod(link: $this->link))->existe(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar si existe producto',data: $existe);
        }
        return $existe;
    }

    /**
     * Verifica si existe una configuracion de precio
     * @param int $com_producto_id
     * @param int $com_tipo_cliente_id
     * @return array|bool
     */
    private function existe_com_conf_precio(int $com_producto_id, int $com_tipo_cliente_id): bool|array
    {

        $filtro['com_tipo_cliente.id'] = $com_tipo_cliente_id;
        $filtro['com_producto.id'] = $com_producto_id;
        $existe = (new com_conf_precio(link: $this->link))->existe(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al verificar precio',data:  $existe);
        }
        return $existe;
    }

    /**
     * Valida si existe una configuracion de precio por cliente
     * @param int $com_cliente_id
     * @param int $com_producto_id
     * @return array|bool
     */
    private function existe_com_precio_cliente(int $com_cliente_id, int $com_producto_id): bool|array
    {
        $filtro['com_cliente.id'] = $com_cliente_id;
        $filtro['com_producto.id'] = $com_producto_id;

        $existe = (new com_precio_cliente(link: $this->link))->existe(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al verificar precio',data:  $existe);
        }
        return $existe;
    }

    /**
     * Valida si existe un producto de sat en una transaccion de ajuste
     * @param int $com_producto_id Producto a validar
     * @return array|bool
     */
    private function existe_producto_ejecucion(int $com_producto_id): bool|array
    {
        $producto_ejecutado = $this->registro(registro_id: $com_producto_id, columnas_en_bruto: true, retorno_obj: true);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener producto',data:  $producto_ejecutado);
        }

        $existe = $this->existe_cat_sat_cve_prod(cat_sat_cve_prod_codigo: $producto_ejecutado->codigo_sat);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar si existe producto',data:  $existe);
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


        if(isset($registro['codigo_sat'])){
            $registro['cat_sat_cve_prod_id'] = $registro['codigo_sat'];
        }

        //var_dump($registro);

        $r_modifica_bd = parent::modifica_bd(registro: $registro,id:  $id, reactiva: $reactiva);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar producto',data:  $r_modifica_bd);
        }

        //print_r($r_modifica_bd);exit;


        return $r_modifica_bd;
    }

    private function modifica_cat_sat_producto(string $codigo_sat){
        $cat_sat_producto = (new cat_sat_producto(link: $this->link))->registro_by_codigo(codigo: $codigo_sat);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar si existe producto',data:  $cat_sat_producto);
        }

        $upd = $this->actualiza_producto_sat(cat_sat_producto_id: $cat_sat_producto['cat_sat_producto_id']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al actualizar',data:  $upd);
        }
        return $upd;
    }

    /**
     * Obtiene el precio final del producto para el cliente asignado dado la siguiente prioridad primero precio por
     * cliente despues precio por configuracion de tipo de cliente y por ultimo el precio asignado al producto
     * @param int $com_cliente_id
     * @param int $com_producto_id
     * @return array|float
     */
    final public function precio(int $com_cliente_id, int $com_producto_id): float|array
    {

        $com_producto = $this->registro(registro_id: $com_producto_id, columnas_en_bruto: true, retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener producto',data:  $com_producto);
        }
        $com_cliente = (new com_cliente(link: $this->link))->registro(registro_id: $com_cliente_id,
            columnas_en_bruto: true,retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener cliente',data:  $com_cliente);
        }

        $precio = $this->precio_final(com_cliente: $com_cliente,com_producto:  $com_producto);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener precio',data:  $precio);
        }


        return round($precio,2);
    }

    private function precio_cliente(int $com_cliente_id, int $com_producto_id, float $precio){
        $existe = $this->existe_com_precio_cliente(com_cliente_id: $com_cliente_id,com_producto_id:  $com_producto_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al verificar precio',data:  $existe);
        }
        if($existe){
            $precio = $this->com_precio_cliente(com_cliente_id: $com_cliente_id,com_producto_id:  $com_producto_id);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener precio',data:  $precio);
            }
        }
        return $precio;
    }

    final public function precio_cliente_row(int $com_cliente_id, int $com_producto_id){
        $precio_cliente_row = array();
        $existe = $this->existe_com_precio_cliente(com_cliente_id: $com_cliente_id,com_producto_id:  $com_producto_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al verificar precio',data:  $existe);
        }
        if($existe){
            $precio_cliente_row = $this->com_precio_cliente_row(com_cliente_id: $com_cliente_id,
                com_producto_id:  $com_producto_id);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener precio',data:  $precio_cliente_row);
            }
        }
        return $precio_cliente_row;
    }

    private function precio_final(stdClass $com_cliente, stdClass $com_producto){
        $precio = $com_producto->precio;

        $precio = $this->precio_tipo_cliente(com_producto_id: $com_producto->id,
            com_tipo_cliente_id:  $com_cliente->com_tipo_cliente_id,precio:  $precio);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener precio',data:  $precio);
        }

        $precio = $this->precio_cliente(com_cliente_id: $com_cliente->id,com_producto_id:  $com_producto->id, precio: $precio);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener precio',data:  $precio);
        }
        return $precio;
    }

    private function precio_tipo_cliente(int $com_producto_id, int $com_tipo_cliente_id, float $precio){
        $existe = $this->existe_com_conf_precio(com_producto_id: $com_producto_id,com_tipo_cliente_id:  $com_tipo_cliente_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al verificar precio',data:  $existe);
        }
        if($existe){
            $precio = $this->com_conf_precio(com_producto_id: $com_producto_id, com_tipo_cliente_id: $com_tipo_cliente_id);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener precio',data:  $precio);
            }
        }
        return $precio;
    }

    private function transacciona_tmp(int $com_producto_id){
        $upd = new stdClass();
        $existe = $this->existe_producto_ejecucion(com_producto_id: $com_producto_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar si existe producto',data:  $existe);
        }

        if($existe){
            $upd = $this->actualiza_producto_ejecucion(com_producto_id: $com_producto_id);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al actualizar',data:  $upd);
            }
        }
        return $upd;
    }
}