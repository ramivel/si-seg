<?php
namespace App\Models;
use CodeIgniter\Model;

class TramitesModel extends Model{

    protected $table      = 'public.tramites';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['fk_tipo_hoja_ruta', 'nombre',  'controlador', 'menu', 'activo', 'correlativo', 'fk_usuario_creador', 'fk_usuario_editor', 'fk_usuario_eliminador'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

}
