<?php /** @var gamboamartin\controllers\controlador_adm_seccion $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->adm_grupo_id; ?>
<?php echo $controlador->inputs->doc_tipo_documento_id; ?>

<?php include (new views())->ruta_templates.'botons/submit/modifica_bd.php';?>
