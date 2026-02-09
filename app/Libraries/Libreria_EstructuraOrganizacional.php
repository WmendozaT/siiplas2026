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

    
   /// Reporte Lista Unidades para firmar Digitalmente
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
}