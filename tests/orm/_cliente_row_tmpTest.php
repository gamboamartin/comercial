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
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->errores = new errores();
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



}

