<?php
namespace tests\links\secciones;

use gamboamartin\comercial\models\com_cliente;
use gamboamartin\comercial\models\com_producto;
use gamboamartin\comercial\models\com_prospecto;
use gamboamartin\comercial\test\base_test;
use gamboamartin\errores\errores;
use gamboamartin\template_1\html;

use gamboamartin\test\liberator;
use gamboamartin\test\test;

use html\com_sucursal_html;

use stdClass;


class com_prospectoTest extends test {
    public errores $errores;
    private stdClass $paths_conf;
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->errores = new errores();
    }

    public function test_alta_bd(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 1;
        $_GET['session_id'] = '1';
        $_GET['registro_id'] = '1';

        $del = (new base_test())->del_com_tipo_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al del tipo prospecto',data:  $del);
            print_r($error);
            exit;
        }

        $alta = (new base_test())->alta_com_tipo_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al insertar tipo prospecto',data:  $alta);
            print_r($error);
            exit;
        }

        $modelo = new com_prospecto($this->link);
        //$modelo = new liberator($modelo);

        $modelo->registro['nombre'] = 'A';
        $modelo->registro['apellido_paterno'] = 'A';
        $modelo->registro['com_tipo_prospecto_id'] = 1;
        $modelo->registro['com_agente_id'] = 1;

        $resultado = $modelo->alta_bd();
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('TIPO 1',$resultado->registro['com_tipo_prospecto_codigo']);



        errores::$error = false;
    }


}

