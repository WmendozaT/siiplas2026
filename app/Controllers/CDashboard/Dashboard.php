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

        // 1. Inicializar sesión si no existe
        $this->session = \Config\Services::session();

        // 2. Control de sesión sencilla: Si no existe 'user_name', redirigir
        if (!$this->session->has('fun_id')) {
            // Esta es la forma limpia en CI4 de forzar una redirección desde initController
            response()->redirect(base_url('login'))->send();
            exit; 
        }
        
        $this->session->get('regional'); 
        $this->session->get('configuracion'); 
        $this->session->get('user_name');
        $this->session->get('view_modulos'); 
        $this->session->get('view_modulos_sidebar'); 
    }


    /// Dasboard Administrador
public function dashboard_admin(){
    // 1. Verificación forzada: Si NO existe la bandera o es falsa, destruir y mandar al login
    if (!session()->get('isLoggedIn')) {
        session()->destroy(); // Limpia cualquier residuo de sesión corrupta
        return redirect()->to(base_url('login'))->with('errors', 'Debes iniciar sesión.');
    }

    // 2. Si pasa la validación, carga el contenido
    $data['boton'] = '<a href="'.base_url().'logout" class="boton-login">Cerrar Sesión</a>';
    return view('View_dashboard/viewdashboard_poa', $data);
}
}


