<?php

namespace App\Controllers;
use App\Models\Index\IndexModel;
use App\Libraries\Calculadora;

class User extends BaseController{

    /// index
    public function index(){
        // Si el usuario ya está logueado, redirigir al dashboard (o donde corresponda)
        if (session()->get('isLoggedIn')) {
            return redirect()->to(base_url('dashboard'));
        }
        
        helper(['form']);
        return view('dashboard/login');
    }


    public function loginAction(){
        $session = session();
        $model = new IndexModel();

        $rules = [
            'usuario' => 'required|min_length[3]|max_length[30]', // Ajusta longitudes
            'password' => 'required|min_length[8]|max_length[255]', // Ajusta longitudes
        ];
        
        /*$errors = [
            'password' => [
                'validateUser' => 'Usuario o Contraseña incorrectos.'
            ]
        ];
*/
        // 2. Ejecutar la validación básica
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 3. Realizar la verificación manual de usuario y contraseña
        $usuario = $this->request->getPost('usuario');
        $password = $this->request->getPost('password');
        echo $usuario.'---'.$usuario;
        /*$user = $model->where('usuario', $usuario)->first();

        if ($user && password_verify($password, $user['password'])) {
            // Éxito: crear sesión y redirigir
            // ...
        } else {
            // Error: credenciales incorrectas
            $session->setFlashdata('error', 'Usuario o Contraseña incorrectos.');
            return redirect()->to('/login')->withInput();
        }*/
    }




    /// Autentica Login
    public function loginAction2(){
        $session = session();
        $model = new IndexModel();

        // 1. Validar la entrada del formulario
        $rules = [
            'usuario' => 'required|min_length[3]|max_length[10]',
            'password' => 'required|min_length[6]|max_length[20]|validateUser[usuario,password]',
        ];

       /* $errors = [
            'password' => [
                'validateUser' => 'Usuario o Contraseña incorrectos.'
            ]
        ];*/

        // 2. Ejecutar la validación básica
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 2. Obtener datos validados
        $usuario = $this->request->getVar('usuario');
        $password = $this->request->getVar('password');
        echo "Hola mundo";
        //return redirect()->to('/dashboard');
        /*// 3. Buscar usuario en la base de datos
        $user = $model->getUserByUsuario($usuario);

        if ($user && password_verify($password, $user['password'])) {
            // 4. Verificar contraseña y crear sesión segura
            $ses_data = [
                'id'        => $user['id'],
                'usuario'   => $user['usuario'],
                'nombre'    => $user['nombre'],
                'rol'       => $user['rol'],
                'isLoggedIn' => TRUE
            ];
            $session->set($ses_data);

            // Redirige al dashboard o página principal
            return redirect()->to('/dashboard');

        } else {
            // Si falla la verificación (esto rara vez se ejecuta si validateUser funciona bien)
            $session->setFlashdata('error', 'Usuario o Contraseña incorrectos.');
            return redirect()->to('/login')->withInput();
        }*/
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/login');
    }







    /////////////


    public function index3(){

        // 1. Instanciar el modelo
        $model = new IndexModel();

        // 2. Llamar al método específico definido en el modelo: 
        //    Debe ser 'obtenerFuncionariosActivosRaw'
        $funcionarios = $model->obtenerFuncionariosActivosRaw(); 
        
        // 3. Mostrar el conteo para verificar que funciona
        echo count($funcionarios);
        
        // Opcional: Pasar los datos a una vista para mostrarlos formateados
        // return view('nombre_de_tu_vista', ['funcionarios' => $funcionarios]);
    }


    public function index2(){
        //echo route_to('admin_proy_listado');
        //echo route_to('admin/proy/list_proy');
        //echo base_url().'assets/img/registro1.png';
        // 1. Opcional: Preparar datos para pasar a la vista

        $model = new IndexModel();

        // Llama al método que creamos en el modelo (Método A)
        $funcionarios = $model->obtenerFuncionariosActivos();
        echo count($funcionarios);

       /* $data = [
            'titulo' => 'Página Principal del Dashboard',
            'mensaje' => '¡Bienvenido al sistema siiplas2026!',
            'url_boton' => base_url('admin/proy/list_proy'),
            'texto_boton' => 'Listado de Proyectos'
        ];

        // 2. Cargar la vista 'dashboard_view.php' y pasarle los datos
        return view('dashboard/index', $data);*/
    }
    /// libreria calculadora
    public function calculadora(){
        //echo route_to('admin_proy_listado');
        //echo route_to('admin/proy/list_proy');
        //echo base_url().'assets/img/registro1.png';
        // 1. Opcional: Preparar datos para pasar a la vista

        $calc = new Calculadora();
        $resultado = $calc->sumar(5, 10);

        echo "El resultado es: " . $resultado;

        
    }

    public function listProy(){
        echo "Hola mundo nuevo nuevo";
    }
   
}
