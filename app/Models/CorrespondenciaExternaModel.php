<?php
namespace App\Models;
use CodeIgniter\Model;

class CorrespondenciaExternaModel extends Model{

    protected $table      = 'public.correspondencia_externa';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'fk_tramite',
        'fk_acto_administrativo',
        'fk_hoja_ruta',
        'fk_persona_externa',
        'cite',
        'fecha_cite',
        'referencia',
        'fojas',
        'adjuntos',
        'doc_digital',
        'estado',
        'fk_usuario_creador',
        'fk_usuario_editor',
        'fk_usuario_recepcion',
        'fecha_recepcion',
        'editar',
        'fk_tipo_documento_externo',
        'observacion_recepcion',
        'fk_usuario_atencion',
        'fecha_atencion',
        'observacion_atencion',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

}
