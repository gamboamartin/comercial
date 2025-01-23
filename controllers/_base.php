<?php
namespace gamboamartin\comercial\controllers;
use base\controller\init;
use gamboamartin\errores\errores;

class _base{

    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }

    /**
     * POR DOCUMENTAR EN WIKI
     * La función keys_selects se encarga de construir y maquetar los campos "numero_interior"
     * y "numero_exterior" en el array de selectores de claves $keys_selects.
     *
     * @param array $keys_selects El array que contiene los selectores de claves que se van a maquetar.
     *                             Este parámetro se pasa por referencia, lo que significa que cualquier
     *                             cambio realizado sobre este array se mantendrá fuera de la función.
     * @return array $keys_selects El array modificado con los selectores de claves maquetados para 'numero_interior' y 'numero_exterior'.
     *
     * @throws errores Retorna una excepción en caso de error al maquetar $keys_selects.
     *
     * @see init::key_select_txt() Para la creación y maquetación de los selectores de claves
     * @see errores::error() Para manejar los errores ocurridos durante la operación.
     * @version 24.12.0
     */
    public function keys_selects(array $keys_selects): array
    {
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'numero_interior',
            keys_selects:$keys_selects, place_holder: 'Num Int', required: false);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'numero_exterior',
            keys_selects:$keys_selects, place_holder: 'Num Ext');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        return $keys_selects;
    }
}
