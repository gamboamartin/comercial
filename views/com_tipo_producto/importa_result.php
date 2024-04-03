<?php /** @var gamboamartin\comercial\controllers\controlador_com_tipo_cliente $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<main class="main section-color-primary">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <?php include (new views())->ruta_templates."head/title.php"; ?>

                <?php include (new views())->ruta_templates."mensajes.php"; ?>

                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">
                    <h3>Columnas Doc</h3>
                    <table class="table">
                        <thead>
                        <?php
                        foreach ($controlador->registros['columnas_xls'] as $columnas_xls){ ?>
                            <tr><th><?php echo $columnas_xls; ?></th></tr>
                        <?php } ?>
                        </thead>
                    </table>
                </div>

                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">
                    <h3>Registros</h3>
                    <table class="table">
                        <thead>
                            <tr>
                            <?php
                            foreach ($controlador->registros['columnas_xls'] as $columnas_xls){ ?>
                                <th><?php echo $columnas_xls; ?></th>
                            <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($controlador->registros['rows_xls'] as $rows_xls){ ?>
                            <tr>
                                <?php foreach ($rows_xls as $value){ ?>
                                <td><?php echo $value; ?></td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">
                    <h3>Registros formato BD</h3>
                    <table class="table">
                        <thead>
                            <?php
                            foreach ($controlador->registros['rows_a_importar_db'] as $indice=>$row_a_importar_db){ ?>
                                <tr><th><?php echo $indice; ?></th></tr>
                                <?php foreach ($row_a_importar_db as $campo=>$value){ ?>
                                    <tr><td><?php echo $campo; ?>: <?php echo $value; ?></td></tr>
                                <?php } ?>
                            <?php } ?>
                        </thead>
                    </table>
                </div>


                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">
                    <h3>Registros Importados</h3>
                    <table class="table">
                        <thead>
                        <?php
                        foreach ($controlador->registros['transacciones'] as $transaccion){ ?>
                            <tr>
                                <td>
                                    Mensaje: <b><?php echo $transaccion->mensaje; ?></b>
                                    Id: <b><?php echo $transaccion->registro_id; ?></b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php
                                    foreach ($transaccion->registro_obj as $campo=>$value) { ?>
                                    <?php echo $campo; ?>: <b><?php echo $value; ?></b><br>
                                    <?php
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                        </thead>
                    </table>
                </div>

            </div>
        </div>
    </div>
</main>


