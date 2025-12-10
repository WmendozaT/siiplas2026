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
    public function verificar_loggin($user_name, $password){
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
    
    // Si usas el método Query Builder recomendado:
   /* public function obtenerFuncionariosActivos()
    {
        // Con la línea protected $table = 'funcionario'; añadida arriba, 
        // este método ya sabe qué tabla usar.
        return $this->where('fun_estado !=', 3)->findAll();
    }*/
}