<?php

namespace App\Controllers;
use CodeIgniter\Controller;
use CodeIgniter\API\ResponseTrait; // Importar si se usa response
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
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
            $modulos = $model_index->modulos($conf['ide'],$is_valid['data']['tp_adm']); /// modulos
            $view_modulos=$this->Modulos_disponibles($modulos); /// vista modulos Cabecera
            $view_modulos_Sidebar=$this->Modulos_disponibles_Sidebar($modulos,$is_valid['data']['fun_nombre'].' '.$is_valid['data']['fun_paterno'].' '.$is_valid['data']['fun_materno'],$is_valid['data']['fun_cargo'],$conf['conf_abrev_sistema']); /// vista modulos Cabecera Sidebar
            $userData = [
            'fun_id'    => $is_valid['data']['fun_id'], // Asegúrate de que tu modelo devuelve 'id'
            'user_name'   => $is_valid['data']['fun_nombre'].' '.$is_valid['data']['fun_paterno'].' '.$is_valid['data']['fun_materno'],
            'usuario'   => $is_valid['data']['fun_usuario'],
            'cargo'   => $is_valid['data']['fun_cargo'],
            'credencial_funcionario'   => $is_valid['data']['sw_pass'],
            'fun_estado'   => $is_valid['data']['fun_estado'],
            'com_id'   => $is_valid['data']['cm_id'],
            'dist_id'   => $is_valid['data']['fun_dist'],
            'tp_adm'   => $is_valid['data']['tp_adm'],
            'rol'   => $model_index->get_rol_usuario($is_valid['data']['fun_id']),
            'configuracion'   => $conf,
            'modulos'   => $modulos,
            'view_modulos'   => $view_modulos,
            'view_modulos_sidebar'   => $view_modulos_Sidebar,
            'view_cabecera'   => $this->Cabecera_sistema($is_valid['data']['fun_nombre'].' '.$is_valid['data']['fun_paterno'].' '.$is_valid['data']['fun_materno'],$is_valid['data']['fun_cargo'],$conf['conf_abrev_sistema'],$conf['conf_img']),
            'view_cabecera_layout'   => $this->Cabecera_sistema_layout($is_valid['data']['fun_nombre'].' '.$is_valid['data']['fun_paterno'].' '.$is_valid['data']['fun_materno'],$is_valid['data']['fun_cargo'],$conf['conf_abrev_sistema'],$conf['conf_img']),
            'view_menu_izquierdo'   => $this->Menu_izquierdo(), /// menu izquierdo
            'view_bienvenida'   => $this->bienvenida($conf['conf_abrev_sistema'],$conf['conf_unidad_resp']), /// bienvenida
            'regional'   => $model_index->datos_regional($is_valid['data']['fun_dist']),
            
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

    /// Bienvenida
    public function bienvenida($sistema,$unidad_responsable){
        $tabla='';
        $tabla.='
          <div class="toast toast-onload align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-body hstack align-items-start gap-6">
              <i class="ti ti-alert-circle fs-6"></i>
              <div>
                <h5 class="text-white fs-3 mb-1">Bienvenidos</h5>
                <h6 class="text-white fs-2 mb-0"><b>Sistema '.$sistema.'</b><br>'.$unidad_responsable.'</h6>
              </div>
              <button type="button" class="btn-close btn-close-white fs-2 m-0 ms-auto shadow-none" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
          </div>';
        return $tabla;
    }


    /// Menu Izquierdo desplazable
    public function Menu_izquierdo(){
        $tabla='';
        $tabla.='
        <button class="btn btn-primary p-3 rounded-circle d-flex align-items-center justify-content-center customizer-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
          <i class="icon ti ti-settings fs-7"></i>
        </button>
        <div class="offcanvas customizer offcanvas-end" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
          <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
            <h4 class="offcanvas-title fw-semibold" id="offcanvasExampleLabel">
              DESCARGAS
            </h4>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body h-n80" data-simplebar>
            <h6 class="fw-semibold fs-4 mb-2">Archivos</h6>
                aqui va el listado de archivos
          </div>
        </div> ';


        return $tabla;
    }

    /// Modulos Habilitados Sidebar
    public function Modulos_disponibles_Sidebar($modulos,$responsable,$cargo,$sistema){
        $model_index = new IndexModel();
        $tabla='';
        $tabla.='
        <aside class="left-sidebar with-vertical">
          <!-- ---------------------------------- -->
          <!-- Start Vertical Layout Sidebar -->
          <!-- ---------------------------------- -->
          <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="index.html" class="text-nowrap logo-img">
              '.$sistema.'
            </a>
            <a href="javascript:void(0)" class="sidebartoggler ms-auto text-decoration-none fs-5 d-block d-xl-none">
              <i class="ti ti-x"></i>
            </a>
          </div>

          <div class="scroll-sidebar" data-simplebar>
            <!-- Sidebar navigation-->
            <nav class="sidebar-nav">
              <ul id="sidebarnav" class="mb-0">

                <!-- ============================= -->
                <!-- Home -->
                <!-- ============================= -->
                <li class="nav-small-cap">
                  <iconify-icon icon="solar:menu-dots-bold-duotone" class="nav-small-cap-icon fs-5"></iconify-icon>
                  <span class="hide-menu">HOME</span>
                </li>
                <li class="sidebar-item">
                  <a class="sidebar-link sidebar-link primary-hover-bg" href="" id="get-url" aria-expanded="false">
                    <span class="aside-icon p-2 bg-primary-subtle rounded-1">
                      <iconify-icon icon="solar:screencast-2-line-duotone" class="fs-6"></iconify-icon>
                    </span>
                    <span class="hide-menu ps-1">DASHBOARD</span>
                  </a>
                </li>';

                foreach($modulos as $row){ 
                    $tabla.='
                    <li class="nav-small-cap">
                      <iconify-icon icon="solar:menu-dots-bold-duotone" class="nav-small-cap-icon fs-5"></iconify-icon>
                      <span class="hide-menu">'.strtoupper($row['mod_descripcion']).'</span>
                    </li>';
                        $sub_menu=$model_index->sub_modulos($row['mod_id']);
                        foreach($sub_menu as $row2){
                            $tabla.='
                            <li class="sidebar-item">
                              <a class="sidebar-link secondary-hover-bg" href="'.base_url().''.$row2['sub_menu_ruta'].'" aria-expanded="false">
                                <span class="aside-icon p-2 bg-secondary-subtle rounded-1">
                                  <iconify-icon icon="solar:notification-unread-lines-line-duotone" class="fs-6"></iconify-icon>
                                </span>
                                <span class="hide-menu ps-1">'.$row2['sub_menu_descripcion'].'</span>
                              </a>
                            </li>';
                        }
                }
                $tabla.='
          </ul>
        </nav>
        <!-- End Sidebar navigation -->
      </div>

      <div class=" fixed-profile mx-3 mt-3">
        <div class="card bg-primary-subtle mb-0 shadow-none">
          <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between gap-3">
              <div class="d-flex align-items-center gap-3">
                <img src="'.base_url('Img/plantillaImg/user-1.jpg').'" width="45" height="45" class="img-fluid rounded-circle" alt="spike-img" />
                <div>
                  <h5 class="mb-1">'.$responsable.'</h5>
                  <p class="mb-0">'.$cargo.'</p>
                </div>
              </div>
              <a href="'.base_url().'logout" class="position-relative" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Logout">
                <iconify-icon icon="solar:logout-line-duotone" class="fs-8"></iconify-icon>
              </a>
            </div>
          </div>
        </div>
      </div>

    </aside>';

        return $tabla;
    }


    /// Modulos Habilitados Cabecera
    public function Modulos_disponibles($modulos){
        $model_index = new IndexModel();
        $tabla='';
        $tabla.='
        <aside class="left-sidebar with-horizontal">
        <!-- Sidebar scroll-->
        <div>
          <!-- Sidebar navigation-->
          <nav id="sidebarnavh" class="sidebar-nav scroll-sidebar container-fluid">
            <ul id="sidebarnav">
              <!-- ============================= -->
              <!-- Home -->
              <!-- ============================= -->
              <li class="nav-small-cap">
                <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                <span class="hide-menu">Home</span>
              </li>
              <!-- =================== -->
              <!-- Dashboard -->
              <!-- =================== -->
              <li class="sidebar-item">
                <a class="sidebar-link sidebar-link primary-hover-bg" href="'.base_url().'dashboard" aria-expanded="false">
                  <iconify-icon icon="solar:atom-line-duotone" class="fs-6 aside-icon"></iconify-icon>
                  <span class="hide-menu ps-1"><b>Dashboard</b></span>
                </a>
              </li>';

                foreach($modulos as $row){ 
                  $tabla.='
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">'.$row['mod_descripcion'].'</span>
                      </li>
                      <li class="sidebar-item">
                        <a class="sidebar-link has-arrow warning-hover-bg" href="javascript:void(0)" aria-expanded="false">
                          <iconify-icon '.$row['icono_mod'].' class="fs-6 aside-icon"></iconify-icon>
                          <span class="hide-menu ps-1"> <b>'.$row['mod_descripcion'].'</b></span>
                        </a>
                        <ul aria-expanded="false" class="collapse first-level">';
                        $sub_menu=$model_index->sub_modulos($row['mod_id']);
                        foreach($sub_menu as $row2){
                            $tabla.='
                            <li class="sidebar-item">
                                <a href="'.base_url().''.$row2['sub_menu_ruta'].'" class="sidebar-link">
                                  <span class="sidebar-icon"></span>
                                  <span class="hide-menu">'.$row2['sub_menu_descripcion'].'</span>
                                </a>
                            </li>';
                        }
                        $tabla.='
                        </ul>
                    </li>';
                }
              $tabla.='
              
            </ul>
        </div>
      </aside>';

        return $tabla;
    }


    /// Cabecera Sistema
    public function Cabecera_sistema($responsable,$cargo,$sistema,$img){
    $tabla='';
    $tabla.='<div class="app-header with-horizontal">
              <nav class="navbar navbar-expand-xl container-fluid p-0">
                <ul class="navbar-nav">
                  <li class="nav-item d-none d-xl-block">
                    <a href="#" class="text-nowrap nav-link" style="color:#ffffff; font-size: 25px;">
                      <img src="'.base_url($img).'" class="dark-logo" width="35" alt="spike-img"/>&nbsp;&nbsp;<b>'.$sistema.'</b>
                    </a>
                  </li>
                </ul>
                <a class="navbar-toggler p-0 border-0" href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="p-2">
                    <i class="ti ti-dots fs-7"></i>
                  </span>
                </a>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                  <div class="d-flex align-items-center justify-content-between">
                    <a href="javascript:void(0)" class="nav-link d-flex d-lg-none align-items-center justify-content-center" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobilenavbar" aria-controls="offcanvasWithBothOptions">
                      <div class="nav-icon-hover-bg rounded-circle ">
                        <i class="ti ti-align-justified fs-7"></i>
                      </div>
                    </a>


                    <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-center">
                      <li class="nav-item dropdown">
                        <a class="nav-link position-relative ms-6" href="javascript:void(0)" id="drop1" aria-expanded="false">
                          <div class="d-flex align-items-center flex-shrink-0">
                            <div class="user-profile me-sm-3 me-2">
                              <img src="'.base_url('Img/plantillaImg/user-1.jpg').'" width="40" class="rounded-circle" alt="spike-img">
                            </div>
                            <span class="d-sm-none d-block"><iconify-icon icon="solar:alt-arrow-down-line-duotone"></iconify-icon></span>

                            <div class="d-none d-sm-block">
                              <h6 class="fs-4 mb-1 profile-name" style="color:white; font-size:8px;">
                                '.$responsable.'
                              </h6>
                              <p class="fs-3 lh-base mb-0 profile-subtext" style="color:white; font-size:5px;">
                                '.$cargo.' 
                              </p>
                            </div>
                          </div>
                        </a>

                        <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop1">
                          <div class="profile-dropdown position-relative" data-simplebar>
                            <div class="d-flex align-items-center justify-content-between pt-3 px-7">
                              <h3 class="mb-0 fs-5">Perfil Usuario</h3>

                            </div>

                            <div class="d-flex align-items-center mx-7 py-9 border-bottom">
                              <img src="'.base_url('Img/plantillaImg/user-1.jpg').'" alt="user" width="90" class="rounded-circle" />
                              <div class="ms-4">
                                <h4 class="mb-0 fs-5 fw-normal">'.$responsable.'</h4>
                                <span class="text-muted">'.$cargo.'</span>
                                <p class="text-muted mb-0 mt-1 d-flex align-items-center">
                                  <iconify-icon icon="solar:mailbox-line-duotone" class="fs-4 me-1"></iconify-icon>
                                  wilmer.mendoza@cns.gob
                                </p>
                              </div>
                            </div>

                            <div class="py-6 px-7 mb-1">
                              <a href="'.base_url().'logout" class="btn btn-primary w-100">Cerrar Sesión</a>
                            </div>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>
              </nav>
            </div>';

    return $tabla;
    }


/// Cabecera Superior Sistema
    public function Cabecera_sistema_layout($responsable,$cargo,$sistema,$img){
    $tabla='';
    $tabla.='<div class="with-vertical">
              <!-- Start Vertical Layout Header -->
              <!-- ---------------------------------- -->
              <nav class="navbar navbar-expand-lg p-0">
                <ul class="navbar-nav">
                  <li class="nav-item nav-icon-hover-bg rounded-circle">
                    <a class="nav-link sidebartoggler" id="headerCollapse" href="javascript:void(0)">
                      <iconify-icon icon="solar:list-bold-duotone" class="fs-7"></iconify-icon>
                    </a>
                  </li>
                </ul>


                <div class="d-block d-lg-none py-3" style="color:white">
                  <img src="'.base_url($img).'" class="dark-logo" width="40" alt="spike-img" />&nbsp;&nbsp;<b>PoaWeb-CNS - Departamento Nacional de Planificación</b>
                </div>


                <a class="navbar-toggler p-0 border-0" href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="p-2">
                    <i class="ti ti-dots fs-7"></i>
                  </span>
                </a>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                  <div class="d-flex align-items-center justify-content-between">
                    <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-center">

                      <li class="nav-item dropdown">
                        <a class="nav-link position-relative ms-6" href="javascript:void(0)" id="drop1" aria-expanded="false">
                          <div class="d-flex align-items-center flex-shrink-0">
                            <div class="user-profile me-sm-3 me-2">
                              <img src="'.base_url('Img/plantillaImg/user-1.jpg').'" width="40" class="rounded-circle" alt="spike-img">
                            </div>
                            <span class="d-sm-none d-block"><iconify-icon icon="solar:alt-arrow-down-line-duotone"></iconify-icon></span>

                            <div class="d-none d-sm-block">
                              <h6 class="fs-4 mb-1 profile-name" >
                                '.$responsable.'
                              </h6>
                              <p class="fs-3 lh-base mb-0 profile-subtext">
                                '.$cargo.'
                              </p>
                            </div>
                          </div>
                        </a>
                        <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop1">
                          <div class="profile-dropdown position-relative" data-simplebar>
                            <div class="d-flex align-items-center justify-content-between pt-3 px-7">
                              <h3 class="mb-0 fs-5">Perfil Usuario</h3>
                            </div>

                            <div class="d-flex align-items-center mx-7 py-9 border-bottom">
                              <img src="'.base_url('Img/plantillaImg/user-1.jpg').'" alt="user" width="90" class="rounded-circle" />
                              <div class="ms-4">
                                <h4 class="mb-0 fs-5 fw-normal" >'.$responsable.'</h4>
                                <span class="text-muted" style="color:white;">'.$cargo.'</span>
                                <p class="text-muted mb-0 mt-1 d-flex align-items-center">
                                  <iconify-icon icon="solar:mailbox-line-duotone" class="fs-4 me-1"></iconify-icon>
                                  info@spike.com
                                </p>
                              </div>
                            </div>
                            <div class="py-6 px-7 mb-1">
                              <a href="'.base_url().'logout" class="btn btn-primary w-100">Cerrar Sesión</a>
                            </div>
                          </div>
                        </div>
                      </li>

                    </ul>
                  </div>
                </div>
              </nav>
            </div>';

    return $tabla;
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
