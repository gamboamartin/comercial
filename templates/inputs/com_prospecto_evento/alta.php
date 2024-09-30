<?php /** @var \gamboamartin\empleado\controllers\controlador_em_conf_tipo_doc_empleado $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->com_prospecto_id; ?>
<?php echo $controlador->inputs->adm_evento_id; ?>
<?php include (new views())->ruta_templates.'botons/submit/alta_bd.php';?>