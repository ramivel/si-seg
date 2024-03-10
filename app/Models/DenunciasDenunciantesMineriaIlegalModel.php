<?php
namespace App\Models;
use CodeIgniter\Model;

class DenunciasDenunciantesMineriaIlegalModel extends Model{

    protected $table      = 'mineria_ilegal.denuncias_denunciantes';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';    

    protected $allowedFields = ['fk_denuncia', 'fk_denunciante'];

}
