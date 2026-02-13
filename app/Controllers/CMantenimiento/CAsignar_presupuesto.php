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
        $miLib_Estructura = new Libreria_EstructuraOrganizacional();
        $model_regional = new Model_regional();
        $regional=$model_regional->obtenerRegionales();

        echo "Hola mundo";


/*        $tabla='';
        $tabla.='
        <div class="row">
            <div class="card">
                <div class="card-body">
                  <h5 class="fs-4 fw-semibold mb-4">ESTRUCTURA ORGANIZACIONAL - GESTIÓN '.$this->session->get('configuracion')['ide'].'</h5>
                  <div class="mb-4 row align-items-center">
                    <label for="exampleInputSelect2" class="form-label col-sm-3 col-form-label text-end">Seleccione la Regional</label>
                    <div class="col-sm-6">
                      <select class="form-select" id="dep_id" name="dep_id" aria-label="Default select example">
                      <option value="0">Seleccione ..</option>';
                      foreach($regional as $row){
                        $tabla.='<option value='.$row['dep_id'].'>'.$row['dep_id'].'.- '.$row['dep_departamento'].'</option>';
                      }
                      $tabla.='
                      </select>
                    </div>
                  </div>
                </div>
                <div id="listado"></div>
            </div>
        </div>';
        $data['formulario']=$tabla;
        return view('View_mantenimiento/View_estructuraCns/view_estructuraOrganizacional',$data);*/
    }

}


