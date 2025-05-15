<?php

namespace html;

use gamboamartin\errores\errores;
use gamboamartin\system\html_controler;
use gamboamartin\template\directivas;
use stdClass;

class com_cliente_cuenta_html extends html_controler
{

    public function input(int $cols, stdClass $row_upd, string $name, string $place_holder, bool $value_vacio,
                          bool $disabled = false): array|string
    {
        $valida = (new directivas(html: $this->html_base))->valida_cols(cols: $cols);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar cols', data: $valida);
        }

        $html = $this->directivas->input_text(disabled: $disabled, name: $name, place_holder: $place_holder, required: true,
            row_upd: $row_upd, value_vacio: $value_vacio);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al generar input', data: $html);
        }

        $div = $this->directivas->html->div_group(cols: $cols, html: $html);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al integrar div', data: $div);
        }

        return $div;
    }
}
