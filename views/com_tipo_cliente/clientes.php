<?php /** @var \gamboamartin\comercial\controllers\controlador_com_tipo_cliente $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<main class="main section-color-primary">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <?php include (new views())->ruta_templates."head/title.php"; ?>

                <?php include (new views())->ruta_templates."mensajes.php"; ?>

                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">
                    <form method="post" action="<?php echo $controlador->link_com_cliente_alta_bd; ?>" class="form-additional">
                        <?php include (new views())->ruta_templates."head/subtitulo.php"; ?>

                        <?php echo $controlador->inputs->com_tipo_cliente_id; ?>
                        <?php echo $controlador->inputs->codigo; ?>
                        <?php echo $controlador->inputs->razon_social; ?>
                        <?php echo $controlador->inputs->rfc; ?>
                        <?php echo $controlador->inputs->telefono; ?>
                        <?php echo $controlador->inputs->cat_sat_regimen_fiscal_id; ?>
                        <?php echo $controlador->inputs->dp_pais_id; ?>
                        <?php echo $controlador->inputs->dp_estado_id; ?>
                        <?php echo $controlador->inputs->dp_municipio_id; ?>
                        <?php echo $controlador->inputs->dp_cp_id; ?>
                        <?php echo $controlador->inputs->dp_colonia_postal_id; ?>
                        <?php echo $controlador->inputs->dp_calle_pertenece_id; ?>
                        <?php echo $controlador->inputs->numero_exterior; ?>
                        <?php echo $controlador->inputs->numero_interior; ?>
                        <?php echo $controlador->inputs->cat_sat_uso_cfdi_id; ?>
                        <?php echo $controlador->inputs->cat_sat_metodo_pago_id; ?>
                        <?php echo $controlador->inputs->cat_sat_forma_pago_id; ?>
                        <?php echo $controlador->inputs->cat_sat_tipo_de_comprobante_id; ?>
                        <?php echo $controlador->inputs->cat_sat_moneda_id; ?>

                        <?php echo $controlador->inputs->hidden_row_id; ?>
                        <?php echo $controlador->inputs->hidden_seccion_retorno; ?>
                        <?php echo $controlador->inputs->hidden_id_retorno; ?>
                        <div class="controls">
                            <button type="submit" class="btn btn-success" value="clientes" name="btn_action_next">Alta</button><br>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<main class="main section-color-primary">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="widget widget-box box-container widget-mylistings">
                    <?php echo $controlador->contenido_table; ?>
                </div> <!-- /. widget-table-->
            </div><!-- /.center-content -->
        </div>
    </div>
</main>

