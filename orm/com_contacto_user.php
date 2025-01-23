<?php

namespace gamboamartin\comercial\models;

use base\orm\_modelo_parent_sin_codigo;
use gamboamartin\administrador\models\adm_usuario;
use gamboamartin\comercial\controllers\_pass;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class com_contacto_user extends _modelo_parent_sin_codigo
{

    public function __construct(PDO $link, array $childrens = array())
    {
        $tabla = 'com_contacto_user';
        $columnas = array($tabla => false, 'com_contacto' => $tabla, 'adm_usuario' => $tabla,
            'com_cliente'=>'com_contacto','adm_grupo'=>'adm_usuario');
        $campos_obligatorios = array('com_contacto_id','adm_usuario_id');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, childrens: $childrens);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Usuarios de cliente';
    }

    public function alta_bd(array $keys_integra_ds = array('descripcion')): array|stdClass
    {
        if(!isset($this->registro['descripcion'])){
            $com_contacto = (new com_contacto(link: $this->link))->registro(
                registro_id: $this->registro['com_contacto_id'], columnas_en_bruto: true, retorno_obj: true);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al obtener contacto',data:  $com_contacto);
            }

            $adm_usuario = (new adm_usuario(link: $this->link))->registro(
                registro_id: $this->registro['adm_usuario_id'], columnas_en_bruto: true, retorno_obj: true);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al obtener adm_usuario',data:  $adm_usuario);
            }

            $descripcion = $adm_usuario->user.' '.$com_contacto->nombre.' '.$com_contacto->ap;
            $this->registro['descripcion'] = $descripcion;

        }

        $r_alta_bd = parent::alta_bd();
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar usuario',data:  $r_alta_bd);
        }
        return $r_alta_bd;
    }


    final public function elimina_bd(int $id): array|stdClass
    {

        $row = $this->registro(registro_id: $id,columnas_en_bruto: true, retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener contacto_user',data:  $row);
        }

        $password = (new _pass())->password_df();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener pass',data:  $password);
        }

        $upd_user['password'] = $password;
        $upd_user['status'] = 'inactivo';
        $upd = (new adm_usuario(link: $this->link))->modifica_bd(registro: $upd_user,id: $row->adm_usuario_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al ajustar usuario',data:  $upd);
        }

        $r_elimina = parent::elimina_bd(id: $id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al elimina contacto_user',data:  $r_elimina);
        }
        return $r_elimina;

    }

    final public function modifica_bd(array $registro, int $id, bool $reactiva = false,
                                      array $keys_integra_ds = array('descripcion')): array|stdClass
    {

        $r_modifica = parent::modifica_bd(registro: $registro,id:  $id,reactiva:  $reactiva,
            keys_integra_ds:  $keys_integra_ds);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al modificar  datos',data:  $r_modifica);
        }

        if(isset($registro['password'])){
            $adm_usuario_upd['password'] = $registro['password'];
            $r_modifica_user = (new adm_usuario(link: $this->link))->modifica_bd(registro: $adm_usuario_upd,
                id: $this->row->adm_usuario_id);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al modificar  r_modifica_user',data:  $r_modifica_user);
            }
        }

        return $r_modifica;

    }



}