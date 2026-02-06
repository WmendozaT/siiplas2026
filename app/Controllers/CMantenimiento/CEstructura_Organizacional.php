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

    /// Obtiene el listado 
    public function obtener_unidades_organizacionales() {
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(403);
    }

    $dep_id = $this->request->getPost('dep_id');
    $model_regional = new Model_regional(); 
    
    // 1. Obtener datos (Asegúrate que get_regional use Bindings como vimos antes)
    $regional = $model_regional->get_regional($dep_id);
    $config = $this->session->get('configuracion');
    $unidades_aperturados = $model_regional->lista_unidades_disponibles($dep_id, $config['ide']);
    $tipo_est= $model_regional->lista_tipo_establecimiento();

    // Generamos el HTML (puedes usar una vista parcial para que sea más limpio)
    $html = '<div class="card-body border-top p-4">';
    $html .= '<h6 class="fw-semibold mb-3">Lista de Unidad Organizacional de la regional '.strtoupper($regional['dep_departamento']).'</h6>';
    
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
                    <td class="ps-0"><span class="text-dark fw-semibold">' . $nro . '</span></td>
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
            $html.='</tbody></table></div>';
    }
    $html .= '</div>';
    
    return $this->response->setJSON([
        'status' => 'success',
        'datos'  => $html,
        'token'  => csrf_hash()
    ]);
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



}


