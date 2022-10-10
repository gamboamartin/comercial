<?php
namespace gamboamartin\comercial\test;
use base\orm\modelo_base;
use gamboamartin\comercial\models\com_sucursal;
use gamboamartin\errores\errores;
use PDO;

class base_test{


    public function alta_com_sucursal(PDO $link): array|\stdClass
    {

        $registro = array();
        $registro['id'] = 1;
        $registro['codigo'] = 1;
        $registro['descripcion'] = 1;
        $registro['com_cliente_id'] = 1;
        $registro['dp_calle_pertenece_id'] = 1;
        $registro['numero_exterior'] = 1;

        $alta = (new com_sucursal($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error('Error al dar de alta ', $alta);

        }
        return $alta;
    }



    public function del(PDO $link, string $name_model): array
    {
        $model = (new modelo_base($link))->genera_modelo(modelo: $name_model);
        $del = $model->elimina_todo();
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al eliminar '.$name_model, data: $del);
        }
        return $del;
    }

    public function del_com_sucursal(PDO $link): array
    {

        $del = $this->del($link, 'gamboamartin\\comercial\\models\\com_sucursal');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }



}
