<?php

namespace App\Controllers;

class Home extends BaseController
{
    /*public function index(): string
    {
        return view('index');
    }*/

/*     public function index()
    {
        echo "Hola mundo"; 
    }*/


    public function index()
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
