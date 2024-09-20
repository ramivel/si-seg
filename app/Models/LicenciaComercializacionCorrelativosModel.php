<?php
namespace App\Models;
use CodeIgniter\Model;

class LicenciaComercializacionCorrelativosModel extends Model{

    protected $table      = 'licencia_comercializacion.correlativos';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';

    protected $allowedFields = ['gestion', 'sigla', 'correlativo_actual'];

}
