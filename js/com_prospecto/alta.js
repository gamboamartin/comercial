const actualizarRazonSocial = () => {
    const nombre = $('#nombre').val();
    const apellidoPaterno = $('#apellido_paterno').val();
    const apellidoMaterno = $('#apellido_materno').val();

    // Concatenar los valores y actualizar el campo 'razon_social'
    const razonSocial = `${nombre} ${apellidoPaterno} ${apellidoMaterno}`.trim();
    $('#razon_social').val(razonSocial);
};

$('#nombre, #apellido_paterno, #apellido_materno').on('input', actualizarRazonSocial);