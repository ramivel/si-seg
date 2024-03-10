<?php
namespace App\Models;
use CodeIgniter\Model;

class DerivacionSincobolModel extends Model{

    protected $DBGroup = 'sincobol';

    protected $table      = 'sincobol.derivacion';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'estado', 'fecha_conclusion', 'motivo_conclusion', 'fk_carpeta_archivo', 'fk_hoja_ruta_adjuntado'
    ];    

}
