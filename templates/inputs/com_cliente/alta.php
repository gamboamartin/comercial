<?php /** @var gamboamartin\comercial\controllers\controlador_com_cliente $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->com_tipo_cliente_id; ?>
<?php echo $controlador->inputs->codigo; ?>
<?php echo $controlador->inputs->razon_social; ?>
<?php echo $controlador->inputs->rfc; ?>
<?php echo $controlador->inputs->telefono; ?>
<?php echo $controlador->inputs->cat_sat_tipo_persona_id; ?>
<?php echo $controlador->inputs->cat_sat_regimen_fiscal_id; ?>

<div id="dp_pais_cont">
    <?php echo $controlador->inputs->dp_pais_id; ?>
</div>
<div id="dp_estado_cont">
    <?php echo $controlador->inputs->dp_estado_id; ?>
</div>
<div id="dp_municipio_cont">
        <?php echo $controlador->inputs->dp_municipio_id; ?>
</div>
<div id="dp_cp_cont">
        <?php echo $controlador->inputs->dp_cp_id; ?>
</div>
<div id="dp_colonia_postal_cont">
        <?php echo $controlador->inputs->dp_colonia_postal_id; ?>
</div>
<div id="dp_calle_pertenece_cont">
        <?php echo $controlador->inputs->dp_calle_pertenece_id; ?>
</div>

<div id="dp_estado_cont_tmp">
    <?php echo $controlador->inputs->dp_estado; ?>
</div>
<div id="dp_municipio_cont_tmp">
        <?php echo $controlador->inputs->dp_municipio; ?>
</div>
<div id="dp_cp_cont_tmp">
        <?php echo $controlador->inputs->dp_cp; ?>
</div>
<div id="dp_colonia_cont_tmp">
        <?php echo $controlador->inputs->dp_colonia; ?>
</div>
<div id="dp_calle_cont_tmp">
        <?php echo $controlador->inputs->dp_calle; ?>
</div>


<?php echo $controlador->inputs->numero_exterior; ?>
<?php echo $controlador->inputs->numero_interior; ?>

<?php echo $controlador->inputs->cat_sat_uso_cfdi_id; ?>
<?php echo $controlador->inputs->cat_sat_metodo_pago_id; ?>
<?php echo $controlador->inputs->cat_sat_forma_pago_id; ?>
<?php echo $controlador->inputs->cat_sat_tipo_de_comprobante_id; ?>
<?php echo $controlador->inputs->cat_sat_moneda_id; ?>
<?php include (new views())->ruta_templates.'botons/submit/alta_bd.php';?>



<!-- Trigger the modal with a button -->


<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="top: 90px;>

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg-danger" style="border-top-left-radius: 8px;border-top-right-radius: 8px;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" style="padding-bottom: 10px;">Error</h4>
            </div>
            <div class="modal-body" style = "padding: 20px !important;background-color: #fff;border-bottom-left-radius: 8px;border-bottom-right-radius: 8px;">
                <h5>Seleccione una forma de pago valida</h5>
            </div>
        </div>

    </div>
</div>





