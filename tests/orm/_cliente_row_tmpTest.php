<?php
namespace gamboamartin\comercial\test\orm;

use gamboamartin\comercial\models\_cliente_row_tmp;
use gamboamartin\comercial\models\com_sucursal;
use gamboamartin\comercial\test\base_test;
use gamboamartin\errores\errores;

use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;


class _cliente_row_tmpTest extends test {
    public errores $errores;
    private stdClass $paths_conf;
    public function __construct(?string $name = null)
    {
        parent::__construct($name);
        $this->errores = new errores();
    }

    public function test_ajusta_colonia(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $obj = new _cliente_row_tmp();
        $obj = new liberator($obj);


        $row_tmp = array();
        $link = $this->link;
        $registro = array();
        $registro['dp_colonia_postal_id'] = 23;
        $resultado = $obj->ajusta_colonia($link, $registro, $row_tmp);
        //print_r($resultado);exit;
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("Residencial Revolución",$resultado['dp_colonia']);

        errores::$error = false;
    }

    public function test_asigna_colonia_pred(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $obj = new _cliente_row_tmp();
        $obj = new liberator($obj);



        $row_tmp = array();
        $link = $this->link;
        $dp_colonia_postal_id = 23;
        $resultado = $obj->asigna_colonia_pred($dp_colonia_postal_id, $link, $row_tmp);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("Residencial Revolución",$resultado['dp_colonia']);

        errores::$error = false;
    }

    public function test_ajusta_cp(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $obj = new _cliente_row_tmp();
        $obj = new liberator($obj);


        $registro = array();
        $link = $this->link;
        $row_tmp = array();
        $resultado = $obj->ajusta_cp($link, $registro, $row_tmp);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEmpty($resultado);

        errores::$error = false;
    }

    public function test_asigna_dp_colonia(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $obj = new _cliente_row_tmp();
        $obj = new liberator($obj);


        $dp_colonia_postal_id = 23;
        $link = $this->link;
        $row_tmp = array();
        $resultado = $obj->asigna_dp_colonia($dp_colonia_postal_id, $link, $row_tmp);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("Residencial Revolución",$resultado['dp_colonia']);

        errores::$error = false;
    }

    public function test_asigna_dp_colonia_tmp(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $obj = new _cliente_row_tmp();
        $obj = new liberator($obj);


        $dp_colonia_postal_id = 23;
        $link = $this->link;
        $row_tmp = array();
        $resultado = $obj->asigna_dp_colonia_tmp($dp_colonia_postal_id, $link, $row_tmp);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("Residencial Revolución",$resultado['dp_colonia']);

        errores::$error = false;

    }

    public function test_asigna_cp_pred(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $obj = new _cliente_row_tmp();
        $obj = new liberator($obj);


        $dp_cp_id = 1;
        $link = $this->link;
        $row_tmp = array();
        $resultado = $obj->asigna_cp_pred($dp_cp_id, $link, $row_tmp);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);


        errores::$error = false;
    }

    public function test_asigna_dp_cp(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $obj = new _cliente_row_tmp();
        $obj = new liberator($obj);


        $dp_cp_id = 1;
        $link = $this->link;
        $row_tmp = array();
        $resultado = $obj->asigna_dp_cp($dp_cp_id, $link, $row_tmp);
        //print_r($resultado);exit;
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);


        errores::$error = false;
    }

    public function test_asigna_dp_cp_tmp(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $obj = new _cliente_row_tmp();
        $obj = new liberator($obj);


        $dp_cp_id = 1;
        $link = $this->link;
        $row_tmp = array();
        $resultado = $obj->asigna_dp_cp_tmp($dp_cp_id, $link, $row_tmp);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);


        errores::$error = false;
    }

    public function test_asigna_row_tmp(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $obj = new _cliente_row_tmp();
        $obj = new liberator($obj);


        $registro = array();
        $resultado = $obj->asigna_row_tmp($registro);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEmpty($resultado->row_tmp);

        errores::$error = false;
    }

    public function test_colonia_tmp(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $obj = new _cliente_row_tmp();
        $obj = new liberator($obj);


        $row_tmp = array();
        $link = $this->link;
        $registro = array();
        $registro['dp_colonia_postal_id'] = '23';
        $resultado = $obj->colonia_tmp($link, $registro, $row_tmp);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("Residencial Revolución",$resultado['dp_colonia']);

        errores::$error = false;
    }

    public function test_cp_tmp(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $obj = new _cliente_row_tmp();
        $obj = new liberator($obj);


        $registro = array();
        $link = $this->link;
        $row_tmp = array();
        $registro['dp_cp_id'] = 1;
        $resultado = $obj->cp_tmp($link, $registro, $row_tmp);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);


        errores::$error = false;
    }

    /**
     */
    public function test_integra_row_upd(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $obj = new _cliente_row_tmp();
        $obj = new liberator($obj);


        $key = 'a';
        $registro = array();
        $row_tmp = array();
        $registro['a'] = ' s   ';
        $resultado = $obj->integra_row_upd($key, $registro, $row_tmp);

        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("s", $resultado['a']);

        errores::$error = false;

    }

    public function test_row_tmp(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $obj = new _cliente_row_tmp();
        //$obj = new liberator($obj);


        $link = $this->link;
        $registro = array();
        $registro['dp_estado'] = 'a';
        $resultado = $obj->row_tmp($link, $registro);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("a", $resultado->row_tmp['dp_estado']);

        errores::$error = false;
    }

    public function test_tmp_dom(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $obj = new _cliente_row_tmp();
        $obj = new liberator($obj);


        $row_tmp = array();
        $link = $this->link;
        $registro = array();
        $registro['dp_cp_id'] = '1';
        $resultado = $obj->tmp_dom($link, $registro, $row_tmp);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);


        errores::$error = false;
    }




}

