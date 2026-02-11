<?php
namespace App\Controllers\CDashboard;
use App\Controllers\BaseController; 
// Importa las clases necesarias para initController
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

use Dompdf\Dompdf;
use Dompdf\Options;

class Dashboard extends BaseController{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger){
        // LLAMADA OBLIGATORIA al initController del padre (DESCOMENTADA)
        parent::initController($request, $response, $logger); 

        // 1. Inicializar sesión si no existe
        $this->session = \Config\Services::session();

        // 2. Control de sesión sencilla: Si no existe 'user_name', redirigir
       /* if (!$this->session->has('fun_id')) {
            // Esta es la forma limpia en CI4 de forzar una redirección desde initController
            response()->redirect(base_url('login'))->send();
            exit; 
        }*/
        
        $this->session->get('regional'); 
        $this->session->get('configuracion'); 
        $this->session->get('funcionario');
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
//session()->destroy();
        // 2. Si pasa la validación, carga el contenido
        $data['boton'] = 'reporte <a href="'.base_url().'reporte_prueba" class="boton-login" target=_black>REPORTE DE PRUEBA</a>';
        /*$data['boton'] = '<a href="'.base_url().'logout" class="boton-login">Cerrar Sesión</a><br>'.$this->session->get('configuracion')['conf_mensaje'].'<br>
        '.$this->session->get('funcionario')['conf_mod_form4'].'';*/
        return view('View_dashboard/viewdashboard_poa', $data);
    }

    //// Reporte Prueba
    public function reporte_prueba() {
        ini_set('memory_limit', '1024M'); // Aumenta a 1GB de RAM temporalmente
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('chroot', FCPATH);
        
        $dompdf = new \Dompdf\Dompdf($options);

        // Ruta de tu logo (ajusta la carpeta según tu proyecto)

        $path = FCPATH . $this->session->get('funcionario')['imagen_perfil']; 
        $base64 = '';

        if (file_exists($path) && !empty($this->session->get('funcionario')['imagen_perfil'])) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

            $html = '
            <html>
            <body style="font-family: sans-serif;">
                <div style="text-align:center;">
                    <img src="' . $base64 . '" width="100"><br>
                    <h1>REPORTE DE PRUEBA</h1>
                    <p>Fecha: ' . date('d/m/Y') . '</p>
                </div>
                <table border="1" style="width:100%; border-collapse: collapse;">
                    <tr><th style="background:#eee;">Estado</th></tr>
                    <tr><td style="text-align:center;">Documento generado para Firma Digital</td></tr>
                </table>
            </body>
            </html>';

            $dompdf->loadHtml($html);
            $dompdf->setPaper('Letter', 'portrait');
            $dompdf->render();

  

        // IMPORTANTE: Para un enlace <a> directo, usamos stream()
        // Esto envía el PDF al navegador inmediatamente.
        return $this->response->setHeader('Content-Type', 'application/pdf')
                              ->setBody($dompdf->output())
                              ->send();
    }
}


