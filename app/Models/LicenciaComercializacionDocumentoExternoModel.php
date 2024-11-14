<?php
namespace App\Models;
use CodeIgniter\Model;

class LicenciaComercializacionDocumentoExternoModel extends Model{

    protected $table      = 'licencia_comercializacion.documento_externo';
    protected $primaryKey = 'fk_hoja_ruta';
    protected $useAutoIncrement = false;
    protected $returnType     = 'array';

    protected $allowedFields = [
        'fk_hoja_ruta',
        'fk_persona_externa',
        'cite',
        'fecha_cite',
        'referencia',
        'fojas',
        'adjuntos',
        'doc_digital',
        'estado',
        'editar',
        'fecha_recepcion',
        'fk_usuario_recepcion',
    ];

}