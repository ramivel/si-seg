<?php
namespace App\Models;
use CodeIgniter\Model;

class DocumentosModel extends Model{

    protected $table      = 'public.documentos';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['fk_acto_administrativo', 'fk_hoja_ruta', 'fk_tipo_documento', 'fk_derivacion', 'correlativo', 'ciudad','fecha', 'referencia', 'estado',
    'fk_usuario_a', 'fk_usuario_via', 'fk_usuario_de', 'contenido', 'motivo_anulacion', 'fk_tramite', 'fk_usuario_creador', 
    'fk_usuario_editor', 'fk_usuario_sol_anulacion', 'fk_usuario_aut_anulacion','doc_digital', 'fk_usuario_doc_digital', 'fecha_notificacion'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

}
