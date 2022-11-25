<?php
namespace html;

use gamboamartin\comercial\controllers\controlador_com_sucursal;
use gamboamartin\comercial\models\com_sucursal;
use gamboamartin\direccion_postal\models\dp_calle_pertenece;
use gamboamartin\direccion_postal\models\dp_pais;
use gamboamartin\errores\errores;
use gamboamartin\system\html_controler;
use gamboamartin\template\directivas;
use PDO;
use stdClass;

class com_sucursal_html extends html_controler {

    private function asigna_inputs_alta(controlador_com_sucursal $controler, array|stdClass $inputs): array|stdClass
    {
        $controler->inputs->select = new stdClass();

        $controler->inputs->select->dp_colonia_id = $inputs['selects']->dp_colonia_id;
        $controler->inputs->select->dp_cp_id = $inputs['selects']->dp_cp_id;
        $controler->inputs->select->dp_municipio_id = $inputs['selects']->dp_municipio_id;
        $controler->inputs->select->dp_pais_id = $inputs['selects']->dp_pais_id;
        $controler->inputs->select->dp_calle_pertenece_id = $inputs['selects']->dp_calle_pertenece_id;
        $controler->inputs->select->dp_estado_id = $inputs['selects']->dp_estado_id;
        $controler->inputs->select->com_cliente_id = $inputs['selects']->com_cliente_id;
        $controler->inputs->select->com_tipo_sucursal_id = $inputs['selects']->com_tipo_sucursal_id;

        $controler->inputs->telefono_3 = $inputs['inputs']->telefono_3;
        $controler->inputs->telefono_2 = $inputs['inputs']->telefono_2;
        $controler->inputs->telefono_1 = $inputs['inputs']->telefono_1;
        $controler->inputs->numero_exterior = $inputs['inputs']->numero_exterior;
        $controler->inputs->numero_interior = $inputs['inputs']->numero_interior;
        $controler->inputs->nombre_contacto = $inputs['inputs']->nombre_contacto;
        return $controler->inputs;
    }

    private function asigna_inputs_modifica(controlador_com_sucursal $controler, array|stdClass $inputs): array|stdClass
    {
        $controler->inputs->select = new stdClass();

        $controler->inputs->select->dp_colonia_id = $inputs->selects->dp_colonia_id;
        $controler->inputs->select->dp_cp_id = $inputs->selects->dp_cp_id;
        $controler->inputs->select->dp_municipio_id = $inputs->selects->dp_municipio_id;
        $controler->inputs->select->dp_pais_id = $inputs->selects->dp_pais_id;
        $controler->inputs->select->dp_calle_pertenece_id = $inputs->selects->dp_calle_pertenece_id;
        $controler->inputs->select->dp_estado_id = $inputs->selects->dp_estado_id;
        $controler->inputs->select->com_cliente_id = $inputs->selects->com_cliente_id;
        $controler->inputs->select->com_tipo_sucursal_id = $inputs->selects->com_tipo_sucursal_id;

        $controler->inputs->telefono_3 = $inputs->texts->telefono_3;
        $controler->inputs->telefono_2 = $inputs->texts->telefono_2;
        $controler->inputs->telefono_1 = $inputs->texts->telefono_1;
        $controler->inputs->numero_exterior = $inputs->texts->numero_exterior;
        $controler->inputs->numero_interior = $inputs->texts->numero_interior;
        $controler->inputs->nombre_contacto = $inputs->texts->nombre_contacto;

        return $controler->inputs;
    }

    public function genera_inputs_alta(controlador_com_sucursal $controler, array $keys_selects,PDO $link): array|stdClass
    {
        $inputs = $this->init_alta2(row_upd: $controler->row_upd, modelo: $controler->modelo, keys_selects: $keys_selects);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar inputs',data:  $inputs);

        }
        $inputs_asignados = $this->asigna_inputs_alta(controler:$controler, inputs: $inputs);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al asignar inputs',data:  $inputs_asignados);
        }

        return $inputs_asignados;
    }

    private function genera_inputs_modifica(controlador_com_sucursal $controler,PDO $link): array|stdClass
    {
        $inputs = $this->init_modifica(link: $link, row_upd: $controler->row_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar inputs',data:  $inputs);

        }
        $inputs_asignados = $this->asigna_inputs_modifica(controler:$controler, inputs: $inputs);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al asignar inputs',data:  $inputs_asignados);
        }

        return $inputs_asignados;
    }

    private function init_modifica(PDO $link, stdClass $row_upd): array|stdClass
    {

        $selects = $this->selects_modifica(link: $link, row_upd: $row_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar selects',data:  $selects);
        }

        $texts = $this->texts_modifica(row_upd: $row_upd, value_vacio: false);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar texts',data:  $texts);
        }

        $alta_inputs = new stdClass();
        $alta_inputs->selects = $selects;
        $alta_inputs->texts = $texts;

        return $alta_inputs;
    }


    public function input_numero_interior(int $cols, stdClass $row_upd, bool $value_vacio): array|string
    {
        $valida = $this->directivas->valida_cols(cols: $cols);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar columnas', data: $valida);
        }

        $html =$this->directivas->input_text_required(disabled: false,name: 'numero_interior',place_holder: 'Numero interior',
            row_upd: $row_upd, value_vacio: $value_vacio);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input', data: $html);
        }

        $div = $this->directivas->html->div_group(cols: $cols,html:  $html);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar div', data: $div);
        }

        return $div;
    }

    public function input_nombre_contacto(int $cols, stdClass $row_upd, bool $value_vacio): array|string
    {
        $valida = $this->directivas->valida_cols(cols: $cols);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar columnas', data: $valida);
        }

        $html =$this->directivas->input_text_required(disabled: false,name: 'nombre_contacto',place_holder: 'Nombre contacto',
            row_upd: $row_upd, value_vacio: $value_vacio);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input', data: $html);
        }

        $div = $this->directivas->html->div_group(cols: $cols,html:  $html);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar div', data: $div);
        }

        return $div;
    }

    public function input_numero_exterior(int $cols, stdClass $row_upd, bool $value_vacio): array|string
    {
        $valida = $this->directivas->valida_cols(cols: $cols);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar columnas', data: $valida);
        }

        $html =$this->directivas->input_text_required(disabled: false,name: 'numero_exterior',place_holder: 'Numero exterior',
            row_upd: $row_upd, value_vacio: $value_vacio);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input', data: $html);
        }

        $div = $this->directivas->html->div_group(cols: $cols,html:  $html);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar div', data: $div);
        }

        return $div;
    }

    public function input_telefono_1(int $cols, stdClass $row_upd, bool $value_vacio): array|string
    {
        $valida = $this->directivas->valida_cols(cols: $cols);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar columnas', data: $valida);
        }

        $html =$this->directivas->input_text_required(disabled: false,name: 'telefono_1',place_holder: 'Telefono 1',
            row_upd: $row_upd, value_vacio: $value_vacio);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input', data: $html);
        }

        $div = $this->directivas->html->div_group(cols: $cols,html:  $html);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar div', data: $div);
        }

        return $div;
    }

    public function input_telefono_2(int $cols, stdClass $row_upd, bool $value_vacio): array|string
    {
        $valida = $this->directivas->valida_cols(cols: $cols);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar columnas', data: $valida);
        }

        $html =$this->directivas->input_text_required(disabled: false,name: 'telefono_2',place_holder: 'Telefono 2',
            row_upd: $row_upd, value_vacio: $value_vacio);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input', data: $html);
        }

        $div = $this->directivas->html->div_group(cols: $cols,html:  $html);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar div', data: $div);
        }

        return $div;
    }

    public function input_telefono_3(int $cols, stdClass $row_upd, bool $value_vacio): array|string
    {
        $valida = $this->directivas->valida_cols(cols: $cols);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar columnas', data: $valida);
        }

        $html =$this->directivas->input_text_required(disabled: false,name: 'telefono_3',place_holder: 'Telefono 3',
            row_upd: $row_upd, value_vacio: $value_vacio);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input', data: $html);
        }

        $div = $this->directivas->html->div_group(cols: $cols,html:  $html);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar div', data: $div);
        }

        return $div;
    }

    public function inputs_com_sucursal(controlador_com_sucursal $controlador): array|stdClass
    {
        $inputs = $this->genera_inputs_modifica(controler: $controlador, link: $controlador->link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar inputs',data:  $inputs);
        }
        return $inputs;
    }


    /**
     * @param int $cols No columnas css
     * @param bool $con_registros si no con registros entonces options vacio
     * @param int|null $id_selected identificador de la sucursal en caso de un selected
     * @param PDO $link Conexion a la base de datos
     * @param string $label Etiqueta a mostrar por default Sucursal
     * @return array|string
     */
    public function select_com_sucursal_id(int $cols, bool $con_registros, int|null $id_selected, PDO $link,
                                           bool $disabled, string $label ='Sucursal'): array|string
    {
        $valida = (new directivas(html:$this->html_base))->valida_cols(cols:$cols);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar cols', data: $valida);
        }
        $modelo = new com_sucursal(link: $link);

        if(is_null($id_selected)){
            $id_selected = -1;
        }

        $select = $this->select_catalogo(cols:$cols,con_registros:$con_registros,id_selected:$id_selected,
            modelo: $modelo, disabled: $disabled,label: $label,required: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar select', data: $select);
        }
        return $select;
    }

    protected function texts_alta(stdClass $row_upd, bool $value_vacio, stdClass $params = new stdClass()): array|stdClass
    {
        $texts = new stdClass();

        $in_numero_exterior = $this->input_numero_exterior(cols: 6,row_upd:  $row_upd,value_vacio:  $value_vacio);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $in_numero_exterior);
        }
        $texts->numero_exterior = $in_numero_exterior;

        $in_numero_interior = $this->input_numero_interior(cols: 6,row_upd:  $row_upd,value_vacio:  $value_vacio);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $in_numero_interior);
        }
        $texts->numero_interior = $in_numero_interior;

        $in_telefono1 = $this->input_telefono_1(cols: 6,row_upd:  $row_upd,value_vacio:  $value_vacio);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $in_telefono1);
        }
        $texts->telefono_1 = $in_telefono1;

        $in_telefono2 = $this->input_telefono_2(cols: 6,row_upd:  $row_upd,value_vacio:  $value_vacio);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $in_telefono2);
        }
        $texts->telefono_2 = $in_telefono2;

        $in_telefono3 = $this->input_telefono_3(cols: 6,row_upd:  $row_upd,value_vacio:  $value_vacio);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $in_telefono3);
        }
        $texts->telefono_3 = $in_telefono3;

        $in_nombre_contacto = $this->input_nombre_contacto(cols: 12,row_upd:  $row_upd,value_vacio:  $value_vacio);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $in_nombre_contacto);
        }
        $texts->nombre_contacto = $in_nombre_contacto;

        return $texts;
    }

    private function selects_modifica(PDO $link, stdClass $row_upd): array|stdClass
    {

        $selects = new stdClass();
        $dp_calle_pertenece = new dp_calle_pertenece(link: $link);

        $r_dp_calle_pertenece = $dp_calle_pertenece->registro(registro_id: $row_upd->dp_calle_pertenece_id);

        $select = (new com_cliente_html(html:$this->html_base))->select_com_cliente_id(
            cols: 6, con_registros:true, id_selected:$row_upd->com_cliente_id,link: $link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar select',data:  $select);
        }

        $selects->com_cliente_id = $select;

        $select = (new com_tipo_sucursal_html(html:$this->html_base))->select_com_tipo_sucursal_id(
            cols: 6, con_registros:true, id_selected:$row_upd->com_tipo_sucursal_id,link: $link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar select',data:  $select);
        }
        $selects->com_tipo_sucursal_id = $select;

        $select = (new dp_pais_html(html:$this->html_base))->select_dp_pais_id(
            cols: 6, con_registros:true, id_selected:$r_dp_calle_pertenece['dp_pais_id'],link: $link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar select',data:  $select);
        }

        $selects->dp_pais_id = $select;



        $select = (new dp_estado_html(html:$this->html_base))->select_dp_estado_id(
            cols: 6, con_registros:true, id_selected:$r_dp_calle_pertenece['dp_estado_id'],link: $link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar select',data:  $select);
        }
        $selects->dp_estado_id = $select;

        $select = (new dp_municipio_html(html:$this->html_base))->select_dp_municipio_id(
            cols: 6, con_registros:true, id_selected:$r_dp_calle_pertenece['dp_municipio_id'],link: $link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar select',data:  $select);
        }
        $selects->dp_municipio_id = $select;

        $select = (new dp_cp_html(html:$this->html_base))->select_dp_cp_id(
            cols: 6, con_registros:true, id_selected:$r_dp_calle_pertenece['dp_cp_id'],link: $link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar select',data:  $select);
        }
        $selects->dp_cp_id = $select;


        $select = (new dp_calle_pertenece_html(html:$this->html_base))->select_dp_calle_pertenece_id(
            cols: 6, con_registros:true, id_selected:$r_dp_calle_pertenece['dp_calle_pertenece_id'],link: $link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar select',data:  $select);
        }
        $selects->dp_calle_pertenece_id = $select;

        $select = (new dp_colonia_html(html:$this->html_base))->select_dp_colonia_id(
            cols: 6, con_registros:true, id_selected:$r_dp_calle_pertenece['dp_colonia_id'],link: $link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar select',data:  $select);
        }
        $selects->dp_colonia_id = $select;

        return $selects;
    }

    protected function texts_modifica(stdClass $row_upd, bool $value_vacio): array|stdClass
    {

        $texts = new stdClass();

        $in_nombre_contacto = $this->input_nombre_contacto(cols: 6,row_upd:  $row_upd,value_vacio:  $value_vacio);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $in_nombre_contacto);
        }
        $texts->nombre_contacto = $in_nombre_contacto;

        $in_numero_exterior = $this->input_numero_exterior(cols: 6,row_upd:  $row_upd,value_vacio:  $value_vacio);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $in_numero_exterior);
        }
        $texts->numero_exterior = $in_numero_exterior;

        $in_numero_interior = $this->input_numero_interior(cols: 6,row_upd:  $row_upd,value_vacio:  $value_vacio);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $in_numero_interior);
        }
        $texts->numero_interior = $in_numero_interior;

        $in_telefono_1 = $this->input_telefono_1(cols: 6,row_upd:  $row_upd,value_vacio:  $value_vacio);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $in_telefono_1);
        }
        $texts->telefono_1 = $in_telefono_1;

        $in_telefono_2 = $this->input_telefono_2(cols: 6,row_upd:  $row_upd,value_vacio:  $value_vacio);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $in_telefono_2);
        }
        $texts->telefono_2 = $in_telefono_2;

        $in_telefono_3 = $this->input_telefono_3(cols: 6,row_upd:  $row_upd,value_vacio:  $value_vacio);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $in_telefono_3);
        }
        $texts->telefono_3 = $in_telefono_3;
        return $texts;
    }
}
