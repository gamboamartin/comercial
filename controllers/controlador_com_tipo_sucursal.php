<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\comercial\controllers;

use gamboamartin\comercial\models\com_tipo_sucursal;
use gamboamartin\errores\errores;
use gamboamartin\system\links_menu;
use gamboamartin\system\system;
use gamboamartin\template\html;
use html\com_tipo_sucursal_html;
use PDO;
use stdClass;

class controlador_com_tipo_sucursal extends system {

    public function __construct(PDO $link, html $html = new \gamboamartin\template_1\html(), stdClass $paths_conf = new stdClass()){
        $modelo = new com_tipo_sucursal(link: $link);

        $html_ = new com_tipo_sucursal_html(html: $html);
        $obj_link = new links_menu(link: $link,registro_id: $this->registro_id);
        parent::__construct(html:$html_, link: $link,modelo:  $modelo, obj_link: $obj_link, paths_conf: $paths_conf);

        $this->titulo_lista = 'Tipos de sucursal';

    }

    public function alta(bool $header, bool $ws = false): array|string
    {
        $r_alta = parent::alta(header: false); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar template',data:  $r_alta, header: $header,ws:$ws);
        }

        return $r_alta;
    }

    public function modifica(bool $header, bool $ws = false, string $breadcrumbs = '', bool $aplica_form = true,
                             bool $muestra_btn = true): stdClass|array
    {
        $r_modifica = parent::modifica(header: false); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar template',data:  $r_modifica, header: $header,ws:$ws);
        }

        return $r_modifica;
    }


}