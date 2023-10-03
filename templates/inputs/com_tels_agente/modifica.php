<?php /** @var \gamboamartin\comercial\controllers\controlador_com_sucursal $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->com_agente_id; ?>
<?php echo $controlador->inputs->com_tipo_tel_id; ?>
<?php echo $controlador->inputs->descripcion; ?>
<?php include (new views())->ruta_templates.'botons/submit/modifica_bd.php';?>