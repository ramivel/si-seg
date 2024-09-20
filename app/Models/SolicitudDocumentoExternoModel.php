<?php
namespace App\Models;
use CodeIgniter\Model;

class SolicitudDocumentoExternoModel extends Model{

    protected $table      = 'cam_dp.solicitud_documento_externo';
    protected $primaryKey = 'fk_solicitud_derecho_preferente';
    protected $useAutoIncrement = false;
    protected $returnType     = 'array';

    protected $allowedFields = [
        'fk_solicitud_derecho_preferente',
        'fk_persona_externa',
        'cite',
        'fecha_cite',
        'referencia',
        'fojas',
        'adjuntos',
        'doc_digital',
    ];

}