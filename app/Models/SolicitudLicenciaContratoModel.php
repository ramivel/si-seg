<?php
namespace App\Models;
use CodeIgniter\Model;

class SolicitudLicenciaContratoModel extends Model{

    protected $DBGroup = 'sincobol';

    protected $table      = 'contratos_licencias.solicitud_licencia_contrato';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'fk_acto_administrativo'
    ];    

}
