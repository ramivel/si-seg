<?php
namespace App\Models;
use CodeIgniter\Model;

class MigrarCAMModel extends Model{

    protected $table      = 'migracion.contratos_administrativos_mineros';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType     = 'array';

    protected $allowedFields = [
        'fk_oficina',
        'fk_hoja_ruta_sincobol',
        'fk_area_minera',
        'codigo_estado',
        'codigo_subestado',
        'fk_usuario_remitente',
        'fk_usuario_destinatario',
        'tipo character',
        'migrado',
    ];

}