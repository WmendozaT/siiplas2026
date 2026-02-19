 base = $('[name="base"]').val();
//// instalar por composer la siguiente libreria :  composer require phpoffice/phpspreadsheet
//// Subir archivo excel
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formImportarExcel');
    const input = document.getElementById('archivo_excel');
    const errorDiv = document.getElementById('error-mensaje');
    const btnSubmit = document.getElementById('btnImportar');
    const spinner = document.getElementById('spinnerLoading');
    const btnText = document.getElementById('btnText');
    const modalElement = document.getElementById('modalExcel');

    // Función para mostrar mensajes "decentes" (Bootstrap)
    function mensajeUI(tipo, texto) {
        errorDiv.style.display = 'block';
        errorDiv.innerHTML = `<div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
            ${texto}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;
    }

    modalElement.addEventListener('hidden.bs.modal', function () {
        form.reset();
        errorDiv.style.display = 'none';
        errorDiv.innerHTML = '';
        input.classList.remove('is-invalid');
        restaurarBoton();
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault(); // IMPORTANTE: Detener el envío tradicional
        
        const archivo = input.files[0];
        errorDiv.style.display = 'none';

        // Validaciones... (las que ya tienes están bien)
        if (!archivo) { mensajeUI('danger', "Seleccione un archivo."); return; }

        // --- PREPARAR AJAX ---
        const formData = new FormData(this);
        
        btnSubmit.disabled = true;
        spinner.style.display = 'inline-block';
        btnText.innerText = 'Procesando...';

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {

                if (response.status === 'success') {
                    mensajeUI('success', '<b>✅ ¡Éxito!</b> ' + response.message);
                    setTimeout(() => { location.reload(); }, 2000);
                } else {
                    // --- NUEVA LÓGICA PARA MOSTRAR ERRORES DETALLADOS ---
                    let htmlErrores = `<b>⚠️ ${response.message}</b>`;
                    
                    if (response.detalles && response.detalles.length > 0) {
                        htmlErrores += `<div class="mt-2" style="max-height: 200px; overflow-y: auto;">
                            <ul class="list-group list-group-flush small text-start">`;
                        
                        response.detalles.forEach(function(error) {
                            htmlErrores += `<li class="list-group-item list-group-item-warning py-1">${error}</li>`;
                        });
                        
                        htmlErrores += `</ul></div>`;
                    }
                    
                    mensajeUI('warning', htmlErrores);
                    restaurarBoton();
                }
            },
            error: function(xhr) {
                // AQUÍ EVITAMOS LA PÁGINA NARANJA 404/500
                let msgError = 'Error desconocido en el servidor.';
                if (xhr.status === 404) msgError = '<b>Error 404:</b> No se encontró la ruta en el controlador.';
                if (xhr.status === 500) msgError = '<b>Error 500:</b> Error interno de base de datos.';
                
                mensajeUI('danger', msgError);
                restaurarBoton();
            }
        });
    });

    function restaurarBoton() {
        btnSubmit.disabled = false;
        spinner.style.display = 'none';
        btnText.innerText = 'Procesar e Importar';
        if(document.getElementById('btnCancelModal')) document.getElementById('btnCancelModal').style.display = 'inline-block';
        if(document.getElementById('btnCloseModal')) document.getElementById('btnCloseModal').style.display = 'inline-block';
    }
});



function initVaciarTabla() {
    alert('hola mundo')
    const btnVaciar = document.getElementById('btnVaciarTodo');
    
    // Verificamos que el botón exista en la página actual
    if (btnVaciar) {
        btnVaciar.addEventListener('click', function() {
            
            // 1. Confirmación Nativa
            if (confirm('⚠️ ¿ESTÁ SEGURO? \n\nEsta acción eliminará TODOS los registros presupuestarios de la gestión actual. Esta operación no se puede deshacer.')) {
                
                // 2. Feedback Visual: Deshabilitar y poner Spinner
                const contenidoOriginal = btnVaciar.innerHTML;
                btnVaciar.disabled = true;
                btnVaciar.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Eliminando...`;

                // 3. Llamada AJAX (Ajusta la URL a tu ruta real)
                $.ajax({
                    url: '<?= base_url("index.php/presupuesto/eliminar_todo_ppto") ?>',
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            alert('✅ Éxito: ' + response.message);
                            location.reload(); // Recargar para ver la tabla vacía
                        } else {
                            alert('❌ Error: ' + response.message);
                            restaurarBoton(btnVaciar, contenidoOriginal);
                        }
                    },
                    error: function() {
                        alert('❌ Error crítico: No se pudo comunicar con el servidor.');
                        restaurarBoton(btnVaciar, contenidoOriginal);
                    }
                });
            }
        });
    }
}

// Función auxiliar para desbloquear el botón si algo falla
function restaurarBoton(boton, contenido) {
    boton.disabled = false;
    boton.innerHTML = contenido;
}