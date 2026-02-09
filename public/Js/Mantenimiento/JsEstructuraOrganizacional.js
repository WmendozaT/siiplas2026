 base = $('[name="base"]').val();
// 1. Buscador por columnas
$(document).on('keyup', '.column-search', function() {
    var colIndex = $(this).data('column');
    var value = $(this).val().toLowerCase();
    
    $("#table_ue tbody tr").each(function() {
        var cellText = $(this).find('td').eq(colIndex).text().toLowerCase();
        // Si el valor no coincide, marcamos la fila con una clase para ocultarla
        $(this).toggle(cellText.indexOf(value) > -1);
    });
});




    /// Get obtiene lista de unidades organizacionales por al Seleccionar la Regional
    $(document).on('change', '#dep_id', function() {
    const dep_id = $(this).val();
    const $contenedor = $('#listado');

    // Si selecciona la opción por defecto, limpiamos el listado
    if (dep_id == "0") {
        $contenedor.html('');
        return;
    }

    // 1. Efecto de carga (Spinner)
    $contenedor.html(`
        <div class="d-flex justify-content-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
    `);

        // 2. Preparar CSRF
        const csrfName = $('meta[name="csrf-token-name"]').attr('content');
        const csrfHash = $('meta[name="csrf-token-value"]').attr('content');

        const dataPost = { dep_id: dep_id };
        dataPost[csrfName] = csrfHash;

        // 3. Petición AJAX
        $.ajax({
            url: base + "mnt/obtener_uorganizacionales", // Ajusta a tu ruta real
            type: 'POST',
            data: dataPost,
            dataType: 'json',
            success: function(res) {
                // Actualizar Token CSRF
                if (res.token) {
                    $('meta[name="csrf-token-value"]').attr('content', res.token);
                }

                if (res.status === 'success') {
                    $contenedor.hide().html(res.datos).fadeIn();
                } else {
                    $contenedor.html('<div class="alert alert-warning m-3">' + res.message + '</div>');
                }
            },
            error: function() {
                $contenedor.html('<div class="alert alert-danger m-3">Error al conectar con el servidor</div>');
            }
        });
    });





    /// UPDATE los valores de la unidad organizacional
   $(document).on('change', '.check-gestion', function() {
    const $switch = $(this);
    const $fila = $switch.closest('tr');
    const $inputs = $fila.find('.input-field, .save-change'); // Asegura capturar todos los campos
    const isChecked = $switch.is(':checked');
    const act_id = $switch.data('id');

    // Bloqueo inmediato para evitar doble clic
    $switch.prop('disabled', true);

    const datos = {
        id: act_id,
        act_cod: $fila.find('[data-field="act_cod"]').val(),
        te_id: $fila.find('[data-field="te_id"]').val(),
        act_descripcion: $fila.find('[data-field="act_descripcion"]').val(),
        incluido: isChecked ? 1 : 0,
        // Usamos el nombre del token dinámico
        "<?= csrf_token() ?>": "<?= csrf_hash() ?>" 
    };

    $.ajax({
        url: base + "mnt/update_uorganizacional",
        type: 'POST',
        data: datos,
        dataType: 'json', // Esperamos JSON del controlador
        success: function(response) {
            // --- MEJORA CLAVE: Actualizar el hash CSRF para la siguiente petición ---
            if(response.token) {
                $("input[name='<?= csrf_token() ?>']").val(response.token);
                // Si tienes el token en un meta tag o variable global, actualízalo también
            }

            if (isChecked) {
                $inputs.prop('disabled', true);
                $fila.addClass('bg-light'); // Estilo Spike Admin para fila bloqueada
            } else {
                $inputs.prop('disabled', false);
                $fila.removeClass('bg-light');
            }
            
            // Reemplaza el alert por un console o un toast sutil
            alert(response.message)
            console.log(response.message);
        },
        error: function(xhr) {
            alert('Error crítico: No se pudo sincronizar con el servidor.');
            $switch.prop('checked', !isChecked); // Revertir el switch
        },
        complete: function() {
            $switch.prop('disabled', false); // Liberar el switch
        }
    });
});



//// Formulario para nuevo registro de Unidad Organizacional
 $(document).on('submit', '#form_add_uo', function(e) {
    e.preventDefault();
    if (validarFormularioUO()) {
        enviarFormularioUO($(this));
    }
});

function validarFormularioUO() {
    let esValido = true;
    const $form = $('#form_add_uo');
    $form.find('.is-invalid').removeClass('is-invalid');

    // 1. Validar AMBOS Selects (Distrital y Tipo)
    const selects = ['#dist_id', '#te_id']; 
    selects.forEach(id => {
        const val = $(id).val();
        if (val === "0" || val === "" || val === null) {
            $(id).addClass('is-invalid');
            esValido = false;
        }
    });

    // 2. Validar Código (1 a 3 dígitos)
    const $cod = $('input[name="cod_unidad"]');
    const valCod = $cod.val().trim();
    if (valCod === "" || valCod < 0 || valCod > 999) {
        $cod.addClass('is-invalid');
        esValido = false;
    }

    // 3. Validar Nombre
    const $nombre = $('input[name="nombre_unidad"]');
    if ($nombre.val().trim().length < 3) {
        $nombre.addClass('is-invalid');
        esValido = false;
    }

    if (!esValido) $form.find('.is-invalid').first().focus();
    return esValido;
}

function enviarFormularioUO($form) {
    const $btn = $('#btnGuardarUO');
    const $loader = $('#btnLoader');
    const $text = $('#btnText');

    $btn.prop('disabled', true);
    $loader.removeClass('d-none');
    $text.text('Guardando...');

    $.ajax({
        url: base + "mnt/add_uorganizacional",
        type: 'POST',
        data: $form.serialize(),
        dataType: 'JSON',
        success: function(res) {
            // ACTUALIZAR TOKEN CSRF (Para que el siguiente envío funcione)
            if (res.token) {
                $('input[name^="csrf_"]').val(res.token); // Actualiza inputs ocultos
                $('meta[name="csrf-token-value"]').attr('content', res.token); // Actualiza meta
            }

            if (res.status === 'success') {
                const $tbody = $('#table_ue tbody');
                
                // Captura de datos para la fila visual
                const nro = $tbody.find('tr').length + 1;
                const distritalText = $("#dist_id option:selected").text().toUpperCase();
                const teId = $("#te_id").val();
                const teText = $("#te_id option:selected").text();
                const cod = $('input[name="cod_unidad"]').val();
                const nombre = $('input[name="nombre_unidad"]').val().toUpperCase();

                // Construcción de la fila idéntica a tu PHP
                const nuevaFila = `
                    <tr data-id="${res.act_id}" class="table-success animate__animated animate__fadeIn">
                        <td class="ps-0"><span class="text-dark fw-semibold">${nro}</span></td>
                        <td>
                            <span class="badge bg-light-primary text-primary fw-semibold fs-2 px-2 py-1 rounded">
                                ${distritalText}
                            </span>
                        </td>
                        <td>
                            <input type="number" value="${cod}" 
                                   class="form-control form-control-sm fw-bold text-center input-field"
                                   min="0" max="999">
                        </td>
                        <td>
                            <select class="form-select form-select-sm input-field">
                                <option value="${teId}" selected>${teText}</option>
                            </select>
                        </td>
                        <td>
                            <textarea class="form-control form-control-sm input-field" rows="2">${nombre}</textarea>
                        </td>
                        <td class="text-end">
                            <div class="form-check form-switch d-flex justify-content-end">
                                <input class="form-check-input check-gestion" type="checkbox" 
                                       style="cursor:pointer; width: 2.5em; height: 1.25em;">
                            </div>
                        </td>
                    </tr>`;

                $tbody.append(nuevaFila);

                // Quitar el color verde de éxito después de 3 segundos
                setTimeout(() => {
                    $(`tr[data-id="${res.act_id}"]`).removeClass('table-success');
                }, 3000);

                $('#modalNuevoRegistro').modal('hide');
                $form[0].reset(); // Reset correcto del DOM
                $form.removeClass('was-validated');
                
                alert("¡Unidad guardada exitosamente!");
            } else {
                alert('Error: ' + res.msg);
            }
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            alert('Error en el servidor. Revise la consola para más detalles.');
        },
        complete: function() {
            $btn.prop('disabled', false);
            $loader.addClass('d-none');
            $text.text('Guardar');
        }
    });
}


//// Generar Reporte en Base64 - Lista de Unidades Organizacionales
function generarReporteBase64_uorganizacionales(dep_id) {
    // 1. Referencia al botón y su contenido original
    var $btn = $("#btnGenerarReporte");
    var originalHtml = $btn.html();
    // 2. Mostrar Loading: Desactivar botón y cambiar icono
    $btn.prop("disabled", true);
    $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generando...');

    $.ajax({
        url: base + "mnt/rep_uorganizacional",
        type: 'POST',
        dataType: 'json',
        data: { dep_id: dep_id },
        success: function(response) {

            if (response.status === 'success') {
                // Abrir en pestaña nueva
                var win = window.open();
                if (win) {
                    win.document.write('<title>Unidad Organizacional Disponibles</title>');
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