<?php

namespace App\Controllers;
use App\Models\Index\IndexModel;
use App\Libraries\Calculadora;

class User extends BaseController{

    /// index
    public function index(){
        // Si el usuario ya está logueado, redirigir al dashboard (o donde corresponda)
        $captcha= $this->generar_captcha(array(0,1,2,3,4,5,6,7,8,9,'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R'),4);
        
        $data['base']='<input name="base" type="hidden" value="'.base_url().'">';
        $data['cod_captcha']=$captcha;
        $data['captcha']=md5($captcha);
        $data['formulario']=$this->form_login();



        if (session()->get('isLoggedIn')) {
            return redirect()->to(base_url('dashboard'));
        }
        


        helper(['form']);
        return view('index/login', $data);
    }


    //// formulario Login
    public function form_login(){
        $tabla='HOLA MUNDO';
        $tabla.='';


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
      if($this->input->is_ajax_request()){
          $post = $this->input->post();
          $captcha= $this->generar_captcha(array(0,1,2,3,4,5,6,7,8,9,'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R'),4);
         
          $result = array(
          'respuesta' => 'correcto',
          'cod_captcha' => $captcha,
          'captcha' => md5($captcha),
        );
          
        echo json_encode($result);
 
      }else{
        show_404();
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
