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
        $miLib_conf = new Libreria_EstructuraOrganizacional();
        $data['formulario']=$miLib_conf->Lista_poa_para_asignacion_presupuesto();

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

}


