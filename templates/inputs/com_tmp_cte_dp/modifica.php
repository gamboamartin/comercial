<?php /** @var \gamboamartin\comercial\controllers\controlador_com_cliente $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->com_cliente_id; ?>
<?php echo $controlador->inputs->dp_pais; ?>
<?php echo $controlador->inputs->dp_estado; ?>
<?php echo $controlador->inputs->dp_municipio; ?>
<?php echo $controlador->inputs->dp_cp; ?>
<?php echo $controlador->inputs->dp_colonia; ?>
<?php echo $controlador->inputs->dp_calle; ?>
<?php include (new views())->ruta_templates.'botons/submit/modifica_bd.php';?>



