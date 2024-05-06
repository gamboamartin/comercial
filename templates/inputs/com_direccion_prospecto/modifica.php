<?php /** @var  \gamboamartin\gastos\controllers\controlador_gt_cotizadores $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->com_direccion_id; ?>
<?php echo $controlador->inputs->com_prospecto_id; ?>
<?php include (new views())->ruta_templates.'botons/submit/modifica_bd.php';?>