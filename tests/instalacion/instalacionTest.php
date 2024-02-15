<?php
namespace gamboamartin\comercial\test\instalacion;

use config\generales;
use gamboamartin\administrador\models\_instalacion;
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
    public function test__add_com_tipo_sucursal(): void
    {

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $drop = (new _instalacion(link: $this->link))->drop_table_segura(table: 'com_num_reg_fiscal');
        if(errores::$error){
            $error = (new errores())->error('Error al drop', $drop);
            print_r($error);
            exit;
        }


        $drop = (new _instalacion(link: $this->link))->drop_table_segura(table: 'com_sucursal');
        if(errores::$error){
            $error = (new errores())->error('Error al drop', $drop);
            print_r($error);
            exit;
        }

        $drop = (new _instalacion(link: $this->link))->drop_table_segura(table: 'com_tipo_sucursal');
        if(errores::$error){
            $error = (new errores())->error('Error al drop', $drop);
            print_r($error);
            exit;
        }

        $ins = new instalacion($this->link);
        $ins = new liberator($ins);


        $resultado = $ins->_add_com_tipo_sucursal(link: $this->link);
        //print_r($resultado);exit;

        $this->assertEquals("CREATE TABLE com_tipo_sucursal (
                    id bigint NOT NULL AUTO_INCREMENT,
                    codigo VARCHAR (255) NOT NULL , descripcion VARCHAR (255) NOT NULL , status VARCHAR (255) NOT NULL DEFAULT 'activo', usuario_alta_id INT (255) NOT NULL , usuario_update_id INT (255) NOT NULL , fecha_alta TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP, fecha_update TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, descripcion_select VARCHAR (255) NOT NULL , alias VARCHAR (255) NOT NULL , codigo_bis VARCHAR (255) NOT NULL , predeterminado VARCHAR (255) NOT NULL DEFAULT 'inactivo', 
                    PRIMARY KEY (id) 
                   
                    );",$resultado->create->data_sql->sql);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);

        errores::$error = false;

        $cat_sat = new \gamboamartin\cat_sat\instalacion\instalacion();

        $instala = $cat_sat->instala(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al instala', $instala);
            print_r($error);
            exit;
        }

        $instala = (new instalacion())->instala(link: $this->link);

        if(errores::$error){
            $error = (new errores())->error('Error al instala', $instala);
            print_r($error);
            exit;
        }
        errores::$error = false;


    }


    public function test_instala(): void
    {


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

