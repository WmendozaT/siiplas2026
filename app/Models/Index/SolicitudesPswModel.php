<?php
namespace App\Models\Index;

use CodeIgniter\Model;

class SolicitudesPswModel  extends Model{
    protected $table = 'solicitudes_psw';
    protected $primaryKey = 'sol_id'; // Asumiendo que este es el ID autoincremental
    protected $allowedFields = ['fun_id', 'email', 'sol_fecha', 'num_ip', 'nom_ip'];
    
   // Método para manejar la inserción y devolver el ID
    public function createPswSolicitud($data){
        // El método insert() es automático en Model y retorna true/false.
        // Usamos el Query Builder interno del Model para mayor control si es necesario, 
        // pero Model::insert() ya lo hace automáticamente.
        
        $this->insert($data);
        
        // Model::getInsertID() obtiene el último ID insertado por esta instancia.
        return $this->getInsertID();
    }




}