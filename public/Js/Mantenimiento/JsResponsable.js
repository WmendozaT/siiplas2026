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




///// Js Update Informacion
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



//////// FORM ADD tp Administracion
$(document).ready(function() {
    $("#tp_adm1").change(function () {
        // Obtener el valor seleccionado directamente
        var tp_adm = $(this).val(); 
       // id = $('[name="id"]').val();
      
        // Validar que el valor no esté vacío (opcional, si tienes un "Seleccione...")
        if (tp_adm === "") return;
        var url = base + "mnt/get_reg_nal_add";
        var request = $.ajax({
            url: url,
            type: "POST",
            dataType: 'json',
            data: { 
                tipo_adm: tp_adm
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

//// select reg add
$(document).ready(function() {
    $("#reg_id1").change(function () {
        // Obtener el valor seleccionado directamente
        var reg_id = $(this).val(); 
        // Validar que el valor no esté vacío (opcional, si tienes un "Seleccione...")
       // if (tp_adm === "") return;
        var url = base + "mnt/get_dist_add";
        var request = $.ajax({
            url: url,
            type: "POST",
            dataType: 'json',
            data: { 
                dep_id: reg_id
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


///// Js Valida Add Formulario Responsable POA
$(document).ready(function() {
    $("#form_add").on("submit", function(e) {
        // 1. SIEMPRE detener el envío automático
        e.preventDefault();
        
        let formulario = this; // Guardamos la referencia al formulario
        let esValido = true;

        // 2. Validar campos de texto y número
        $("#fn_nom, #fn_pt, #fn_mt, #fn_ci, #fn_fono, #fn_cargo, #fn_usu, #fun_password").each(function() {
            if ($(this).val().trim() === "") {
                $(this).addClass("is-invalid").removeClass("is-valid");
                esValido = false;
            } else {
                $(this).removeClass("is-invalid").addClass("is-valid");
            }
        });

        // 3. Validar Selects (Administración, Regional, Distrital)
        $("#tp_adm1, #reg_id1, #dist_id").each(function() {
            let valor = $(this).val();
            if (valor === "0" || valor === "" || valor === null) {
                $(this).addClass("is-invalid").removeClass("is-valid");
                esValido = false;
            } else {
                $(this).removeClass("is-invalid").addClass("is-valid");
            }
        });

        // 4. Si la validación visual falla, no continuar
        if (!esValido) {
            alert("Por favor, complete todos los campos obligatorios correctamente.");
            return false;
        }

        // 5. Validación AJAX (¿Existe el usuario?)
        let usuarioDigitado = $("#fn_usu").val().trim();
        let urlVerificacion = base + "mnt/verif_usuario";

        // Deshabilitar botón para evitar múltiples clics
        $("#btnGuardar").prop("disabled", true);
        $("#textGuardar").text("Verificando...");

        $.ajax({
            url: urlVerificacion,
            type: "POST",
            dataType: 'json',
            data: { usuario: usuarioDigitado },
            success: function(response) {
                if (response.respuesta === 'correcto') {
                    // EL USUARIO NO EXISTE: Proceder con el guardado
                    $("#textGuardar").text("Guardando...");
                    $("#spinnerGuardar").removeClass("d-none");

                    // Esperar un breve momento para efecto visual y enviar
                    setTimeout(function() {
                        // Importante: usamos formulario.submit() (nativo) 
                        // para que no vuelva a entrar en este bucle de jQuery
                        formulario.submit();
                    }, 800);

                } else {
                    // EL USUARIO YA EXISTE
                    alert("El nombre de usuario '" + usuarioDigitado + "' ya se encuentra registrado. Intente con otro.");
                    $("#fn_usu").addClass("is-invalid").focus();
                    $("#btnGuardar").prop("disabled", false);
                    $("#textGuardar").text("Guardar");
                }
            },
            error: function() {
                alert("Error de conexión con el servidor.");
                $("#btnGuardar").prop("disabled", false);
                $("#textGuardar").text("Guardar");
            }
        });
    });

    // Limpiar clases de error dinámicamente cuando el usuario corrige el campo
    $("input, select").on("input change", function() {
        if ($(this).val().trim() !== "" && $(this).val() !== "0") {
            $(this).removeClass("is-invalid");
        }
    });
});






//// Generar Reporte en Base64 - Responsables POA
function generarReporteBase64() {
    // 1. Referencia al botón y su contenido original
    var $btn = $("#btnGenerarReporte");
    var originalHtml = $btn.html();
    tp = $('[name="tp_rep"]').val();
    //alert(tp)
    // 2. Mostrar Loading: Desactivar botón y cambiar icono
    $btn.prop("disabled", true);
    $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generando...');

    $.ajax({
        url: base + "mnt/Pdf_responsables",
        type: 'POST',
        dataType: 'json',
        data: { tp_rep: tp },
        success: function(response) {
            if (response.status === 'success') {
                // Abrir en pestaña nueva
                var win = window.open();
                if (win) {
                    win.document.write('<title>Reporte de Responsables</title>');
                    win.document.write('<iframe src="' + response.pdf + '" frameborder="0" style="border:0; top:0px; left:0px; bottom:0px; right:0px; width:100%; height:100%;" allowfullscreen></iframe>');
                    win.document.body.style.margin = '0';
                } else {
                    alert("Por favor, permita las ventanas emergentes.");
                }
            } else {
                alert("Error: " + response.message);
            }
        },
        error: function() {
            alert("Error de servidor");
        },
        complete: function() {
            // 3. RESTAURAR BOTÓN: Se ejecuta siempre (al terminar éxito o error)
            $btn.prop("disabled", false);
            $btn.html(originalHtml);
        }
    });
}

//// Eliminar Responsable
function eliminarResponsable(id, elemento) {
    if (confirm("¿Está seguro de que desea eliminar este registro?")) {
        $.ajax({
            url: base + "mnt/delete_responsable",
            type: 'POST',
            dataType: 'json',
            data: { 
                fun_id: id
            },
            success: function(response) {
                if (response.respuesta === 'correcto') {
                    // Animación para eliminar la fila de la tabla
                    $(elemento).closest('tr').fadeOut(400, function() {
                        $(this).remove();
                        // Opcional: Recargar contador de filas si es necesario
                    });
                    alert("Registro dado de baja con éxito");
                } else {
                    alert("Error: " + (response.mensaje || "No se pudo eliminar"));
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert("Error de comunicación con el servidor");
            }
        });
    }
}


//// loading para descargar en archivo excel
document.getElementById('btnExportar').addEventListener('click', function(e) {
    const btn = this;
    const iconContainer = document.getElementById('btnIcon');
    const textContainer = document.getElementById('btnText');
    
    // 1. Guardar contenido original
    const originalIcon = iconContainer.innerHTML;
    const originalText = textContainer.innerText;

    // 2. Aplicar estado "Cargando"
    btn.classList.add('disabled');
    iconContainer.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
    textContainer.innerText = ' Descargando archivo.xls...';

    // 3. Función para revisar si la cookie ya existe
    const checkCookie = setInterval(function() {
        if (document.cookie.indexOf("excel_status=terminado") !== -1) {
            
            // 4. Restaurar botón
            clearInterval(checkCookie);
            btn.classList.remove('disabled');
            iconContainer.innerHTML = originalIcon;
            textContainer.innerText = originalText;

            // 5. Limpiar la cookie para la próxima vez
            document.cookie = "excel_status=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        }
    }, 1000); // Revisa cada segundo
});


////// SEGUIMIENTO POA
//// select Apertura programatica a traves de la regional
  $(document).ready(function() {
    $("#reg_id2").change(function () {
        // Obtener el valor seleccionado directamente
        var reg_id = $(this).val(); 
        // Validar que el valor no esté vacío (opcional, si tienes un "Seleccione...")
        var url = base + "mnt/get_aper_seg";
        var request = $.ajax({
            url: url,
            type: "POST",
            dataType: 'json',
            data: { 
                dep_id: reg_id
            }
        });

        request.done(function (response) {
            if (response.respuesta == 'correcto') {
                $("#programa").html(response.select_aper);
            }
        });

        request.fail(function (jqXHR, textStatus, errorThrown) {
            console.error("Error en la petición: " + textStatus, errorThrown);
        });
    }); 
  })

/// select Unidad Responsable a traves del proyecto
$(document).ready(function() {
    $("#proy_id").change(function () {
        // Obtener el valor seleccionado directamente
        var proy_id = $(this).val(); 
        // Validar que el valor no esté vacío (opcional, si tienes un "Seleccione...")
        var url = base + "mnt/get_uresp_seg";
        var request = $.ajax({
            url: url,
            type: "POST",
            dataType: 'json',
            data: { 
                proy_id: proy_id
            }
        });

        request.done(function (response) {
            if (response.respuesta == 'correcto') {
                $("#uresp").html(response.select_unidad);
            }
        });

        request.fail(function (jqXHR, textStatus, errorThrown) {
            console.error("Error en la petición: " + textStatus, errorThrown);
        });
    }); 
  })


///// Js Valida Add Formulario Responsable-Seguimiento POA
$(document).ready(function() {
    $("#form_addspoa").on("submit", function(e) {
        // 1. SIEMPRE detener el envío automático
        e.preventDefault();
        
        let formulario = this; // Guardamos la referencia al formulario
        let esValido = true;

        // 2. Validar campos de texto y número
        $("#fn_usu, #fun_password").each(function() {
            if ($(this).val().trim() === "") {
                $(this).addClass("is-invalid").removeClass("is-valid");
                esValido = false;
            } else {
                $(this).removeClass("is-invalid").addClass("is-valid");
            }
        });

        // 3. Validar Selects (Regional, Programa, Unidad Responsable)
        $("#reg_id2,#proy_id,#com_id").each(function() {
            let valor = $(this).val();
            if (valor === "0" || valor === "" || valor === null) {
                $(this).addClass("is-invalid").removeClass("is-valid");
                esValido = false;
            } else {
                $(this).removeClass("is-invalid").addClass("is-valid");
            }
        });

        // 4. Si la validación visual falla, no continuar
        if (!esValido) {
            alert("Por favor, complete todos los campos obligatorios correctamente.");
            return false;
        }

        // 5. Validación AJAX (¿Existe el usuario?)
        let usuarioDigitado = $("#fn_usu").val().trim();
        let urlVerificacion = base + "mnt/verif_usuario";

        // Deshabilitar botón para evitar múltiples clics
        $("#btnGuardar").prop("disabled", true);
        $("#textGuardar").text("Verificando...");

        $.ajax({
            url: urlVerificacion,
            type: "POST",
            dataType: 'json',
            data: { usuario: usuarioDigitado },
            success: function(response) {
                if (response.respuesta === 'correcto') {
                    // EL USUARIO NO EXISTE: Proceder con el guardado
                    $("#textGuardar").text("Guardando...");
                    $("#spinnerGuardar").removeClass("d-none");

                    // Esperar un breve momento para efecto visual y enviar
                    setTimeout(function() {
                        // Importante: usamos formulario.submit() (nativo) 
                        // para que no vuelva a entrar en este bucle de jQuery
                        formulario.submit();
                    }, 800);

                } else {
                    // EL USUARIO YA EXISTE
                    alert("El nombre de usuario '" + usuarioDigitado + "' ya se encuentra registrado. Intente con otro.");
                    $("#fn_usu").addClass("is-invalid").focus();
                    $("#btnGuardar").prop("disabled", false);
                    $("#textGuardar").text("Guardar");
                }
            },
            error: function() {
                alert("Error de conexión con el servidor.");
                $("#btnGuardar").prop("disabled", false);
                $("#textGuardar").text("Guardar");
            }
        });
    });

    // Limpiar clases de error dinámicamente cuando el usuario corrige el campo
    $("input, select").on("input change", function() {
        if ($(this).val().trim() !== "" && $(this).val() !== "0") {
            $(this).removeClass("is-invalid");
        }
    });
});


///// Js Update Informacion responsable-seguimiento
$(document).ready(function() {
    $("#form_update").on("submit", function(e) {
        let esValido = true;

        // 1. Validar campos de texto y número
        // Nota: eliminé el duplicado de fn_usu que tenías en tu HTML
        $("#fn_usu, #fun_password").each(function() {
            if ($(this).val().trim() === "") {
                $(this).addClass("is-invalid");
                esValido = false;
            } else {
                $(this).removeClass("is-invalid").addClass("is-valid");
            }
        });

        // 2. Validar Select de Administración
        $("#proy_id,#com_id").each(function() {
            let valor = $(this).val();
            if (valor === "0" || valor === "" || valor === null) {
                $(this).addClass("is-invalid").removeClass("is-valid");
                esValido = false;
            } else {
                $(this).removeClass("is-invalid").addClass("is-valid");
            }
        });

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
                $("#form_update").off("submit").submit(); // Quita el evento actual y envía el form
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