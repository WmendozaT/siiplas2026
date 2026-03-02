<?php
namespace App\Controllers\CProgramacion;
use App\Controllers\BaseController; 
// Importa las clases necesarias para initController
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\Index\IndexModel;
use App\Models\Model_Mantenimiento\Model_funcionarios;
use App\Models\Model_Mantenimiento\Model_regional;
use App\Models\Model_Mantenimiento\Model_configuracion;
use App\Models\Model_Poa\Model_formulario5;
use App\Models\Model_Ppto\Model_PptoAsig;
use App\Libraries\Libreria_ProgramacionPoa;
use App\Libraries\Libreria_ReportesPoa;
//use App\Libraries\Libreria_Responsable;
//use App\Libraries\Libreria_EstructuraOrganizacional;
use App\Libraries\Libreria_Index;

use Dompdf\Dompdf;
use Dompdf\Options;


use PhpOffice\PhpSpreadsheet\IOFactory;


class CProgramacionPoa extends BaseController{
    protected $IndexModel;
    protected $Model_funcionarios;
    protected $Model_regional;
    protected $Model_configuracion;

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
        $this->session->get('funcionario');
        $this->session->get('view_modulos'); 
        $this->session->get('view_modulos_sidebar'); 
    }

    /// menu AnteProyectoPOA
    public function lista_poa($tp_id){
        $session = session(); // Necesario para romper la sesión
        $miLib_ProgPoa = new Libreria_ProgramacionPoa();
        $data['formulario']=$miLib_ProgPoa->Lista_ProgramacionPoa($tp_id);

        return view('View_programacion/view_programacion_poa',$data);
    }

    /// Obtiene el listado de las unidades Organizacionales de la regional Seleccionado
    public function obtener_unidades_organizacionales_disponibles_x_regional() {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $dep_id = $this->request->getPost('id');
        $tp_id = $this->request->getPost('tp_id');
        $model_regional = new Model_regional();
        $unidades_disponibles=$model_regional->lista_unidades_disponibles_addpoa($dep_id,$this->session->get('configuracion')['ide']);
        $html = 'Trabajando en la adicion de Unidades Organizacionales al POA '.$tp_id;

        
        return $this->response->setJSON([
            'status' => 'success',
            'datos'  => $html,
            'token'  => csrf_hash()
        ]);
    }

    
    //// Lista de Unidades aperturados en el POA por Regional
    public function pdf_lista_poa_uorganizacional_regional($dep_id,$tp_id) {
    try {
        // 1. VALIDACIÓN PREVIA (Evita que el JS reciba basura o errores 500 silenciosos)
        if ($dep_id === null || $dep_id === '') {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'ID Regional no proporcionado']);
        }

        $miLib_Pdf_repPoa = new Libreria_ReportesPoa();
        $model_regional = new Model_regional();
        $regional=$model_regional->get_regional($dep_id);
        $unidades_disponibles_x_regional=$model_regional->lista_programacion_poa_x_regional($dep_id,$tp_id);

        // 2. CONFIGURACIÓN DOMPDF
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'sans-serif');
        $dompdf = new \Dompdf\Dompdf($options);
        
        $html = $miLib_Pdf_repPoa->Pdf1_Lista_unidades_disponibles_poa_x_regional($unidades_disponibles_x_regional,$regional,$tp_id); /// rep pdf1

        $dompdf->loadHtml($html);
        $dompdf->setPaper('Letter', 'portrait');
        $dompdf->render();

        // 3. RESPUESTA COMPATIBLE CON EL IFRAME Y FETCH
        $pdf_content = $dompdf->output();

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="reporte_regional_'.$dep_id.'.pdf"')
            ->setHeader('Cache-Control', 'no-cache, must-revalidate')
            ->setBody($pdf_content);

        } catch (\Exception $e) {
            // Si algo falla, enviamos un 500 que el 'catch' de tu JS capturará
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
      




    //// Exportar Lista de Unidades aperturados en el POA por Regional
    public function exportar_poa_excel($dep_id,$tp_id) {
    $model_regional = new Model_regional();
    $regional=$model_regional->get_regional($dep_id);
    $unidades_disponibles_x_regional=$model_regional->lista_programacion_poa_x_regional($dep_id,$tp_id);

    $titulo='POA '.$regional['dep_departamento'].': GASTO CORRIENTE - '.$this->session->get('configuracion')['ide'];
    if($tp_id==1){
       $titulo='POA '.$regional['dep_departamento'].': PROYECTO DE INVERSION - '.$this->session->get('configuracion')['ide'];
    }


    $filename = "Listado_Poa_".$regional['dep_departamento']."" . date('Ymd_His') . ".xls";

    // 1. PRIMERO: Configurar la cookie ANTES de enviar cualquier contenido
    // Esto es lo que detectará tu JavaScript para quitar el loading
    setcookie("excel_status", "terminado", [
        'expires' => time() + 30, 
        'path' => '/',
        'samesite' => 'Lax'
    ]);

    // 2. Cabeceras del archivo
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=$filename");
    header("Pragma: no-cache");
    header("Expires: 0");

    // 3. BOM para caracteres especiales
    echo "\xEF\xBB\xBF";

    // 4. Construcción de la tabla (HTML/CSS)
    echo '<table border="1">';
    echo '<tr>
            <th colspan="7" style="background-color: #1F4E78; color: #FFFFFF; font-size: 16pt; text-align: center;">
               '.$titulo.'
            </th>
          </tr>';

            echo '
            <tr style="background-color: #D9D9D9; font-weight: bold; text-align: center;">
                <th style="width: 50px;">#</th>
                <th style="width: 100px;">ESTADO</th>
                <th style="width: 200px;">DISTRITAL</th>
                <th style="width: 150px;">APERTURA PROGRAMATICA</th>';
                if($tp_id==1){
                    echo '<th style="width: 350px;">CODIGO SISIN</th>
                    <th style="width: 350px;">PROYECTO DE INVERSION</th>';
                }
                else{
                    echo '<th style="width: 250px;">TIPO</th>
                    <th style="width: 350px;">UNIDAD ORGANIZACIONAL</th>';
                }
                echo '
                
                <th style="width: 150px;">PRESUPUESTO ASIGNADO</th>
            </tr>';
        
        $nro = 0;
        foreach ($unidades_disponibles_x_regional as $row) {
            $nro++;
            $colorFila = ($nro % 2 == 0) ? '#F2F2F2' : '#FFFFFF';
            echo '<tr style="background-color: '.$colorFila.';">';
            echo '  <td style="text-align: center; border: 1px solid #CCC;">' . $nro . '</td>';
            echo '  <td style="border: 1px solid #CCC; padding: 5px;">'.$row['estado_poa'].'</td>';
            echo '  <td style="border: 1px solid #CCC; padding: 5px;">' . $row['dist_distrital'] . '</td>';
            echo '  <td style="border: 1px solid #CCC; text-align: center;">'.$row['prog'].' '.$row['proy'].' '.$row['act'].'</td>';
            if($tp_id==1){
                echo '  <td style="border: 1px solid #CCC; text-align: center;">'.$row['proy_sisin'].'</td>';
                echo '  <td style="border: 1px solid #CCC;">'.$row['proy_nombre'].'</td>';
            }
            else{
                echo '  <td style="border: 1px solid #CCC;">'.$row['tipo'].'</td>';
                echo '  <td style="border: 1px solid #CCC;">'.$row['actividad'].' '.$row['abrev'].'</td>';
            }
            echo '  <td style="border: 1px solid #CCC;" text-align: lefth;>'.round($row['ppto_asignado'],2).'</td>';
            echo '</tr>';
        }
    echo '</table>';

    // 5. UN SOLO EXIT AL FINAL
    exit; 
    } 
}


