<?php
namespace App\Models;
use CodeIgniter\Model;

class DenunciasHrSincobolMineriaIlegalModel extends Model{

    protected $table      = 'mineria_ilegal.denuncias_hr_sincobol';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';    

    protected $allowedFields = ['fk_denuncia', 'fk_hoja_ruta'];

}
