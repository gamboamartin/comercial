<?php
namespace gamboamartin\comercial\test\orm;

use gamboamartin\comercial\models\com_agente;
use gamboamartin\comercial\models\com_sucursal;
use gamboamartin\comercial\models\com_tipo_cambio;
use gamboamartin\comercial\test\base_test;
use gamboamartin\errores\errores;

use gamboamartin\test\test;


use stdClass;


class com_agenteTest extends test {
    public errores $errores;
    private stdClass $paths_conf;
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->errores = new errores();
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


}

