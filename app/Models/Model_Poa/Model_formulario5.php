<?php
namespace App\Models\Model_Poa;

use CodeIgniter\Model;

class Model_formulario5 extends Model{

    // get partida
    public function get_obtenerPartida($codigo) {
        // 1. Usamos Query Binding para evitar ataques de Inyección SQL
        $sql = 'SELECT * FROM partidas WHERE par_codigo = ?';
        $query = $this->db->query($sql, [$codigo]);

        // 2. Obtenemos el resultado
        $row = $query->getRowArray();

        // 3. Verificamos si existe: si no hay datos, retornamos false o un mensaje
        return ($row) ? $row : false;
    }

}