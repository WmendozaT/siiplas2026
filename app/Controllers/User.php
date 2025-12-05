<?php

namespace App\Controllers;

class User extends BaseController
{
    /*public function index(): string
    {
        return view('index');
    }*/

/*     public function index()
    {
        echo "Hola mundo"; 
    }*/


    public function index(){
        //echo route_to('admin_proy_listado');
        //echo route_to('admin/proy/list_proy');
        //echo base_url().'assets/img/registro1.png';
        // 1. Opcional: Preparar datos para pasar a la vista
        $data = [
            'titulo' => 'Página Principal del Dashboard',
            'mensaje' => '¡Bienvenido al sistema siiplas2026!',
            'url_boton' => base_url('admin/proy/list_proy'),
            'texto_boton' => 'Listado de Proyectos'
        ];

        // 2. Cargar la vista 'dashboard_view.php' y pasarle los datos
        return view('dashboard/index', $data);
    }


    public function listProy(){
        echo "Hola mundo nuevo";
    }
   
}
