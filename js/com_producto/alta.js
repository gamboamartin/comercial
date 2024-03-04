$('#cat_sat_producto').keyup(function () {
    let dato_env = $(this).val();
    //alert(dato_env);
    let url = get_url("cat_sat_cve_prod","get_data_descripcion", {data: dato_env, limit: 20});
    //alert(url);
    $.ajax({
        url: url,
        context: document.body
    }).done(function(data) {
        $("#datos_producto").empty();
        console.log(data);
        let rows = data.registros;
        $.each( rows, function( key, row ) {
            let dato_row = "Codigo: "+row.cat_sat_cve_prod_codigo;
            dato_row = dato_row+ " Descripcion: "+ row.cat_sat_cve_prod_descripcion;
            let id = row.cat_sat_cve_prod_id;
            let input = '<input type="radio" id="scales" name="cat_sat_producto_radio" value="'+id+'"/>';

            let tr = "<td>"+dato_row+"</td><td>"+input+"</td>";
            $("#datos_producto").append("<tr>" + tr + "</tr>");
        });
    });

});
