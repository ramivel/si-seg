<?php
namespace App\Models;
use CodeIgniter\Model;

class UsuariosModel extends Model{

    protected $table      = 'public.usuarios';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'nombre_completo','email','fk_oficina','usuario', 'pass', 'activo', 'fk_perfil', 'tramites', 'permisos', 'atencion', 'derivacion'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /*protected $validationRules    = [
        'usuario' => 'required|is_unique_with_schemas[cam.usuarios.usuario]',
        'pass' => 'required',
        'fk_persona' => 'required'
    ];
    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Lo sentimos el correo ya esta siendo usado'
        ]
    ];
    protected $skipValidation = false;*/

}
