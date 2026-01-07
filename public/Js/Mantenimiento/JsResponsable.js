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
        let mensaje = "";

        // 1. Validar campos de texto y número (que no estén vacíos)
        $("#fn_nom, #fn_pt, #fn_mt, #fn_ci, #fn_fono, #fn_cargo, #fn_usu").each(function() {
            if ($(this).val().trim() === "") {
                $(this).addClass("is-invalid"); // Clase de borde rojo de Bootstrap
                esValido = false;
            } else {
                $(this).removeClass("is-invalid").addClass("is-valid");
            }
        });

        // 2. Validar Selects (que no sea el valor "0" o vacío)
        if ($("#tp_adm").val() === "0" || $("#tp_adm").val() === "") {
            $("#tp_adm").addClass("is-invalid");
            esValido = false;
        } else {
            $("#tp_adm").removeClass("is-invalid").addClass("is-valid");
        }

        // 3. Si algo falla, detenemos el envío
        if (!esValido) {
            e.preventDefault(); // Evita que se recargue la página
            alert("Por favor, complete todos los campos obligatorios.");
        }
    });

    // Limpiar el estado de error cuando el usuario escribe
    $("input, select").on("change keyup", function() {
        if ($(this).val().trim() !== "" && $(this).val() !== "0") {
            $(this).removeClass("is-invalid");
        }
    });
});