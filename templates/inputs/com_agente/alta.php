<?php /** @var \gamboamartin\comercial\controllers\controlador_com_tipo_cliente $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->com_tipo_agente_id; ?>
<?php echo $controlador->inputs->adm_grupo_id; ?>
<?php echo $controlador->inputs->nombre; ?>
<?php echo $controlador->inputs->apellido_paterno; ?>
<?php echo $controlador->inputs->apellido_materno; ?>
<?php echo $controlador->inputs->user; ?>
<?php echo $controlador->inputs->password; ?>
<?php echo $controlador->inputs->email; ?>
<?php echo $controlador->inputs->telefono; ?>
<?php include (new views())->ruta_templates.'botons/submit/alta_bd.php';?>