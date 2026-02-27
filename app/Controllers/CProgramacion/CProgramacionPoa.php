<?php
namespace App\Controllers\CProgramacion;
use App\Controllers\BaseController; 
// Importa las clases necesarias para initController
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\Index\IndexModel;
use App\Models\Model_Mantenimiento\Model_funcionarios;
use App\Models\Model_Mantenimiento\Model_regional;
use App\Models\Model_Mantenimiento\Model_configuracion;
use App\Models\Model_Poa\Model_formulario5;
use App\Models\Model_Ppto\Model_PptoAsig;
use App\Libraries\Libreria_ProgramacionPoa;
use App\Libraries\Libreria_ReportesPoa;
//use App\Libraries\Libreria_Responsable;
//use App\Libraries\Libreria_EstructuraOrganizacional;
use App\Libraries\Libreria_Index;

use Dompdf\Dompdf;
use Dompdf\Options;


use PhpOffice\PhpSpreadsheet\IOFactory;


class CProgramacionPoa extends BaseController{
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

    /// menu AnteProyectoPOA
    public function lista_poa_anteproyecto(){
        $session = session(); // Necesario para romper la sesión
        $miLib_ProgPoa = new Libreria_ProgramacionPoa();
        $data['formulario']=$miLib_ProgPoa->Lista_ProgramacionPoa();

        return view('View_programacion/view_programacion_poa',$data);
    }

    /// Obtiene el listado de las unidades Organizacionales de la regional Seleccionado
    public function obtener_unidades_organizacionales_disponibles_x_regional() {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $dep_id = $this->request->getPost('id');
        $model_regional = new Model_regional();
        $unidades_disponibles=$model_regional->lista_unidades_disponibles_addpoa($dep_id,$this->session->get('configuracion')['ide']);
        $html = 'Trabajando en la adicion de Unidades Organizacionales al POA ';

        
        return $this->response->setJSON([
            'status' => 'success',
            'datos'  => $html,
            'token'  => csrf_hash()
        ]);
    }

    
    //// Lista de Unidades aperturados en el POA por Regional
    public function pdf_lista_poa_uorganizacional_regional($dep_id) {
    try {
        // 1. VALIDACIÓN PREVIA (Evita que el JS reciba basura o errores 500 silenciosos)
        if ($dep_id === null || $dep_id === '') {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'ID Regional no proporcionado']);
        }

        $miLib_Pdf_repPoa = new Libreria_ReportesPoa();
        $model_regional = new Model_regional();
        $regional=$model_regional->get_regional($dep_id);
        $unidades_disponibles_x_regional=$model_regional->lista_programacion_poa_x_regional($dep_id);

        // 2. CONFIGURACIÓN DOMPDF
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'sans-serif');
        $dompdf = new \Dompdf\Dompdf($options);
        
        $html = $miLib_Pdf_repPoa->Pdf1_Lista_unidades_disponibles_poa_x_regional($unidades_disponibles_x_regional,$regional); /// rep pdf1

        $dompdf->loadHtml($html);
        $dompdf->setPaper('Letter', 'portrait');
        $dompdf->render();

        // 3. RESPUESTA COMPATIBLE CON EL IFRAME Y FETCH
        $pdf_content = $dompdf->output();

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="reporte_regional_'.$dep_id.'.pdf"')
            ->setHeader('Cache-Control', 'no-cache, must-revalidate')
            ->setBody($pdf_content);

        } catch (\Exception $e) {
            // Si algo falla, enviamos un 500 que el 'catch' de tu JS capturará
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
       
}


