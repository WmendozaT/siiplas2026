 base = $('[name="base"]').val();


    /// Get obtiene lista de unidades organizacionales por regional
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