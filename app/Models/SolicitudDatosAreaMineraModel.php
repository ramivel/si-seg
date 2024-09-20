<?php
namespace App\Models;
use CodeIgniter\Model;

class SolicitudDatosAreaMineraModel extends Model{

    protected $table      = 'cam_dp.solicitud_datos_area_minera';
    protected $primaryKey = 'id';
    protected $returnType     = 'array';

    protected $allowedFields = [
        'fk_solicitud_derecho_preferente',
        'fk_area_minera',
        'codigo_unico',
        'denominacion',
        'tipo_area',
        'extension',
        'departamentos',
        'provincias',
        'municipios',
        'regional',
        'representante_legal',
        'nacionalidad',
        'titular',
        'clasificacion_titular',
        'extension_solicitada',
    ];

}