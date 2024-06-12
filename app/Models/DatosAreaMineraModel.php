<?php
namespace App\Models;
use CodeIgniter\Model;

class DatosAreaMineraModel extends Model{

    protected $table      = 'public.datos_area_minera';
    protected $primaryKey = 'fk_acto_administrativo';
    protected $useAutoIncrement = false;
    protected $returnType     = 'array';

    protected $allowedFields = [
        'fk_acto_administrativo',
        'codigo_unico',
        'denominacion',
        'extension',
        'departamentos',
        'provincias',
        'municipios',
        'regional',
        'area_protegida',
        'area_protegida_adicional',
        'representante_legal',
        'nacionalidad',
        'titular',
        'clasificacion_titular',
        'the_geom',
    ];

}