<?php
namespace App\Models\Model_mantenimiento;

use CodeIgniter\Model;

class Model_regional extends Model{
    protected $table = '_departamentos';

    /// lista de Regionales
    public function obtenerRegionales(){
        $sql = 'SELECT *
            from _departamentos
            where dep_id!=0
            ORDER BY dep_id asc';
        $query = $this->query($sql);
        return $query->getResultArray();
    }

    //// get datos Regional
/*    public function get_regional2($dep_id){
        return $this->db->get_where('_departamentos', ['dep_id' => $dep_id])
                        ->getRowArray();
    }*/

    //// get datos Regional
    public function get_regional($dep_id){
        $sql = 'SELECT *
                from _departamentos
                where dep_id='.$dep_id.'';

        $query = $this->db->query($sql);
        return $query->getRowArray();
    }

    /// lista de Distritales por Regional
    public function obtenerDistritales($dep_id){
        $sql = '
            SELECT *
            from _distritales
            where dep_id='.$dep_id.' and dist_estado!=0
            ORDER BY dist_id asc';
        $query = $this->query($sql);
        return $query->getResultArray();
    }


    /// lista Unidades Organizacionales
    public function obtenerUnidadesOrganizacionales(){
        $sql = '
            SELECT *
            from unidadorganizacional
            order by uni_id asc';
        $query = $this->query($sql);
        return $query->getResultArray();
    }

    /// lista de unidades organizacionales (Todos) para el poa de la gestion por regional
    public function lista_unidades_disponibles($dep_id,$gestion){
        $sql = '
            SELECT dist.*,ua.*,te.*,
            CASE 
            WHEN ug.ug_id IS NOT NULL THEN 1 
            ELSE 0 
            END AS incluido
            from _distritales dist
            Inner Join unidad_actividad as ua On ua.dist_id=dist.dist_id
            Inner Join v_tp_establecimiento as te On te.te_id=ua.te_id

            LEFT JOIN uni_gestion ug ON ug.act_id = ua.act_id AND ug.g_id = '.$gestion.'
            where dist.dep_id='.$dep_id.' and ua.act_estado!=3
            order by dist.dist_id, ua.act_id asc';
        $query = $this->query($sql);
        return $query->getResultArray();
    }


    /// lista de unidades organizacionales para el Reporte de la gestion por regional
    public function lista_unidades_disponibles_rep($dep_id,$gestion){
        $sql = '
            SELECT dist.*,ua.*,te.*,
            CASE 
            WHEN ug.ug_id IS NOT NULL THEN 1 
            ELSE 0 
            END AS incluido
            from _distritales dist
            Inner Join unidad_actividad as ua On ua.dist_id=dist.dist_id
            Inner Join v_tp_establecimiento as te On te.te_id=ua.te_id

            Inner JOIN uni_gestion ug ON ug.act_id = ua.act_id AND ug.g_id = '.$gestion.'
            where dist.dep_id='.$dep_id.' and ua.act_estado!=3
            order by dist.dist_id, ua.act_id asc';
        $query = $this->query($sql);
        return $query->getResultArray();
    }

    /// lista tipo de establecimiento
    public function lista_tipo_establecimiento(){
        $sql = '
            SELECT *
            from tipo_establecimiento
            where ta_id=2
            order by te_id asc';
        $query = $this->query($sql);
        return $query->getResultArray();
    }


    /// get modulo estado de la unidadOrganizacional en la gestion vigente
    public function existe_uorganizacional_en_la_gestion($act_id, $gestion) {
    return $this->db->table('uni_gestion')
                    ->where(['act_id' => $act_id, 'g_id' => $gestion])
                    ->countAllResults() > 0; // Devuelve true o false
    }


    //// ASIGNACION DE PRESUPUESTOS

    // lista poa general
    public function lista_poa_gral() {
    /// tp_id : 1 Proy inversion
    /// tp_id : 4 Gasto Corriente
        $gestion = session()->get('configuracion')['conf_gestion'] ?? null;
        $sql = "SELECT *,
                    CASE 
                        WHEN tp_id = 1 THEN 'INVERSIÓN'
                        WHEN tp_id = 4 THEN 'GASTO CORRIENTE'
                        ELSE 'OTRO'
                    END AS tipo_gasto_nombre
                FROM lista_poa_nacional(".$gestion.")
                ORDER BY dep_id, dist_id, prog, proy, act ASC";
        $query = $this->query($sql);
        return $query->getResultArray();
    }

}