<?php /** @var gamboamartin\comercial\controllers\controlador_com_tipo_cliente $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<main class="main section-color-primary">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <?php include (new views())->ruta_templates."head/title.php"; ?>

                <?php include (new views())->ruta_templates."mensajes.php"; ?>

                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">
                    <form method="post" action="<?php echo $controlador->link_importa_previo_muestra; ?>" class="form-additional">
                        <?php include (new views())->ruta_templates."head/subtitulo.php"; ?>

                        <?php
                        foreach ($controlador->columnas_calc as $columna_calc){

                            echo $columna_calc;
                        }
                        ?>

                        <div class="controls">
                            <button type="submit" class="btn btn-success" value="Importa" name="btn_action_next">Importa</button><br>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>


