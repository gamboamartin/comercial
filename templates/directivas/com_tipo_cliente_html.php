<?php
namespace html;

use gamboamartin\comercial\models\com_tipo_cliente;
use gamboamartin\errores\errores;
use gamboamartin\system\html_controler;
use PDO;


class com_tipo_cliente_html extends html_controler {


    public function select_com_tipo_cliente_id(int $cols, bool $con_registros, int $id_selected, PDO $link): array|string
    {
        $modelo = new com_tipo_cliente(link: $link);

        $select = $this->select_catalogo(cols:$cols,con_registros:$con_registros,id_selected:$id_selected,
            modelo: $modelo,label: 'Tipo cliente');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar select', data: $select);
        }
        return $select;
    }

}
