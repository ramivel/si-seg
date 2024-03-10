<?php

namespace App\Controllers;

use App\Models\PerfilesModel;
use App\Models\TipoDocumentoExternoModel;
use App\Models\TramitesModel;

class TipoDocumentoExterno extends BaseController
{
    protected $titulo = 'Administrar Tipos de Documentos Externos';
    protected $controlador = 'tipo_documento_externo/';
    protected $carpeta = 'tipo_documento_externo/';
    protected $menuActual = 'tipo_documento_externo';

    public function index()
    {
        $db = \Config\Database::connect();
        $campos = array('id', 'nombre', 'descripcion', 'dias_intermedio', 'dias_limite',
        "CASE WHEN notificar THEN 'SI' ELSE 'NO' END AS notificar");
        $builder = $db->table('public.tipo_documento_externo')
        ->select($campos)
        ->where('deleted_at IS NULL')
        ->orderBy('nombre');
        $datos = $builder->get()->getResult('array');
        $campos_listar=array('Nombre','Notificar','Días Intermedio','Días Limite');
        $campos_reales=array('nombre','notificar','dias_intermedio','dias_limite');
        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Listado';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['datos'] = $datos;
        $contenido['campos_listar'] = $campos_listar;
        $contenido['campos_reales'] = $campos_reales;
        $contenido['subtitulo'] = 'Listado';
        $contenido['controlador'] = $this->controlador;
        $data['content'] = view($this->carpeta.'index', $contenido);
        $data['menu_actual'] = $this->menuActual;
        $data['tramites_menu'] = $this->tramitesMenu();
        echo view('templates/template', $data);
    }

    public function agregar(){

        if ($this->request->getPost()) {
            $validation = $this->validate([
                'nombre' => [
                    'rules' => 'required',
                ],
            ]);
            if(!$validation){
                $contenido['validation'] = $this->validator;
            }else{
                $tipoDocumentoExternoModel = new TipoDocumentoExternoModel();
                $data = array(
                    'nombre' => mb_strtoupper($this->request->getPost('nombre')),
                    'descripcion' => mb_strtoupper($this->request->getPost('descripcion')),
                    'dias_intermedio' => $this->request->getPost('dias_intermedio') ? $this->request->getPost('dias_intermedio') : NULL,
                    'dias_limite' => $this->request->getPost('dias_limite') ? $this->request->getPost('dias_limite') : NULL,
                    'notificar' => $this->request->getPost('notificar')=='true'?'true':'false',
                );                                
                if($tipoDocumentoExternoModel->save($data) === false)
                    session()->setFlashdata('fail', $tipoDocumentoExternoModel->errors());
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
        $contenido['controlador'] = $this->controlador;
        $data['content'] = view($this->carpeta.'agregar', $contenido);
        $data['menu_actual'] = $this->menuActual;
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['validacion_js'] = 'tipo-documento-externo-validation.js';
        echo view('templates/template', $data);
    }

    public function editar($id){
        $tipoDocumentoExternoModel = new TipoDocumentoExternoModel();        
        if($fila = $tipoDocumentoExternoModel->find($id)){            
            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Editar Registro';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['fila'] = $fila;
            $contenido['id'] = $id;
            $contenido['accion'] = $this->controlador.'guardar_editar';
            $contenido['validation'] = $this->validator;            
            $contenido['controlador'] = $this->controlador;
            $data['content'] = view($this->carpeta.'editar', $contenido);
            $data['menu_actual'] = $this->menuActual;
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['validacion_js'] = 'tipo-documento-externo-validation.js';
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador);
        }
    }
    public function guardarEditar(){
        $id = $this->request->getPost('id');
        if(isset($id) && $id>0){            
            $validation = $this->validate([
                'nombre' => [
                    'rules' => 'required',
                ],                
            ]);
            if(!$validation){                
                $cabera['titulo'] = $this->titulo;
                $cabera['navegador'] = true;
                $cabera['subtitulo'] = 'Editar Registro';
                $contenido['title'] = view('templates/title',$cabera);                
                $contenido['id'] = $id;
                $contenido['accion'] = $this->controlador.'guardar_editar';
                $contenido['validation'] = $this->validator;
                $contenido['controlador'] = $this->controlador;                
                $data['content'] = view($this->carpeta.'editar', $contenido);
                $data['menu_actual'] = $this->menuActual;
                $data['tramites_menu'] = $this->tramitesMenu();
                $data['validacion_js'] = 'tipo-documento-externo-validation.js';
                echo view('templates/template', $data);
            }else{
                $tipoDocumentoExternoModel = new TipoDocumentoExternoModel();
                $data = array(
                    'id' => $id,
                    'nombre' => mb_strtoupper($this->request->getPost('nombre')),
                    'descripcion' => mb_strtoupper($this->request->getPost('descripcion')),
                    'dias_intermedio' => $this->request->getPost('dias_intermedio') ? $this->request->getPost('dias_intermedio') : NULL,
                    'dias_limite' => $this->request->getPost('dias_limite') ? $this->request->getPost('dias_limite') : NULL,
                    'notificar' => $this->request->getPost('notificar')=='true'?'true':'false',
                );
                if($tipoDocumentoExternoModel->save($data) === false)
                    session()->setFlashdata('fail', $tipoDocumentoExternoModel->errors());
                else
                    session()->setFlashdata('success', 'Se Actualizo Correctamente la Información.');
                return redirect()->to($this->controlador);
            }
        }
    }
    public function eliminar($id){
        $tipoDocumentoExternoModel = new TipoDocumentoExternoModel();        
        if($fila = $tipoDocumentoExternoModel->find($id)){            
            $tipoDocumentoExternoModel->delete($fila['id']);
            session()->setFlashdata('success', 'Se elimino correctamente al Registro.');            
        }else{
            session()->setFlashdata('fail', 'El Registro no existe.');
        }
        return redirect()->to($this->controlador);
    }    
}