<?php
namespace App\Models;
use CodeIgniter\Model;

class DatosAreaMineraLPEModel extends Model{

    protected $table      = 'licencia_prospeccion_exploracion.datos_area_minera';
    protected $primaryKey = 'fk_hoja_ruta';
    protected $useAutoIncrement = false;
    protected $returnType     = 'array';

    protected $allowedFields = [
        'fk_hoja_ruta',
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