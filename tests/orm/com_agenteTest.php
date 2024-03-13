<?php
namespace gamboamartin\comercial\test\orm;

use gamboamartin\comercial\models\com_agente;
use gamboamartin\comercial\models\com_sucursal;
use gamboamartin\comercial\models\com_tipo_cambio;
use gamboamartin\comercial\test\base_test;
use gamboamartin\errores\errores;

use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;


class com_agenteTest extends test {
    public errores $errores;
    private stdClass $paths_conf;
    public function __construct(?string $name = null)
    {
        parent::__construct($name);
        $this->errores = new errores();
    }

    public function test_adm_usuario(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $modelo = new com_agente($this->link);
        $modelo = new liberator($modelo);

        /*
        $del = (new base_test())->del_com_agente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }


        $alta = (new base_test())->alta_com_agente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }*/

        $registro = array();
        $registro['user'] = 'TEST';
        $registro['password'] = 'TEST';
        $registro['email'] = 'a@a.com';
        $registro['telefono'] = '1234567890';
        $registro['adm_grupo_id'] = '2';
        $registro['nombre'] = 'TEST';
        $registro['apellido_paterno'] = 'TEST';
        $resultado = $modelo->adm_usuario($registro);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertIsNumeric( $resultado->registro_id);
        errores::$error = false;
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
        $modelo = new com_agente($this->link);
        //$modelo = new liberator($modelo);

        $del = (new base_test())->del_com_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }

        $del = (new base_test())->del_com_agente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }


        $alta = (new base_test())->alta_com_agente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }

        $resultado = $modelo->activa_bd(reactiva:false,registro_id: 1);

        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("1", $resultado->registro_id);

        errores::$error = false;


    }

    public function test_adm_usuario_ins(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $modelo = new com_agente($this->link);
        $modelo = new liberator($modelo);


        $registro = array();
        $registro['user'] = 'A';
        $registro['password'] = 'A';
        $registro['email'] = 'A';
        $registro['telefono'] = 'A';
        $registro['adm_grupo_id'] = 'A';
        $registro['nombre'] = 'A';
        $registro['apellido_paterno'] = 'A';
        $resultado = $modelo->adm_usuario_ins($registro);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertNotEmpty( $resultado);

        errores::$error = false;
    }

    public function test_com_agentes_session(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $modelo = new com_agente($this->link);
        //$modelo = new liberator($modelo);

        $del = (new base_test())->del_com_agente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }


        $alta = (new base_test())->alta_com_agente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }

        $resultado = $modelo->com_agentes_session();
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEmpty( $resultado);

        errores::$error = false;
    }

    public function test_descripcion(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $modelo = new com_agente($this->link);
        $modelo = new liberator($modelo);

        $registro = array();
        $registro['nombre'] = 'A';
        $registro['apellido_paterno'] = 'B';
        $registro['apellido_materno'] = '  C  ';
        $resultado = $modelo->descripcion($registro);
        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("A B C", $resultado);

        errores::$error = false;
    }

    public function test_inserta_adm_usuario(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $modelo = new com_agente($this->link);
        $modelo = new liberator($modelo);



        $registro = array();
        $registro['user'] = mt_rand(1,99999999);
        $registro['password'] = 'A';
        $registro['email'] = 'a@test.com';
        $registro['telefono'] = '1234567890';
        $registro['adm_grupo_id'] = '2';
        $registro['nombre'] = 'A';
        $registro['apellido_paterno'] = 'A';
        $resultado = $modelo->inserta_adm_usuario($registro);
        //print_r($resultado);exit;
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('A', $resultado->registro['adm_usuario_password']);

        errores::$error = false;
    }




}

