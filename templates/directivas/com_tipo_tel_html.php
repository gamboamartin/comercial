<?php
namespace html;

use gamboamartin\comercial\models\com_tipo_tel;
use gamboamartin\errores\errores;
use gamboamartin\system\html_controler;
use PDO;


class com_tipo_tel_html extends html_controler {


    public function select_com_tipo_tel_id(int $cols, bool $con_registros, int $id_selected, PDO $link): array|string
    {
        $modelo = new com_tipo_tel(link: $link);

        $select = $this->select_catalogo(cols:$cols,con_registros:$con_registros,id_selected:$id_selected,
            modelo: $modelo,label: 'Tipo Telefono');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar select', data: $select);
        }
        return $select;
    }

}
