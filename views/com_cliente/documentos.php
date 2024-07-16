<?php /** @var  gamboamartin\inmuebles\controllers\controlador_inm_prospecto $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>

<main class="main section-color-primary">
    <div class="container">

        <div class="row">

            <div class="col-lg-12">

                <div class="widget" >

                    <?php include (new views())->ruta_templates."head/title.php"; ?>
                    <?php include (new views())->ruta_templates."head/subtitulo.php"; ?>
                    <?php include (new views())->ruta_templates."mensajes.php"; ?>


                    <?php echo $controlador->inputs->com_tipo_cliente_id; ?>
                    <?php echo $controlador->inputs->codigo; ?>
                    <?php echo $controlador->inputs->razon_social; ?>
                    <?php echo $controlador->inputs->rfc; ?>
                    <?php echo $controlador->inputs->telefono; ?>

                </div>
            </div>

        </div>
    </div>
    <br>

    <div class="container" style="margin-top: 20px;">
        <div class="row">
            <div class="col-lg-12" style="display: flex; gap: 15px;">
                <form id="form-documentos" action="<?php echo $controlador->link_agrupa_documentos; ?>" method="post"
                      enctype="multipart/form-data">
                    <input type="hidden" id="documentos" name="documentos" required>
                    <button id="agrupar" class="btn btn-success">Agrupar</button>
                </form>
                <form id="form-documentos-verificar" action="<?php echo $controlador->link_verifica_documentos; ?>" method="post"
                      enctype="multipart/form-data">
                    <input type="hidden" id="documentos-verificar" name="documentos" required>
                    <button id="verificar" class="btn btn-success">Verificar</button>
                </form>
                    <button id="enviar" class="btn btn-success">Enviar Documentos</button>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-12 table-responsive">
                <table id="table-inm_prospecto" class="table mb-0 table-striped table-sm "></table>
            </div>
        </div>
    </div>
</main>

<dialog id="myModal">
    <span class="close-btn" id="closeModalBtn">&times;</span>
    <h2>Vista Previa</h2>
    <div class="content">
    </div>
</dialog>

<dialog id="modalSnd">
    <span class="close-btn" id="closeModalSendBtn">&times;</span>
    <h2>Enviar Documentos</h2>
    <div class="content">
        <form id="form-documentos-enviar" action="<?php echo $controlador->link_envia_documentos; ?>" method="post"
              enctype="multipart/form-data">
            <input type="hidden" id="documentos-enviar" name="documentos" required>
            <?php echo $controlador->inputs->documentos;?>
            <?php echo $controlador->inputs->receptor; ?>
            <?php echo $controlador->inputs->asunto; ?>
            <?php echo $controlador->inputs->mensaje; ?>
            <button id="enviarDocs" class="btn btn-success">Enviar</button>
        </form>
    </div>
</dialog>