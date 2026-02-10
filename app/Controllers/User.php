<?php

namespace App\Controllers;
use CodeIgniter\Controller;
use CodeIgniter\API\ResponseTrait; // Importar si se usa response
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\Index\IndexModel;
use App\Models\Index\SolicitudesPswModel;
use App\Models\Model_Mantenimiento\Model_funcionarios;

use App\Libraries\Libreria_Index;

class User extends BaseController{
    protected $IndexModel;
    protected $Model_funcionarios;
    protected $SolicitudesPswModel; // Declara el nuevo modelo

    public function __construct(){
        $this->IndexModel = new IndexModel();
        $this->Model_funcionarios = new Model_funcionarios();   
        $this->SolicitudesPswModel = new SolicitudesPswModel(); // Inicializa el nuevo modelo
    }


    /// index
    public function index() {
        // 1. Instanciar el servicio de sesión
        $session = session(); 
        $miLib_index = new Libreria_Index();
        // 2. Si ya está logueado, redirigir al dashboard
        if ($session->get('isLoggedIn')) {
            return redirect()->to(base_url('dashboard'));
        }

        // 3. Cargar helpers y preparar datos
        helper(['form']);
        $data['formulario'] = $miLib_index->form_login();
        
        // 4. Retornar la vista
        return view('index/login', $data);
    }


   
    /// Valida login
    public function loginAction(){
        $session = session();
        $miLib_index = new Libreria_Index();
        $model_index = new IndexModel();
        $model_funcionario = new Model_funcionarios();

        $rules = [
            'user_name' => 'required|min_length[3]|max_length[20]', // Ajusta longitudes
            'password' => 'required|min_length[5]|max_length[20]', // Ajusta longitudes
        ];
        
        // 2. Ejecutar la validación básica
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $usuario = $this->request->getPost('user_name');
        $password = $this->request->getPost('password');
        $tp = $this->request->getPost('tp');
        $captcha = $this->request->getPost('captcha');
        $dat_captcha = $this->request->getPost('dat_captcha');
        $is_valid = $model_index->verificar_loggin($usuario, $password, $captcha, $dat_captcha);
        if($is_valid['bool']==true){
            $funcionario=$model_funcionario->get_responsablePoa($is_valid['data']['fun_id']); /// Datos del Funcionario
            $conf = $model_index->get_gestion_activo(); /// configuracion gestion activo
            $modulos = $model_index->modulos($conf['ide'],$is_valid['data']['tp_adm']); /// modulos
            
            $view_modulos=$miLib_index->Modulos_disponibles($modulos); /// vista modulos Cabecera
            $view_modulos_Sidebar=$miLib_index->Modulos_disponibles_Sidebar($modulos,$funcionario['fun_nombre'].' '.$funcionario['fun_paterno'].' '.$funcionario['fun_materno'],$funcionario['fun_cargo'],$conf['conf_abrev_sistema'],$funcionario['imagen_perfil']); /// vista modulos Cabecera Sidebar
            $userData = [
            'fun_id'    => $is_valid['data']['fun_id'], // Asegúrate de que tu modelo devuelve 'id'
            'funcionario'   => $funcionario,
            'rol'   => $model_index->get_rol_usuario($is_valid['data']['fun_id']),
            'configuracion'   => $conf,
            'modulos'   => $modulos,
            'view_modulos'   => $view_modulos,
            'view_modulos_sidebar'   => $view_modulos_Sidebar,
            'view_cabecera'   => $miLib_index->Cabecera_sistema($funcionario['fun_nombre'].' '.$funcionario['fun_paterno'].' '.$funcionario['fun_materno'],$funcionario['fun_cargo'],$conf['conf_abrev_sistema'],$conf['conf_img'],$funcionario['imagen_perfil']),
            'view_cabecera_layout'   => $miLib_index->Cabecera_sistema_layout($funcionario['fun_nombre'].' '.$funcionario['fun_paterno'].' '.$funcionario['fun_materno'],$funcionario['fun_cargo'],$conf['conf_abrev_sistema'],$conf['conf_img'],$funcionario['imagen_perfil']),
            'view_menu_izquierdo'   => $miLib_index->Menu_izquierdo(), /// menu izquierdo
            'view_bienvenida'   => $miLib_index->bienvenida($conf['conf_abrev_sistema'],$conf['conf_unidad_resp']), /// bienvenida
            'regional'   => $model_index->datos_regional($funcionario['fun_dist']),
            
            'isLoggedIn' => TRUE, // Bandera clave para tus filtros de acceso
            ];
       

            $session->set($userData); // Guarda la sesión

            // 2. Redirigir al usuario a una página protegida (ej. dashboard)
            return redirect()->to(base_url('dashboard')); 
           
        }
        else{
            //$session->destroy(); 
            return redirect()->to(base_url('login'))->with('errors', $is_valid['message'] ?? 'Error de acceso contactarse con el Administrador.');
        }

    }



    /// Solicitar recuperacion de password
    public function user_password(){
        $data['formulario']='
            <style>
                #loadingpws {
                    display: none;
                    position: fixed;
                    left: 50%;
                    top: 50%;
                    transform: translate(-50%, -50%);
                    font-size: 24px;
                    z-index: 1000;
                }

                .modal-backdrop {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.6);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 1000; /* Asegura que esté por encima de todo */
                }

                /* El cuadro del mensaje */
                .modal-content {
                    background-color: #fff;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
                    width: 90%;
                    max-width: 400px;
                    text-align: center;
                }

                .modal-header h4 {
                    margin-top: 0;
                    color: #333;
                }

                .modal-body p {
                    color: #555;
                    margin-bottom: 20px;
                }

                .modal-footer {
                    display: flex;
                    justify-content: center;
                    gap: 10px; /* Espacio entre botones */
                }

                /* Estilos básicos para los botones (usa tus propias clases si usas Bootstrap) */
                .btn-primary {
                    background-color: #125d55;
                    color: white;
                    border: none;
                    padding: 10px 20px;
                    border-radius: 5px;
                    cursor: pointer;
                }

                .btn-secondary {
                    background-color: #6c757d;
                    color: white;
                    border: none;
                    padding: 10px 20px;
                    border-radius: 5px;
                    cursor: pointer;
                }
            </style>

            <div class="d-flex justify-content-center align-items-center cardCenter">
                <div class="card rounded-3 blue-border cardSize">
                    <div class="card-body">
                        <h5 class="text-center fw-bold font-24">Olvidé mi contraseña</h5>
                        <h6 class="card-subitle text-center mb-2 text-muted">Ingrese su Usuario y Correo Electronico</h6>

                        <div class="d-flex align-items-center my-3">
                            <svg class="iconEmailReset" style=" width: 55px; height: 55px; margin-right: 20px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <path fill="#67757c" d="M215.4 96H144 107.8 96v8.8V144v40.4 89L.2 202.5c1.6-18.1 10.9-34.9 25.7-45.8L48 140.3V96c0-26.5 21.5-48 48-48h76.6l49.9-36.9C232.2 3.9 243.9 0 256 0s23.8 3.9 33.5 11L339.4 48H416c26.5 0 48 21.5 48 48v44.3l22.1 16.4c14.8 10.9 24.1 27.7 25.7 45.8L416 273.4v-89V144 104.8 96H404.2 368 296.6 215.4zM0 448V242.1L217.6 403.3c11.1 8.2 24.6 12.7 38.4 12.7s27.3-4.4 38.4-12.7L512 242.1V448v0c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64v0zM176 160H336c8.8 0 16 7.2 16 16s-7.2 16-16 16H176c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64H336c8.8 0 16 7.2 16 16s-7.2 16-16 16H176c-8.8 0-16-7.2-16-16s7.2-16 16-16z"/>
                            </svg>
                            <h6 class="mb-2 text-muted" style="padding-top : 15px">"Ingrese su Usuario asignado y la dirección de correo electrónico y le enviaremos un enlace para restablecer su contraseña."</h6>
                        </div>';

                            if(session()->getFlashdata('errors')):
                                $data['formulario'].='<div class="alert alert-danger">'.session()->getFlashdata('errors').'</div>';
                            endif;

                             if(session()->getFlashdata('success')):
                                $data['formulario'].='<div class="alert alert-success">'.session()->getFlashdata('success').'</div>';
                            endif;

                        $data['formulario'].='
                            <div id="loadingpws" ><i class="fas fa-spinner fa-spin"></i> Cargando...</div>
                            <form id="formpws" novalidate class="form-horizontal" action="'.base_url('valida_psw').'" method="post">
    
                            <!-- Campo de Usuario -->
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="form-floating mb-2">
                                        <input tabindex="1" id="user_namepws" class="form-control form-input-bg" name="user_namepws" value="" type="text" autocomplete="off" spellcheck="false" placeholder="Usuario" minlength="5" maxlength="20" style="text-transform:uppercase;" oninput="this.value = this.value.toUpperCase();"/>
                                        <label for="user_namepws">Usuario</label>
                                        <!-- Contenedor para el icono (puedes ajustar el estilo CSS para posicionarlo correctamente) -->
                                        <div style="position: absolute; right: 10px; top: 15px;">
                                            <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g id="SVGRepo_iconCarrier">
                                                    <path d="M12 4C9.79086 4 8 5.79086 8 8C8 10.2091 9.79086 12 12 12C14.2091 12 16 10.2091 16 8C16 5.79086 14.2091 4 12 4ZM12 14C8.13401 14 5 17.134 5 21H19C19 17.134 15.866 14 12 14Z" fill="#67757C"></path>
                                                </g>
                                            </svg>
                                        </div>
                                        <!-- Div para mostrar el error de usuario -->
                                        <div id="usupsw" class="text-danger text-start mt-1" style="font-size: 0.8rem;"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Campo de Correo Electrónico -->
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="form-floating mb-2">
                                        <input tabindex="2" id="emailpws" class="form-control form-input-bg" name="emailpws" value="" type="email" autocomplete="off" spellcheck="false" placeholder="Email" />
                                        <label for="emailpws">Correo</label>
                                        <!-- Contenedor para el icono -->
                                        <div style="position: absolute; right: 10px; top: 15px;">
                                            <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g id="SVGRepo_iconCarrier"> 
                                                    <path d="M4 7.00005L10.2 11.65C11.2667 12.45 12.7333 12.45 13.8 11.65L20 7" stroke="#67757C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> 
                                                    <rect x="3" y="5" width="18" height="14" rx="2" stroke="#67757C" stroke-width="2" stroke-linecap="round"></rect> 
                                                </g>
                                            </svg>
                                        </div>
                                        <!-- Div para mostrar el error de email -->
                                        <div id="email" class="text-danger text-start mt-1" style="font-size: 0.8rem;"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 col-10 mx-auto mt-4">
                                <button class="btn btnColor borderRadius" name="login" id="kc-login" type="submit">ENVIAR</button>
                                <a href="'.base_url().'logout" class="btn btn-outline-secondary borderRadius">VOLVER</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div id="customConfirmModal" class="modal-backdrop" style="display: none;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Confirmación de Envío</h4>
                    </div>
                    <div class="modal-body">
                        <p>¿Está seguro de que desea enviar el formulario de restablecimiento de contraseña?</p>
                    </div>
                    <div class="modal-footer">
                        <button id="confirmYes" class="btn btn-primary">Sí, enviar</button>
                        <button id="confirmNo" class="btn btn-secondary">Cancelar</button>
                    </div>
                </div>
            </div>';

        return view('index/password', $data);
    }



    /// Valida password
    public function ValidaPws(){
        $postData = $this->request->getPost();
        if ($postData) {
            //$model_index = new IndexModel();
            $usuario_ingresado = $this->request->getPost('user_namepws');
            $email_ingresado = $this->request->getPost('emailpws');
            $datos_funcionario = $this->IndexModel->fun_usuario($usuario_ingresado);
            
            if (count($datos_funcionario)!=0) {
                $data_to_store = [
                    'fun_id'    => $datos_funcionario['fun_id'],
                    'email'     => $email_ingresado,
                    'sol_fecha' => date("Y-m-d H:i:s"), 
                    'num_ip'    => $this->request->getIPAddress(),
                    'nom_ip'    => gethostbyaddr($this->request->getIPAddress()),
                ];
                 $sol_id = $this->SolicitudesPswModel->createPswSolicitud($data_to_store);

                    if(count($this->SolicitudesPswModel->solicitud_contraseñas($sol_id))!=0){
                        return redirect()->to(base_url('password'))->with('success', 'Instrucciones enviadas a su correo.');
                    }
                    else{
                        return redirect()->back()->with('errors', 'Error al registrar la solicitud.');
                    }
                
            } else {
                // La fila NO fue encontrada (el usuario no existe o el estado es 3)
                 return redirect()->back()->with('errors', 'Usuario no válido o inactivo.');
            }

        } else {
            
            return redirect()->to(base_url('password'))->with('errors', 'Error!');
        }
    }





    /// Lista de documentos
    public function list_documentos(){
        $data['formulario']='
        <div class="main-wrapper">
          <div class="row auth-wrapper gx-0">
            <div class="col-lg-4 col-xl-3 bg-primary auth-box-2 on-sidebar" style="background: #004640 !important; padding-bottom: 10px !important; padding-top: 10px !important;">
              <div class="h-100 d-flex align-items-center justify-content-center">
                <div class="row justify-content-center text-center">
                  <div class="col-md-7 col-lg-12 col-xl-9">
                    <div>
                      <span class="db">
                        <img src="'.base_url().'Img/login/logo_CNS_header.png" style="width: 112px; height: 125; ">
                      </span>
                    </div>
                    <h2 class="text-white mt-4 fw-light">
                      <span class="font-weight-medium">Sistema de Planificación y Seguimiento al POA</span><br>
                      <span style="font-size: 30px;"> SIIPLAS V3.0</span>                      
                    </h2>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.login-logo -->
            <div class="col-lg-8 col-xl-9 d-flex align-items-center justify-content-center">
              <div class="row justify-content-center w-100 mt-4 mt-lg-0">
                <div class="col-lg-6 col-xl-4 col-md-9">
                  <div class="card" id="loginform">
                    <div class="card-body">
                      <h2>Lista de Archivos</h2>
                      <hr>


                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>';

        return view('index/archivos', $data);
    }







    public function logout(){
        $session = session();
        $session->destroy();
        return redirect()->to('/login');
    }





   
}
