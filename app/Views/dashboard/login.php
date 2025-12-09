<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login SIIPLAS2026</title>
    <link rel="stylesheet" href="<?= base_url('Index/stylesLogin.css') ?>">
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión SIIPLAS 2026</h2>

        <!-- Mostrar mensajes de éxito/error flashdata -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert error"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <!-- Formulario de Login -->
        <form action="<?= base_url('login/auth') ?>" method="post">
            <div class="form-group">
                <label for="usu">Usuario:</label>
                <input type="usuario" name="usuario" id="usuario" value="<?= old('usuario') ?>" required>
                <!-- Mostrar errores de validación específicos -->
                <?php if (isset($errors['usuario'])): ?>
                    <p class="error-text"><?= $errors['usuario'] ?></p>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" name="password" id="password" required>
                 <?php if (isset($errors['password'])): ?>
                    <p class="error-text"><?= $errors['password'] ?></p>
                <?php endif; ?>
            </div>

            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>