<?php

namespace gamboamartin\comercial\models;

use base\orm\_modelo_parent;
use base\orm\_modelo_parent_sin_codigo;
use gamboamartin\errores\errores;
use gamboamartin\validacion\validacion;
use PDO;
use stdClass;

class com_contacto extends _modelo_parent_sin_codigo
{

    public function __construct(PDO $link, array $childrens = array())
    {
        $tabla = 'com_contacto';
        $columnas = array($tabla => false, 'com_tipo_contacto' => $tabla, 'com_cliente' => $tabla);
        $campos_obligatorios = array();

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, childrens: $childrens);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Contactos';
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $validacion = $this->validacion(datos: $this->registro, registro_id: -1);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error de validacion', data: $validacion);
        }

        $this->registro = $this->inicializa_campos($this->registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al inicializar campo base', data: $this->registro);
        }

        $r_alta_bd = parent::alta_bd(keys_integra_ds: $keys_integra_ds);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al dar de alta correo', data: $r_alta_bd);
        }

        return $r_alta_bd;
    }

    protected function inicializa_campos(array $registros): array
    {
        $registros['codigo'] = $this->get_codigo_aleatorio();
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error generar codigo', data: $registros);
        }

        $registros['descripcion'] = $registros['nombre'] . ' ' . $registros['ap'];

        if (array_key_exists('am', $registros)) {
            $registros['descripcion'] .= ' ' . $registros['am'];
        }

        return $registros;
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

    public function validacion(array $datos, int $registro_id): array
    {
        if (array_key_exists('status', $datos)) {
            return $datos;
        }

        $validacion = (new validacion())->valida_correo(correo: $datos['correo']);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error de validacion', data: $validacion);
        }

        $validacion =(new validacion())->valida_numero_tel_mx(tel: $datos['telefono']);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error de validacion', data: $validacion);
        }

        $validacion =(new validacion())->valida_solo_texto(texto: $datos['nombre']);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error de validacion', data: $validacion);
        }

        $validacion =(new validacion())->valida_solo_texto(texto: $datos['ap']);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error de validacion', data: $validacion);
        }

        if (strlen($datos['nombre']) < 3) {
            $mensaje_error = sprintf("Error el campo nombre '%s' debe tener como minimo 3 caracteres",
                $datos['nombre']);
            return $this->error->error(mensaje: $mensaje_error, data: $datos);
        }

        if (strlen($datos['ap']) < 3) {
            $mensaje_error = sprintf("Error el campo apellido paterno '%s' debe tener como minimo 3 caracteres",
                $datos['ap']);
            return $this->error->error(mensaje: $mensaje_error, data: $datos);
        }

        return $datos;
    }





}