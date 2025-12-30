<?php

namespace App\Controllers;
use CodeIgniter\Controller;
use CodeIgniter\API\ResponseTrait; // Importar si se usa response
use App\Models\Index\IndexModel;
use App\Models\Index\SolicitudesPswModel;
use App\Libraries\Calculadora;

class User extends BaseController{
    protected $IndexModel;
    protected $SolicitudesPswModel; // Declara el nuevo modelo

    public function __construct(){
        $this->IndexModel = new IndexModel();
        $this->SolicitudesPswModel = new SolicitudesPswModel(); // Inicializa el nuevo modelo
    }


    /// index
    public function index(){
        $data['formulario']=$this->form_login();
        if (session()->get('isLoggedIn')) {
            return redirect()->to(base_url('dashboard'));
        }
        
        helper(['form']);
        return view('index/login', $data);
    }


    //// formulario Login
    public function form_login(){
       // $tabla='HOLA MUNDO';
        $captcha= $this->generar_captcha(array(0,1,2,3,4,5,6,7,8,9,'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R'),4);
        
        $data['cod_captcha']=$captcha;
        $data['captcha']=md5($captcha);

         $tabla='
            <style>
                .caja {
                font-family: sans-serif;
                font-size: 28px;
                font-weight: 100;
                color: #000000;
                background: #d1d9dc;
                margin: 0 0 15px;
                overflow: hidden;
                padding: 3px;
                }

                #loading {
                    display: none;
                    position: fixed;
                    left: 50%;
                    top: 50%;
                    transform: translate(-50%, -50%);
                    font-size: 24px;
                    z-index: 1000;
                }

                #loadingpws {
                    display: none;
                    position: fixed;
                    left: 50%;
                    top: 50%;
                    transform: translate(-50%, -50%);
                    font-size: 24px;
                    z-index: 1000;
                }
            </style>';
        
        $tabla.='
        <div id="kc-content-wrapper">
        <input name="base" type="hidden" value="'.base_url().'">
        <div class="background-siat-login overflow-hidden d-flex justify-content-center align-items-center" style="height: 100vh;">
            <div class="container px-md-5 text-center text-lg-start my-5 ">
                <div class="row gx-lg-5 align-items-center mb-sm-0">
                    <div class="col-lg-6 mb-sm-0 mb-lg-0 text-center mt-lg-0" style="z-index: 10">
                        <div class="imgSiat">
                            <picture>
                                <source srcset="'.base_url().'Img/login/logo_CNS_header.png" media="(min-width: 992px)" width="200px" height="auto">
                                <source srcset="'.base_url().'Img/login/logo_CNS_header.png" media="(min-width: 768px)" width="200px" height="auto">
                                <img class="img-fluid animateBolivia" src="'.base_url().'Img/login/logo_CNS_header.png"alt="logoSiatBolivia" width="200px" height="auto">
                            </picture>
                            
                            <h1 class="my-5 display-5 fw-bold ls-tight text-center titleSiat" style="color: hsl(218, 81%, 95%)">
                                Sistema de Planificaci&oacute;n y Seguimiento al POA
                                <br/>
                                <span style="color: #FFFF">SIIPLAS v3.0</span>
                            </h1>
                            
                            <div class="redesSocialesHeader">
                                <a href="https://www.facebook.com/CNS.Bolivia/" target="_blank"><img class="rrss mx-2" src="'.base_url().'Img/login/facebook.svg"/ alt="rrssFacebook"></a>
                                <a href="https://www.instagram.com/cnsbolivia/" target="_blank"><img class="rrss mx-2" src="'.base_url().'Img/login/instagram.svg"/ alt="rrssinstagram"></a>
                                <a href="https://www.youtube.com/channel/UCH8i2IHse60iSiyeYAihomg" target="_blank"><img class="rrss mx-2" src="'.base_url().'Img/login/youtube.svg"/ alt="rrssYoutube"></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-lg-0 position-relative">
                    <br/>
                        <div class="card bg-card">
                            <div class="card-body px-4 py-4 px-md-5">

                                <div id="loading"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>
                                <form role="form" action="'.base_url('login/auth').'" method="post" id="form" class="login-form">
                                    <input type="text" name="tp" id="tp" value="0">
                                    <div align=center>
                                        <b style="color:black;">DEPARTAMENTO NACIONAL DE PLANIFICACIÓN - C.N.S.</b>
                                    </div>

                                    <h5 class="text-center fw-bold my-4 titleBienvenido">Bienvenido/a!</h5>';

                                    if(session()->getFlashdata('errors')):
                                        $tabla.='<div class="alert alert-danger">'.session()->getFlashdata('errors').'</div>';
                                    endif;

                                    $tabla.='
                                    <div class="row align-items-center">
                                        <div class="col">
                                        <div id="form-login-username" class="form-group">      
                                            <input type="radio" name="radio-inline" id="radio0" checked="checked">
                                            <i></i><b>Unidad Administrativa</b></label> &nbsp;&nbsp; 
                                            <input type="radio" name="radio-inline" id="radio1">
                                            <i></i><font color="#146f64"><b>Establecimiento de Salud</b></font></label>
                                        </div>
                                        </div>
                                    </div>

                                    <div class="row align-items-center">
                                        <div class="col">
                                            <div class="form-floating mb-2">
                                                <input tabindex="1" type="text" class="form-control form-input-bg" name="user_name" id="user_name" value="'.old('user_name').'" placeholder="USUARIO" minlength="5" maxlength="20" autocomplete="off" style="text-transform:uppercase;" oninput="this.value = this.value.toUpperCase();">
                                                <label for="user_name">USUARIO SIIPLAS</label>
                                                <div id="usu" class="text-danger text-start" style="font-size:9px;visibility: hidden;">
                                                   <b> Este campo es requerido</b>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto pf-0">
                                            <img src="'.base_url().'Img/login/help.svg" class="tootip" title="USUARIO: Acceso asignado por el Departamento Nacional de Planificación"/>
                                        </div>
                                    </div>

                                    <div class="row align-items-center">
                                        <div class="col">
                                            <div class="form-floating mb-2">
                                                <input tabindex="3" id="password" class="form-control form-input-bg" name="password" id="password" type="password" autocomplete="off" placeholder="CONTRASEÑA" minlength="6" maxlength="50"/>
                                                <label for="password">PASSWORD</label>
                                                <div id="pass" class="text-danger text-start" style="font-size:9px; visibility: hidden;" style="font-size:8px;">
                                                  <b>  Este campo es requerido</b>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto pf-0">
                                            <img src="'.base_url().'Img/login/help.svg" onclick="togglePassword(\'password\')" class="tootip" id="toggleIcon" title="CLAVE DE ACCESO: Acceso asignado por el Departamento Nacional de Planificación"/>
                                        </div>
                                    </div>

                                    <div class="text-center py-3">
                                        <p class="caja" id="refreshs" style="text-align:center"><b>'.$data['cod_captcha'].'</b></p>
                                        <input type="hidden" name="captcha" id="captcha"  value="'.$data['captcha'].'" style="text-transform:uppercase;" oninput="this.value = this.value.toUpperCase();">
                                    </div>

                                    <div class="mb-4">
                                        <input tabindex="4" id="dat_captcha" name="dat_captcha" type="text" class="form-control form-input-bg text-center" placeholder="Ingrese el texto de la imagen" autofocus minlength="4" maxlength="4" >
                                        <div id="cat" class="text-danger text-start" style="font-size:9px; visibility: hidden;" style="font-size:8px;">
                                            <b>  Este campo es requerido</b>
                                        </div>
                                    </div>

                                    <div class="d-grid gap-2 mt-2">
                                        <input tabindex="4" class="btn btn-lg mdl-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" style="width: 100%;" name="login" id="kc-login" type="submit" value="INGRESAR"/>
                                    </div>
                                </form>
                                    <div class="d-flex justify-content-between fs-5 mt-4">
                                        <div class="">
                                            <span><a tabindex="5" href="'.base_url().'password">Olvide mi contraseña</a></span>
                                        </div>
                                        <div class="">
                                            <span><a tabindex="5" href="'.base_url().'documents">Archivos Adjuntos</a></span>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                
                </div>
            </div>
        </div>';

        return $tabla;
    }


    /// Valida login
    public function loginAction(){
        $session = session();
        $model_index = new IndexModel();

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

/*        if($tp==0){ /// Administracion

        }
        else{ /// Establecimiento de salud

        }*/

        $is_valid = $model_index->verificar_loggin($usuario, $password, $captcha,$dat_captcha);
        if($is_valid['bool']==true){
            $conf = $model_index->get_gestion_activo(); /// configuracion gestion activo
            $modulos = $model_index->modulos($conf['ide']); /// modulos

            $userData = [
            'fun_id'    => $is_valid['data']['fun_id'], // Asegúrate de que tu modelo devuelve 'id'
            'user_name'   => $is_valid['data']['fun_nombre'].' '.$is_valid['data']['fun_paterno'].' '.$is_valid['data']['fun_materno'],
            'usuario'   => $is_valid['data']['fun_usuario'],
            'cargo'   => $is_valid['data']['fun_cargo'],
            'credencial_funcionario'   => $is_valid['data']['sw_pass'],
            'fun_estado'   => $is_valid['data']['fun_estado'],
            'com_id'   => $is_valid['data']['cm_id'],
            'dist_id'   => $is_valid['data']['fun_dist'],
            'rol'   => $model_index->get_rol_usuario($is_valid['data']['fun_id']),
            'modulos'   => $modulos,
            'regional'   => $model_index->datos_regional($is_valid['data']['fun_dist']),
            'configuracion'   => $conf,
            'isLoggedIn' => TRUE, // Bandera clave para tus filtros de acceso
            ];
            $session->set($userData); // Guarda la sesión

            // 2. Redirigir al usuario a una página protegida (ej. dashboard)
            return redirect()->to(base_url('dashboard')); 
        }
        else{
            return redirect()->to(base_url('login'))->with('errors', $is_valid['message']);
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




    /// GET CAPTCHA
    public function get_captcha(){
        $captcha= $this->generar_captcha(array(0,1,2,3,4,5,6,7,8,9,'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R'),4);
         
          $result = array(
          'respuesta' => 'correcto',
          'cod_captcha' => $captcha,
          'captcha' => md5($captcha),
        );

        return $this->response->setJSON($result);
    }


    //// GENERAR CAPTCHA
    function generar_captcha($chars,$length){
        $captcha=null;
        for ($i=0; $i <$length ; $i++) { 
            $rand= rand(0,count($chars)-1);
            $captcha .=$chars[$rand];
        }

        return $captcha;
    }


    public function logout(){
        $session = session();
        $session->destroy();
        return redirect()->to('/login');
    }





   
}
