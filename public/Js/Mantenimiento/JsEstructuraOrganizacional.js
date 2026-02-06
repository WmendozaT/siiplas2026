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