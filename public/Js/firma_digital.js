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



//let pdfPendiente = null;
//let codigoReporte = null;

async function verReporteModal() {
    const url = base + "mnt/Pdf_responsables"; 
alert(url)
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
