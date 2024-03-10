<?php
namespace App\Models;
use CodeIgniter\Model;

class EstadoTramiteModel extends Model{

    protected $table      = 'public.estado_tramite';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['fk_tramite', 'fk_estado_padre', 'nombre', 'descripcion', 'orden', 'padre', 'dias_intermedio', 'dias_limite', 'notificar', 'anexar_documentos', 'finalizar'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';    

}
