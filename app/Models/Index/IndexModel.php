<?php
namespace App\Models\Index;

use CodeIgniter\Model;

class IndexModel extends Model
{
    protected $table = 'funcionario'; 
    
    public function obtenerFuncionariosActivosRaw()
    {
        $sql = "SELECT * FROM funcionario WHERE fun_estado != 3";
        $query = $this->query($sql);
        
        return $query->getResult(); 
    }
    
    // Si usas el método Query Builder recomendado:
   /* public function obtenerFuncionariosActivos()
    {
        // Con la línea protected $table = 'funcionario'; añadida arriba, 
        // este método ya sabe qué tabla usar.
        return $this->where('fun_estado !=', 3)->findAll();
    }*/
}