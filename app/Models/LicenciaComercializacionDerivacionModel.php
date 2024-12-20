<?php
namespace App\Models;
use CodeIgniter\Model;

class LicenciaComercializacionDerivacionModel extends Model{

    protected $table      = 'licencia_comercializacion.derivacion';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'fk_hoja_ruta',
        'fk_estado_tramite_padre',
        'fk_estado_tramite_hijo',
        'estado',
        'observaciones',
        'instruccion',
        'motivo_conclusion',
        'fecha_recepcion',
        'fecha_atencion',
        'fecha_devolucion',
        'fecha_actualizacion_estado',
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
