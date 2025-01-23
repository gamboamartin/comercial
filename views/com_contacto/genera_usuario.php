<?php /** @var  gamboamartin\comercial\controllers\controlador_com_contacto $controlador controlador en ejecucion */ ?>
<?php use config\views; ?>

<main class="main section-color-primary">
    <div class="container">

        <div class="row">

            <div class="col-lg-12">

                <div class="widget  widget-box box-container form-main widget-form-cart" id="form" >

                    <form method="post" action="<?php echo $controlador->link_com_contacto_user_bd; ?>" class="form-additional">
                        <?php include (new views())->ruta_templates . "head/title.php"; ?>
                        <?php include (new views())->ruta_templates . "head/subtitulo.php"; ?>
                        <?php include (new views())->ruta_templates . "mensajes.php"; ?>
                        <?php echo $controlador->inputs->adm_usuario->user; ?>
                        <?php echo $controlador->inputs->adm_usuario->password; ?>
                        <?php echo $controlador->inputs->adm_usuario->email; ?>
                        <?php echo $controlador->inputs->adm_usuario->adm_grupo_id; ?>
                        <?php echo $controlador->inputs->adm_usuario->telefono; ?>
                        <?php echo $controlador->inputs->adm_usuario->nombre; ?>
                        <?php echo $controlador->inputs->adm_usuario->ap; ?>
                        <?php echo $controlador->inputs->adm_usuario->am; ?>
                        <div class="control-group btn-alta">
                            <div class="controls">
                                <button class="btn btn-success" role="submit">Crear</button><br>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12 buttons-form">
            <?php echo $controlador->button_com_contacto_modifica; ?>
        </div>
    </div>

</main>

<main class="main section-color-primary">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="widget widget-box box-container widget-mylistings">
                    <?php echo $controlador->contenido_table; ?>
                </div> <!-- /. widget-table-->
            </div><!-- /.center-content -->
        </div>
    </div>
</main>


















