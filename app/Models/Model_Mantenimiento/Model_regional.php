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


    /// lista de Aperturas por Regional
    public function obtenerAperturasxRegional($dep_id){
        $gestion = session()->get('configuracion')['conf_gestion'] ?? null;
        $sql = '
            SELECT *
            from lista_poa_gastocorriente_nacional('.$gestion.')
            where ta_id!=2 and dep_id='.$dep_id.'
            order by da,aper_programa asc';
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




}