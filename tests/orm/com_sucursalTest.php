<?php
namespace gamboamartin\comercial\test\orm;

use gamboamartin\comercial\models\com_sucursal;
use gamboamartin\comercial\test\base_test;
use gamboamartin\errores\errores;

use gamboamartin\test\test;


use stdClass;


class com_sucursalTest extends test {
    public errores $errores;
    private stdClass $paths_conf;
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->errores = new errores();
    }

    /**
     */
    public function test_activa_bd(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $modelo = new com_sucursal($this->link);
        //$modelo = new liberator($modelo);



        $del = (new base_test())->del_cat_sat_moneda($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar', $del);
            print_r($error);
            exit;
        }

        $del = (new base_test())->del_cat_sat_metodo_pago($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar', $del);
            print_r($error);
            exit;
        }

        $del = (new base_test())->del_cat_sat_moneda($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar', $del);
            print_r($error);
            exit;
        }

        $del = (new base_test())->del_com_cliente($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar', $del);
            print_r($error);
            exit;
        }

        $del = (new base_test())->del_com_sucursal($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar', $del);
            print_r($error);
            exit;
        }


        $alta = (new base_test())->alta_com_sucursal($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al insertar', $alta);
            print_r($error);
            exit;
        }

        $resultado = $modelo->activa_bd(reactiva:false,registro_id: 1);


        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("1", $resultado->registro_id);

        errores::$error = false;


    }


    public function test_ds(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $modelo = new com_sucursal($this->link);
        //$modelo = new liberator($modelo);


        $com_cliente_razon_social = 'c';
        $com_cliente_rfc = 'b';
        $data = array();
        $data['codigo']  ='x';
        $resultado = $modelo->ds($com_cliente_razon_social, $com_cliente_rfc, $data);
        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("x b c", $resultado);
        errores::$error = false;
    }

    public function test_modifica_bd(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';
        $modelo = new com_sucursal($this->link);
        //$modelo = new liberator($modelo);


        $registro = array();
        $id = 1;

        $registro['dp_calle_pertenece_id'] = 1;

        $resultado = $modelo->modifica_bd(registro: $registro,id:  $id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("1", $resultado->registro_actualizado->dp_calle_pertenece_id);

        errores::$error = false;

        $registro = array();
        $id = 1;

        $registro['dp_calle_pertenece_id'] = 2;
        $registro['com_tipo_sucursal_id'] = 9;

        $resultado = $modelo->modifica_bd(registro: $registro,id:  $id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("2", $resultado->registro_actualizado->dp_calle_pertenece_id);
        $this->assertEquals("9", $resultado->registro_actualizado->com_tipo_sucursal_id);


        errores::$error = false;

    }

    public function test_sucursales(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $modelo = new com_sucursal($this->link);
        //$modelo = new liberator($modelo);



        /*$del = (new base_test())->del_cat_sat_moneda($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar', $del);
            print_r($error);
            exit;
        }

        $del = (new base_test())->del_cat_sat_metodo_pago($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar', $del);
            print_r($error);
            exit;
        }

        $del = (new base_test())->del_cat_sat_moneda($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar', $del);
            print_r($error);
            exit;
        }

        $del = (new base_test())->del_com_cliente($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar', $del);
            print_r($error);
            exit;
        }

        $del = (new base_test())->del_com_sucursal($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar', $del);
            print_r($error);
            exit;
        }


        $alta = (new base_test())->alta_com_sucursal($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al insertar', $alta);
            print_r($error);
            exit;
        }*/

        $com_cliente_id = 1;
        $resultado = $modelo->sucursales($com_cliente_id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1, $resultado->registros[0]['com_sucursal_id']);
        $this->assertEquals(1, $resultado->registros[0]['com_cliente_id']);



        errores::$error = false;
    }

}

