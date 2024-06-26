<?php

namespace gamboamartin\comercial\models;

use base\orm\_modelo_parent_sin_codigo;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class com_tipo_cliente extends _modelo_parent_sin_codigo
{
    public function __construct(PDO $link, array $childrens = array())
    {
        $tabla = 'com_tipo_cliente';
        $columnas = array($tabla => false);
        $campos_obligatorios = array();
        $childrens['com_cliente'] = "gamboamartin\comercial\models";

        $columnas_extra['com_tipo_cliente_n_clientes'] =
            "(SELECT COUNT(*) FROM com_cliente WHERE com_cliente.com_tipo_cliente_id = com_tipo_cliente.id)";

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, childrens: $childrens);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Tipo de cliente';


    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $validacion = $this->validacion(datos: $this->registro,registro_id: -1);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error de validacion', data: $validacion);
        }

        $alta = parent::alta_bd($keys_integra_ds); // TODO: Change the autogenerated stub
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error de validacion', data: $alta);
        }
        return $alta;
    }

    public function validacion(array $datos, int $registro_id): array|stdClass
    {
        if(array_key_exists('status', $datos)){
            return $datos;
        }

        if (strlen($datos['descripcion']) < 3) {
            $mensaje_error = sprintf("Error el tipo de cliente '%s' debe tener como minimo 3 caracteres",
                $datos['descripcion']);
            return $this->error->error(mensaje: $mensaje_error, data: $datos);
        }

        $filtro['com_tipo_cliente.descripcion'] = trim($datos['descripcion']);
        $filtro_extra[0]['com_tipo_cliente.id']['operador'] = '!=';
        $filtro_extra[0]['com_tipo_cliente.id']['comparacion'] = 'AND';
        $filtro_extra[0]['com_tipo_cliente.id']['valor'] = $registro_id;
        $existe = $this->filtro_and(filtro: $filtro, filtro_extra: $filtro_extra);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error de duplcacion', data: $existe);
        }

        if ($existe->n_registros > 0) {
            $mensaje_error = sprintf("Error el tipo de cliente '%s' ya existe", $datos['descripcion']);
            return $this->error->error(mensaje: $mensaje_error, data: $datos);
        }

        return $existe;
    }

    public function modifica_bd(array $registro, int $id, bool $reactiva = false,
                                array $keys_integra_ds = array('descripcion')): array|stdClass
    {
        $validacion = $this->validacion(datos: $registro, registro_id: $id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error de validacion', data: $validacion);
        }

        $modifica = parent::modifica_bd($registro, $id, $reactiva, $keys_integra_ds);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error de validacion', data: $validacion);

        }
        return $modifica;
    }


    public function clientes(int $com_tipo_cliente_id): array
    {
        if ($com_tipo_cliente_id <= 0) {
            return $this->error->error(mensaje: 'Error com_tipo_cliente_id debe ser mayor a 0', data: $com_tipo_cliente_id);
        }

        $filtro['com_tipo_cliente.id'] = $com_tipo_cliente_id;

        $data = (new com_cliente($this->link))->filtro_and(filtro: $filtro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener clientes', data: $data);
        }
        return $data->registros;
    }
}