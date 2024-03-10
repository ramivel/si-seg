<?php
namespace App\Models;
use CodeIgniter\Model;

class CarpetaArchivoSincobolModel extends Model{

    protected $DBGroup = 'sincobol';

    protected $table      = 'sincobol.carpeta_archivo';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'nombre', 'fk_persona', 'fk_tipo_hoja_ruta', 'fk_cargo'
    ];    

}
