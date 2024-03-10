<?php
namespace App\Models;
use CodeIgniter\Model;

class DerivacionModel extends Model{

    protected $table      = 'public.derivacion';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'fk_acto_administrativo', 'domicilio_legal', 'domicilio_procesal', 'telefono_solicitante',
        'fk_estado_tramite_padre', 'fk_estado_tramite_hijo', 'observaciones',
        'instruccion', 'estado', 'motivo_conclusion', 'fk_usuario_remitente', 'fk_usuario_destinatario',
        'fk_usuario_creador', 'fk_usuario_modificador', 'fk_usuario_eliminador', 'fecha_atencion', 'motivo_anexo', 'fecha_devolucion', 'fk_usuario_responsable', 'fecha_recepcion',
        'recurso_jerarquico',
        'recurso_revocatoria',
        'oposicion',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

}
