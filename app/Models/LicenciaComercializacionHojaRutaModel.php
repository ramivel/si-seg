<?php
namespace App\Models;
use CodeIgniter\Model;

class LicenciaComercializacionHojaRutaModel extends Model{

    protected $table      = 'licencia_comercializacion.hoja_ruta';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'fk_oficina',
        'correlativo',
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
        'fk_usuario_actual',
        'fk_usuario_creador',
        'fk_usuario_destino',
        'fk_usuario_eliminador',
        'fecha_hoja_ruta',
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
