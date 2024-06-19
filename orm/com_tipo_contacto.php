<?php
namespace gamboamartin\comercial\models;
use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;

class com_tipo_contacto extends _modelo_parent{
    public function __construct(PDO $link, array $childrens = array()){
        $tabla = 'com_tipo_contacto';
        $columnas = array($tabla=>false);
        $campos_obligatorios = array();
        $childrens['com_tipo_contacto'] ="gamboamartin\comercial\models";

        $columnas_extra['com_tipo_contacto_n_contactos'] =
            "(SELECT COUNT(*) FROM com_contacto WHERE com_contacto.com_tipo_contacto_id = com_tipo_contacto.id)";

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, childrens: $childrens);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Tipo de contacto';

    }


    /**
     * Obtiene los productos de un tipo de producto
     * @param int $com_tipo_contacto_id identificador Tipo de producto
     * @return array
     * @version
     */
    public function contactos(int $com_tipo_contacto_id): array
    {
        if($com_tipo_contacto_id <= 0){
            return $this->error->error(mensaje: 'Error com_tipo_contacto_id debe ser mayor a 0',data:  $com_tipo_contacto_id);
        }

        $filtro['com_tipo_contacto.id'] = $com_tipo_contacto_id;

        $data = (new com_contacto($this->link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener contactos',data:  $data);
        }
        return $data->registros;
    }
}