<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $titulo ?></title> <!-- Usamos la variable $titulo de PHP -->
    <style>
        body { font-family: sans-serif; background-color: #f4f4f4; margin: 40px; }
        .container { background-color: white; padding: 20px; border-radius: 8px; }
        h1 { color: #007bff; }
    </style>
</head>
<body>
    <div class="container">
        <h1><?= $titulo ?></h1>
        <p><?= $mensaje ?></p> <!-- Usamos la variable $mensaje de PHP -->
        <p>Esta vista se cargó correctamente desde el controlador CDashboard/Dashboard.</p><br>
        <a href="<?= $url_boton ?>" class="btn btn-primary"><?= $texto_boton ?></a>
    </div>
</body>
</html>