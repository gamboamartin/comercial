<?php
namespace gamboamartin\comercial\test\controllers;

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


class controlador_com_clienteTest extends test {
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

    public function test_init_configuraciones(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'adm_accion';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';
        $_GET['registro_id'] = '1';
        $ctl = new controlador_com_cliente(link: $this->link, paths_conf: $this->paths_conf);
        $ctl = new liberator($ctl);


        $resultado = $ctl->init_configuraciones();
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        errores::$error = false;
    }

    public function test_init_controladores(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'adm_accion';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';
        $_GET['registro_id'] = '1';
        $ctl = new controlador_com_cliente(link: $this->link, paths_conf: $this->paths_conf);
        $ctl = new liberator($ctl);


        $resultado = $ctl->init_controladores($this->paths_conf);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        errores::$error = false;
    }


    public function test_init_datatable(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'adm_accion';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';
        $_GET['registro_id'] = '1';
        $ctl = new controlador_com_cliente(link: $this->link, paths_conf: $this->paths_conf);
        $ctl = new liberator($ctl);


        $resultado = $ctl->init_datatable();


        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("Id", $resultado->columns['com_cliente_id']['titulo']);
        $this->assertEquals("Código", $resultado->columns['com_cliente_codigo']['titulo']);
        $this->assertEquals("Razón Social", $resultado->columns['com_cliente_razon_social']['titulo']);
        $this->assertEquals("RFC", $resultado->columns['com_cliente_rfc']['titulo']);
        $this->assertEquals("Régimen Fiscal", $resultado->columns['cat_sat_regimen_fiscal_descripcion']['titulo']);
        $this->assertEquals("Sucursales", $resultado->columns['com_cliente_n_sucursales']['titulo']);
        $this->assertEquals("com_cliente.id", $resultado->filtro[0]);
        $this->assertEquals("com_cliente.codigo", $resultado->filtro[1]);
        $this->assertEquals("com_cliente.razon_social", $resultado->filtro[2]);
        $this->assertEquals("com_cliente.rfc", $resultado->filtro[3]);
        $this->assertEquals("cat_sat_regimen_fiscal.descripcion", $resultado->filtro[4]);

        errores::$error = false;



    }



}

