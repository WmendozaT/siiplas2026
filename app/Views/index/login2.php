<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login SIIPLAS2026</title>
    <link rel="stylesheet" href="<?= base_url('Css/Index/stylesLogin.css') ?>">
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión SIIPLAS 2026</h2>

        <!-- AQUÍ VA EL MENSAJE DE ERROR ESPECÍFICO DEL LOGIN -->
        <?php if (session()->getFlashdata('error_message')): ?>
            <div class="alert error">
                <?= session()->getFlashdata('error_message') ?>
            </div>
        <?php endif; ?>

        <?= $formulario; ?>

        <!-- Formulario de Login -->
        <form action="<?= base_url('login/auth') ?>" method="post">
         
            <input name="base" type="hidden" value="<?= base_url() ?>">
            <div class="form-group">
                <label for="usu">Usuario:</label>
                <!-- Mantén 'old()' para preservar el usuario escrito si hay error -->
                <input type="usuario" name="user_name" id="user_name" value="<?= old('user_name') ?>" required>
                <!-- Mostrar errores de validación específicos -->
                <?php if (isset($errors['user_name'])): ?>
                    <p class="error-text"><?= $errors['user_name'] ?></p>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" name="password" id="password" required>
                 <?php if (isset($errors['password'])): ?>
                    <p class="error-text"><?= $errors['password'] ?></p>
                <?php endif; ?>
            </div>

            <!-- ... el resto de tu código para el captcha ... -->
            <div class="text-center py-3">
                <p class="caja" id="refreshs" style="text-align:center"><b><?= $cod_captcha ?></b></p>
                <input type="hidden" name="captcha" id="captcha"  value="<?= $captcha ?>" style="text-transform:uppercase;" oninput="this.value = this.value.toUpperCase();">
            </div>

            <div class="form-group">
                <input tabindex="4" id="dat_captcha" name="dat_captcha" type="text" class="form-control form-input-bg text-center" placeholder="Ingrese el texto de la imagen" autofocus minlength="4" maxlength="4" required>
                <div id="cat" class="text-danger text-start" style="font-size:9px; visibility: hidden;" style="font-size:8px;">
                    <b>  Este campo es requerido</b>
                </div>
            </div>

            <button type="submit">Entrar</button>
        </form>
    </div>
    <script src=<?= base_url('Js/Index/lg.js') ?>></script>
</body>
</html>