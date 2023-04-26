<?php /** @var gamboamartin\comercial\controllers\controlador_com_cliente $controlador  controlador en ejecucion */ ?>


<?php echo $controlador->url_servicios['dp_pais']['event_change']; ?>

<?php echo $controlador->url_servicios['dp_estado']['event_full']; ?>
<?php echo $controlador->url_servicios['dp_municipio']['event_full']; ?>
<?php echo $controlador->url_servicios['dp_cp']['event_full']; ?>
<?php echo $controlador->url_servicios['dp_colonia_postal']['event_full']; ?>

<?php echo $controlador->url_servicios['dp_calle_pertenece']['event_update']; ?>

<script>
    let dp_pais_id_sl = $("#dp_pais_id");
    let dp_estado_id_sl = $("#dp_estado_id");
    let dp_municipio_id_sl = $("#dp_municipio_id");
    let dp_cp_id_sl = $("#dp_cp_id");
    let dp_colonia_postal_id_sl = $("#dp_colonia_postal_id");
    let dp_calle_pertenece_id_sl = $("#dp_calle_pertenece_id");


    let dp_estado_tmp = $("#dp_estado");
    let dp_municipio_tmp = $("#dp_municipio");
    let dp_cp_tmp = $("#dp_cp");
    let dp_colonia_tmp = $("#dp_colonia");
    let dp_calle_tmp = $("#dp_calle");

    let dp_estado_ct = $("#dp_estado").parent().parent();
    let dp_municipio_ct = $("#dp_municipio").parent().parent();
    let dp_cp_ct = $("#dp_cp").parent().parent();
    let dp_colonia_ct = $("#dp_colonia").parent().parent();
    let dp_calle_ct = $("#dp_calle").parent().parent();



    let dp_estado_contenedor = $("#dp_estado_cont");
    let dp_municipio_contenedor = $("#dp_municipio_cont");
    let dp_cp_contenedor = $("#dp_cp_cont");
    let dp_colonia_contenedor = $("#dp_colonia_postal_cont");
    let dp_calle_contenedor = $("#dp_calle_pertenece_cont");

    let dp_estado_contenedor_tmp = $("#dp_estado_cont_tmp");
    let dp_municipio_contenedor_tmp = $("#dp_municipio_cont_tmp");
    let dp_cp_contenedor_tmp = $("#dp_cp_cont_tmp");
    let dp_colonia_contenedor_tmp = $("#dp_colonia_cont_tmp");
    let dp_calle_contenedor_tmp = $("#dp_calle_cont_tmp");


    let dp_pais_id = -1;
    let dp_estado_id = -1;
    let dp_municipio_id = -1;
    let dp_colonia_postal_id = -1;
    let dp_calle_pertenece_id = -1;

    dp_estado_contenedor_tmp.hide();
    dp_municipio_contenedor_tmp.hide();
    dp_cp_contenedor_tmp.hide();
    dp_colonia_contenedor_tmp.hide();
    dp_calle_contenedor_tmp.hide();

    dp_estado_tmp.prop("disabled",true);
    dp_municipio_tmp.prop("disabled",true);
    dp_colonia_tmp.prop("disabled",true);
    dp_cp_tmp.prop("disabled",true);
    dp_calle_tmp.prop("disabled",true);

    dp_pais_id_sl.change(function() {
        dp_pais_id = $(this).val();
        dp_estado_id_sl.prop( "disabled", false );
        dp_municipio_id_sl.prop( "disabled", false );
        dp_cp_id_sl.prop( "disabled", false );
        dp_colonia_postal_id_sl.prop( "disabled", false );
        dp_calle_pertenece_id_sl.prop( "disabled", false );

        dp_estado_contenedor.show();
        dp_municipio_contenedor.show();
        dp_cp_contenedor.show();
        dp_colonia_contenedor.show();
        dp_calle_contenedor.show();

        dp_estado_tmp.prop("disabled",true);
        dp_municipio_tmp.prop("disabled",true);
        dp_colonia_tmp.prop("disabled",true);
        dp_cp_tmp.prop("disabled",true);
        dp_calle_tmp.prop("disabled",true);

        dp_estado_contenedor_tmp.hide();
        dp_municipio_contenedor_tmp.hide();
        dp_cp_contenedor_tmp.hide();
        dp_colonia_contenedor_tmp.hide();
        dp_calle_contenedor_tmp.hide();



        if(dp_pais_id === '253'){
            dp_estado_id_sl.prop( "disabled", true );
            dp_municipio_id_sl.prop( "disabled", true );
            dp_cp_id_sl.prop( "disabled", true );
            dp_colonia_postal_id_sl.prop( "disabled", true );
            dp_calle_pertenece_id_sl.prop( "disabled", true );

            dp_estado_ct.removeClass( "col-sm-4" );
            dp_estado_ct.addClass( "col-sm-6" );

            dp_municipio_ct.removeClass( "col-sm-4" );
            dp_municipio_ct.addClass( "col-sm-6" );

            dp_cp_ct.removeClass( "col-sm-4" );
            dp_cp_ct.addClass( "col-sm-6" );

            dp_estado_contenedor.hide();
            dp_municipio_contenedor.hide();
            dp_cp_contenedor.hide();
            dp_colonia_contenedor.hide();
            dp_calle_contenedor.hide();

            dp_estado_contenedor_tmp.show();
            dp_municipio_contenedor_tmp.show();
            dp_cp_contenedor_tmp.show();
            dp_colonia_contenedor_tmp.show();
            dp_calle_contenedor_tmp.show();

            dp_estado_tmp.prop("disabled",false);
            dp_municipio_tmp.prop("disabled",false);
            dp_colonia_tmp.prop("disabled",false);
            dp_cp_tmp.prop("disabled",false);
            dp_calle_tmp.prop("disabled",false);


        }

    });

    dp_estado_id_sl.change(function() {
        dp_estado_id = $(this).val();

        dp_municipio_id_sl.prop( "disabled", false );
        dp_cp_id_sl.prop( "disabled", false );
        dp_colonia_postal_id_sl.prop( "disabled", false );
        dp_calle_pertenece_id_sl.prop( "disabled", false );

        dp_estado_tmp.prop("disabled",true);
        dp_municipio_tmp.prop("disabled",true);
        dp_colonia_tmp.prop("disabled",true);
        dp_cp_tmp.prop("disabled",true);
        dp_calle_tmp.prop("disabled",true);


        dp_municipio_contenedor.show();
        dp_cp_contenedor.show();
        dp_colonia_contenedor.show();
        dp_calle_contenedor.show();


        dp_municipio_contenedor_tmp.hide();
        dp_cp_contenedor_tmp.hide();
        dp_colonia_contenedor_tmp.hide();
        dp_calle_contenedor_tmp.hide();


        if(dp_estado_id === '101' || dp_estado_id === '99'){

            dp_municipio_id_sl.prop( "disabled", true );
            dp_cp_id_sl.prop( "disabled", true );
            dp_colonia_postal_id_sl.prop( "disabled", true );
            dp_calle_pertenece_id_sl.prop( "disabled", true );

            dp_municipio_ct.removeClass( "col-sm-4" );
            dp_municipio_ct.addClass( "col-sm-6" );

            dp_cp_ct.removeClass( "col-sm-4" );
            dp_cp_ct.addClass( "col-sm-6" );


            dp_municipio_contenedor.hide();
            dp_cp_contenedor.hide();
            dp_colonia_contenedor.hide();
            dp_calle_contenedor.hide();


            dp_municipio_contenedor_tmp.show();
            dp_cp_contenedor_tmp.show();
            dp_colonia_contenedor_tmp.show();
            dp_calle_contenedor_tmp.show();

            dp_municipio_tmp.prop("disabled",false);
            dp_colonia_tmp.prop("disabled",false);
            dp_cp_tmp.prop("disabled",false);
            dp_calle_tmp.prop("disabled",false);
        }

    });


    dp_municipio_id_sl.change(function() {
        dp_municipio_id = $(this).val();

        dp_cp_id_sl.prop( "disabled", false );
        dp_colonia_postal_id_sl.prop( "disabled", false );
        dp_calle_pertenece_id_sl.prop( "disabled", false );

        dp_estado_tmp.prop("disabled",true);
        dp_municipio_tmp.prop("disabled",true);
        dp_colonia_tmp.prop("disabled",true);
        dp_cp_tmp.prop("disabled",true);
        dp_calle_tmp.prop("disabled",true);

        dp_cp_contenedor.show();
        dp_colonia_contenedor.show();
        dp_calle_contenedor.show();


        dp_cp_contenedor_tmp.hide();
        dp_colonia_contenedor_tmp.hide();
        dp_calle_contenedor_tmp.hide();


    if(dp_municipio_id === '2467'){

        dp_cp_id_sl.prop( "disabled", true );
        dp_colonia_postal_id_sl.prop( "disabled", true );
        dp_calle_pertenece_id_sl.prop( "disabled", true );


        dp_cp_ct.removeClass( "col-sm-4" );
        dp_cp_ct.addClass( "col-sm-6" );


        dp_cp_contenedor.hide();
        dp_colonia_contenedor.hide();
        dp_calle_contenedor.hide();


        dp_cp_contenedor_tmp.show();
        dp_colonia_contenedor_tmp.show();
        dp_calle_contenedor_tmp.show();

        dp_colonia_tmp.prop("disabled",false);
        dp_cp_tmp.prop("disabled",false);
        dp_calle_tmp.prop("disabled",false);

    }

    });

    dp_cp_id_sl.change(function() {
        dp_cp_id = $(this).val();

        dp_colonia_postal_id_sl.prop( "disabled", false );
        dp_calle_pertenece_id_sl.prop( "disabled", false );

        dp_estado_tmp.prop("disabled",true);
        dp_municipio_tmp.prop("disabled",true);
        dp_colonia_tmp.prop("disabled",true);
        dp_cp_tmp.prop("disabled",true);
        dp_calle_tmp.prop("disabled",true);



        dp_colonia_contenedor.show();
        dp_calle_contenedor.show();

        dp_colonia_contenedor_tmp.hide();
        dp_calle_contenedor_tmp.hide();
        dp_cp_contenedor_tmp.hide();

        if(dp_cp_id === '11'){

            dp_colonia_postal_id_sl.prop( "disabled", true );
            dp_calle_pertenece_id_sl.prop( "disabled", true );

            dp_cp_ct.removeClass( "col-sm-4" );
            dp_cp_ct.removeClass( "col-sm-6" );
            dp_cp_ct.removeClass( "col-sm-12" );
            dp_cp_ct.addClass( "col-sm-6" );

            dp_colonia_ct.removeClass( "col-sm-4" );
            dp_colonia_ct.removeClass( "col-sm-6" );
            dp_colonia_ct.removeClass( "col-sm-12" );
            dp_colonia_ct.addClass( "col-sm-6" );

            dp_calle_ct.removeClass( "col-sm-4" );
            dp_calle_ct.removeClass( "col-sm-6" );
            dp_calle_ct.removeClass( "col-sm-12" );
            dp_calle_ct.addClass( "col-sm-6" );


            dp_colonia_contenedor.hide();
            dp_calle_contenedor.hide();

             dp_cp_contenedor_tmp.show();
            dp_colonia_contenedor_tmp.show();
            dp_calle_contenedor_tmp.show();

            dp_colonia_tmp.prop("disabled",false);
            dp_cp_tmp.prop("disabled",false);
            dp_calle_tmp.prop("disabled",false);
        }
    });

    dp_colonia_postal_id_sl.change(function() {
        dp_colonia_postal_id = $(this).val();

        dp_calle_pertenece_id_sl.prop( "disabled", false );

        dp_estado_tmp.prop("disabled",true);
        dp_municipio_tmp.prop("disabled",true);
        dp_colonia_tmp.prop("disabled",true);
        dp_cp_tmp.prop("disabled",true);
        dp_calle_tmp.prop("disabled",true);

        dp_calle_contenedor.show();

        dp_calle_contenedor_tmp.hide();
        dp_colonia_contenedor_tmp.hide();

        if(dp_colonia_postal_id === '105'){


            dp_calle_pertenece_id_sl.prop( "disabled", true );
            dp_cp_tmp.prop( "disabled", true );


            dp_calle_ct.removeClass( "col-sm-6" );
            dp_calle_ct.removeClass( "col-sm-4" );
            dp_calle_ct.addClass( "col-sm-12" );


            dp_calle_contenedor.hide();

            dp_colonia_contenedor_tmp.show();
            dp_calle_contenedor_tmp.show();

            dp_colonia_tmp.prop("disabled",false);
            dp_calle_tmp.prop("disabled",false);

        }
    });

    dp_calle_pertenece_id_sl.change(function() {
        dp_calle_pertenece_id = $(this).val();

        dp_calle_contenedor_tmp.hide();

        dp_estado_tmp.prop("disabled",true);
        dp_municipio_tmp.prop("disabled",true);
        dp_colonia_tmp.prop("disabled",true);
        dp_cp_tmp.prop("disabled",true);
        dp_calle_tmp.prop("disabled",true);


        if(dp_calle_pertenece_id === '100'){

            dp_cp_tmp.prop( "disabled", true );
            dp_calle_ct.removeClass( "col-sm-4" );
            dp_calle_ct.removeClass( "col-sm-6" );
            dp_calle_ct.removeClass( "col-sm-12" );
            dp_calle_ct.addClass( "col-sm-12" );
            dp_calle_contenedor_tmp.show();

            dp_colonia_tmp.prop("disabled",false);
            dp_calle_tmp.prop("disabled",false);
        }
});

</script>
