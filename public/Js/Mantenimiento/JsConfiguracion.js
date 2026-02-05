 base = $('[name="base"]').val();

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('input-search');
    const tableRows = document.querySelectorAll('.search-items');
    const noResultsRow = document.getElementById('no-results-row');

    searchInput.addEventListener('keyup', function () {
        const filter = this.value.toLowerCase();
        let hasVisibleRows = false;

        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(filter)) {
                row.style.setProperty('display', '', 'important');
                hasVisibleRows = true;
            } else {
                row.style.setProperty('display', 'none', 'important');
            }
        });

        // Mostramos u ocultamos el mensaje según si hay filas visibles
        noResultsRow.style.display = hasVisibleRows ? 'none' : 'table-row';
    });
});

//// para el listado de partidas
document.addEventListener('DOMContentLoaded', function () {
    const rowsPerPage = 10;
    let currentPage = 1;
    
    const tableRows = Array.from(document.querySelectorAll('#mi-tablapartida tbody .search-items'));
    const filters = document.querySelectorAll('.minimalist-filter');
    const noResultsRow = document.getElementById('no-results-row');
    
    function updateTable() {
        // 1. Obtener solo las filas que pasan el filtro
        const filteredRows = tableRows.filter(row => {
            let isVisible = true;
            filters.forEach(filter => {
                const colIndex = filter.getAttribute('data-index');
                const filterValue = filter.value.toLowerCase().trim();
                const cellValue = row.cells[colIndex].textContent.toLowerCase().trim();
                if (filterValue && !cellValue.includes(filterValue)) isVisible = false;
            });
            return isVisible;
        });

        // 2. Calcular paginación sobre los resultados filtrados
        const totalRows = filteredRows.length;
        const totalPages = Math.ceil(totalRows / rowsPerPage) || 1;
        
        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        // 3. Ocultar todas y mostrar solo las de la página actual
        tableRows.forEach(row => row.style.display = 'none');
        
        filteredRows.slice(start, end).forEach(row => {
            row.style.display = '';
        });

        // 4. Actualizar textos e interfaz
        noResultsRow.style.display = totalRows === 0 ? 'table-row' : 'none';
        document.getElementById('span-inicio').textContent = totalRows === 0 ? 0 : start + 1;
        document.getElementById('span-fin').textContent = Math.min(end, totalRows);
        document.getElementById('span-total').textContent = totalRows;
        document.getElementById('page-num').textContent = currentPage;
    }

    // Eventos de Filtros
    filters.forEach(input => {
        input.addEventListener('keyup', () => {
            currentPage = 1; // Reiniciar a pag 1 al buscar
            updateTable();
        });
    });

    // Eventos de Botones
    document.getElementById('btn-prev').addEventListener('click', () => {
        if (currentPage > 1) { currentPage--; updateTable(); }
    });

    document.getElementById('btn-next').addEventListener('click', () => {
        const totalRows = tableRows.filter(row => {
            return Array.from(filters).every(f => {
                const val = f.value.toLowerCase().trim();
                return !val || row.cells[f.getAttribute('data-index')].textContent.toLowerCase().includes(val);
            });
        }).length;
        
        if (currentPage < Math.ceil(totalRows / rowsPerPage)) { 
            currentPage++; 
            updateTable(); 
        }
    });

    // Inicializar
    updateTable();
});


//// Get Unidades de Medida
$(document).on('keyup', '#search-um-internal', function() {
    let valor = $(this).val().toLowerCase();
    $("#tab_umed tbody tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(valor) > -1)
    });
});




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

 ///// GET PARTIDAS
 document.addEventListener('click', function (e) {
    // Detectamos si el click fue en el botón o dentro del code
    const btn = e.target.closest('.btn-activar');
    
    if (btn) {
        const idPartida = btn.getAttribute('data-id');
        console.log("Activando ID:", idPartida);

        // Referencia a tu div de destino
        const contenedor = document.getElementById('u_medida');
        const codigoPartida = btn.getAttribute('data-codigo');

        // Ejemplo de cómo podrías mostrar la información
        contenedor.innerHTML = `
            <div class="d-flex align-items-center justify-content-center h-100">
                <div class="spinner-border text-primary" role="status"></div>
                <span class="ms-2">Cargando datos del código ${codigoPartida}...</span>
            </div>`;

            const dataPost = {
                id: idPartida
            };

            $.ajax({
                url: base + "mnt/get_unidades_medida",
                type: 'POST',
                data: dataPost,
                dataType: 'json',
                success: function(response) {
                
                    if (response.status === 'success') {
                        contenedor.innerHTML = response.datos;
                        console.log("Módulo actualizado correctamente");
                    } else {
                        alert('Error: ' + (response.message || 'No se pudo actualizar'));
                        // Revertir estado visual si el servidor reporta error
                        $input.prop('checked', !($input.is(':checked')));
                    }
                }
            });
                
    }
});


//// activa el estado de activar la unidades de medida por la Partida
$(document).on('change', '.btn-switch-update_umedida', function() {
    const $input = $(this);
    const $container = $input.closest('.form-check'); // Para el feedback visual

    const um_id      = $input.data('um-id');      
    const par_id = $input.data('par-id'); 
    const estado   = $input.is(':checked') ? 1 : 0;

    // 3. Captura de CSRF (Importante: capturarlos justo antes del envío)
    const csrfName = $('meta[name="csrf-token-name"]').attr('content');
    const csrfHash = $('meta[name="csrf-token-value"]').attr('content');

    // 4. Preparación del objeto
    const dataPost = {
        um_id: um_id,
        par_id: par_id,
        estado: estado
    };

    // 5. Inyección dinámica del token
    dataPost[csrfName] = csrfHash;

    $.ajax({
        url: base + "mnt/update_estado_umedida",
        type: 'POST',
        data: dataPost,
        dataType: 'json',
        beforeSend: function() {
            // Opcional: Bloquear el switch mientras procesa para evitar doble clic
            $input.prop('disabled', true);
        },
        success: function(response) {
           // alert(response.status)
            // ACTUALIZACIÓN DEL TOKEN: Vital para que el siguiente clic no de error 403
            if (response.token) {
                $('meta[name="csrf-token-value"]').attr('content', response.token);
            }

            if (response.status == 'success') {
                alert(response.message);
            }
            else{
                $input.prop('checked', !dataPost.estado);
                alert(response.message);
            }
        },
        error: function() {
            $input.prop('checked', !dataPost.estado);
            alert('Error de conexión');
        },
        complete: function() {
            $input.prop('disabled', false);
            $container.removeClass('opacity-50');
        }
    });
});

//// Modal que muestra el listado de las unidades de medida alineados a la partida
    $(document).on('click', '#btn-ver-alineadas', function() {
        let listadoHtml = '<ul class="list-group list-group-flush">';
        let contador = 0;

        // Recorremos solo las filas que tienen el checkbox marcado
        $('.um-item-row').each(function() {
            const row = $(this);
            const isChecked = row.find('.btn-switch-update_umedida').is(':checked');
            
            if (isChecked) {
                const nombre = row.find('td:nth-child(2)').text().trim();
                listadoHtml += `
                    <li class="list-group-item d-flex align-items-center py-2">
                        <img src="${base}Img/Iconos/page_white_key.png" 
                                    alt="Eliminar" 
                                    style="width:16px; margin-right:5px;">
                        <span class="small fw-medium text-uppercase"> ${nombre}</span>
                    </li>`;
                contador++;
            }
        });

        listadoHtml += '</ul>';

        if (contador === 0) {
            listadoHtml = '<div class="p-4 text-center text-muted small">No hay unidades alineadas todavía.</div>';
        }

        $('#body-alineadas').html(listadoHtml);
        
        // Abrir el modal
        const modal = new bootstrap.Modal(document.getElementById('modalAlineadas'));
        modal.show();
    });