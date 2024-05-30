<?php /** @var gamboamartin\comercial\controllers\controlador_com_prospecto $controlador controlador en ejecucion */ ?>
<?php use config\views; ?>
<main class="main section-color-primary">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <?php include (new views())->ruta_templates . "head/title.php"; ?>

                <?php include (new views())->ruta_templates . "mensajes.php"; ?>

                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">
                    <form method="post" action="<?php echo $controlador->link_alta_etapa; ?>" class="form-additional">
                        <?php include (new views())->ruta_templates . "head/subtitulo.php"; ?>

                        <?php echo $controlador->inputs->com_tipo_prospecto_id; ?>
                        <?php echo $controlador->inputs->com_agente_id; ?>
                        <?php echo $controlador->inputs->nombre; ?>
                        <?php echo $controlador->inputs->apellido_paterno; ?>
                        <?php echo $controlador->inputs->apellido_materno; ?>
                        <?php echo $controlador->inputs->telefono; ?>
                        <?php echo $controlador->inputs->correo; ?>
                        <?php echo $controlador->inputs->razon_social; ?>

                        <?php include (new views())->ruta_templates . 'botons/submit/modifica_bd.php'; ?>
                    </form>

                </div>

            </div>
        </div>

    </div>

</main>

<main class="main section-color-primary">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="widget widget-box box-container widget-mylistings">
                    <ivi-tab-group>
                        <ivi-tab label="Direcciones" active>
                            <form method="post" action="<?php echo $controlador->link_alta_direccion; ?>"
                                  class="form-additional">
                                <?php echo $controlador->inputs->com_tipo_direccion_id; ?>
                                <?php echo $controlador->inputs->dp_pais_id; ?>
                                <?php echo $controlador->inputs->dp_estado_id; ?>
                                <?php echo $controlador->inputs->dp_municipio_id; ?>
                                <?php echo $controlador->inputs->cp; ?>
                                <?php echo $controlador->inputs->colonia; ?>
                                <?php echo $controlador->inputs->calle; ?>
                                <?php echo $controlador->inputs->texto_exterior; ?>
                                <?php echo $controlador->inputs->texto_interior; ?>
                                <?php echo $controlador->inputs->com_prospecto_id; ?>


                                <div class="control-group col-sm-12"
                                     style="display: flex; flex-direction: row-reverse;">
                                    <button type="submit" class="btn btn-success" value="correo" name="btn_action_next">
                                        Alta
                                    </button>
                                    <br>
                                </div>
                            </form>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="">
                                        <div class="widget-header"
                                             style="display: flex;justify-content: space-between;align-items: center; margin-left: -15px;">
                                            <h2>Registro de Direcciones</h2>
                                        </div>

                                        <div class="table-responsive">
                                            <table id="table-com_direccion" class="table mb-0 table-striped table-sm "></table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </ivi-tab>

                    </ivi-tab-group>
                </div>
            </div>
        </div>

    </div>
</main>

