const actualizarRazonSocial = () => {
    const nombre = $('#nombre').val();
    const apellidoPaterno = $('#apellido_paterno').val();
    const apellidoMaterno = $('#apellido_materno').val();

    // Concatenar los valores y actualizar el campo 'razon_social'
    const razonSocial = `${nombre} ${apellidoPaterno} ${apellidoMaterno}`.trim();
    $('#razon_social').val(razonSocial);
};

$('#nombre, #apellido_paterno, #apellido_materno').on('input', actualizarRazonSocial);

class IVITabGroup extends HTMLElement {
    connectedCallback() {
        this.currentTab = 0;
        this.tabs = this.querySelectorAll('ivi-tab');
        this.render();
    }

    render() {
        this.innerHTML = '';
        const tabHeaders = document.createElement('div');
        const tabContents = document.createElement('div');
        tabHeaders.classList.add('tab-headers');
        tabContents.classList.add('tab-contents');

        this.tabs.forEach((tab, index) => {
            const tabHeader = document.createElement('div');
            tabHeader.textContent = tab.getAttribute('label');
            tabHeader.classList.add('tab-header');
            tabHeader.addEventListener('click', () => this.showTab(index));
            tabHeaders.appendChild(tabHeader);

            const tabContent = document.createElement('div');
            tabContent.innerHTML = tab.innerHTML;
            tabContent.classList.add('tab-content');
            tabContents.appendChild(tabContent);

            if (tab.hasAttribute('active')) {
                this.currentTab = index;
            }
        });

        this.appendChild(tabHeaders);
        this.appendChild(tabContents);

        this.showTab(this.currentTab);
    }

    showTab(index) {
        const tabHeaders = this.querySelectorAll('.tab-header');
        tabHeaders.forEach((header, headerIndex) => {
            if (headerIndex === index) {
                header.classList.add('active');
            } else {
                header.classList.remove('active');
            }
        });

        const tabContents = this.querySelectorAll('.tab-content');
        tabContents.forEach((content, contentIndex) => {
            if (contentIndex === index) {
                content.style.display = 'block';
            } else {
                content.style.display = 'none';
            }
        });

        this.currentTab = index;
    }
}

class IVITab extends HTMLElement {
    connectedCallback() {
        this.style.display = 'none';
    }
}

customElements.define('ivi-tab-group', IVITabGroup);
customElements.define('ivi-tab', IVITab);

let sl_dp_pais = $("#dp_pais_id");
let sl_dp_estado = $("#dp_estado_id");
let sl_dp_municipio = $("#dp_municipio_id");
let sl_dp_cp = $("#dp_cp_id");
let sl_dp_colonia = $("#dp_colonia_postal_id");
let sl_dp_calle_pertenece = $("#dp_calle_pertenece_id");

function dp_asigna_estados(dp_pais_id = '',dp_estado_id = ''){

    let url = get_url("dp_estado","get_estado", {dp_pais_id: dp_pais_id});

    get_data(url, function (data) {
        sl_dp_estado.empty();
        integra_new_option(sl_dp_estado,'Seleccione un estado','-1');

        $.each(data.registros, function( index, dp_estado ) {
            integra_new_option(sl_dp_estado,dp_estado.dp_estado_descripcion,dp_estado.dp_estado_id,
                "data-dp_estado_predeterminado",dp_estado.dp_estado_predeterminado);
        });
        sl_dp_estado.val(dp_estado_id);
        sl_dp_estado.selectpicker('refresh');
    });
}

function dp_asigna_municipios(dp_estado_id = '',dp_municipio_id = ''){

    let url = get_url("dp_municipio","get_municipio", {dp_estado_id: dp_estado_id});

    get_data(url, function (data) {
        sl_dp_municipio.empty();

        integra_new_option(sl_dp_municipio,'Seleccione un municipio','-1');

        $.each(data.registros, function( index, dp_municipio ) {
            integra_new_option(sl_dp_municipio,dp_municipio.dp_municipio_descripcion,dp_municipio.dp_municipio_id,
                "data-dp_municipio_predeterminado",dp_municipio.dp_municipio_predeterminado);
        });
        sl_dp_municipio.val(dp_municipio_id);
        sl_dp_municipio.selectpicker('refresh');
    });
}

function dp_asigna_cps(dp_municipio_id = '',dp_cp_id = ''){

    let url = get_url("dp_cp","get_cp", {dp_municipio_id: dp_municipio_id});

    get_data(url, function (data) {
        sl_dp_cp.empty();

        integra_new_option(sl_dp_cp,'Seleccione un cp','-1');

        $.each(data.registros, function( index, dp_cp ) {
            integra_new_option(sl_dp_cp,dp_cp.dp_cp_descripcion,dp_cp.dp_cp_id,"data-dp_cp_predeterminado",
                dp_cp.dp_cp_predeterminado);
        });
        sl_dp_cp.val(dp_cp_id);
        sl_dp_cp.selectpicker('refresh');
    });
}

function dp_asigna_colonias_postales(dp_cp_id = '',dp_colonia_postal_id = ''){

    let url = get_url("dp_colonia_postal","get_colonia_postal", {dp_cp_id: dp_cp_id});

    get_data(url, function (data) {
        sl_dp_colonia.empty();

        integra_new_option(sl_dp_colonia,'Seleccione una colonia','-1');

        $.each(data.registros, function( index, dp_colonia_postal ) {
            integra_new_option(sl_dp_colonia,dp_colonia_postal.dp_colonia_postal_descripcion,dp_colonia_postal.dp_colonia_postal_id,
                "data-dp_colonia_postal_predeterminado", dp_colonia_postal.dp_colonia_postal_predeterminado);
        });
        sl_dp_colonia.val(dp_colonia_postal_id);
        sl_dp_colonia.selectpicker('refresh');
    });
}

function dp_asigna_calles_pertenece(dp_colonia_postal_id = '',dp_calle_pertenece_id = ''){

    let url = get_url("dp_calle_pertenece","get_calle_pertenece", {dp_colonia_postal_id: dp_colonia_postal_id});

    get_data(url, function (data) {
        sl_dp_calle_pertenece.empty();

        integra_new_option(sl_dp_calle_pertenece,'Seleccione una calle','-1');

        $.each(data.registros, function( index, dp_calle_pertenece ) {
            integra_new_option(sl_dp_calle_pertenece,dp_calle_pertenece.dp_calle_pertenece_descripcion,dp_calle_pertenece.dp_calle_pertenece_id,
                "data-dp_calle_pertenece_predeterminado", dp_calle_pertenece.dp_calle_pertenece_predeterminado);
        });
        sl_dp_calle_pertenece.val(dp_calle_pertenece_id);
        sl_dp_calle_pertenece.selectpicker('refresh');
    });
}


sl_dp_pais.change(function () {
    let selected = $(this).find('option:selected');
    let predeterminado = selected.data(`dp_pais_predeterminado`);

    dp_asigna_estados(selected.val());

    sl_dp_estado.prop( "disabled", false );
    sl_dp_municipio.prop( "disabled", false );
    sl_dp_cp.prop( "disabled", false );
    sl_dp_colonia.prop( "disabled", false );
    sl_dp_calle_pertenece.prop( "disabled", false );

    if (predeterminado === 'activo'){
        sl_dp_estado.prop( "disabled", true );
        sl_dp_municipio.prop( "disabled", true );
        sl_dp_cp.prop( "disabled", true );
        sl_dp_colonia.prop( "disabled", true );
        sl_dp_calle_pertenece.prop( "disabled", true );
    }
});

sl_dp_estado.change(function () {
    let selected = $(this).find('option:selected');
    let predeterminado = selected.data(`dp_estado_predeterminado`);

    dp_asigna_municipios(selected.val());

    sl_dp_municipio.prop( "disabled", false );
    sl_dp_cp.prop( "disabled", false );
    sl_dp_colonia.prop( "disabled", false );
    sl_dp_calle_pertenece.prop( "disabled", false );

    if (predeterminado === 'activo'){
        sl_dp_municipio.prop( "disabled", true );
        sl_dp_cp.prop( "disabled", true );
        sl_dp_colonia.prop( "disabled", true );
        sl_dp_calle_pertenece.prop( "disabled", true );

    }
});

sl_dp_municipio.change(function () {
    let selected = $(this).find('option:selected');
    let predeterminado = selected.data(`dp_municipio_predeterminado`);
    dp_asigna_cps(selected.val());

    sl_dp_cp.prop( "disabled", false );
    sl_dp_colonia.prop( "disabled", false );
    sl_dp_calle_pertenece.prop( "disabled", false );

    if (predeterminado === 'activo'){
        sl_dp_cp.prop( "disabled", true );
        sl_dp_colonia.prop( "disabled", true );
        sl_dp_calle_pertenece.prop( "disabled", true );

    }
});

sl_dp_cp.change(function () {
    let selected = $(this).find('option:selected');
    let predeterminado = selected.data(`dp_cp_predeterminado`);

    dp_asigna_colonias_postales(selected.val());

    sl_dp_colonia.prop( "disabled", false );
    sl_dp_calle_pertenece.prop( "disabled", false );

    if (predeterminado === 'activo'){
        sl_dp_colonia.prop( "disabled", true );
        sl_dp_calle_pertenece.prop( "disabled", true );

    }
});

sl_dp_colonia.change(function () {
    let selected = $(this).find('option:selected');
    let predeterminado = selected.data(`dp_colonia_postal_predeterminado`);

    dp_asigna_calles_pertenece(selected.val());

    sl_dp_calle_pertenece.prop( "disabled", false );

    if (predeterminado === 'activo'){
        sl_dp_calle_pertenece.prop( "disabled", true );

    }
});

let registro_id = getParameterByName('registro_id');

const columns_com_direccion = [
    {
        title: "Id",
        data: `com_direccion_id`
    },
    {
        title: "Acciones",
        data: null
    }
];



const table_com_direccion = table('com_direccion', columns_com_direccion, []);


$('#table-com_direccion_prospecto').css({"width":"100%"});


