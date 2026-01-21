 base = $('[name="base"]').val();

//// Para Firmar Digitalmente
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

