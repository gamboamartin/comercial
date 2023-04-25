<?php /** @var \gamboamartin\comercial\controllers\controlador_com_cliente $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->com_producto_id; ?>
<?php echo $controlador->inputs->cat_sat_producto; ?>
<?php include (new views())->ruta_templates.'botons/submit/modifica_bd.php';?>



