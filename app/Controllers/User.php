<?php

namespace App\Controllers;
use App\Models\Index\IndexModel;
use App\Libraries\Calculadora;

class User extends BaseController{

    /// index
    public function index(){
        // Si el usuario ya está logueado, redirigir al dashboard (o donde corresponda)
       /* $captcha= $this->generar_captcha(array(0,1,2,3,4,5,6,7,8,9,'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R'),4);
        
        $data['base']='<input name="base" type="hidden" value="'.base_url().'">';
        $data['cod_captcha']=$captcha;
        $data['captcha']=md5($captcha);*/
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

                .modal {
                    display: none;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.6);
                }

                .modal2 {
                    display: none;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 250%;
                    height: 150%;
                }

                .modal-content {
                    background: white;
                    width: 400px;
                    margin: 50px auto;
                    padding: 30px;
                    text-align: center;
                    border-radius: 8px;
                }

                .modal-content2 {
                    background: white;
                    width: 600px;
                    margin: 50px auto;
                    padding: 30px;
                    border-radius: 18px;
                }

                .open-btn {
                    background: #4CAF50;
                    color: white;
                    font-size: 16px;
                }

                .close-btn {
                    background: #f44336;
                    color: white;
                }
            </style>';
        
        $tabla.='
        <div id="kc-content-wrapper">
        <input name="base" type="hidden" value="'.base_url().'">
        <div class="background-siat-login overflow-hidden d-flex justify-content-center align-items-center" style="height: 100vh;">
            <div class="container px-md-5 text-center text-lg-start my-5 ">
            <div>
              <a href="#" style="font-size:11px;color: hsl(150, 80%, 90%)" onclick="show2()"><b>ARCHIVOS ADJUNTOS</b></a>
            </div>
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
                                    <input type="hidden" name="tp" id="tp" value="0">
                                    <div align=center>
                                        <b style="color:black;">DEPARTAMENTO NACIONAL DE PLANIFICACIÓN - C.N.S.</b>
                                    </div>

                                    <h5 class="text-center fw-bold my-4 titleBienvenido">Bienvenido/a!</h5>';
                                    if (session()->getFlashdata('error_message')): 
                                        $tabla.='<div class="alert error">'.session()->getFlashdata('error_message').'</div>';
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

                                    <input id="deviceId" class="dOt" name="deviceId">

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

                                    <input id="deviceId" class="dOt" name="deviceId">

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
                                <br>
                                <a href="#" style="color:blue; font-size:11px;" onclick="show()">Olvidaste tu Contraseña?</a>
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
        $dat_captcha = $this->request->getPost('dat_captcha');

        $is_valid = $model_index->verificar_loggin($usuario, $password);
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
            'regional'   => $model_index->datos_regional($is_valid['data']['fun_dist']),
            
            'mes' => $conf['conf_mes'],
            'conf_ajuste_poa' => $conf['conf_ajuste_poa'],
            'estado_notificaciones' => $conf['conf_poa'], /// Estado para las Notificaciones 0:no activo, 1: Habilitado
            'entidad' => $conf['conf_nombre_entidad'],
            'trimestre' => $conf['conf_mes_otro'], /// Trimestre 1,2,3,4
            'verif_ppto' => $conf['ppto_poa'], /// Ppto poa : 0 (Ante proyecto), 1: (Aprobado)
            'conf_poa_estado' => $conf['conf_poa_estado'], /// Estado Poa Estado : 1 (Inicial), 2: (Ajuste), 3: (Aprobado)
            'conf_form4' => $conf['conf_form4'], /// Estado de Registro del formulario N4, 0 (Inactivo), 1 (Activo)
            'conf_form5' => $conf['conf_form5'], /// Estado de Registro del formulario N5, 0 (Inactivo), 1 (Activo)
            'conf_mod_ope' => $conf['conf_mod_ope'], /// Estado de modificacion del formulario N4, 0 (Inactivo), 1 (Activo)
            'conf_mod_req' => $conf['conf_mod_req'], /// Estado de modificacion del formulario N5, 0 (Inactivo), 1 (Activo)
            'conf_certificacion' => $conf['conf_certificacion'], /// Estado de modificacion del formulario N5, 0 (Inactivo), 1 (Activo)
            'conf_psw'=>$conf['conf_psw'],
            'm_id'=>$conf['conf_mes'],
            'g_id'=>$conf['conf_gestion'],
            //'desc_mes' => $this->mes_texto($//[0]['conf_mes']),
            'abrev_sistema' => 'SIIPLAS V3.0',
            'direccion' => 'DEPARTAMENTO NACIONAL DE PLANIFICACI&Oacute;N',
            'sistema' => 'SISTEMA DE PLANIFICACI&Oacute;N Y SEGUIMIENTO POA - SIIPLAS V3.0',
            'sistema_pie' => 'SIIPLAS - Sistema de Planificaci&oacute;n y Seguimiento POA',

            'isLoggedIn' => TRUE, // Bandera clave para tus filtros de acceso
            ];
        
            $session->set($userData); // Guarda la sesión

            // 2. Redirigir al usuario a una página protegida (ej. dashboard)
            return redirect()->to(base_url('dashboard')); 
        }
        else{
            return redirect()->to(base_url('login'))->with('error_message', $is_valid['message']); 
        }
      
    }







    /// GET CAPTCHA
    public function get_captcha(){
     // if($this->request->isAJAX()){
       //   $post = $this->input->post();

          $captcha= $this->generar_captcha(array(0,1,2,3,4,5,6,7,8,9,'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R'),4);
         
          $result = array(
          'respuesta' => 'correcto',
          'cod_captcha' => $captcha,
          'captcha' => md5($captcha),
        );
          echo json_encode($result);

      //}else{
        //show_404();
      //}
    }

    public function get_captcha2()
    {
        // En CI4, accedes a la solicitud mediante $this->request
        if ($this->request->isAJAX()) {
            // $post = $this->request->getPost(); // Puedes usar esto si necesitas datos POST específicos

            // Asegúrate de que la función generar_captcha() esté definida en alguna parte
            $captcha_word = $this->generar_captcha(
                array(0,1,2,3,4,5,6,7,8,9,'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R'),
                4
            );
            
            $result = array(
                'respuesta' => 'correcto',
                'cod_captcha' => $captcha_word,   // El texto plano para mostrar visualmente
                'captcha' => md5($captcha_word), // El texto cifrado para validación
            );
            
            // Usa el método respond() del ResponseTrait para enviar la respuesta JSON
            return $this->respond($result);

        } else {
            // Usa show_404() o throw new \CodeIgniter\Exceptions\PageNotFoundException();
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }
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
