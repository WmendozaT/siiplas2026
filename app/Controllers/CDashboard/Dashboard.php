<?php

namespace App\Controllers\CDashboard;
use App\Controllers\BaseController; 
// Importa las clases necesarias para initController
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Dashboard extends BaseController{
    protected $gestion; 
    protected $fun_id; 
    protected $regional; 
    
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger){
        // LLAMADA OBLIGATORIA al initController del padre (DESCOMENTADA)
        parent::initController($request, $response, $logger); 

        // TODO el código de inicialización y acceso a sesión va AQUÍ dentro
        if (empty($this->session)) {
            $this->session = \Config\Services::session();
        }
        
        $this->gestion = $this->session->get('g_id');
        $this->fun_id = $this->session->get('fun_id');
        $this->dat_regional = $this->session->get('regional'); 
    }


    /// Dasboard
    public function dashboard_admin(){
        echo "Hola mundo mundo".$this->gestion.'--'.$this->fun_id.' ->'.$this->dat_regional['dist_distrital'];
        echo "<br>";
        echo '<a href="'.base_url().'logout" class="boton-login">
                    Ir a Inicio de Sesión
                </a>';
    }
}


