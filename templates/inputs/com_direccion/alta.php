<?php /** @var \gamboamartin\comercial\models\com_direccion $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->com_tipo_direccion_id; ?>
<?php echo $controlador->inputs->dp_pais_id; ?>
<?php echo $controlador->inputs->dp_estado_id; ?>
<?php echo $controlador->inputs->dp_municipio_id; ?>
<?php echo $controlador->inputs->dp_cp_id; ?>
<?php echo $controlador->inputs->dp_colonia_postal_id; ?>
<?php echo $controlador->inputs->dp_calle_pertenece_id; ?>

<?php echo $controlador->inputs->texto_exterior; ?>
<?php echo $controlador->inputs->texto_interior; ?>

<?php include (new views())->ruta_templates.'botons/submit/alta_bd.php';?>





