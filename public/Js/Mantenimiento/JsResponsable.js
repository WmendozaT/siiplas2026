 base = $('[name="base"]').val();


$(document).ready(function() {
    $("#tp_adm").change(function () {
        // Obtener el valor seleccionado directamente
        var tp_adm = $(this).val(); 
        id = $('[name="id"]').val();
        // Validar que el valor no esté vacío (opcional, si tienes un "Seleccione...")
        if (tp_adm === "") return;
        var url = base + "mnt/get_reg_nal";
        var request = $.ajax({
            url: url,
            type: "POST",
            dataType: 'json',
            data: { 
                tipo_adm: tp_adm, // Se envía como $_POST['tipo_adm']
                id: id
            }
        });

        request.done(function (response) {
            if (response.respuesta == 'correcto') {
                $("#select_reg").html(response.select_reg);
                $("#select_dist").html(response.select_dist);
            }
        });

        request.fail(function (jqXHR, textStatus, errorThrown) {
            console.error("Error en la petición: " + textStatus, errorThrown);
        });
    }); 
  })








/*    $(function(){
        $('#radio0').click(function(){
          $('[name="tp"]').val(0);
        });

        $('#radio1').click(function(){
          $('[name="tp"]').val(1);
        });
    })
*/



/*
    /// Valida form
    $(document).ready(function() {
        $('#form').on('submit', function(event) {
            event.preventDefault(); // Evitar el envío del formulario

            // Mostrar el loading
            $('#loading').show();

            // Validación de datos
            let valid = true;

            // Validar usuario
            const userName = $('input[name="user_name"]').val();
            if (userName.trim() === '') {
                $('#usu').css('visibility', 'visible');
                valid = false;
            } else {
                $('#usu').css('visibility', 'hidden');
            }

            // Validar contraseña
            const password = $('#password').val();
            if (password.trim() === '') {
                $('#pass').css('visibility', 'visible');
                valid = false;
            } else {
                $('#pass').css('visibility', 'hidden');
            }

            // Validar captcha
            const captcha = $('#dat_captcha').val();
            if (captcha.trim() === '') {
                $('#cat').css('visibility', 'visible');
                valid = false;
            } else {
                $('#cat').css('visibility', 'hidden');
            }

            if (valid) {
                // Simulación de envío de datos
                this.submit(); // Enviar el formulario si es válido
            } else {
                $('#loading').hide(); // Ocultar loading si hay un error
            }
        });
    });



    ///// Valida formulario de Password 
    $(document).ready(function() {
        $('#formpws').on('submit', function(event) {
            event.preventDefault(); // Evitar el envío del formulario

            // Mostrar el loading
            $('#loadingpws').show();

            // Validación de datos
            let valid = true;
            const alphanumericRegex = /^[A-Za-z0-9.]+$/; // Regex para letras y números
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Expresión regular para validación

            // Validar usuario
            const userName = $('input[name="user_namepws"]').val().trim();
            if (!userName) {
                $('#usupsw').text('Campo obligatorio').css('visibility', 'visible');
                valid = false;
            } else if (!alphanumericRegex.test(userName)) {
                $('#usupsw').text('Solo letras y números permitidos').css('visibility', 'visible');
                valid = false;
            } else {
                $('#usupsw').css('visibility', 'hidden');
            }

            // Validar contraseña
              const email = $('#emailpws').val().trim();
                if (!email) {
                    $('#email').text('Email requerido').css('visibility', 'visible');
                    valid = false;
                } else if (!emailRegex.test(email)) {
                    $('#email').text('Formato inválido (ej: usuario@dominio.com)').css('visibility', 'visible');
                    valid = false;
                } else {
                    $('#email').css('visibility', 'hidden');
                }

            if (valid) {
               
                 // MOSTRAR EL MODAL PERSONALIZADO EN LUGAR DE confirm()
                $('#loadingpws').show(); // Muestra el loading mientras el usuario decide
                $('#customConfirmModal').fadeIn(200); // Muestra el modal suavemente
                
                // Capturar la referencia al formulario actual para usarla en los manejadores de clic
                const currentForm = this;

                // Manejar clic en "Sí, enviar"
                $('#confirmYes').one('click', function() {
                    $('#customConfirmModal').fadeOut(200); // Oculta el modal
                    // Envía el formulario real
                    currentForm.submit(); 
                });

                // Manejar clic en "Cancelar"
                $('#confirmNo').one('click', function() {
                    $('#customConfirmModal').fadeOut(200); // Oculta el modal
                    $('#loadingpws').hide(); // Oculta el loading
                    // Detiene el proceso (ya que preventDefault() ya se llamó)
                });

            } else {
                $('#loadingpws').hide(); // Ocultar loading si hay un error
            }
        });
    });



        ///// Get Captcha
        $(document).ready(function(e) {
            var request;
          $('#refreshs').click(function(){
              var url = base + "User/get_captcha"; 
             // alert(url)
 
              if (request) {
                  request.abort();
              }
              request = $.ajax({
                url: url,
                type: "POST",
                dataType: 'json', 
              });

              request.done(function (response, textStatus, jqXHR) {
                if (response.respuesta == 'correcto') {
                   // alert('hola mundo')
                  $("#refreshs").html(response.cod_captcha);
                  document.getElementById("captcha").value = response.captcha;
                }
              }); 
          });
        });


        $("#sub").on("click", function (e) {
          document.getElementById("but").style.display = 'none';
          document.getElementById("but2").style.display = 'none';
          document.getElementById("load").style.display = 'block';
        });


///---------------------------

    function togglePassword(fieldId) {
        const passwordInput = document.getElementById(fieldId);
        let toggleIconId;

         // Determinar el ID del icono basado en el campo
        switch(fieldId) {
            case 'password':
                toggleIconId = 'toggleIcon';
                break;
        }
        
        const toggleIcon = document.getElementById(toggleIconId);
        if (passwordInput && toggleIcon) {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                //toggleIcon.textContent = '🙈';
            } else {
                passwordInput.type = 'password';
                //toggleIcon.textContent = '👁️';
            }
        }
    }*/