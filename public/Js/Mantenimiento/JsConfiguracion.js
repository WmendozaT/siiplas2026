 base = $('[name="base"]').val();

///// Js Update formulario de configuracion del Sistema
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


//// Js para actualizar estado de los modulos disponibles
$(document).on('change', '.btn-switch-updates', function() {
    const $input = $(this);
    const id      = $input.data('id');      
    const columna = $input.data('columna'); 
    const valor   = $input.is(':checked') ? 1 : 0;

    // 3. Captura de CSRF (Importante: capturarlos justo antes del envío)
    const csrfName = $('meta[name="csrf-token-name"]').attr('content');
    const csrfHash = $('meta[name="csrf-token-value"]').attr('content');

    // 4. Preparación del objeto
    const dataPost = {
        id: id,
        columna: columna,
        valor: valor
    };

    // 5. Inyección dinámica del token
    dataPost[csrfName] = csrfHash;

    $.ajax({
        url: base + "mnt/update_estado_modulos",
        type: 'POST',
        data: dataPost,
        dataType: 'json',
        beforeSend: function() {
            // Opcional: Bloquear el switch mientras procesa para evitar doble clic
            $input.prop('disabled', true);
        },
        success: function(response) {
            // ACTUALIZACIÓN DEL TOKEN: Vital para que el siguiente clic no de error 403
            if (response.token) {
                $('meta[name="csrf-token-value"]').attr('content', response.token);
            }

            if (response.status === 'success') {
                console.log("Módulo actualizado correctamente");
            } else {
                alert('Error: ' + (response.message || 'No se pudo actualizar'));
                // Revertir estado visual si el servidor reporta error
                $input.prop('checked', !($input.is(':checked')));
            }
        },
        error: function(xhr) {
            console.error("Error del servidor:", xhr.responseText);
            alert('Error de comunicación con el servidor');
            // Revertir estado visual si hay error de red o 500
            $input.prop('checked', !($input.is(':checked')));
        },
        complete: function() {
            // Desbloquear el switch
            $input.prop('disabled', false);
        }
    });
});



$(document).ready(function() {
    // 1. Obtener el nombre y valor del CSRF (necesario para CI4)
    // Asegúrate de tener esto en tu vista: <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrf_token">
    
    function enviarFormulario() {
        $('#btn-add').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Guardando...');
        
        const formData = {
            prog: $('#prog').val(),
            detalle: $('#detalle').val(),
            // Incluye el token CSRF si está activo en tu Config/Filters.php
            [csrfName]: csrfHash 
        };
        
        $.ajax({
            url: base + "mnt/aperturas",
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('✅ ' + response.message);
                    $('#addContactModal').modal('hide'); // Cierra el modal
                    location.reload(); // Opcional: recargar para ver cambios
                } else {
                    // Si el controlador manda errores de validación
                    if(response.errors) {
                        alert('Error: ' + JSON.stringify(response.errors));
                    } else {
                        alert('❌ ' + response.message);
                    }
                }
            },
            error: function(xhr) {
                alert('Error crítico en el servidor');
            },
            complete: function() {
                $('#btn-add').prop('disabled', false).text('Guardar');
            }
        });
    }

    // Corregir el reset del modal
    $('#addContactModal').on('hidden.bs.modal', function() {
    // Busca el formulario dentro del modal y resetealo
    $(this).find('form')[0].reset(); 
    
    // Quita las clases de validación de Bootstrap
    $('#prog, #detalle').removeClass('is-valid is-invalid');
});
});