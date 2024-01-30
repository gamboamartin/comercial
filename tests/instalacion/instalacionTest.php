<?php
namespace gamboamartin\comercial\test\instalacion;

use config\generales;
use gamboamartin\comercial\instalacion\instalacion;
use gamboamartin\comercial\models\com_agente;
use gamboamartin\comercial\models\com_sucursal;
use gamboamartin\comercial\models\com_tipo_cambio;
use gamboamartin\comercial\test\base_test;
use gamboamartin\errores\errores;

use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;


class instalacionTest extends test {
    public errores $errores;
    private stdClass $paths_conf;
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->errores = new errores();
    }

    public function test_instala(): void
    {

        $del = (new base_test())->del_cat_sat_metodo_pago(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al del', $del);
            print_r($error);
            exit;
        }

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $init_cat_sat = (new \gamboamartin\cat_sat\instalacion\instalacion())->instala(link: $this->link);

        if(errores::$error){
            $error = (new errores())->error('Error al del', $init_cat_sat);
            print_r($error);
            exit;
        }
        errores::$error = false;


        $ins = new instalacion($this->link);
        //$modelo = new liberator($modelo);


        $resultado = $ins->instala(link: $this->link);

        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);

        errores::$error = false;
    }


}

