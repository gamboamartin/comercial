<?php /** @var \gamboamartin\comercial\controllers\controlador_com_tipo_cliente $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->doc_documento_id; ?>
<?php echo $controlador->inputs->com_cliente_id; ?>
<?php include (new views())->ruta_templates.'botons/submit/modifica_bd.php';?>