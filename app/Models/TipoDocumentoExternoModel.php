<?php
namespace App\Models;
use CodeIgniter\Model;

class TipoDocumentoExternoModel extends Model{

    protected $table      = 'public.tipo_documento_externo';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['nombre', 'descripcion', 'dias_limite', 'dias_intermedio', 'notificar',];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';    

}
