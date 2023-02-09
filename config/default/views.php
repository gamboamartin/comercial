<?php
namespace config;

class views
{
    public int $reg_x_pagina = 15; //Registros a mostrar en listas
    public string $titulo_sistema = 'organigrama'; //Titulo de sistema
    public string $ruta_template_base = "/var/www/html/organigrama/vendor/gamboa.martin/template_1/";
    public string $ruta_templates = "/var/www/html/organigrama/vendor/gamboa.martin/template_1/template/";
    public string $url_assets = '';
    public array $subtitulos_menu = array();
    public function __construct(){
        $url = 'http://localhost/organigrama/';
        $this->url_assets = $url.'vendor/gamboa.martin/template_1/assets/';
    }
}