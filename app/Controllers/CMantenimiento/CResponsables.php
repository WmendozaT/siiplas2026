<?php
namespace App\Controllers\CMantenimiento;
use App\Controllers\BaseController; 
// Importa las clases necesarias para initController
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\Model_Mantenimiento\Model_funcionarios;
use App\Models\Model_Mantenimiento\Model_regional;
use App\Libraries\Libreria_Responsable;
use App\Libraries\Libreria_Index;



class CResponsables extends BaseController{
    protected $Model_funcionarios;
    protected $Model_regional;
    protected $session;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger){
        // LLAMADA OBLIGATORIA al initController del padre (DESCOMENTADA)
        parent::initController($request, $response, $logger); 

    $this->session = \Config\Services::session(); 
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



    /// Vista Lista Reponsables POA
    public function lista_responsables(){
        $miLib_resp = new Libreria_Responsable();
        $model_funcionario = new Model_funcionarios();


        $data['formulario']=$miLib_resp->responsables_poa(); /// lista de responsables (Libreria Responsable)
        return view('View_mantenimiento/View_responsables/view_funcionarios',$data);
    }


    /// Vista Lista Reponsables-Seguimiento POA
    public function lista_responsables_seguimientopoa(){
        $miLib_resp = new Libreria_Responsable();
        $model_funcionario = new Model_funcionarios();

        $data['formulario']=$miLib_resp->responsables_seguimiento_poa(); /// lista de responsables se Seguimiento POA (Libreria Responsable)
        return view('View_mantenimiento/View_responsables/view_funcionarios',$data);
    }


    /// Vista Form Update Responsable POA
    public function update_responsable($id){
        $miLib_resp = new Libreria_Responsable();
        $model_funcionario = new Model_funcionarios();
        $get_rep=$model_funcionario->get_responsablePoa($id);
                
        if (empty($get_rep)) {
            $data['formulario']='SIN REGISTRO POR MOSTRAR !!!';
        }
        else{
            $data['formulario']=$miLib_resp->get_responsables_poa($get_rep); /// formulario de edicion de responsable (Libreria Responsable)
        }

        return view('View_mantenimiento/View_responsables/view_funcionarios',$data);
    }


    /// Vista Form Update Responsable-Seguimiento POA
    public function form_update_segpoa($id){
        $miLib_resp = new Libreria_Responsable();
        $model_funcionario = new Model_funcionarios();
        $get_rep=$model_funcionario->get_responsablePoa($id);
                
        if (empty($get_rep)) {
            $data['formulario']='SIN REGISTRO POR MOSTRAR !!!';
        }
        else{
            $data['formulario']=$miLib_resp->get_responsables_seguimiento_poa($get_rep); /// formulario de edicion de responsable-Seguimiento (Libreria Responsable)
        }

        return view('View_mantenimiento/View_responsables/view_funcionarios',$data);
    }

    /// Vista Form Add Responsable POA
    public function new_responsables(){
        $miLib_resp = new Libreria_Responsable();
        $model_funcionario = new Model_funcionarios();
                
        $data['formulario']=$miLib_resp->form_add_responsables_poa(); /// formulario de adicion de responsable (Libreria Responsable)
        return view('View_mantenimiento/View_responsables/view_funcionarios',$data);
     //   phpinfo();
    }

    /// Vista Form Add Responsable - Seguimiento POA
    public function new_responsables_segpoa(){
        $miLib_resp = new Libreria_Responsable();
        $model_funcionario = new Model_funcionarios();
                
        $data['formulario']=$miLib_resp->form_add_responsables_seguimiento_poa(); /// formulario de adicion de responsable Seguimiento POA (Libreria Responsable)
        return view('View_mantenimiento/View_responsables/view_funcionarios',$data);
     //   phpinfo();
    }



//// Update Permisos 
public function update_permisos_responsable() {
    $db = \Config\Database::connect();
    $model_funcionario = new Model_funcionarios();
    $session = session(); // <--- IMPORTANTE: Definir la variable de sesión

    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Acceso no permitido']);
    }

    $id      = $this->request->getPost('id');
    $columna = $this->request->getPost('columna');
    $valor   = $this->request->getPost('valor');

    $columnasPermitidas = ['tp_adm', 'conf_mod_form4', 'conf_mod_form5', 'conf_mod_ppto', 'conf_cert_poa', 'conf_eval_poa', 'conf_cert_digital', 'sw_pass'];

    if (!in_array($columna, $columnasPermitidas)) {
        return $this->response->setJSON([
            'status' => 'error', 
            'message' => 'Columna no permitida',
            'token'  => csrf_hash()
        ]);
    }

    $data = [$columna => $valor];

    // Ejecutar actualización
    $resultado = $db->table('funcionario')
                    ->where('fun_id', $id)
                    ->update($data);

    if ($resultado) {
        // Solo actualizamos la sesión si el ID editado es el mismo que está logueado
        if ($session->get('fun_id') == $id) {
            $funcionario = $model_funcionario->get_responsablePoa($id);
            
            $userData = [
                'funcionario' => $funcionario,
                // No es necesario setear 'isLoggedIn' de nuevo si ya existe
            ];
            $session->set($userData); 
        }

        return $this->response->setJSON([
            'status' => 'success',
            'token'  => csrf_hash()
        ]);
    }

    return $this->response->setJSON([
        'status' => 'error',
        'message' => 'No se pudo actualizar',
        'token'  => csrf_hash()
    ]);
}





  /// Formulario 

  /// Valida Add Responsable POA
  public function Add_resp() {
    $db = \Config\Database::connect(); 
    try {
        $model_funcionario = new Model_funcionarios();

        $data = [
            'fun_nombre'   => strtoupper($this->request->getPost('fn_nom')),
            'car_id'  => 0,
            'fun_paterno'  => strtoupper($this->request->getPost('fn_pt')),
            'fun_materno'  => strtoupper($this->request->getPost('fn_mt')),
            'fun_ci'       => $this->request->getPost('fn_ci'),
            'fun_telefono' => $this->request->getPost('fn_fono'),
            'fun_cargo'    => strtoupper($this->request->getPost('fn_cargo')),
            'fun_adm'      => $this->request->getPost('tp_adm1'),
            'fun_dist'     => $this->request->getPost('dist_id'),
            'uni_id'       => $this->request->getPost('uni_id'),
            'fun_usuario'  => strtoupper($this->request->getPost('fn_usu')),
        ];

        $email_destino = 'mendozatrujillowilmer@gmail.com'; // Asegúrate de capturar el correo del formulari
        $usuario = $this->request->getPost('fn_usu');
        $pass = $this->request->getPost('fun_password');

        if (!empty($pass)) {
          // 1. Hashear la contraseña
          $data['fun_password'] = password_hash($pass, PASSWORD_BCRYPT);

          // 2. PRIMERO insertar el funcionario para generar el ID
            $db->table('funcionario')->insert($data);
            $id_generado = $db->insertID(); // Ahora sí tenemos el ID

            // 3. SEGUNDO insertar en el historial usando el ID generado
            $db->table('historial_psw')->insert([
                'fun_id'        => $id_generado, // Usamos la variable correcta
                'fun_apassword' => $pass
            ]);

            // --- INICIO ENVÍO DE CORREO ---
            $this->enviarCredenciales($email_destino, $usuario, $pass);
            // --- FIN ENVÍO DE CORREO ---

            return redirect()->to(base_url('mnt/responsables'))
                             ->with('success', 'Datos guardados correctamente.');
        } else {
            return redirect()->back()->withInput()->with('error', 'La contraseña es obligatoria.');
        }

    } catch (Exception $e) {
        // 5. Manejo de la excepción: Loguear el error y avisar al usuario
        log_message('error', 'Error en update_resp: ' . $e->getMessage());
        
        return redirect()->back()->withInput()->with('error', 'Ocurrió un error inesperado: ' . $e->getMessage());
    }
  }


  /// Valida Add Responsable-Seguimiento POA
  public function Add_segpoa() {
    $db = \Config\Database::connect(); 
    try {
        $model_funcionario = new Model_funcionarios();
        $get_proy=$model_funcionario->get_AperturasxRegional($this->request->getPost('proy_id'));

        $data = [
            'fun_nombre'   => strtoupper($this->request->getPost('fn_usu')),
            'car_id'  => 0,
            'fun_paterno'  => 'CNS',
            'fun_materno'  => 'CNS',
            'fun_cargo'    => 'SEGUIMIENTO POA',
            'fun_adm'      => 2,
            'fun_dist'     => $get_proy['dist_id'],
            'uni_id'       => 0,
            'cm_id'  => $this->request->getPost('com_id'),
            'sw_pass'  => 1,
            'fun_usuario'  => strtoupper($this->request->getPost('fn_usu')),
        ];

        $email_destino = 'mendozatrujillowilmer@gmail.com'; // Asegúrate de capturar el correo del formulari
        $usuario = $this->request->getPost('fn_usu');
        $pass = $this->request->getPost('fun_password');

        if (!empty($pass)) {
          // 1. Hashear la contraseña
          $data['fun_password'] = password_hash($pass, PASSWORD_BCRYPT);

          // 2. PRIMERO insertar el funcionario para generar el ID
            $db->table('funcionario')->insert($data);
            $id_generado = $db->insertID(); // Ahora sí tenemos el ID

            // 3. SEGUNDO insertar en el historial usando el ID generado
            $db->table('historial_psw')->insert([
                'fun_id'        => $id_generado, // Usamos la variable correcta
                'fun_apassword' => $pass
            ]);

            // --- INICIO ENVÍO DE CORREO ---
            //$this->enviarCredenciales($email_destino, $usuario, $pass);
            // --- FIN ENVÍO DE CORREO ---

            return redirect()->to(base_url('mnt/resp_seguimientopoa'))
                             ->with('success', 'Datos guardados correctamente.');
        } else {
            return redirect()->back()->withInput()->with('error', 'La contraseña es obligatoria.');
        }

    } catch (Exception $e) {
        // 5. Manejo de la excepción: Loguear el error y avisar al usuario
        log_message('error', 'Error en Formulario: ' . $e->getMessage());
        
        return redirect()->back()->withInput()->with('error', 'Ocurrió un error inesperado: ' . $e->getMessage());
    }
  }


/// Envia Usuario y Password a Correo electronico
  private function enviarCredenciales($para, $usuario, $password) {
    $email = \Config\Services::email();
    $email->setTo($para);
    $email->setSubject('Acceso al Sistema');
    $email->setMessage("Usuario: $usuario - Clave: $password");
    return $email->send();
  }

/*private function enviarCredenciales3($para, $usuario, $password) {
    $email = \Config\Services::email();
    
    $config = [
        'protocol'    => 'smtp',
        // AJUSTE 1: Agregar ssl:// al host fuerza el inicio cifrado inmediato
        'SMTPHost'    => 'ssl://smtp.gmail.com', 
        'SMTPUser'    => 'siiplas.dnplanificacion@gmail.com',
        'SMTPPass'    => 'fmmgmikcadgrncsk',
        'SMTPPort'    => 465,
        // AJUSTE 2: En CI4, si usas puerto 465, algunos recomiendan dejar esto vacío 
        // o como 'ssl' para evitar conflictos con el prefijo ssl:// del host
        'SMTPCrypto'  => 'ssl', 
        'mailType'    => 'html',
        'newline'     => "\r\n",
        'CRLF'        => "\r\n",
        'SMTPOptions' => [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
                // AJUSTE 3: Forzar TLS 1.2 ayuda a estabilizar la conexión en 2026
                'crypto_method'     => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT,
            ],
        ],
    ];

    $email->initialize($config);
    $email->setFrom('siiplas.dnplanificacion@gmail.com', 'Sistema POA');
    $email->setTo($para);
    $email->setSubject('Acceso al Sistema');
    $email->setMessage("Usuario: $usuario - Clave: $password");

    return $email->send();
}*/

    /// Exportar Listado en Excel
public function exportar_responsables(){
    $model_funcionario = new Model_funcionarios();
    $responsables=$model_funcionario->obtenerFuncionariosActivos();

    $filename = "Listado_Responsables_" . date('Ymd_His') . ".xls";

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
    echo '<table border="1" style="font-family: Arial, sans-serif; border-collapse: collapse; width: 100%;">';
    echo '<tr><th colspan="6" style="font-size: 18px; height: 40px; background-color: #1F4E78; color: #FFFFFF;">LISTADO DE RESPONSABLES POA</th></tr>';
    echo '<thead>
            <tr style="background-color: #D9D9D9; font-weight: bold; text-align: center; border: 1px solid #000;">
                <th style="width: 50px;">#</th>
                <th style="width: 300px;">RESPONSABLE POA</th>
                <th style="width: 250px;">UNIDAD DEPENDIENTE</th>
                <th style="width: 150px;">USUARIO</th>
                <th style="width: 150px;">ADMINISTRACION</th>
                <th style="width: 150px;">DISTRITAL</th>
            </tr>
          </thead>';
    echo '<tbody>';
    
    $nro = 0;
    foreach ($responsables as $row) {
        $nro++;
        $colorFila = ($nro % 2 == 0) ? '#F2F2F2' : '#FFFFFF';
        echo '<tr style="background-color: '.$colorFila.';">';
        echo '  <td style="text-align: center; border: 1px solid #CCC;">' . $nro . '</td>';
        echo '  <td style="border: 1px solid #CCC; padding: 5px;">' . mb_strtoupper($row['fun_nombre'] . ' ' . $row['fun_paterno'] . ' ' . $row['fun_materno']) . '</td>';
        echo '  <td style="border: 1px solid #CCC; padding: 5px;">' . $row['uni_unidad'] . '</td>';
        echo '  <td style="border: 1px solid #CCC; text-align: center;">' . $row['fun_usuario'] . '</td>';
        echo '  <td style="border: 1px solid #CCC; text-align: center;">' . $row['adm'] . '</td>';
        echo '  <td style="border: 1px solid #CCC; text-align: center;">' . $row['dist_distrital'] . '</td>';
        echo '</tr>';
    }
    
    echo '  </tbody>';
    echo '</table>';

    // 5. UN SOLO EXIT AL FINAL
    exit; 
}


  /// Valida Form Update Responsable
  public function Update_resp() {
    $db = \Config\Database::connect(); 
    $session = session(); // <--- IMPORTANTE: Definir la variable de sesión
    try {
      $model_funcionario = new Model_funcionarios();
     // $datos = $this->request->getPost();
      
      $id=$this->request->getPost('fun_id');

      if (!$id) {
        throw new \Exception("ID de funcionario no proporcionado.");
      }

      $data = [
            'fun_nombre'   => strtoupper($this->request->getPost('fn_nom')),
            'fun_paterno'  => strtoupper($this->request->getPost('fn_pt')),
            'fun_materno'  => strtoupper($this->request->getPost('fn_mt')),
            'fun_ci'       => $this->request->getPost('fn_ci'),
            'fun_telefono' => $this->request->getPost('fn_fono'),
            'fun_cargo'    => strtoupper($this->request->getPost('fn_cargo')),
            'fun_adm'      => $this->request->getPost('tp_adm'),
            'fun_dist'      => $this->request->getPost('dist_id'),
            'uni_id'       => $this->request->getPost('uni_id'),
            'conf_img'       => $this->request->getPost('img_id'),
            'fun_usuario'  => strtoupper($this->request->getPost('fn_usu')),
        ];

      $pass = $this->request->getPost('fun_password');
      if (!empty($pass)) {
          $data['fun_password'] = $pass;

          $pass_ini=$model_funcionario->get_pwd($id);

          if(count($pass_ini)==0){
           // if($pass_ini[0]['fun_apassword']!=$pass){
                $db->table('historial_psw')->insert([
                'fun_id'        => $id,
                'fun_apassword' => $pass
                ]);
            //}
          }
      }

      // Actualización del funcionario (Operación independiente)
        if (!$model_funcionario->update($id, $data)) {
            throw new \Exception("No se pudo actualizar los datos del funcionario.");
        }

        // --- ACTUALIZACIÓN DE SESIÓN EN CALIENTE ---
        // Solo actualizamos si el usuario editado es el mismo que está operando
        if ($session->get('fun_id') == $id) {
            $funcionario = $model_funcionario->get_responsablePoa($id);
            $session->set(['funcionario' => $funcionario]); 
        }
        return redirect()->to(base_url('mnt/responsables'))
                         ->with('success', 'Datos actualizados correctamente.');

    } catch (Exception $e) {
        // 5. Manejo de la excepción: Loguear el error y avisar al usuario
        log_message('error', 'Error en update_resp: ' . $e->getMessage());
        
        return redirect()->back()->withInput()->with('error', 'Ocurrió un error inesperado: ' . $e->getMessage());
    }
  }





/// Valida Form Update Responsable-Seguimiento POA
  public function Update_respspoa() {
    $db = \Config\Database::connect(); 
    try {
      $model_funcionario = new Model_funcionarios();
      $get_proy=$model_funcionario->get_AperturasxRegional($this->request->getPost('proy_id'));
      $id=$this->request->getPost('fun_id');

      if (!$id) {
        throw new \Exception("ID de funcionario no proporcionado.");
      }

      $data = [
            'fun_nombre'   => strtoupper($this->request->getPost('fn_usu')),
            'fun_dist'      => $get_proy['dist_id'],
            'fun_usuario'  => strtoupper($this->request->getPost('fn_usu')),
        ];

        $pass = $this->request->getPost('fun_password');
        if (!empty($pass)) {
          //$data['fun_password'] = $pass;

          $pass_ini=$model_funcionario->get_pwd($id);
          if(!empty($pass_ini)){
            if($pass_ini[0]['fun_apassword']!=$pass){
                $db->table('historial_psw')->insert([
                'fun_id'        => $id,
                'fun_apassword' => $pass
                ]);

                
            }
            $data['fun_password'] = password_hash($pass, PASSWORD_DEFAULT);
          }

          //// update funcionario
          $db->table('funcionario')
               ->where('fun_id', $id) // Aquí defines tu condición
               ->update($data);
        }

        return redirect()->to(base_url('mnt/resp_seguimientopoa'))
                         ->with('success', 'Datos actualizados correctamente.');

    } catch (Exception $e) {
        // 5. Manejo de la excepción: Loguear el error y avisar al usuario
        log_message('error', 'Error en update_resp: ' . $e->getMessage());
        
        return redirect()->back()->withInput()->with('error', 'Ocurrió un error inesperado: ' . $e->getMessage());
    }
  }

  /// Funcion GET REGIONAL UPDATE
  public function get_reg_nal_add() {
    $model_funcionario = new Model_funcionarios();
    $model_reg = new Model_regional();
    $regionales=$model_reg->obtenerRegionales();
    $tp_adm = $this->request->getPost('tipo_adm'); /// tipo adm
    $select_reg='';
    $select_dist='';

    if($tp_adm==1){ /// Nacional
      $select_reg.='<option value="10">Administración Central</option>';
      $select_dist.='<option value="22">Oficina Nacional</option>';
    }
    else{ /// Regional
      $select_reg.='<';
                    foreach($regionales as $row){
                      $select_reg.='<option value="'.$row['dep_id'].'" >'.strtoupper($row['dep_departamento']).'</option>'; 
                    }
      $select_dist.='<option value="0">Seleccione ..</option>';
    }

    $result = [
        'respuesta'      => 'correcto',
        'select_reg' => $select_reg, // Regional
        'select_dist' => $select_dist // Distrital
    ];

    return $this->response->setJSON($result);
}


  /// Funcion GET REGIONAL UPDATE
  public function get_reg_nal() {
    $model_funcionario = new Model_funcionarios();
    $model_reg = new Model_regional();
    $regionales=$model_reg->obtenerRegionales();
    $tp_adm = $this->request->getPost('tipo_adm'); /// tipo adm
    $fun_id = $this->request->getPost('id'); /// id
    $get_rep=$model_funcionario->get_responsablePoa($fun_id);
    $distritales=$model_reg->obtenerDistritales($get_rep[0]['dep_id']);
    $select_reg='';
    $select_dist='';

    if($tp_adm==1){ /// Nacional
      $select_reg.='<option value="10">Administración Central</option>';
      $select_dist.='<option value="22">Oficina Nacional</option>';
    }
    else{ /// Regional
      $select_reg.='<option value="0" selected>Seleccione ..</option>';
                    foreach($regionales as $row){
                      if($row['dep_id']==$get_rep[0]['dep_id']){
                        $select_reg.='<option value="'.$row['dep_id'].'" selected >'.strtoupper($row['dep_departamento']).'</option>';    
                      }
                      else{
                        $select_reg.='<option value="'.$row['dep_id'].'" >'.strtoupper($row['dep_departamento']).'</option>';    
                      }
                    }

      $select_dist.='<option value="0">Seleccione ...</option>';
                    foreach($distritales as $row){
                      if($row['dist_id']==$get_rep[0]['dist_id']){
                        $select_dist.='<option value="'.$row['dist_id'].'" selected >'.strtoupper($row['dist_distrital']).'</option>';    
                      }
                      else{
                        $select_dist.='<option value="'.$row['dist_id'].'" >'.strtoupper($row['dist_distrital']).'</option>';    
                      }
                    }
    }

    $result = [
        'respuesta'      => 'correcto',
        'select_reg' => $select_reg, // Regional
        'select_dist' => $select_dist // Distrital
    ];

    return $this->response->setJSON($result);
}



 /// Funcion GET DISTRITAL (form add) Res POA
  public function get_distritales_add() {
    $model_funcionario = new Model_funcionarios();
    $model_reg = new Model_regional();

   // $regionales=$model_reg->obtenerRegionales();
    $dep_id = $this->request->getPost('dep_id'); /// tipo adm
    $distritales=$model_reg->obtenerDistritales($dep_id);
    
    $select_dist='';

    $select_dist.='';
                    foreach($distritales as $row){
                      $select_dist.='<option value="'.$row['dist_id'].'" >'.strtoupper($row['dist_distrital']).'</option>'; 
                    }

    $result = [
        'respuesta'      => 'correcto',
        'select_dist' => $select_dist // Distrital
    ];

    return $this->response->setJSON($result);
  }


 /// Funcion GET Apertura Prog - Seguimiento POA
  public function get_aper_add() {
    $model_funcionario = new Model_funcionarios();
  //  $model_reg = new Model_regional();

    $dep_id = $this->request->getPost('dep_id'); /// tipo adm
    $aperturas=$model_funcionario->obtenerAperturasxRegional($dep_id);

    $select_aper='';
      $select_aper.='<option value="0" >Seleccione ..</option>';
      foreach($aperturas as $row){
        $select_aper.='<option value="'.$row['proy_id'].'" >'.$row['aper_programa'].' '.$row['aper_proyecto'].' '.$row['aper_actividad'].'-'.strtoupper($row['actividad']).' '.$row['abrev'].'</option>'; 
      }

    $result = [
        'respuesta'      => 'correcto',
        'select_aper' => $select_aper // Distrital
    ];

    return $this->response->setJSON($result);
  }


   /// Funcion GET Apertura Unidad Responsable - Seguimiento POA
  public function get_uresp_add() {
    $model_funcionario = new Model_funcionarios();
    $model_reg = new Model_regional();

    $proy_id = $this->request->getPost('proy_id'); /// proy_id
    $uresponsables=$model_funcionario->get_list_unidadresponsables($proy_id);

    $select_unidad='';
    $select_unidad.='<option value="0" >Seleccione ..</option>';
      foreach($uresponsables as $row){
        $verif=$model_funcionario->verif_uresponsable_existente_seguimiento($row['com_id']);
        if(count($verif)==0){
          $select_unidad.='<option value="'.$row['com_id'].'" >'.$row['tipo_subactividad'].' '.$row['serv_descripcion'].'</option>'; 
        }
        
      }

    $result = [
        'respuesta'      => 'correcto',
        'select_unidad' => $select_unidad // Distrital
    ];

    return $this->response->setJSON($result);
  }


 /// Funcion GET DISTRITAL (form update)
  public function get_distritales() {
    $model_funcionario = new Model_funcionarios();
    $model_reg = new Model_regional();

   // $regionales=$model_reg->obtenerRegionales();
    $dep_id = $this->request->getPost('dep_id'); /// tipo adm
    $fun_id = $this->request->getPost('id'); /// id
    
    $get_rep=$model_funcionario->get_responsablePoa($fun_id);
    $distritales=$model_reg->obtenerDistritales($dep_id);
    
    $select_dist='';

    $select_dist.='<option value="0">Seleccione ...</option>';
                    foreach($distritales as $row){
                      if($row['dist_id']==$get_rep[0]['dist_id']){
                        $select_dist.='<option value="'.$row['dist_id'].'" selected >'.strtoupper($row['dist_distrital']).'</option>';    
                      }
                      else{
                        $select_dist.='<option value="'.$row['dist_id'].'" >'.strtoupper($row['dist_distrital']).'</option>';    
                      }
                    }

    $result = [
        'respuesta'      => 'correcto',
        'select_dist' => $select_dist // Distrital
    ];

    return $this->response->setJSON($result);
  }


/// Funcion VERIF USUARIO
public function verif_usuario() {
    // Verificar que la petición sea AJAX para mayor seguridad
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(403)->setJSON(['respuesta' => 'error', 'msj' => 'Acceso no permitido']);
    }

    $model_funcionario = new Model_funcionarios();
    
    // Obtener y limpiar el input (trim elimina espacios accidentales)
    $usuario = trim($this->request->getPost('usuario') ?? '');

    if (empty($usuario)) {
        return $this->response->setJSON(['respuesta' => 'error', 'msj' => 'Usuario vacío']);
    }

    // Consulta al modelo
    $get_usuario = $model_funcionario->get_usuario_responsablePoa($usuario);
  
    // Si count es 0, significa que el nombre de usuario está DISPONIBLE
    if (count($get_usuario) == 0) {
        $result = ['respuesta' => 'correcto'];
    } else {
        // El usuario ya existe en la base de datos
        $result = ['respuesta' => 'error'];
    }

    return $this->response->setJSON($result);
  }

  /// Funcion Desactiva Responsable POA
  public function delete_responsable() {
    $db = \Config\Database::connect();
    $id=$this->request->getPost('fun_id');
    if (empty($id)) {
      return $this->response->setJSON(['respuesta' => 'error', 'mensaje' => 'ID no válido']);
    }
    // Solo agregas más elementos al array $data
    $data = [
        'fun_estado' => 3,
        'sw_pass' => 0 // Ejemplo de una tercera variable
    ];

    $resultado = $db->table('funcionario')
                    ->where('fun_id', $id)
                    ->update($data);


    if ($db->affectedRows() > 0) {
        return $this->response->setJSON(['respuesta' => 'correcto']);
    } else {
        return $this->response->setJSON(['respuesta' => 'error', 'mensaje' => 'No se encontró el registro']);
    }

    return $this->response->setJSON($result);
  }


}


