<?php
namespace gamboamartin\comercial\models;
use base\orm\modelo;
use gamboamartin\administrador\models\adm_seccion;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

/**
 * MIGRARA A SYSTEM
 */
class _exporta
{

    private errores $error;

    public function __construct()
    {
        $this->error = new errores();

    }

    private function celda_busqueda(array $campos_hd, array $letras, string $nombre_tabla_relacion): string
    {
        $celda_busqueda = 'A2';
        foreach ($campos_hd as $indice=>$campo_hd){
            if($campo_hd === $nombre_tabla_relacion.'_id'){
                $letra = $letras[$indice];
                $celda_busqueda = $letra.'2';
                break;
            }
        }
        return $celda_busqueda;

    }

    private function columnas_rows(string $tabla): array
    {
        return array($tabla.'_id',$tabla.'_codigo', $tabla.'_descripcion');

    }

    final public function data_hojas_xls(int $contador_hojas, stdClass $data_hojas, array $keys, string $nombre_tabla_relacion,
                                         array $registros): stdClass
    {

        if(isset($data_hojas->nombre_hojas)) {
            $nombre_hojas = $data_hojas->nombre_hojas;
        }
        if(isset($data_hojas->keys_hojas)) {
            $keys_hojas = $data_hojas->keys_hojas;
        }

        $nombre_hojas[$contador_hojas] = $nombre_tabla_relacion;

        $nombre_hojas = array_reverse($nombre_hojas);

        $keys_hojas[$nombre_tabla_relacion] = new stdClass();
        $keys_hojas[$nombre_tabla_relacion]->keys = $keys;
        $keys_hojas[$nombre_tabla_relacion]->registros = $registros;

        $data_hojas->nombre_hojas = $nombre_hojas;
        $data_hojas->keys_hojas = $keys_hojas;

        return $data_hojas;



    }

    final public function frm_vlookup(array $campos_hd, array $letras, string $nombre_tabla_relacion)
    {
        $celda_busqueda = $this->celda_busqueda(campos_hd: $campos_hd,letras:  $letras,nombre_tabla_relacion:  $nombre_tabla_relacion);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener celda_busqueda', data: $celda_busqueda);
        }

        $vlookup = (new _exporta())->vlookup(celda_busqueda: $celda_busqueda,nombre_tabla_relacion:  $nombre_tabla_relacion);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener vlookup', data: $vlookup);
        }
        return $vlookup;


    }

    final public function limpia_adm_campo(array $adm_campo): stdClass
    {
        $continue = false;
        $keys_limpia = array('id','usuario_alta_id','usuario_update_id','fecha_alta','fecha_update');
        if(in_array($adm_campo['adm_campo_descripcion'], $keys_limpia)) {
            $continue = true;
        }
        $data = new stdClass();
        $data->continue = $continue;
        $data->adm_campo = $adm_campo;

        return $data;

    }

    final public function nombre_tabla_relacion(array $adm_campo, stdClass $foraneas)
    {
        $campo_name = $adm_campo['adm_campo_descripcion'];
        $fk_info = $foraneas->$campo_name;
        return $fk_info->nombre_tabla_relacion;

    }

    private function registros_rel(modelo $modelo_relacion)
    {
        $columnas = $this->columnas_rows(tabla: $modelo_relacion->tabla);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener columnas', data: $columnas);
        }

        $registros = $modelo_relacion->registros(columnas: $columnas,columnas_en_bruto: true, order: array('id'=>'ASC'));
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener registros', data: $registros);
        }
        return $registros;

    }

    final public function rows_rel(PDO $link, string $nombre_tabla_relacion)
    {
        $modelo_relacion = (new adm_seccion(link: $link))->crea_modelo(adm_seccion_descricpion: $nombre_tabla_relacion);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener modelo_relacion', data: $modelo_relacion);
        }

        $registros = $this->registros_rel(modelo_relacion: $modelo_relacion);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener registros', data: $registros);
        }
        return $registros;

    }

    private function vlookup(string $celda_busqueda, string $nombre_tabla_relacion): string
    {
        return "VLOOKUP($celda_busqueda,$nombre_tabla_relacion.A:C,3,0)";

    }





}
