<?php
namespace gamboamartin\comercial\test\orm;

use gamboamartin\cat_sat\instalacion\instalacion;
use gamboamartin\cat_sat\models\cat_sat_moneda;
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
    public function __construct(?string $name = null)
    {
        parent::__construct($name);
        $this->errores = new errores();
        $this->paths_conf = new stdClass();
        $this->paths_conf->generales = '/var/www/html/organigrama/config/generales.php';
        $this->paths_conf->database = '/var/www/html/organigrama/config/database.php';
        $this->paths_conf->views = '/var/www/html/organigrama/config/views.php';
    }

    public function test_alta_bd(): void
    {
        unset($_SESSION['columnas']);
        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $init_cat_sat = (new instalacion(link: $this->link))->instala(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al init_cat_sat', $init_cat_sat);
            print_r($error);
            exit;
        }


        errores::$error = false;



        $del = (new base_test())->del_com_cliente($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al del', $del);
            print_r($error);
            exit;
        }

        $cat_sat_moneda_id = (new cat_sat_moneda(link: $this->link))->primer_id();
        if(errores::$error){
            $error = (new errores())->error('Error al obtener primer id', $cat_sat_moneda_id);
            print_r($error);
            exit;
        }

        $modelo = new com_cliente($this->link);
        //$modelo = new liberator($modelo);

        $modelo->registro['cat_sat_moneda_id'] = $cat_sat_moneda_id;
        $modelo->registro['cat_sat_metodo_pago_id'] = 1;
        $modelo->registro['cat_sat_forma_pago_id'] = 1;
        $modelo->registro['telefono'] = 1;
        $modelo->registro['numero_exterior'] = 1;
        $modelo->registro['cat_sat_regimen_fiscal_id'] = 601;
        $modelo->registro['cat_sat_tipo_persona_id'] = 4;
        $modelo->registro['razon_social'] = 1;
        $modelo->registro['rfc'] = 'AAA010101AAA';
        $modelo->registro['dp_municipio_id'] = '230';
        $modelo->registro['cp'] = '230';

        $resultado = $modelo->alta_bd();
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1, $resultado->registro['com_cliente_razon_social']);

        errores::$error = false;

        $alta = (new base_test())->del_com_cliente($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al insertar', $alta);
            print_r($error);
            exit;
        }

        $modelo = new com_cliente($this->link);
        //$modelo = new liberator($modelo);

        $modelo->registro['cat_sat_moneda_id'] = 161;
        $modelo->registro['cat_sat_metodo_pago_id'] = 1;
        $modelo->registro['cat_sat_forma_pago_id'] = 1;
        $modelo->registro['telefono'] = 1;
        $modelo->registro['numero_exterior'] = 1;
        $modelo->registro['cat_sat_regimen_fiscal_id'] = 601;
        $modelo->registro['cat_sat_tipo_persona_id'] = 4;
        $modelo->registro['razon_social'] = 1;
        $modelo->registro['rfc'] = 'AAA010101AAB';
        $modelo->registro['dp_pais_id'] = '151';
        $modelo->registro['dp_estado_id'] = '14';
        $modelo->registro['dp_municipio_id'] = '230';
        $modelo->registro['colonia'] = 'A';
        $modelo->registro['calle'] = 'A';
        $modelo->registro['cp'] = '1';
        $resultado = $modelo->alta_bd();
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('1', $resultado->registro['com_cliente_cp']);

        errores::$error = false;

    }
    public function test_com_sucursal_descripcion(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
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

    public function test_com_sucursal_upd(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_GET['session_id'] = '1';
        $modelo = new com_cliente($this->link);
        $modelo = new liberator($modelo);

        $com_cliente = new stdClass();
        $com_cliente->dp_calle_pertenece_id= 1;
        $com_cliente->numero_exterior= 1;
        $com_cliente->telefono= 1;
        $com_cliente_id = 1;
        $com_sucursal_descripcion = 'r';
        $sucursal = array();
        $sucursal['com_sucursal_codigo'] = 'R';
        $sucursal['com_tipo_sucursal_descripcion'] = 'o';

        $resultado = $modelo->com_sucursal_upd($com_cliente, $com_cliente_id, $com_sucursal_descripcion, $sucursal);

        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("R", $resultado['codigo']);
        $this->assertEquals("r", $resultado['descripcion']);
        $this->assertEquals("1", $resultado['com_cliente_id']);

        errores::$error = false;
    }

    public function test_com_sucursal_upd_dom(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_GET['session_id'] = '1';
        $modelo = new com_cliente($this->link);
        $modelo = new liberator($modelo);

        $com_cliente = new stdClass();
        $com_sucursal_upd = array();
        $com_cliente->dp_calle_pertenece_id = 1;
        $com_cliente->numero_exterior = 1;
        $com_cliente->telefono = 1;
        $com_cliente->pais = 1;
        $com_cliente->estado = 1;
        $com_cliente->municipio = 1;
        $com_cliente->colonia = 1;
        $com_cliente->calle = 1;
        $com_cliente->dp_municipio_id = 1;
        $com_cliente->cp = 1;
        $resultado = $modelo->com_sucursal_upd_dom($com_cliente, $com_sucursal_upd);
        //print_r($resultado);exit;
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("1", $resultado['numero_exterior']);
        $this->assertEquals("", $resultado['numero_interior']);
        $this->assertEquals("1", $resultado['telefono_1']);
        $this->assertEquals("1", $resultado['telefono_2']);
        $this->assertEquals("1", $resultado['telefono_3']);
        $this->assertEquals("1", $resultado['pais']);
        $this->assertEquals("1", $resultado['estado']);
        $this->assertEquals("1", $resultado['municipio']);
        $this->assertEquals("1", $resultado['colonia']);
        $this->assertEquals("1", $resultado['calle']);
        $this->assertEquals("1", $resultado['dp_municipio_id']);
        $this->assertEquals("1", $resultado['cp']);

        errores::$error = false;

    }

    public function test_desactiva_bd(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
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
        $_SESSION['grupo_id'] = 2;
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

    public function test_elimina_bd(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';
        $modelo = new com_cliente($this->link);
        //$modelo = new liberator($modelo);
        $del = (new base_test())->del_com_cliente($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al del', $del);
            print_r($error);
            exit;
        }

        $id = 1;
        $resultado = $modelo->elimina_bd($id);
        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error al eliminar", $resultado['mensaje_limpio']);

        errores::$error = false;

        $alta = (new base_test())->alta_com_cliente($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al alta', $alta);
            print_r($error);
            exit;
        }
        $id = 1;
        $resultado = $modelo->elimina_bd($id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("DELETE FROM com_cliente WHERE id = 1", $resultado->sql);


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



        $data = array();
        $funcion_llamada = 'alta_bd';
        $resultado = $modelo->inicializa_foraneas($data, $funcion_llamada);
        //print_r($resultado);exit;

        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);


        errores::$error = false;
    }

    public function test_init_base(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
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
        $_SESSION['grupo_id'] = 2;
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

    public function test_modifica_bd(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';
        $modelo = new com_cliente($this->link);
        //$modelo = new liberator($modelo);

        $del = (new base_test())->del_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al del',data:  $del);
            print_r($error);
            exit;
        }


        $alta = (new base_test())->alta_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al insertar',data:  $alta);
            print_r($error);
            exit;
        }

        $registro = array();
        $id = 1;
        $resultado = $modelo->modifica_bd($registro, $id);

        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);

        errores::$error = false;
    }

    public function test_registro(): void
    {
        unset($_SESSION['columnas']);
        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        errores::$error = false;


        $modelo = new com_cliente($this->link);

        $registro_id = 1;
        $resultado = $modelo->registro($registro_id);

        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1, $resultado['com_cliente_id']);

        errores::$error = false;

    }

    public function test_registro_cliente_upd(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
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

    public function test_row_com_sucursal_upd(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_GET['session_id'] = '1';
        $modelo = new com_cliente($this->link);
        $modelo = new liberator($modelo);

        $com_cliente = new stdClass();
        $com_cliente_id = 1;
        $sucursal = array();
        $sucursal['com_sucursal_codigo'] = 'Q';
        $sucursal['com_tipo_sucursal_descripcion'] = 'E';
        $com_cliente->razon_social = 'O';
        $com_cliente->rfc = 'T';
        $com_cliente->dp_calle_pertenece_id = '1';
        $com_cliente->numero_exterior = '1';
        $com_cliente->telefono = '1';


        $resultado = $modelo->row_com_sucursal_upd($com_cliente, $com_cliente_id, $sucursal);

        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('Q',$resultado['codigo']);
        $this->assertEquals('Q T O',$resultado['descripcion']);
        $this->assertEquals('1',$resultado['com_cliente_id']);

        errores::$error = false;
    }

    public function test_upd_sucursal(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';
        $modelo = new com_cliente($this->link);
        $modelo = new liberator($modelo);


        $alta = (new base_test())->alta_com_sucursal(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al insertar',data:  $alta);
            print_r($error);
            exit;
        }

        $com_cliente = new stdClass();
        $com_cliente_id = 1;
        $sucursal = array();
        $sucursal['com_sucursal_codigo'] = 'A';
        $sucursal['com_tipo_sucursal_descripcion'] = 'D';
        $sucursal['com_sucursal_id'] = '1';
        $com_cliente->razon_social = 'B';
        $com_cliente->rfc = 'C';
        $com_cliente->dp_calle_pertenece_id = '1';
        $com_cliente->numero_exterior = '1';
        $com_cliente->telefono = '1';
        $resultado = $modelo->upd_sucursal($com_cliente, $com_cliente_id, $sucursal);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('A C B', $resultado->registro_actualizado->com_sucursal_descripcion);
        errores::$error = false;

    }

    public function test_upd_sucursales(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';
        $modelo = new com_cliente($this->link);
        $modelo = new liberator($modelo);

        $com_cliente = new stdClass();
        $com_cliente->razon_social = 'A';
        $com_cliente->rfc = 'A';
        $com_cliente->dp_calle_pertenece_id = '1';
        $com_cliente->numero_exterior = '1';
        $com_cliente->telefono = '1';
        $com_cliente->pais = '1';
        $com_cliente->estado = '1';
        $com_cliente->municipio = '1';
        $com_cliente->colonia = '1';
        $com_cliente->calle = '1';
        $com_cliente->dp_municipio_id = '230';
        $com_cliente->cp = '230';
        $com_cliente_id = 1;
        $resultado = $modelo->upd_sucursales($com_cliente, $com_cliente_id);
        //print_r($resultado);exit;
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertIsString( $resultado[0]->registro_actualizado->com_sucursal_descripcion);
        errores::$error = false;
    }

    public function test_valida_data_sucursal(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_GET['session_id'] = '1';
        $modelo = new com_cliente($this->link);
        $modelo = new liberator($modelo);

        $com_cliente = new stdClass();
        $sucursal = array();
        $sucursal['com_sucursal_codigo'] = 'Q';
        $sucursal['com_tipo_sucursal_descripcion'] = 'E';
        $com_cliente->razon_social = 'O';
        $com_cliente->rfc = 'T';


        $resultado = $modelo->valida_data_sucursal($com_cliente, $sucursal);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);
        errores::$error = false;
    }

    public function test_valida_data_upd_sucursal(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';
        $modelo = new com_cliente($this->link);
        $modelo = new liberator($modelo);

        $com_cliente = new stdClass();
        $com_cliente_id = 1;
        $sucursal = array();
        $sucursal['com_sucursal_codigo'] = 'A';
        $sucursal['com_tipo_sucursal_descripcion'] = 'D';
        $sucursal['com_sucursal_id'] = '1';
        $com_cliente->razon_social = 'B';
        $com_cliente->rfc = 'C';
        $resultado = $modelo->valida_data_upd_sucursal($com_cliente, $com_cliente_id, $sucursal);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);
        errores::$error = false;

    }

}

