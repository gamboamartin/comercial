<?php /** @var gamboamartin\comercial\controllers\controlador_com_cliente $controlador  controlador en ejecucion */ ?>


<?php echo $controlador->url_servicios['dp_pais']['event_change']; ?>
<?php echo $controlador->url_servicios['dp_estado']['event_full']; ?>
<?php echo $controlador->url_servicios['dp_municipio']['event_full']; ?>
<?php echo $controlador->url_servicios['dp_cp']['event_full']; ?>



<script>

    let dp_municipio_id_sl = $("#dp_municipio_id");


    let cat_sat_metodo_pago_id_sl = $("#cat_sat_metodo_pago_id");
    let cat_sat_forma_pago_id_sl = $("#cat_sat_forma_pago_id");

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




</script>
