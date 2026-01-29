 base = $('[name="base"]').val();


///// Js Update Informacion responsable-seguimiento
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