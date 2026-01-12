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



function firmarYAbrirReporte() {
    // 1. Mostrar un loader/mensaje inicial con console.log (alert detiene el script)
    console.log('Generando documento... Espere mientras preparamos el reporte para la firma.');

    // 2. Obtener el PDF sin firmar del servidor CI4
    $.ajax({
        url: base + "mnt/Pdf_responsables_sfirma", 
        type: 'GET', 
        dataType: 'json',
        success: function(respuestaCI4) {

            if (respuestaCI4.status === 'success' && respuestaCI4.pdf_sin_firmar) {
                
                // Mensaje informativo antes de la firma
                console.log('Iniciando conexión con Jacobitus. Verifique si aparece una ventana pidiendo su PIN.');

                // 3. Preparar los datos para Jacobitus API Local
                const datosJacobitus = {
                    base64: respuestaCI4.pdf_sin_firmar, // Campo común usado por la API de ADSIB
                    nombre: "Reporte_Responsables_POA.pdf"
                };


                // 4. Enviar a la API local de Jacobitus (Asegura el puerto 4500 o 9999)
                $.ajax({
                    url: "https://localhost:9000/api/firmarPdf", 
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(datosJacobitus),
                    success: function(respuestaJacobitus) {
                        const pdfFirmadoBase64 = respuestaJacobitus.pdf_signed_base64 || (respuestaJacobitus.datos ? respuestaJacobitus.datos.archivo : null);

                        if (pdfFirmadoBase64) {
                            
                            // 5. Abrir el PDF ya firmado de forma segura usando Blob (Mejor que Data URL)
                            const byteCharacters = atob(pdfFirmadoBase64);
                            const byteNumbers = new Array(byteCharacters.length);
                            for (let i = 0; i < byteCharacters.length; i++) {
                                byteNumbers[i] = byteCharacters.charCodeAt(i);
                            }
                            const byteArray = new Uint8Array(byteNumbers);
                            const blob = new Blob([byteArray], { type: 'application/pdf' });
                            const fileURL = URL.createObjectURL(blob);
                            
                            window.open(fileURL, '_blank');
                            
                            // Mensaje de éxito nativo
                            alert('Éxito: El documento ha sido firmado y abierto correctamente.');

                        } else {
                            // Mensaje de error de firma nativo
                            alert('Fallo en Firma: Jacobitus no devolvió un PDF firmado. Verifique el PIN o el certificado.');
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        // Mensaje de error de conexión local nativo
                        alert('Error de Conexión Local: No se pudo conectar con el software Jacobitus (Puerto 4500/9999). Asegúrese de que esté abierto.');
                    }
                });

            } else {
                // Mensaje de error CI4 nativo
                alert('Error: Error al generar el PDF en el servidor CI4 o el base64 estaba vacío.');
            }
        },
        error: function() {
            // Mensaje de error de servidor nativo
            alert('Error de Servidor: No se pudo contactar al servidor CI4 para obtener el PDF base.');
        }
    });
}


//// firmar digitalmente con estilo de alertas
function firmarYAbrirReporte2() {
    Swal.fire({
        title: 'Generando documento...',
        text: 'Espere mientras preparamos el reporte para la firma.',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    $.ajax({
        url: base + "mnt/Pdf_responsables_sfirma", 
        type: 'GET', 
        dataType: 'json',
        success: function(respuestaCI4) {
            if (respuestaCI4.status === 'success' && respuestaCI4.pdf_sin_firmar) {
                
                Swal.fire({
                    title: 'Iniciando firma...',
                    text: 'Se abrirá la ventana de Jacobitus para ingresar su PIN del Softoken (.p12).',
                    icon: 'info',
                    showConfirmButton: false,
                    timer: 3000
                });

                const datosJacobitus = {
                    base64: respuestaCI4.pdf_sin_firmar, 
                    nombre: "Reporte_Responsables_POA.pdf"
                };

                $.ajax({
                    // Intenta con 4500. Si no funciona, prueba 9999.
                    url: "https://localhost:4500/api/firmarPdf", 
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(datosJacobitus),
                    success: function(respuestaJacobitus) {
                        const pdfFirmadoBase64 = respuestaJacobitus.pdf_signed_base64 || (respuestaJacobitus.datos ? respuestaJacobitus.datos.archivo : null);

                        if (pdfFirmadoBase64) {
                            // Convertir Base64 a Blob de forma segura para navegadores modernos
                            const byteCharacters = atob(pdfFirmadoBase64);
                            const byteNumbers = new Array(byteCharacters.length);
                            for (let i = 0; i < byteCharacters.length; i++) {
                                byteNumbers[i] = byteNumbers[i] = byteCharacters.charCodeAt(i);
                            }
                            const byteArray = new Uint8Array(byteNumbers);
                            const blob = new Blob([byteArray], { type: 'application/pdf' });
                            const fileURL = URL.createObjectURL(blob);
                            
                            window.open(fileURL, '_blank');
                            Swal.fire('Éxito', 'El documento ha sido firmado con su Softoken y abierto.', 'success');

                        } else {
                            Swal.fire('Fallo en Firma', 'Jacobitus no devolvió un PDF firmado. Verifique el PIN.', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error de Conexión Local', 'No se pudo conectar con el software Jacobitus. Asegúrese de que esté abierto y configurado para aceptar CORS.', 'error');
                    }
                });

            } else {
                Swal.fire('Error', 'Error al generar el PDF en el servidor CI4.', 'error');
            }
        },
        error: function() {
            Swal.fire('Error de Servidor', 'No se pudo contactar al servidor CI4.', 'error');
        }
    });
}



//// Generar Reporte en Base64
function generarReporteBase64() {
    // 1. Referencia al botón y su contenido original
    var $btn = $("#btnGenerarReporte");
    var originalHtml = $btn.html();

    // 2. Mostrar Loading: Desactivar botón y cambiar icono
    $btn.prop("disabled", true);
    $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generando...');

    $.ajax({
        url: base + "mnt/Pdf_responsables",
        type: 'GET',
        dataType: 'json',
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


function eliminarResponsable(id, elemento) {
    // Confirmación nativa del navegador
    if (confirm("¿Está seguro de que desea eliminar este registro?")) {
        $.ajax({
            url: 'mnt/delete_responsable', // Asegúrate de que la ruta sea correcta
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alert("Eliminado correctamente");
                    // Elimina la fila de la tabla visualmente
                    $(elemento).closest('tr').fadeOut(500, function() {
                        $(this).remove();
                    });
                } else {
                    alert("Error: " + response.message);
                }
            },
            error: function() {
                alert("Error de conexión con el servidor");
            }
        });
    }
}