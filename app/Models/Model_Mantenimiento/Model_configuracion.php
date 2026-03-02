<?php
namespace App\Models\Model_Mantenimiento;

use CodeIgniter\Model;

class Model_configuracion extends Model{

    /// Listado de Partidas
    public function lista_partidas(){
        $sql = 'SELECT *
                from partidas
                where par_id!=0 and par_depende!=0
                order by par_codigo asc';

        // CORRECCIÓN: Agregar "db" antes de query
        $query = $this->db->query($sql); 
        return $query->getResultArray();
    }

    /// Get Partida
    public function get_partidas($par_id){
        $sql = 'SELECT *
                from partidas
                where par_id='.$par_id.'';

        $query = $this->db->query($sql);
        return $query->getRowArray();
    }


    /// Listado de Unidades de Medida alineados a la partida
    public function lista_umedidas($par_id){
        $sql = 'SELECT 
                    um.*, 
                    CASE 
                    WHEN pum.um_id IS NOT NULL THEN 1 
                    ELSE 0 
                    END AS incluido
                FROM insumo_unidadmedida um
                LEFT JOIN par_umedida pum ON pum.um_id = um.um_id AND pum.par_id = '.$par_id.'
                ORDER BY um.um_id ASC;';

        $query = $this->db->query($sql); 
        return $query->getResultArray();
    }

    /// get modulo estado en la gestion
    public function existe_umedia_a_partida($par_id, $um_id) {
    return $this->db->table('par_umedida')
                    ->where(['par_id' => $par_id, 'um_id' => $um_id])
                    ->countAllResults() > 0; // Devuelve true o false
    }

    
    /// Lista de TIPOS DE SERVICIOS
    public function list_tp_establecimiento(){
        $sql = ' select *
                 from v_tp_establecimiento';

        $query = $this->db->query($sql);
        return $query->getResultArray();
    }

}