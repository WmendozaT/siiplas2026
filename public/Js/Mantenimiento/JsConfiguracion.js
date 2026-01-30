 base = $('[name="base"]').val();


//// Js para actualizar estado de los modulos disponibles
$(document).on('change', '.btn-switch-updates', function() {
    const $input = $(this);
    
    // Recuperamos los nombres y valores desde los meta tags
    const csrfName = $('meta[name="csrf-token-name"]').attr('content');
    const csrfHash = $('meta[name="csrf-token-value"]').attr('content');

    const dataPost = {
        id: $input.data('id'),
        columna: $input.data('columna'),
        valor: $input.is(':checked') ? 1 : 0
    };

    // Añadimos el token dinámicamente al objeto de datos
    dataPost[csrfName] = csrfHash;

    $.ajax({
        url: base + "mnt/update_estado_modulos",
        type: 'POST',
        data: dataPost,
        dataType: 'json',
        success: function(response) {
            // IMPORTANTE: Si CI4 regenera el token, debemos actualizar el meta tag
            if (response.token) {
                //alert(response.token)
                $('meta[name="csrf-token-value"]').attr('content', response.token);
            }

            if (response.status !== 'success') {
                alert('Error: ' + response.message);
                $input.prop('checked', !$input.is(':checked'));
            }
        },
        error: function() {
            alert('Error de comunicación con el servidor');
            $input.prop('checked', !$input.is(':checked'));
        }
    });
});

///// Js Update Permisos responsable-seguimiento
$(document).ready(function() {
    $("#form_conf").on("submit", function(e) {
        let esValido = true;

        // Validar textos y números
        $("#NombreEntidad, #SiglaEntidad, #MisionEntidad, #VisionEntidad, #conf_gestion_desde, #conf_gestion_hasta").each(function() {
            if ($(this).val().trim() === "") {
                $(this).addClass("is-invalid").removeClass("is-valid");
                esValido = false;
            } else {
                $(this).removeClass("is-invalid").addClass("is-valid");
            }
        });

        // VALIDACIÓN DE FECHAS
        const fIni = $("#EvalIni").val();
        const fFin = $("#EvalFin").val();

        if (!fIni) { $("#EvalIni").addClass("is-invalid"); esValido = false; }
        if (!fFin) { $("#EvalFin").addClass("is-invalid"); esValido = false; }

        if (fIni && fFin && fIni > fFin) {
            $("#EvalFin").addClass("is-invalid");
            alert("Error: La fecha final debe ser posterior a la fecha inicial.");
            esValido = false;
        }

        // Lógica de envío con alerta de confirmación
        if (!esValido) {
            e.preventDefault();
            alert("Por favor, complete todos los campos obligatorios correctamente.");
        } else {
            e.preventDefault();
            if (confirm("¿Está seguro de guardar los cambios?")) {
                $("#btnGuardar").prop("disabled", true);
                $("#textGuardar").text("Procesando...");
                $("#spinnerGuardar").removeClass("d-none");

                setTimeout(() => {
                    $("#form_conf").off("submit").submit();
                }, 800);
            }
        }
    });

    // Limpiar errores al cambiar datos (incluyendo fechas)
    $("input, select, textarea").on("change keyup", function() {
        if ($(this).val() !== "") {
            $(this).removeClass("is-invalid");
        }
    });
});







