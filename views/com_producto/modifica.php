<?php /** @var gamboamartin\comercial\controllers\controlador_com_producto $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<main class="main section-color-primary">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <?php include (new views())->ruta_templates."head/title.php"; ?>

                <?php include (new views())->ruta_templates."mensajes.php"; ?>

                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">
                    <form method="post" action="<?php echo $controlador->link_com_producto_modifica_bd; ?>" class="form-additional">
                        <?php include (new views())->ruta_templates."head/subtitulo.php"; ?>

                        <?php echo $controlador->inputs->com_tipo_producto_id; ?>
                        <?php echo $controlador->inputs->cat_sat_producto; ?>
                        <?php echo $controlador->inputs->cat_sat_unidad_id; ?>
                        <?php echo $controlador->inputs->cat_sat_obj_imp_id; ?>
                        <?php echo $controlador->inputs->codigo; ?>
                        <?php echo $controlador->inputs->precio; ?>
                        <?php echo $controlador->inputs->descripcion; ?>

                        <div class="controls">
                            <button type="submit" class="btn btn-success" value="modifica" name="btn_action_next">Modifica</button><br>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-12"">
                <div class="widget  widget-box box-container">
                    <label>Tipo de Producto SAT: </label>
                    <?php echo $controlador->registro['cat_sat_tipo_producto_codigo']; ?>
                    <?php echo $controlador->registro['cat_sat_tipo_producto_descripcion']; ?>
                    <label>Division SAT: </label>
                    <?php echo $controlador->registro['cat_sat_division_producto_codigo']; ?>
                    <?php echo $controlador->registro['cat_sat_division_producto_descripcion']; ?>
                    <label>Grupo SAT: </label>
                    <?php echo $controlador->registro['cat_sat_grupo_producto_codigo']; ?>
                    <?php echo $controlador->registro['cat_sat_grupo_producto_descripcion']; ?>
                    <label>Clase SAT: </label>
                    <?php echo $controlador->registro['cat_sat_clase_producto_codigo']; ?>
                    <?php echo $controlador->registro['cat_sat_clase_producto_descripcion']; ?>
                    <label>Producto SAT: </label>
                    <?php echo $controlador->registro['cat_sat_producto_codigo']; ?>
                    <?php echo $controlador->registro['cat_sat_producto_descripcion']; ?>
                </div>

            </div>
        </div>
    </div>
</main>


