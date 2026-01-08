 base = $('[name="base"]').val();


$(document).ready(function() {
    $("#tp_adm").change(function () {
        // Obtener el valor seleccionado directamente
        var tp_adm = $(this).val(); 
        id = $('[name="id"]').val();
        // Validar que el valor no esté vacío (opcional, si tienes un "Seleccione...")
        if (tp_adm === "") return;
        var url = base + "mnt/get_reg_nal";
        var request = $.ajax({
            url: url,
            type: "POST",
            dataType: 'json',
            data: { 
                tipo_adm: tp_adm, // Se envía como $_POST['tipo_adm']
                id: id
            }
        });

        request.done(function (response) {
            if (response.respuesta == 'correcto') {
                $("#select_reg").html(response.select_reg);
                $("#select_dist").html(response.select_dist);
            }
        });

        request.fail(function (jqXHR, textStatus, errorThrown) {
            console.error("Error en la petición: " + textStatus, errorThrown);
        });
    }); 
  })


$(document).ready(function() {
    $("#reg_id").change(function () {
        // Obtener el valor seleccionado directamente
        var reg_id = $(this).val(); 
        id = $('[name="id"]').val();
        // Validar que el valor no esté vacío (opcional, si tienes un "Seleccione...")
        if (tp_adm === "") return;
        var url = base + "mnt/get_dist";
        var request = $.ajax({
            url: url,
            type: "POST",
            dataType: 'json',
            data: { 
                dep_id: reg_id, // Se envía como $_POST['tipo_adm']
                id: id
            }
        });

        request.done(function (response) {
            if (response.respuesta == 'correcto') {
                $("#select_dist").html(response.select_dist);
            }
        });

        request.fail(function (jqXHR, textStatus, errorThrown) {
            console.error("Error en la petición: " + textStatus, errorThrown);
        });
    }); 
  })



/////
$(document).ready(function() {
    $("#form").on("submit", function(e) {
        let esValido = true;

        // 1. Validar campos de texto y número
        // Nota: eliminé el duplicado de fn_usu que tenías en tu HTML
        $("#fn_nom, #fn_pt, #fn_mt, #fn_ci, #fn_fono, #fn_cargo, #fn_usu, #fun_password").each(function() {
            if ($(this).val().trim() === "") {
                $(this).addClass("is-invalid");
                esValido = false;
            } else {
                $(this).removeClass("is-invalid").addClass("is-valid");
            }
        });

        // 2. Validar Select de Administración
        if ($("#tp_adm").val() === "0" || $("#tp_adm").val() === "") {
            $("#tp_adm").addClass("is-invalid");
            esValido = false;
        } else {
            $("#tp_adm").removeClass("is-invalid").addClass("is-valid");
        }

        // 3. Lógica de Envío
        if (!esValido) {
            e.preventDefault(); // Detiene el envío si hay error
            alert("Por favor, complete todos los campos obligatorios.");
        } else {
            e.preventDefault(); // Detenemos el envío inmediato
    
            $("#btnGuardar").prop("disabled", true);
            $("#textGuardar").text("Procesando...");
            $("#spinnerGuardar").removeClass("d-none");

            // Esperar 3 segundos antes de enviar realmente el formulario
            setTimeout(function() {
                $("#form").off("submit").submit(); // Quita el evento actual y envía el form
            }, 1000);
        }
    });

    // Limpiar errores mientras el usuario escribe
    $("input, select").on("change keyup", function() {
        if ($(this).val().trim() !== "" && $(this).val() !== "0") {
            $(this).removeClass("is-invalid");
        }
    });
});