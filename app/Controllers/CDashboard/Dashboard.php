<?php

namespace App\Controllers\CDashboard;
use App\Controllers\BaseController; 
// Importa las clases necesarias para initController
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Dashboard extends BaseController{

    
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger){
        // LLAMADA OBLIGATORIA al initController del padre (DESCOMENTADA)
        parent::initController($request, $response, $logger); 

        // TODO el código de inicialización y acceso a sesión va AQUÍ dentro
        if (empty($this->session)) {
            $this->session = \Config\Services::session();
        }
        
        $this->session->get('regional'); 
        $this->session->get('configuracion'); 
        $this->session->get('user_name');
        $this->session->get('view_modulos'); 
        $this->session->get('view_modulos_sidebar'); 
    }


    /// Dasboard
    public function dashboard_admin(){

        $data['boton']='<a href="'.base_url().'logout" class="boton-login">
                    Ir a Inicio de Sesión
                </a>';


        return view('dashboard/viewdashboard_poa',$data);
    }
}


