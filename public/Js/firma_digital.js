 base = $('[name="base"]').val();

const FirmaDigital = {
    versionMinima: '1.3.0',

    async validarEstado() {
        // 1. Verificar ejecución (Tu JS devuelve {exito, datos: {versionCompatible}})
        const status = await jacobitusTotal.verificarEjecucion(this.versionMinima);
        if (!status.exito) throw new Error("Jacobitus no está ejecutándose o no responde.");
        if (status.datos && status.datos.versionCompatible === false) {
            throw new Error("Versión de Jacobitus no compatible. Se requiere " + this.versionMinima);
        }

        // 2. Verificar Token (Tu JS devuelve {exito, mensaje})
        const token = await jacobitusTotal.verificarDispositivo();
        if (!token.exito) throw new Error("No se detectó el dispositivo (Token) conectado.");
        
        return true;
    },

async ejecutarFirma(idVisualizador) {
    try {
        await this.validarEstado();

        // 1. Obtener Dispositivos
        const dispositivos = await jacobitusTotal.obtenerDispositivos();
        
        if (!dispositivos.exito || dispositivos.datos.dispositivos.length === 0) {
            throw new Error("No se detectó ningún Token conectado.");
        }
        
        const slot = dispositivos.datos.dispositivos[0].slot;
        
        // 2. Obtener PIN
        const pin = $('#tokenPin').val(); 
        if(!pin) throw new Error("Debe ingresar el PIN del Token.");

        // 3. Obtener Certificados
        const certs = await jacobitusTotal.obtenerCertificadosParaFirmaDigital(slot, pin);
        if(!certs.exito) throw new Error("PIN incorrecto o no se pudieron leer certificados.");
        
        const alias = certs.datos.certificados[0].alias;

        // 4. Limpiar Base64 del PDF
        // Jacobitus suele necesitar el Base64 PURO, sin el prefijo "data:application/pdf;base64,"
        let pdfBase64 = $('#' + idVisualizador).attr('src');
        if (pdfBase64.includes(',')) {
            pdfBase64 = pdfBase64.split(',')[1];
        }
        
        // 5. Firmar
        const resultado = await jacobitusTotal.firmarPdf(slot, pin, alias, pdfBase64);
        
        if (resultado.exito) {
            return 'data:application/pdf;base64,' + resultado.datos.docFirmado;
        } else {
            throw new Error(resultado.mensaje || "Error desconocido al firmar.");
        }
    } catch (error) {
        // CORRECCIÓN: alert nativo solo recibe un texto
        alert("Error de Firma: " + error.message);
        console.error("Detalle del error:", error);
        return null;
    }
}
};



let pdfPendiente = null;
let codigoReporte = null;

async function verReporteModal() {
    const url = base + "mnt/Pdf_responsables"; 

    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        success: async function(response) {
            if (response.status === 'success') {
                $('#frameReporte').attr('src', response.pdf);
                
                // USANDO TU NUEVO JS:
                try {
                    // Nota que ahora usamos jacobitusTotal (el nombre de la const en tu archivo)
                    const estado = await jacobitusTotal.verificarEjecucion('1.3.0');
                    if (estado.exito) {
                        console.log("Jacobitus listo: ", estado.datos.apiVersion);
                    } else {
                        console.warn("Jacobitus no está activo: ", estado.mensaje);
                    }
                } catch (e) {
                    console.error("No se pudo conectar al Jacobitus local.");
                }

                var myModal = new bootstrap.Modal(document.getElementById('modalReporte'));
                myModal.show();
            }
        }
    });
}


async function ejecutarFirmaDigital() {
    const pdfFirmado = await FirmaDigital.ejecutarFirma('frameReporte');
    if (pdfFirmado) {
        $('#frameReporte').attr('src', pdfFirmado); // Actualiza la vista
        // Aquí llamas a tu función para guardar en el servidor
        subirAlServidor(pdfFirmado);
    }
}


/*async function ejecutarFirmaDigital() {
    // 1. Obtener datos del token primero (necesitamos el slot y alias)
    const infoToken = await jacobitusTotal.obtenerDispositivos();
    
    if (!infoToken.exito || infoToken.datos.dispositivos.length === 0) {
        Swal.fire("Error", "No se detectó el token físico.", "error");
        return;
    }

    const slot = infoToken.datos.dispositivos[0].slot;
    const pin = $('#tokenPin').val(); // Aquí sí usas tu input de PIN

    // 2. Obtener certificados para sacar el Alias
    const certs = await jacobitusTotal.obtenerCertificadosParaFirmaDigital(slot, pin);
    if (!certs.exito) {
        Swal.fire("Error", "PIN incorrecto o error de lectura.", "error");
        return;
    }
    const alias = certs.datos.certificados[0].alias;

    // 3. FIRMAR
    const pdfBase64 = $('#frameReporte').attr('src');
    const resultado = await jacobitusTotal.firmarPdf(slot, pin, alias, pdfBase64);

    if (resultado.exito) {
        // El resultado viene en: resultado.datos.docFirmado
        const pdfFirmado = 'data:application/pdf;base64,' + resultado.datos.docFirmado;
        $('#frameReporte').attr('src', pdfFirmado);
        Swal.fire("¡Éxito!", "Documento firmado.", "success");
    } else {
        Swal.fire("Error", resultado.mensaje, "error");
    }
}*/











// //// Para Firmar Digitalmente
// function firmarYAbrirReporte() {
//     // 1. Mostrar un loader/mensaje inicial con console.log (alert detiene el script)
//     console.log('Generando documento... Espere mientras preparamos el reporte para la firma.');

//     // 2. Obtener el PDF sin firmar del servidor CI4
//     $.ajax({
//         url: base + "mnt/Pdf_responsables_sfirma", 
//         type: 'GET', 
//         dataType: 'json',
//         success: function(respuestaCI4) {

//             if (respuestaCI4.status === 'success' && respuestaCI4.pdf_sin_firmar) {
                
//                 // Mensaje informativo antes de la firma
//                 console.log('Iniciando conexión con Jacobitus. Verifique si aparece una ventana pidiendo su PIN.');

//                 // 3. Preparar los datos para Jacobitus API Local
//                 const datosJacobitus = {
//                     base64: respuestaCI4.pdf_sin_firmar, // Campo común usado por la API de ADSIB
//                     nombre: "Reporte_Responsables_POA.pdf"
//                 };


//                 // 4. Enviar a la API local de Jacobitus (Asegura el puerto 4500 o 9999)
//                 $.ajax({
//                     url: "https://localhost:9000/api/firmarPdf", 
//                     type: 'POST',
//                     contentType: 'application/json',
//                     data: JSON.stringify(datosJacobitus),
//                     success: function(respuestaJacobitus) {
//                         const pdfFirmadoBase64 = respuestaJacobitus.pdf_signed_base64 || (respuestaJacobitus.datos ? respuestaJacobitus.datos.archivo : null);

//                         if (pdfFirmadoBase64) {
                            
//                             // 5. Abrir el PDF ya firmado de forma segura usando Blob (Mejor que Data URL)
//                             const byteCharacters = atob(pdfFirmadoBase64);
//                             const byteNumbers = new Array(byteCharacters.length);
//                             for (let i = 0; i < byteCharacters.length; i++) {
//                                 byteNumbers[i] = byteCharacters.charCodeAt(i);
//                             }
//                             const byteArray = new Uint8Array(byteNumbers);
//                             const blob = new Blob([byteArray], { type: 'application/pdf' });
//                             const fileURL = URL.createObjectURL(blob);
                            
//                             window.open(fileURL, '_blank');
                            
//                             // Mensaje de éxito nativo
//                             alert('Éxito: El documento ha sido firmado y abierto correctamente.');

//                         } else {
//                             // Mensaje de error de firma nativo
//                             alert('Fallo en Firma: Jacobitus no devolvió un PDF firmado. Verifique el PIN o el certificado.');
//                         }
//                     },
//                     error: function(xhr) {
//                         console.error(xhr);
//                         // Mensaje de error de conexión local nativo
//                         alert('Error de Conexión Local: No se pudo conectar con el software Jacobitus (Puerto 4500/9999). Asegúrese de que esté abierto.');
//                     }
//                 });

//             } else {
//                 // Mensaje de error CI4 nativo
//                 alert('Error: Error al generar el PDF en el servidor CI4 o el base64 estaba vacío.');
//             }
//         },
//         error: function() {
//             // Mensaje de error de servidor nativo
//             alert('Error de Servidor: No se pudo contactar al servidor CI4 para obtener el PDF base.');
//         }
//     });
// }


// //// firmar digitalmente con estilo de alertas
// function firmarYAbrirReporte2() {
//     Swal.fire({
//         title: 'Generando documento...',
//         text: 'Espere mientras preparamos el reporte para la firma.',
//         allowOutsideClick: false,
//         didOpen: () => { Swal.showLoading(); }
//     });

//     $.ajax({
//         url: base + "mnt/Pdf_responsables_sfirma", 
//         type: 'GET', 
//         dataType: 'json',
//         success: function(respuestaCI4) {
//             if (respuestaCI4.status === 'success' && respuestaCI4.pdf_sin_firmar) {
                
//                 Swal.fire({
//                     title: 'Iniciando firma...',
//                     text: 'Se abrirá la ventana de Jacobitus para ingresar su PIN del Softoken (.p12).',
//                     icon: 'info',
//                     showConfirmButton: false,
//                     timer: 3000
//                 });

//                 const datosJacobitus = {
//                     base64: respuestaCI4.pdf_sin_firmar, 
//                     nombre: "Reporte_Responsables_POA.pdf"
//                 };

//                 $.ajax({
//                     // Intenta con 4500. Si no funciona, prueba 9999.
//                     url: "https://localhost:4500/api/firmarPdf", 
//                     type: 'POST',
//                     contentType: 'application/json',
//                     data: JSON.stringify(datosJacobitus),
//                     success: function(respuestaJacobitus) {
//                         const pdfFirmadoBase64 = respuestaJacobitus.pdf_signed_base64 || (respuestaJacobitus.datos ? respuestaJacobitus.datos.archivo : null);

//                         if (pdfFirmadoBase64) {
//                             // Convertir Base64 a Blob de forma segura para navegadores modernos
//                             const byteCharacters = atob(pdfFirmadoBase64);
//                             const byteNumbers = new Array(byteCharacters.length);
//                             for (let i = 0; i < byteCharacters.length; i++) {
//                                 byteNumbers[i] = byteNumbers[i] = byteCharacters.charCodeAt(i);
//                             }
//                             const byteArray = new Uint8Array(byteNumbers);
//                             const blob = new Blob([byteArray], { type: 'application/pdf' });
//                             const fileURL = URL.createObjectURL(blob);
                            
//                             window.open(fileURL, '_blank');
//                             Swal.fire('Éxito', 'El documento ha sido firmado con su Softoken y abierto.', 'success');

//                         } else {
//                             Swal.fire('Fallo en Firma', 'Jacobitus no devolvió un PDF firmado. Verifique el PIN.', 'error');
//                         }
//                     },
//                     error: function() {
//                         Swal.fire('Error de Conexión Local', 'No se pudo conectar con el software Jacobitus. Asegúrese de que esté abierto y configurado para aceptar CORS.', 'error');
//                     }
//                 });

//             } else {
//                 Swal.fire('Error', 'Error al generar el PDF en el servidor CI4.', 'error');
//             }
//         },
//         error: function() {
//             Swal.fire('Error de Servidor', 'No se pudo contactar al servidor CI4.', 'error');
//         }
//     });
// }

