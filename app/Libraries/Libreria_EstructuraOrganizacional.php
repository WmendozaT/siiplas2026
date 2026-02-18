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


class Libreria_EstructuraOrganizacional{
    protected $session;
    protected $db;

    public function __construct() {
        // Inicializamos el servicio de sesión
        $this->session = \Config\Services::session();
        // Si necesitas base de datos también:
        $this->db = \Config\Database::connect();
    }

    
   /// Reporte PDF Lista Unidades para firmar Digitalmente -> Componente Estructura Cns
    public function listado_uorganizacional_rep($lista){
        $tabla='';
        $tabla.='<table class="table-report">
                    <thead>
                        <tr>
                          <th width="1%" class="text-center">#</th>
                          <th width="15%" class="text-center">DISTRITAL</th>
                          <th width="10%" class="text-center">DA</th>
                          <th width="10%" class="text-center">UE</th>
                          <th width="10%" class="text-center">COD.</th>
                          <th width="30%" class="text-center">UNIDAD ORGANIZACIONAL</th>
                        </tr>
                      </thead>
                      <tbody>';
                      $nro=0;
                      foreach($lista as $row){ 
                        $nro++;
                        $tabla.='
                        <tr>
                          <td style="aling:center;">'.$nro.'</td>
                          <td>'.strtoupper($row['dist_distrital']).'</td>
                          <td>'.$row['da'].'</td>
                          <td>'.$row['ue'].'</td>
                          <td>'.$row['act_cod'].'</td>
                          <td>'.$row['tipo'].' '.$row['act_descripcion'].' '.$row['abrev'].'</td>
                        </tr>';
                      }
                      $tabla.='
                      </tbody>
                    </table>';

        return $tabla;
    }



    /// Lista POA para asignacion del Presupuesto
    public function Lista_poa_para_asignacion_presupuesto(){
        $model_regional = new Model_regional();
        $unidades_disponibles=$model_regional->lista_poa_gral();
        $tabla='';
        $tabla.='
        <div class="datatables">
            <!-- start Zero Configuration -->
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h4 class="card-title mb-0">Asignación Presupuestaria - Gestión '.$this->session->get('configuracion')['ide'].'</h4>
                    <button type="button" class="btn btn-success d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalExcel">
                        <i class="ti ti-file-spreadsheet fs-4 me-1"></i> Subir Excel
                    </button>
                </div>
                <div class="table-responsive pb-4">
                    <table id="all-student" class="table table-striped table-bordered border text-nowrap align-middle" style="font-size:10.5px;">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th></th>
                          <th>TIPO DE GASTO</th>
                          <th>DISTRITAL</th>
                          <th>APERTURA PROGRAMATICA</th>
                          <th>CODIGO SISIN</th>
                          <th>GASTO CORRIENTE / INVERSIÓN</th>
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
                          <td class="text-center"></td>
                          <td>'.$row['tipo_gasto_nombre'].'</td>
                          <td>'.$row['dist_distrital'].'</td>
                          <td>'.$row['prog'].' '.$row['proy'].' '.$row['act'].'</td>
                          <td>'.$row['proy_sisin'].'</td>
                          <td>'.$detalle.'</td>
                          
                        </tr>';
                      }
                      $tabla.='
                      </tbody>
                    </table>
                </div>
            </div>
        </div>


        <div class="modal fade" id="modalExcel" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalExcelLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="modalExcelLabel">Importar Asignación desde Excel</h5>
                    <!-- Quitar el botón X o deshabilitarlo en el submit es opcional -->
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="btnCloseModal"></button>
                  </div>
                  <form id="formImportarExcel" action="'.base_url().'tu_controlador/importar_excel" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                      <div class="mb-3">
                        <label for="archivo_excel" class="form-label">Seleccione el archivo (.xlsx, .xls)</label>
                        <input class="form-control" type="file" id="archivo_excel" name="archivo_excel" accept=".xlsx, .xls">
                        <div id="error-mensaje" class="text-danger mt-2" style="display:none; font-size: 12px;"></div>
                      </div>
                      <div class="alert alert-info">
                        <small>Asegúrese de que el formato coincida con las columnas de la base de datos.</small>
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
            </div>';

        return $tabla;
    }
}