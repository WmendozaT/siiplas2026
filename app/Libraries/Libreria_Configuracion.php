<?php 
namespace App\Libraries;
use App\Controllers\BaseController; 
// Importa las clases necesarias para initController
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\Index\IndexModel;
use App\Models\Model_Mantenimiento\Model_funcionarios;
use App\Models\Model_Mantenimiento\Model_regional;
use App\Models\Model_Mantenimiento\Model_configuracion;


class Libreria_Configuracion{
    protected $session;
    protected $db;

    public function __construct() {
        // Inicializamos el servicio de sesión
        $this->session = \Config\Services::session();
        // Si necesitas base de datos también:
        $this->db = \Config\Database::connect();
    }

    //// Conf Entidad
    public function conf_form1(){
        $model_index = new IndexModel();
        $conf = $model_index->get_gestion_activo(); /// configuracion gestion activo
        $gestiones=$model_index->list_gestiones_disponibles(); /// list Gestiones
        $trimestre=$model_index->list_trimestre_disponibles(); /// list Trimestres
        $meses=$model_index->list_meses_disponibles(); /// list Meses
        $eval_inicio = (!empty($conf['eval_inicio'])) ? date('Y-m-d', strtotime($conf['eval_inicio'])) : '';
        $eval_fin = (!empty($conf['eval_fin'])) ? date('Y-m-d', strtotime($conf['eval_fin'])) : '';
       // $responsables=$model_funcionario->obtenerFuncionariosActivos();
        $tabla='';
        $tabla.='<div class="row">
                    <div class="col-12">
                      <div class="card w-100 border position-relative overflow-hidden mb-0">
                        <div class="card-body p-4">
                          <h4 class="card-title">Datos Entidad</h4>';
                          if (session()->getFlashdata('success')) {
                              $tabla .= '
                              <div class="alert alert-success alert-dismissible fade show" role="alert">
                                  <i class="ti ti-check fs-4 me-2"></i> ' . session()->getFlashdata('success') . '
                                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                              </div>';
                          }

                          if (session()->getFlashdata('error')) {
                              $tabla .= '
                              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                  <i class="ti ti-alert-circle fs-4 me-2"></i> ' . session()->getFlashdata('error') . '
                                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                              </div>';
                          }
                          $tabla.='
                          <input name="base" type="hidden" value="'.base_url().'">
                          <form role="form" action="'.base_url('mnt/update_conf').'" method="post" id="form_conf" class="login-form">
                          <input name="ide" id="ide" type="hidden" value="'.$conf['ide'].'">
                            <div class="row">
                              <div class="col-lg-3">
                                <div class="mb-3">
                                  <label for="NombreEntidad" class="form-label">Nombre de la Entidad</label>
                                  <input type="text" class="form-control" name="NombreEntidad" id="NombreEntidad" value="'.$conf['conf_nombre_entidad'].'">
                                </div>
                                <div class="mb-3">
                                  <label for="SiglaEntidad" class="form-label">Sigla Entidad</label>
                                  <input type="text" class="form-control" name="SiglaEntidad" id="SiglaEntidad" value="'.$conf['conf_sigla_entidad'].'">
                                </div>
                                <div class="mb-3">
                                  <label for="MisionEntidad" class="form-label">Misión Institucional</label>
                                  <textarea type="text" class="form-control" name="MisionEntidad" id="MisionEntidad" style="height:150px;">'.$conf['conf_mision'].'</textarea>
                                </div>
                                <div class="mb-3">
                                  <label for="VisionEntidad" class="form-label">Visión Institucional</label>
                                  <textarea type="text" class="form-control" name="VisionEntidad" id="VisionEntidad" style="height:150px;">'.$conf['conf_vision'].'</textarea>
                                </div>
                              </div>

                              <div class="col-lg-3">
                                <div class="mb-3">
                                  <label class="form-label">Gestión</label>
                                  <select class="form-select" aria-label="Default select example" name="g_id" id="g_id">';
                                    foreach ($gestiones as $row) {
                                      $selected = ($conf['ide'] == $row['ide']) ? 'selected' : '';
                                      $tabla .= '<option value="'.$row['ide'].'" '.$selected.'>'.$row['conf_gestion'].'</option>';
                                    }
                                  $tabla.='
                                  </select>
                                </div>
                                <div class="mb-3">
                                  <label class="form-label">Trimestre</label>
                                  <select class="form-select" aria-label="Default select example" name="trm_id" id="trm_id">';
                                    foreach ($trimestre as $row) {
                                      $selected = ($conf['conf_mes_otro'] == $row['trm_id']) ? 'selected' : '';
                                      $tabla .= '<option value="'.$row['trm_id'].'" '.$selected.'>'.$row['trm_descripcion'].'</option>';
                                    }
                                  $tabla.='
                                  </select>
                                </div>
                                <div class="mb-3">
                                  <label class="form-label">Mes</label>
                                  <select class="form-select" aria-label="Default select example" name="conf_mes" id="conf_mes">';
                                    foreach ($meses as $row) {
                                      $selected = ($conf['conf_mes'] == $row['m_id']) ? 'selected' : '';
                                      $tabla .= '<option value="'.$row['m_id'].'" '.$selected.'>'.$row['m_descripcion'].'</option>';
                                    }
                                  $tabla.='
                                  </select>
                                </div>
                                <div class="mb-3">
                                  <label for="conf_gestion_desde" class="form-label">Pei - Inicio</label>
                                  <input type="number" class="form-control" name="conf_gestion_desde" id="conf_gestion_desde" value="'.$conf['conf_gestion_desde'].'">
                                </div>
                                <div class="mb-3">
                                  <label for="conf_gestion_hasta" class="form-label">Pei - Fin</label>
                                  <input type="number" class="form-control" name="conf_gestion_hasta" id="conf_gestion_hasta" value="'.$conf['conf_gestion_hasta'].'">
                                </div>
                              </div>

                              <div class="col-lg-3">
                                <div class="mb-3">
                                  <label class="form-label">Ajustar Saldos</label>
                                  <select class="form-select" aria-label="Default select example" name="conf_ajuste_poa" id="conf_ajuste_poa">';
                                    if($conf['conf_ajuste_poa']==1){
                                      $tabla.='
                                      <option value="1" selected>SI</option>
                                      <option value="0">NO</option>';
                                    }
                                    else{
                                      $tabla.='
                                      <option value="1">SI</option>
                                      <option value="0" selected>NO</option>';
                                    }
                                  $tabla.='
                                  </select>
                                </div>
                                
                                <div class="mb-3">
                                  <label class="form-label">Ajustar Credenciales</label>
                                  <select class="form-select" aria-label="Default select example" name="conf_ajuste_poa" id="conf_ajuste_poa">';
                                    if($conf['conf_psw']==1){
                                      $tabla.='
                                      <option value="1" selected>SI</option>
                                      <option value="0">NO</option>';
                                    }
                                    else{
                                      $tabla.='
                                      <option value="1">SI</option>
                                      <option value="0" selected>NO</option>';
                                    }
                                  $tabla.='
                                  </select>
                                </div>
                                <div class="mb-3">
                                  <label class="form-label">Tipo de Mensaje</label>
                                  <select class="form-select" aria-label="Default select example" name="tp_msn" id="tp_msn">';
                                    if($conf['tp_msn']==0){
                                      $tabla.='
                                      <option value="0" selected>NINGUN MENSAJE</option>
                                      <option value="1">ALERTA ROJA</option>
                                      <option value="2">ALERTA AMARILLO</option>
                                      <option value="3">ALERTA VERDE</option>';
                                    }
                                    elseif($conf['tp_msn']==1){
                                      $tabla.='
                                      <option value="0">NINGUN MENSAJE</option>
                                      <option value="1" selected>ALERTA ROJA</option>
                                      <option value="2">ALERTA AMARILLO</option>
                                      <option value="3">ALERTA VERDE</option>';
                                    }
                                    elseif($conf['tp_msn']==2){
                                      $tabla.='
                                      <option value="0">NINGUN MENSAJE</option>
                                      <option value="1">ALERTA ROJA</option>
                                      <option value="2" selected>ALERTA AMARILLO</option>
                                      <option value="3">ALERTA VERDE</option>';
                                    }
                                    elseif($conf['tp_msn']==3){
                                      $tabla.='
                                      <option value="0">NINGUN MENSAJE</option>
                                      <option value="1">ALERTA ROJA</option>
                                      <option value="2">ALERTA AMARILLO</option>
                                      <option value="3" selected>ALERTA VERDE</option>';
                                    }
                                  $tabla.='
                                  </select>
                                </div>
                                <div class="mb-3">
                                  <label for="conf_mensaje" class="form-label">Mensaje</label>
                                  <textarea type="text" class="form-control" name="conf_mensaje" id="conf_mensaje" >'.$conf['conf_mensaje'].'</textarea>
                                </div>
                                <div class="mb-3">
                                  <label for="EvalIni" class="form-label">Fecha Evaluación Inicial</label>
                                  <input type="date" name="eval_inicio" class="form-control" name="EvalIni" id="EvalIni" value="'.$eval_inicio.'"/>
                                </div>
                                <div class="mb-3">
                                  <label for="EvalFin" class="form-label">Fecha Evaluación Final</label>
                                  <input type="date" name="eval_fin" class="form-control" name="EvalFin" id="EvalFin" value="'.$eval_fin.'"/>
                                </div>
                              </div>

                              <div class="col-lg-3">
                                <div class="mb-3">
                                  <label for="rd_aprobacion_poa" class="form-label">RD Aprobacion POA</label>
                                  <textarea type="text" class="form-control" name="rd_aprobacion_poa" id="rd_aprobacion_poa" >'.$conf['rd_aprobacion_poa'].'</textarea>
                                </div>
                                
                                <div class="mb-3">
                                  <label for="rd_abrev_sistema" class="form-label">Abrev. Sistema</label>
                                  <input type="text" class="form-control" name="conf_abrev_sistema" id="conf_abrev_sistema" value="'.$conf['conf_abrev_sistema'].'">
                                </div>
                                <div class="mb-3">
                                  <label for="conf_unidad_resp" class="form-label">Unidad Responsable</label>
                                  <textarea type="text" class="form-control" name="conf_unidad_resp" id="conf_unidad_resp" >'.$conf['conf_unidad_resp'].'</textarea>
                                </div>
                                <div class="mb-3">
                                  <label for="conf_sis_pie" class="form-label">Pie de reporte</label>
                                  <textarea type="text" class="form-control" name="conf_sis_pie" id="conf_sis_pie" >'.$conf['conf_sis_pie'].'</textarea>
                                </div>
                              </div>

                              <div class="col-12">
                                <div class="d-flex align-items-center justify-content-end mt-4 gap-6">
                                  <button type="submit" id="btnGuardar" class="btn btn-primary">
                                    <span id="textGuardar">Guardar Información</span>
                                    <span id="spinnerGuardar" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                  </button>
                                  <a href="'.base_url('dashboard').'" class="btn bg-danger-subtle text-danger">Cancelar</a>
                                </div>
                              </div>

                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>';
        return $tabla;
    }

    //// Conf Modulo
    public function conf_form2(){
        $modulos=$this->session->get('modulos'); 
        $tabla='';
        $tabla.='<div class="row justify-content-center">
                    <div class="col-lg-9">
                      <div class="card border shadow-none">
                        <div class="card-body p-4">
                          <h4 class="card-title">Modulos Disponibles</h4>
                          <input name="base" type="hidden" value="'.base_url().'">
                          <hr>
                          <div>';
                          foreach ($modulos as $row) {
                            $id=$row['modulo_id'];
                            $tabla.='
                            <div class="d-flex align-items-center justify-content-between mb-4">
                              <div class="d-flex align-items-center gap-3">
                                <div class="text-bg-light rounded-1 p-6 d-flex align-items-center justify-content-center">
                                  <i class="ti ti-article text-dark d-block fs-7" width="22" height="22"></i>
                                </div>
                                <div>
                                  <h5 class="fs-4 fw-semibold">'.mb_strtoupper($row['modulo_descripcion']).'</h5>
                                  <p class="mb-0">colocar detalle '.$row['info'].'-'.$id.'</p>
                                </div>
                              </div>
                     
                              <div class="form-check form-switch d-flex justify-content-center">
                                  <input class="form-check-input btn-switch-updates" type="checkbox" 
                                         data-id="'.$row['modulo_id'].'" data-columna="modulo_estado" 
                                         '.($row['incluido'] == 1 ? 'checked' : '').' style="width: 2.5em; height: 1.3em;">
                              </div>
                            </div>';
                          }
                          $tabla.='
                            
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>';


        return $tabla;
    }

    //// Aperuras Programaticas
    public function conf_form3(){
        $model_index = new IndexModel();
        $aperturas=$model_index->List_aperturas($this->session->get('configuracion')['ide']);
        $tabla='
          <div class="col-md-10 mx-auto"> 
          
            <div class="card card-body" >
              <div class="row">
                <div class="col-md-4 col-xl-3">
                  <form class="position-relative">
                    <input type="text" class="form-control product-search ps-5" id="input-search" placeholder="Buscar..." />
                  </form>
                </div>
                <div class="col-md-8 col-xl-9 text-end d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                  <a href="javascript:void(0)" 
                     id="btn-add-contact" 
                     class="btn btn-primary d-flex align-items-center"
                     data-bs-toggle="modal" 
                     data-bs-target="#addContactModal">
                     Nueva Apertura
                  </a>
                </div>
              </div>
            </div>
            <!-- Modal -->

            <div class="modal fade" id="addContactModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="addContactModalTitle" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header d-flex align-items-center">
                    <h5 class="modal-title" id="addContactModalTitle">Nueva Apertura Programatica '.$this->session->get('configuracion')['ide'].'</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <form id="miFormulario" class="needs-validation" novalidate method="post">
                      <input type="hidden" id="id_apertura" name="id_apertura" value="">
                      <div class="row">
                        <div class="col-md-12 mb-3">
                          <label class="form-label">Descripción</label>
                          <input type="text" class="form-control" id="detalle" name="detalle" required />
                          <div class="invalid-feedback">Por favor, ingresa una descripción.</div>
                        </div>
                        <div class="col-md-4 mb-3">
                          <label class="form-label">Programa</label>
                            <input 
                              type="text" 
                              class="form-control" 
                              id="prog" 
                              name="prog" 
                              placeholder="000"

                              maxlength="3"
                              pattern="\d{2,3}"
                              inputmode="numeric"
                              required 
                            />
                          <div class="invalid-feedback">Debe ingresar exactamente 3 números.</div>
                        </div>
                        <div class="col-md-4 mb-3">
                          <label class="form-label">Proyecto</label>
                          <input type="text" class="form-control" value="0000" disabled/>
                        </div>
                        <div class="col-md-4 mb-3">
                          <label class="form-label">Actividad</label>
                          <input type="text" class="form-control" value="000" disabled/>
                        </div>
                      </div>
                    </form>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-light-danger text-danger font-medium" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btnGuardar" form="miFormulario" class="btn btn-primary">
                        <span id="btnText">Guardar</span>
                        <span id="btnLoader" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div class="card card-body">
              <div class="table-responsive">

                <table class="table search-table align-middle text-nowrap">
                  <thead class="header-item">
                    <th style="text-align:center">#</th>
                    <th style="text-align:center">CODIGO</th>
                    <th style="text-align:center">DESCRIPCIÓN PROGRAMA</th>
                    <th style="text-align:center"></th>
                    <th style="text-align:center"></th>
                  </thead>
                  <tbody id="tabla-cuerpo">';
                  $nro=0;
                    foreach($aperturas as $row){
                      $nro++;
                      $tabla.='
                      <tr class="search-items">
                      <td>'.$nro.'</td>
                      <td>
                        '.$row['aper_programa'].' '.$row['aper_proyecto'].' '.$row['aper_actividad'].'
                      </td>
                      <td>
                        '.$row['aper_descripcion'].'
                      </td>
                      <td class="text-center">
                            <div class="action-btn">
                              <!-- Botón de Ver/Editar -->
                              <a href="javascript:void(0)" 
                                 class="btn btn-outline-primary btn-sm d-flex align-items-center edit shadow-sm btn-edit"
                                 data-id="'.$row['aper_id'].'" 
                                 data-prog="'.$row['aper_programa'].'" 
                                 data-desc="'.$row['aper_descripcion'].'"
                                 data-bs-toggle="modal" 
                                 data-bs-target="#addContactModal">
                                 <img src="'.base_url().'Img/Iconos/application_form_edit.png" 
                                    alt="Editar" 
                                    style="width:16px; margin-right:5px;"> Ver
                              </a>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="action-btn">
                              <!-- Botón de Eliminar -->
                              <a href="javascript:void(0)" 
                                 class="btn btn-outline-danger btn-sm d-flex align-items-center delete shadow-sm btn-delete" 
                                 data-id="'.$row['aper_id'].'">
                                 <img src="'.base_url().'Img/Iconos/delete.png" 
                                    alt="Eliminar" 
                                    style="width:16px; margin-right:5px;"> Borrar
                              </a>
                            </div>
                        </td>
                    </tr>';
                    }
                  $tabla.='
                  <tr id="no-results-row" style="display: none;">
                      <td colspan="5" class="text-center text-muted py-4">
                          No se encontraron resultados para tu búsqueda.
                      </td>
                  </tr>
                  </tbody>
                </table>
                
              </div>
            </div>
          </div>';


        return $tabla;
    }


    //// Listado de Partidas disponibles
    public function conf_form4(){
        $model_index = new IndexModel();
        $partidas=$model_index->lista_partidas();
        $tabla='
                  <div class="row">

                    <div class="modal fade" id="modalAlineadas" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered modal-sm">
                        <div class="modal-content border-0 shadow-lg">
                          <!-- Cambios: background-color con dos puntos y clase text-white -->
                          <div class="modal-header text-white" style="background-color: #004640;">
                            <h6 class="modal-title fw-bold text-white">Unidades Alineadas</h6>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body p-0" id="body-alineadas" style="max-height: 350px; overflow-y: auto;">
                            <!-- Aquí se cargará el listado resumido -->
                          </div>
                          <div class="modal-footer bg-light py-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                          </div>
                        </div>
                      </div>
                    </div>


                    <div class="col-lg-8 d-flex align-items-stretch">
                      <div class="card w-100 border position-relative overflow-hidden">
                        <div class="card-body p-4">
                          <div class="table-responsive">
                            <table class="table table-striped w-100 table-bordered datatable-select-inputs" id="mi-tablapartida" style="table-layout: fixed;">
                              <thead style="background-color: #fcfcfd;">
                                <tr>
                                  <!-- Definimos anchos reales en el primer TR del THEAD -->
                                  <th class="py-3 ps-4 text-muted fw-bold" style="width: 60px; font-size: 11px; letter-spacing: 0.5px;">#</th>
                                  <th class="py-3 text-muted fw-bold text-center" style="width: 150px; font-size: 11px; letter-spacing: 0.5px;">CÓDIGO</th>
                                  <th class="py-3 text-muted fw-bold text-center" style="width: 45%; font-size: 11px; letter-spacing: 0.5px;">DESCRIPCIÓN</th>
                                  <th class="py-3 text-muted fw-bold text-center" style="font-size: 11px; letter-spacing: 0.5px;">DEPENDE</th>
                                </tr>
                                <!-- Fila de filtros -->
                                <tr>
                                  <td class="ps-4 pb-3 border-0"></td>
                                  <td class="pb-3 border-0">
                                    <input type="text" class="form-control form-control-sm minimalist-filter text-center" data-index="1" placeholder="Filtrar...">
                                  </td>
                                  <td class="pb-3 border-0">
                                    <input type="text" class="form-control form-control-sm minimalist-filter" data-index="2" placeholder="Buscar descripción...">
                                  </td>
                                  <td class="pb-3 border-0">
                                    <input type="text" class="form-control form-control-sm minimalist-filter" data-index="3" placeholder="Buscar Dependencia...">
                                  </td>
                                </tr>
                              </thead>
                              <tbody class="border-top-0">';
                              $nro=0;
                                foreach($partidas as $row){
                                  $nro++;
                                  $tabla.='
                                   <tr class="search-items minimalist-row">
                                    <td class="ps-4 text-muted small" title="'.$row['par_id'].'">'.$nro.'</td>
                                    <td class="text-center">
                                        <button type="button" 
                                                class="btn btn-link p-0 border-0 btn-activar" 
                                                data-id="'.$row['par_id'].'" 
                                                data-codigo="'.$row['par_codigo'].'" 
                                                title="Click para activar">
                                            <code class="text-primary-emphasis bg-primary-subtle px-2 py-1 rounded hover-active" style="font-size: 14px; cursor: pointer;">
                                                <b>'.$row['par_codigo'].'</b>
                                            </code>
                                        </button>
                                    </td>
                                    <td class="text-wrap">
                                      <div class="fw-medium text-dark">'.strtoupper($row['par_nombre']).'</div>
                                    </td>
                                    <td class="text-wrap">
                                      <div class="fw-medium text-dark">'.$row['par_depende'].'</div>
                                    </td>
                                  </tr>';
                                }
                              $tabla.='
                              </tbody>
                            </table>
                          </div>
                          <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
                              <div class="text-muted small">
                                  Mostrando <span id="span-inicio">1</span> a <span id="span-fin">10</span> de <span id="span-total">0</span> registros
                              </div>
                              <nav>
                                  <ul class="pagination pagination-sm mb-0">
                                      <li class="page-item"><a class="page-link border-0 shadow-none text-muted" href="javascript:void(0)" id="btn-prev">
                                      <img src="'.base_url().'Img/Iconos/resultset_previous.png" alt="Eliminar" style="width:16px; margin-right:5px;"></a></li></a></li>
                                      <li class="page-item active"><a class="page-link border-0" href="javascript:void(0)" id="page-num">1</a></li>
                                      <li class="page-item"><a class="page-link border-0 shadow-none text-muted" href="javascript:void(0)" id="btn-next">
                                      <img src="'.base_url().'Img/Iconos/resultset_next.png" alt="Eliminar" style="width:16px; margin-right:5px;"></a></li>
                                  </ul>
                              </nav>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-4 d-flex align-items-stretch">
                      <div class="card w-100 border position-relative overflow-hidden">
                          <!-- Añadimos max-height y overflow-y -->
                          <div class="card-body p-4" id="u_medida" style="max-height: 800px; overflow-y: auto;">
                              <h4 class="card-title">Mis Unidades de Medida</h4>
                              <!-- Tu lista de unidades o tabla aquí -->
                          </div>
                      </div>
                    </div>
                  </div>';
        return $tabla;
    }
}