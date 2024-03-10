<?php

namespace App\Controllers;

use App\Models\PerfilesModel;

class Perfiles extends BaseController
{
    protected $titulo = 'Cargos AJAM';
    protected $controlador = 'perfiles/';
    protected $carpeta = 'perfiles/';
    protected $menuActual = 'perfiles';    

    public function index()
    {
        $perfilesModel = new PerfilesModel();
        $datos = $perfilesModel->orderBy('nombre','asc')->findAll();
        $campos_listar=array('Nombre','Estado');
        $campos_reales=array('nombre','activo');
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
                $cabera['titulo'] = $this->titulo;
                $cabera['navegador'] = true;
                $cabera['subtitulo'] = 'Agregar Nuevo';
                $contenido['title'] = view('templates/title',$cabera);                
                $contenido['subtitulo'] = 'Agregar Nuevo';
                $contenido['accion'] = $this->controlador.'agregar';
                $contenido['validation'] = $this->validator;
                $data['content'] = view($this->carpeta.'agregar', $contenido);
                $data['menu_actual'] = $this->menuActual;
                $data['tramites_menu'] = $this->tramitesMenu();
                $data['validacion_js'] = 'perfiles-validation.js';
                echo view('templates/template', $data);
            }else{
                $perfilesModel = new PerfilesModel();                
                $data = array(
                    'nombre' => mb_strtoupper($this->request->getPost('nombre')),                  
                );
                if($perfilesModel->save($data) === false)
                    session()->setFlashdata('fail', $perfilesModel->errors());
                else
                    session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información.');
                return redirect()->to($this->controlador);
            }
        }else{
            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Agregar Nuevo';
            $contenido['title'] = view('templates/title',$cabera);            
            $contenido['subtitulo'] = 'Agregar Nuevo';
            $contenido['accion'] = $this->controlador.'agregar';
            $contenido['validation'] = $this->validator;
            $data['content'] = view($this->carpeta.'agregar', $contenido);
            $data['menu_actual'] = $this->menuActual;
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['validacion_js'] = 'perfiles-validation.js';
            echo view('templates/template', $data);
        }
    }

    public function editar($id){
        $perfilesModel = new PerfilesModel();
        $fila = $perfilesModel->find($id);
        if($fila){            
            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Editar Registro';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['fila'] = $fila;                        
            $contenido['subtitulo'] = 'Editar Registro';
            $contenido['accion'] = $this->controlador.'guardar_editar';
            $data['content'] = view($this->carpeta.'editar', $contenido);
            $data['menu_actual'] = $this->menuActual;
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['validacion_js'] = 'perfiles-validation.js';
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador);
        }
    }
    public function guardar_editar(){
        $id = $this->request->getPost('id');
        if(isset($id) && $id>0){
            $perfilesModel = new PerfilesModel();
            $validation = $this->validate([
                'nombre' => [
                    'rules' => 'required',
                ],                
            ]);
            if(!$validation){
                $fila = $perfilesModel->find($id);
                $cabera['titulo'] = $this->titulo;
                $cabera['navegador'] = true;
                $cabera['subtitulo'] = 'Editar Registro';
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['fila'] = $fila;                
                $contenido['subtitulo'] = 'Editar Registro';
                $contenido['accion'] = $this->controlador.'guardar_editar';
                $data['content'] = view($this->carpeta.'editar', $contenido);
                $data['menu_actual'] = $this->menuActual;
                $data['tramites_menu'] = $this->tramitesMenu();
                $data['validacion_js'] = 'perfiles-validation.js';
                echo view('templates/template', $data);
            }else{
                $data = array(
                    'id' => $id,
                    'nombre' => mb_strtoupper($this->request->getPost('nombre')),                    
                );                               
                if($perfilesModel->save($data) === false)
                    session()->setFlashdata('fail', $perfilesModel->errors());
                else
                    session()->setFlashdata('success', 'Se Actualizo Correctamente la Información.');
                return redirect()->to($this->controlador);
            }
        }
    }
    public function eliminar($id){
        $perfilesModel = new PerfilesModel();
        $fila = $perfilesModel->find($id);
        if($fila){            
            $perfilesModel->delete($fila['id']);
            session()->setFlashdata('success', 'Se elimino correctamente al Registro.');            
        }else{
            session()->setFlashdata('fail', 'El Registro no existe.');
        }
        return redirect()->to($this->controlador);
    }
}