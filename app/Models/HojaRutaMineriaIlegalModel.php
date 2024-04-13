<?php
namespace App\Models;
use CodeIgniter\Model;

class HojaRutaMineriaIlegalModel extends Model{

    protected $table      = 'mineria_ilegal.hoja_ruta';
    protected $primaryKey = 'id';    

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'fk_denuncia',
        'fk_oficina',
        'correlativo',
        'fk_usuario_actual',
        'ultimo_fecha_derivacion',
        'ultimo_estado',
        'ultimo_fk_estado_tramite_padre',
        'ultimo_fk_estado_tramite_hijo',
        'ultimo_instruccion',
        'ultimo_fk_usuario_responsable',
        'ultimo_fk_usuario_remitente',
        'ultimo_fk_usuario_destinatario',
        'ultimo_fk_documentos',
        'fk_usuario_creador',
        'fk_usuario_destino',
        'fojas',
        'adjuntos',
        'editar',
        'fecha_hoja_ruta',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

}
