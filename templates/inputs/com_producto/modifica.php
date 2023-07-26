<?php /** @var \gamboamartin\comercial\controllers\controlador_com_producto $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
    <div id="cat_sat_division_producto_id_ct">
        <?php echo $controlador->inputs->cat_sat_division_producto_id; ?>
    </div>
    <div id="cat_sat_grupo_producto_id_ct">
        <?php echo $controlador->inputs->cat_sat_grupo_producto_id; ?>
    </div>
    <div id="cat_sat_clase_producto_id_ct">
        <?php echo $controlador->inputs->cat_sat_clase_producto_id; ?>
    </div>
    <div id="cat_sat_producto_id_ct">
        <?php echo $controlador->inputs->cat_sat_producto_id; ?>
    </div>

    <div id="cat_sat_producto_id_tmp">
        <?php echo $controlador->inputs->cat_sat_producto; ?>
    </div>
<?php echo $controlador->inputs->codigo; ?>
<?php echo $controlador->inputs->com_tipo_producto_id; ?>
<?php echo $controlador->inputs->descripcion; ?>
<?php echo $controlador->inputs->precio; ?>
<?php echo $controlador->inputs->cat_sat_unidad_id; ?>
<?php echo $controlador->inputs->cat_sat_conf_imps_id; ?>
<?php include (new views())->ruta_templates.'botons/submit/modifica_bd.php';?>