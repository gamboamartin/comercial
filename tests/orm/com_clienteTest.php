<?php
namespace tests\links\secciones;

use gamboamartin\comercial\models\com_cliente;
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

        $registro = array();
        $registro['razon_social'] = 'a';
        $registro['rfc'] = 'c';
        $registro['codigo'] = 'd';
        $resultado = $modelo->descripcion_select($registro);


        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("d c a", $resultado['descripcion_select']);

        errores::$error = false;


    }

}

