<?php
namespace App\Models;
use CodeIgniter\Model;

class TipoSolicitudModel extends Model{

    protected $table      = 'public.tipo_solicitud';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';           

}
