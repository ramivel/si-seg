<?php
namespace App\Models;
use CodeIgniter\Model;

class CorrelativoDocumentosModel extends Model{

    protected $table      = 'public.correlativo_documentos';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';    

    protected $allowedFields = ['gestion', 'sigla', 'correlativo_actual'];

}
