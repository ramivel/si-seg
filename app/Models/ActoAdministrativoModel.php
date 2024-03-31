<?php
namespace App\Models;
use CodeIgniter\Model;

class ActoAdministrativoModel extends Model{

    protected $table      = 'public.acto_administrativo';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'fk_solicitud_licencia_contrato',
        'fk_area_minera',
        'fk_hoja_ruta',
        'fk_oficina',
        'correlativo',
        'fecha_mecanizada',
        'fk_usuario_actual',
        'ultimo_fk_estado_tramite_padre',
        'ultimo_fk_estado_tramite_hijo',
        'ultimo_fecha_notificacion',
        'ultimo_instruccion',
        'ultimo_fk_usuario_remitente',
        'ultimo_estado',
        'ultimo_fecha_derivacion',
        'ultimo_fecha_atencion',
        'fk_usuario_creador',
        'fk_usuario_eliminador',
        'ultimo_fk_usuario_responsable',
        'ultimo_fk_documentos',
        'ultimo_fk_usuario_destinatario',
        'editar',
        'fk_tipo_hoja_ruta',
        'estado_tramite_apm',
        'documentos_apm',
        'ultimo_recurso_jerarquico',
        'ultimo_recurso_revocatoria',
        'ultimo_oposicion',
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