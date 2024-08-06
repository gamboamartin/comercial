<?php
namespace gamboamartin\comercial\controllers;

use gamboamartin\errores\errores;
use stdClass;

class _pass
{
    private errores $error;
    public function _construct()
    {
        $this->error = new errores();

    }
    private function caracteres_random(): stdClass
    {
        $caracteres = new stdClass();
        $caracteres->numeros = '0123456789';
        $caracteres->munisculas = 'abcdefghijklmnopqrstuvwxyz';
        $caracteres->mayusculas = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $caracteres->especiales = '+-.*';

        $full = implode(separator: '', array: (array)$caracteres);
        $caracteres->full = $full;

        return $caracteres;

    }

    final public function password_df(): array|string
    {
        $caracteres = $this->caracteres_random();
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener caracteres', data: $caracteres);
        }

        $password_df = $this->password_df_ini(caracteres: $caracteres,longitud_por_tipo:  1);
        if (errores::$error) {
            return $this->error->error(mensaje:'Error al obtener password_df', data: $caracteres);
        }

        $password_df = $this->password_df_var(caracteres: $caracteres, iteraciones: 1, longitud: 1, password_df: $password_df);
        if (errores::$error) {
            return $this->error->error(mensaje:'Error al obtener password_df', data: $caracteres);
        }
        return $password_df;

    }
    private function password_df_ini(stdClass $caracteres, int $longitud_por_tipo): string
    {
        $password_df = '';
        foreach ($caracteres as $cadena){
            $password_df .=   substr(str_shuffle($cadena), 0, $longitud_por_tipo);
        }
        return $password_df;

    }

    private function password_df_var(stdClass $caracteres, int $iteraciones, int $longitud, string $password_df): string
    {
        $contador = 0;
        $ini = '';
        $fin = '';
        while($contador <= $iteraciones) {
            $ini .= substr(str_shuffle($caracteres->full), 0, $longitud);
            $fin .= substr(str_shuffle($caracteres->full), 0, $longitud) ;
            $contador++;
        }

        return $ini.$password_df.$fin;

    }


}
