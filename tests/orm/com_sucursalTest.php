<?php
namespace gamboamartin\comercial\test\orm;

use gamboamartin\comercial\models\com_sucursal;
use gamboamartin\comercial\test\base_test;
use gamboamartin\errores\errores;

use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;


class com_sucursalTest extends test {
    public errores $errores;
    private stdClass $paths_conf;
    public function __construct(?string $name = null)
    {
        parent::__construct($name);
        $this->errores = new errores();
    }

    /**
     */
    public function test_activa_bd(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $modelo = new com_sucursal($this->link);
        //$modelo = new liberator($modelo);


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



    public function test_alias(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $modelo = new com_sucursal($this->link);
        $modelo = new liberator($modelo);


        $data = array();
        $data['codigo'] = 'X';

        $resultado = $modelo->alias($data);
        $this->assertIsaRRAY($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("X", $resultado['codigo']);
        $this->assertEquals("X", $resultado['alias']);
        errores::$error = false;


    }

    public function test_alta_bd(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $modelo = new com_sucursal($this->link);
        //$modelo = new liberator($modelo);

        $del = (new base_test())->del_com_sucursal(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar', $del);
            print_r($error);
            exit;
        }

        $modelo->registro['com_cliente_id'] = 1;
        $modelo->registro['codigo'] = 1;
        $modelo->registro['numero_exterior'] = 1;
        $modelo->registro['com_tipo_sucursal_id'] = 1;
        $modelo->registro['dp_municipio_id'] = 230;
        $modelo->registro['cp'] = 230;
        $modelo->registro['colonia'] = 'col';
        $modelo->registro['calle'] = 'calle';

        $resultado = $modelo->alta_bd();
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("1 MOFY900516NL1 YADIRA MAGALY MONTAÑEZ FELIX", $resultado->registro_puro->descripcion);
        errores::$error = false;
    }
    public function test_codigo_bis(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $modelo = new com_sucursal($this->link);
        $modelo = new liberator($modelo);


        $com_cliente_rfc = 'd';
        $data = array();
        $data['codigo'] = 'P';

        $resultado = $modelo->codigo_bis($com_cliente_rfc, $data);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("Pd", $resultado['codigo_bis']);
        errores::$error = false;
    }

    public function test_descripcion(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $modelo = new com_sucursal($this->link);
        $modelo = new liberator($modelo);


        $com_cliente_razon_social = 'a';
        $com_cliente_rfc = 'v';
        $data = array();
        $data['codigo'] = 'ZZ';

        $resultado = $modelo->descripcion($com_cliente_razon_social, $com_cliente_rfc, $data);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("ZZ v a", $resultado['descripcion']);
        errores::$error = false;
    }

    public function test_descripcion_select_sc(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $modelo = new com_sucursal($this->link);
        $modelo = new liberator($modelo);


        $com_cliente_razon_social = 'd';
        $com_cliente_rfc = 'b';
        $data = array();
        $data['codigo'] = 'A';

        $resultado = $modelo->descripcion_select_sc($com_cliente_razon_social, $com_cliente_rfc, $data);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("A b d", $resultado['descripcion_select']);
        errores::$error = false;
    }

    public function test_ds(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
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

    public function test_init_base(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $modelo = new com_sucursal($this->link);
        $modelo = new liberator($modelo);

        $data = array();
        $data['com_cliente_id'] = 1;
        $data['codigo'] = 1;

        $resultado = $modelo->init_base($data);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1, $resultado['com_cliente_id']);
        $this->assertEquals(1, $resultado['codigo']);
        $this->assertEquals("1 MOFY900516NL1 YADIRA MAGALY MONTAÑEZ FELIX", $resultado['descripcion']);
        $this->assertEquals("1MOFY900516NL1", $resultado['codigo_bis']);
        $this->assertEquals("1 MOFY900516NL1 YADIRA MAGALY MONTAÑEZ FELIX", $resultado['descripcion_select']);
        $this->assertEquals(1, $resultado['alias']);
        errores::$error = false;
    }

    public function test_limpia_campos(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $modelo = new com_sucursal($this->link);
        $modelo = new liberator($modelo);

        $registro = array();
        $campos_limpiar = array();


        $resultado = $modelo->limpia_campos($registro, $campos_limpiar);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEmpty($resultado);
        errores::$error = false;

    }

    public function test_maqueta_data(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $modelo = new com_sucursal($this->link);
        //$modelo = new liberator($modelo);

        $codigo = '';
        $nombre_contacto = '';
        $com_cliente_id = -1;
        $telefono = '';
        $numero_exterior = '';
        $numero_interior = '';
        $resultado = $modelo->maqueta_data(calle: 'calle', codigo: $codigo, colonia: 'colonia', cp: 1,
            nombre_contacto: $nombre_contacto, com_cliente_id: $com_cliente_id, telefono: $telefono,
            dp_municipio_id: 230, numero_exterior: $numero_exterior,
            numero_interior: $numero_interior);

        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
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
        $modelo = new com_sucursal($this->link);
        //$modelo = new liberator($modelo);

        $alta = (new base_test())->alta_com_sucursal(link: $this->link,com_tipo_sucursal_id: 2);
        if(errores::$error){
            $error = (new errores())->error('Error al insertar', $alta);
            print_r($error);
            exit;
        }

        $registro = array();
        $id = 1;

        $registro['dp_calle_pertenece_id'] = 1;

        $resultado = $modelo->modifica_bd(registro: $registro,id:  $id);
        //print_r($resultado);exit;
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("230", $resultado->registro_actualizado->dp_municipio_id);

        errores::$error = false;

        $registro = array();
        $id = 1;

        $registro['dp_calle_pertenece_id'] = 2;
        $registro['com_tipo_sucursal_id'] = 1;

        $resultado = $modelo->modifica_bd(registro: $registro,id:  $id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("230", $resultado->registro_actualizado->dp_municipio_id);
        $this->assertEquals("1", $resultado->registro_actualizado->com_tipo_sucursal_id);


        errores::$error = false;

    }

    public function test_sucursales(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $modelo = new com_sucursal($this->link);
        //$modelo = new liberator($modelo);





        $com_cliente_id = 1;
        $resultado = $modelo->sucursales($com_cliente_id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1, $resultado->registros[0]['com_sucursal_id']);
        $this->assertEquals(1, $resultado->registros[0]['com_cliente_id']);



        errores::$error = false;
    }

    public function test_sucursales_by_tipo_cliente(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $modelo = new com_sucursal($this->link);
        //$modelo = new liberator($modelo);

        $com_tipo_cliente_id = 1;
        $resultado = $modelo->sucursales_by_tipo_cliente($com_tipo_cliente_id);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        errores::$error = false;
    }

    public function test_valida_base_sucursal(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $modelo = new com_sucursal($this->link);
        $modelo = new liberator($modelo);



        $registro = array();
        $registro['com_cliente_id'] = 1;
        $registro['codigo'] = 1;

        $resultado = $modelo->valida_base_sucursal($registro);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
    }

    public function test_valida_data_descripciones(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $modelo = new com_sucursal($this->link);
        $modelo = new liberator($modelo);


        $com_cliente_razon_social = 'a';
        $com_cliente_rfc = 'v';
        $data = array();
        $data['codigo'] = 'ZZ';

        $resultado = $modelo->valida_data_descripciones($com_cliente_razon_social, $com_cliente_rfc, $data);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
    }

}

