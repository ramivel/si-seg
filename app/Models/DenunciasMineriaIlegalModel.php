<?php
namespace App\Models;
use CodeIgniter\Model;

class DenunciasMineriaIlegalModel extends Model{

    protected $table      = 'mineria_ilegal.denuncias';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'fk_municipio',
        'fk_tipo_denuncia',
        'correlativo',
        'comunidad_localidad',
        'descripcion_lugar',
        'autores',
        'descripcion_materiales',
        'motivo_archivo',
        'fk_usuario_creador',
        'departamento',
        'provincia',
        'municipio',
        'fk_usuario_editor',
        'tiene_area_minera',
        'persona_juridica',
        'areas_denunciadas',
        'origen_oficio',
        'enlace',
        'informe_tecnico_numero',
        'informe_tecnico_fecha',
        'informe_tecnico_adjunto',
        'descripcion_oficio',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

}
