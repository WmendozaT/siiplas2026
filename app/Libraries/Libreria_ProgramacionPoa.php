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
use App\Models\Model_Ppto\Model_PptoAsig;


class Libreria_ProgramacionPoa{
    protected $session;
    protected $db;

    public function __construct() {
        // Inicializamos el servicio de sesión
        $this->session = \Config\Services::session();
        // Si necesitas base de datos también:
        $this->db = \Config\Database::connect();
    }

    ///// LISTA PROGRAMACION POA
    public function Lista_ProgramacionPoa(){
        $model_regional = new Model_regional();
        $regionales=$model_regional->obtenerRegionales();
        $unidades_disponibles=$model_regional->lista_programacion_poa();
        $tabla='';
        $tabla.='

            <div class="card">
              <div class="card-body">
                <div class="d-md-flex align-items-center justify-content-between mb-4">
                  <div>
                    <h4 class="card-title fw-semibold">Programación POA - '.$this->session->get('configuracion')['ide'].'</h4>
                  </div>
                  <div class="d-flex align-items-center gap-2 mt-3 mt-md-0">

                      <div class="ms-auto">
                        <select class="form-select border-primary text-primary fw-semibold" id="dep_id_add" name="dep_id_add" style="border-radius: 7px; background-color: rgba(93, 135, 255, 0.1); font-size:11px;">
                          <option value="" selected disabled>+ Adicionar POA</option>';
                          foreach($regionales as $row){
                            $tabla.='<option value="'.$row['dep_id'].'">'.$row['dep_departamento'].'.pdf</option>';
                          }
                          $tabla.='
                        </select>
                      </div>

                      <div class="ms-auto">
                        <select class="form-select border-success text-success fw-semibold" id="dep_id_pdf" name="dep_id_pdf" style="border-radius: 7px; background-color: rgba(19, 222, 185, 0.1); font-size:11px;">
                          <option value="" selected disabled>📄 Generar PDF</option>
                          <option value="0">INSTITUCIONAL.pdf</option>';
                          foreach($regionales as $row){
                            $tabla.='<option value="'.$row['dep_id'].'">'.$row['dep_departamento'].'.Pdf</option>';
                          }
                        $tabla.='
                        </select>
                      </div>

                      <div class="ms-auto">
                        <select class="form-select border-success text-success fw-semibold" id="dep_id_xls" name="dep_id_xls" style="border-radius: 7px; background-color: rgba(19, 222, 185, 0.1); font-size:11px;">
                          <option value="" selected disabled>📊 Exportar Excel</option>
                          <option value="0">INSTITUCIONAL.xls</option>';
                          foreach($regionales as $row){
                            $tabla.='<option value="'.$row['dep_id'].'">'.$row['dep_departamento'].'.Xls</option>';
                          }
                        $tabla.='
                        </select>
                      </div>

                  </div>

                </div>
                <div class="table-responsive pb-4">

                    <table id="all-student" class="table table-striped table-bordered border text-nowrap align-middle" style="font-size:10.5px;">
                      <thead>
                        <tr>
                            <th style="white-space: normal; min-width: 5px;">#</th>
                            <th style="white-space: normal; min-width: 50px;">ESTADO</th>
                            <th style="white-space: normal; min-width: 50px;">TIPO DE GASTO</th>
                            <th style="white-space: normal; min-width: 50px;"></th>
                            <th style="white-space: normal; min-width: 50px;"></th>
                            <th style="white-space: normal; min-width: 50px;"></th>
                            <th style="white-space: normal; min-width: 60px;">DISTRITAL</th>
                            <th style="white-space: normal; min-width: 60px;">APERTURA PROGRAMATICA</th>
                            <th style="white-space: normal; min-width: 60px;">CODIGO SISIN</th>
                            <th style="white-space: normal; min-width: 50px;">GASTO CORRIENTE / INVERSIÓN</th>
                            <th >PPTO. ASIGNADO '.$this->session->get('configuracion')['ide'].'</th>
                        </tr>
                      </thead>
                      <tbody>';
                      $nro=0;
                      foreach($unidades_disponibles as $row){
                        $detalle=$row['actividad'].' '.$row['abrev'];
                        if($row['tp_id']==1){
                            $detalle=$row['proy_nombre'];
                        }
                        $nro++;
                        $tabla.='
                        <tr style="font-size:10px;">
                          <td class="text-center">'.$nro.'</td>
                          <td>'.$row['estado_poa'].'</td>
                          <td>'.$row['tipo_gasto_nombre'].'</td>
                          <td class="text-center">';
                          if($row['ppto_asignado']!=0){
                            $tabla.='
                            <button type="button" 
                                    class="btn btn-sm btn-success btn-ver-partidas" 
                                    data-id="'.$row['aper_id'].'" 
                                    data-nombre="'.$detalle.'"
                                    title="Ver Partidas">
                              <span id="btnIcon">
                              <img src="'.base_url().'Img/Iconos/application_view_detail.png" alt="Partidas">
                              </span> Ver
                            </button>';
                          }
                          $tabla.='
                          </td>
                          <td></td>
                          <td></td>
                          <td>'.$row['dist_distrital'].'</td>
                          <td>'.$row['prog'].' '.$row['proy'].' '.$row['act'].'</td>
                          <td>'.$row['proy_sisin'].'</td>
                          <td style="white-space: normal; min-width: 200px;">'.$detalle.'</td>

                          <td>'.number_format($row['ppto_asignado'], 2, '.', ',').'</td>
                        </tr>';
                      }
                      $tabla.='
                      </tbody>
                    </table>
                </div>
            </div>



          <!-- Modal para Formulario POA -->
          <div class="modal fade" id="modalAdicionarPoa" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
              <div class="modal-content" style="border-radius: 15px; border: none; min-height: 80vh;">
                
                <div class="modal-header border-bottom py-3">
                  <h5 class="modal-title fw-bold text-primary fs-5">Unidades Organizacionales disponibles</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                  <!-- Formulario estructurado para ocupar el espacio largo -->
                  <form id="formPoa" class="row g-4">
                    <div id="listado_uo"></div>
                  </form>
                </div>

                <div class="modal-footer border-top-0 p-4">
                  <button type="button" class="btn btn-light-danger text-danger px-4" data-bs-dismiss="modal">Cancelar Operación</button>
                  <button type="button" class="btn btn-primary px-4">Finalizar y Guardar</button>
                </div>
              </div>
            </div>
          </div>
          <!-- END Modal para Formulario POA -->

          <div class="modal fade" id="modalVisorPdf" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
              <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
                
                <!-- Cabecera con Degradado y Badge -->
                <div class="modal-header d-flex align-items-center justify-content-between border-0 py-3" 
                     style="background: #004640; padding-left: 25px; padding-right: 25px;">
                  <div class="d-flex align-items-center">
                      <div>
                          <h5 class="modal-title fw-bolder text-white mb-0" style="letter-spacing: 0.5px;">POA GESTIÓN '.$this->session->get('configuracion')['ide'].'</h5>
                          <div class="d-flex align-items-center mt-1">
                              <span class="badge bg-white fw-bold text-uppercase px-3 py-1 shadow-sm" 
                                    id="nombre_regional_pdf" style="font-size: 0.75rem; border-radius: 50px; color: #004640;">
                                  Cargando Regional...
                              </span>
                          </div>
                      </div>
                  </div>
                  <div class="d-flex align-items-center gap-2">
                      <!-- BOTÓN REFRESH -->
                      <button type="button" class="btn btn-sm btn-light-success d-flex align-items-center justify-content-center p-2" 
                              id="btn_refresh_pdf" title="Actualizar Reporte" style="border-radius: 8px; background: rgba(255,255,255,0.1); border: none; color: white;">
                          <i class="ti ti-refresh fs-5"> <b>Actualizar Reporte</b></i>
                      </button>
                     <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.8;"></button>
                  </div>
                </div>

                <div class="modal-body p-0" style="background-color: #f8fafd; height: 78vh; position: relative;">
                  <!-- Pantalla de Loading Mejorada -->
                  <div id="loading_pdf" class="position-absolute top-50 start-50 translate-middle text-center w-100">
                      <div class="spinner-grow text-primary" role="status" style="width: 4rem; height: 4rem; opacity: 0.4;"></div>
                      <h5 class="mt-4 fw-bold text-dark-light">PROCESANDO REPORTE</h5>
                      <p class="text-muted small px-5">Estamos extrayendo la información del servidor para generar el archivo PDF.</p>
                  </div>

                  <iframe id="iframe_pdf" src="" width="100%" height="100%" style="border: none; display: none;"></iframe>
                </div>

                <!-- Pie de página sutil para balancear el diseño -->
                <div class="modal-footer bg-light border-0 py-2 justify-content-center">
                    <small class="text-muted fw-medium"><i class="ti ti-info-circle me-1"></i> Use los controles del visor para imprimir o descargar el reporte.</small>
                </div>
              </div>
            </div>
            </div>';

        return $tabla;
    }
}