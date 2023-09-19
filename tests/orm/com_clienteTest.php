<?php
namespace gamboamartin\comercial\test\orm;

use gamboamartin\cat_sat\tests\base;
use gamboamartin\comercial\models\com_cliente;
use gamboamartin\comercial\test\base_test;
use gamboamartin\errores\errores;
use gamboamartin\template_1\html;

use gamboamartin\test\liberator;
use gamboamartin\test\test;

use html\com_sucursal_html;

use stdClass;


class com_clienteTest extends test {
    public errores $errores;
    private stdClass $paths_conf;
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->errores = new errores();
        $this->paths_conf = new stdClass();
        $this->paths_conf->generales = '/var/www/html/organigrama/config/generales.php';
        $this->paths_conf->database = '/var/www/html/organigrama/config/database.php';
        $this->paths_conf->views = '/var/www/html/organigrama/config/views.php';
    }

    public function test_com_sucursal_descripcion(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_GET['session_id'] = '1';
        $modelo = new com_cliente($this->link);
        $modelo = new liberator($modelo);

        $com_cliente = new stdClass();
        $sucursal = array();
        $sucursal['com_sucursal_codigo'] = 'A';
        $com_cliente->razon_social = 'B';
        $com_cliente->rfc = 'D';
        $resultado = $modelo->com_sucursal_descripcion($com_cliente, $sucursal);

        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("A D B", $resultado);

        errores::$error = false;

    }


    public function test_desactiva_bd(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $_GET['registro_id'] = '1';
        $modelo = new com_cliente($this->link);
        //$modelo = new liberator($modelo);

        $del = (new base_test())->del_com_cliente($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar', $del);
            print_r($error);
            exit;
        }

        $del = (new base_test())->del_com_tipo_producto($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar tipo producto', $del);
            print_r($error);
            exit;
        }

        $del = (new base_test())->del_com_producto($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar producto', $del);
            print_r($error);
            exit;
        }

        $del = (new base_test())->del_cat_sat_moneda($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar moneda', $del);
            print_r($error);
            exit;
        }
        $del = (new base_test())->del_cat_sat_metodo_pago($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar metodo pago', $del);
            print_r($error);
            exit;
        }
        $del = (new base_test())->del_cat_sat_conf_reg_tp($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar metodo pago', $del);
            print_r($error);
            exit;
        }

        $alta = (new base_test())->alta_cat_sat_conf_reg_tp($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al insertar', $alta);
            print_r($error);
            exit;
        }

        $alta = (new base_test())->alta_com_cliente($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al insertar', $alta);
            print_r($error);
            exit;
        }

        $modelo->registro_id = 1;

        $resultado = $modelo->desactiva_bd();
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("1", $resultado['registro_id']);

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
        $modelo = new com_cliente($this->link);
        $modelo = new liberator($modelo);

        $data = array();
        $data['razon_social'] = 'a';
        $data['rfc'] = 'c';
        $data['codigo'] = 'd';
        $keys_integra_ds = array('codigo','rfc', 'razon_social');
        $resultado = $modelo->descripcion_select(data: $data,keys_integra_ds: $keys_integra_ds);
        //print_r($resultado);exit;

        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("d C A", $resultado);

        errores::$error = false;


    }

    public function test_inicializa_foraneas(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';
        $modelo = new com_cliente($this->link);
        $modelo = new liberator($modelo);

        $del = (new base_test())->del_cat_sat_moneda(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar',data:  $del);
            print_r($error);
            exit;
        }
        $del = (new base_test())->del_cat_sat_metodo_pago(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar',data:  $del);
            print_r($error);
            exit;
        }


        $alta = (new base_test())->alta_cat_sat_moneda(link: $this->link, id: 161, predeterminado: 'activo');
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al insertar',data:  $alta);
            print_r($error);
            exit;
        }

        $alta = (new base_test())->alta_cat_sat_metodo_pago(link: $this->link, id: 1, predeterminado: 'activo');
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al insertar',data:  $alta);
            print_r($error);
            exit;
        }

        $data = array();
        $funcion_llamada = 'alta_bd';
        $resultado = $modelo->inicializa_foraneas($data, $funcion_llamada);

        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(161, $resultado['cat_sat_moneda_id']);


        errores::$error = false;
    }

    public function test_init_base(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_GET['session_id'] = '1';
        $modelo = new com_cliente($this->link);
        $modelo = new liberator($modelo);

        $data = array();
        $resultado = $modelo->init_base($data);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEmpty($resultado);

        errores::$error = false;
    }

    public function test_limpia_campos(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_GET['session_id'] = '1';
        $modelo = new com_cliente($this->link);
        $modelo = new liberator($modelo);

        $registro = array();
        $campos_limpiar = array('a');

        $resultado = $modelo->limpia_campos($registro, $campos_limpiar);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEmpty($resultado);

        errores::$error = false;
    }

    public function test_registro_cliente_upd(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $obj = new com_cliente(link: $this->link);
        $obj = new liberator($obj);


        $registro = array();

        $resultado = $obj->registro_cliente_upd($registro);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEmpty($resultado);

        errores::$error = false;
    }

}

