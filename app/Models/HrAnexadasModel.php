<?php
namespace App\Models;
use CodeIgniter\Model;

class HrAnexadasModel extends Model{

    protected $table      = 'public.hr_anexadas';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'fk_derivacion', 'fk_hoja_ruta'
    ];

    /*protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';*/

}
