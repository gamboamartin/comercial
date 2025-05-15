<?php

namespace gamboamartin\comercial\models;

use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class com_cliente_cuenta extends _modelo_parent
{

    public function __construct(PDO $link, array $childrens = array())
    {
        $tabla = 'com_cliente_cuenta';
        $columnas = array($tabla => false, 'bn_banco' => $tabla, 'com_cliente' => $tabla);
        $campos_obligatorios = array();

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, childrens: $childrens);

        $this->NAMESPACE = __NAMESPACE__;
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $registro = $this->integra_base();
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al al integrar campos base', data: $registro);
        }

        $r_alta_bd = parent::alta_bd(keys_integra_ds: $keys_integra_ds);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al dar de alta correo', data: $r_alta_bd);
        }

        return $r_alta_bd;
    }

    private function integra_base(): array
    {
        if (!isset($this->registro['codigo'])) {

            $codigo = $this->get_codigo_aleatorio();
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al al integrar codigo', data: $codigo);
            }
            $this->registro['codigo'] = $codigo;
        }

        if (!isset($this->registro['descripcion'])) {
            $descripcion = $this->registro['codigo'];
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al al integrar descripcion', data: $descripcion);
            }
            $this->registro['descripcion'] = $descripcion;
        }


        return $this->registro;
    }


}