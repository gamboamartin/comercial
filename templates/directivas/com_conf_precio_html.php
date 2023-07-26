<?php
namespace html;

use gamboamartin\comercial\models\com_conf_precio;
use gamboamartin\errores\errores;
use gamboamartin\system\html_controler;
use PDO;



class com_conf_precio_html extends html_controler {


    public function select_com_conf_precio_id(int $cols, bool $con_registros, int $id_selected, PDO $link,
                                            bool $required = false): array|string
    {
        $modelo = new com_conf_precio(link: $link);

        $select = $this->select_catalogo(cols:$cols,con_registros:$con_registros,id_selected:$id_selected,
            modelo: $modelo,label: 'Precio', required: $required);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar select', data: $select);
        }
        return $select;
    }

}
