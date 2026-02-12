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
use App\Libraries\Libreria_EstructuraOrganizacional;
use App\Libraries\Libreria_Index;

use Dompdf\Dompdf;
use Dompdf\Options;
use setasign\Fpdi\TcpdfFpdi; // Importante para la firma

class CEstructura_organizacional extends BaseController{
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
    public function menu_estructura(){
        $session = session(); // Necesario para romper la sesión
        $miLib_Estructura = new Libreria_EstructuraOrganizacional();
        $model_regional = new Model_regional();
        $regional=$model_regional->obtenerRegionales();

        $tabla='';
        $tabla.='
        <div class="row">
            <div class="card">
                <div class="card-body">
                  <h5 class="fs-4 fw-semibold mb-4">ESTRUCTURA ORGANIZACIONAL - GESTIÓN '.$this->session->get('configuracion')['ide'].'</h5>
                  <div class="mb-4 row align-items-center">
                    <label for="exampleInputSelect2" class="form-label col-sm-3 col-form-label text-end">Seleccione la Regional</label>
                    <div class="col-sm-6">
                      <select class="form-select" id="dep_id" name="dep_id" aria-label="Default select example">
                      <option value="0">Seleccione ..</option>';
                      foreach($regional as $row){
                        $tabla.='<option value='.$row['dep_id'].'>'.$row['dep_id'].'.- '.$row['dep_departamento'].'</option>';
                      }
                      $tabla.='
                      </select>
                    </div>
                  </div>
                </div>
                <div id="listado"></div>
            </div>
        </div>';
        $data['formulario']=$tabla;
        return view('View_mantenimiento/View_estructuraCns/view_estructuraOrganizacional',$data);
    }


    /// Obtiene el listado de las unidades Organizacionales de la regional Seleccionado
    public function obtener_unidades_organizacionales() {
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(403);
    }

    $dep_id = $this->request->getPost('dep_id');
    $model_regional = new Model_regional(); 
   // $miLib_Estructura = new Libreria_EstructuraOrganizacional();

    // 1. Obtener datos (Asegúrate que get_regional use Bindings como vimos antes)
    $regional = $model_regional->get_regional($dep_id);
    $config = $this->session->get('configuracion');
    $unidades_aperturados = $model_regional->lista_unidades_disponibles($dep_id, $config['ide']);
    $tipo_est= $model_regional->lista_tipo_establecimiento();

    $lista_distritales=$model_regional->obtenerDistritales($dep_id);
    // Generamos el HTML (puedes usar una vista parcial para que sea más limpio)
    $html = '
    <div class="modal fade" id="modalNuevoRegistro" 
     data-bs-backdrop="static" 
     data-bs-keyboard="false" 
     tabindex="-1" 
     aria-labelledby="staticBackdropLabel" 
     aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Registrar Unidad Organizacional</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="form_add_uo" method="post" class="login-form">
            <div class="modal-body">
              <!-- Agrega aquí tus campos (input dep_id oculto, nombre, etc.) -->
              <input type="hidden" name="dep_id" value="'.$dep_id.'">
              <div class="mb-3">
              <label class="form-label">Seleccione Distrital</label>
                  <select class="form-select" aria-label="Default select example" name="dist_id" id="dist_id">
                    <option value="0">Seleccione..</option>';
                    foreach ($lista_distritales as $rows) {
                      $html.= '<option value="'.$rows['dist_id'].'">'.$rows['dist_distrital'].'</option>';
                    }
                  $html .='
                  </select>
                  <div class="invalid-feedback">Por favor seleccione una distrital.</div>
              </div>
              <div class="mb-3">
              <label class="form-label">Seleccione Tipo de Establecimiento</label>
                  <select class="form-select" aria-label="Default select example" name="te_id" id="te_id">
                    <option value="0">Seleccione..</option>';
                    foreach ($tipo_est as $rows) {
                      $html.= '<option value="'.$rows['te_id'].'">'.$rows['tipo'].' - '.$rows['establecimiento'].'</option>';
                    }
                  $html .='
                  </select>
              </div>
               <div class="mb-3">
                    <label class="form-label">Código Unidad (3 dígitos)</label>
                    <input type="number" class="form-control" name="cod_unidad" min="1" max="999" step="1" >
                    <div class="invalid-feedback">Ingrese un código válido (1-999).</div>
                </div>
              <div class="mb-3">
                <label class="form-label">Nombre de la Unidad</label>
                <input type="text" class="form-control" name="nombre_unidad" >
                <div class="invalid-feedback">El nombre es obligatorio (min. 3 caracteres).</div>
              </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" id="btnGuardarUO" class="btn btn-primary">
                    <span id="btnText">Guardar</span>
                    <span id="btnLoader" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="card-body border-top p-4">';

// Contenedor de Título y Botones (Flexbox para alineación)
$html .= '<div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-semibold mb-0">Lista de Unidad Organizacional: '.strtoupper($regional['dep_departamento']).'</h6>
            <div class="d-flex gap-2">
            <button type="button" id="btnGenerarReporte" onclick="generarReporteBase64_uorganizacionales('.$dep_id.')" class="btn btn-outline-primary btn-sm ms-2">
                <img src="'.base_url().'Img/Iconos/page_red.png" alt="Nuevo"> 
                <span>Generar Reporte.Pdf</span>
            </button>
            <button type="button" class="btn btn-primary btn-sm d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalNuevoRegistro">
                <img src="'.base_url().'Img/Iconos/application_form_add.png" alt="Nuevo"> Nuevo Registro
            </div>
          </div>';
    
    if (empty($unidades_aperturados)) {
        $html .= '<p class="text-muted small">No hay distritales registradas para esta regional.</p>';
    } else {
    $html .= '
            <div class="table-responsive">
                <table class="table text-nowrap align-middle mb-0" id="table_ue">
                    <thead>
                         <tr class="text-muted fw-semibold">
                            <th style="width: 10px;" class="ps-0">#</th>
                            <th style="width: 10px;">Distrital</th>
                            <th style="width: 10px;">Código</th>
                            <th style="width: 10px;">Tipo</th>
                            <th style="width: 200px;">Unidad Organizacional</th>
                            <th style="width: 10px;" class="text-end">Estado</th>
                        </tr>
                        <!-- Fila de Buscadores por Columna -->
                        <tr class="filter-row">
                            <th></th>
                            <th><input type="text" class="form-control form-control-sm column-search" data-column="1" placeholder="Filtrar..."></th>
                            <th><input type="text" class="form-control form-control-sm column-search" data-column="2" placeholder="Filtrar..."></th>
                            <th><input type="text" class="form-control form-control-sm column-search" data-column="3" placeholder="Filtrar..."></th>
                            <th><input type="text" class="form-control form-control-sm column-search" data-column="4" placeholder="Filtrar..."></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="border-top">';

            $options_html = '';
            foreach ($tipo_est as $tp) {
                // Solo guardamos las opciones; la selección "selected" la haremos con un Replace rápido o JS
                $options_html .= '<option value="'.$tp['te_id'].'">'.$tp['tipo'].'</option>';
            }

            $nro = 0;
            foreach ($unidades_aperturados as $row) {
                $nro++;
                $id = $row['act_id'];
                $is_incluido = ($row['incluido'] == 1);
                $checked = $is_incluido ? 'checked' : ''; 
                $disabled = $is_incluido ? 'disabled' : ''; // Si está incluido, deshabilitamos campos

                $html .= '<tr data-id="'.$id.'">
                    <td  class="ps-0"><span class="text-dark fw-semibold" title="'.$id.'">' . $nro . '</span></td>
                    <td>
                        <span class="badge bg-light-primary text-primary fw-semibold fs-2 px-2 py-1 rounded">
                            '.strtoupper($row['dist_distrital']).'
                        </span>
                    </td>
                    <td>
                        <input type="number" data-field="act_cod" value="'.$row['act_cod'].'" 
                               class="form-control form-control-sm fw-bold text-center input-field"
                               '.$disabled.' min="0" max="999"
                               oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);">
                    </td>
                    <td>';
                        if($row['ta_id'] == 2) {
                            $html .= '<select class="form-select form-select-sm input-field" data-field="te_id" '.$disabled.'>';
                            $html .= str_replace('value="'.$row['te_id'].'"', 'value="'.$row['te_id'].'" selected', $options_html);
                            $html .= '</select>';
                        }
                $html .= '</td>
                    <td>
                        <textarea class="form-control form-control-sm input-field" data-field="act_descripcion" 
                                  rows="2" '.$disabled.'>'.$row['act_descripcion'].'</textarea>
                    </td>
                    <td class="text-end">
                        <div class="form-check form-switch d-flex justify-content-end">
                            <input class="form-check-input check-gestion" type="checkbox" 
                                   data-field="incluido" data-id="' . $id . '" ' . $checked . ' 
                                   style="cursor:pointer; width: 2.5em; height: 1.25em;">
                        </div>
                    </td>
                </tr>';
            }
            $html.='</tbody>
            </table>
            </div>';
    }
    $html .= '</div>';
    
    return $this->response->setJSON([
        'status' => 'success',
        'datos'  => $html,
        'token'  => csrf_hash()
    ]);
}


    //// Adiciona Nueva Estructura Organizacional
    public function add_uorganizacional(){
        if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(403);
        }

        $db = \Config\Database::connect(); 
        // 2. Recolección de datos
        $dep_id = $this->request->getPost('dep_id');
        $dist_id = $this->request->getPost('dist_id');
        $te_id = $this->request->getPost('te_id');
        $cod = $this->request->getPost('cod_unidad');
        $nombre = strtoupper(trim($this->request->getPost('nombre_unidad')));

        // 3. Preparar datos para insertar
        $data = [
            'dist_id'         => $this->request->getPost('dist_id'),
            'te_id'           => $this->request->getPost('te_id'),
            'act_cod'         => $this->request->getPost('cod_unidad'),
            'act_descripcion' => strtoupper($this->request->getPost('nombre_unidad')),
            'fun_id'        => $this->session->get('fun_id'), // Por defecto al crear
            'fecha'  => date('Y-m-d H:i:s')
        ];

        try {
            $db->table('unidad_actividad')->insert($data);
            $id_insertado = $db->insertID();

            if ($id_insertado) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'act_id' => $id_insertado, // Se usará en el data-id de la fila
                    'msg'    => 'Registro guardado correctamente',
                    'token'  => csrf_hash() // Mantiene la seguridad activa
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error', 
                    'msg'    => 'Error al insertar en la base de datos.'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg'    => 'Error de servidor: ' . $e->getMessage()
            ]);
        }
    }



    /// Update Estado Unidad Organizacional
    public function update_estado_uorganizacional() {
        // 1. Verificación de seguridad
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $db = \Config\Database::connect();
        // Asegúrate de que el modelo esté correctamente instanciado
        $model_regional = new Model_regional(); 

        // 2. Recolección de datos (nombres de variables sincronizados con el JS)
        $act_id          = $this->request->getPost('id');
        $act_cod         = $this->request->getPost('act_cod');
        $te_id           = $this->request->getPost('te_id');
        $act_descripcion = strtoupper($this->request->getPost('act_descripcion'));
        $incluido        = $this->request->getPost('incluido'); // El valor 0 o 1 del switch
        $gestion         = $this->session->get('configuracion')['ide'];
        $msg             = "Cambios guardados correctamente";

        try {
            // --- LÓGICA DE GESTIÓN (Tabla uni_gestion) ---
            // Verificamos si ya está registrada en la gestión actual
            $existe_en_gestion = $db->table('uni_gestion')
                                    ->where('act_id', $act_id)
                                    ->where('g_id', $gestion)
                                    ->get()
                                    ->getRow();

            if ($existe_en_gestion) {
                if ($incluido == 0) {
                    // Si existe y el switch se apagó (0), eliminamos el registro de la gestión
                    $db->table('uni_gestion')
                       ->where('act_id', $act_id)
                       ->where('g_id', $gestion)
                       ->delete(); 
                    $msg = "Unidad Organizacional EXCLUIDA de la Gestión " . $gestion;
                }
            } else {
                if ($incluido == 1) {
                    // Si no existe y el switch se encendió (1), insertamos en la gestión
                    $db->table('uni_gestion')->insert([
                        'act_id' => $act_id,
                        'g_id'   => $gestion
                    ]);
                    $msg = "Unidad Organizacional INCLUIDA en la Gestión " . $gestion;
                }
            }

            // --- ACTUALIZACIÓN DE DATOS (Tabla unidad_actividad) ---
            $updateData = [
                'act_cod'         => $act_cod,
                'act_descripcion' => $act_descripcion
            ];

            // Solo incluimos te_id si no es nulo (para filas con ta_id=2)
            if ($te_id !== null && $te_id !== '') {
                $updateData['te_id'] = $te_id;
            }

            $db->table('unidad_actividad')
               ->where('act_id', $act_id)
               ->update($updateData);

            // 3. Respuesta Final Exitosa
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => $msg,
                'token'   => csrf_hash() // Nuevo token para la siguiente petición AJAX
            ]);

        } catch (\Exception $e) {
            // Respuesta en caso de error
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Error en el servidor: ' . $e->getMessage(),
                'token'   => csrf_hash()
            ]);
        }
    }



    /// Reporte Unidades disponibles por regional
    public function Pdf_lista_uorganizacional(){
    $miLib_Estructura = new Libreria_EstructuraOrganizacional();
    $model_regional = new Model_regional();
    $dep_id = $this->request->getPost('dep_id'); /// id regional
    $regional = $model_regional->get_regional($dep_id);
    $fecha = date('d/m/Y'); // Definir la variable fecha
    $listado_unidad=$model_regional->lista_unidades_disponibles_rep($dep_id,$this->session->get('configuracion')['ide']);
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
                <p>Lista de Unidades Organizacionales regional '.strtoupper($regional['dep_departamento']).' - Gestión '.$this->session->get('configuracion')['conf_gestion'].'</p>
            </header>

            <footer>
                <p>'.$this->session->get('configuracion')['conf_version'].'. Página <span class="pagenum"></span></p>
            </footer>

            <!-- El contenido principal va aquí -->
            <main>
                '.$miLib_Estructura->listado_uorganizacional_rep($listado_unidad).'
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
            'nombre' => 'unidad_organizacional.pdf',
            'pdf'    => 'data:application/pdf;base64,' . $base64
        ]);
    }



}


