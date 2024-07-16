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
    }, true, "tipos_documentos", {registro_id: registro_id}, options);

