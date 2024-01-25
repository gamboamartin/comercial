<?php
namespace gamboamartin\comercial\models;
use gamboamartin\direccion_postal\models\dp_colonia_postal;
use gamboamartin\direccion_postal\models\dp_cp;
use gamboamartin\errores\errores;
use gamboamartin\validacion\validacion;
use PDO;
use stdClass;

class _cliente_row_tmp{

    private errores $error;
    public function __construct(){
        $this->error = new errores();
    }

    /**
     * Ajusta los datos de una colonia
     * @param PDO $link Conexion a la base de datos
     * @param array $registro Registro en proceso
     * @param array $row_tmp Registro temporal
     * @return array
     * @deprecated
     */
    private function ajusta_colonia(PDO $link, array $registro, array $row_tmp): array
    {
        if(!isset($registro['dp_colonia_postal_id'])){
            $registro['dp_colonia_postal_id'] = '';
        }
        if (trim($registro['dp_colonia_postal_id']) !== '') {
            if($registro['dp_colonia_postal_id'] <= 0){
                return $this->error->error(mensaje: 'Error $registro[dp_colonia_postal_id] debe ser mayor a 0',
                    data: $registro);
            }
            $row_tmp = $this->asigna_colonia_pred(dp_colonia_postal_id: $registro['dp_colonia_postal_id'],
                link: $link, row_tmp: $row_tmp);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al asignar cp', data: $row_tmp);
            }
        }
        return $row_tmp;
    }

    /**
     * Ajusta un codigo postal validando si es tmp
     * @param PDO $link Conexion a la base de datos
     * @param array $registro Registro en proceso
     * @param array $row_tmp Registro temporal
     * @return array
     * @deprecated
     */
    private function ajusta_cp(PDO $link, array $registro, array $row_tmp): array
    {

        if(!isset( $registro['dp_cp_id'])){
            $registro['dp_cp_id'] = '';
        }
        if (trim($registro['dp_cp_id']) !== '') {
            $row_tmp = $this->asigna_cp_pred(dp_cp_id: $registro['dp_cp_id'], link: $link, row_tmp: $row_tmp);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al asignar cp', data: $row_tmp);
            }
        }
        return $row_tmp;
    }

    /**
     * Asigna un cp predeterminado
     * @param int $dp_cp_id Identificador a asignar
     * @param PDO $link Conexion a la base de datos
     * @param array $row_tmp Registro temporal de asignacion
     * @return array
     * @deprecated
     */
    private function asigna_cp_pred(int $dp_cp_id, PDO $link, array $row_tmp): array
    {
        if($dp_cp_id <= 0){
            return $this->error->error(mensaje: 'Error dp_cp_id debe ser mayor a 0', data: $dp_cp_id);
        }
        if ($dp_cp_id !== 11) {
            $row_tmp = $this->asigna_dp_cp(dp_cp_id: $dp_cp_id, link: $link, row_tmp: $row_tmp);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al asignar cp', data: $row_tmp);
            }
        }
        return $row_tmp;
    }

    /**
     * Asigna la colonia temporal
     * @param int $dp_colonia_postal_id Colonia id
     * @param PDO $link Conexion a la base de datos
     * @param array $row_tmp Registro temporal de insersion
     * @return array
     * @deprecated
     */
    private function asigna_dp_colonia(int $dp_colonia_postal_id, PDO $link, array $row_tmp): array
    {
        if($dp_colonia_postal_id <= 0){
            return $this->error->error(mensaje: 'Error dp_colonia_postal_id es menor a 0', data: $dp_colonia_postal_id);
        }

        if (!isset($row_tmp['dp_colonia_postal']) || trim($row_tmp['dp_colonia_postal']) !== '') {
            $row_tmp = $this->asigna_dp_colonia_tmp(dp_colonia_postal_id: $dp_colonia_postal_id, link: $link,
                row_tmp: $row_tmp);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al asignar cp', data: $row_tmp);
            }
        }
        return $row_tmp;
    }

    /**
     * Asigna la colonia predeterminada
     * @param int $dp_colonia_postal_id id de colonia
     * @param PDO $link Conexion a la base de datos
     * @param array $row_tmp Registro temporal de insersion
     * @return array
     * @deprecated
     */
    private function asigna_colonia_pred(int $dp_colonia_postal_id, PDO $link, array $row_tmp): array
    {
        if($dp_colonia_postal_id <= 0){
            return $this->error->error(mensaje: 'Error dp_colonia_postal_id es menor a 0', data: $dp_colonia_postal_id);
        }
        if ($dp_colonia_postal_id !== 105) {
            $row_tmp = $this->asigna_dp_colonia(dp_colonia_postal_id: $dp_colonia_postal_id, link: $link,
                row_tmp: $row_tmp);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al asignar cp', data: $row_tmp);
            }
        }
        return $row_tmp;
    }

    /**
     * Asigna el id de la colonia temporal a ajustar
     * @param int $dp_colonia_postal_id Colonia id
     * @param PDO $link Conexion a la base de datos
     * @param array $row_tmp Registro temporal de asignacion
     * @return array
     * @deprecated
     */
    private function asigna_dp_colonia_tmp(int $dp_colonia_postal_id, PDO $link, array $row_tmp): array
    {
        if($dp_colonia_postal_id <= 0){
            return $this->error->error(mensaje: 'Error dp_colonia_postal_id es menor a 0', data: $dp_colonia_postal_id);
        }
        $dp_colonia_postal = (new dp_colonia_postal(link: $link))->registro(registro_id: $dp_colonia_postal_id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener dp_colonia_postal', data: $dp_colonia_postal);
        }
        $row_tmp['dp_colonia'] = $dp_colonia_postal['dp_colonia_descripcion'];
        return $row_tmp;
    }

    /**
     * Asigna un cp en caso de que este no exista
     * @param int $dp_cp_id Id a integrar
     * @param PDO $link Conexion a la base de datos
     * @param array $row_tmp Registro temporal de domicilios
     * @return array
     * @deprecated
     */
    private function asigna_dp_cp(int $dp_cp_id, PDO $link, array $row_tmp): array
    {
        if($dp_cp_id <= 0){
            return $this->error->error(mensaje: 'Error dp_cp_id debe ser mayor a 0', data: $dp_cp_id);
        }

        if (!isset($row_tmp['dp_cp']) || trim($row_tmp['dp_cp']) !== '') {
            $row_tmp = $this->asigna_dp_cp_tmp(dp_cp_id: $dp_cp_id, link: $link, row_tmp: $row_tmp);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al asignar cp', data: $row_tmp);
            }
        }
        return $row_tmp;
    }

    /**
     * Asigna un id de cp temporal
     * @param int $dp_cp_id Id a asignar
     * @param PDO $link Conexion a la base de datos
     * @param array $row_tmp Registro tmp
     * @return array
     * @deprecated
     */
    private function asigna_dp_cp_tmp(int $dp_cp_id, PDO $link, array $row_tmp): array
    {
        if($dp_cp_id <= 0){
            return $this->error->error(mensaje: 'Error dp_cp_id debe ser mayor a 0', data: $dp_cp_id);
        }
        $dp_cp = (new dp_cp(link: $link))->registro(registro_id: $dp_cp_id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener cp', data: $dp_cp);
        }
        $row_tmp['dp_cp'] = $dp_cp['dp_cp_codigo'];
        return $row_tmp;
    }

    /**
     * Limpia campos de un registro temporal
     * @param array $registro Registro en proceso
     * @return stdClass
     * @deprecated
     */
    private function asigna_row_tmp(array $registro): stdClass
    {
        $keys_tmp = array('dp_estado','dp_municipio','dp_cp','dp_colonia','dp_calle');
        $row_tmp = array();
        foreach ($keys_tmp as $key){
            if(isset($registro[$key])){
                $row_tmp = $this->integra_row_upd(key: $key,registro:  $registro,row_tmp:  $row_tmp);
                if (errores::$error) {
                    return $this->error->error(mensaje: 'Error al integrar row tmp', data: $row_tmp);
                }
                unset($registro[$key]);
            }
        }
        $data = new stdClass();
        $data->row_tmp = $row_tmp;
        $data->registro = $registro;
        return $data;
    }

    /**
     * Ajusta la colonia para un temporal
     * @param PDO $link Conexion a la base de datos
     * @param array $registro Registro en proceso
     * @param array $row_tmp Registro temporal
     * @return array
     * @deprecated
     */
    private function colonia_tmp(PDO $link, array $registro, array $row_tmp): array
    {
        if (isset($registro['dp_colonia_postal_id'])) {
            $row_tmp = $this->ajusta_colonia(link: $link, registro: $registro, row_tmp: $row_tmp);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al asignar cp', data: $row_tmp);
            }
        }
        return $row_tmp;
    }

    /**
     * Ajusta o integra un cp temporal
     * @param PDO $link Conexion a la base de datos
     * @param array $registro Registro en proceso
     * @param array $row_tmp Registro temporal
     * @return array
     * @deprecated
     */
    private function cp_tmp(PDO $link, array $registro, array $row_tmp): array
    {
        if (isset($registro['dp_cp_id'])) {
            $row_tmp = $this->ajusta_cp(link: $link, registro: $registro, row_tmp: $row_tmp);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al asignar cp', data: $row_tmp);
            }
        }
        return $row_tmp;
    }

    /**
     * Genera un registro temporal de domicilio
     * @param PDO $link Conexion a la base de datos
     * @param array $registro Registro en proceso
     * @return array|stdClass
     * @deprecated
     */
    final public function row_tmp(PDO $link, array $registro): array|stdClass
    {
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

    /**
     * Ajusta los campos de un registro en proceso de validacion
     * @param string $key Key del row
     * @param array $registro registro en proceso
     * @param array $row_tmp registro temporal
     * @return array
     * @deprecated
     */
    private function integra_row_upd(string $key, array $registro, array $row_tmp): array
    {
        $key = trim($key);
        if($key === ''){
            return $this->error->error(mensaje: 'Error key esta vacio', data: $key);
        }

        $keys[] = $key;
        $valida = (new validacion())->valida_existencia_keys(keys: $keys,registro:  $registro,valida_vacio: false);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar registro', data: $valida);
        }

        $value = trim($registro[$key]);
        if($value !== ''){
            $row_tmp[$key] = $value;
        }
        return $row_tmp;
    }


    /**
     * Integra lso elementos de un temporal de direcciones
     * @param PDO $link Conexion a la base de datos
     * @param array $registro Registro en proceso
     * @param array $row_tmp Registro temporal de direcciones
     * @return array
     * @deprecated
     */
    private function tmp_dom(PDO $link, array $registro, array $row_tmp): array
    {
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
