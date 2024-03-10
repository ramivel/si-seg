<?php
namespace App\Models;
use CodeIgniter\Model;

class MunicipiosModel extends Model{

    protected $table      = 'mineria_ilegal.municipios';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';    

    protected $allowedFields = ['municipio','provincia','departamento','codigo','activo',];

}
