<?php
namespace App\Models;
use CodeIgniter\Model;

class HojaRutaLPEModel extends Model{

    protected $table      = 'licencia_prospeccion_exploracion.hoja_ruta';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'fk_solicitud_licencia_contrato',
        'fk_area_minera',
        'fk_hoja_ruta_sincobol',
        'fk_oficina',
        'correlativo',
        'fecha_mecanizada',
        'editar',
        'ultimo_estado',
        'ultimo_fecha_derivacion',
        'ultimo_fk_estado_tramite_padre',
        'ultimo_fk_estado_tramite_hijo',
        'ultimo_fecha_actualizacion_estado',
        'ultimo_instruccion',
        'ultimo_fk_usuario_remitente',
        'ultimo_fk_usuario_destinatario',
        'ultimo_fk_usuario_responsable',
        'ultimo_fk_documentos',
        'ultimo_recurso_jerarquico',
        'ultimo_recurso_revocatoria',
        'ultimo_oposicion',
        'fk_usuario_actual',
        'fk_usuario_creador',
        'fk_usuario_eliminador',
        'documentos_apm',
        'estado_tramite_apm',
        'codigo_seguimiento',
        'caja_documental',
        'fojas',
        'gestion_archivo',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

}
