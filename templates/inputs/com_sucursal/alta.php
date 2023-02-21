<?php /** @var \gamboamartin\comercial\controllers\controlador_com_sucursal $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->com_tipo_sucursal_id; ?>
<?php echo $controlador->inputs->codigo; ?>
<?php echo $controlador->inputs->nombre_contacto; ?>
<?php echo $controlador->inputs->com_cliente_id; ?>
<?php echo $controlador->inputs->dp_pais_id; ?>
<?php echo $controlador->inputs->dp_estado_id; ?>
<?php echo $controlador->inputs->dp_municipio_id; ?>
<?php echo $controlador->inputs->dp_cp_id; ?>
<?php echo $controlador->inputs->dp_colonia_postal_id; ?>
<?php echo $controlador->inputs->dp_calle_pertenece_id; ?>
<?php echo $controlador->inputs->numero_exterior; ?>
<?php echo $controlador->inputs->numero_interior; ?>

<?php echo $controlador->inputs->telefono_1; ?>
<?php echo $controlador->inputs->telefono_2; ?>
<?php echo $controlador->inputs->telefono_3; ?>
<?php include (new views())->ruta_templates.'botons/submit/alta_bd.php';?>



