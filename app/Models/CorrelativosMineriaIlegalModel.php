<?php
namespace App\Models;
use CodeIgniter\Model;

class CorrelativosMineriaIlegalModel extends Model{

    protected $table      = 'mineria_ilegal.correlativos';
    protected $primaryKey = 'id';    

    protected $returnType     = 'array';    

    protected $allowedFields = ['gestion', 'sigla', 'correlativo_actual'];

}
