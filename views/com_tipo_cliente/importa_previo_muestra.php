<?php /** @var gamboamartin\comercial\controllers\controlador_com_tipo_cliente $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<main class="main section-color-primary">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <?php include (new views())->ruta_templates."head/title.php"; ?>

                <?php include (new views())->ruta_templates."mensajes.php"; ?>

                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">
                    <form method="post" action="<?php echo $controlador->link_importa_previo_muestra_bd; ?>" class="form-additional">
                        <?php include (new views())->ruta_templates."head/subtitulo.php"; ?>

                        <table class="table">
                            <thead>
                            <tr>
                                <?php
                                foreach ($controlador->ths as $th){ ?>
                                <th><?php echo $th; ?></th>
                                <?php
                                }
                                ?>
                            </tr>
                            </thead>

                        <?php
                        foreach ($controlador->registros as $registro){ ?>
                            <tr>
                            <?php foreach ($registro as $campo_data){ ?>
                                <?php if(is_string($campo_data)){
                                    echo "<td>$campo_data</td>";
                                    continue;
                                }
                                ?>

                                <td class="bg-<?php echo $campo_data['contexto']; ?>" title="<?php echo $campo_data['mensaje']; ?>">
                                    <?php echo $campo_data['value']; ?>
                                </td>

                            <?php } ?>
                            </tr>
                        <?php }
                        ?>

                            <div class="controls">
                                <?php echo $controlador->input_params_importa; ?>
                                <button type="submit" class="btn btn-success" value="Importa" name="btn_action_next">Importa</button><br>
                            </div>

                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>


