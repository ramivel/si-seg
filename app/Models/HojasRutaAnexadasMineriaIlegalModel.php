<?php
namespace App\Models;
use CodeIgniter\Model;

class HojasRutaAnexadasMineriaIlegalModel extends Model{

    protected $table      = 'mineria_ilegal.hojas_ruta_anexadas';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'fk_derivacion', 'fk_hoja_ruta', 'fk_hoja_ruta_sincobol', 'fk_usuario_creador', 'motivo_anexo'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

}
