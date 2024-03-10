<?php
namespace App\Models;
use CodeIgniter\Model;

class AdjuntosMineriaIlegalModel extends Model{

    protected $table      = 'mineria_ilegal.adjuntos';
    protected $primaryKey = 'id';
    protected $useSoftDeletes = true;

    protected $returnType     = 'array';

    protected $allowedFields = [
        'fk_denuncia',
        'nombre',
        'tipo',
        'adjunto',
        'cite',
        'fecha_cite',
        'referencia',
        'fojas',
        'adjuntos',
        'fk_usuario_creador',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
