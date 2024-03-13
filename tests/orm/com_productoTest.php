<?php
namespace tests\links\secciones;

use gamboamartin\comercial\models\com_cliente;
use gamboamartin\comercial\models\com_producto;
use gamboamartin\comercial\test\base_test;
use gamboamartin\errores\errores;
use gamboamartin\template_1\html;

use gamboamartin\test\liberator;
use gamboamartin\test\test;

use html\com_sucursal_html;

use stdClass;


class com_productoTest extends test {
    public errores $errores;
    private stdClass $paths_conf;
    public function __construct(?string $name = null)
    {
        parent::__construct($name);
        $this->errores = new errores();
    }

    public function test_existe_cat_sat_cve_prod(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $_GET['registro_id'] = '1';
        $modelo = new com_producto($this->link);
        $modelo = new liberator($modelo);

        $del = (new base_test())->del_cat_sat_producto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar',data:  $del);
            print_r($error);
            exit;
        }

        $cat_sat_producto_codigo = '12345678';
        $resultado = $modelo->existe_cat_sat_cve_prod($cat_sat_producto_codigo);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertNotTrue($resultado);

        $alta = (new base_test())->alta_cat_sat_producto(link: $this->link, codigo: '12345678', id: 12345678);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al insertar',data:  $alta);
            print_r($error);
            exit;
        }

        $cat_sat_producto_codigo = '81112221';
        $resultado = $modelo->existe_cat_sat_cve_prod($cat_sat_producto_codigo);
       // print_r($resultado);exit;
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);

        errores::$error = false;
    }

    public function test_limpia_campos(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $_GET['registro_id'] = '1';
        $modelo = new com_producto($this->link);
        $modelo = new liberator($modelo);

        $registro = array('a'=>'b');
        $campos_limpiar = array('a');
        $resultado = $modelo->limpia_campos($registro, $campos_limpiar);

        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEmpty($resultado);
    }


    public function test_registro(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $_GET['registro_id'] = '1';
        $modelo = new com_producto($this->link);
        //$modelo = new liberator($modelo);


        $del = (new base_test())->del_com_producto($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar', $del);
            print_r($error);
            exit;
        }

        $alta = (new base_test())->alta_com_producto($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al insertar', $alta);
            print_r($error);
            exit;
        }


        $resultado = $modelo->registro(1);

        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("1", $resultado['com_producto_id']);

        errores::$error = false;


    }

    /**
     */
    public function test_descripcion_select(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_GET['session_id'] = '1';
        $modelo = new com_producto($this->link);
        $modelo = new liberator($modelo);

        $registro = array();
        $registro['razon_social'] = 'a';
        $registro['rfc'] = 'c';
        $registro['codigo'] = 'd';

        $keys_integra_ds = array('codigo','rfc','razon_social');
        $resultado = $modelo->descripcion_select(data: $registro, keys_integra_ds: $keys_integra_ds );

        //print_r($resultado);exit;
        $this->assertIsString($resultado);

        $this->assertNotTrue(errores::$error);
        $this->assertEquals("d C A", $resultado);

        errores::$error = false;


    }

}

