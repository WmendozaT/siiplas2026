<?php
namespace App\Controllers\CMantenimiento;
use App\Controllers\BaseController; 
// Importa las clases necesarias para initController
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\Model_Mantenimiento\Model_funcionarios;
use App\Models\Model_Mantenimiento\Model_regional;
use App\Libraries\Libreria_Responsable;

use Dompdf\Dompdf;
use Dompdf\Options;
use setasign\Fpdi\TcpdfFpdi; // Importante para la firma

class CResponsables_Pdf extends BaseController{
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


    public function Pdf_lista_responsables(){
    $tp_rep = 0; // O recuperar del Post
    $fecha = date('d/m/Y'); 
    
    ini_set('memory_limit', '1024M');
    $options = new \Dompdf\Options();
    $options->set('isRemoteEnabled', true);
    $options->set('chroot', FCPATH);
    
    $dompdf = new \Dompdf\Dompdf($options);
    $path = FCPATH . $this->session->get('configuracion')['conf_img']; 
        $base64 = '';

        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

$html='';
$html = '
<!DOCTYPE html>
<html>
<head>
        <style>
            @page { margin: 110px 40px 70px 40px; }
            header {
                position: fixed; top: -95px; left: 0px; right: 0px; height: 85px;
                border-bottom: 2px solid #004640; display: block; width: 100%;
            }
            .header-table { width: 100%; border-collapse: collapse; }
            .header-table td { border: none; vertical-align: middle; }
            .header-title { text-align: center; color: #004640; }
            .header-title h2 { margin: 0; font-size: 20px; text-transform: uppercase; }
            .header-title p { margin: 2px 0 0 0; font-size: 12px; color: #000; }
            
            footer {
                position: fixed; bottom: -50px; left: 0px; right: 0px; height: 40px;
                width: 100%; font-size: 9px; color: #666; border-top: 1px solid #ddd; text-align: center;
            }
            .pagenum:before { content: counter(page); }

            .table-report { width: 100%; border-collapse: collapse; margin-top: 10px; font-family: sans-serif; font-size: 8.5px; }
            .table-report th { background-color: #004640; color: white; padding: 8px; text-align: left; border: 1px solid #ddd; text-transform: uppercase; }
            .table-report td { padding: 6px; border: 1px solid #ddd; vertical-align: middle; }
            .table-report tr:nth-child(even) { background-color: #f9f9f9; }
            .text-center { text-align: center; }
            .table-report tr { page-break-inside: avoid; } 
        </style>
</head>
<body>

            <header>
            <table class="header-table">
                <tr>
                    <!-- Espacio para el logo izquierdo -->
                    <td style="width: 20%; text-align: left;">
                        <img src="' . $base64 . '" style="height: 70px; width: auto;">
                    </td>
                    
                    <!-- Texto central -->
                    <td style="width: 60%;" class="header-title">
                        <h2>'.$this->session->get('configuracion')['conf_nombre_entidad'].'</h2>
                        <p>GESTIÓN '.$this->session->get('configuracion')['conf_gestion'].'</p>
                        <p><strong>REPORTE OFICIAL DE RESPONSABLES POAssss</strong></p>
                    </td>
                    
                    <!-- Espacio para fecha o código de control (derecha) -->
                    <td style="width: 20%; text-align: right; font-size: 8px; color: #999;">
                        Fecha: '.date('d/m/Y').'<br>
                        Hora: '.date('H:i').'
                    </td>
                </tr>
            </table>
            </header>

    <footer>
                <p>'.$this->session->get('configuracion')['conf_version'].'. Página <span class="pagenum"></span></p>
            </footer>

    <main>
   
        '.$this->responsables_poa($tp_rep).'
    </main>
</body>
</html>';
    // 3. Renderizar PDF
    $dompdf->loadHtml($html);
    $dompdf->setPaper('Letter', 'portrait');
    $dompdf->render();

    // 4. Guardar para Jacobitus
    $pdf_binario = $dompdf->output();
    
    // Recomendación: Usar una carpeta específica en writable para evitar problemas de permisos
    $carpetaDir = FCPATH . 'uploads/reportes_firmas/';
    if (!is_dir($carpetaDir)) {
        mkdir($carpetaDir, 0777, true);
    }
    
    $nombreArchivo = 'reporte_poa.pdf'; // O usar un identificador único
    $ruta = $carpetaDir . $nombreArchivo;
    file_put_contents($ruta, $pdf_binario);

if (ob_get_length()) ob_clean();

return $this->response
            ->setStatusCode(200)
            ->setContentType('application/json')
            ->setJSON([
                'status' => 'success',
                'pdf'    => 'data:application/pdf;base64,' . base64_encode($pdf_binario),
                'ruta'   => $ruta 
            ]);
    }




    /// Reporte Lista de Reponsables POA para firmar Digitalmente
    public function responsables_poa($tp){
        $miLib_resp = new Libreria_Responsable();
        $model_funcionario = new Model_funcionarios();
        $tabla='';
        if($tp==0){ //// Responsables POA
            $responsables=$model_funcionario->obtenerFuncionariosActivos(); //// responsables poa
            $tabla.='<table class="table-report">
                    <thead>
                        <tr>
                          <th width="1%" class="text-center">#</th>
                          <th width="5%" class="text-center"></th>
                          <th width="15%" class="text-center">REPONSABLE POA</th>
                          <th width="5%" class="text-center">CI</th>
                          <th width="10%" class="text-center">CARGO</th>
                          <th width="10%" class="text-center">TELEFONO</th>
                          <th width="10%" class="text-center">CORREO</th>
                          <th width="8%" class="text-center">USUARIO</th>
                          <th width="10%" class="text-center">TIPO ADM.</th>
                          <th width="10%" class="text-center">DISTRITAL</th>
                        </tr>
                      </thead>
                      <tbody>';
                      $nro=0;
                      foreach($responsables as $row){ 
                        $urlImagen = FCPATH . $row['imagen_perfil']; 
                        $nro++;
                        $tabla.='
                        <tr>
                          <td style="aling:center;">'.$nro.'</td>
                          <td style="text-align:center;">
                             <img src="'.$urlImagen.'" width="35" height="35">
                          </td>
                          <td>'.$row['fun_nombre'].' '.$row['fun_paterno'].' '.$row['fun_materno'].'</td>
                          <td>'.$row['fun_ci'].'</td>
                          <td>'.$row['fun_cargo'].'</td>
                          <td>'.$row['fun_telefono'].'</td>
                          <td></td>
                          <td>'.$row['fun_usuario'].'</td>
                          <td>'.$row['adm'].'</td>
                          <td>'.$row['dist_distrital'].'</td>
                        </tr>';
                      }
                      $tabla.='
                      </tbody>
                    </table>';
        }
        else{ //// Responsables Seguimiento POA
            $responsables=$model_funcionario->obtenerFuncionariosActivos_seguimientoPOA();
             $tabla.='<table class="table-report">
                    <thead>
                        <tr>
                          <th width="1%" class="text-center">#</th>
                          <th width="15%" class="text-center">UNIDAD ORGANIZACIONAL</th>
                          <th width="10%" class="text-center">UNIDAD RESPONSABLE</th>
                          <th width="10%" class="text-center">ADMINISTRACIÓN</th>
                          <th width="10%" class="text-center">USUARIO</th>
                          <th width="10%" class="text-center">CREDENCIAL</th>
                        </tr>
                      </thead>
                      <tbody>';
                      $nro=0;
                      foreach($responsables as $row){ 
                        $info = password_get_info($row['fun_password']);
                          if($info['algoName']=='unknown'){
                            $has_title='<div style="color:red"><b>No Hasheado</b></div>';
                          }
                          else{
                            $get_pss=$model_funcionario->get_pwd($row['id']);
                            if(count($get_pss)!=0) {
                                $has_title=$get_pss[0]['fun_apassword']; 
                            }
                            else{
                             $has_title='<div style="color:red"><b>sin Credencial</b></div>';
                            }
                          }
                        $nro++;
                        $tabla.='
                        <tr>
                          <td style="aling:center;">'.$nro.'</td>
                          <td>'.$row['aper_programa'].' '.$row['aper_proyecto'].' '.$row['aper_actividad'].' - '.$row['aper_descripcion'].'</td>
                          <td>'.$row['tipo_subactividad'].' '.$row['serv_descripcion'].'</td>
                          <td>'.strtoupper($row['dist_distrital']).'</td>
                          <td>'.$row['fun_usuario'].'</td>
                          <td>'.$has_title.'</td>
                        </tr>';
                      }
                      $tabla.='
                      </tbody>
                    </table>';
        }

        return $tabla;
    }


}


