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
use App\Libraries\Libreria_Configuracion;
use App\Libraries\Libreria_Index;

class CConfiguracion extends BaseController{
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


    /// Vista Configuracion sistema POA
    public function Menu_configuracion(){
        $miLib_conf = new Libreria_Configuracion();
        //$model_funcionario = new Model_funcionarios();
        $data['formulario']='    
          <!--  Header End -->
          <div class="mb-3 overflow-hidden position-relative">
            <div class="px-3">
              <h4 class="fs-6 mb-0">CONFIGURACIÓN SISTEMA </h4>
            </div>
          </div>
          <div class="card">
            <ul class="nav nav-pills user-profile-tab" id="pills-tab" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link position-relative rounded-0 active d-flex align-items-center justify-content-center bg-transparent fs-3 py-3" id="pills-account-tab" data-bs-toggle="pill" data-bs-target="#pills-account" type="button" role="tab" aria-controls="pills-account" aria-selected="true">
                  <i class="ti ti-user-circle me-2 fs-6"></i>
                  <span class="d-none d-md-block">ENTIDAD</span>
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent fs-3 py-3" id="pills-notifications-tab" data-bs-toggle="pill" data-bs-target="#pills-notifications" type="button" role="tab" aria-controls="pills-notifications" aria-selected="false">
                  <i class="ti ti-bell me-2 fs-6"></i>
                  <span class="d-none d-md-block">MODULOS</span>
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent fs-3 py-3" id="pills-bills-tab" data-bs-toggle="pill" data-bs-target="#pills-bills" type="button" role="tab" aria-controls="pills-bills" aria-selected="false">
                  <i class="ti ti-article me-2 fs-6"></i>
                  <span class="d-none d-md-block">PROGRAMAS</span>
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent fs-3 py-3" id="pills-security-tab" data-bs-toggle="pill" data-bs-target="#pills-security" type="button" role="tab" aria-controls="pills-security" aria-selected="false">
                  <i class="ti ti-lock me-2 fs-6"></i>
                  <span class="d-none d-md-block">Security</span>
                </button>
              </li>
            </ul>
            <div class="card-body">
              <div class="tab-content" id="pills-tabContent">
                
                <div class="tab-pane fade show active" id="pills-account" role="tabpanel" aria-labelledby="pills-account-tab" tabindex="0">
                  '.$miLib_conf->conf_form1().'
                </div>

                <div class="tab-pane fade" id="pills-notifications" role="tabpanel" aria-labelledby="pills-notifications-tab" tabindex="0">
                  '.$miLib_conf->conf_form2().'
                </div>


                <div class="tab-pane fade" id="pills-bills" role="tabpanel" aria-labelledby="pills-bills-tab" tabindex="0">
                  '.$miLib_conf->conf_form3().'
                </div>

                <div class="tab-pane fade" id="pills-security" role="tabpanel" aria-labelledby="pills-security-tab" tabindex="0">
                  CUATRO
                </div>

              </div>
            </div>
          </div>';

        //$data['formulario']=$miLib_resp->responsables_poa(); /// lista de responsables (Libreria Responsable)
        return view('View_mantenimiento/View_configuracion/view_configuracion',$data);
    }


/// Valida Form Update Configuracion
  public function Update_configuracion() {
    $db = \Config\Database::connect(); 
    $session = session(); // Necesario para romper la sesión
    $miLib_index = new Libreria_Index();
    $model_index = new IndexModel();

    try {
        $id_actual = $this->request->getPost('ide');
        $id_nuevo_seleccionado = $this->request->getPost('g_id');
        $eval_inicio = $this->request->getPost('eval_inicio');
        $eval_fin    = $this->request->getPost('eval_fin');

        if (!$id_actual) {
            throw new \Exception("ID de configuración actual no proporcionado.");
        }

        // 1. Preparar datos (Asegúrate que los names en el HTML coincidan)
        $data = [
            'conf_nombre_entidad' => strtoupper(trim($this->request->getPost('NombreEntidad'))),
            'conf_sigla_entidad'  => strtoupper(trim($this->request->getPost('SiglaEntidad'))),
            'conf_mision'         => strtoupper(trim($this->request->getPost('MisionEntidad'))),
            'conf_vision'         => strtoupper(trim($this->request->getPost('VisionEntidad'))),
            'conf_mes_otro'       => $this->request->getPost('trm_id'),
            'conf_mes'            => $this->request->getPost('conf_mes'),
            'conf_gestion_desde'  => $this->request->getPost('conf_gestion_desde'),
            'conf_gestion_hasta'  => $this->request->getPost('conf_gestion_hasta'),
            'conf_ajuste_poa'     => $this->request->getPost('conf_ajuste_poa'),
            'tp_msn'              => $this->request->getPost('tp_msn'),
            'conf_mensaje'        => trim($this->request->getPost('conf_mensaje')),
            'eval_inicio' => (!empty($eval_inicio)) ? $eval_inicio : null,
            'eval_fin'    => (!empty($eval_fin))    ? $eval_fin    : null,
            'rd_aprobacion_poa'   => trim($this->request->getPost('rd_aprobacion_poa')),
            'conf_abrev_sistema'  => trim($this->request->getPost('conf_abrev_sistema')),
            'conf_unidad_resp'    => strtoupper(trim($this->request->getPost('conf_unidad_resp'))),
            'conf_sis_pie'        => trim($this->request->getPost('conf_sis_pie')),
        ];

        // 2. Actualizar la configuración actual
        $db->table('configuracion')->where('ide', $id_actual)->update($data);

        $conf = $model_index->get_gestion_activo(); /// configuracion gestion activo
        $modulos = $model_index->modulos($conf['ide'],$session->get('funcionario')['tp_adm']); /// modulos
        $view_modulos=$miLib_index->Modulos_disponibles($modulos); /// vista modulos Cabecera
        $view_modulos_Sidebar=$miLib_index->Modulos_disponibles_Sidebar($modulos,$session->get('funcionario')['fun_nombre'].' '.$session->get('funcionario')['fun_paterno'].' '.$session->get('funcionario')['fun_materno'],$session->get('funcionario')['fun_cargo'],$conf['conf_abrev_sistema']); /// vista modulos Cabecera Sidebar
        $view_cabecera=$miLib_index->Cabecera_sistema($session->get('funcionario')['fun_nombre'].' '.$session->get('funcionario')['fun_paterno'].' '.$session->get('funcionario')['fun_materno'],$session->get('funcionario')['fun_cargo'],$conf['conf_abrev_sistema'],$conf['conf_img']);
        $view_cabecera_layout=$miLib_index->Cabecera_sistema_layout($session->get('funcionario')['fun_nombre'].' '.$session->get('funcionario')['fun_paterno'].' '.$session->get('funcionario')['fun_materno'],$session->get('funcionario')['fun_cargo'],$conf['conf_abrev_sistema'],$conf['conf_img']);

        //// Actualizando en la session
         $userData = [
            'configuracion'   => $conf,
            'modulos'   => $modulos,
            'view_modulos'   => $view_modulos,
            'view_modulos_sidebar'   => $view_modulos_Sidebar,
            'view_cabecera'   => $view_cabecera,
            'view_cabecera_layout'   => $view_cabecera_layout,
            'view_menu_izquierdo'   => $miLib_index->Menu_izquierdo(), /// menu izquierdo
            'isLoggedIn' => TRUE, // Bandera clave para tus filtros de acceso
            ];
       
            $session->set($userData); // Guarda la sesión


        // 3. Lógica de Cambio de Gestión Activa
        if ($id_actual != $id_nuevo_seleccionado) {
            // Desactivar la anterior
            $db->table('configuracion')->where('ide', $id_actual)->update(['conf_estado' => 0]);
            
            // Activar la nueva
            $db->table('configuracion')->where('ide', $id_nuevo_seleccionado)->update(['conf_estado' => 1]);

            // --- ROMPER SESIÓN Y REDIRIGIR ---
            $session->destroy();
            return redirect()->to(base_url('login'))
                             ->with('error', 'Gestión cambiada. Por favor, inicie sesión nuevamente.');
        } 
        
        return redirect()->to(base_url('mnt/ConfiguracionSistema'))
                         ->with('success', 'Configuración actualizada correctamente.');

    } catch (\Exception $e) {
        log_message('error', 'Error en Update_configuracion: ' . $e->getMessage());
        return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
    }
}


//// Update Estado de los Modulos 
    public function update_estado_modulos() {
        $db = \Config\Database::connect(); 
        $session = session(); // Necesario para romper la sesión
        $miLib_index = new Libreria_Index();
        $model_index = new IndexModel();

        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Acceso no permitido']);
        }

        $id      = $this->request->getPost('id');
        $valor   = $this->request->getPost('valor');
        $ide_gestion = $session->get('configuracion')['ide'];


            // 1. Lógica de Base de Datos
        // CORRECCIÓN: Quitamos el $ extra de $this->$model_index
        if ($model_index->existe_modulo_configurado($id, $ide_gestion)) {
            if ($valor == 0) {
                // Si existe y el switch se apagó, eliminamos
                $db->table('confi_modulo')
                   ->where('mod_id', $id)
                   ->where('ide', $ide_gestion)
                   ->delete(); 
            }
        } else {
            if ($valor == 1) {
                // Si no existe y el switch se encendió, insertamos
                $db->table('confi_modulo')->insert([
                    'mod_id' => $id,
                    'ide'    => $ide_gestion
                ]);
            }
        }

        // 2. Actualización de Sesión
        $tp_adm = $session->get('funcionario')['tp_adm'];
        $modulos = $model_index->modulos($ide_gestion, $tp_adm);
        $view_modulos_Sidebar=$miLib_index->Modulos_disponibles_Sidebar($modulos,$session->get('funcionario')['fun_nombre'].' '.$session->get('funcionario')['fun_paterno'].' '.$session->get('funcionario')['fun_materno'],$session->get('funcionario')['fun_cargo'],$this->session->get('configuracion')['conf_abrev_sistema']); /// vista modulos Cabecera Sidebar
        
        // Generamos la vista actualizada del menú
        $view_modulos = $miLib_index->Modulos_disponibles($modulos); 

        $session->set([
            'modulos'      => $modulos,
            'view_modulos' => $view_modulos,
            'view_modulos_sidebar'   => $view_modulos_Sidebar,
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'token'  => csrf_hash()
        ]);
    }


    /// Valida form aperturas programaticas
    public function valida_aperturas(){
        $db = \Config\Database::connect();
        $rules = [
            'prog' => 'required|numeric|greater_than[0]',
            'detalle' => 'required|min_length[3]|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Por favor, complete todos los campos correctamente.',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
        'aper_programa'    => $this->request->getPost('prog'),
        'aper_proyecto'    => '0000',
        'aper_actividad'   => '000',
        'aper_descripcion' => strtoupper(trim($this->request->getPost('detalle'))),
        'aper_asignado'    => 1,
        'fun_id'           => session()->get('fun_id') 
        ];

        if ($db->table('aperturaprogramatica')->insert($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Apertura guardada correctamente'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No se pudo guardar la información en la base de datos.'
            ]);
        }
    }


/*public function valida_aperturas() {
    // 1. Validar datos
    $rules = [
        'prog'    => 'required|numeric|min_length[2]|max_length[2]',
        'detalle' => 'required|min_length[3]'
    ];

    if (!$this->validate($rules)) {
        return $this->response->setJSON([
            'status' => 'error_val',
            'errors' => $this->validator->getErrors()
        ]);
    }

    // 2. Preparar datos para la tabla 'aperturaprogramatica'
    $data = [
        'aper_programa'    => $this->request->getPost('prog'),
        'aper_proyecto'    => '0000',
        'aper_actividad'   => '000',
        'aper_descripcion' => strtoupper(trim($this->request->getPost('detalle'))),
        'aper_asignado'    => 1,
        'fun_id'           => session()->get('fun_id') 
    ];

    try {
        $db = \Config\Database::connect();
        $builder = $db->table('aperturaprogramatica');
        
        if ($builder->insert($data)) {
            // Respuesta de éxito definitiva
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No se pudo insertar.']);
        }
    } catch (\Exception $e) {
        return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
    }
}*/

public function valida_aperturas2(){
    $validation = \Config\Services::validation(); // Cargar el servicio manualmente

    $rules = [
        'prog'    => 'required|numeric|min_length[2]|max_length[2]',
        'detalle' => 'required|min_length[3]'
    ];

    // Obtenemos los datos del POST explícitamente
    $dataInput = $this->request->getPost();

    if (!$this->validateData($dataInput, $rules)) { // Usamos validateData en lugar de validate
        return $this->response->setJSON([
            'status' => 'error1',
            'message' => 'Validación fallida',
            'errors' => $this->validator->getErrors()
        ]);
    }

    // 3. Preparación de datos
    $data = [
        'aper_programa'    => $this->request->getPost('prog'),
        'aper_proyecto'    => '0000',
        'aper_actividad'   => '000',
        'aper_descripcion' => strtoupper(trim($this->request->getPost('detalle'))),
        'aper_asignado'    => 1,
        'fun_id'           => session()->get('fun_id') // Usando el helper global más seguro
    ];

    // 4. Inserción
    try {
        $db = \Config\Database::connect();
        $builder = $db->table('aperturaprogramatica');
        
        if ($builder->insert($data)) {
            return $this->response->setJSON(['status' => 'success']);
        } 
        
        return $this->response->setJSON(['status' => 'error', 'message' => 'No se pudo guardar.']);
        
    } catch (\Exception $e) {
        return $this->response->setJSON([
            'status' => 'error2', 
            'message' => 'Error de base de datos: ' . $e->getMessage()
        ]);
    }
}




}


