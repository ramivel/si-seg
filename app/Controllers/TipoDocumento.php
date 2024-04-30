<?php

namespace App\Controllers;

use App\Models\PerfilesModel;
use App\Models\TipoDocumentoModel;
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
}