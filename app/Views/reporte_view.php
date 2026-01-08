<!DOCTYPE html>
<html>
<head>
    <title>Hola</title>
    <style>
        body { font-family: sans-serif; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>Hola</h1>
    <p>Generado el: <?= date('d/m/Y') ?></p>
    <table>
        <tr><th>ID</th><th>Descripción</th></tr>
        <tr><td>001</td><td>Ejemplo de reporte en CI4</td></tr>
    </table>
</body>
</html>