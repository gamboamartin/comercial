<?php
namespace gamboamartin\comercial\controllers;

use gamboamartin\direccion_postal\controllers\_init_dps;
use gamboamartin\errores\errores;
use gamboamartin\system\_ctl_base;

class _base_comercial extends _ctl_base{
    public function alta(bool $header, bool $ws = false): array|string
    {
        $urls_js = (new _init_dps())->init_js(controler: $this);

        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar url js',data:  $urls_js,header: $header,ws: $ws);
        }

        $r_alta =  parent::alta(header: false);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar template',data:  $r_alta, header: $header,ws:$ws);
        }

        $inputs = $this->genera_inputs(keys_selects:  $this->keys_selects);
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al generar inputs',data:  $inputs);
            print_r($error);
            die('Error');
        }


        return $r_alta;
    }
}
