<?php
namespace gamboamartin\comercial\models;
use base\orm\_modelo_parent;
use base\orm\_modelo_parent_sin_codigo;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class com_cliente_documento extends _modelo_parent_sin_codigo {
    public function __construct(PDO $link, array $childrens = array()){
        $tabla = 'com_cliente_documento';
        $columnas = array($tabla=>false,  'com_cliente'=>$tabla, 'doc_documento' => $tabla,
            'doc_tipo_documento' => 'doc_documento', 'doc_extension' => 'doc_documento');
        $campos_obligatorios = array('doc_documento_id','com_cliente_id');

        $columnas_extra = array();

        $atributos_criticos =  array('doc_documento_id','com_cliente_id');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, childrens: $childrens,
            atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Documento Cliente';


    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $validaciones = $this->validaciones($this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error en validaciones', data: $validaciones);
        }

        $this->registro = $this->inicializa_campos($this->registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al inicializar campo base', data: $this->registro);
        }

        $r_alta_bd = parent::alta_bd(keys_integra_ds: $keys_integra_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar com_rel_agente', data: $r_alta_bd );
        }

        return $r_alta_bd;
    }

    final public function documentos(int $com_cliente, array $tipos_documentos)
    {
        $in = array();

        if (count($tipos_documentos) > 0) {
            $in['llave'] = 'doc_documento.doc_tipo_documento_id';
            $in['values'] = $tipos_documentos;
        }

        $documentos = $this->filtro_and(filtro: array('com_cliente.id' => $com_cliente), in: $in);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener documentos', data: $documentos);
        }

        return $documentos->registros;
    }

    protected function validaciones(array $registros): array
    {
        $filtro['doc_tipo_documento_id'] = $registros['doc_tipo_documento_id'];
        $filtro['com_cliente_id'] = $registros['com_cliente_id'];
        $existe = (new com_conf_tipo_doc_cliente(link: $this->link))->existe(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al verificar si existe', data: $filtro);
        }

        if(!$existe){
            return $this->error->error(mensaje: "No existe una configuración para el cliente y el tipo de documento",
                data: $filtro);
        }

        return $registros;
    }

    protected function inicializa_campos(array $registros): array
    {
        $registros['codigo'] = $this->get_codigo_aleatorio();
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error generar codigo', data: $registros);
        }

        if(!isset($registros['descripcion'])){
            $descripcion = trim($registros['doc_tipo_documento_id']);
            $descripcion .= '-'.trim($registros['com_cliente_id']);
            $descripcion .= '-'.trim($registros['codigo']);
            $registros['descripcion'] = $descripcion;
        }

        return $registros;
    }

}