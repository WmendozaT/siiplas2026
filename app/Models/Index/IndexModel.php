<?php
namespace App\Models\Index;

use CodeIgniter\Model;

class IndexModel extends Model{
    protected $table = 'funcionario'; 
    
    /// lista de funcionario
    public function obtenerFuncionariosActivosRaw(){
        $sql = "SELECT * FROM funcionario WHERE fun_estado != 3";
        $query = $this->query($sql);
        
        return $query->getResult(); 
    }

    /// Verifica Usuario Administr
public function verificar_loggin($user_name, $password_plano){
    $data = array(
        'bool'   => false,
        'fun_id' => null,
        'data'   => null,
        'message'=> 'Error de credenciales.'
    );

    $builder = $this->db->table('funcionario');
    $user = $builder->where('fun_usuario', $user_name)->get()->getRowArray();

    if ($user) {
        // 3. Verificar si el usuario existe
        if (!$user) {
            return $data;
        }


        if ($user['fun_estado'] == 3) {
            $data['message'] = 'Usuario inactivo.';
            return $data; // Sale de la función, el bool sigue siendo false
        }



            if($user['sw_pass']==0){ // haseando el password
                
                // --- 2. MIGRAR A UN HASH SEGURO INMEDIATAMENTE (password_hash) ---
                $new_secure_hash = password_hash($password_plano, PASSWORD_DEFAULT); // Usa bcrypt/argon2
                
                // Actualizar la base de datos con el nuevo hash seguro
                $builder->where('fun_id', $user['fun_id'])
                        ->update([
                            'fun_password' => $new_secure_hash,
                            'sw_pass' => 1
                        ]);
            }


            if (password_verify($password_plano, $user['fun_password'])) {
             $data['bool'] = true;
             $data['fun_id'] = $user['fun_id'];
             $data['data'] = $user;
             $data['message'] = 'Login exitoso.';
             return $data;
            } 

    }
    else{
        return $data; // Si no hay usuario o las contraseñas no coinciden en ningún caso    
    }
    
    
}


    public function verificar_loggin3($user_name, $password_plano){
    
        // 1. Usar Query Builder para prevenir SQL Injection
        $this->db->select('*');
        $this->db->from('funcionario');
        $this->db->where('fun_usuario', $user_name); // CI3 sanitiza automáticamente aquí

        $query = $this->db->get();
        $user = $query->row_array(); // Obtiene UNA sola fila como array

        $data = array(
            'bool' => false,
            'fun_id' => null,
            'data' => null // Es útil devolver todos los datos
        );

        // 2. Verificar si se encontró un usuario
        if ($user) {
            // 3. Verificar la contraseña usando password_verify()
            // Asume que fun_password está hasheado correctamente con password_hash()
            if (password_verify($password_plano, $user['fun_password'])) {
                // ¡Éxito!
                $data['bool'] = true;
                $data['fun_id'] = $user['fun_id'];
                $data['data'] = $user;
            }
        }
        
        // Devuelve el array con el resultado
        return $data;
    }





/*    public function verificar_loggin($user_name, $password){
        $query = "SELECT *
        FROM funcionario
        WHERE fun_usuario = '".$user_name."' ";
        $query = $this->db->query($query);
        $query = $query->result_array();
        $data = array(
            'bool' => false,
            'fun_id' => null  
        );
        foreach ($query as $fila) {
            $var = $this->password_decod($fila['fun_password']);
            if($var == $password){
                $data['bool'] = true;
                $data['fun_id'] = $fila['fun_id'];
            }
        }
        return $data;
    }
    */
    // Si usas el método Query Builder recomendado:
   /* public function obtenerFuncionariosActivos()
    {
        // Con la línea protected $table = 'funcionario'; añadida arriba, 
        // este método ya sabe qué tabla usar.
        return $this->where('fun_estado !=', 3)->findAll();
    }*/
}