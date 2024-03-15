<?php
namespace App\Models;
use CodeIgniter\Model;

class DerivacionMineriaIlegalModel extends Model{

    protected $table      = 'mineria_ilegal.derivacion';
    protected $primaryKey = 'id';    

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'fk_hoja_ruta',
        'fk_estado_tramite_padre',
        'fk_estado_tramite_hijo',
        'instruccion',
        'estado',
        'fk_usuario_responsable',
        'fk_usuario_remitente',
        'fk_usuario_destinatario',
        'fk_usuario_creador',
        'fk_usuario_modificador',
        'fk_usuario_eliminador',
        'fecha_recepcion',
        'fecha_atencion',
        'fecha_devolucion',
        'motivo_conclusion',
        'motivo_anexo',
        'observaciones'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

}
