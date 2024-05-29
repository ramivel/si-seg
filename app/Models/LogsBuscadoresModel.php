<?php
namespace App\Models;
use CodeIgniter\Model;

class LogsBuscadoresModel extends Model{    

    protected $table      = 'seguridad.logs_buscadores';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'tramite',
        'modulo',
        'fk_usuario',
        'texto',
        'campo',
        'fecha',
    ];

}
