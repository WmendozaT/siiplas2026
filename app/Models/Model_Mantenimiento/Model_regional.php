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

    /// lista de unidades organizacionales para el poa de la gestion por regional
    public function lista_unidades_disponibles($dep_id,$gestion){
        $sql = '
            SELECT *,
            CASE 
            WHEN ug.ug_id IS NOT NULL THEN 1 
            ELSE 0 
            END AS incluido
            from _distritales dist
            Inner Join unidad_actividad as ua On ua.dist_id=dist.dist_id

            LEFT JOIN uni_gestion ug ON ug.act_id = ua.act_id AND ug.g_id = '.$gestion.'
            where dist.dep_id='.$dep_id.'
            order by dist.dist_id, ua.act_id asc';
        $query = $this->query($sql);
        return $query->getResultArray();
    }



}