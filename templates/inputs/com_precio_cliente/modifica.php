<?php /** @var gamboamartin\comercial\controllers\controlador_com_email_cte $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->com_producto_id; ?>
<?php echo $controlador->inputs->com_cliente_id; ?>
<?php echo $controlador->inputs->cat_sat_conf_imps_id; ?>
<?php echo $controlador->inputs->precio; ?>
<?php include (new views())->ruta_templates.'botons/submit/modifica_bd.php';?>