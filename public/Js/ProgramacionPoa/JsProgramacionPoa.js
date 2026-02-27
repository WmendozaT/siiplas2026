 base = $('[name="base"]').val();
//// instalar por composer la siguiente libreria :  composer require phpoffice/phpspreadsheet



































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