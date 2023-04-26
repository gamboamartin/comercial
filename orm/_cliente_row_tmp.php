<?php
namespace gamboamartin\comercial\models;
use gamboamartin\direccion_postal\models\dp_colonia_postal;
use gamboamartin\direccion_postal\models\dp_cp;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class _cliente_row_tmp{

    private errores $error;
    public function __construct(){
        $this->error = new errores();
    }

    private function ajusta_colonia(PDO $link, array $registro, array $row_tmp){
        if (trim($registro['dp_colonia_postal_id']) !== '') {
            $row_tmp = $this->asigna_colonia_pred(dp_colonia_postal_id: $registro['dp_colonia_postal_id'], link: $link, row_tmp: $row_tmp);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al asignar cp', data: $row_tmp);
            }
        }
        return $row_tmp;
    }

    private function ajusta_cp(PDO $link, array $registro, array $row_tmp){
        if (trim($registro['dp_cp_id']) !== '') {
            $row_tmp = $this->asigna_cp_pred(dp_cp_id: $registro['dp_cp_id'], link: $link, row_tmp: $row_tmp);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al asignar cp', data: $row_tmp);
            }
        }
        return $row_tmp;
    }

    private function asigna_cp_pred(int $dp_cp_id, PDO $link, array $row_tmp){
        if ($dp_cp_id !== 11) {
            $row_tmp = $this->asigna_dp_cp(dp_cp_id: $dp_cp_id, link: $link, row_tmp: $row_tmp);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al asignar cp', data: $row_tmp);
            }
        }
        return $row_tmp;
    }

    private function asigna_dp_colonia(int $dp_colonia_postal_id, PDO $link, array $row_tmp){
        if (!isset($row_tmp['dp_colonia_postal']) || trim($row_tmp['dp_colonia_postal']) !== '') {
            $row_tmp = $this->asigna_dp_colonia_tmp(dp_colonia_postal_id: $dp_colonia_postal_id, link: $link, row_tmp: $row_tmp);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al asignar cp', data: $row_tmp);
            }
        }
        return $row_tmp;
    }

    private function asigna_colonia_pred(int $dp_colonia_postal_id, PDO $link, array $row_tmp){
        if ($dp_colonia_postal_id !== 105) {
            $row_tmp = $this->asigna_dp_colonia(dp_colonia_postal_id: $dp_colonia_postal_id, link: $link, row_tmp: $row_tmp);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al asignar cp', data: $row_tmp);
            }
        }
        return $row_tmp;
    }

    private function asigna_dp_colonia_tmp(int $dp_colonia_postal_id, PDO $link, array $row_tmp){
        $dp_colonia_postal = (new dp_colonia_postal(link: $link))->registro(registro_id: $dp_colonia_postal_id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener dp_colonia_postal', data: $dp_colonia_postal);
        }
        $row_tmp['dp_colonia'] = $dp_colonia_postal['dp_colonia_descripcion'];
        return $row_tmp;
    }

    private function asigna_dp_cp(int $dp_cp_id, PDO $link, array $row_tmp){
        if (!isset($row_tmp['dp_cp']) || trim($row_tmp['dp_cp']) !== '') {
            $row_tmp = $this->asigna_dp_cp_tmp(dp_cp_id: $dp_cp_id, link: $link, row_tmp: $row_tmp);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al asignar cp', data: $row_tmp);
            }
        }
        return $row_tmp;
    }

    private function asigna_dp_cp_tmp(int $dp_cp_id, PDO $link, array $row_tmp){
        $dp_cp = (new dp_cp(link: $link))->registro(registro_id: $dp_cp_id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener cp', data: $dp_cp);
        }
        $row_tmp['dp_cp'] = $dp_cp['dp_cp_codigo'];
        return $row_tmp;
    }
    private function asigna_row_tmp(array $registro): stdClass
    {
        $keys_tmp = array('dp_estado','dp_municipio','dp_cp','dp_colonia','dp_calle');
        $row_tmp = array();
        foreach ($keys_tmp as $key){
            if(isset($registro[$key])){
                $value = trim($registro[$key]);
                if($value !== ''){
                    $row_tmp[$key] = $value;
                }
                unset($registro[$key]);
            }
        }
        $data = new stdClass();
        $data->row_tmp = $row_tmp;
        $data->registro = $registro;
        return $data;
    }

    private function colonia_tmp(PDO $link, array $registro, array $row_tmp){
        if (isset($registro['dp_colonia_postal_id'])) {
            $row_tmp = $this->ajusta_colonia(link: $link, registro: $registro, row_tmp: $row_tmp);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al asignar cp', data: $row_tmp);
            }
        }
        return $row_tmp;
    }

    private function cp_tmp(PDO $link, array $registro, array $row_tmp){
        if (isset($registro['dp_cp_id'])) {
            $row_tmp = $this->ajusta_cp(link: $link, registro: $registro, row_tmp: $row_tmp);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al asignar cp', data: $row_tmp);
            }
        }
        return $row_tmp;
    }

    final public function row_tmp(PDO $link, array $registro){
        $data_row = $this->asigna_row_tmp(registro: $registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al asignar row', data: $data_row);
        }
        $registro = $data_row->registro;
        $row_tmp = $data_row->row_tmp;

        if(count($row_tmp) > 0) {
            $row_tmp = $this->tmp_dom(link: $link, registro: $registro, row_tmp: $row_tmp);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al asignar cp', data: $row_tmp);
            }
        }
        $data = new stdClass();
        $data->registro = $registro;
        $data->row_tmp = $row_tmp;
        return $data;
    }


    private function tmp_dom(PDO $link, array $registro, array $row_tmp){
        $row_tmp = $this->cp_tmp(link: $link, registro: $registro, row_tmp: $row_tmp);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al asignar cp', data: $row_tmp);
        }
        $row_tmp = $this->colonia_tmp(link: $link, registro: $registro, row_tmp: $row_tmp);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al asignar cp', data: $row_tmp);
        }
        return $row_tmp;
    }
}
