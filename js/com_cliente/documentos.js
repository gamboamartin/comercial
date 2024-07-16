const registro_id = getParameterByName('registro_id');

const columns_tipos_documentos = [
    {
        title: "Tipo documento",
        data: "doc_tipo_documento_descripcion"
    },{
        title: "Etapa",
        data: "doc_etapa"
    },
    {
        title: "Descarga",
        data: "descarga"
    },
    {
        title: "Vista previa",
        data: "vista_previa"
    },
    {
        title: "ZIP",
        data: "descarga_zip"
    },
    {
        title: "Elimina",
        data: "elimina_bd"
    }
];

const options = {paging: false, info: false, searching: false}

const table_tipos_documentos = table('com_cliente', columns_tipos_documentos, [], [], function () {
    }, true,
    "tipos_documentos", {registro_id: registro_id}, options);


var modal = document.getElementById("myModal");
var modalSend = document.getElementById("modalSnd");
var closeBtn = document.getElementById("closeModalBtn");
var openMdl = document.getElementById("enviar");
var closeMdl = document.getElementById("closeModalSendBtn");

$(document).on("click", "#table-inm_prospecto a[title='Vista Previa']", function (event) {
    event.preventDefault();
    var url = $(this).attr("href");

    var loaderOverlay = $('<div class="loader-overlay"><div class="loader"></div></div>');
    $('body').append(loaderOverlay);

    $.ajax({
        url: url,
        type: 'GET',
        success: function (data) {
            var tempDiv = $("<div>").html(data);
            var viewContent = tempDiv.find(".view");

            $("#myModal .content").html(viewContent);
            modal.showModal();
            loaderOverlay.remove();
        },
        error: function () {
            $("#myModal .content").html("<p>Error al cargar el contenido.</p>");
            modal.showModal();
            loaderOverlay.remove();
        }
    });
});

closeBtn.onclick = function () {
    $("#myModal .content").empty();
    modal.close();
}
openMdl.onclick = function () {
    modalSend.showModal();
}
closeMdl.onclick = function () {
    modalSend.close();
}
modal.addEventListener('click', function (event) {
    if (event.target === modal) {
        $("#myModal .content").empty();
        modal.close();
    }
});

let documentos_seleccionados = [];

$("#table-inm_prospecto").on('click', 'td:first-child,td:nth-child(2)', function (e) {
    let timer = null;

    clearTimeout(timer);

    timer = setTimeout(() => {
        let selectedData = table_tipos_documentos.rows({selected: true}).data();

        documentos_seleccionados = [];

        selectedData.each(function (value, index, data) {
            const url = $(value.vista_previa).attr('href')
            const params = new URLSearchParams(url);
            const accion = params.get('accion');
            const id = params.get('registro_id');

            if (accion === 'vista_previa') {
                documentos_seleccionados.push(id);
            } else {
                const rowIndex = table_tipos_documentos.rows().indexes().filter((idx) => {
                    return table_tipos_documentos.row(idx).data() === value;
                });

                table_tipos_documentos.rows(rowIndex).deselect();
                alert("Seleccione un documento cargado");
            }
        });

        $('#documentos').val(documentos_seleccionados);
        $('#documentos-verificar').val(documentos_seleccionados);
        $('#documentos-enviar').val(documentos_seleccionados);
    }, 500);
});

$("#table-inm_prospecto").on('click', 'tr:first-child', function (e) {
    let timer = null;

    clearTimeout(timer);

    timer = setTimeout(() => {
        let selectedData = table_tipos_documentos.rows({selected: true}).data();

        documentos_seleccionados = [];

        selectedData.each(function (value, index, data) {
            console.log(value);

            const url = $(value.vista_previa).attr('href')
            const params = new URLSearchParams(url);
            const accion = params.get('accion');
            const id = params.get('registro_id');

            if (accion === 'vista_previa') {
                documentos_seleccionados.push(id);
            } else {
                const rowIndex = table_tipos_documentos.rows().indexes().filter((idx) => {
                    return table_tipos_documentos.row(idx).data() === value;
                });

                table_tipos_documentos.rows(rowIndex).deselect();
            }
        });

        $('#documentos').val(documentos_seleccionados);
        $('#documentos-verificar').val(documentos_seleccionados);
        $('#documentos-enviar').val(documentos_seleccionados);
    }, 500);
});



$("#form-documentos").on('submit', function (e) {
    if (documentos_seleccionados.length <= 1) {
        e.preventDefault();
        alert("Seleccione más de un documento para agruparlos");
    }
});

$("#form-documentos-verificar").on('submit', function (e) {
    if (documentos_seleccionados.length < 1) {
        e.preventDefault();
        alert("Seleccione un documento para verificarlo");
    }
});

$("#form-documentos-enviar").on('submit', function (e) {
    if (documentos_seleccionados.length < 1) {
        e.preventDefault();
        alert("Seleccione un documento para enviar");
    }
});

