<?php

namespace App\Controllers\CDashboard;
use App\Controllers\BaseController; 
// Importa las clases necesarias para initController
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Dashboard extends BaseController{
    protected $dat_regional;
    protected $dat_conf; 
    protected $name; 
    
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger){
        // LLAMADA OBLIGATORIA al initController del padre (DESCOMENTADA)
        parent::initController($request, $response, $logger); 

        // TODO el código de inicialización y acceso a sesión va AQUÍ dentro
        if (empty($this->session)) {
            $this->session = \Config\Services::session();
        }
        
        $this->dat_regional = $this->session->get('regional'); 
        $this->dat_conf = $this->session->get('configuracion'); 
        $this->name = $this->session->get('user_name'); 
    }


    /// Dasboard
    public function dashboard_admin(){

        return view('dashboard/viewdashboard_poa');
       // echo "Hola mundo mundo".$this->gestion.'--'.$this->fun_id.' ->'.$this->dat_regional['dist_distrital'];
       // $distrital = $this->dat_conf['conf_gestion'] ?? 'No definido';
     //   echo "<br>".$this->name.'<br>';
    /*    echo '<a href="'.base_url().'logout" class="boton-login">
                    Ir a Inicio de Sesión
                </a>';*/
    }
}


