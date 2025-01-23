<?php
namespace html;

use gamboamartin\comercial\models\com_tipo_contacto;
use gamboamartin\errores\errores;
use gamboamartin\system\html_controler;
use PDO;


class com_tipo_contacto_html extends html_controler {


    public function select_com_tipo_contacto_id(int $cols, bool $con_registros, int $id_selected, PDO $link, bool $disabled = false): array|string
    {
        $modelo = new com_tipo_contacto(link: $link);

        $select = $this->select_catalogo(cols: $cols, con_registros: $con_registros, id_selected: $id_selected,
            modelo: $modelo, disabled: $disabled, label: 'Tipo Contacto');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar select', data: $select);
        }
        return $select;
    }

}
