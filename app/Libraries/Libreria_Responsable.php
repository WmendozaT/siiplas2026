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
                          <a href="'.base_url('mnt/nuevo_responsable').'" class="btn btn-outline-primary btn-sm ms-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Formulario de Registro">
                              <img src="'.base_url().'Img/Iconos/application_form_add.png" alt="Nuevo"> 
                              <span>Nuevo Registro</span>
                          </a>

                          <!-- Para generar reporte de manera clasica -->
                          <a href="'.base_url('mnt/Pdf_responsables').'" class="btn btn-outline-primary btn-sm ms-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Formulario de Registro">
                              <img src="'.base_url().'Img/Iconos/application_form_add.png" alt="Nuevo"> 
                              <span>Reporte Clasico</span>
                          </a>

                          <button type="button" 
                                  onclick="verReporteModal()" 
                                  class="btn btn-outline-primary btn-sm ms-2" 
                                  data-bs-toggle="tooltip" 
                                  title="Ver Reporte">
                              <img src="'.base_url().'Img/Iconos/page_red.png" alt="Nuevo"> 
                              <span>GENERAR REPORTE PARA FIRMA</span>
                          </button>
                          <!-- Botón Reporte (Impresión) -->
                          <button type="button" id="btnGenerarReporte" onclick="generarReporteBase64()" class="btn btn-outline-primary btn-sm ms-2">
                              <img src="'.base_url().'Img/Iconos/page_red.png" alt="Nuevo"> 
                              <span>Generar Reporte.Pdf</span>
                          </button>

                          <!-- Botón Exportar -->
                          <a href="'.base_url('mnt/exportar_responsablePoa').'" 
                             id="btnExportar"
                             class="btn btn-outline-primary btn-sm ms-2" 
                             data-bs-toggle="tooltip" 
                             title="Exportar Listado">
                              <span id="btnIcon">
                                  <img src="'.base_url().'Img/Iconos/page_excel.png" alt="Excel">
                              </span>
                              <span id="btnText">Exportar Listado.xls</span>
                          </a>

                          <button type="button" onclick="firmarYAbrirReporte()" class="btn btn-info btn-sm ms-2 text-white">
                              <i class="ti ti-certificate"></i> Firmar Digitalmente
                          </button>
                      </div>
                  </div>

                <div class="table-responsive pb-4">
                    <input name="base" type="hidden" value="'.base_url().'">
                    <input name="tp_rep" type="hidden" value="0">
                    <table id="all-student" class="table table-striped table-bordered border text-nowrap align-middle" style="font-size:10.5px;">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>REPONSABLE POA</th>
                          <th>UNIDAD DEPENDIENTE</th>
                          <th>USUARIO</th>
                          <th>DISTRITAL</th>
                          <th>ADMIN</th>
                          <th>MOD. FORM4.</th>
                          <th>MOD. FORM5.</th>
                          <th>MOD. PPTO.</th>
                          <th>CERT. POA</th>
                          <th>EVAL. POA.</th>
                          <th>CERT. DIGITAL</th>
                          <th>PASS.</th>
                          <th></th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>';
                      $nro=0;
                      foreach($responsables as $row){ 
                        $id=$row['id'];
                        $nro++;
                        
                        $tabla.='
                        <tr>
                          <td class="text-center">
                              <div class="icono_perfil mx-auto shadow-sm" style="width: 35px; height: 35px; border-radius: 50%; overflow: hidden; border: 2px solid #ddd; cursor: pointer;">
                                  <img src="'.base_url($row['imagen_perfil']).'" 
                                       class="img-fluid rounded-circle img-preview" 
                                       data-img="'.base_url($row['imagen_perfil']).'" 
                                       data-name="'.$row['fun_nombre'].' '.$row['fun_paterno'].'  '.$row['fun_materno'].'"
                                       data-unidad="'.strtoupper($row['dist_distrital']).'"
                                       alt="Perfil" 
                                       style="width: 100%; height: 100%; object-fit: cover;"/>
                              </div>
                          </td>
                          <td>'.$row['fun_nombre'].' '.$row['fun_paterno'].' '.$row['fun_materno'].'</td>
                          <td>'.$row['uni_unidad'].'</td>
                          <td>'.$row['fun_usuario'].'</td>
                          <td>'.$row['dist_distrital'].'</td>
                          <td class="text-center">
                              <div class="form-check form-switch d-flex justify-content-center">
                                  <input class="form-check-input btn-switch-update" type="checkbox" 
                                         data-id="'.$id.'" data-columna="tp_adm" 
                                         '.($row['tp_adm'] == 1 ? 'checked' : '').' style="width: 2.5em; height: 1.3em;">
                              </div>
                          </td>
                          <td class="text-center">
                              <div class="form-check form-switch d-flex justify-content-center">
                                  <input class="form-check-input btn-switch-update" type="checkbox" 
                                         data-id="'.$id.'" data-columna="conf_mod_form4" 
                                         '.($row['conf_mod_form4'] == 1 ? 'checked' : '').' style="width: 2.5em; height: 1.3em;">
                              </div>
                          </td>
                          <td class="text-center">
                              <div class="form-check form-switch d-flex justify-content-center">
                                  <input class="form-check-input btn-switch-update" type="checkbox" 
                                         data-id="'.$id.'" data-columna="conf_mod_form5" 
                                         '.($row['conf_mod_form5'] == 1 ? 'checked' : '').' style="width: 2.5em; height: 1.3em;">
                              </div>
                          </td>
                          <td class="text-center">
                              <div class="form-check form-switch d-flex justify-content-center">
                                  <input class="form-check-input btn-switch-update" type="checkbox" 
                                         data-id="'.$id.'" data-columna="conf_mod_ppto" 
                                         '.($row['conf_mod_ppto'] == 1 ? 'checked' : '').' style="width: 2.5em; height: 1.3em;">
                              </div>
                          </td>
                          <td class="text-center">
                              <div class="form-check form-switch d-flex justify-content-center">
                                  <input class="form-check-input btn-switch-update" type="checkbox" 
                                         data-id="'.$id.'" data-columna="conf_cert_poa" 
                                         '.($row['conf_cert_poa'] == 1 ? 'checked' : '').' style="width: 2.5em; height: 1.3em;">
                              </div>
                          </td>
                          <td class="text-center">
                              <div class="form-check form-switch d-flex justify-content-center">
                                  <input class="form-check-input btn-switch-update" type="checkbox" 
                                         data-id="'.$id.'" data-columna="conf_eval_poa" 
                                         '.($row['conf_eval_poa'] == 1 ? 'checked' : '').' style="width: 2.5em; height: 1.3em;">
                              </div>
                          </td>
                          <td class="text-center">
                              <div class="form-check form-switch d-flex justify-content-center">
                                  <input class="form-check-input btn-switch-update" type="checkbox" 
                                         data-id="'.$id.'" data-columna="conf_cert_digital" 
                                         '.($row['conf_cert_digital'] == 1 ? 'checked' : '').' style="width: 2.5em; height: 1.3em;">
                              </div>
                          </td>
                          <td class="text-center">
                              <div class="form-check form-switch d-flex justify-content-center">
                                  <input class="form-check-input btn-switch-update" type="checkbox" 
                                         data-id="'.$id.'" data-columna="sw_pass" 
                                         '.($row['sw_pass'] == 1 ? 'checked' : '').' style="width: 2.5em; height: 1.3em;">
                              </div>
                          </td>
                          <td>
                            <a href="'.base_url().'mnt/update_responsable/'.$row['id'].'" 
                               class="btn btn-primary btn-sm" 
                               data-bs-toggle="tooltip" 
                               data-bs-placement="top" 
                               title="Ver Información del Responsable">
                               
                               <img src="'.base_url().'Img/Iconos/application_form_edit.png" 
                                    alt="Editar" 
                                    style="width:16px; margin-right:5px;"> 
                               Modificar
                            </a>
                          </td>
                          <td>
                            <a href="javascript:void(0)" onclick="eliminarResponsable('.$row['id'].', this)" 
                               class="btn btn-danger btn-sm" 
                               data-bs-toggle="tooltip" 
                               data-bs-placement="top" 
                               title="Desactivar Responsable">
                               
                               <img src="'.base_url().'Img/Iconos/delete.png" 
                                    alt="Eliminar" 
                                    style="width:16px; margin-right:5px;"> 
                               desactivar
                            </a>
                          </td>
                        </tr>';
                      }
                      $tabla.='
                      </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalVerPerfil" tabindex="-1" aria-hidden="true">
            <!-- Cambiado modal-sm a modal-md para más ancho -->
            <div class="modal-dialog modal-md modal-dialog-centered"> 
                <div class="modal-content shadow-lg">
                    <div class="modal-header">
                        <h5 class="modal-title fw-semibold" id="perfilNombre">Información del Responsable</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <!-- Aumentado de 150px a 250px -->
                        <div class="rounded-circle border border-4 border-light shadow-sm mx-auto mb-3" 
                             style="width: 250px; height: 250px; overflow: hidden; background-color: #f8f9fa;">
                            <img src="" id="imgModal" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <p class="text-muted small"><div id="unidad"></div></p>
                    </div>
                </div>
            </div>
        </div>

    <div class="modal fade" id="modalReporte" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Reporte de Responsables</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- CAMBIO: iframe por embed para que Jacobitus acceda al Base64 -->
                <embed id="frameReporte" src="" type="application/pdf" width="100%" height="500px">
                
                <!-- OBLIGATORIO: Div para que Jacobitus procese logs/errores -->
                <div id="div-logs-firma" style="display:none;"></div>

                <div class="card mt-3 border-primary">
                    <div class="card-body bg-light">
                        <h6 class="text-primary"><i class="fas fa-pen-fancy"></i> Firma Digital (ADSIB)</h6>
                        <p class="small text-muted">Asegúrese de que <b>Jacobitus Total</b> esté iniciado.</p>
                        <div class="row g-2">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary w-100" onclick="ejecutarFirmaDigital()">
                                    <i class="fas fa-certificate"></i> Firmar Documento
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>';

        return $tabla;
    }


  //// Listado de Unidades Administrativas para el Seguimiento POA
  public function responsables_seguimiento_poa(){
        $model_funcionario = new Model_funcionarios();
        $responsables=$model_funcionario->obtenerFuncionariosActivos_seguimientoPOA();
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
                      <h4 class="card-title mb-0">RESPONSABLES - SEGUIMIENTO POA</h4>
                      <div>
                          <!-- Botón Nuevo Registro -->
                          <a href="'.base_url('mnt/nuevo_reponsable_seguimientopoa').'" class="btn btn-outline-primary btn-sm ms-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Formulario de Registro">
                              <img src="'.base_url().'Img/Iconos/application_form_add.png" alt="Nuevo"> 
                              <span>Nuevo Registro</span>
                          </a>
                          <!-- Botón Reporte (Impresión) -->
                          <button type="button" id="btnGenerarReporte" onclick="generarReporteBase64()" class="btn btn-outline-primary btn-sm ms-2">
                              <img src="'.base_url().'Img/Iconos/page_red.png" alt="Nuevo"> 
                              <span>Generar Reporte.Pdf</span>
                          </button>

                          <!-- Botón Exportar -->
                          <a href="'.base_url('mnt/exportar_responsablePoa').'" 
                             id="btnExportar"
                             class="btn btn-outline-primary btn-sm ms-2" 
                             data-bs-toggle="tooltip" 
                             title="Exportar Listado">
                              <span id="btnIcon">
                                  <img src="'.base_url().'Img/Iconos/page_excel.png" alt="Excel">
                              </span>
                              <span id="btnText">Exportar Listado.xls</span>
                          </a>

                          <button type="button" onclick="firmarYAbrirReporte()" class="btn btn-info btn-sm ms-2 text-white">
                              <i class="ti ti-certificate"></i> Firmar Digitalmente
                          </button>
                      </div>
                  </div>

                <div class="table-responsive pb-4">
                    <input name="base" type="hidden" value="'.base_url().'">
                    <input name="tp_rep" type="hidden" value="1">
                    <table id="all-student" class="table table-striped table-bordered border text-nowrap align-middle" style="font-size:10.5px;">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>UNIDAD ORGANIZACIONAL</th>
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
                          <td>'.$row['aper_programa'].' '.$row['aper_proyecto'].' '.$row['aper_actividad'].' - '.$row['aper_descripcion'].'</td>
                          <td>'.$row['tipo_subactividad'].' '.$row['serv_descripcion'].'</td>
                          <td>'.$row['fun_usuario'].'</td>
                          <td>'.$row['adm'].'</td>
                          <td>'.$row['dist_distrital'].'</td>
                          <td>
                            <a href="'.base_url().'mnt/form_update_segpoa/'.$row['id'].'" 
                               class="btn btn-primary btn-sm" 
                               data-bs-toggle="tooltip" 
                               data-bs-placement="top" 
                               title="Ver Información del Responsable">
                               
                               <img src="'.base_url().'Img/Iconos/application_form_edit.png" 
                                    alt="Editar" 
                                    style="width:16px; margin-right:5px;"> 
                               Ver
                            </a>
                          </td>
                          <td>
                            <a href="javascript:void(0)" onclick="eliminarResponsable('.$row['id'].', this)" 
                               class="btn btn-danger btn-sm" 
                               data-bs-toggle="tooltip" 
                               data-bs-placement="top" 
                               title="Desactivar Responsable">
                               
                               <img src="'.base_url().'Img/Iconos/delete.png" 
                                    alt="Eliminar" 
                                    style="width:16px; margin-right:5px;"> 
                               Ver
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

     ////Formulario Datos del Responsable para su edicion
    public function form_add_responsables_poa(){
    //  $model_funcionario = new Model_funcionarios();
      $model_reg = new Model_regional();

      ////
     /// $regionales=$model_reg->obtenerRegionales();
      $unidadOrganizacional=$model_reg->obtenerUnidadesOrganizacionales();
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
        <div id="btnExportar"></div>
        <div class="col-12">
                      <div class="card w-100 border position-relative overflow-hidden mb-0">
                        <div class="card-body p-4">
                          <h4 class="card-title">Adicionar Responsable POA</h4>
                          <p class="card-subtitle mb-4">Formulario para Adicionar Nuevo Reponsable POA</p>
                          <form role="form" action="'.base_url('mnt/add_resp').'" method="post" id="form_add" class="login-form">
                        
                            <div class="row">
                              <div class="col-lg-4">
                                <div class="mb-3">
                                  <label for="exampleInputtext" class="form-label">NOMBRE</label>
                                  <input type="text" class="form-control" id="fn_nom" name="fn_nom" placeholder="Nombre ..">
                                </div>
                                <div class="mb-3">
                                  <label for="exampleInputtext" class="form-label">APELLIDO PATERNO</label>
                                  <input type="text" class="form-control" id="fn_pt" name="fn_pt" placeholder="Paterno ..">
                                </div>
                                <div class="mb-3">
                                  <label for="exampleInputtext" class="form-label">APELLIDO PATERNO</label>
                                  <input type="text" class="form-control" id="fn_mt" name="fn_mt" placeholder="Materno ..">
                                </div>
                                <div class="mb-3">
                                  <label for="exampleInputtext" class="form-label">CI</label>
                                  <input type="number" class="form-control" id="fn_ci" name="fn_ci" placeholder="Nro Ci ..">
                                </div>
                                <div class="mb-3">
                                  <label for="exampleInputtext" class="form-label">NRO DE CELULAR</label>
                                  <input type="number" class="form-control" id="fn_fono" name="fn_fono" placeholder="">
                                </div>
                              </div>

                              <div class="col-lg-4">
                                <div class="mb-3">
                                  <label for="exampleInputtext" class="form-label">CARGO ADMINISTRATIVO</label>
                                  <input type="text" class="form-control" id="fn_cargo" name="fn_cargo" placeholder="Cargo ..">
                                </div>
                                <div class="mb-3">
                                  <label class="form-label">ADMINISTRACIÓN</label>
                                  <select class="form-select" name="tp_adm1" id="tp_adm1"aria-label="Default select example">
                                    <option value="0">Seleccione ..</option>
                                    <option value="1">NACIONAL</option>
                                    <option value="2">REGIONAL</option>
                                  </select>
                                </div>
                                <div class="mb-3">
                                  <label class="form-label">REGIONAL</label>
                                  <select class="form-select" name="reg_id1" id="reg_id1" aria-label="Default select example">
                                  
                                    <div id="select_reg"><option value="0">Seleccione ..</option></div>
                                  </select>
                                  
                                </div>
                                <div class="mb-3">
                                  <label class="form-label">DISTRITAL</label>
                                  <select class="form-select" name="dist_id" id="dist_id" aria-label="Default select example">
                                  
                                    <div id="select_dist"><option value="0">Seleccione ..</option></div>
                                  </select>
                                </div>
                              </div>

                              <div class="col-lg-4">
                                <div class="mb-3">
                                  <label class="form-label">UNIDAD ORGANIZACIONAL</label>
                                  <select class="form-select" name="uni_id" id="uni_id" aria-label="Default select example">';
                                      foreach($unidadOrganizacional as $row){
                                        $tabla.='<option value="'.$row['uni_id'].'">'.strtoupper($row['uni_unidad']).'</option>';
                                      }
                                    $tabla.='
                                  </select>
                                </div>
                                <div class="mb-3">
                                  <label for="exampleInputtext2" class="form-label">USUARIO</label>
                                  <input type="text" class="form-control" id="fn_usu" name="fn_usu" placeholder="Asignar Usuario ..">
                                </div>
                                <div class="mb-3">
                                  <label for="exampleInputtext2" class="form-label">PASSWORD</label>
                                  <input type="text" class="form-control" id="fun_password" name="fun_password" placeholder="Asignar Contraseña ..">
                                </div>
                                <div class="mb-3">
                                  <label for="exampleInputtext2" class="form-label">CORREO</label>
                                  <input type="text" class="form-control" id="fn_email" name="fn_email" placeholder="ejemplo@gmail.com">
                                </div>
                              </div>
                              <div class="col-12">
                                <div class="d-flex align-items-center justify-content-end mt-4 gap-6">
                                  <button type="submit" id="btnGuardar" class="btn btn-primary">
                                    <span id="textGuardar">Guardar Información</span>
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


    ////Formulario Datos del Responsable Seguimiento POA para su registro
    public function form_add_responsables_seguimiento_poa(){
      $model_reg = new Model_regional();
      $regionales=$model_reg->obtenerRegionales();

        $tabla='';
        $tabla.='
        <style>
          .is-loading {
              cursor: wait;
              opacity: 0.7;
              pointer-events: none; /* Bloquea clics en toda la página */
          }
        </style>
        
        <div id="btnExportar"></div>
        <div class="col-12">
              <div class="card w-100 border position-relative overflow-hidden mb-0">
                <div class="card-body p-4">
                  <h4 class="card-title">Adicionar Responsable-Seguimiento POA</h4>
                  <p class="card-subtitle mb-4">Formulario para Adicionar Nuevo Reponsable para el Seguimiento POA</p>
                  <form role="form" action="'.base_url('mnt/add_segpoa').'" method="post" id="form_addspoa" class="login-form">
                    <input name="base" type="hidden" value="'.base_url().'">
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="mb-3">
                          <label for="exampleInputtext" class="form-label">SELECCIONE REGIONAL</label>
                          <select class="form-select" name="reg_id2" id="reg_id2" aria-label="Default select example">
                          <option value="0" >Seleccione ..</option>';
                          foreach($regionales as $row){
                            $tabla.='<option value="'.$row['dep_id'].'" >'.strtoupper($row['dep_departamento']).'</option>'; 
                          }
                          $tabla.='
                          </select>
                        </div>
                        <div class="mb-3">
                          <label for="exampleInputtext" class="form-label">PROGRAMA</label>
                          <select class="form-select" name="proy_id" id="proy_id" aria-label="Default select example">
                            <div id="programa"></div>
                          </select>
                        </div>
                        <div class="mb-3">
                          <label for="exampleInputtext" class="form-label">UNIDAD RESPONSABLE</label>
                          <select class="form-select" name="com_id" id="com_id" aria-label="Default select example">
                            <div id="uresp"></div>
                          </select>
                        </div>
                      </div>

                      <div class="col-lg-6">
                        <div class="mb-3">
                          <label for="exampleInputtext2" class="form-label">USUARIO</label>
                          <input type="text" class="form-control" id="fn_usu" name="fn_usu" placeholder="Asignar Usuario ..">
                        </div>
                        <div class="mb-3">
                          <label for="exampleInputtext2" class="form-label">PASSWORD</label>
                          <input type="text" class="form-control" id="fun_password" name="fun_password" placeholder="Asignar Contraseña ..">
                        </div>
                        <div class="mb-3">
                          <label for="exampleInputtext2" class="form-label">CORREO</label>
                          <input type="text" class="form-control" id="fn_email" name="fn_email" placeholder="ejemplo@gmail.com">
                        </div>
                      </div>
                      <div class="col-12">
                        <div class="d-flex align-items-center justify-content-end mt-4 gap-6">
                          <button type="submit" id="btnGuardar" class="btn btn-primary">
                            <span id="textGuardar">Guardar Información</span>
                            <span id="spinnerGuardar" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                          </button>
                          <a href="'.base_url('mnt/resp_seguimientopoa').'" class="btn bg-danger-subtle text-danger">Cancelar</a>
                        </div>

                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>';
        return $tabla;
    }

    ////Formulario Datos del Responsable POA para su edicion
    public function get_responsables_poa($get_rep){
      $model_funcionario = new Model_funcionarios();
      $model_reg = new Model_regional();

      ////
      $get_pss=$model_funcionario->get_pwd($get_rep['id']);
      if (empty($get_pss)) {
        $pss='';
      }
      else{
        $pss=$get_pss[0]['fun_apassword'];
      }
      ////

      ////
      $regionales=$model_reg->obtenerRegionales();
      $distritales=$model_reg->obtenerDistritales($get_rep['dep_id']);
      $unidadOrganizacional=$model_reg->obtenerUnidadesOrganizacionales();
      $img_perfiles=$model_funcionario->perfiles_img();
      ////

      ////
      $info = password_get_info($get_rep['fun_password']);
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
        <div id="btnExportar"></div>
        <div class="col-12">
                      <div class="card w-100 border position-relative overflow-hidden mb-0">
                        <div class="card-body p-4">
                          <h4 class="card-title">Datos del Responsable POA</h4>
                          <p class="card-subtitle mb-4">Formulario para cambiar editar Información del Reponsable POA</p>
                          <form role="form" action="'.base_url('mnt/update_resp').'" method="post" id="form" class="login-form">
                          <input name="fun_id" id="fun_id" type="hidden" value="'.$get_rep['id'].'">
                            <div class="row">
                              <div class="col-lg-4">
                                <div class="mb-3">
                                  <label for="exampleInputtext" class="form-label">NOMBRE</label>
                                  <input type="text" class="form-control" id="fn_nom" name="fn_nom" placeholder="'.$get_rep['fun_nombre'].'" value="'.$get_rep['fun_nombre'].'">
                                </div>
                                <div class="mb-3">
                                  <label for="exampleInputtext" class="form-label">APELLIDO PATERNO</label>
                                  <input type="text" class="form-control" id="fn_pt" name="fn_pt" placeholder="'.$get_rep['fun_paterno'].'" value="'.$get_rep['fun_paterno'].'">
                                </div>
                                <div class="mb-3">
                                  <label for="exampleInputtext" class="form-label">APELLIDO PATERNO</label>
                                  <input type="text" class="form-control" id="fn_mt" name="fn_mt" placeholder="'.$get_rep['fun_materno'].'" value="'.$get_rep['fun_materno'].'">
                                </div>
                                <div class="mb-3">
                                  <label for="exampleInputtext" class="form-label">CI</label>
                                  <input type="number" class="form-control" id="fn_ci" name="fn_ci" placeholder="'.$get_rep['fun_ci'].'" value="'.$get_rep['fun_ci'].'">
                                </div>
                                <div class="mb-3">
                                  <label for="exampleInputtext" class="form-label">NRO DE CELULAR</label>
                                  <input type="number" class="form-control" id="fn_fono" name="fn_fono" placeholder="'.$get_rep['fun_telefono'].'" value="'.$get_rep['fun_telefono'].'">
                                </div>
                              </div>

                              <div class="col-lg-4">
                                <div class="mb-3">
                                  <label for="exampleInputtext" class="form-label">CARGO ADMINISTRATIVO</label>
                                  <input type="text" class="form-control" id="fn_cargo" name="fn_cargo" placeholder="'.$get_rep['fun_cargo'].'" value="'.$get_rep['fun_cargo'].'">
                                </div>
                                <div class="mb-3">
                                  <label class="form-label">ADMINISTRACIÓN</label>
                                  <select class="form-select" name="tp_adm" id="tp_adm"aria-label="Default select example">
                                    <option value="0">Seleccione ..</option>';
                                    if ($get_rep['fun_adm']==1) {
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
                                    if($get_rep['fun_adm']==1){
                                      $tabla.='<option value="10" selected>Administración Central</option>';    
                                    }
                                    else{
                                      foreach($regionales as $row){
                                      if($row['dep_id']==$get_rep['dep_id']){
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
                                    if($get_rep['fun_adm']==1){
                                      $tabla.='<option value="22" selected>Oficina Nacional</option>';    
                                    }
                                    else{
                                      foreach($distritales as $row){
                                      if($row['dist_id']==$get_rep['dist_id']){
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

                                <div class="mb-3">
                                      <label class="form-label">IMG PERFIL</label>
                                      <select class="form-select" name="img_id" id="img_id">';
                                      
                                      foreach($img_perfiles as $row) {
                                          $selected = ($row['img_id'] == $get_rep['conf_img']) ? 'selected' : '';
                                          $rutaImg = base_url($row['imagen_perfil']);

                                          // El atributo data-img es la clave para el JS
                                          $tabla .= '<option value="'.$row['img_id'].'" '.$selected.' data-img="'.$rutaImg.'">
                                                        Perfil '.$row['img_id'].'
                                                     </option>';
                                      }
                                      
                                  $tabla .= '
                                      </select>
                                  </div>

                                  <div class="d-flex justify-content-center mb-3">
                                      <div id="icono_perfil" class="mx-auto shadow-sm" 
                                           style="width: 130px; height: 130px; border-radius: 50%; overflow: hidden; border: 2px solid #ddd; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                          <!-- La imagen se inyectará aquí -->
                                      </div>
                                  </div>
                              </div>

                              <div class="col-lg-4">
                                <div class="mb-3">
                                  <label class="form-label">UNIDAD ORGANIZACIONAL</label>
                                  <select class="form-select" name="uni_id" id="uni_id" aria-label="Default select example">';
                                      foreach($unidadOrganizacional as $row){
                                      if($row['uni_id']==$get_rep['uni_id']){
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
                                  <input type="text" class="form-control" id="fn_usu" name="fn_usu" placeholder="'.$get_rep['fun_usuario'].'" value="'.$get_rep['fun_usuario'].'">
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



    ////Formulario Datos del Responsable-Seguimiento POA para su edicion
    public function get_responsables_seguimiento_poa($get_rep){
      $model_funcionario = new Model_funcionarios();
      $model_reg = new Model_regional();

      ////
      $get_pss=$model_funcionario->get_pwd($get_rep['id']);
      if (empty($get_pss)) {
        $pss='';
      }
      else{
        $pss=$get_pss[0]['fun_apassword'];
      }
      ////

      ////
      $regionales=$model_reg->obtenerRegionales();
     /// $distritales=$model_reg->obtenerDistritales($get_rep[0]['dep_id']);
     /// $unidadOrganizacional=$model_reg->obtenerUnidadesOrganizacionales();

      $get_uni=$model_funcionario->get_uniresponsable($get_rep['cm_id']); /// relacion unires->programa
      //$get_prog=$model_funcionario->get_AperturasxRegional($get_uni['proy_id']); /// id detalle proyecto
      $get_reg=$model_funcionario->datos_regional($get_rep['fun_dist']); //// id distrital, id regional
      $lista_programas=$model_funcionario->obtenerAperturasxRegional($get_reg['dep_id']); /// lista de programas por regional
      $lista_unidadresp=$model_funcionario->get_list_unidadresponsables($get_uni['proy_id']); /// lista de unidades responsables por proyecto
      ////

      ////
      $info = password_get_info($get_rep['fun_password']);
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
        <div id="btnExportar"></div>
        <div class="col-12">
              <div class="card w-100 border position-relative overflow-hidden mb-0">
                <div class="card-body p-4">
                  <h4 class="card-title">Datos del Responsable-Seguimiento POA</h4>
                  <p class="card-subtitle mb-4">Formulario para cambiar editar Información del Reponsable POA</p>
                  <form role="form" action="'.base_url('mnt/update_respspoa').'" method="post" id="form_update" class="login-form">
                  <input name="fun_id" id="fun_id" type="hidden" value="'.$get_rep['id'].'">
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="mb-3">
                          <label class="form-label">REGIONAL</label>
                          <select class="form-select" name="reg_id" id="reg_id" aria-label="Default select example" disabled>
                            <option value="0">Seleccione ..</option>';
                              foreach($regionales as $row){
                              if($row['dep_id']==$get_rep['dep_id']){
                                  $tabla.='<option value="'.$row['dep_id'].'" selected>'.strtoupper($row['dep_departamento']).'</option>';    
                                }
                                else{
                                  $tabla.='<option value="'.$row['dep_id'].'">'.strtoupper($row['dep_departamento']).'</option>';
                                }
                              }
                            $tabla.='
                          </select>
                        </div>
                        <div class="mb-3">
                          <label class="form-label">PROGRAMA</label>
                          <select class="form-select" name="proy_id" id="proy_id" aria-label="Default select example">
                            <div id="select_reg">
                            <option value="0">Seleccione ..</option>';
                              foreach($lista_programas as $row){
                                if($row['proy_id']==$get_uni['proy_id']){
                                  $tabla.='<option value="'.$row['proy_id'].'" selected>'.$row['aper_programa'].' '.$row['aper_proyecto'].' '.$row['aper_actividad'].'-'.strtoupper($row['aper_descripcion']).'</option>';    
                                }
                                else{
                                  $tabla.='<option value="'.$row['proy_id'].'">'.$row['aper_programa'].' '.$row['aper_proyecto'].' '.$row['aper_actividad'].'-'.strtoupper($row['aper_descripcion']).'</option>';
                                }
                              }
                            $tabla.='
                            </div>
                          </select>
                          
                        </div>
                        <div class="mb-3">
                          <label class="form-label">UNIDAD RESPONSABLE</label>
                          <select class="form-select" name="com_id" id="com_id" aria-label="Default select example">
                            <div id="uresp">';
                              foreach($lista_unidadresp as $row){
                                if($row['com_id']==$get_rep['cm_id']){
                                    $tabla.='<option value="'.$row['com_id'].'" selected>'.$row['tipo_subactividad'].' '.$row['serv_descripcion'].'</option>';    
                                  }
                                  else{
                                    $verif=$model_funcionario->verif_uresponsable_existente_seguimiento($row['com_id']);
                                    if(count($verif)==0){
                                      $tabla.='<option value="'.$row['com_id'].'">'.$row['tipo_subactividad'].' '.$row['serv_descripcion'].'</option>';
                                    }
                                  }
                              }
                            $tabla.='
                            </div>
                          </select>
                        </div>
                      </div>

                      <div class="col-lg-6">
                        <div class="mb-3">
                          <label for="exampleInputtext2" class="form-label">USUARIO</label>
                          <input type="text" class="form-control" id="fn_usu" name="fn_usu" placeholder="'.$get_rep['fun_usuario'].'" value="'.$get_rep['fun_usuario'].'">
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
                          <a href="'.base_url('mnt/resp_seguimientopoa').'" class="btn bg-danger-subtle text-danger">Cancelar</a>
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