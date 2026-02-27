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
                    <!-- Botón Importar con estilo Spike -->
                    <button type="button" class="btn btn-primary btn-sm ms-2"  data-bs-toggle="modal" data-bs-target="#modalExcel">
                      <span class="d-none d-sm-block">Importar Excel</span>
                    </button>

                    <!-- Botón Vaciar con estilo Outlined Danger -->
                    <button type="button" id="btnVaciarTabla" class="btn btn-danger btn-sm ms-2" >
                      <span class="d-none d-sm-block">Eliminar Registro</span>
                    </button>

                    <!-- Botón Exportar -->
                      <a href="'.base_url('mnt/exportar_ppto_asignado').'" 
                         id="btnExportar"
                         class="btn btn-outline-primary btn-sm ms-2" 
                         data-bs-toggle="tooltip" 
                         title="Exportar Listado">
                          <span id="btnIcon">
                              <img src="'.base_url().'Img/Iconos/page_excel.png" alt="Excel">
                          </span>
                          <span id="btnText">Exportar.xls</span>
                      </a>

                  </div>
                </div>
                <div class="table-responsive pb-4">

                    <table id="all-student" class="table table-striped table-bordered border text-nowrap align-middle" style="font-size:10.5px;">
                      <thead>
                        <tr>
                            <th >#</th>
                            <th >ESTADO</th>
                            <th >TIPO DE GASTO</th>
                            <th ></th>
                            <th ></th>
                            <th ></th>
                            <th >DISTRITAL</th>
                            <th >APERTURA PROGRAMATICA</th>
                            <th >CODIGO SISIN</th>
                            <th >GASTO CORRIENTE / INVERSIÓN</th>
                            <th >GASTO CORRIENTE / INVERSIÓN</th>
                            <th >GASTO CORRIENTE / INVERSIÓN</th>
                            <th >GASTO CORRIENTE / INVERSIÓN</th>
                            <th >GASTO CORRIENTE / INVERSIÓN</th>
                            <th >GASTO CORRIENTE / INVERSIÓN</th>
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
                        <tr>
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
                          <td style="white-space: normal; min-width: 200px;">'.$detalle.'</td>
                          <td style="white-space: normal; min-width: 200px;">'.$detalle.'</td>
                          <td style="white-space: normal; min-width: 200px;">'.$detalle.'</td>
                          <td style="white-space: normal; min-width: 200px;">'.$detalle.'</td>
                          <td style="white-space: normal; min-width: 200px;">'.$detalle.'</td>

                          <td>'.number_format($row['ppto_asignado'], 2, '.', ',').'</td>
                        </tr>';
                      }
                      $tabla.='
                      </tbody>
                    </table>
                </div>
            </div>



        <div class="modal fade" id="modalExcel" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalExcelLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title" id="modalExcelLabel">Importar Asignación de PPTO-POA '.$this->session->get('configuracion')['ide'].' desde Excel</h6>
                  <!-- Quitar el botón X o deshabilitarlo en el submit es opcional -->
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="btnCloseModal"></button>
                </div>
                <form id="formImportarExcel" action="'.base_url().'mnt/valida_ppto" method="POST" enctype="multipart/form-data">
                  <div class="modal-body">
                    <div class="mb-3">
                      <label for="archivo_excel" class="form-label">Seleccione el archivo (.xlsx, .xls)</label>
                      <input class="form-control" type="file" id="archivo_excel" name="archivo_excel" accept=".xlsx, .xls">
                      <div id="error-mensaje" class="text-danger mt-2" style="display:none; font-size: 12px;"></div>
                    </div>
                    <div class="alert alert-info">
                      <small>Asegúrese de que el formato coincida con las siguientes columnas: <br>DA|UE|PROG|PROY|ACT|PARTIDA|MONTO</small>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" id="btnCancelModal">Cancelar</button>
                    <button type="submit" id="btnImportar" class="btn btn-primary">
                      <span id="spinnerLoading" class="spinner-border spinner-border-sm me-2" role="status" style="display: none;"></span>
                      <span id="btnText">Procesar e Importar</span>
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>


          <div class="modal fade" id="modalPartidas" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <!-- ID añadido: modalHeader -->
                <div class="modal-header bg-primary text-white" id="modalHeader" style="background-color: #094d48 !important;">
                  <h5 class="modal-title text-white" id="tituloModal">Partidas Asignadas</h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <!-- ID añadido: modalSubtitulo -->
                  <h6 id="subtituloUnidad" class="fw-bold mb-3" style="color: #094d4d;"></h6>
                  <div id="contenidoPartidas">
                    <div class="text-center p-4">
                      <div class="spinner-border text-primary" role="status"></div>
                      <p>Cargando partidas...</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>';

        return $tabla;
    }
}