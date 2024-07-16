<?php /** @var gamboamartin\comercial\controllers\controlador_com_email_cte $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->com_contacto_id; ?>
<?php echo $controlador->inputs->adm_usuario_id; ?>
<?php include (new views())->ruta_templates.'botons/submit/alta_bd.php';?>