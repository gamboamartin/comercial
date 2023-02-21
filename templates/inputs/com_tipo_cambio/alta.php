<?php /** @var gamboamartin\comercial\models\com_tipo_cambio $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>

<?php echo $controlador->inputs->dp_pais_id; ?>
<?php echo $controlador->inputs->cat_sat_moneda_id; ?>
<?php echo $controlador->inputs->fecha; ?>
<?php echo $controlador->inputs->monto; ?>
<?php include (new views())->ruta_templates.'botons/submit/alta_bd.php';?>


