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


    /// get existe alineado unidad responsable al tipo de establecimiento
    public function existe_uresponsable_a_establecimiento($te_id, $serv_id) {
    return $this->db->table('establecimiento_servicio')
                    ->where(['te_id' => $te_id, 'serv_id' => $serv_id])
                    ->countAllResults() > 0; // Devuelve true o false
    }

    /// get existe alineado el Programa al tipo de establecimiento
    public function existe_aperprograma_a_establecimiento($aper_id, $te_id) {
        $gestion = session()->get('configuracion')['conf_gestion'] ?? date('Y');
    return $this->db->table('aper_establecimiento')
                    ->where(['aper_id' => $aper_id, 'te_id' => $te_id, 'g_id' => $gestion])
                    ->countAllResults() > 0; // Devuelve true o false
    }


    /// Lista de TIPOS DE ESTABLECIMIENTOS
    public function list_tp_establecimiento(){
        $gestion = session()->get('configuracion')['conf_gestion'] ?? date('Y');
        $sql = " 
            SELECT *,
            CASE 
            WHEN ae.te_id IS NOT NULL THEN 1 
            ELSE 0 
            END AS incluido_programa
                from v_tp_establecimiento tp_est
                LEFT JOIN aper_establecimiento ae ON ae.te_id = tp_est.te_id AND ae.g_id = $gestion
                order by tp_est.te_id asc";

        $query = $this->db->query($sql);
        return $query->getResultArray();
    }

    //// Lista de unidades responsables para alinear a el tipo de establecimiento
    public function list_uresponsables_para_alinear_a_tipoEstablecimiento($te_id){
        $sql = "SELECT *,
            CASE 
            WHEN uresp.serv_id IS NOT NULL THEN 1 
            ELSE 0 
            END AS alineado
                from servicios_actividad serv
                LEFT JOIN establecimiento_servicio uresp ON uresp.serv_id = serv.serv_id AND uresp.te_id =$te_id
                where serv.activo!=0
                order by serv.serv_cod asc";

        $query = $this->db->query($sql);
        return $query->getResultArray();
    }
}