<?php /** @var controllers\controlador_com_sucursal $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->select->com_cliente_id; ?>
<?php echo $controlador->inputs->codigo; ?>
<?php echo $controlador->inputs->codigo_bis; ?>
<?php echo $controlador->inputs->descripcion; ?>
<?php echo $controlador->inputs->nombre_contacto; ?>

<?php include (new views())->ruta_templates.'botons/submit/alta_bd_otro.php';?>