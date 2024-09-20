<?php
namespace App\Models;
use CodeIgniter\Model;

class SolicitudDerivacionModel extends Model{

    protected $table      = 'cam_dp.solicitud_derivacion';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'fk_solicitud_derecho_preferente',
        'fk_estado_tramite_padre',
        'fk_estado_tramite_hijo',
        'domicilio_legal',
        'domicilio_procesal',
        'telefono_solicitante',
        'observaciones',
        'instruccion',
        'motivo_conclusion',
        'motivo_anexo',
        'estado',
        'fk_usuario_remitente',
        'fk_usuario_destinatario',
        'fk_usuario_responsable',
        'fk_usuario_creador',
        'fk_usuario_modificador',
        'fk_usuario_eliminador',
        'fecha_recepcion',
        'fecha_atencion',
        'fecha_devolucion',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

}
