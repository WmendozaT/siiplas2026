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
    $fecha = date('d/m/Y'); // Definir la variable fecha
    
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);

    /// instalar DOMPF composer require dompdf/dompdf
    // Usamos comillas DOBLES al principio para que las simples de CSS no rompan el código
    // Y concatenamos $fecha para que se muestre el valor real
        $html = "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
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
        </style>
        </head>
        <body>
            <!-- Estos elementos se repetirán en cada hoja automáticamente -->
            <header>
                <h3>Empresa de Ejemplo S.A.</h3>
                <p>Reporte Oficial de Responsables - Gestión 2026</p>
            </header>

            <footer>
                <p>Documento generado electrónicamente. Página <span class='pagenum'></span></p>
            </footer>

            <!-- El contenido principal va aquí -->
            <main>
                <table>
                    <thead>
                        <tr><th>ID</th><th>Nombre</th></tr>
                    </thead>
                    <tbody>
                        <!-- Si esta tabla tiene 100 filas, el header y footer se repetirán en cada hoja -->
                        <tr><td>...</td><td>...</td></tr>
                    </tbody>
                </table>
            </main>
        </body>
        </html>";

        $dompdf->loadHtml($html);
        $dompdf->setPaper('Letter', 'portrait');
        $dompdf->render();
        $dompdf->stream("reporte.pdf", ["Attachment" => false]);
    }

   

}


