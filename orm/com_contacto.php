<?php

namespace gamboamartin\comercial\models;

use base\orm\_modelo_parent;
use base\orm\_modelo_parent_sin_codigo;
use config\generales;
use gamboamartin\administrador\models\adm_usuario;
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

    private function adm_grupo_contacto_id(): int
    {
        $generales = new generales();
        $adm_grupo_id = -1;
        if(isset($generales->grupo_contacto_usuario_id)){
            $adm_grupo_id = $generales->grupo_contacto_usuario_id;
        }
        return $adm_grupo_id;

    }

    private function adm_user_ins(int $adm_grupo_id, stdClass $com_contacto): array
    {
        $adm_user_ins = array();
        $adm_user_ins['user'] = $_POST['user'];
        $adm_user_ins['password'] = $_POST['password'];
        $adm_user_ins['email'] = $com_contacto->correo;
        $adm_user_ins['telefono'] = $com_contacto->telefono;
        $adm_user_ins['nombre'] = $com_contacto->nombre;
        $adm_user_ins['ap'] = $com_contacto->ap;
        $adm_user_ins['am'] = $com_contacto->am;
        $adm_user_ins['adm_grupo_id'] = $adm_grupo_id;

        return $adm_user_ins;

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

    private function com_contacto_user_ins(int $adm_usuario_id, int $com_contacto_id): array
    {
        $com_contacto_user_ins['com_contacto_id'] = $com_contacto_id;
        $com_contacto_user_ins['adm_usuario_id'] = $adm_usuario_id;

        return $com_contacto_user_ins;

    }

    public function elimina_bd(int $id): array|stdClass
    {
        $filtro['com_contacto.id'] = $id;
        $del = (new com_contacto_user(link: $this->link))->elimina_con_filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar contactos', data: $del);
        }

        $r_elimina = parent::elimina_bd(id: $id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar contacto', data: $r_elimina);
        }

        return $r_elimina;

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

    private function inserta_adm_usuario(int $com_contacto_id)
    {
        $com_contacto = $this->registro(registro_id: $com_contacto_id, columnas_en_bruto: true, retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener contacto',data:  $com_contacto);
        }

        //print_r($com_contacto);exit;

        $adm_grupo_id = $this->adm_grupo_contacto_id();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener adm_grupo_id',data:  $adm_grupo_id);
        }


        $adm_user_ins = $this->adm_user_ins(adm_grupo_id: $adm_grupo_id, com_contacto: $com_contacto);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener adm_user_ins',data:  $adm_user_ins);
        }

        $r_adm_user = (new adm_usuario(link: $this->link))->alta_registro(registro: $adm_user_ins);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar usuario',data:  $r_adm_user);
        }
        return $r_adm_user;


    }

    final public function inserta_com_contacto_user(int $com_contacto_id)
    {
        $r_adm_user = $this->inserta_adm_usuario(com_contacto_id: $com_contacto_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar usuario',data:  $r_adm_user);
        }

        $com_contacto_user_ins = $this->com_contacto_user_ins(adm_usuario_id: $r_adm_user->registro_id, com_contacto_id: $com_contacto_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener com_contacto_user_ins',data:  $com_contacto_user_ins);
        }

        $r_com_contacto_user = (new com_contacto_user(link: $this->link))->alta_registro(registro: $com_contacto_user_ins);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar r_com_contacto_user',data:  $r_com_contacto_user);
        }
        return $r_com_contacto_user;

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