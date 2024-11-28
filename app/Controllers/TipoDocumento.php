<?php

namespace App\Controllers;

use App\Models\PerfilesModel;
use App\Models\TipoDocumentoModel;
use App\Models\TipoDocumentoTramiteEstadoModel;
use App\Models\TramitesModel;

class TipoDocumento extends BaseController
{
    protected $titulo = 'Administrar Tipos de Documentos';
    protected $controlador = 'tipo_documento/';
    protected $carpeta = 'tipo_documento/';
    protected $menuActual = 'tipo_documento';
    protected $rutaArchivos = 'archivos/tipo_documento/';

    public function index()
    {
        $tramitesModel = new TramitesModel();
        $tmp=$tramitesModel->select('id, nombre')->findAll();
        $tramites = array();
        foreach($tmp as $row){
            $tramites[$row['id']] = $row['nombre'];
        }
        $perfilesModel = new PerfilesModel();
        $tmp=$perfilesModel->select('id, nombre')->findAll();
        $perfiles = array();
        foreach($tmp as $row){
            $perfiles[$row['id']] = $row['nombre'];
        }
        $tipoDocumentoModel = new TipoDocumentoModel();
        $datos = $tipoDocumentoModel->orderBy('nombre','asc')->findAll();
        $campos_listar=array('Nombre','Sigla','Fecha Notificación','Tramites','Cargos');
        $campos_reales=array('nombre','sigla','notificacion','tramites','perfiles');
        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Listado';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['datos'] = $datos;
        $contenido['campos_listar'] = $campos_listar;
        $contenido['campos_reales'] = $campos_reales;
        $contenido['subtitulo'] = 'Listado';
        $contenido['controlador'] = $this->controlador;
        $contenido['perfiles'] = $perfiles;
        $contenido['tramites'] = $tramites;
        $data['content'] = view($this->carpeta.'index', $contenido);
        $data['menu_actual'] = $this->menuActual;
        $data['tramites_menu'] = $this->tramitesMenu();
        echo view('templates/template', $data);
    }

    public function agregar(){

        $tramitesModel = new TramitesModel();
        $perfilesModel = new PerfilesModel();
        if ($this->request->getPost()) {
            $validation = $this->validate([
                'nombre' => [
                    'rules' => 'required',
                ],
                'sigla' => [
                    'rules' => 'required',
                ],
                'perfiles' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar al menos un Cargo'
                    ]
                ],
                'tramites' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar al menos un Tramite'
                    ]
                ],
            ]);
            if(!$validation){
                $contenido['validation'] = $this->validator;
            }else{
                $tipoDocumentoModel = new TipoDocumentoModel();
                $data = array(
                    'nombre' => mb_strtoupper($this->request->getPost('nombre')),
                    'sigla' => mb_strtoupper($this->request->getPost('sigla')),
                    'descripcion' => mb_strtoupper($this->request->getPost('descripcion')),
                    'perfiles' => implode(',',$this->request->getPost('perfiles')),
                    'tramites' => implode(',',$this->request->getPost('tramites')),
                    'fk_usuario_creador' => session()->get('registroUser'),
                    'notificacion' => $this->request->getPost('notificacion')=='true'?'true':'false',
                );
                $adjunto = $this->request->getFile('adjunto');
                if(!empty($adjunto) && $adjunto->getSize()>0){
                    $nombreAdjunto = $adjunto->getRandomName();
                    $adjunto->move($this->rutaArchivos,$nombreAdjunto);
                    $data['plantilla'] = $nombreAdjunto;
                }
                if($tipoDocumentoModel->save($data) === false)
                    session()->setFlashdata('fail', $tipoDocumentoModel->errors());
                else
                    session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información.');
                return redirect()->to($this->controlador);
            }
        }

        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Agregar Nuevo';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['accion'] = $this->controlador.'agregar';
        $contenido['validation'] = $this->validator;
        $contenido['tramites'] = $tramitesModel->findAll();
        $contenido['perfiles'] = $perfilesModel->orderBy('nombre', 'ASC')->findAll();
        $contenido['controlador'] = $this->controlador;
        $data['content'] = view($this->carpeta.'agregar', $contenido);
        $data['menu_actual'] = $this->menuActual;
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['validacion_js'] = 'tipo-documento-validation.js';
        echo view('templates/template', $data);
    }
    public function asignarTramite($id){
        $tipoDocumentoModel = new TipoDocumentoModel();
        $fila = $tipoDocumentoModel->find($id);
        if($fila){
            $db = \Config\Database::connect();
            $campos_listar=array('Trámite','Cambia Estado','Estado Trámite','Justificación');
            $campos_reales=array('tramite','cambia_estado','estado_tramite','justificacion');
            $campos = array(
                "tdte.id","t.nombre as tramite", "tdte.cambia_estado",
                "CASE WHEN tdte.justificacion THEN 'SI' ELSE 'NO' END as justificacion",
                "CASE WHEN tdte.fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'. ',etp.nombre,'<br>',etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE CONCAT(etp.orden,'. ',etp.nombre) END as estado_tramite"
            );
            $where = array(
                'tdte.deleted_at' => NULL,
                'tdte.fk_tipo_documento' => $fila['id'],
            );
            $builder = $db->table('public.tipo_documento_tramite_estado as tdte')
            ->select($campos)
            ->join('public.tramites as t', 'tdte.fk_tramite = t.id', 'left')
            ->join('estado_tramite as etp', 'tdte.fk_estado_tramite_padre = etp.id', 'left')
            ->join('estado_tramite as eth', 'tdte.fk_estado_tramite_hijo = eth.id', 'left')
            ->where($where)
            ->orderBY('tdte.id', 'DESC');
            $datos = $builder->get()->getResultArray();
            $cabera['titulo'] = $this->titulo;
            $cabera['subtitulo'] = 'Asignación de Trámite y Estados';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['fila'] = $fila;
            $contenido['accion'] = $this->controlador.'guardar_asignar_tramite';
            $contenido['tramites'] = array(''=>'SELECCIONE UNA OPCIÓN') + $this->obtenerTramitesSeleccion();
            $contenido['datos'] = $datos;
            $contenido['campos_listar'] = $campos_listar;
            $contenido['campos_reales'] = $campos_reales;
            $contenido['controlador'] = $this->controlador;
            $data['content'] = view($this->carpeta.'asignar_tramite', $contenido);
            $data['menu_actual'] = $this->menuActual;
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['validacion_js'] = 'tipo-documento-asignar-tramite-validation.js';
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador);
        }
    }
    public function guardarAsignarTramite(){
        $tipoDocumentoModel = new TipoDocumentoModel();
        $id = $this->request->getPost('id');
        if($fila = $tipoDocumentoModel->find($id)){
            $validation = $this->validate([
                'cambia_estado' => [
                    'rules' => 'required',
                ],
                'fk_tramite' => [
                    'rules' => 'required',
                ],
            ]);
            if(!$validation){
                session()->setFlashdata('fail', 'Existe errores en el formulario.');
            }else{
                $tipoDocumentoTramiteEstadoModel = new TipoDocumentoTramiteEstadoModel();
                $cambia_estado = $this->request->getPost('cambia_estado');
                $dataTipoDocumento = array(
                    'fk_tipo_documento' => $fila['id'],
                    'fk_tramite' => $this->request->getPost('fk_tramite'),
                    'cambia_estado' => $cambia_estado,
                    'justificacion' => $this->request->getPost('justificacion')=='true'?'true':'false',
                );
                if($cambia_estado == 'SI'){
                    $dataTipoDocumento['fk_estado_tramite_padre'] = $this->request->getPost('fk_estado_tramite');
                    $dataTipoDocumento['fk_estado_tramite_hijo'] = ((!empty($this->request->getPost('fk_estado_tramite_hijo'))) ? $this->request->getPost('fk_estado_tramite_hijo') : NULL);
                }else{
                    $dataTipoDocumento['fk_estado_tramite_padre'] = NULL;
                    $dataTipoDocumento['fk_estado_tramite_hijo'] = NULL;
                }
                if($existe = $tipoDocumentoTramiteEstadoModel->where(array('fk_tipo_documento'=>$fila['id'],'fk_tramite'=>$this->request->getPost('fk_tramite'),'deleted_at'=>NULL))->first()){
                    $dataTipoDocumento['id'] = $existe['id'];
                    if($tipoDocumentoTramiteEstadoModel->save($dataTipoDocumento) === false)
                        session()->setFlashdata('fail', $tipoDocumentoTramiteEstadoModel->errors());
                    else
                        session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información.');
                }else{
                    if($tipoDocumentoTramiteEstadoModel->insert($dataTipoDocumento) === false)
                        session()->setFlashdata('fail', $tipoDocumentoTramiteEstadoModel->errors());
                    else
                        session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información.');
                }
            }
            return redirect()->to($this->controlador.'asignar_tramite/'.$fila['id']);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador);
        }
    }
    public function editar($id){
        $tipoDocumentoModel = new TipoDocumentoModel();
        $fila = $tipoDocumentoModel->find($id);
        if($fila){
            $tramitesModel = new TramitesModel();
            $perfilesModel = new PerfilesModel();
            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Editar Registro';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['fila'] = $fila;
            $contenido['id'] = $id;
            $contenido['accion'] = $this->controlador.'guardar_editar';
            $contenido['validation'] = $this->validator;
            $contenido['tramites'] = $tramitesModel->findAll();
            $contenido['tramites_elegidos'] = explode(',',$fila['tramites']);
            $contenido['perfiles'] = $perfilesModel->orderBy('nombre', 'ASC')->findAll();
            $contenido['perfiles_elegidos'] = explode(',',$fila['perfiles']);
            $contenido['controlador'] = $this->controlador;
            $data['content'] = view($this->carpeta.'editar', $contenido);
            $data['menu_actual'] = $this->menuActual;
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['validacion_js'] = 'tipo-documento-editar-validation.js';
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador);
        }
    }
    public function guardar_editar(){
        $id = $this->request->getPost('id');
        if(isset($id) && $id>0){
            $tramitesModel = new TramitesModel();
            $perfilesModel = new PerfilesModel();
            $tipoDocumentoModel = new TipoDocumentoModel();
            $validation = $this->validate([
                'nombre' => [
                    'rules' => 'required',
                ],
                'sigla' => [
                    'rules' => 'required',
                ],
                'perfiles' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar al menos un Cargo'
                    ]
                ],
                'tramites' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar al menos un Tramite'
                    ]
                ],
            ]);
            if(!$validation){
                $fila = $tipoDocumentoModel->find($id);
                $cabera['titulo'] = $this->titulo;
                $cabera['navegador'] = true;
                $cabera['subtitulo'] = 'Editar Registro';
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['fila'] = $fila;
                $contenido['id'] = $id;
                $contenido['accion'] = $this->controlador.'guardar_editar';
                $contenido['validation'] = $this->validator;
                $contenido['controlador'] = $this->controlador;
                $contenido['tramites'] = $tramitesModel->findAll();
                $contenido['perfiles'] = $perfilesModel->orderBy('nombre', 'ASC')->findAll();
                $data['content'] = view($this->carpeta.'editar', $contenido);
                $data['menu_actual'] = $this->menuActual;
                $data['tramites_menu'] = $this->tramitesMenu();
                $data['validacion_js'] = 'tipo-documento-editar-validation.js';
                echo view('templates/template', $data);
            }else{
                $data = array(
                    'id' => $id,
                    'nombre' => mb_strtoupper($this->request->getPost('nombre')),
                    'sigla' => mb_strtoupper($this->request->getPost('sigla')),
                    'descripcion' => mb_strtoupper($this->request->getPost('descripcion')),
                    'perfiles' => implode(',',$this->request->getPost('perfiles')),
                    'tramites' => implode(',',$this->request->getPost('tramites')),
                    'fk_usuario_editor' => session()->get('registroUser'),
                    'notificacion' => $this->request->getPost('notificacion')=='true'?'true':'false',
                );
                $adjunto = $this->request->getFile('adjunto');
                if(!empty($adjunto) && $adjunto->getSize()>0){
                    if(file_exists($this->rutaArchivos.$this->request->getPost('adjunto_anterior')))
                        @unlink($this->rutaArchivos.$this->request->getPost('adjunto_anterior'));
                    //$nombreAdjunto = $adjunto->getName();
                    $nombreAdjunto = $adjunto->getRandomName();
                    $adjunto->move($this->rutaArchivos,$nombreAdjunto);
                    $data['plantilla'] = $nombreAdjunto;
                }
                if($tipoDocumentoModel->save($data) === false)
                    session()->setFlashdata('fail', $tipoDocumentoModel->errors());
                else
                    session()->setFlashdata('success', 'Se Actualizo Correctamente la Información.');
                return redirect()->to($this->controlador);
            }
        }
    }
    public function eliminar($id){
        $tipoDocumentoModel = new TipoDocumentoModel();
        $fila = $tipoDocumentoModel->find($id);
        if($fila){
            $fila['fk_usuario_eliminador'] = session()->get('registroUser');
            if($tipoDocumentoModel->save($fila) === false){
                session()->setFlashdata('fail', $tipoDocumentoModel->errors());
            }else{
                if(file_exists($this->rutaArchivos.$fila['plantilla']))
                    unlink($this->rutaArchivos.$fila['plantilla']);
                $tipoDocumentoModel->delete($fila['id']);
                session()->setFlashdata('success', 'Se elimino correctamente al Registro.');
            }
        }else{
            session()->setFlashdata('fail', 'El Registro no existe.');
        }
        return redirect()->to($this->controlador);
    }
    public function eliminarAsignacion($id){
        $tipoDocumentoTramiteEstadoModel = new TipoDocumentoTramiteEstadoModel();
        if($fila = $tipoDocumentoTramiteEstadoModel->find($id)){
            $tipoDocumentoTramiteEstadoModel->delete($fila['id']);
            session()->setFlashdata('success', 'Se elimino correctamente al Registro.');
            return redirect()->to($this->controlador.'asignar_tramite/'.$fila['fk_tipo_documento']);
        }
        session()->setFlashdata('fail', 'El Registro no existe.');
        return redirect()->to($this->controlador);
    }
    public function descargar($id){
        $tipoDocumentoModel = new TipoDocumentoModel();
        $fila = $tipoDocumentoModel->find($id);
        if($fila){
            return $this->response->download($this->rutaArchivos.$fila['plantilla'], null);
        }else{
            session()->setFlashdata('fail', 'El Archivo no existe.');
            return redirect()->to($this->controlador);
        }
    }

    private function obtenerTramitesSeleccion(){
        $tramitesModel = new TramitesModel();
        $resultado = array();
        $tramites = $tramitesModel->findAll();
        foreach($tramites as $tramite)
            $resultado[$tramite['id']] = $tramite['nombre'];
        return $resultado;
    }

}