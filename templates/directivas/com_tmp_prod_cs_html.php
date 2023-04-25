<?php
namespace html;

use gamboamartin\comercial\models\com_tmp_prod_cs;
use gamboamartin\errores\errores;
use gamboamartin\system\html_controler;
use gamboamartin\template\directivas;
use PDO;
use stdClass;


class com_tmp_prod_cs_html extends html_controler {

    public function input_cat_sat_producto(int $cols, stdClass $row_upd, bool $value_vacio, bool $disabled = false,
                                string $place_holder = 'Producto SAT', bool $required = false): array|string
    {

        $valida = (new directivas(html: $this->html_base))->valida_cols(cols: $cols);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar cols', data: $valida);
        }

        $html =$this->directivas->input_text(disabled: $disabled, name: 'cat_sat_producto', place_holder: $place_holder,
            required: $required, row_upd: $row_upd, value_vacio: $value_vacio);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input', data: $html);
        }

        $div = $this->directivas->html->div_group(cols: $cols,html:  $html);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar div', data: $div);
        }

        return $div;
    }


    public function select_com_tmp_prod_cs_id(int $cols, bool $con_registros, int $id_selected, PDO $link): array|string
    {
        $modelo = new com_tmp_prod_cs(link: $link);

        $select = $this->select_catalogo(cols:$cols,con_registros:$con_registros,id_selected:$id_selected,
            modelo: $modelo,label: 'Temporal Producto');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar select', data: $select);
        }
        return $select;
    }

}
