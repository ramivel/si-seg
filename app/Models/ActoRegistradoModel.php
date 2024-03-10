<?php
namespace App\Models;
use CodeIgniter\Model;

class ActoRegistradoModel extends Model{
    protected $table = 'acto_registrado';
    protected $primaryKey = "id";

    protected $returnType = "array";
    protected $useSoftDeletes = true; // campo para eliminar delete

    protected $allowedFields = [
        //campos de la tabla
    ];

}
