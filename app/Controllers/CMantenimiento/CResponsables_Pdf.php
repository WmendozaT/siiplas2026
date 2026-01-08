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

    /// Reporte Lista de Reponsables POA
    public function Pdf_lista_responsables(){
    $miLib_resp = new Libreria_Responsable();
    $model_funcionario = new Model_funcionarios();
    $responsables=$model_funcionario->obtenerFuncionariosActivos();

    $fecha = date('d/m/Y'); // Definir la variable fecha
    
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);


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
                <h3>'.$this->session->get('configuracion')['conf_nombre_entidad'].'</h3>
                <p>Reporte Oficial de Responsables POA - Gestión '.$this->session->get('configuracion')['conf_gestion'].'</p>
            </header>

            <footer>
                <p>'.$this->session->get('configuracion')['conf_version'].'. Página <span class="pagenum"></span></p>
            </footer>

            <!-- El contenido principal va aquí -->
            <main>
                <table class="table-report">
                    <thead>
                        <tr>
                          <th width="1%" class="text-center">#</th>
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
                        $nro++;
                        $html.='
                        <tr>
                          <td style="aling:center;">'.$nro.'</td>
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
                      $html.='
                      </tbody>
                    </table>
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

   ////

}


