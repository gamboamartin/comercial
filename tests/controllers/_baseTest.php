<?php
namespace gamboamartin\comercial\test\controllers;

use gamboamartin\comercial\controllers\_base;
use gamboamartin\comercial\controllers\controlador_com_cliente;
use gamboamartin\comercial\controllers\controlador_com_sucursal;
use gamboamartin\comercial\models\com_cliente;
use gamboamartin\comercial\test\base_test;
use gamboamartin\errores\errores;
use gamboamartin\template_1\html;

use gamboamartin\test\liberator;
use gamboamartin\test\test;

use html\com_sucursal_html;

use stdClass;


class _baseTest extends test {
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

    public function test_keys_selects(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'adm_accion';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';
        $_GET['registro_id'] = '1';
        $base = new _base();
        //$ctl = new liberator($ctl);
        $keys_selects = array();
        $resultado = $base->keys_selects(keys_selects: $keys_selects);
        //print_r($resultado);exit;
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(6,$resultado['numero_interior']->cols);
        $this->assertEquals("Num Int",$resultado['numero_interior']->place_holder);
        $this->assertNotTrue($resultado['numero_interior']->required);

        $this->assertEquals(6,$resultado['numero_exterior']->cols);
        $this->assertEquals("Num Ext",$resultado['numero_exterior']->place_holder);
        $this->assertTrue($resultado['numero_exterior']->required);
        errores::$error = false;
    }





}

