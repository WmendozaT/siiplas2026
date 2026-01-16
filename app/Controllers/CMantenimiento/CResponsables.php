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



class CResponsables extends BaseController{
    protected $Model_funcionarios;
    protected $Model_regional;

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
        $this->session->get('user_name');
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


    /// Vista Form Add Responsable POA
    public function new_responsables(){
        $miLib_resp = new Libreria_Responsable();
        $model_funcionario = new Model_funcionarios();
                
        $data['formulario']=$miLib_resp->form_add_responsables_poa(); /// formulario de adicion de responsable (Libreria Responsable)
        return view('View_mantenimiento/View_responsables/view_funcionarios',$data);
    }


  /// Formulario 

  /// Valida Add Responsable
  public function Add_resp() {
    $db = \Config\Database::connect(); 
    try {
        $model_funcionario = new Model_funcionarios();

        $data = [
            'fun_nombre'   => $this->request->getPost('fn_nom'),
            'car_id'  => 0,
            'fun_paterno'  => $this->request->getPost('fn_pt'),
            'fun_materno'  => $this->request->getPost('fn_mt'),
            'fun_ci'       => $this->request->getPost('fn_ci'),
            'fun_telefono' => $this->request->getPost('fn_fono'),
            'fun_cargo'    => $this->request->getPost('fn_cargo'),
            'fun_adm'      => $this->request->getPost('tp_adm1'),
            'fun_dist'     => $this->request->getPost('dist_id'),
            'uni_id'       => $this->request->getPost('uni_id'),
            'fun_usuario'  => $this->request->getPost('fn_usu'),
        ];

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

/*    } catch (\Throwable $e) {  /// para mostrar
    // Usamos \Throwable para capturar tanto errores de PHP como de Base de Datos
    
    // OPCIÓN A: Para depurar rápido (Detiene todo y muestra el error en pantalla)
    die("Error detectado: " . $e->getMessage()); 
    }*/
  }

  /// Valida Form Update Responsable
  public function Update_resp() {
    $db = \Config\Database::connect(); 
    try {
      $model_funcionario = new Model_funcionarios();
     // $datos = $this->request->getPost();
      
      $id=$this->request->getPost('fun_id');

      if (!$id) {
        throw new \Exception("ID de funcionario no proporcionado.");
      }

      $data = [
            'fun_nombre'   => $this->request->getPost('fn_nom'),
            'fun_paterno'  => $this->request->getPost('fn_pt'),
            'fun_materno'  => $this->request->getPost('fn_mt'),
            'fun_ci'       => $this->request->getPost('fn_ci'),
            'fun_telefono' => $this->request->getPost('fn_fono'),
            'fun_cargo'    => $this->request->getPost('fn_cargo'),
            'fun_adm'      => $this->request->getPost('tp_adm'),
            'fun_dist'      => $this->request->getPost('dist_id'),
            'uni_id'       => $this->request->getPost('uni_id'),
            'fun_usuario'  => $this->request->getPost('fn_usu'),
        ];

      $pass = $this->request->getPost('fun_password');
      if (!empty($pass)) {
          $data['fun_password'] = $pass;

          $db->table('historial_psw')->insert([
                'fun_id'        => $id,
                'fun_apassword' => $pass
            ]);
      }

      // Actualización del funcionario (Operación independiente)
        if (!$model_funcionario->update($id, $data)) {
            throw new \Exception("No se pudo actualizar los datos del funcionario.");
        }

        return redirect()->to(base_url('mnt/responsables'))
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



 /// Funcion GET DISTRITAL (form add)
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


}


