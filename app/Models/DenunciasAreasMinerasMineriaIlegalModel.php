<?php
namespace App\Models;
use CodeIgniter\Model;

class DenunciasAreasMinerasMineriaIlegalModel extends Model{

    protected $table      = 'mineria_ilegal.denuncias_areas_mineras';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';    

    protected $allowedFields = ['fk_denuncia', 'fk_area_minera','codigo_unico','area_minera'];

}
