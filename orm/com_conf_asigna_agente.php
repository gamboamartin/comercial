<?php
namespace gamboamartin\comercial\models;
use base\orm\_modelo_parent;
use PDO;

/**
 * La clase 'com_conf_asigna_agente' extiende a la clase '_modelo_parent'.
 * Esta clase maneja la configuración de asignación de agentes en el sistema al dar de alta un prospecto.
 * El uso de esta entidad se define para insertar un com_rel_agente para la aplicacion de la seguridad
 *
 *
 *
 * @property PDO $link Es el enlace a la base de datos.
 * @property array $childrens Son las clases hijas de esta clase.
 * @property string $tabla Es el nombre de la tabla en la base de datos.
 * @property array $campos_obligatorios Son los campos que deben estar presentes en la tabla.
 * @property array $columnas Es un array que contiene las columnas de la tabla.
 * @property array $columnas_extra Son las columnas adicionales que no son necesarias para la tabla.
 *
 *
 */
class com_conf_asigna_agente extends _modelo_parent{
    public function __construct(PDO $link, array $childrens = array()){
        $tabla = 'com_conf_asigna_agente';
        $columnas = array($tabla=>false);
        $campos_obligatorios = array();
        $columnas_extra = array();


        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, childrens: $childrens);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Conf de asignacion de agentes';


    }

}