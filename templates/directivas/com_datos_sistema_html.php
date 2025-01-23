<?php
namespace html;
use gamboamartin\errores\errores;
use gamboamartin\comercial\models\com_datos_sistema;
use gamboamartin\system\html_controler;
use PDO;
use stdClass;

class com_datos_sistema_html extends html_controler {

    public function select_com_datos_sistema_id(int $cols, bool $con_registros, int $id_selected, PDO $link,
                                            bool $disabled = false, array $filtro = array()): array|string
    {
        $modelo = new com_datos_sistema(link: $link);

        $select = $this->select_catalogo(cols: $cols, con_registros: $con_registros, id_selected: $id_selected,
            modelo: $modelo, disabled: $disabled, filtro: $filtro, label: 'Datos Sistema', required: true);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al generar select', data: $select);
        }
        return $select;
    }


}
