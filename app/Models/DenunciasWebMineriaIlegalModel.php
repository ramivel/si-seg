<?php
namespace App\Models;
use CodeIgniter\Model;

class DenunciasWebMineriaIlegalModel extends Model{

    protected $table      = 'mineria_ilegal.denuncias_web';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'fk_denuncia',
        'fk_municipio',
        'correlativo',
        'nombres',
        'apellidos',
        'documento_identidad',
        'expedido',
        'telefonos',
        'email',
        'direccion',
        'documento_identidad_digital',
        'comunidad_localidad',
        'descripcion_lugar',
        'autores',
        'persona_juridica',
        'descripcion_materiales',
        'areas_denunciadas',
        'fotografia_uno',
        'fotografia_dos',
        'fotografia_tres',
        'codigo_seguimiento',
        'ip',
        'navegador',
        'estado',
        'fk_usuario_atencion',
        'fk_denuncia',
        'so',
        'motivo_archivado',
        'fk_usuario_archivado',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

}
