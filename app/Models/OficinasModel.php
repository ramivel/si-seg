<?php
namespace App\Models;
use CodeIgniter\Model;

class OficinasModel extends Model{

    protected $table      = 'public.oficinas';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'nombre', 
        'departamento', 
        'correlativo', 
        'telefonos', 
        'direccion', 
        'activo', 
        'regional_busqueda', 
        'fk_oficina_sincobol', 
        'fk_oficina_derivacion', 
        'fk_usuario_creador', 
        'fk_usuario_editor', 
        'desconcentrado',
        'departamentos_atencion',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

}
