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
        if (dep_id !== "") {
            
            // Referencia al contenedor de la tabla
            const $listado = $('#listado_uo');
            $listado.html('<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>');
            
            $.ajax({
			    url: base + "m2/obtener_uorganizacionales_disponibles",
			    type: 'POST',
			    data: { 
			        id: dep_id,
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

	// --- NUEVA FUNCIÓN REUTILIZABLE ---
	async function ejecutarCargaPdf(id_rep) {
	    idRegionalActiva = id_rep; 

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
	    const urlFinal = base + "m2/reporte_uorganizacional_gral_poa/" + id_rep + "?t=" + new Date().getTime();

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

	    const textoRegional = this.options[this.selectedIndex].text.replace(/\.pdf$/i, '');
	    nombreRegionalBadge.innerText = textoRegional; 

	    modalPdf.show();
	    ejecutarCargaPdf(id_rep); // Llamamos a la función

	    this.value = ""; 
	});

	// --- LISTENER DEL BOTÓN REFRESH ---
	btnRefresh.addEventListener('click', function() {
	    if (idRegionalActiva !== null) {
	        // Opcional: añadir animación de giro al icono de Tabler
	        const icon = this.querySelector('i');
	        icon.classList.add('ti-spin'); 
	        
	        ejecutarCargaPdf(idRegionalActiva).finally(() => {
	            icon.classList.remove('ti-spin');
	        });
	    }
	});
});
































// //// Subir archivo excel
// document.addEventListener('DOMContentLoaded', function() {
//     const form = document.getElementById('formImportarExcel');
//     const input = document.getElementById('archivo_excel');
//     const errorDiv = document.getElementById('error-mensaje');
//     const btnSubmit = document.getElementById('btnImportar');
//     const spinner = document.getElementById('spinnerLoading');
//     const btnText = document.getElementById('btnText');
//     const modalElement = document.getElementById('modalExcel');

//     const btnVaciar = document.getElementById('btnVaciarTabla');

//     // Función para mostrar mensajes "decentes" (Bootstrap)
//     function mensajeUI(tipo, texto) {
//         errorDiv.style.display = 'block';
//         errorDiv.innerHTML = `<div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
//             ${texto}
//             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
//         </div>`;
//     }

//     modalElement.addEventListener('hidden.bs.modal', function () {
//         form.reset();
//         errorDiv.style.display = 'none';
//         errorDiv.innerHTML = '';
//         input.classList.remove('is-invalid');
//         restaurarBoton();
//     });

//     form.addEventListener('submit', function(e) {
//         e.preventDefault(); // IMPORTANTE: Detener el envío tradicional
        
//         const archivo = input.files[0];
//         errorDiv.style.display = 'none';

//         // Validaciones... (las que ya tienes están bien)
//         if (!archivo) { mensajeUI('danger', "Seleccione un archivo."); return; }

//         // --- PREPARAR AJAX ---
//         const formData = new FormData(this);
        
//         btnSubmit.disabled = true;
//         spinner.style.display = 'inline-block';
//         btnText.innerText = 'Procesando...';

//         $.ajax({
//             url: $(this).attr('action'),
//             type: 'POST',
//             data: formData,
//             processData: false,
//             contentType: false,
//             dataType: 'json',
//             success: function(response) {

//                 if (response.status === 'success') {
//                     mensajeUI('success', '<b>✅ ¡Éxito!</b> ' + response.message);
//                     setTimeout(() => { location.reload(); }, 2000);
//                 } else {
//                     // --- NUEVA LÓGICA PARA MOSTRAR ERRORES DETALLADOS ---
//                     let htmlErrores = `<b>⚠️ ${response.message}</b>`;
                    
//                     if (response.detalles && response.detalles.length > 0) {
//                         htmlErrores += `<div class="mt-2" style="max-height: 200px; overflow-y: auto;">
//                             <ul class="list-group list-group-flush small text-start">`;
                        
//                         response.detalles.forEach(function(error) {
//                             htmlErrores += `<li class="list-group-item list-group-item-warning py-1">${error}</li>`;
//                         });
                        
//                         htmlErrores += `</ul></div>`;
//                     }
                    
//                     mensajeUI('warning', htmlErrores);
//                     restaurarBoton();
//                 }
//             },
//             error: function(xhr) {
//                 // AQUÍ EVITAMOS LA PÁGINA NARANJA 404/500
//                 let msgError = 'Error desconocido en el servidor.';
//                 if (xhr.status === 404) msgError = '<b>Error 404:</b> No se encontró la ruta en el controlador.';
//                 if (xhr.status === 500) msgError = '<b>Error 500:</b> Error interno de base de datos.';
                
//                 mensajeUI('danger', msgError);
//                 restaurarBoton();
//             }
//         });
//     });



//     // --- LÓGICA DE VACIAR TABLA (AÑADIDA AQUÍ) ---
//     if (btnVaciar) {
//         btnVaciar.addEventListener('click', function() {
//             // Alert de confirmación nativo
//             const confirmar = confirm("⚠️ ¿ESTÁ SEGURO?\n\nEsta acción eliminará TODOS los registros de la Asignacion presupuestaria de la gestión actual.");

//             if (confirmar) {
//                 const originalHTML = btnVaciar.innerHTML;
//                 btnVaciar.disabled = true;
//                 btnVaciar.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Vaciando...`;

//                 $.ajax({
//                     url: base + "mnt/eliminar_ppto_asignado",
//                     type: 'POST',
//                     dataType: 'json',
//                     success: function(res) {
//                         if (res.status === 'success') {
//                             alert("✅ " + res.message);
//                             location.reload();
//                         } else {
//                             alert("❌ Error: " + res.message);
//                             restaurarBtn2();
//                         }
//                     },
//                     error: function(xhr, status, error) {
//                         // --- MANEJO DE ERRORES HTTP ---
//                         let mensajeError = "Ocurrió un error inesperado.";
                        
//                         if (xhr.status === 404) {
//                             mensajeError = "❌ Error 404: No se encontró la ruta. Verifique la URL en su controlador.";
//                         } else if (xhr.status === 500) {
//                             mensajeError = "❌ Error 500: Error interno del servidor. Es posible que existan restricciones de base de datos (llaves foráneas).";
//                         } else if (status === 'timeout') {
//                             mensajeError = "❌ Tiempo de espera agotado. El servidor tarda mucho en responder.";
//                         } else {
//                             mensajeError = "❌ Error: " + (error || "No se pudo conectar con el servidor.");
//                         }

//                         alert(mensajeError);
//                         restaurarBtn2();
//                     }
//                 });

//                 function restaurarBtn2() {
//                     btnVaciar.disabled = false;
//                     btnVaciar.innerHTML = originalHTML;
//                 }
//             }
//         });
//     }


//     function restaurarBoton() {
//         btnSubmit.disabled = false;
//         spinner.style.display = 'none';
//         btnText.innerText = 'Procesar e Importar';
//         if(document.getElementById('btnCancelModal')) document.getElementById('btnCancelModal').style.display = 'inline-block';
//         if(document.getElementById('btnCloseModal')) document.getElementById('btnCloseModal').style.display = 'inline-block';
//     }
// });

// //// loading para DESCARGAR en archivo excel
// document.getElementById('btnExportar').addEventListener('click', async function(e) {
//     e.preventDefault(); // Detiene la navegación inmediata
    
//     const btn = this;
//     const url = btn.href;
//     const iconContainer = document.getElementById('btnIcon');
//     const textContainer = document.getElementById('btnText');
    
//     const originalIcon = iconContainer.innerHTML;
//     const originalText = textContainer.innerText;

//     // 1. UI en estado de carga
//     btn.classList.add('disabled');
//     iconContainer.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
//     textContainer.innerText = ' Verificando...';

//     try {
//         // 2. PETICIÓN SILENCIOSA: Verificamos si la ruta existe y responde bien
//         const response = await fetch(url, { method: 'GET' });

//         if (!response.ok) {
//             // Si el servidor responde 404 o 500, lanzamos error al bloque catch
//             throw new Error(`Servidor respondió con error ${response.status}`);
//         }

//         // 3. SI TODO ESTÁ BIEN: Disparamos la descarga
//         // Usamos un iframe invisible para que el usuario no vea cambios de URL
//         let iframe = document.getElementById('download_iframe');
//         if (!iframe) {
//             iframe = document.createElement('iframe');
//             iframe.id = 'download_iframe';
//             iframe.style.display = 'none';
//             document.body.appendChild(iframe);
//         }
//         iframe.src = url;

//         // 4. ESPERAR COOKIE (Tu lógica actual)
//         const checkCookie = setInterval(function() {
//             if (document.cookie.indexOf("excel_status=terminado") !== -1) {
//                 clearInterval(checkCookie);
//                 restaurarBoton3();
//                 document.cookie = "excel_status=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
//             }
//         }, 1000);

//     } catch (error) {
//         // 5. MANEJO DE ERRORES: Aquí capturamos el 404/500 sin salir de la página
//         console.error("Error capturado:", error);
//         alert("No se pudo generar el archivo. Por favor, verifique que la ruta exista o intente más tarde.");
//         restaurarBoton3();
//     }

//     function restaurarBoton3() {
//         btn.classList.remove('disabled');
//         iconContainer.innerHTML = originalIcon;
//         textContainer.innerText = originalText;
//     }
// });


// //// Ver Detalle de Partidas por Unidad Organizacional
// $(document).on('click', '.btn-ver-partidas', function() {
//     const aperId = $(this).data('id');
//     const nombreUnidad = $(this).data('nombre');
  
//     // Configurar títulos
//     $('#tituloModal').text('Detalle de Partidas');
//     $('#subtituloUnidad').text(nombreUnidad);
//     $('#contenidoPartidas').html('<div class="text-center p-3"><div class="spinner-border text-primary"></div></div>');
    
//     // Mostrar modal
//     const myModal = new bootstrap.Modal(document.getElementById('modalPartidas'));
//     myModal.show();

//     // Petición al servidor
//     $.ajax({
//         url: base + "mnt/get_detalle_poa_ppto",
//         type: 'POST',
//         data: { id: aperId },
//         success: function(response) {
//             // El controlador debe devolver un fragmento de HTML (la tabla)
//             $('#contenidoPartidas').html(response);
//         },
//         error: function() {
//             $('#contenidoPartidas').html('<div class="alert alert-danger">Error al cargar los datos.</div>');
//         }
//     });
// });