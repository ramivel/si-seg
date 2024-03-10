<?php
namespace App\Models;
use CodeIgniter\Model;

class CoordenadasWebMineriaIlegalModel extends Model{

    protected $table      = 'mineria_ilegal.coordenadas_web';
    protected $primaryKey = 'id';
    protected $useSoftDeletes = true;

    protected $returnType     = 'array';

    protected $allowedFields = ['fk_denuncia_web', 'latitud', 'longitud'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
