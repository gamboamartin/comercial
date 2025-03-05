<?php /** @var gamboamartin\comercial\controllers\controlador_com_cliente $controlador  controlador en ejecucion */ ?>


<?php echo $controlador->url_servicios['dp_pais']['event_change']; ?>
<?php echo $controlador->url_servicios['dp_estado']['event_full']; ?>
<?php echo $controlador->url_servicios['dp_municipio']['event_full']; ?>
<?php echo $controlador->url_servicios['dp_cp']['event_full']; ?>



<script>

    let dp_municipio_id_sl = $("#dp_municipio_id");


    let cat_sat_metodo_pago_id_sl = $("#cat_sat_metodo_pago_id");
    let cat_sat_forma_pago_id_sl = $("#cat_sat_forma_pago_id");
    let cat_sat_tipo_persona_id_sl = $("#cat_sat_tipo_persona_id");
    let cat_sat_regimen_fiscal_id_sl = $("#cat_sat_regimen_fiscal_id");
    let dp_pais_final_id_sl = $("#dp_pais_id");
    let dp_estado_final_id_sl = $("#dp_estado_id");
    let dp_municipio_final_id_sl = $("#dp_municipio_id");

    let metodo_pago_permitido = <?php echo(json_encode((new \gamboamartin\cat_sat\models\_validacion())->metodo_pago_permitido)); ?>;
    let formas_pagos_permitidas = [];

    let cat_sat_metodo_pago_codigo = '';
    let cat_sat_forma_pago_codigo = '';

    dp_municipio_id_sl.prop('required',true);

    cat_sat_metodo_pago_id_sl.change(function() {
        cat_sat_metodo_pago_codigo = $('option:selected', this).data("cat_sat_metodo_pago_codigo");
        formas_pagos_permitidas = metodo_pago_permitido[cat_sat_metodo_pago_codigo];

    });

    cat_sat_forma_pago_id_sl.change(function() {
        cat_sat_forma_pago_codigo = $('option:selected', this).data("cat_sat_forma_pago_codigo");
        let permitido = false;
        $.each(formas_pagos_permitidas, function(i, item) {
            if(item == cat_sat_forma_pago_codigo){
                permitido = true;
            }
        });

        if(!permitido){
            cat_sat_forma_pago_id_sl.val(null);
            $('#myModal').modal('show');
        }

    });

    $("#form_com_cliente_alta").submit(function() {
        if(dp_municipio_id_sl.val() === '-1'){
            alert('Seleccione un municipio');
            return false;
        }
    });

    let txt_codigo = $("#codigo");
    let txt_rfc = $("#rfc");
    let txt_razon_social = $("#razon_social");
    let txt_tipo_persona = $("#tipo_persona");
    let txt_regimen_fiscal = $("#regimen_fiscal");
    let txt_estado = $("#estado");
    let txt_municipio = $("#municipio");
    let txt_cp = $("#cp");
    let txt_colonia = $("#colonia");
    let txt_calle = $("#calle");
    let txt_numero_exterior = $("#numero_exterior");
    let txt_numero_interior = $("#numero_interior");

    document.getElementById('documento').addEventListener('change', function (event) {
    var file = event.target.files[0];

    if (!file) {
        alert('No se seleccionó ningún archivo.');
        event.target.value = '';
        return;
    }

    if (file.type !== 'application/pdf') {
        alert('El archivo seleccionado no es un PDF.');
        event.target.value = '';
        return;
    }

    var loaderOverlay = $('<div class="loader-overlay"><div class="loader"></div></div>');
    $('body').append(loaderOverlay);

    var formData = new FormData();
    formData.append('documento', this.files[0]);

    let url = get_url("com_cliente", "leer_qr", {registro_id: -1});

    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if(result.status === 'error'){
            alert(result.mensaje);
            loaderOverlay.remove();
            return;
        }

        data = result.data;
        console.log(data);
        persona = data.datos_identificacion;

        let url = get_url("cat_sat_tipo_persona", "get_tipo_persona", {tipo_persona: data.tipo_persona });
        get_data(url, function (data_tp) {
            cat_sat_tipo_persona_id_sl.val(data_tp.cat_sat_tipo_persona_id);
            cat_sat_tipo_persona_id_sl.selectpicker('refresh');
        });

        let url_rf = get_url("cat_sat_regimen_fiscal", "get_regimen_fiscal",
            {regimen_fiscal: data.datos_fiscales.regimen });
        get_data(url_rf, function (data_rf) {
            cat_sat_regimen_fiscal_id_sl.val(data_rf.cat_sat_regimen_fiscal_id);
            cat_sat_regimen_fiscal_id_sl.selectpicker('refresh');
        });

        let url_ubi = get_url("dp_municipio", "get_ubicacion_sat",
        {municipio: data.datos_ubicacion.municipio_o_delegacion});

        get_data(url_ubi, function (data_mun) {

            dp_pais_final_id_sl.val(data_mun.dp_pais_id);
            dp_pais_final_id_sl.selectpicker('refresh');

            let url_est = get_url("dp_estado","get_estado", {dp_pais_id: data_mun.dp_pais_id});

            get_data(url_est, function (data_est) {
                dp_estado_final_id_sl.empty();
                integra_new_option(dp_estado_final_id_sl,'Seleccione un estado','-1');

                $.each(data_est.registros, function( index, dp_estado ) {
                    integra_new_option(dp_estado_final_id_sl,dp_estado.dp_estado_descripcion,dp_estado.dp_estado_id,
                    "data-dp_estado_predeterminado",dp_estado.dp_estado_predeterminado);
                });

                dp_estado_final_id_sl.val(data_mun.dp_estado_id);
                dp_estado_final_id_sl.selectpicker('refresh');
            });

            let url_mun = get_url("dp_municipio","get_municipio", {dp_estado_id: data_mun.dp_estado_id});

            get_data(url_mun, function (data_mund) {
                dp_municipio_final_id_sl.empty();

                integra_new_option(dp_municipio_final_id_sl,'Seleccione un municipio','-1');

                $.each(data_mund.registros, function( index, dp_municipio ) {
                    integra_new_option(dp_municipio_final_id_sl,dp_municipio.dp_municipio_descripcion,dp_municipio.dp_municipio_id,
                    "data-dp_municipio_predeterminado",dp_municipio.dp_municipio_predeterminado);
                });

                dp_municipio_final_id_sl.val(data_mun.dp_municipio_id);
                dp_municipio_final_id_sl.selectpicker('refresh');
            });
        });

        let razon_social = "";

        if (data.tipo_persona === 'PERSONA FISICA') {
            razon_social = persona.nombre + ' ' + persona.apellido_paterno + ' ' + persona.apellido_materno;
        } else if (data.tipo_persona === 'PERSONA MORAL') {
            razon_social = persona.denominacion_o_razon_social;
        }

        txt_codigo.val(data.rfc);
        txt_rfc.val(data.rfc);
        txt_razon_social.val(razon_social);
        txt_tipo_persona.val(data.tipo_persona);
        txt_regimen_fiscal.val(data.datos_fiscales.regimen);
        txt_estado.val(data.datos_ubicacion.entidad_federativa);
        txt_municipio.val(data.datos_ubicacion.municipio_o_delegacion);
        txt_cp.val(data.datos_ubicacion.cp);
        txt_colonia.val(data.datos_ubicacion.colonia);
        txt_calle.val(data.datos_ubicacion.nombre_de_la_vialidad);
        txt_numero_exterior.val(data.datos_ubicacion.numero_exterior);
        txt_numero_interior.val(data.datos_ubicacion.numero_interior);

        loaderOverlay.remove();
    })
    .catch(error => {
        alert('Error al leer el documento.');
        console.error("Error procesando la respuesta:", error);
        loaderOverlay.remove();
    });
});


</script>
