<?php
namespace App\Models;
use CodeIgniter\Model;

class TipoDocumentoModel extends Model{

    protected $table      = 'public.tipo_documento';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['nombre', 'descripcion', 'sigla', 'activo', 'correlativo_compartido', 'plantilla', 'tramites', 'perfiles',
    'fk_usuario_creador', 'fk_usuario_editor', 'fk_usuario_eliminador', 'notificacion'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

}
