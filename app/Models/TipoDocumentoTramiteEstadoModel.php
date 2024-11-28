<?php
namespace App\Models;
use CodeIgniter\Model;

class TipoDocumentoTramiteEstadoModel extends Model{

    protected $table      = 'public.tipo_documento_tramite_estado';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'fk_tipo_documento',
        'fk_tramite',
        'fk_estado_tramite_padre',
        'fk_estado_tramite_hijo',
        'cambia_estado',
        'justificacion',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

}
