<?php
namespace App\Models\Model_mantenimiento;

use CodeIgniter\Model;

class Model_funcionarios extends Model{
    protected $table      = 'funcionario';
    protected $primaryKey = 'fun_id';

  //  protected $allowedFields_add = ['fun_nombre', 'fun_paterno', 'fun_materno', 'fun_ci', 'fun_telefono', 'fun_cargo', 'fun_adm', 'fun_dist', 'uni_id', 'fun_usuario', 'fun_password'];

    //// update
    protected $allowedFields = [
        'fun_nombre', 
        'fun_paterno', 
        'fun_materno', 
        'fun_ci', 
        'fun_telefono', 
        'fun_cargo', 
        'fun_adm',  
        'fun_dist', 
        'uni_id', 
        'fun_usuario', 
        'fun_password' // Asegúrate de que este nombre sea exacto al de tu columna
    ];

    // --- AGREGAR ESTO PARA EL HASHEO AUTOMÁTICO ---
    // Estos eventos se disparan al usar $model->insert() o $model->update()
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    // Función interna que realiza el hasheo
    protected function hashPassword(array $data) {
        // Solo hashea si el campo 'fun_password' existe en los datos que se envían
        if (!isset($data['data']['fun_password'])) {
            return $data;
        }

        // Usa el estándar de PHP para cifrar la clave
        $data['data']['fun_password'] = password_hash($data['data']['fun_password'], PASSWORD_DEFAULT);

        return $data;
    }




    /// lista de funcionarios POA Activos
    public function obtenerFuncionariosActivos(){
        $sql = 'SELECT * 
                from vlist_funcionario
                where cm_id=0';
        $query = $this->query($sql);
        
        return $query->getResultArray();
    }


    /// Get Usuario POA
public function get_usuario_responsablePoa($usuario) {
    // El método where() escapa automáticamente el valor de $usuario
    return $this->db->table('vlist_funcionario')
                    ->where('fun_usuario', $usuario)
                    ->get()
                    ->getResultArray();
}


    /// Get Responsable POA
    public function get_responsablePoa($id){
        $sql = 'SELECT * 
                from vlist_funcionario
                where id='.$id.'';
        $query = $this->query($sql);
        
        return $query->getRowArray();
    }

    /// Get Password 2026
    public function get_pwd($id){
        $sql = 'SELECT *
                from historial_psw
                where fun_id='.$id.'
                order by psw_id desc
                LIMIT 1;';
        $query = $this->query($sql);
        
        return $query->getResultArray();
    }

    /// lista de funcionarios para Seguimiento POA activos
    public function obtenerFuncionariosActivos_seguimientoPOA(){
        $gestion = session()->get('configuracion')['conf_gestion'] ?? null;
        $sql = 'SELECT * 
                        from vlist_funcionario vf
                        Inner Join _distritales as dist On dist.dist_id=vf.fun_dist
                        Inner Join _componentes as c On c.com_id=vf.cm_id
                        Inner Join tipo_subactividad as tpa On tpa.tp_sact=c.tp_sact
                        Inner Join servicios_actividad as sa On sa.serv_id=c.serv_id
                        Inner Join _proyectofaseetapacomponente as pfe On pfe.pfec_id=c.pfec_id
                        Inner Join aperturaprogramatica as apg On apg.aper_id=pfe.aper_id
                        where vf.cm_id!=0 and apg.aper_gestion='.$gestion.'';
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


    /// Get apertura Programatica
    public function get_AperturasxRegional($proy_id){
        $gestion = session()->get('configuracion')['conf_gestion'] ?? null;
        $sql = '
            SELECT *
            from lista_poa_gastocorriente_nacional('.$gestion.')
            where proy_id='.$proy_id.'';
        $query = $this->query($sql);
        return $query->getRowArray();
    }




    /// Gestion Activo
/*    public function get_gestion_activo(){
        $sql = "SELECT * 
                FROM configuracion conf
                Inner Join mes as m On m.m_id=conf.conf_mes
                Inner Join trimestre_mes as trm On trm.trm_id=conf.conf_mes_otro
                WHERE conf.conf_estado = 1";
        $query = $this->query($sql);
        
        return $query->getRowArray();
    }*/


    /// Lista de Unidades Responsables segun el proyecto
    public function get_list_unidadresponsables($proy_id){
        $sql = 'SELECT *
                from vista_subactividades
                where proy_id='.$proy_id.'';
        $query = $this->query($sql);
        
        return $query->getResultArray();
    }

    //// get datos unidad responsable registrado para el seguimiento poa
    public function get_uniresponsable($com_id){
        $sql = 'SELECT *
                from vista_subactividades
                where com_id='.$com_id.'';
        $query = $this->query($sql);
        
        return $query->getRowArray();
    }

    /// Verif si existe ya unidad registrado para el Seguimiento POA
    public function verif_uresponsable_existente_seguimiento($com_id){
        $sql = 'SELECT *
                from vlist_funcionario
                where cm_id='.$com_id.'';
        $query = $this->query($sql);
        
        return $query->getResultArray();
    }

    /// Modulos Activos
/*    public function modulos($ide,$tp_adm){
        if($tp_adm==1){ /// Nacional
            $sql = "select *
                from modulo
                order by mod_id asc";
        }
        else{ /// regional / distrital
            $sql = "select * 
                from confi_modulo conf
                Inner Join modulo as mod On mod.mod_id=conf.mod_id
                WHERE conf.ide = ".$ide."
                order by conf.mod_id asc";
        }
        $query = $this->query($sql);
        return $query->getResultArray();
    }*/


    /// Sub Modulos Activos
/*    public function sub_modulos($id){
        $sql = "select *
                from modulo_menu
                where mod_id=".$id."
                order by sub_id asc";
        $query = $this->query($sql);
        return $query->getResultArray();
    }*/


    /// Get Buscando funcionario por su Usuario
    public function fun_usuario($usuario){
        // Usando el Query Builder de CI4 para una consulta segura
            $sql = 'select *
                from funcionario
                where fun_usuario=\''.$usuario.'\' and fun_estado!=3';

        $query = $this->db->query($sql);
        return $query->getRowArray();
    }




    /// Datos Regional Distrital
/*    public function datos_regional($dist_id){
        $sql = 'select *
                from _distritales ds
                Inner Join _departamentos as d On d.dep_id=ds.dep_id
                where ds.dist_id='.$dist_id.'';

        $query = $this->db->query($sql);
        return $query->getRowArray();
    }*/

    /// Datos Rol del funcionario
/*    public function get_rol_usuario($fun_id){
        $sql = '
        select f.fun_id,f.r_id,f.r_estado,r.r_estado,r.r_nombre
        from fun_rol f
        Inner Join rol as r On r.r_id=f.r_id
        where f.fun_id='.$fun_id.'
        group by f.fun_id,f.r_id,f.r_estado,r.r_estado,r.r_nombre';

        $query = $this->db->query($sql);
        return $query->getRowArray();
    }*/


    /// Verifica Usuario activo
/*    public function verificar_loggin($user_name, $password_plano, $captcha,$dat_captcha){
    $data = array(
        'bool'   => false,
        'fun_id' => null,
        'data'   => null,
        'message'=> 'Error de credenciales.'
    );

    $builder = $this->db->table('funcionario');
    $user = $builder->where('fun_usuario', $user_name)->get()->getRowArray();

        // 3. Verificar si el usuario existe
        if (!$user) {
            return $data;
        }

        if(md5($dat_captcha)!=$captcha){
            $data['message'] = 'Error en el código.';
            return $data; // Sale de la función, el bool sigue siendo false
        }

        if ($user['fun_estado'] == 3) {
            $data['message'] = 'Usuario inactivo.';
            return $data; // Sale de la función, el bool sigue siendo false
        }

        if($user['sw_pass']==0){
            // ---  MIGRAR A UN HASH SEGURO INMEDIATAMENTE (password_hash) ---
            $new_secure_hash = password_hash($password_plano, PASSWORD_DEFAULT); // Usa bcrypt/argon2
            // Actualizar la base de datos con el nuevo hash seguro y marcar como migrado
            $builder->where('fun_id', $user['fun_id'])
                    ->update([
                        'fun_password' => $new_secure_hash,
                        'sw_pass'      => 1
                    ]);
        }

        $builder = $this->db->table('funcionario');
        $user = $builder->where('fun_usuario', $user_name)->get()->getRowArray();

     //   5. Verificar la contraseña usando password_verify()
        if (password_verify($password_plano, $user['fun_password'])) {
            // --- Contraseña correcta: Preparar respuesta de éxito ---
            $data['bool']   = true;
            $data['fun_id'] = $user['fun_id'];
            $data['data']   = $user;
            $data['message'] = 'Login exitoso.';

            return $data;
        }
        else{
            return $data;
        }
    }*/





}