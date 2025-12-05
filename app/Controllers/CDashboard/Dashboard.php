<?php

namespace App\Controllers;

class Dashboard extends BaseController
{

    public function index(): string
    {
        // 1. Opcional: Preparar datos para pasar a la vista
        $data = [
            'titulo' => 'Página Principal del Dashboard',
            'mensaje' => '¡Bienvenido al sistema siiplas2026!'
        ];

        // 2. Cargar la vista 'dashboard_view.php' y pasarle los datos
        return view('dashboard/index', $data);
    }
}


