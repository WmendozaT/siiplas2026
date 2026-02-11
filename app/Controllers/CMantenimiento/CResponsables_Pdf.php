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


/// Reporte Lista de Reponsables POA NORMAL
    public function Pdf_lista_responsables(){
    $tp_rep = 0; // O recuperar del Post
    $fecha = date('d/m/Y'); 
    
    ini_set('memory_limit', '1024M');
    $options = new \Dompdf\Options();
    $options->set('isRemoteEnabled', true);
    $options->set('chroot', FCPATH);
    
    $dompdf = new \Dompdf\Dompdf($options);

    //$path = FCPATH . $this->session->get('configuracion')['conf_img']; 
   // $path = FCPATH . 'Img/login/logo_CNS_header.png'; 
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





    /// Reporte Lista de Reponsables POA CON AJAX
    public function Pdf_lista_responsables2(){
    
    $tp_rep = $this->request->getPost('tp_rep'); /// tipo de Reporte
    $fecha = date('d/m/Y'); // Definir la variable fecha
    
        ini_set('memory_limit', '1024M'); // Aumenta a 1GB de RAM temporalmente
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('chroot', FCPATH);
        
        $dompdf = new \Dompdf\Dompdf($options);


        $html='';
        $html.='
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
        <style>
            /* 1. Definir márgenes de la página */
            @page {
                margin: 110px 50px 80px 50px; /* Margen: superior, derecho, inferior, izquierdo */
            }

            /* 2. Encabezado Fijo */
            header {
                position: fixed;
                top: -90px; /* Se coloca dentro del margen superior */
                left: 0px;
                right: 0px;
                height: 80px;
                text-align: center;
                border-bottom: 1px solid #ccc;
            }

            /* 3. Pie de Página Fijo */
            footer {
                position: fixed;
                bottom: -60px; /* Se coloca dentro del margen inferior */
                left: 0px;
                right: 0px;
                height: 50px;
                text-align: center;
                font-size: 10px;
                color: #777;
                border-top: 1px solid #ccc;
            }

            /* 4. Numeración de páginas (Script especial para Dompdf) */
            .pagenum:before {
                content: counter(page);
            }

            /* --- ESTILOS ESPECÍFICOS PARA LA TABLA --- */
            .table-report {
                width: 100%;
                border-collapse: collapse; /* Quita el espacio entre bordes */
                margin-top: 10px;
                font-family: sans-serif;
                font-size: 8.5px;
            }

            .table-report th {
                background-color: #004640; /* Color institucional */
                color: white;
                padding: 8px;
                text-align: left;
                border: 1px solid #ddd;
                text-transform: uppercase;
            }

            .table-report td {
                padding: 6px;
                border: 1px solid #ddd;
                vertical-align: middle;
            }

            /* Cebra: Filas alternas de color gris claro */
            .table-report tr:nth-child(even) {
                background-color: #f9f9f9;
            }

            .text-center { text-align: center; }
            /* Evita que una fila se corte a la mitad entre dos páginas */
            .table-report tr { page-break-inside: avoid; } 
        </style>
        </head>
        <body>
            <!-- Estos elementos se repetirán en cada hoja automáticamente -->
            <header>
                <h3>'.$this->session->get('configuracion')['conf_nombre_entidad'].'</h3>';
                if($tp_rep==0){
                    $html.='
                    <p>Reporte Oficial de Responsables POA - Gestión '.$this->session->get('configuracion')['conf_gestion'].'</p>';
                }
                else{
                     $html.='
                    <p>Reporte Oficial de Responsables para el Seguimiento POA - Gestión '.$this->session->get('configuracion')['conf_gestion'].'</p>';
                }
                $html.='
            </header>

            <footer>
                <p>'.$this->session->get('configuracion')['conf_version'].'. Página <span class="pagenum"></span></p>
            </footer>

            <!-- El contenido principal va aquí -->
            <main>
                '.$this->responsables_poa($tp_rep).'
            </main>
        </body>
        </html>';

        $dompdf->loadHtml($html);
        $dompdf->setPaper('Letter', 'portrait');
        $dompdf->render();

        // 1. Capturamos el contenido binario del PDF generado
        $pdf_content = $dompdf->output();
       

        // 2. Convertimos a Base64
        $base64 = base64_encode($pdf_content);

        // 3. Retornamos como JSON (ideal para recibirlo con AJAX hoy 2026)
        return $this->response->setJSON([
            'status' => 'success',
            'nombre' => 'Responsables_POA.pdf',
            'pdf'    => 'data:application/pdf;base64,' . $base64
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
//$urlImagen = base_url($row['imagen_perfil']);
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





     /// Reporte Lista de Reponsables POA para firmar Digitalmente
    public function Pdf_lista_responsables_para_firmar(){
    $model_funcionario = new Model_funcionarios();
    $responsables = $model_funcionario->obtenerFuncionariosActivos();
    $config = $this->session->get('configuracion');
    
    $options = new Options();
    $options->set('isRemoteEnabled', true); // Cambiar a false si no usas imágenes externas
    $options->set('defaultFont', 'Helvetica');
    $dompdf = new Dompdf($options);

    $html = '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <style>
            @page { margin: 110px 50px 80px 50px; }
            header { position: fixed; top: -90px; left: 0px; right: 0px; height: 80px; text-align: center; border-bottom: 1px solid #000; }
            footer { position: fixed; bottom: -60px; left: 0px; right: 0px; height: 50px; text-align: center; font-size: 10px; border-top: 1px solid #ccc; }
            .pagenum:before { content: counter(page); }
            
            .table-report { width: 100%; border-collapse: collapse; font-family: sans-serif; font-size: 8.5px; }
            .table-report th { background-color: #004640; color: white; padding: 8px; border: 1px solid #ddd; }
            .table-report td { padding: 6px; border: 1px solid #ddd; vertical-align: middle; }
            .text-center { text-align: center; }
            tr { page-break-inside: avoid; }
        </style>
    </head>
    <body>
            <header>
                <h3>'.$this->session->get('configuracion')['conf_nombre_entidad'].'</h3>
                <p>Reporte Oficial de Responsables POA - Gestión '.$this->session->get('configuracion')['conf_gestion'].'</p>
            </header>

        <footer>
            <span>'.$this->session->get('configuracion')['conf_version'].' - Generado el '.date('d/m/Y H:i').' - Página </span><span class="pagenum"></span>
        </footer>

        <main>
            <table class="table-report">
                <thead>
                    <tr>
                      <th width="3%">#</th>
                      <th>RESPONSABLE POA</th>
                      <th width="10%">CI</th>
                      <th width="15%">CARGO</th>
                      <th width="10%">USUARIO</th>
                      <th width="10%">DISTRITAL</th>
                    </tr>
                </thead>
                <tbody>';
                foreach($responsables as $nro => $row){ 
                    $html.='
                    <tr>
                      <td class="text-center">'.($nro + 1).'</td>
                      <td>'.$row['fun_nombre'].' '.$row['fun_paterno'].' '.$row['fun_materno'].'</td>
                      <td class="text-center">'.$row['fun_ci'].'</td>
                      <td>'.$row['fun_cargo'].'</td>
                      <td class="text-center">'.$row['fun_usuario'].'</td>
                      <td class="text-center">'.$row['dist_distrital'].'</td>
                    </tr>';
                }
                $html.='</tbody>
            </table>
            
            <!-- Espacio opcional para firma visual si fuera necesario -->
            <div style="margin-top: 30px; text-align: center;">
                <p style="font-size: 9px; color: #555;">Documento generado electrónicamente para fines de firma digital.</p>
            </div>
        </main>
    </body>
    </html>';

    $dompdf->loadHtml($html);
    $dompdf->setPaper('Letter', 'portrait');
    $dompdf->render();

    // Importante: El output del PDF para firma debe ser limpio
    $pdf_output = $dompdf->output();

    return $this->response->setJSON([
        'status' => 'success',
        'pdf_sin_firmar' => base64_encode($pdf_output),
        'nombre_archivo' => 'Reporte_Responsables_'.date('YmdHis').'.pdf'
    ]);
    }
}


