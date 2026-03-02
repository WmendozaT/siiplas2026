 base = $('[name="base"]').val();
//// instalar por composer la siguiente libreria :  composer require phpoffice/phpspreadsheet

document.addEventListener('DOMContentLoaded', function() {
    const selectAdd = document.getElementById('dep_id_add');
    const modalElement = new bootstrap.Modal(document.getElementById('modalAdicionarPoa'), {
        backdrop: 'static',
        keyboard: false
    });

    selectAdd.addEventListener('change', function() {
        const dep_id = this.value;

        // Obtener el tp_id del atributo data del option seleccionado
    	const selectedOption = this.options[this.selectedIndex];
    	const tp_id = selectedOption.getAttribute('data-tp');

        if (dep_id !== "") {
            
            // Referencia al contenedor de la tabla
            const $listado = $('#listado_uo');
            $listado.html('<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>');
            
            $.ajax({
			    url: base + "m2/obtener_uorganizacionales_disponibles",
			    type: 'POST',
			    data: { 
			        id: dep_id,
                	tp_id: tp_id,
			        "<?= csrf_token() ?>": "<?= csrf_hash() ?>" 
			    },
			    dataType: 'json',
			    success: function(response) {
			        if (response.status === 'success') {
			            $('#listado_uo').html(response.datos);
			            modalElement.show();
			        }
			    },
			    error: function(xhr, status, error) {
			        // Evitamos que el navegador lance el error genérico y personalizamos el mensaje
			        let mensajeError = "Ocurrió un inconveniente al cargar las unidades.";
			        
			        if (xhr.status === 404) {
			            mensajeError = "La ruta de consulta no fue encontrada (404).";
			        } else if (xhr.status === 500) {
			            mensajeError = "Error interno del servidor (500). Intente más tarde.";
			        } else if (xhr.status === 403) {
			            mensajeError = "Acceso denegado o sesión expirada (403).";
			        }

			        // Mostramos el error con estilo Spike (Light Danger)
			        $('#listado_uo').html(`
			            <div class="alert alert-light-danger border-danger text-danger d-flex align-items-center" role="alert">
			                <i class="ti ti-alert-circle fs-5 me-2"></i>
			                <div>${mensajeError}</div>
			            </div>
			        `);
			        
			        // Opcional: Abrir el modal de todas formas para mostrar el error dentro
			        modalElement.show();
			    },
			    complete: function() {
			        selectAdd.value = "";
			    }
			});
        }
    });



    // --- NUEVA LÓGICA PARA PDF ---
   	const selectPdf = document.getElementById('dep_id_pdf');
	const modalPdf = new bootstrap.Modal(document.getElementById('modalVisorPdf'), { backdrop: 'static' });
	const iframe = document.getElementById('iframe_pdf');
	const loader = document.getElementById('loading_pdf');
	const nombreRegionalBadge = document.getElementById('nombre_regional_pdf');
	const btnRefresh = document.getElementById('btn_refresh_pdf'); // El ID de tu botón nuevo

	// Variable para recordar qué ID se cargó
	let idRegionalActiva = null;
	let tpIdActivo = null; // <--- AGREGAR ESTA LÍNEA


	// --- NUEVA FUNCIÓN REUTILIZABLE ---
	async function ejecutarCargaPdf(id_rep,tp_id) {
	    idRegionalActiva = id_rep; 
	    tpIdActivo = tp_id; // Guardamos el tp_id para el botón refresh

	    // 1. Preparar UI (Inmediato)
	    iframe.style.display = 'none';
	    loader.style.display = 'block';
	    
	    // IMPORTANTE: Liberar memoria del blob anterior y limpiar src
	    if (iframe.src.startsWith('blob:')) {
	        URL.revokeObjectURL(iframe.src);
	    }
	    iframe.src = "about:blank"; // Limpia el visor para que no se vea el reporte viejo mientras carga
	    
	    const alertasPrevias = iframe.parentNode.querySelectorAll('.alert');
	    alertasPrevias.forEach(alerta => alerta.remove());

	    // Añadimos un timestamp (?t=...) para saltar la caché del servidor
	    const urlFinal = base + "m2/reporte_uorganizacional_gral_poa/" + id_rep + "/" + tp_id + "?t=" + new Date().getTime();

	    try {
	        const respuesta = await fetch(urlFinal, { method: 'GET' });

	        if (!respuesta.ok) {
	            let errorMsg = `Error ${respuesta.status}`;
	            if (respuesta.status === 404) errorMsg = "Reporte no encontrado (404)";
	            if (respuesta.status === 500) errorMsg = "Error interno del servidor (500)";
	            throw new Error(errorMsg);
	        }

	        const blob = await respuesta.blob();
	        const urlBlob = URL.createObjectURL(blob);
	        
	        iframe.src = urlBlob;

	        // Este evento quita el loading SOLO cuando el navegador termina de renderizar el nuevo PDF
	        iframe.onload = function() {
	            loader.style.display = 'none';
	            iframe.style.display = 'block';
	        };

	    } catch (error) {
	        loader.style.display = 'none';
	        iframe.style.display = 'none';
	        
	        const errorContainer = document.createElement('div');
	        errorContainer.className = "alert alert-light-danger text-danger m-4 border-danger animate__animated animate__fadeIn";
	        errorContainer.innerHTML = `
	            <div class="d-flex align-items-center">
	                <i class="ti ti-alert-circle fs-7 me-3"></i>
	                <div>
	                    <h6 class="mb-1 fw-bold text-danger">Fallo en la Generación</h6>
	                    <small>${error.message}. Por favor, contacte al Administrador Nacional de Planificación.</small>
	                </div>
	            </div>`;
	        iframe.parentNode.appendChild(errorContainer);
	    }
	}

	// --- LISTENER DEL SELECT ---
	selectPdf.addEventListener('change', function() {
	    const id_rep = this.value;
	    if (id_rep === "") return;

	    // Capturar el tp_id desde el atributo data-tp del option seleccionado
    	const selectedOption = this.options[this.selectedIndex];
    	const tp_id = selectedOption.getAttribute('data-tp') || 0; // 0 por defecto para el institucional

	    const textoRegional = this.options[this.selectedIndex].text.replace(/\.pdf$/i, '');
	    nombreRegionalBadge.innerText = textoRegional; 

	    modalPdf.show();
	    ejecutarCargaPdf(id_rep, tp_id); // Llamamos a la función

	    this.value = ""; 
	});

	// --- LISTENER DEL BOTÓN REFRESH ---
	btnRefresh.addEventListener('click', function() {
	    if (idRegionalActiva !== null) {
	        // Opcional: añadir animación de giro al icono de Tabler
	        const icon = this.querySelector('i');
	        icon.classList.add('ti-spin'); 
	        
	        ejecutarCargaPdf(idRegionalActiva,tpIdActivo).finally(() => {
	            icon.classList.remove('ti-spin');
	        });
	    }
	});

	//////// para generar excel

	const selectXls = document.getElementById('dep_id_xls');
    const loaderExcel = document.getElementById('loading_excel_overlay');

    selectXls.addEventListener('change', async function() {
        const id_rep = this.value;
        if (id_rep === "") return;

        const selectedOption = this.options[this.selectedIndex];
        const tp_id = selectedOption.getAttribute('data-tp') || 0;
        const nombreArchivo = selectedOption.text;

        // 1. Mostrar el Loader
        loaderExcel.style.display = 'flex';

        const url = base + "m2/exportar_excel_poa/" + id_rep + "/" + tp_id;

        try {
            const response = await fetch(url, { method: 'GET' });

            if (!response.ok) throw new Error('Error en el servidor');

            // Recibir el archivo como Blob
            const blob = await response.blob();
            
            // 2. Crear descarga
            const urlBlob = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = urlBlob;
            a.download = nombreArchivo.endsWith('.Xls') ? nombreArchivo : nombreArchivo + '.xlsx';
            document.body.appendChild(a);
            a.click();
            
            window.URL.revokeObjectURL(urlBlob);
            a.remove();

        } catch (error) {
            console.error(error);
            alert("No se pudo generar el archivo Excel. Verifique su conexión.");
        } finally {
            // 3. Ocultar el Loader y resetear select
            loaderExcel.style.display = 'none';
            this.value = ""; 
        }
    });

});











