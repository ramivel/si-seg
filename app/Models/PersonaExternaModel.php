<?php
namespace App\Models;
use CodeIgniter\Model;

class PersonaExternaModel extends Model{

    protected $table      = 'public.persona_externa';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'nombres',
        'apellidos',
        'documento_identidad',
        'expedido',
        'telefonos',
        'direccion',
        'email',
        'documento_identidad_digital',
        'institucion',
        'cargo',
        'fk_usuario_creador',
        'fk_usuario_editor',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

}
