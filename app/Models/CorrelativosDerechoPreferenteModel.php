<?php
namespace App\Models;
use CodeIgniter\Model;

class CorrelativosDerechoPreferenteModel extends Model{

    protected $table      = 'cam_dp.correlativos';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';

    protected $allowedFields = ['gestion', 'sigla', 'correlativo_actual'];

}
