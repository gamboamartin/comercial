<?php
namespace gamboamartin\comercial\models;
use base\orm\_modelo_parent;
use gamboamartin\administrador\models\adm_usuario;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class com_agente extends _modelo_parent{
    public function __construct(PDO $link, array $childrens = array()){
        $tabla = 'com_agente';
        $columnas = array($tabla=>false,'com_tipo_agente'=>$tabla,'adm_usuario'=>$tabla);
        $campos_obligatorios = array('adm_usuario_id','com_tipo_agente_id');
        $childrens['com_prospecto'] ="gamboamartin\comercial\models";

        $columnas_extra['com_agente_n_prospectos'] =
            "(SELECT COUNT(*) FROM com_prospecto WHERE com_prospecto.com_agente_id = com_agente.id)";

        $atributos_criticos[] = 'adm_usuario_id';
        $atributos_criticos[] = 'com_tipo_agente_id';


        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, childrens: $childrens,
            atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Agentes';


    }

    /**
     * Inserta u obtiene un usuario
     * @param array $registro Registro en proceso
     * @return array|stdClass
     */
    private function adm_usuario(array $registro): array|stdClass
    {

        $keys = array('user','password','email','telefono','adm_grupo_id','nombre','apellido_paterno');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys, registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al valida ',data:  $valida);
        }

        $filtro['adm_usuario.user'] = $registro['user'];
        $r_adm_usuario_fil = (new adm_usuario(link: $this->link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener usuario',data:  $r_adm_usuario_fil);
        }
        if($r_adm_usuario_fil->n_registros === 0){
            $r_adm_usuario = $this->inserta_adm_usuario(registro: $registro);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al ajustar adm_usuario_ins',data:  $r_adm_usuario);
            }
        }
        else{
            $r_adm_usuario = new stdClass();
            $r_adm_usuario->registro_id = $r_adm_usuario_fil->registros[0]['adm_usuario_id'];
        }

        return $r_adm_usuario;
    }

    /**
     * Maqueta un array para insertar un usuario
     * @param array $registro Registro en proceso de agente
     * @return array
     * @version 18.33.0
     */
    private function adm_usuario_ins(array $registro): array
    {
        $keys = array('user','password','email','telefono','adm_grupo_id','nombre','apellido_paterno');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys, registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al valida ',data:  $valida);
        }
        if(!isset($registro['apellido_materno'])){
            $registro['apellido_materno'] = '';
        }
        $adm_usuario_ins['user'] = trim($registro['user']);
        $adm_usuario_ins['password'] = trim($registro['password']);;
        $adm_usuario_ins['email'] = trim($registro['email']);
        $adm_usuario_ins['telefono'] = trim($registro['telefono']);
        $adm_usuario_ins['adm_grupo_id'] = trim($registro['adm_grupo_id']);
        $adm_usuario_ins['nombre'] = trim($registro['nombre']);
        $adm_usuario_ins['ap'] = trim($registro['apellido_paterno']);
        $adm_usuario_ins['am'] = trim($registro['apellido_materno']);

        return $adm_usuario_ins;
    }

    public function alta_bd(array $keys_integra_ds = array('descripcion')): array|stdClass
    {
        $keys = array('nombre','apellido_paterno','user','password','email','telefono','adm_grupo_id');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al valida registro',data:  $valida);
        }


        $registro = $this->previo_alta(registro: $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar descripcion',data:  $registro);
        }


        $this->registro = $registro;

        $r_alta_bd = parent::alta_bd(keys_integra_ds: $keys_integra_ds); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al r_alta_bd agente',data:  $r_alta_bd);
        }
        return $r_alta_bd;

    }

    /**
     * Obtiene los agentes en la session iniciada
     * @return array
     * @version 18.30.0
     */
    final public function com_agentes_session(): array
    {
        $filtro['adm_usuario.id'] = $_SESSION['usuario_id'];
        $filtro['com_agente.status'] = 'activo';
        $r_com_agentes = $this->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener com_agentes',data:  $r_com_agentes);
        }
        return $r_com_agentes->registros;
    }

    /**
     * Maqueta la descripcion de un agente
     * @param array $registro Registro en proceso
     * @return string|array
     * @version 18.41.0
     */
    private function descripcion(array $registro): string|array
    {
        $keys = array('nombre','apellido_paterno');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }
        $descripcion = trim($registro['nombre']);
        $descripcion .= ' '.trim($registro['apellido_paterno']);
        if(!isset($registro['apellido_materno'])){
            $registro['apellido_materno'] = '';
        }
        $descripcion .= ' '.trim($registro['apellido_materno']);
        return trim($descripcion);
    }

    /**
     * Insertar un usuario
     * @param array $registro Registro en proceso
     * @return array|stdClass
     * @version 18.34.0
     */
    private function inserta_adm_usuario(array $registro): array|stdClass
    {
        $keys = array('user','password','email','telefono','adm_grupo_id','nombre','apellido_paterno');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys, registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al valida ',data:  $valida);
        }

        $keys = array('email');
        $valida = $this->validacion->valida_correos(keys: $keys,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }

        $keys = array('telefono');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys, registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al valida ',data:  $valida);
        }

        $keys = array('adm_grupo_id');
        $valida = $this->validacion->valida_ids(keys: $keys, registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al valida ',data:  $valida);
        }


        $adm_usuario_ins = $this->adm_usuario_ins(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al ajustar adm_usuario_ins',data:  $adm_usuario_ins);
        }

        $r_adm_usuario = (new adm_usuario(link: $this->link))->alta_registro(registro: $adm_usuario_ins);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar usuario',data:  $r_adm_usuario);
        }
        return $r_adm_usuario;
    }

    /**
     * Integra una descripcion de usuario
     * @param array $registro
     * @return array
     */
    private function integra_descripcion(array $registro): array
    {
        if(!isset($registro['descripcion'])){

            $descripcion = $this->descripcion(registro: $registro);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al generar descripcion',data:  $descripcion);
            }

            $registro['descripcion'] = trim($descripcion);
        }
        return $registro;
    }

    public function modifica_bd(array $registro, int $id, bool $reactiva = false,
                                array $keys_integra_ds = array('descripcion')): array|stdClass
    {
        $modifica_usuario = $this->actualizar_usuario_agente(datos: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar usuario',data:  $modifica_usuario);
        }

        $r_modifica_bd = parent::modifica_bd(registro: $registro,id:  $id,reactiva:  $reactiva,
            keys_integra_ds:  $keys_integra_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar agente',data:  $r_modifica_bd);
        }
        return $r_modifica_bd;
    }

    public function actualizar_usuario_agente(array $datos ): array|stdClass
    {
        $agente = $this->registro(registro_id: $this->registro_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener agente',data:  $agente);
        }

        $datos['ap'] = $datos['apellido_paterno'];
        $datos['am'] = $datos['apellido_materno'];
        $modifica = (new adm_usuario($this->link))->modifica_bd(registro: $datos, id: $agente['adm_usuario_id']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar usuario',data:  $modifica);
        }

        return $modifica;
    }



    private function previo_alta(array $registro){
        $registro = $this->integra_descripcion(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar descripcion',data:  $registro);
        }

        $codigo = $registro['nombre'][0].$registro['apellido_paterno'][0].mt_rand(100,999);

        if(!isset($registro['codigo']) || trim($registro['codigo']) === ''){
            $registro['codigo'] = $codigo;
        }

        $r_adm_usuario = $this->adm_usuario(registro: $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al ajustar adm_usuario_ins',data:  $r_adm_usuario);
        }
        $registro['adm_usuario_id'] = $r_adm_usuario->registro_id;

        if(!isset($registro['descripcion_select'])){
            $registro['descripcion_select'] = $registro['descripcion'];
        }

        return $registro;
    }

    public function prospectos(int $com_agente_id): array
    {
        if($com_agente_id <= 0){
            return $this->error->error(mensaje: 'Error com_agente_id debe ser mayor a 0',data:  $com_agente_id);
        }

        $filtro['com_agente.id'] = $com_agente_id;

        $data = (new com_prospecto($this->link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener clientes',data:  $data);
        }
        return $data->registros;
    }

    final public function regenera_descripcion_select(int $com_agente_id): array|stdClass
    {
        if($com_agente_id <= 0){
            return $this->error->error(mensaje: 'Error com_agente_id debe ser mayor a 0',data:  $com_agente_id);
        }

        $com_agente = $this->registro(registro_id: $com_agente_id, columnas_en_bruto: true,retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener com_agente',data:  $com_agente);
        }

        $descripcion_select = trim($com_agente->nombre.' '.$com_agente->apellido_paterno);
        $upd['descripcion_select'] = $descripcion_select;

        $result_upd = parent::modifica_bd(registro: $upd, id: $com_agente_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar com_agente',data:  $result_upd);
        }

        return $result_upd;
    }

}