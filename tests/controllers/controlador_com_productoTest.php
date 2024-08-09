<?php
namespace gamboamartin\comercial\test\controllers;

use gamboamartin\comercial\controllers\controlador_com_cliente;
use gamboamartin\comercial\controllers\controlador_com_producto;
use gamboamartin\comercial\controllers\controlador_com_sucursal;
use gamboamartin\comercial\models\com_cliente;
use gamboamartin\comercial\test\base_test;
use gamboamartin\errores\errores;
use gamboamartin\template_1\html;

use gamboamartin\test\liberator;
use gamboamartin\test\test;

use html\com_sucursal_html;

use stdClass;


class controlador_com_productoTest extends test {
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

    public function test_inicializa_propiedades(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'adm_accion';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';
        $_GET['registro_id'] = '1';
        $ctl = new controlador_com_producto(link: $this->link, paths_conf: $this->paths_conf);
        $ctl = new liberator($ctl);

        $resultado = $ctl->inicializa_propiedades();
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("SAT - Unidad", $resultado['cat_sat_unidad_id']->label);
        $this->assertEquals("Conf Impuestos", $resultado['cat_sat_conf_imps_id']->label);

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
        $ctl = new controlador_com_producto(link: $this->link, paths_conf: $this->paths_conf);
        //$ctl = new liberator($ctl);


        $resultado = $ctl->init_datatable();
        //print_r($resultado);exit;
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("Id", $resultado->columns['com_producto_id']['titulo']);
        $this->assertEquals("Código", $resultado->columns['com_producto_codigo']['titulo']);
        $this->assertEquals("Código SAT", $resultado->columns['cat_sat_cve_prod_codigo']['titulo']);
        $this->assertEquals("SAT Unidad", $resultado->columns['cat_sat_unidad_descripcion']['titulo']);
        $this->assertEquals("SAT ObjetoImp", $resultado->columns['cat_sat_obj_imp_descripcion']['titulo']);
        $this->assertEquals("Producto", $resultado->columns['com_producto_descripcion']['titulo']);


        errores::$error = false;


    }



}

