<?php /** @var \gamboamartin\comercial\models\com_direccion $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->dp_calle_id; ?>


<?php echo $controlador->inputs->texto_exterior; ?>
<?php echo $controlador->inputs->texto_interior; ?>
<?php include (new views())->ruta_templates.'botons/submit/modifica_bd.php';?>



