<?php
namespace App\Controllers\CMantenimiento;
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
use App\Libraries\Libreria_EstructuraOrganizacional;
use App\Libraries\Libreria_Index;

use Dompdf\Dompdf;
use Dompdf\Options;


use PhpOffice\PhpSpreadsheet\IOFactory;


class Casignar_presupuesto extends BaseController{
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

    /// menu Estructura Organizacional
    public function menu_lista_poa(){
        $session = session(); // Necesario para romper la sesión
        $miLib_EstrOrg = new Libreria_EstructuraOrganizacional();
        $data['formulario']=$miLib_EstrOrg->Lista_poa_para_asignacion_presupuesto(); /// listado del poa general con el ppto asignado
        return view('View_mantenimiento/View_pptoAsignado/view_asignacion_presupuestaria',$data);
    }


    //// Valida migracion del archivo excel
    public function valida_migracion_ppto() {
        $archivo = $this->request->getFile('archivo_excel');
        $model_regional = new Model_regional();
        $model_poaf5 = new Model_formulario5();

        if (!$archivo || !$archivo->isValid()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Archivo no válido.']);
        }

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo->getTempName());
            $hojaActual   = $spreadsheet->getActiveSheet();
            
            // Validación de columnas
            $ultimaColumnaLetra = $hojaActual->getHighestColumn();
            $totalColumnas = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($ultimaColumnaLetra);
            if ($totalColumnas !== 7) {
                return $this->response->setJSON([
                    'status'  => 'error', 
                    'message' => "Se requieren 7 columnas, se detectaron $totalColumnas."
                ]);
            }

            $datos = $hojaActual->toArray(null, true, true, true);
            $datosParaInsertar = [];
            $errores = [];

            foreach ($datos as $i => $fila) {
                if ($i === 1) continue; // Saltar cabecera
                if (empty(array_filter($fila))) continue; // Saltar filas vacías

                $da      = trim($fila['A'] ?? '');
                $ue      = trim($fila['B'] ?? '');
                $prog    = trim($fila['C'] ?? '');
                $proy    = trim($fila['D'] ?? '');
                $act     = trim($fila['E'] ?? '');
                $partida = trim($fila['F'] ?? '');
                $montoOriginal    = trim($fila['G'] ?? '0');
                $monto = str_replace(',', '.', $montoOriginal);

                $errorFila = [];

                // 1. Validaciones de formato inicial
                if (empty($da))      $errorFila[] = "DA vacío";
                if (empty($partida)) $errorFila[] = "Partida vacía";
                if (!is_numeric($monto)) $errorFila[] = "el monto ($montoOriginal) no tiene un formato valido.";

                // 2. Validaciones de Base de Datos (Solo si el formato inicial es correcto)
                if (empty($errorFila)) {
                    $existeEnBD = $model_regional->get_unidad_organizacional($da, $ue, $prog, $act);
                    $existeEnBD_partida = $model_poaf5->get_obtenerPartida($partida);

                    if (!$existeEnBD) {
                        $errorFila[] = "U.O. no existe: DA($da) UE($ue) PROG($prog) ACT($act) aperturado en la BD.";
                    }
                    if (!$existeEnBD_partida) {
                        $errorFila[] = "Partida ($partida) no existe en el POA";
                    }
                }

                // 3. Recolección de errores o preparación de datos
                if (!empty($errorFila)) {
                    $errores[] = "Fila $i: " . implode(" | ", $errorFila);
                } else {
                    $datosParaInsertar[] = [
                        'aper_id'      => $existeEnBD['aper_id'],
                        'da'           => $da,
                        'ue'           => $ue,
                        'aper_programa'         => $prog,
                        'aper_proyecto'         => $proy,
                        'aper_actividad'          => $act,
                        'par_id'       => $existeEnBD_partida['par_id'],
                        'partida'      => $partida,
                        'importe'      => $monto,
                        'g_id'         => $this->session->get('configuracion')['ide'],
                        'ppto_inicial' => $monto
                    ];
                }
            }

            // 4. Respuesta según resultados
            if (!empty($errores)) {
                return $this->response->setJSON([
                    'status'   => 'warning',
                    'message'  => 'No se pudo procesar el archivo por errores de datos.',
                    'detalles' => $errores // Aquí enviamos TODO el listado
                ]);
            }

            if (empty($datosParaInsertar)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'El archivo no contiene datos válidos para insertar.']);
            }

            $db = \Config\Database::connect();
            $db->table('ptto_partidas_sigep')->insertBatch($datosParaInsertar);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => '¡Éxito! Se importaron ' . count($datosParaInsertar) . ' registros.'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }





    //// Valida eliminar registro de asignacion de Presupuesto
    public function eliminar_ppto_asignado() {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Acceso denegado.']);
        }

        try {
            $db = \Config\Database::connect();
            $gestion = $this->session->get('configuracion')['ide'];

            $builder = $db->table('ptto_partidas_sigep');
            $builder->where('g_id', $gestion);
            $builder->delete();

            // CAPTURAR EL NÚMERO DE FILAS AFECTADAS
            $filasEliminadas = $db->affectedRows();

            if ($filasEliminadas > 0) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => "¡Éxito! Se eliminaron {$filasEliminadas} registros de la gestión {$gestion}."
                ]);
            } else {
                return $this->response->setJSON([
                    'status'  => 'warning',
                    'message' => 'No se encontraron registros para eliminar en esta gestión.'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Error de base de datos: ' . $e->getMessage()
            ]);
        }
    }


    /// Exportar Listado en Excel
    public function exportar_ppto_asignado(){
    $model_ppto = new Model_PptoAsig();
    $gestion = $this->session->get('configuracion')['ide'];
    $Ppto_asignado=$model_ppto->get_partidas_asignadas_x_unidadOrganizacional_institucional();

    $filename = "Listado_ppto_".$gestion."-Asignado" . date('Ymd_His') . ".xls";

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
    // CABECERA PRINCIPAL (Título combinado)
    // Nota: Quitamos el estilo de altura fija para que Excel lo maneje automáticamente
    echo '<tr>
            <th colspan="8" style="background-color: #1F4E78; color: #FFFFFF; font-size: 16pt; text-align: center;">
                LISTADO DE PRESUPUESTO ASIGNADO '.$gestion.'
            </th>
          </tr>';

    // ENCABEZADOS DE COLUMNA (Sin thead para evitar saltos de formato)
    echo '<tr style="background-color: #D9D9D9; font-weight: bold; text-align: center;">
            <th width="50">#</th>
            <th width="300">TIPO DE GASTO</th>
            <th width="250">DISTRITAL</th>
            <th width="150">APERTURA PROGRAMATICA</th>
            <th width="150">CODIGO SISIN</th>
            <th width="300">GASTO CORRIENTE / INVERSIÓN</th>
            <th width="100">PARTIDA</th>
            <th width="120">PPTO. ASIGNADO</th>
          </tr>';

    $nro = 0;
    foreach ($Ppto_asignado as $row) {
        $nro++;
        $detalle = ($row['tp_id'] == 1) ? $row['proy_nombre'] : $row['actividad'].' '.$row['abrev'];
        
        // Alternar color de fila si deseas (opcional)
        $colorFila = ($nro % 2 == 0) ? '#F2F2F2' : '#FFFFFF';

        echo '<tr style="background-color: '.$colorFila.';">';
        echo '  <td style="text-align: center;">' . $nro . '</td>';
        echo '  <td>' . $row['tipo_gasto_nombre'] . '</td>';
        echo '  <td>' . $row['dist_distrital'] . '</td>';
        echo '  <td style="text-align: center;">' . $row['prog'].' '.$row['proy'].' '.$row['act'] . '</td>';
        echo '  <td style="text-align: center;">' . $row['proy_sisin'] . '</td>';
        echo '  <td>' . $detalle . '</td>';
        echo '  <td style="text-align: center;">' . $row['partida'] . '</td>';
        echo '  <td style="text-align: right;">' . round($row['ppto_asignado'],2) . '</td>';
        echo '</tr>';
    }

    echo '</table>';

    // 5. UN SOLO EXIT AL FINAL
    exit; 
    }


    /// Ver Detalle del POA-ppto por Unidad Organizacional
    public function ver_detalle_poa_ppto_x_uo() {
    $id = $this->request->getPost('id');
    $model_ppto = new Model_PptoAsig();
    // Supongamos que tienes este método que filtra por ID
    $partidas = $model_ppto->get_partidas_asignadas_x_uniorganizacional($id); 

    $html = '
    <style>
        .tabla-ppto { font-size: 0.85rem; width: 100%; table-layout: fixed; }
        .tabla-ppto th { background-color: #f8f9fa; color: #333; vertical-align: middle; }
        .col-partida { width: 80px; }
        .col-importe { width: 110px; }
        .col-accion { width: 40px; }
        .truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    </style>

    <table class="table table-sm table-hover table-bordered tabla-ppto">
        <thead>
            <tr class="text-center">
                <th class="col-partida">Partida</th>
                <th>Descripción</th>
                <th class="col-importe">Ppto. Asignado '.$this->session->get('configuracion')['ide'].'</th>
                <th class="col-importe">Poa</th>
                <th class="col-importe">Variación</th>
                <th class="col-accion"></th>
            </tr>
        </thead>
        <tbody>';

    $total = 0;
    foreach ($partidas as $p) {
        $total += $p['importe'];
        $html .= '<tr>
                    <td class="text-center fw-bold">'.$p['partida'].'</td>
                    <td class="truncate" title="'.$p['par_nombre'].'">'.$p['par_nombre'].'</td>
                    <td class="text-end font-monospace">'.number_format($p['importe'], 2).'</td>
                    <td class="text-end text-muted">-</td>
                    <td class="text-end text-muted">-</td>
                    <td class="text-center">
                        <i class="fas fa-search text-secondary" style="cursor:pointer"></i>
                    </td>
                  </tr>';
    }

    $html .= '</tbody>
              <tfoot>
                <tr class="table-dark">
                    <td colspan="2" class="text-end">TOTAL ASIGNADO:</td>
                    <td class="text-end font-monospace">'.number_format($total, 2).'</td>
                    <td colspan="3"></td>
                </tr>
              </tfoot>
            </table>';

    return $html;
    }

}


