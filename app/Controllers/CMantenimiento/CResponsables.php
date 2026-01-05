<?php
namespace App\Controllers\CMantenimiento;
use App\Controllers\BaseController; 
// Importa las clases necesarias para initController
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\Model_Mantenimiento\Model_funcionarios;

class CResponsables extends BaseController{
    protected $Model_funcionarios;

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


    /// Vista Reponsables POA
    public function lista_responsables(){
        $model_funcionario = new Model_funcionarios();
        $data['formulario']=$this->responsables_poa(); /// lista de responsables
        return view('View_mantenimiento/View_responsables/view_funcionarios',$data);
    }

    /// Vista Update Responsable POA
    public function update_responsable($id){
        $model_funcionario = new Model_funcionarios();
        $get_rep=$model_funcionario->get_responsablePoa($id);
        if (empty($get_rep)) {
            $data['formulario']='SIN REGISTRO POR MOSTRAR !!!';
        }
        else{
            $data['formulario']=$this->get_responsables_poa($get_rep);
        }

        return view('View_mantenimiento/View_responsables/view_funcionarios',$data);
    }


    /// Formulario 
    public function get_responsables_poa($get_rep){
        $tabla='';
        $tabla.='
        <div class="col-12">
                      <div class="card w-100 border position-relative overflow-hidden mb-0">
                        <div class="card-body p-4">
                          <h4 class="card-title">Datos del Responsable POA</h4>
                          <p class="card-subtitle mb-4">Formulario para cambiar editar Información del Reponsable POA</p>
                          <form>
                            <div class="row">
                              <div class="col-lg-6">
                                <div class="mb-3">
                                  <label for="exampleInputtext" class="form-label">NOMBRE</label>
                                  <input type="text" class="form-control" id="fn_nom" name="fn_nom" placeholder="'.$get_rep[0]['fun_nombre'].'" value="'.$get_rep[0]['fun_nombre'].'">
                                </div>
                                <div class="mb-3">
                                  <label for="exampleInputtext" class="form-label">APELLIDO PATERNO</label>
                                  <input type="text" class="form-control" id="fn_pt" name="fn_pt" placeholder="'.$get_rep[0]['fun_paterno'].'" value="'.$get_rep[0]['fun_paterno'].'">
                                </div>
                                <div class="mb-3">
                                  <label for="exampleInputtext" class="form-label">APELLIDO PATERNO</label>
                                  <input type="text" class="form-control" id="fn_mt" name="fn_mt" placeholder="'.$get_rep[0]['fun_materno'].'" value="'.$get_rep[0]['fun_materno'].'">
                                </div>
                                <div class="mb-3">
                                  <label for="exampleInputtext" class="form-label">CI</label>
                                  <input type="number" class="form-control" id="fn_ci" name="fn_ci" placeholder="'.$get_rep[0]['fun_ci'].'" value="'.$get_rep[0]['fun_ci'].'">
                                </div>
                                <div class="mb-3">
                                  <label for="exampleInputtext" class="form-label">NRO DE CELULAR</label>
                                  <input type="number" class="form-control" id="fn_fono" name="fn_fono" placeholder="'.$get_rep[0]['fun_telefono'].'" value="'.$get_rep[0]['fun_telefono'].'">
                                </div>
                                <div class="mb-3">
                                  <label class="form-label">Location</label>
                                  <select class="form-select" aria-label="Default select example">
                                    <option selected>United Kingdom</option>
                                    <option value="1">United States</option>
                                    <option value="2">United Kingdom</option>
                                    <option value="3">India</option>
                                    <option value="3">Russia</option>
                                  </select>
                                </div>
                              </div>
                              <div class="col-lg-6">
                                <div class="mb-3">
                                  <label for="exampleInputtext2" class="form-label">USUARIO</label>
                                  <input type="text" class="form-control" id="exampleInputtext2" placeholder="Maxima Studio">
                                </div>
                                <div class="mb-3">
                                  <label class="form-label">Currency</label>
                                  <select class="form-select" aria-label="Default select example">
                                    <option selected>India (INR)</option>
                                    <option value="1">US Dollar ($)</option>
                                    <option value="2">United Kingdom (Pound)</option>
                                    <option value="3">India (INR)</option>
                                    <option value="3">Russia (Ruble)</option>
                                  </select>
                                </div>
                                <div class="mb-3">
                                  <label for="exampleInputtext3" class="form-label">Phone</label>
                                  <input type="text" class="form-control" id="exampleInputtext3" placeholder="+91 12345 65478">
                                </div>
                              </div>
                              <div class="col-12">
                                <div>
                                  <label for="exampleInputtext4" class="form-label">Address</label>
                                  <input type="text" class="form-control" id="exampleInputtext4" placeholder="814 Howard Street, 120065, India">
                                </div>
                              </div>
                              <div class="col-12">
                                <div class="d-flex align-items-center justify-content-end mt-4 gap-6">
                                  <button class="btn btn-primary">Save</button>
                                  <button class="btn bg-danger-subtle text-danger">Cancel</button>
                                </div>
                              </div>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>';
        return $tabla;
    }





    ///Lista Reponsables POA
    public function responsables_poa(){
        $model_funcionario = new Model_funcionarios();
        $responsables=$model_funcionario->obtenerFuncionariosActivos();
        $tabla='';
        $tabla.='
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4 pb-2">RESPONSABLES POA</h4>
                <div class="table-responsive pb-4">
                    <table id="all-student" class="table table-striped table-bordered border text-nowrap align-middle" style="font-size:10.5px;">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>REPONSABLE POA</th>
                          <th>UNIDAD DEPENDIENTE</th>
                          <th>USUARIO</th>
                          <th>ADMINISTRACIÓN</th>
                          <th>DISTRITAL</th>
                          <th></th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>';
                      $nro=0;
                      foreach($responsables as $row){ 
                        $nro++;
                        $tabla.='
                        <tr>
                          <td style="aling:center;">'.$nro.'</td>
                          <td>'.$row['fun_nombre'].' '.$row['fun_paterno'].' '.$row['fun_materno'].'</td>
                          <td>'.$row['uni_unidad'].'</td>
                          <td>'.$row['fun_usuario'].'</td>
                          <td>'.$row['adm'].'</td>
                          <td>'.$row['dist_distrital'].'</td>
                          <td>
                            <a href="'.base_url().'mnt/update_responsable/'.$row['id'].'" class="link-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Ver Detalles ">
                              <i class="ti ti-eye fs-3">Ver</i>
                            </a>
                          </td>
                          <td>
                            <a href="teacher-details.html" class="link-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar">
                              <i class="ti ti-eye fs-3">Eliminar</i>
                            </a>
                          </td>
                        </tr>';
                      }
                      $tabla.='
                      </tbody>
                    </table>
                </div>
            </div>
        </div>';

        return $tabla;
    }
}


