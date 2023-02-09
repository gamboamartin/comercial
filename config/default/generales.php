<?php
namespace config;
class generales{
    public bool $muestra_index = true;
    public string $path_base;
    public string $session_id = '';
    public string $sistema = 'organigrama';
    public string $url_base = 'http://localhost/organigrama/';
    public array $secciones = array("");
    public bool $encripta_md5 = false;
    public bool $aplica_seguridad = true;

    public int $tipo_sucursal_matriz_id = 1;
    public int $tipo_sucursal_base_id = 2;

    public function __construct(){
        //$this->path_base = getcwd();
        $this->path_base = '/var/www/html/organigrama/';
        if(isset($_GET['session_id'])){
            $this->session_id = $_GET['session_id'];
        }
    }
}