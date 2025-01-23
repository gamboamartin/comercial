<?php
namespace html;

use gamboamartin\comercial\models\com_contacto_user;
use gamboamartin\errores\errores;
use gamboamartin\system\html_controler;
use gamboamartin\template\directivas;
use PDO;
use stdClass;


class com_contacto_user_html extends html_controler {



    public function select_com_contacto_user_id(int $cols, bool $con_registros, int|null $id_selected, PDO $link,
                                            bool $required = false): array|string
    {
        if(is_null($id_selected)){
            $id_selected = -1;
        }
        $modelo = new com_contacto_user(link: $link);

        $select = $this->select_catalogo(cols:$cols,con_registros:$con_registros,id_selected:$id_selected,
            modelo: $modelo,label: 'Usuario de contacto', required: $required);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar select', data: $select);
        }
        return $select;
    }

}
