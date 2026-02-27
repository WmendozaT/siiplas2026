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


class Libreria_ReportesPoa{
    protected $session;
    protected $db;

    public function __construct() {
        // Inicializamos el servicio de sesión
        $this->session = \Config\Services::session();
        // Si necesitas base de datos también:
        $this->db = \Config\Database::connect();
    }


    ///// POA -> Lista Unidades en el POA
    public function Pdf1_Lista_unidades_disponibles_poa_x_regional($lista,$regional){
      $html='';
      //$logoPath = base_url('Img/login/logo_CNS_header.png'); // Asegúrate que la ruta sea accesible
      $logoPath = FCPATH . 'Img/login/logo_CNS_header.png';
      $fechaActual = date('d/m/Y');
      $horaActual = date('H:i:s');

      $html = '
      <!DOCTYPE html>
      <html lang="es">
      <head>
          <meta charset="UTF-8">
          <style>
              /* 1. REGULAR DISTANCIA: Aumentamos margin-top a 140px para dar aire al header */
              @page { margin: 140px 30px 60px 30px; }

              /* 2. HEADER: Ajustamos top para que quepa la imagen */
              header { 
                  position: fixed; 
                  top: -100px; 
                  left: 0px; 
                  right: 0px; 
                  height: 90px; 
                  border-bottom: 2px solid #004640; 
                  font-family: sans-serif; 
              }

              footer { 
                  position: fixed; 
                  bottom: -40px; 
                  left: 0px; 
                  right: 0px; 
                  height: 30px; 
                  text-align: center; 
                  font-size: 9px; 
                  color: #555; 
                  border-top: 1px solid #ccc; 
                  font-family: sans-serif; 
              }

              /* Estilos de tabla y utilidades */
              .table-header { width: 100%; border: none; border-collapse: collapse; }
              .table-report { width: 100%; border-collapse: collapse; margin-top: 10px; font-family: sans-serif; }
              .table-report th { background-color: #004640; color: white; padding: 5px; border: 1px solid #000; font-size: 8px; text-transform: uppercase; }
              .table-report td { padding: 4px; border: 1px solid #ccc; font-size: 8px; vertical-align: middle; }
              
              .text-center { text-align: center; }
              .text-right { text-align: right; }
              .bold { font-weight: bold; }
              .pagenum:before { content: counter(page); }
          </style>
      </head>
      <body>
          <header>
              <table class="table-header">
                  <tr>
                      <!-- LOGO IZQUIERDA -->
                      <td style="width: 20%; text-align: left;">
                          <img src="'.$logoPath.'" style="height: 60px;">
                      </td>
                      <!-- TITULOS CENTRO -->
                      <td style="width: 60%; text-align: center;">
                          <span style="font-size: 27px;" class="bold">'.($this->session->get('configuracion')['conf_nombre_entidad']).'</span><br>
                          <span style="font-size: 17px;" class="bold">'.($this->session->get('configuracion')['conf_unidad_resp']).'</span><br>
                          <span style="font-size: 10px;">UNIDADES ORGANIZACIONALES : REGIONAL '.strtoupper($regional['dep_departamento']).' - GESTIÓN '.($this->session->get('configuracion')['conf_gestion']).'</span>
                      </td>
                      <!-- FECHA/HORA DERECHA -->
                      <td style="width: 20%; text-align: right; font-size: 8px; color: #333;">
                          <span class="bold">Fecha:</span> '.$fechaActual.'<br>
                          <span class="bold">Usuario:</span> 
                      </td>
                  </tr>
              </table>

          </header>

          <footer>
              <table style="width: 100%">
                  <tr>
                      <td style="text-align: left; width: 33%">'.($this->session->get('configuracion')['conf_version']).'</td>
                      <td style="text-align: center; width: 33%">Plataforma de Programación POA</td>
                      <td style="text-align: right; width: 33%">Página <span class="pagenum"></span></td>
                  </tr>
              </table>
          </footer>

          <main>

              <table class="table-report">
                  <thead>
                      <tr>
                          <th>#</th>
                          <th>Estado</th>
                          <th>Tipo Gasto</th>
                          <th>Reg. / Dist.</th>
                          <th>D.A.</th>
                          <th>U.E.</th>
                          <th>Apertura</th>
                          <th>Sisin</th>
                          <th>Descripción Detallada (Gasto / Inversión)</th>
                          <th>Ppto. Asignado</th>
                      </tr>
                  </thead>
                  <tbody>';
                  $nro = 0;
                  foreach($lista as $row){
                      $nro++;
                      $detalle = ($row['tp_id'] == 1) ? $row['proy_nombre'] : $row['actividad'].' '.$row['abrev'];
                      $html .= '
                      <tr>
                          <td class="text-center">'.$nro.'</td>
                          <td class="text-center">'.$row['estado_poa'].'</td>
                          <td>'.$row['tipo_gasto_nombre'].'</td>
                          <td>'.strtoupper($row['dist_distrital']).'</td>
                          <td class="text-center">'.$row['da'].'</td>
                          <td class="text-center">'.$row['ue'].'</td>
                          <td class="text-center">'.$row['prog'].' '.$row['proy'].' '.$row['act'].'</td>
                          <td class="text-center">'.$row['proy_sisin'].'</td>
                          <td style="width: 220px;">'.$detalle.'</td>
                          <td class="text-right bold">'.number_format($row['ppto_asignado'], 2, ".", ",").'</td>
                      </tr>';
                  }
                  $html .= '
                  </tbody>
              </table>
          </main>
      </body>
      </html>';

      return $html;
    }
    






}