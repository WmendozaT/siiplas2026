<?php
namespace App\Models\Model_Ppto;

use CodeIgniter\Model;

class Model_PptoAsig extends Model{

    // get partida
    public function get_partidas_asignadas_institucional() {
        $gestion = session()->get('configuracion')['ide'] ?? null;
        $sql = 'select *
                from ptto_partidas_sigep
                where g_id='.$gestion.'
                order by da,ue,aper_programa,aper_actividad,partida asc';
        $query = $this->query($sql);
        
        return $query->getResultArray();
    }

    // get detalle presupuesto por partida asignado por unidad organizacional
    public function get_partidas_asignadas_x_unidadOrganizacional_institucional() {
        $gestion = session()->get('configuracion')['ide'] ?? null;
        if (!$gestion) return [];

        // 1. Usamos comillas simples (') para los textos: 'INVERSIÓN'
        // 2. Agregamos el 'END' para cerrar el CASE
        $sql = "SELECT poa.*, ppto.partida, ppto.importe ppto_asignado, 
                CASE 
                    WHEN poa.tp_id = 1 THEN 'INVERSIÓN'::TEXT
                    WHEN poa.tp_id = 4 THEN 'GASTO CORRIENTE'::TEXT
                    ELSE 'OTRO'::TEXT
                END as tipo_gasto_nombre
                FROM lista_poa_nacional(?) poa
                LEFT JOIN ptto_partidas_sigep ppto ON ppto.aper_id = poa.aper_id
                ORDER BY poa.dep_id, poa.dist_id, poa.prog, poa.proy, poa.act, ppto.partida ASC";

        // Ejecución con Query Bindings para seguridad
        $query = $this->db->query($sql, [$gestion]);
        return $query->getResultArray();
    }

    public function get_partidas_asignadas_x_uniorganizacional($aper_id) {
        $gestion = session()->get('configuracion')['ide'] ?? null;
        $sql = 'select *
                from ptto_partidas_sigep ppto
                LEFT JOIN partidas par ON par.par_id = ppto.par_id
                where ppto.aper_id='.$aper_id.' and ppto.g_id='.$gestion.'
                order by ppto.da,ppto.ue,ppto.aper_programa,ppto.aper_actividad,partida asc';
        $query = $this->db->query($sql, [$gestion]);
        
        return $query->getResultArray();
    }



}