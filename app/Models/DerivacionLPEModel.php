<?php
namespace App\Models;
use CodeIgniter\Model;

class DerivacionLPEModel extends Model{

    protected $table      = 'licencia_prospeccion_exploracion.derivacion';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'fk_hoja_ruta',
        'fk_estado_tramite_padre',
        'fk_estado_tramite_hijo',
        'estado',
        'domicilio_legal',
        'domicilio_procesal',
        'telefono_solicitante',
        'observaciones',
        'instruccion',
        'motivo_conclusion',
        'fecha_recepcion',
        'fecha_atencion',
        'fecha_devolucion',
        'fecha_actualizacion_estado',
        'recurso_jerarquico',
        'recurso_revocatoria',
        'oposicion',
        'fk_usuario_remitente',
        'fk_usuario_destinatario',
        'fk_usuario_responsable',
        'fk_usuario_creador',
        'fk_usuario_modificador',
        'fk_usuario_eliminador',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

}
