let url = getAbsolutePath();
let registro_id = getParameterByName('registro_id');
let session_id = getParameterByName('session_id');

let sl_adm_menu_id = $("#adm_menu_id");
let sl_adm_seccion_id = $("#adm_seccion_id");
let adm_menu_id = sl_adm_menu_id.val();
let adm_seccion_id = sl_adm_seccion_id.val();

sl_adm_menu_id.change(function(){
    adm_menu_id = $(this).val();
    adm_asigna_secciones(adm_menu_id);
});




function adm_asigna_secciones(adm_menu_id = ''){
    let sl_adm_seccion_id = $("#adm_seccion_id");

    let url = "index.php?seccion=adm_seccion&ws=1&accion=get_adm_seccion&adm_menu_id="+adm_menu_id+"&session_id="+session_id;

    $.ajax({
        type: 'GET',
        url: url,
    }).done(function( data ) {  // Función que se ejecuta si todo ha ido bien
        sl_adm_seccion_id.empty();
        if(!isNaN(data.error)){
            if(data.error === 1){
                let msj = data.mensaje_limpio+' '+url;
                alert(msj);
                console.log(data);
                return false;
            }
        }
        integra_new_option("#adm_seccion_id",'Seleccione una seccion','-1');
        $.each(data.registros, function( index, adm_seccion ) {

            integra_new_option("#adm_seccion_id",adm_seccion.adm_menu_descripcion+' '+adm_seccion.adm_seccion_descripcion,adm_seccion.adm_seccion_id);
        });
        sl_adm_seccion_id.val(adm_seccion_id);
        sl_adm_seccion_id.selectpicker('refresh');
    }).fail(function (jqXHR, textStatus, errorThrown){ // Función que se ejecuta si algo ha ido mal
        alert('Error al ejecutar');

        console.log("The following error occured: "+ textStatus +" "+ errorThrown);
    });

}





