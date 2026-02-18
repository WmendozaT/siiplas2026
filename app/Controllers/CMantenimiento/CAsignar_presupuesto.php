<?php
namespace App\Controllers\CMantenimiento;
use App\Controllers\BaseController; 
// Importa las clases necesarias para initController
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\Index\IndexModel;
use App\Models\Model_Mantenimiento\Model_funcionarios;
use App\Models\Model_Mantenimiento\Model_regional;
use App\Models\Model_Mantenimiento\Model_configuracion;
use App\Libraries\Libreria_EstructuraOrganizacional;
use App\Libraries\Libreria_Index;

use Dompdf\Dompdf;
use Dompdf\Options;


class Casignar_presupuesto extends BaseController{
    protected $IndexModel;
    protected $Model_funcionarios;
    protected $Model_regional;
    protected $Model_configuracion;

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
        $this->session->get('funcionario');
        $this->session->get('view_modulos'); 
        $this->session->get('view_modulos_sidebar'); 
    }

    /// menu Estructura Organizacional
    public function menu_lista_poa(){
        $session = session(); // Necesario para romper la sesión
        $miLib_conf = new Libreria_EstructuraOrganizacional();
        $data['formulario']=$miLib_conf->Lista_poa_para_asignacion_presupuesto();

        return view('View_mantenimiento/View_pptoAsignado/view_asignacion_presupuestaria',$data);
    }


    //// Valida migracion del archivo excel
    public function valida_migracion_ppto() {
        // 1. Validar si llegó el archivo
      $file = $this->request->getFile('archivo_excel');

      if (!$file->isValid()) {
          return redirect()->back()->with('error', 'Archivo no seleccionado o corrupto.');
      }


      return $this->response->setJSON([
        'status' => 'success',
        'message' => 'Los datos se migraron correctamente.'
      ]);


    }

}


