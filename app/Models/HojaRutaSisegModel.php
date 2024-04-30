<?php
namespace App\Models;
use CodeIgniter\Model;

class HojaRutaSisegModel extends Model{

    protected $DBGroup = 'sincobol';

    protected $table      = 'sincobol.hoja_ruta_siseg';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'fk_hoja_ruta',
        'fk_tramite',
        'fk_siseg',
        'usuario',
        'fecha',
        'tabla_siseg',
    ];

}
