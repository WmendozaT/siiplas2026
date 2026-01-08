<?php 
namespace App\Libraries;
use App\Controllers\BaseController; 
// Importa las clases necesarias para initController
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\Model_Mantenimiento\Model_funcionarios;
use App\Models\Model_Mantenimiento\Model_regional;


class Libreria_Responsable{

    //// Listado de responsables POA
    public function responsables_poa(){
        $model_funcionario = new Model_funcionarios();
        $responsables=$model_funcionario->obtenerFuncionariosActivos();
        $tabla='';

        // --- BLOQUE DE MENSAJES (SUCCESS / ERROR) ---
        if (session()->getFlashdata('success')) {
            $tabla .= '
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>¡Éxito!</strong> ' . session()->getFlashdata('success') . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        }

        if (session()->getFlashdata('error')) {
            $tabla .= '
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error:</strong> ' . session()->getFlashdata('error') . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        }
        // --------------------------------------------

        $tabla.='
        <div class="card">
            <div class="card-body">

                <!-- Contenedor flexible para Título y Botones -->
                  <div class="d-flex justify-content-between align-items-center mb-4 pb-2">
                      <h4 class="card-title mb-0">RESPONSABLES POA</h4>
                      <div>
                          <!-- Botón Nuevo Registro -->
                          <a href="'.base_url('mnt/nuevo_responsable').'" class="btn btn-success btn-sm">
                              <i class="ti ti-plus"></i> Nuevo Registro
                          </a>
                          <!-- Botón Reporte (Impresión) -->
                          <button type="button" id="btnGenerarReporte" onclick="generarReporteBase64()" class="btn btn-outline-primary btn-sm ms-2">
                              <i class="ti ti-printer"></i> Generar Reporte
                          </button>
                      </div>
                  </div>

                <div class="table-responsive pb-4">
                    <input name="base" type="hidden" value="'.base_url().'">
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
                            <a href="javascript:void(0)" onclick="eliminarResponsable('.$row['id'].', this)" class="text-danger" title="Eliminar">
                                <i class="ti ti-trash fs-3">Eliminar</i>
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



    //// Datos del Responsable para su edicion
    public function get_responsables_poa($get_rep){
      $model_funcionario = new Model_funcionarios();
      $model_reg = new Model_regional();

      ////
      $get_pss=$model_funcionario->get_pwd($get_rep[0]['id']);
      if (empty($get_pss)) {
        $pss='';
      }
      else{
        $pss=$get_pss[0]['fun_apassword'];
      }
      ////

      ////
      $regionales=$model_reg->obtenerRegionales();
      $distritales=$model_reg->obtenerDistritales($get_rep[0]['dep_id']);
      $unidadOrganizacional=$model_reg->obtenerUnidadesOrganizacionales();
      ////

      ////
      $info = password_get_info($get_rep[0]['fun_password']);
      $has_title='<div style="color:green"><b> Hasheado</b></div>';
      if($info['algoName']=='unknown'){
        $has_title='<div style="color:red"><b>No Hasheado</b></div>';
      }
      ////
        $tabla='';
        $tabla.='
        <style>
          .is-loading {
              cursor: wait;
              opacity: 0.7;
              pointer-events: none; /* Bloquea clics en toda la página */
          }
        </style>
        <input name="base" type="hidden" value="'.base_url().'">
        
        <div class="col-12">
                      <div class="card w-100 border position-relative overflow-hidden mb-0">
                        <div class="card-body p-4">
                          <h4 class="card-title">Datos del Responsable POA</h4>
                          <p class="card-subtitle mb-4">Formulario para cambiar editar Información del Reponsable POA</p>
                          <form role="form" action="'.base_url('mnt/update_resp').'" method="post" id="form" class="login-form">
                          <input name="fun_id" id="fun_id" type="hidden" value="'.$get_rep[0]['id'].'">
                            <div class="row">
                              <div class="col-lg-4">
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
                              </div>

                              <div class="col-lg-4">
                                <div class="mb-3">
                                  <label for="exampleInputtext" class="form-label">CARGO ADMINISTRATIVO</label>
                                  <input type="text" class="form-control" id="fn_cargo" name="fn_cargo" placeholder="'.$get_rep[0]['fun_cargo'].'" value="'.$get_rep[0]['fun_cargo'].'">
                                </div>
                                <div class="mb-3">
                                  <label class="form-label">ADMINISTRACIÓN</label>
                                  <select class="form-select" name="tp_adm" id="tp_adm"aria-label="Default select example">
                                    <option value="0">Seleccione ..</option>';
                                    if ($get_rep[0]['fun_adm']==1) {
                                      $tabla.=' <option value="1" selected="true">NACIONAL</option>
                                                <option value="2">REGIONAL</option>';
                                    }
                                    else{
                                      $tabla.='<option value="1">NACIONAL</option>
                                                <option value="2" selected="true">REGIONAL</option>';
                                    }
                                    $tabla.='
                                  </select>
                                </div>
                                <div class="mb-3">
                                  <label class="form-label">REGIONAL</label>
                                  <select class="form-select" name="reg_id" id="reg_id" aria-label="Default select example">
                                    <div id="select_reg">';
                                    if($get_rep[0]['fun_adm']==1){
                                      $tabla.='<option value="10" selected>Administración Central</option>';    
                                    }
                                    else{
                                      foreach($regionales as $row){
                                      if($row['dep_id']==$get_rep[0]['dep_id']){
                                          $tabla.='<option value="'.$row['dep_id'].'" selected>'.strtoupper($row['dep_departamento']).'</option>';    
                                        }
                                        else{
                                          $tabla.='<option value="'.$row['dep_id'].'">'.strtoupper($row['dep_departamento']).'</option>';
                                        }
                                      }
                                    }
                                    $tabla.='
                                    </div>
                                  </select>
                                  
                                </div>
                                <div class="mb-3">
                                  <label class="form-label">DISTRITAL</label>
                                  <select class="form-select" name="dist_id" id="dist_id" aria-label="Default select example">
                                    <div id="select_dist">';
                                    if($get_rep[0]['fun_adm']==1){
                                      $tabla.='<option value="22" selected>Oficina Nacional</option>';    
                                    }
                                    else{
                                      foreach($distritales as $row){
                                      if($row['dist_id']==$get_rep[0]['dist_id']){
                                          $tabla.='<option value="'.$row['dist_id'].'" selected>'.strtoupper($row['dist_distrital']).'</option>';    
                                        }
                                        else{
                                          $tabla.='<option value="'.$row['dist_id'].'">'.strtoupper($row['dist_distrital']).'</option>';
                                        }
                                      }
                                    }
                                    $tabla.='
                                    </div>
                                  </select>
                                </div>
                              </div>

                              <div class="col-lg-4">
                                <div class="mb-3">
                                  <label class="form-label">UNIDAD ORGANIZACIONAL</label>
                                  <select class="form-select" name="uni_id" id="uni_id" aria-label="Default select example">';
                                      foreach($unidadOrganizacional as $row){
                                      if($row['uni_id']==$get_rep[0]['uni_id']){
                                          $tabla.='<option value="'.$row['uni_id'].'" selected>'.strtoupper($row['uni_unidad']).'</option>';    
                                        }
                                        else{
                                          $tabla.='<option value="'.$row['uni_id'].'">'.strtoupper($row['uni_unidad']).'</option>';
                                        }
                                      }
                                    $tabla.='
                                  </select>
                                </div>
                                <div class="mb-3">
                                  <label for="exampleInputtext2" class="form-label">USUARIO</label>
                                  <input type="text" class="form-control" id="fn_usu" name="fn_usu" placeholder="'.$get_rep[0]['fun_usuario'].'" value="'.$get_rep[0]['fun_usuario'].'">
                                </div>
                                <div class="mb-3">
                                  <label for="exampleInputtext2" class="form-label">PASSWORD'.$has_title.'</label>
                                  <input type="text" class="form-control" id="fun_password" name="fun_password" placeholder="Contraseña" value="'.$pss.'">
                                </div>
                                <div class="mb-3">
                                  <label for="exampleInputtext2" class="form-label">CORREO</label>
                                  <input type="text" class="form-control" id="fn_email" name="fn_email" placeholder="ejemplo@gmail.com">
                                </div>
                              </div>
                              <div class="col-12">
                                <div class="d-flex align-items-center justify-content-end mt-4 gap-6">
                                  <button type="submit" id="btnGuardar" class="btn btn-primary">
                                    <span id="textGuardar">Guardar Cambios</span>
                                    <span id="spinnerGuardar" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                  </button>
                                  <a href="'.base_url('mnt/responsables').'" class="btn bg-danger-subtle text-danger">Cancelar</a>
                                </div>

                              </div>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>';
        return $tabla;
    }
}