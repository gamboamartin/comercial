<?php
namespace gamboamartin\comercial\controllers;
use base\controller\init;
use gamboamartin\errores\errores;

class _base{

    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }

    public function keys_selects(array $keys_selects){
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
