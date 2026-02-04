 base = $('[name="base"]').val();

///// Js Update formulario de configuracion del Sistema
$(document).ready(function() {
    $("#form_conf").on("submit", function(e) {
        let esValido = true;

        // Validar textos y números
        $("#NombreEntidad, #SiglaEntidad, #MisionEntidad, #VisionEntidad, #conf_gestion_desde, #conf_gestion_hasta").each(function() {
            if ($(this).val().trim() === "") {
                $(this).addClass("is-invalid").removeClass("is-valid");
                esValido = false;
            } else {
                $(this).removeClass("is-invalid").addClass("is-valid");
            }
        });

        // VALIDACIÓN DE FECHAS
        const fIni = $("#EvalIni").val();
        const fFin = $("#EvalFin").val();

        if (!fIni) { $("#EvalIni").addClass("is-invalid"); esValido = false; }
        if (!fFin) { $("#EvalFin").addClass("is-invalid"); esValido = false; }

        if (fIni && fFin && fIni > fFin) {
            $("#EvalFin").addClass("is-invalid");
            alert("Error: La fecha final debe ser posterior a la fecha inicial.");
            esValido = false;
        }

        // Lógica de envío con alerta de confirmación
        if (!esValido) {
            e.preventDefault();
            alert("Por favor, complete todos los campos obligatorios correctamente.");
        } else {
            e.preventDefault();
            if (confirm("¿Está seguro de guardar los cambios?")) {
                $("#btnGuardar").prop("disabled", true);
                $("#textGuardar").text("Procesando...");
                $("#spinnerGuardar").removeClass("d-none");

                setTimeout(() => {
                    $("#form_conf").off("submit").submit();
                }, 800);
            }
        }
    });

    // Limpiar errores al cambiar datos (incluyendo fechas)
    $("input, select, textarea").on("change keyup", function() {
        if ($(this).val() !== "") {
            $(this).removeClass("is-invalid");
        }
    });
});


//// Js para actualizar estado de los modulos disponibles
$(document).on('change', '.btn-switch-updates', function() {
    const $input = $(this);
    const id      = $input.data('id');      
    const columna = $input.data('columna'); 
    const valor   = $input.is(':checked') ? 1 : 0;

    // 3. Captura de CSRF (Importante: capturarlos justo antes del envío)
    const csrfName = $('meta[name="csrf-token-name"]').attr('content');
    const csrfHash = $('meta[name="csrf-token-value"]').attr('content');

    // 4. Preparación del objeto
    const dataPost = {
        id: id,
        columna: columna,
        valor: valor
    };

    // 5. Inyección dinámica del token
    dataPost[csrfName] = csrfHash;

    $.ajax({
        url: base + "mnt/update_estado_modulos",
        type: 'POST',
        data: dataPost,
        dataType: 'json',
        beforeSend: function() {
            // Opcional: Bloquear el switch mientras procesa para evitar doble clic
            $input.prop('disabled', true);
        },
        success: function(response) {
            // ACTUALIZACIÓN DEL TOKEN: Vital para que el siguiente clic no de error 403
            if (response.token) {
                $('meta[name="csrf-token-value"]').attr('content', response.token);
            }

            if (response.status === 'success') {
                console.log("Módulo actualizado correctamente");
            } else {
                alert('Error: ' + (response.message || 'No se pudo actualizar'));
                // Revertir estado visual si el servidor reporta error
                $input.prop('checked', !($input.is(':checked')));
            }
        },
        error: function(xhr) {
            console.error("Error del servidor:", xhr.responseText);
            alert('Error de comunicación con el servidor');
            // Revertir estado visual si hay error de red o 500
            $input.prop('checked', !($input.is(':checked')));
        },
        complete: function() {
            // Desbloquear el switch
            $input.prop('disabled', false);
        }
    });
});


//// valida add - Update formulario de Aperturas
document.addEventListener('DOMContentLoaded', function () {
    const formulario = document.getElementById('miFormulario');
    const btnGuardar = document.getElementById('btnGuardar');
    const btnText = document.getElementById('btnText');
    const btnLoader = document.getElementById('btnLoader');
    const modalElement = document.getElementById('addContactModal');
    const modalBootstrap = bootstrap.Modal.getOrCreateInstance(modalElement);
    const inputProg = document.getElementById('prog');

    inputProg.addEventListener('input', function() {
        // Elimina cualquier caracter que no sea número
        this.value = this.value.replace(/\D/g, '');
    });

    ///// verifica que tipo de modal sera nuevo - update
    modalElement.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const inputProg = document.getElementById('prog'); // Asegúrate de obtenerlo aquí

        if (button.hasAttribute('data-id')) {
            const prog = button.getAttribute('data-prog');
            
            // Cargamos los datos
            document.getElementById('id_apertura').value = button.getAttribute('data-id');
            document.getElementById('detalle').value = button.getAttribute('data-desc');
            inputProg.value = prog;

            // IMPORTANTE: Guardamos el original para la comparación posterior
            inputProg.setAttribute('data-original', prog);
            
            document.getElementById('addContactModalTitle').innerText = 'Editar Apertura';
        } else {
            // Si es nuevo, limpiamos el atributo para que no tenga basura de una edición previa
            formulario.reset();
            inputProg.removeAttribute('data-original');
            document.getElementById('id_apertura').value = '';
            document.getElementById('addContactModalTitle').innerText = 'Nueva Apertura';
        }
    });
    //////////////

    formulario.addEventListener('submit', function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (!formulario.checkValidity()) {
            formulario.classList.add('was-validated');
            return;
        }

        const inputProg = document.getElementById('prog');
        const programaNuevo = inputProg.value.trim();
        const programaOriginal = inputProg.getAttribute('data-original'); // Recuperamos el original

      //  alert(programaNuevo+'----'+programaOriginal)
        if (programaOriginal === null || programaNuevo !== programaOriginal) {
            //const programaNuevo = document.getElementById('prog').value.trim();
            const filas = document.querySelectorAll('#tabla-cuerpo tr'); // Selecciona las filas de la tabla
            let yaExiste = false;

            filas.forEach(fila => {
            // Obtenemos el texto de la segunda columna (CÓDIGO)
            // Usamos split(' ')[0] para extraer solo el código del programa (antes del espacio)
            const codigoCelda = fila.cells[1].innerText.trim().split(' ')[0];
            
            if (codigoCelda === programaNuevo) {
                yaExiste = true;
            }
            });

            if (yaExiste) {
                alert('⚠️ El código de programa "' + programaNuevo + '" ya se encuentra registrado en el listado.');
                document.getElementById('prog').focus();
                return; // Detiene el envío
            }
        }
        

        // --- ACTIVAR LOADING ---
        btnGuardar.disabled = true;        // Evita múltiples clics
        btnText.innerText = "Guardando..."; // Cambia el texto
        btnLoader.classList.remove('d-none'); // Muestra el spinner
        const formData = new FormData(formulario);

        fetch(base + "mnt/aperturas", { 
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // CAMBIO CLAVE: Validamos 'data.success' que es lo que envía tu PHP
            if (data.success === true) {
               // alert(data.message);
                
                // 1. Obtener la referencia de la tabla
                const tbody = document.getElementById('tabla-cuerpo');
                
                // 2. Calcular el siguiente número (opcional, basado en filas actuales)
                const nro = tbody.rows.length + 1;
                
                // 3. Capturar los valores actuales del formulario para la fila
                const programa = document.getElementById('prog').value;
                const detalle = document.getElementById('detalle').value.toUpperCase();
                
                // 4. Crear el HTML de la nueva fila (idéntico al de tu PHP)
                const nuevaFila = `
                  <tr class="search-items">
                      <td>${nro}</td>
                      <td>${programa} 0000 000</td>
                      <td>${detalle}</td>
                      <td class="text-center">
                          <div class="action-btn">
                              <a href="javascript:void(0)" class="btn btn-outline-primary btn-sm d-flex align-items-center edit shadow-sm">
                                  <i class="ti ti-eye me-1 fs-5"></i> Ver
                              </a>
                          </div>
                      </td>
                      <td class="text-center">
                          <div class="action-btn">
                              <a href="javascript:void(0)" class="btn btn-outline-danger btn-sm d-flex align-items-center delete shadow-sm">
                                  <i class="ti ti-trash me-1 fs-5"></i> Borrar
                              </a>
                          </div>
                      </td>
                  </tr>`;

                // 5. Insertar la fila al final de la tabla
                tbody.insertAdjacentHTML('beforeend', nuevaFila);

                
                formulario.reset();
                formulario.classList.remove('was-validated');
                modalBootstrap.hide();
                alert(data.message);

            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error en la petición:', error);
            alert('Hubo un problema al conectar con el servidor.');
        })
         .finally(() => {
            // ESTO REACTIVA EL BOTÓN SIEMPRE
            btnGuardar.disabled = false;
            btnText.innerText = "Guardar";
            btnLoader.classList.add('d-none');
        });;

    }, false);
});

//// Eliminar Apertura Programatica
 document.getElementById('tabla-cuerpo').addEventListener('click', function(e) {
    // Buscamos si el clic fue en el botón de borrar o en su icono
    const btnDelete = e.target.closest('.btn-delete');
    
    if (btnDelete) {
        const id = btnDelete.getAttribute('data-id');
        const fila = btnDelete.closest('tr'); // La fila que vamos a eliminar

        if (confirm('¿Estás seguro de eliminar este registro?')) {
            
            // Enviamos la petición al controlador
            fetch(base + "mnt/eliminar_apertura", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `id=${id}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Animación simple de salida y eliminar fila del DOM
                    fila.style.transition = "0.3s";
                    fila.style.opacity = "0";
                    setTimeout(() => fila.remove(), 300);
                    
                    alert(data.message);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('No se pudo conectar con el servidor.');
            });
        }
    }
});