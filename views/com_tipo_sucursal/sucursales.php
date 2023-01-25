<?php /** @var \gamboamartin\comercial\controllers\controlador_com_tipo_sucursal $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<main class="main section-color-primary">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <?php include (new views())->ruta_templates."head/title.php"; ?>

                <?php include (new views())->ruta_templates."mensajes.php"; ?>

                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">
                    <form method="post" action="<?php echo $controlador->link_com_sucursal_alta_bd; ?>" class="form-additional">
                        <?php include (new views())->ruta_templates."head/subtitulo.php"; ?>

                        <?php echo $controlador->inputs->select->com_tipo_sucursal_id; ?>
                        <?php echo $controlador->inputs->com_sucursal_codigo; ?>
                        <?php echo $controlador->inputs->com_sucursal_nombre_contacto; ?>
                        <?php echo $controlador->inputs->select->com_cliente_id; ?>
                        <?php echo $controlador->inputs->select->dp_pais_id; ?>
                        <?php echo $controlador->inputs->select->dp_estado_id; ?>
                        <?php echo $controlador->inputs->select->dp_municipio_id; ?>
                        <?php echo $controlador->inputs->select->dp_cp_id; ?>
                        <?php echo $controlador->inputs->select->dp_colonia_postal_id; ?>
                        <?php echo $controlador->inputs->select->dp_calle_pertenece_id; ?>
                        <?php echo $controlador->inputs->com_sucursal_numero_exterior; ?>
                        <?php echo $controlador->inputs->com_sucursal_numero_interior; ?>
                        <?php echo $controlador->inputs->com_sucursal_telefono_1; ?>
                        <?php echo $controlador->inputs->com_sucursal_telefono_2; ?>
                        <?php echo $controlador->inputs->com_sucursal_telefono_3; ?>

                        <?php echo $controlador->inputs->hidden_row_id; ?>
                        <?php echo $controlador->inputs->hidden_seccion_retorno; ?>
                        <?php echo $controlador->inputs->hidden_id_retorno; ?>
                        <div class="controls">
                            <button type="submit" class="btn btn-success" value="sucursales" name="btn_action_next">Alta</button><br>
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

