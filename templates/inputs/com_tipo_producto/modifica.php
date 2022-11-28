<?php /** @var \gamboamartin\comercial\controllers\controlador_com_tipo_producto $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->codigo; ?>
<?php echo $controlador->inputs->descripcion; ?>
<?php echo $controlador->inputs->cat_sat_moneda_id; ?>
<?php include (new views())->ruta_templates.'botons/submit/modifica_bd.php';?>