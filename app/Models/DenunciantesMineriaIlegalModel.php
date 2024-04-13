<?php
namespace App\Models;
use CodeIgniter\Model;

class DenunciantesMineriaIlegalModel extends Model{

    protected $table      = 'mineria_ilegal.denunciantes';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [        
        'nombres',
        'apellidos',
        'documento_identidad',
        'expedido',
        'telefonos',
        'direccion',
        'domicilio_procesal',
        'email',
        'documento_identidad_digital',
        'fk_usuario_creador',
        'fk_usuario_editor',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

}
