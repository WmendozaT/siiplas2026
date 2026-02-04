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


    /// Gestion Activo
    public function get_gestion_activo(){
        $sql = "SELECT * 
                FROM configuracion conf
                Inner Join mes as m On m.m_id=conf.conf_mes
                Inner Join trimestre_mes as trm On trm.trm_id=conf.conf_mes_otro
                WHERE conf.conf_estado = 1";
        $query = $this->query($sql);
        
        return $query->getRowArray();
    }


    /// List Gestiones Disponibles
    public function list_gestiones_disponibles(){
        $sql = "SELECT *
                from configuracion
                where estado=1
                order by ide asc";
        $query = $this->query($sql);
        
        return $query->getResultArray();
    }

    /// List Trimestres Disponibles
    public function list_trimestre_disponibles(){
        $sql = "SELECT *
                from trimestre_mes
                where estado=1
                order by trm_id asc";
        $query = $this->query($sql);
        
        return $query->getResultArray();
    }

    /// List Meses Disponibles
    public function list_meses_disponibles(){
        $sql = "SELECT *
                from mes
                order by trm_id asc";
        $query = $this->query($sql);
        
        return $query->getResultArray();
    }

    /// Modulos Activos
    public function modulos($ide,$tp_adm){
        if($tp_adm==1){ /// Nacional
            $sql = "SELECT 
                    ms.*,
                    CASE 
                        WHEN cm.mod_id IS NOT NULL THEN 1 
                        ELSE 0 
                    END AS incluido
                FROM modulos_sistema ms
                LEFT JOIN confi_modulo cm 
                    ON ms.modulo_id = cm.mod_id 
                    AND cm.ide = ".$ide."
                    order by ms.modulo_id asc;";
        }
        else{ /// regional / distrital
            $sql = "SELECT * 
                from confi_modulo conf
                Inner Join modulos_sistema as modu On modu.modulo_id=conf.mod_id
                where ide=".$ide." and modu.modulo_estado=1
                order by conf.mod_id asc";
        }
        $query = $this->query($sql);
        return $query->getResultArray();
    }

    /// get modulo estado en la gestion
    public function existe_modulo_configurado($id_modulo, $gestion) {
    return $this->db->table('confi_modulo')
                    ->where(['mod_id' => $id_modulo, 'ide' => $gestion])
                    ->countAllResults() > 0; // Devuelve true o false
    }


    /// Sub Modulos Activos
    public function sub_modulos($id){
        $sql = "SELECT *
                from modulo_menu
                where mod_id=".$id." and sub_menu_estado=1
                order by sub_id asc";
        $query = $this->query($sql);
        return $query->getResultArray();
    }


    /// Listado de Aperturas Programaticas
    public function List_aperturas($gestion){
        $sql = "
                SELECT *
                from aperturaprogramatica a
                where a.aper_estado!=3 and a.aper_gestion=".$gestion." and a.aper_asignado=1
                order by a.aper_gestion,a.aper_programa,a.aper_proyecto,a.aper_actividad asc";
        $query = $this->query($sql);
        return $query->getResultArray();
    }


    /// Listado de Partidas
    public function lista_partidas(){
        $sql = 'SELECT *
                from partidas
                where par_id!=0 and par_depende!=0
                order by par_codigo asc';

        $query = $this->query($sql);
        return $query->getResultArray();
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

        $query = $this->query($sql);
        return $query->getResultArray();
    }


    /// Get Buscando funcionario por su Usuario
    public function fun_usuario($usuario){
        // Usando el Query Builder de CI4 para una consulta segura
            $sql = 'SELECT *
                from funcionario
                where fun_usuario=\''.$usuario.'\' and fun_estado!=3';

        $query = $this->db->query($sql);
        return $query->getRowArray();
    }




    /// Datos Regional Distrital
    public function datos_regional($dist_id){
        $sql = 'SELECT *
                from _distritales ds
                Inner Join _departamentos as d On d.dep_id=ds.dep_id
                where ds.dist_id='.$dist_id.'';

        $query = $this->db->query($sql);
        return $query->getRowArray();
    }

    /// Datos Rol del funcionario
    public function get_rol_usuario($fun_id){
        $sql = '
        SELECT f.fun_id,f.r_id,f.r_estado,r.r_estado,r.r_nombre
        from fun_rol f
        Inner Join rol as r On r.r_id=f.r_id
        where f.fun_id='.$fun_id.'
        group by f.fun_id,f.r_id,f.r_estado,r.r_estado,r.r_nombre';

        $query = $this->db->query($sql);
        return $query->getRowArray();
    }


    /// Verificar Login
    public function verificar_loggin($user_name, $password_plano, $captcha, $dat_captcha) {
    $data = [
        'bool'    => false,
        'message' => 'Error de credenciales.'
    ];

    // 1. Validar Captcha
    if (md5($dat_captcha) != $captcha) {
        $data['message'] = 'Error en el código captcha.';
        return $data;
    }

    $builder = $this->db->table('funcionario');
    $user = $builder->where('fun_usuario', $user_name)->get()->getRowArray();

    // 2. Verificar existencia y estado
    if (!$user) return $data;

    if ($user['fun_estado'] == 3) {
        $data['message'] = 'Usuario inactivo.';
        return $data;
    }

    // 3. CASO A: Usuario NO migrado (sw_pass == 0)
    if ($user['sw_pass'] == 0) {
        // VALIDACIÓN CRÍTICA: Compara la clave plana contra la DB ANTES de migrar
        // Si antes usabas MD5 sería: if (md5($password_plano) == $user['fun_password'])
     
            
            $new_secure_hash = password_hash($password_plano, PASSWORD_DEFAULT);
            $this->db->table('funcionario')
                 ->where('fun_id', $user['fun_id'])
                 ->update([
                     'fun_password' => $new_secure_hash,
                     'sw_pass'      => 1
                 ]);
            
            // Recargar datos para devolver el nuevo estado
            $user = $this->db->table('funcionario')->where('fun_id', $user['fun_id'])->get()->getRowArray();

    }

    // 4. CASO B: Verificación normal para todos (incluye recién migrados)
    if (password_verify($password_plano, $user['fun_password'])) {
        return [
            'bool'    => true,
            'data'    => $user,
            'message' => 'Login exitoso.'
        ];
    }

    return $data;
}



}