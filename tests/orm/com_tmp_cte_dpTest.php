<?php
namespace gamboamartin\comercial\test\orm;

use gamboamartin\comercial\models\com_sucursal;
use gamboamartin\comercial\models\com_tipo_cambio;
use gamboamartin\comercial\models\com_tmp_cte_dp;
use gamboamartin\comercial\test\base_test;
use gamboamartin\errores\errores;

use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;


class com_tmp_cte_dpTest extends test {
    public errores $errores;
    private stdClass $paths_conf;
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->errores = new errores();
    }

    /**
     */
    public function test_upd_cliente(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';
        $modelo = new com_tmp_cte_dp($this->link);
        $modelo = new liberator($modelo);

        $tmp = new stdClass();
        $tmp->dp_calle_pertenece_id = 1;
        $tmp->com_cliente_id = 1;
        $resultado = $modelo->upd_cliente($tmp);

        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertIsString( $resultado->registro_actualizado->com_cliente_calle);

        errores::$error = false;


    }


}

