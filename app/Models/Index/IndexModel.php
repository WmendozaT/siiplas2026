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

    /// Modulos Activos
    public function modulos($ide){
        $builder = $this->db->table('confi_modulo');
        $builder->where('ide', $ide); // El segundo parámetro se escapa automáticamente
        $query = $builder->get();
        
        return $query->getRowArray();
    }

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
    public function datos_regional($dist_id){
        $sql = 'select *
                from _distritales ds
                Inner Join _departamentos as d On d.dep_id=ds.dep_id
                where ds.dist_id='.$dist_id.'';

        $query = $this->db->query($sql);
        return $query->getRowArray();
    }

    /// Datos Rol del funcionario
    public function get_rol_usuario($fun_id){
        $sql = '
        select f.fun_id,f.r_id,f.r_estado,r.r_estado,r.r_nombre
        from fun_rol f
        Inner Join rol as r On r.r_id=f.r_id
        where f.fun_id='.$fun_id.'
        group by f.fun_id,f.r_id,f.r_estado,r.r_estado,r.r_nombre';

        $query = $this->db->query($sql);
        return $query->getRowArray();
    }


    /// Verifica Usuario activo
    public function verificar_loggin($user_name, $password_plano, $captcha,$dat_captcha){
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
    }





}