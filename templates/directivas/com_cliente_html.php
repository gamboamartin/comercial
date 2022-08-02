<?php
namespace html;

use gamboamartin\errores\errores;
use gamboamartin\system\html_controler;
use models\com_cliente;
use models\com_tipo_cambio;
use PDO;


class com_cliente_html extends html_controler {


    public function select_com_cliente_id(int $cols, bool $con_registros, int $id_selected, PDO $link): array|string
    {
        $modelo = new com_cliente(link: $link);

        $select = $this->select_catalogo(cols:$cols,con_registros:$con_registros,id_selected:$id_selected,
            modelo: $modelo,label: 'Cliente');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar select', data: $select);
        }
        return $select;
    }

}
