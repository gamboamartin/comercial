<?php /** @var \gamboamartin\comercial\controllers\controlador_com_tipo_producto $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<main class="main section-color-primary">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <?php include (new views())->ruta_templates."head/title.php"; ?>

                <?php include (new views())->ruta_templates."mensajes.php"; ?>

                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">
                    <form method="post" action="<?php echo $controlador->link_com_producto_alta_bd; ?>" class="form-additional">
                        <?php include (new views())->ruta_templates."head/subtitulo.php"; ?>

                        <?php echo $controlador->inputs->select->cat_sat_tipo_producto_id; ?>
                        <?php echo $controlador->inputs->select->cat_sat_division_producto_id; ?>
                        <?php echo $controlador->inputs->select->cat_sat_grupo_producto_id; ?>
                        <?php echo $controlador->inputs->select->cat_sat_clase_producto_id; ?>
                        <?php echo $controlador->inputs->select->cat_sat_producto_id; ?>
                        <?php echo $controlador->inputs->com_producto_codigo; ?>
                        <?php echo $controlador->inputs->select->com_tipo_producto_id; ?>
                        <?php echo $controlador->inputs->com_producto_descripcion; ?>
                        <?php echo $controlador->inputs->select->cat_sat_unidad_id; ?>
                        <?php echo $controlador->inputs->select->cat_sat_obj_imp_id; ?>

                        <?php echo $controlador->inputs->hidden_row_id; ?>
                        <?php echo $controlador->inputs->hidden_seccion_retorno; ?>
                        <?php echo $controlador->inputs->hidden_id_retorno; ?>
                        <div class="controls">
                            <button type="submit" class="btn btn-success" value="productos" name="btn_action_next">Alta</button><br>
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

