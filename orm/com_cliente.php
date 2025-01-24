<?php

namespace gamboamartin\comercial\models;

use base\orm\_modelo_parent;
use DOMDocument;
use DOMXPath;
use gamboamartin\cat_sat\models\_validacion;
use gamboamartin\cat_sat\models\cat_sat_forma_pago;
use gamboamartin\cat_sat\models\cat_sat_metodo_pago;
use gamboamartin\cat_sat\models\cat_sat_moneda;
use gamboamartin\cat_sat\models\cat_sat_regimen_fiscal;
use gamboamartin\cat_sat\models\cat_sat_tipo_de_comprobante;
use gamboamartin\cat_sat\models\cat_sat_uso_cfdi;
use gamboamartin\comercial\controllers\controlador_com_cliente;
use gamboamartin\direccion_postal\models\dp_calle_pertenece;
use gamboamartin\direccion_postal\models\dp_municipio;
use gamboamartin\documento\models\adm_grupo;
use gamboamartin\documento\models\doc_conf_tipo_documento_seccion;
use gamboamartin\documento\models\doc_documento;
use gamboamartin\documento\models\doc_tipo_documento;
use gamboamartin\errores\errores;
use gamboamartin\plugins\imagen;
use gamboamartin\plugins\pdf;
use gamboamartin\plugins\web;
use PDO;
use stdClass;

class com_cliente extends _modelo_parent
{
    public function __construct(PDO $link)
    {
        $tabla = 'com_cliente';

        $columnas = array($tabla => false, 'cat_sat_moneda' => $tabla, 'cat_sat_regimen_fiscal' => $tabla,
            'dp_municipio' => $tabla, 'dp_estado' => 'dp_municipio', 'dp_pais' => 'dp_estado',
            'com_tipo_cliente' => $tabla, 'cat_sat_uso_cfdi' => $tabla, 'cat_sat_metodo_pago' => $tabla,
            'cat_sat_forma_pago' => $tabla, 'cat_sat_tipo_de_comprobante' => $tabla, 'cat_sat_tipo_persona' => $tabla);

        $campos_obligatorios = array('cat_sat_moneda_id', 'cat_sat_regimen_fiscal_id', 'cat_sat_moneda_id',
            'cat_sat_forma_pago_id', 'cat_sat_uso_cfdi_id', 'cat_sat_tipo_de_comprobante_id', 'cat_sat_metodo_pago_id',
            'telefono', 'cat_sat_tipo_persona_id', 'pais', 'estado', 'municipio', 'colonia', 'calle', 'cp', 'dp_municipio_id');

        $columnas_extra['com_cliente_n_sucursales'] =
            "(SELECT COUNT(*) FROM com_sucursal WHERE com_sucursal.com_cliente_id = com_cliente.id)";


        $seguridad = $this->seguridad_datos(columnas_extra: $columnas_extra,link:  $link);
        if (errores::$error) {
            $error = (new errores())->error(mensaje: 'Error al integrar columnas_seguridad', data: $seguridad);
            print_r($error);
            exit;
        }

        $tipo_campos = array();
        $tipo_campos['rfc'] = 'rfc';

        $atributos_criticos[] = 'cat_sat_tipo_persona_id';



        parent::__construct(link: $link, tabla: $tabla, aplica_seguridad: $seguridad->aplica_seguridad,
            campos_obligatorios: $campos_obligatorios, columnas: $columnas, columnas_extra: $seguridad->columnas_extra,
            tipo_campos: $tipo_campos, atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;

        $this->etiqueta = 'Cliente';
    }

    private function ajusta_key_dom(string $key_dom, array $registro)
    {
        $dp_calle_pertenece = (new dp_calle_pertenece(link: $this->link))->registro(
            registro_id: $registro['dp_calle_pertenece_id']);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener calle', data: $dp_calle_pertenece);
        }

        $registro = $this->integra_key_dom_faltante(dp_calle_pertenece: $dp_calle_pertenece, key_dom: $key_dom, registro: $registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al integrar key_dom', data: $registro);
        }
        return $registro;

    }

    private function ajusta_keys_dom(array $registro)
    {
        $keys_dom = array('pais', 'estado', 'municipio', 'colonia', 'calle', 'cp');

        foreach ($keys_dom as $key_dom) {

            $registro = $this->ajusta_key_dom(key_dom: $key_dom, registro: $registro);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al integrar key_dom', data: $registro);
            }
        }


        return $registro;

    }

    /**
     * Inserta un cliente
     * @param array $keys_integra_ds Campos para la integracion de descricpion select
     * @return array|stdClass
     */
    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $this->registro = $this->init_base(data: $this->registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al inicializar campo base', data: $this->registro);
        }
        $this->registro = $this->inicializa_foraneas(data: $this->registro, funcion_llamada: __FUNCTION__);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al inicializar foraneas', data: $this->registro);
        }

        $keys = array('telefono', 'numero_exterior', 'razon_social', 'dp_municipio_id');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys, registro: $this->registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar registro', data: $valida);
        }

        if (!isset($this->registro["numero_interior"])) {
            $this->registro['numero_interior'] = '';
        }

        $es_empleado = false;

        if (isset($this->registro["es_empleado"])) {
            $es_empleado = $this->registro["es_empleado"];
        }

        $dp_municipio_modelo = new dp_municipio(link: $this->link);
        $dp_municipio = $dp_municipio_modelo->registro(registro_id: $this->registro['dp_municipio_id']);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener dp_municipio', data: $dp_municipio);
        }

        $this->registro['pais'] = $dp_municipio['dp_pais_descripcion'];
        $this->registro['estado'] = $dp_municipio['dp_estado_descripcion'];
        $this->registro['municipio'] = $dp_municipio['dp_municipio_descripcion'];


        $this->registro = $this->limpia_campos(registro: $this->registro, campos_limpiar: array('dp_pais_id',
            'dp_estado_id', 'dp_cp_id', 'dp_cp_id', 'dp_colonia_postal_id', 'es_empleado'));
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al limpiar campos', data: $this->registro);
        }

        $valida = (new _validacion())->valida_metodo_pago(link: $this->link, registro: $this->registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar datos', data: $valida);
        }

        $valida = (new _validacion())->valida_conf_tipo_persona(link: $this->link, registro: $this->registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar datos', data: $valida);
        }

        $registro = $this->descripcion(registro: $this->registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al integrar descripcion', data: $registro);
        }
        $this->registro = $registro;


        $registro = $this->ajusta_keys_dom(registro: $this->registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al integrar key_dom', data: $registro);
        }
        $this->registro = $registro;


        if(isset($this->registro['dp_calle_pertenece_id'])){
            unset($this->registro['dp_calle_pertenece_id']);
        }


        $r_alta_bd = parent::alta_bd(keys_integra_ds: $keys_integra_ds);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar cliente', data: $r_alta_bd);
        }
        if ($this->registro['cp'] === 'PREDETERMINADO') {
            $this->registro['cp'] = '99999';
        }
        if ($this->registro['cp'] === 'PRED') {
            $this->registro['cp'] = '99999';
        }
        $data = (new com_sucursal($this->link))->maqueta_data(calle: $this->registro['calle'],
            codigo: $this->registro["codigo"], colonia: $this->registro['colonia'], cp: $this->registro['cp'],
            nombre_contacto: $this->registro["razon_social"], com_cliente_id: $r_alta_bd->registro_id,
            telefono: $this->registro["telefono"], dp_municipio_id: $dp_municipio['dp_municipio_id'],
            numero_exterior: $this->registro["numero_exterior"], numero_interior: $this->registro["numero_interior"],
            es_empleado: $es_empleado);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al maquetar datos de sucursal', data: $data);
        }

        $valida = (new com_sucursal(link: $this->link))->valida_base_sucursal(registro: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar datos para sucursal', data: $valida);
        }

        $alta_sucursal = (new com_sucursal($this->link))->alta_registro(registro: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar sucursal', data: $alta_sucursal);
        }

        if(trim($_FILES['documento']['name']) !== '') {
            $inserta_documento = $this->registra_documento_cliente(com_cliente: $r_alta_bd->registro_id);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al insertar documento para cliente', data: $inserta_documento);
            }
        }

        return $r_alta_bd;
    }

    private function aplica_seguridad(PDO $link): bool|array
    {
        $aplica_seguridad = false;
        if(isset($_SESSION['grupo_id'])) {
            $aplica_seguridad = true;
            $grupo_id = $_SESSION['grupo_id'];

            $adm_grupo = (new adm_grupo(link: $link))->registro(registro_id: $grupo_id, columnas_en_bruto: true,
                retorno_obj: true);
            if (errores::$error) {
                return (new errores())->error(mensaje: 'Error al obtener grupo de usuario', data: $adm_grupo);
            }

            if (!isset($adm_grupo->solo_mi_info)) {
                $adm_grupo->solo_mi_info = 'inactivo';
            }
            if ($adm_grupo->solo_mi_info === 'inactivo') {
                $aplica_seguridad = false;
            }
        }
        return $aplica_seguridad;


    }

    public function registra_documento_cliente(int $com_cliente) : array|stdClass {
        if (!array_key_exists('documento', $_FILES)) {
            return array();
        }

        $tipo_documento = (new doc_documento($this->link))->validar_permisos_documento(modelo: $this->tabla);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar permisos para el documento', data: $tipo_documento);
        }

        $_POST = array();
        $com_cliente_documento = new com_cliente_documento($this->link);
        $com_cliente_documento->registro['com_cliente_id'] = $com_cliente;
        $com_cliente_documento->registro['doc_tipo_documento_id'] = $tipo_documento['doc_tipo_documento_id'];
        $_POST['doc_tipo_documento_id'] = $tipo_documento['doc_tipo_documento_id'];

        $alta_documento = $com_cliente_documento->alta_bd();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar documento', data: $alta_documento );
        }

        return $alta_documento;
    }

    final public function asigna_prospecto(int $com_cliente_id, int $com_prospecto_id)
    {
        $registro['com_cliente_id'] = $com_cliente_id;
        $registro['com_prospecto_id'] = $com_prospecto_id;
        $tiene_relacion = (new com_rel_prospecto_cte(link: $this->link))->tiene_relacion(registro: $registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error a verificar si tiene relacion', data: $tiene_relacion);
        }

        if ($tiene_relacion) {
            return $this->error->error(mensaje: 'Error el cliente ya esta relacionado', data: $tiene_relacion);
        }

        $com_rel_prospecto_cte_ins['com_cliente_id'] = $com_cliente_id;
        $com_rel_prospecto_cte_ins['com_prospecto_id'] = $com_prospecto_id;

        $inserta = (new com_rel_prospecto_cte(link: $this->link))->alta_registro(registro: $com_rel_prospecto_cte_ins);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar relacion', data: $inserta);
        }
        return $inserta;

    }

    public function leer_codigo_qr(): array|stdClass
    {
        if (!array_key_exists('documento', $_FILES)) {
            return $this->error->error(mensaje: 'Error no existe documento', data: $_FILES);
        }

        $directorio_destino = 'archivos/temporales/pdf/cliente_'. $_GET['registro_id'].'/';

        if (!file_exists($directorio_destino)) {
            mkdir($directorio_destino, 0777, true);
        }

        $nombre_archivo = basename($_FILES['documento']['name']);
        $ruta_destino = $directorio_destino . $nombre_archivo;

        if (!move_uploaded_file($_FILES['documento']['tmp_name'], $ruta_destino)) {
            return $this->error->error(mensaje: 'Error al mover archivo', data: $_FILES);
        }

        $nombre_directorio_imagen = 'archivos/temporales/imagenes/cliente_'.$_GET['registro_id'].'/';

        $contenido = (new pdf())->leer_pdf(directorio: $nombre_directorio_imagen, prefijo_imagen: "imagen",
            ruta_pdf: $ruta_destino);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al leer pdf', data: $contenido);
        }

        $ruta_qr = (new imagen())->obtener_qr($contenido['imagenes']);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener qr', data: $ruta_qr);
        }

        $url = (new imagen())->leer_codigo_qr(ruta_qr: $ruta_qr);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al leer código QR', data: $url);
        }

        $directorio_borrado = (new doc_documento($this->link))->borrar_directorio($directorio_destino);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al borrar directorio', data: $directorio_borrado);
        }

        $directorio_borrado = (new doc_documento($this->link))->borrar_directorio($nombre_directorio_imagen);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al borrar directorio', data: $directorio_borrado);
        }

        $contenido = (new web())->leer_contenido(url: $url);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al leer contenido', data: $contenido);
        }

        $contenido_formateado = $this->contenido_web_formateado(html: $contenido);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al formatear contenido', data: $contenido_formateado);
        }

        $contenido_formateado = $this->contenido_anexar_campos(contenido: $contenido_formateado);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al anexar campos', data: $contenido_formateado);
        }

        return get_object_vars($contenido_formateado);
    }

    public function contenido_anexar_campos(stdClass $contenido): array|stdClass
    {
        if (!property_exists($contenido, 'datos_identificacion')) {
            return $this->error->error(mensaje: 'Error no existe datos_identificacion', data: $contenido);
        }

        $datos_identificacion = $contenido->datos_identificacion;

        $contenido->tipo_persona = "PERSONA FISICA";

        if (array_key_exists('denominacion_o_razon_social', $datos_identificacion)) {
            $contenido->tipo_persona = "PERSONA MORAL";
        }

        return $contenido;
    }

    /**
     * Obtiene el contenido de una página web
     * @param string $html Contenido de la página web
     * @return stdClass Objeto con los datos obtenidos de la página web
     */
    public function contenido_web_formateado(string $html): stdClass
    {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        $rfc_node = $xpath->query('//ul[@data-role="listview"][1]/li[contains(text(), "El RFC:")]');
        $datos_identificacion = $xpath->query('//ul[@data-role="listview"][2]//table/tbody/tr');
        $datos_ubicacion = $xpath->query('//ul[@data-role="listview"][3]//table/tbody/tr');
        $datos_fiscales = $xpath->query('//ul[@data-role="listview"][4]//table/tbody/tr');

        function extraer_rfc($nodes)
        {
            $rfc = '';

            if ($nodes->length > 0) {
                $rfc_text = $nodes->item(0)->textContent;
                if (preg_match('/El RFC:\s*([A-Z0-9]{10,13})/', $rfc_text, $matches)) {
                    if (isset($matches[1])) {
                        $rfc = trim($matches[1]);
                    }
                }
            }

            return $rfc;
        }

        function extraer_datos($nodes)
        {
            $datos = [];
            foreach ($nodes as $row) {
                $tds = $row->getElementsByTagName('td');
                if ($tds->length == 2) {
                    $key = trim($tds->item(0)->textContent);
                    $value = trim($tds->item(1)->textContent);

                    if (preg_match('/^\$\(/', $key) === 0) {
                        $key = strtolower($key);
                        $key = str_replace(':', '', $key);
                        $key = str_replace(' ', '_', $key);
                        $key = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $key);
                        $datos[$key] = $value;
                    }
                }
            }
            return $datos;
        }

        $datos_rfc = extraer_rfc($rfc_node);
        $datos_identificacion = extraer_datos($datos_identificacion);
        $datos_ubicacion = extraer_datos($datos_ubicacion);
        $datos_fiscales = extraer_datos($datos_fiscales);

        $salida = new stdClass();
        $salida->rfc = $datos_rfc;
        $salida->datos_identificacion = $datos_identificacion;
        $salida->datos_ubicacion = $datos_ubicacion;
        $salida->datos_fiscales = $datos_fiscales;

        return $salida;
    }



    private function columna_seguridad(bool $aplica_seguridad): string
    {
        $sql = "$_SESSION[usuario_id]";
        if($aplica_seguridad) {
            $sql =
                "(SELECT adm_usuario.id FROM com_contacto
                    LEFT JOIN com_contacto_user ON com_contacto_user.com_contacto_id = com_contacto.id 
                    LEFT JOIN adm_usuario ON adm_usuario.id = com_contacto_user.adm_usuario_id
                    WHERE com_contacto.com_cliente_id = com_cliente.id 
                    AND adm_usuario.id = $_SESSION[usuario_id])";
        }

        return $sql;

    }

    final public function com_prospecto(int $com_cliente_id)
    {
        $existe = $this->tiene_prospecto(com_cliente_id: $com_cliente_id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al verificar si existe prospecto', data: $existe);
        }
        $filtro['com_cliente.id'] = $com_cliente_id;
        $r_com_rel_prospecto_cte = (new com_rel_prospecto_cte(link: $this->link))->filtro_and(filtro: $filtro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener relaciones', data: $r_com_rel_prospecto_cte);
        }
        if ($r_com_rel_prospecto_cte->n_registros > 1) {
            return $this->error->error(mensaje: 'Error hay un error de integridad', data: $r_com_rel_prospecto_cte);
        }
        return (object)$r_com_rel_prospecto_cte->rgeistros[0];

    }

    final public function com_prospecto_id(int $com_cliente_id)
    {
        $com_prospecto = $this->com_prospecto(com_cliente_id: $com_cliente_id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener com_prospecto', data: $com_prospecto);
        }
        return (int)$com_prospecto->com_prospecto_id;
    }

    /**
     * Obtiene la descripcion de una sucursal
     * @param stdClass $com_cliente Registro de tipo cliente
     * @param array $sucursal Registro de tipo sucursal
     * @return array|string
     * @version 17.16.0
     */
    private function com_sucursal_descripcion(stdClass $com_cliente, array $sucursal): array|string
    {
        $valida = $this->valida_data_sucursal(com_cliente: $com_cliente, sucursal: $sucursal);;
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al al validar datos', data: $valida);
        }

        $data = array();
        $data['codigo'] = $sucursal['com_sucursal_codigo'];

        $com_sucursal_descripcion = (new com_sucursal(link: $this->link))->ds(
            com_cliente_razon_social: $com_cliente->razon_social, com_cliente_rfc: $com_cliente->rfc, data: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener com_sucursal_descripcion',
                data: $com_sucursal_descripcion);
        }
        return $com_sucursal_descripcion;
    }

    /**
     * Integra los elementos a actualizar de una sucursal basada en los datos de un cliente
     * @param stdClass $com_cliente Registro de cliente
     * @param int $com_cliente_id Identificador del cliente
     * @param string $com_sucursal_descripcion Descripcion de la sucursal
     * @param array $sucursal Registro de sucursal previo
     * @return array
     */
    private function com_sucursal_upd(stdClass $com_cliente, int $com_cliente_id, string $com_sucursal_descripcion,
                                      array    $sucursal): array
    {
        $keys = array('com_sucursal_codigo', 'com_tipo_sucursal_descripcion');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys, registro: $sucursal);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar sucursal', data: $valida);
        }
        if ($com_cliente_id <= 0) {
            return $this->error->error(mensaje: 'Error com_cliente_id debe ser mayor a 0', data: $com_cliente_id);
        }

        $keys = array('numero_exterior', 'telefono');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys, registro: $com_cliente);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar cliente', data: $valida);
        }
        if (!isset($com_cliente->numero_interior)) {
            $com_cliente->numero_interior = '';
        }

        $com_sucursal_descripcion = trim($com_sucursal_descripcion);
        if ($com_sucursal_descripcion === '') {
            return $this->error->error(mensaje: 'Error com_sucursal_descripcion esta vacia',
                data: $com_sucursal_descripcion);
        }

        $com_sucursal_upd['codigo'] = $sucursal['com_sucursal_codigo'];
        $com_sucursal_upd['descripcion'] = $com_sucursal_descripcion;
        $com_sucursal_upd['com_cliente_id'] = $com_cliente_id;

        if ($sucursal['com_tipo_sucursal_descripcion'] === 'MATRIZ') {
            $com_sucursal_upd = $this->com_sucursal_upd_dom(com_cliente: $com_cliente,
                com_sucursal_upd: $com_sucursal_upd);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al maquetar sucursal', data: $com_sucursal_upd);
            }
        }

        return $com_sucursal_upd;
    }

    /**
     * Valida que sean correctos los elementos de la direccion de un cliente
     * @param stdClass $com_cliente Registro de tipo cliente
     * @param array $com_sucursal_upd registro a actualizar de sucursal
     * @return array
     */
    private function com_sucursal_upd_dom(stdClass $com_cliente, array $com_sucursal_upd): array
    {
        $keys = array('numero_exterior', 'telefono', 'pais', 'estado', 'municipio', 'colonia',
            'calle', 'dp_municipio_id', 'cp');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys, registro: $com_cliente);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar cliente', data: $valida);
        }
        if (!isset($com_cliente->numero_interior)) {
            $com_cliente->numero_interior = '';
        }
        $com_sucursal_upd['numero_exterior'] = trim($com_cliente->numero_exterior);
        $com_sucursal_upd['numero_interior'] = trim($com_cliente->numero_interior);
        $com_sucursal_upd['telefono_1'] = trim($com_cliente->telefono);
        $com_sucursal_upd['telefono_2'] = trim($com_cliente->telefono);
        $com_sucursal_upd['telefono_3'] = trim($com_cliente->telefono);
        $com_sucursal_upd['pais'] = trim($com_cliente->pais);
        $com_sucursal_upd['estado'] = trim($com_cliente->estado);
        $com_sucursal_upd['municipio'] = trim($com_cliente->municipio);
        $com_sucursal_upd['colonia'] = trim($com_cliente->colonia);
        $com_sucursal_upd['calle'] = trim($com_cliente->calle);
        $com_sucursal_upd['dp_municipio_id'] = trim($com_cliente->dp_municipio_id);
        $com_sucursal_upd['cp'] = trim($com_cliente->cp);


        return $com_sucursal_upd;
    }

    private function descripcion(array $registro): array
    {
        if (!isset($registro['descripcion'])) {
            $descripcion = trim($registro['razon_social'] . ' ' . $registro['rfc']);
            $registro['descripcion'] = $descripcion;
        }
        return $registro;

    }


    /**
     * Elimina un cliente mas las sucursales dentro del cliente
     * @param int $id Identificador del cliente
     * @return array|stdClass
     */

    public function elimina_bd(int $id): array|stdClass
    {

        if ($id <= 0) {
            return $this->error->error(mensaje: 'Error id es menor a 0', data: $id);
        }

        $filtro['com_cliente.id'] = $id;
        $r_com_sucursal = (new com_sucursal(link: $this->link))->elimina_con_filtro_and(filtro: $filtro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al eliminar sucursales', data: $r_com_sucursal);
        }

        $r_com_sucursal = (new com_email_cte(link: $this->link))->elimina_con_filtro_and(filtro: $filtro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al eliminar sucursales', data: $r_com_sucursal);
        }
        $del = (new com_rel_prospecto_cte(link: $this->link))->elimina_con_filtro_and(filtro: $filtro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al eliminar relacion', data: $del);
        }

        $r_cliente_documento = (new com_cliente_documento(link: $this->link))->elimina_con_filtro_and(filtro: $filtro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al eliminar cliente_documento', data: $r_cliente_documento);
        }

        $r_elimina_bd = parent::elimina_bd(id: $id); // TODO: Change the autogenerated stub
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al eliminar', data: $r_elimina_bd);
        }
        return $r_elimina_bd;
    }

    /**
     * TOTAL
     * Función para inicializar las variables básicas del cliente.
     *
     * Esta función recibe un arreglo asociativo con la información del cliente, si dentro de este arreglo se encuentra el índice
     * 'razon_social' y no se encuentra el índice 'descripcion', asignará el valor de 'razon_social' a 'descripcion'.
     *
     * @param array $data El arreglo asociativo que contiene los datos que serán inicializados.
     * @return array El arreglo asociativo $data después de haber sido procesado por la función.
     *
     * @example Ejemplo de uso:
     * <code>
     *  $cliente = array(
     *      'razon_social' => 'Compañía XY',
     *  );
     *  $cliente = init_base($cliente);
     *  echo $cliente['descripcion']; // Imprime: Compañía XY
     * </code>
     * @version 22.2.0
     * @url https://github.com/gamboamartin/comercial/wiki/orm.com_cliente.init_base
     */
    final protected function init_base(array $data): array
    {
        if (isset($data['razon_social']) && !isset($data['descripcion'])) {
            $data['descripcion'] = $data['razon_social'];
        }

        return $data;
    }

    /**
     * Inicializa las foraneas base de un cliente siempre y cuando sea ejecutado en alta bd
     * @param array $data Registro
     * @param string $funcion_llamada Funcion base de llamada alta_bd o modifica_bd
     * @return array
     */
    private function inicializa_foraneas(array $data, string $funcion_llamada): array
    {
        $funcion_llamada = trim($funcion_llamada);
        if ($funcion_llamada === '') {
            return $this->error->error(mensaje: "Error al funcion_llamada esta vacia" . $this->tabla,
                data: $funcion_llamada);
        }

        if (isset($data['status'])) {
            return $data;
        }

        $foraneas['cat_sat_moneda_id'] = new cat_sat_moneda($this->link);
        $foraneas['dp_calle_pertenece_id'] = new dp_calle_pertenece($this->link);
        $foraneas['cat_sat_regimen_fiscal_id'] = new cat_sat_regimen_fiscal($this->link);
        $foraneas['cat_sat_forma_pago_id'] = new cat_sat_forma_pago($this->link);
        $foraneas['cat_sat_uso_cfdi_id'] = new cat_sat_uso_cfdi($this->link);
        $foraneas['cat_sat_tipo_de_comprobante_id'] = new cat_sat_tipo_de_comprobante($this->link);
        $foraneas['cat_sat_metodo_pago_id'] = new cat_sat_metodo_pago($this->link);
        $foraneas['com_tipo_cliente_id'] = new com_tipo_cliente($this->link);

        foreach ($foraneas as $key => $modelo_pred) {

            if ($funcion_llamada === 'alta_bd') {
                if (!isset($data[$key]) || $data[$key] === -1) {
                    $predeterminado = ($modelo_pred)->id_predeterminado();
                    if (errores::$error) {
                        return $this->error->error(mensaje: "Error al $key predeterminada en modelo " . $this->tabla,
                            data: $predeterminado);
                    }
                    $data[$key] = $predeterminado;
                }
            }
        }

        return $data;
    }

    private function integra_columna_seguridad(bool $aplica_seguridad, array $columnas_extra): array
    {
        $columnas_seguridad = $this->columna_seguridad(aplica_seguridad: $aplica_seguridad);
        if (errores::$error) {
           return (new errores())->error(mensaje: 'Error al obtener columnas_seguridad', data: $columnas_seguridad);
        }
        $columnas_extra['usuario_permitido_id'] = $columnas_seguridad;

        return $columnas_extra;

    }

    final public function integra_documentos(controlador_com_cliente $controler)
    {
        $cliente = $this->registro(registro_id: $controler->registro_id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener cliente', data: $cliente);
        }

        $conf_tipos_docs = (new doc_conf_tipo_documento_seccion(link: $controler->link))->filtro_and(
            columnas: ['doc_tipo_documento_id'],filtro: array('adm_seccion.descripcion' => $this->tabla));
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener conf. de tipos de documentos', data: $conf_tipos_docs);
        }

        $doc_ids = array_map(function ($registro) {
            return $registro['doc_tipo_documento_id'];
        }, $conf_tipos_docs->registros);

        if (count($doc_ids) <= 0) {
            return array();
        }

        $clientes_documentos = (new com_cliente_documento(link: $controler->link))->documentos(
            com_cliente: $controler->registro_id, tipos_documentos: $doc_ids);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener documentos', data: $clientes_documentos);
        }

        $buttons_documentos = $this->buttons_documentos(controler: $controler, clientes_documentos: $clientes_documentos,
            tipos_documentos: $doc_ids);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al integrar buttons', data: $buttons_documentos);
        }

        return $buttons_documentos;
    }

    public function buttons_documentos(controlador_com_cliente $controler, array $clientes_documentos, array $tipos_documentos)
    {
        $conf_docs = $this->documentos_de_cliente(com_cliente_id: $controler->registro_id,
            link: $controler->link, todos: true, tipos_documentos: $tipos_documentos);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener configuraciones de documentos',
                data: $conf_docs);
        }

        foreach ($conf_docs as $indice => $doc_tipo_documento) {
            $conf_docs = $this->inm_docs_prospecto(controler: $controler,
                doc_tipo_documento: $doc_tipo_documento, indice: $indice,
                com_conf_tipo_doc_cliente: $conf_docs, clientes_documentos: $clientes_documentos);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al integrar buttons', data: $conf_docs);
            }
        }

        return $conf_docs;
    }

    final public function documentos_de_cliente(int $com_cliente_id, PDO $link, bool $todos, array $tipos_documentos)
    {
        $in = array();

        if (count($tipos_documentos) > 0) {
            $in['llave'] = 'doc_tipo_documento.id';
            $in['values'] = $tipos_documentos;
        }

        $r_doc_tipo_documento = (new doc_tipo_documento(link: $link))->filtro_and(in: $in);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al Obtener tipos de documento', data: $r_doc_tipo_documento);
        }

        return $r_doc_tipo_documento->registros;
    }

    private function inm_docs_prospecto(controlador_com_cliente $controler, array $doc_tipo_documento, int $indice,
                                        array $com_conf_tipo_doc_cliente, array $clientes_documentos)
    {
        $existe = false;
        foreach ($clientes_documentos as $cliente_documento) {
            $existe_doc = $this->doc_existente(controler: $controler,
                doc_tipo_documento: $doc_tipo_documento, indice: $indice,
                com_conf_tipo_doc_cliente: $com_conf_tipo_doc_cliente, clientes_documentos: $cliente_documento);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al integrar datos', data: $existe_doc);
            }

            $com_conf_tipo_doc_cliente = $existe_doc->com_conf_tipo_doc_cliente;
            $existe = $existe_doc->existe;
            if ($existe) {
                break;
            }
        }

        if (!$existe) {
            $com_conf_tipo_doc_cliente = $this->integra_data(controler: $controler,
                doc_tipo_documento: $doc_tipo_documento, indice: $indice,
                com_conf_tipo_doc_cliente: $com_conf_tipo_doc_cliente);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al integrar button', data: $com_conf_tipo_doc_cliente);
            }
        }

        return $com_conf_tipo_doc_cliente;
    }

    private function integra_data(controlador_com_cliente $controler, array $doc_tipo_documento,
                                  int $indice, array $com_conf_tipo_doc_cliente){
        $params = array('doc_tipo_documento_id'=>$doc_tipo_documento['doc_tipo_documento_id']);

        $button = $controler->html->button_href(accion: 'subir_documento',etiqueta:
            'Subir Documento',registro_id:  $controler->registro_id,
            seccion:  'com_cliente',style:  'warning', params: $params);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar button',data:  $button);
        }

        $com_conf_tipo_doc_cliente = $this->integra_button_default(button: $button,
            indice:  $indice,com_conf_tipo_doc_cliente:  $com_conf_tipo_doc_cliente);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar button',data:  $com_conf_tipo_doc_cliente);
        }

        return $com_conf_tipo_doc_cliente;
    }

    private function integra_button_default(string $button, int $indice, array $com_conf_tipo_doc_cliente): array
    {
        $com_conf_tipo_doc_cliente[$indice]['descarga'] = $button;
        $com_conf_tipo_doc_cliente[$indice]['vista_previa'] = $button;
        $com_conf_tipo_doc_cliente[$indice]['descarga_zip'] = $button;
        $com_conf_tipo_doc_cliente[$indice]['elimina_bd'] = $button;
        return $com_conf_tipo_doc_cliente;
    }

    private function doc_existente(controlador_com_cliente $controler, array $doc_tipo_documento, int $indice,
                                   array                   $com_conf_tipo_doc_cliente, array $clientes_documentos)
    {

        $existe = false;
        if ($doc_tipo_documento['doc_tipo_documento_id'] === $clientes_documentos['doc_tipo_documento_id']) {

            $existe = true;

            $com_conf_tipo_doc_cliente = $this->buttons_base(controler: $controler, indice: $indice,
                com_cliente_documento_id: $controler->registro_id, com_conf_tipo_doc_cliente: $com_conf_tipo_doc_cliente,
                com_cliente_documento: $clientes_documentos);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al integrar button', data: $com_conf_tipo_doc_cliente);
            }
        }

        $data = new stdClass();
        $data->existe = $existe;
        $data->com_conf_tipo_doc_cliente = $com_conf_tipo_doc_cliente;
        return $data;
    }

    private function buttons_base(controlador_com_cliente $controler, int $indice, int $com_cliente_documento_id,
                                  array $com_conf_tipo_doc_cliente, array $com_cliente_documento): array
    {
        $com_conf_tipo_doc_cliente = $this->buttons(controler: $controler, indice: $indice,
            com_conf_tipo_doc_cliente: $com_conf_tipo_doc_cliente, com_cliente_documento: $com_cliente_documento);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al integrar button', data: $com_conf_tipo_doc_cliente);
        }

        $com_conf_tipo_doc_cliente = $this->button_del(controler: $controler, indice: $indice,
            com_cliente_documento_id: $com_cliente_documento_id, com_conf_tipo_doc_cliente: $com_conf_tipo_doc_cliente,
            com_cliente_documento: $com_cliente_documento);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al integrar button', data: $com_conf_tipo_doc_cliente);
        }

        return $com_conf_tipo_doc_cliente;
    }

    private function buttons(controlador_com_cliente $controler, int $indice, array $com_conf_tipo_doc_cliente,
                             array $com_cliente_documento)
    {

        $com_conf_tipo_doc_cliente = $this->button(accion: 'descarga', controler: $controler,
            etiqueta: 'Descarga', indice: $indice, com_cliente_documento_id: $com_cliente_documento['com_cliente_documento_id'],
            com_conf_tipo_doc_cliente: $com_conf_tipo_doc_cliente);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al integrar button', data: $com_conf_tipo_doc_cliente);
        }

        $com_conf_tipo_doc_cliente = $this->button(accion: 'vista_previa', controler: $controler,
            etiqueta: 'Vista Previa', indice: $indice, com_cliente_documento_id: $com_cliente_documento['com_cliente_documento_id'],
            com_conf_tipo_doc_cliente: $com_conf_tipo_doc_cliente, target: '_blank');
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al integrar button', data: $com_conf_tipo_doc_cliente);
        }

        $com_conf_tipo_doc_cliente = $this->button(accion: 'descarga_zip', controler: $controler,
            etiqueta: 'ZIP', indice: $indice, com_cliente_documento_id: $com_cliente_documento['com_cliente_documento_id'],
            com_conf_tipo_doc_cliente: $com_conf_tipo_doc_cliente, target: '_blank');
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al integrar button', data: $com_conf_tipo_doc_cliente);
        }

        return $com_conf_tipo_doc_cliente;
    }

    private function button(string $accion, controlador_com_cliente $controler, string $etiqueta, int $indice,
                            int    $com_cliente_documento_id, array $com_conf_tipo_doc_cliente, array $params = array(),
                            string $style = 'success', string $target = ''): array
    {
        $button = $controler->html->button_href(accion: $accion, etiqueta: $etiqueta,
            registro_id: $com_cliente_documento_id, seccion: 'com_cliente_documento', style: $style, params: $params,
            target: $target);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al integrar button', data: $button);
        }
        $com_conf_tipo_doc_cliente[$indice][$accion] = $button;

        return $com_conf_tipo_doc_cliente;
    }

    final public function button_del(controlador_com_cliente $controler, int $indice, int $com_cliente_documento_id,
                                     array $com_conf_tipo_doc_cliente, array $com_cliente_documento){
        $params = array('accion_retorno'=>'documentos','seccion_retorno'=>$controler->seccion,
            'id_retorno'=>$com_cliente_documento_id);

        $com_conf_tipo_doc_cliente = $this->button(accion: 'elimina_bd', controler: $controler,
            etiqueta: 'Elimina', indice: $indice, com_cliente_documento_id: $com_cliente_documento['com_cliente_documento_id'],
            com_conf_tipo_doc_cliente: $com_conf_tipo_doc_cliente, params: $params, style: 'danger');
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al integrar button', data: $com_conf_tipo_doc_cliente);
        }

        return $com_conf_tipo_doc_cliente;
    }

    private function integra_key_dom(array $dp_calle_pertenece, string $key_dom, array $registro): array
    {
        $registro[$key_dom] = $dp_calle_pertenece['dp_' . $key_dom . '_descripcion'];
        return $registro;

    }

    private function integra_key_dom_faltante(array $dp_calle_pertenece, string $key_dom, array $registro)
    {
        if (!isset($registro[$key_dom])) {
            $registro = $this->integra_key_dom(dp_calle_pertenece: $dp_calle_pertenece, key_dom: $key_dom, registro: $registro);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al integrar key_dom', data: $registro);
            }
        }
        return $registro;

    }

    /**
     * Limpia elementos no insertables
     * @param array $registro Registro en proceso
     * @param array $campos_limpiar campos a quitar de registro
     * @return array
     */
    private function limpia_campos(array $registro, array $campos_limpiar): array
    {
        foreach ($campos_limpiar as $valor) {
            $valor = trim($valor);
            if ($valor === '') {
                return $this->error->error(mensaje: "Error valor esta vacio" . $this->tabla, data: $valor);
            }
            if (isset($registro[$valor])) {
                unset($registro[$valor]);
            }
        }
        return $registro;
    }

    /**
     * Modifica un registro de cliente
     * @param array $registro Datos a actualizar
     * @param int $id Identificador
     * @param bool $reactiva Si reactiva no valida transacciones restrictivas
     * @param array $keys_integra_ds Datos para selects
     * @param bool $valida_conf_tipo_persona
     * @param bool $valida_metodo_pago
     * @return array|stdClass
     */
    public function modifica_bd(array $registro, int $id, bool $reactiva = false,
                                array $keys_integra_ds = array('codigo', 'descripcion'),
                                bool  $valida_conf_tipo_persona = true,
                                bool  $valida_metodo_pago = true): array|stdClass
    {
        if ($id <= 0) {
            return $this->error->error(mensaje: 'Error id debe ser mayor a 0', data: $id);
        }
        $registro_previo = $this->registro(registro_id: $id, columnas_en_bruto: true, retorno_obj: true);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener registro previo', data: $registro_previo);
        }

        $registro = $this->registro_cliente_upd(registro: $registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al limpiar campos', data: $registro);
        }

        if (!isset($registro['descripcion'])) {
            $registro['descripcion'] = $registro_previo->descripcion;;
        }

        $r_modifica_bd = parent::modifica_bd(registro: $registro, id: $id, reactiva: $reactiva,
            keys_integra_ds: $keys_integra_ds);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al modificar cliente', data: $r_modifica_bd);
        }

        if ($valida_conf_tipo_persona) {
            $valida = (new _validacion())->valida_conf_tipo_persona(link: $this->link,
                registro: (array)$r_modifica_bd->registro_actualizado);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al validar datos', data: $valida);
            }
        }

        $com_cliente = $this->registro(registro_id: $id, columnas_en_bruto: true, retorno_obj: true);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener cliente', data: $com_cliente);
        }

        if ($valida_metodo_pago) {
            $valida = (new _validacion())->valida_metodo_pago(link: $this->link, registro: (array)$com_cliente);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al validar datos', data: $valida);
            }
        }
        $r_com_sucursal = $this->upd_sucursales(com_cliente: $com_cliente, com_cliente_id: $id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al modificar sucursales', data: $r_com_sucursal);
        }

        return $r_modifica_bd;
    }

    final public function modifica_en_bruto(array $registro, int $id)
    {
        $upd = parent::modifica_bd(registro: $registro, id: $id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al modificar registro', data: $upd);
        }
        return $upd;

    }


    /**
     * Ajusta los elementos para una modificacion
     * @param array $registro Registro en proceso
     * @return array
     */
    private function registro_cliente_upd(array $registro): array
    {
        $registro = $this->init_base(data: $registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al inicializar campo base', data: $registro);
        }

        $registro = $this->inicializa_foraneas(data: $registro, funcion_llamada: 'modifica_bd');
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al inicializar foraneas', data: $registro);
        }

        $registro = $this->limpia_campos(registro: $registro, campos_limpiar: array('dp_pais_id', 'dp_estado_id',
            'dp_cp_id', 'dp_cp_id', 'dp_colonia_postal_id'));
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al limpiar campos', data: $registro);
        }
        return $registro;
    }

    /**
     * Obtiene el registro a modificar de una sucursal
     * @param stdClass $com_cliente Registro de cliente
     * @param int $com_cliente_id Identificador de cliente
     * @param array $sucursal Sucursal a actualizar
     * @return array
     */
    private function row_com_sucursal_upd(stdClass $com_cliente, int $com_cliente_id, array $sucursal): array
    {
        $valida = $this->valida_data_upd_sucursal(com_cliente: $com_cliente, com_cliente_id: $com_cliente_id,
            sucursal: $sucursal);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al al validar datos', data: $valida);
        }

        $keys = array('numero_exterior', 'telefono');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys, registro: $com_cliente);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar cliente', data: $valida);
        }
        if (!isset($com_cliente->numero_interior)) {
            $com_cliente->numero_interior = '';
        }

        $com_sucursal_descripcion = $this->com_sucursal_descripcion(com_cliente: $com_cliente, sucursal: $sucursal);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener com_sucursal_descripcion',
                data: $com_sucursal_descripcion);
        }


        $com_sucursal_upd = $this->com_sucursal_upd(com_cliente: $com_cliente, com_cliente_id: $com_cliente_id,
            com_sucursal_descripcion: $com_sucursal_descripcion, sucursal: $sucursal);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al maquetar row', data: $com_sucursal_upd);
        }

        return $com_sucursal_upd;
    }

    private function seguridad_datos(array $columnas_extra, PDO $link): array|stdClass
    {
        $aplica_seguridad = $this->aplica_seguridad(link: $link);
        if (errores::$error) {
            return (new errores())->error(mensaje: 'Error al obtener grupo de usuario', data: $aplica_seguridad);
        }

        $columnas_extra = $this->integra_columna_seguridad(aplica_seguridad: $aplica_seguridad,columnas_extra:  $columnas_extra);
        if (errores::$error) {
            return (new errores())->error(mensaje: 'Error al integrar columnas_seguridad', data: $columnas_extra);
        }
        $seguridad = new stdClass();
        $seguridad->aplica_seguridad = $aplica_seguridad;
        $seguridad->columnas_extra = $columnas_extra;

        return $seguridad;

    }

    final public function tiene_prospecto(int $com_cliente_id)
    {
        $filtro['com_cliente.id'] = $com_cliente_id;
        $existe = (new com_rel_prospecto_cte(link: $this->link))->existe(filtro: $filtro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error validar si existe prospecto', data: $existe);
        }
        return $existe;

    }


    /**
     * Actualiza los datos de las sucursales
     * @param stdClass $com_cliente Registro de cliente
     * @param int $com_cliente_id Identificador de cliente
     * @param array $sucursal Sucursal a modificar
     * @return array|stdClass
     */
    private function upd_sucursal(stdClass $com_cliente, int $com_cliente_id, array $sucursal): array|stdClass
    {
        $valida = $this->valida_data_upd_sucursal(com_cliente: $com_cliente, com_cliente_id: $com_cliente_id,
            sucursal: $sucursal);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al al validar datos', data: $valida);
        }
        $keys = array('com_sucursal_id');
        $valida = $this->validacion->valida_ids(keys: $keys, registro: $sucursal);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error validar $sucursal', data: $valida);
        }
        $keys = array('numero_exterior', 'telefono');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys, registro: $com_cliente);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar cliente', data: $valida);
        }
        if (!isset($com_cliente->numero_interior)) {
            $com_cliente->numero_interior = '';
        }

        $com_sucursal_upd = $this->row_com_sucursal_upd(com_cliente: $com_cliente, com_cliente_id: $com_cliente_id, sucursal: $sucursal);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al maquetar row', data: $com_sucursal_upd);
        }

        $com_sucursal_modelo = new com_sucursal(link: $this->link);
        $com_sucursal_modelo->transaccion_desde_cliente = true;
        $r_com_sucursal = $com_sucursal_modelo->modifica_bd(registro: $com_sucursal_upd,
            id: $sucursal['com_sucursal_id']);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al modificar sucursales', data: $r_com_sucursal);
        }
        return $r_com_sucursal;
    }

    /**
     * Actualiza las sucursales de un cliente
     * @param stdClass $com_cliente Registro de cliente
     * @param int $com_cliente_id Identificador de cliente
     * @return array
     */
    private function upd_sucursales(stdClass $com_cliente, int $com_cliente_id): array
    {
        if ($com_cliente_id <= 0) {
            return $this->error->error(mensaje: 'Error $com_cliente_id debe ser mayor a 0', data: $com_cliente_id);
        }
        $keys = array('numero_exterior', 'telefono');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys, registro: $com_cliente);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar cliente', data: $valida);
        }
        if (!isset($com_cliente->numero_interior)) {
            $com_cliente->numero_interior = '';
        }

        $r_com_sucursales = array();
        $r_sucursales = (new com_sucursal(link: $this->link))->sucursales(com_cliente_id: $com_cliente_id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener sucursales', data: $r_sucursales);
        }

        $sucursales = $r_sucursales->registros;
        foreach ($sucursales as $sucursal) {
            $valida = $this->valida_data_upd_sucursal(com_cliente: $com_cliente, com_cliente_id: $com_cliente_id,
                sucursal: $sucursal);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al al validar datos', data: $valida);
            }

            $r_com_sucursal = $this->upd_sucursal(com_cliente: $com_cliente, com_cliente_id: $com_cliente_id,
                sucursal: $sucursal);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al modificar sucursales', data: $r_com_sucursal);
            }
            $r_com_sucursales[] = $r_com_sucursal;
        }
        return $r_com_sucursales;
    }

    /**
     * Valida que los datos basicos esten bien integrados para la actualizacion de une sucursal
     * @param array|stdClass $com_cliente Registro de cliente
     * @param array|stdClass $sucursal Registro sucursal
     * @return array|true
     * @version 17.16.0
     */
    private function valida_data_sucursal(array|stdClass $com_cliente, array|stdClass $sucursal): bool|array
    {
        $keys = array('com_sucursal_codigo');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys, registro: $sucursal);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al al validar sucursal', data: $valida);
        }
        $keys = array('razon_social', 'rfc');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys, registro: $com_cliente);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al al validar com_cliente', data: $valida);
        }
        return true;
    }

    /**
     * Valida los elementos para transaccionar sobre una sucursal
     * @param array|stdClass $com_cliente Registro de cliente
     * @param int $com_cliente_id Identificador de cliente
     * @param array|stdClass $sucursal Sucursal a afectar
     * @return array|true
     * @version 17.18.0
     */
    private function valida_data_upd_sucursal(array|stdClass $com_cliente, int $com_cliente_id,
                                              array|stdClass $sucursal): bool|array
    {
        $valida = $this->valida_data_sucursal(com_cliente: $com_cliente, sucursal: $sucursal);;
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al al validar datos', data: $valida);
        }
        $keys = array('com_sucursal_codigo', 'com_tipo_sucursal_descripcion');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys, registro: $sucursal);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar sucursal', data: $valida);
        }
        if ($com_cliente_id <= 0) {
            return $this->error->error(mensaje: 'Error com_cliente_id debe ser mayor a 0', data: $com_cliente_id);
        }
        return true;
    }
}