<?php

namespace App\Controllers;

use App\Models\TramitesModel;

class Tramites extends BaseController
{
    protected $titulo = 'Tramites AJAM';
    protected $controlador = 'tramites/';
    protected $carpeta = 'tramites/';
    protected $menuActual = 'tramites';

    public function index()
    {
        $tramitesModel = new TramitesModel();
        $datos = $tramitesModel->findAll();
        $campos_listar=array('Nombre','Menu','Controlador','Correlativo','Estado');
        $campos_reales=array('nombre','menu','controlador','correlativo','activo');
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

    private function tipo_hoja_ruta(){
        $dbSincobol = \Config\Database::connect('sincobol');
        $campos = array('id','nombre','sigla');
        $where = array(
            'tipo_ingreso' => 'solicitud',
        );
        $builder = $dbSincobol->table('sincobol.tipo_hoja_ruta')
        ->select($campos)
        ->where($where);
        return $builder->get()->getResultArray();
    }

    public function agregar(){
        $tipos_hr = array(
            '' => 'NUEVA HOJA DE RUTA',
        );
        foreach($this->tipo_hoja_ruta() as $row)
            $tipos_hr[$row['id']] = $row['nombre'];
        if ($this->request->getPost()) {
            $validation = $this->validate([
                'nombre' => [
                    'rules' => 'required',
                ],
                'controlador' => [
                    'rules' => 'required',
                ],
                'menu' => [
                    'rules' => 'required',
                ],
                'correlativo' => [
                    'rules' => 'required',
                ],                
            ]);
            if(!$validation){
                $contenido['validation'] = $this->validator;
            }else{
                $tramitesModel = new TramitesModel();
                $data = array(
                    'nombre' => $this->request->getPost('nombre'),
                    'menu' => $this->request->getPost('menu'),
                    'controlador' => $this->request->getPost('controlador'),
                    'correlativo' => mb_strtoupper($this->request->getPost('correlativo')),                    
                    'fk_tipo_hoja_ruta' => ((!empty($this->request->getPost('fk_tipo_hoja_ruta'))) ? $this->request->getPost('fk_tipo_hoja_ruta') : NULL),
                    'fk_usuario_creador' => session()->get('registroUser'),
                );
                if($tramitesModel->save($data) === false)
                    session()->setFlashdata('fail', $tramitesModel->errors());
                else
                    session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información.');
                return redirect()->to($this->controlador);
            }
        }
        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Agregar Nuevo';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['subtitulo'] = 'Agregar Nuevo';
        $contenido['accion'] = $this->controlador.'agregar';
        $contenido['validation'] = $this->validator;
        $contenido['tipos_hr'] = $tipos_hr;
        $contenido['controlador'] = $this->controlador;
        $data['content'] = view($this->carpeta.'agregar', $contenido);
        $data['menu_actual'] = $this->menuActual;
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['validacion_js'] = 'tramites-validation.js';
        echo view('templates/template', $data);        
    }

    public function editar($id){
        $tipos_hr = array();
        foreach($this->tipo_hoja_ruta() as $row)
            $tipos_hr[$row['id']] = $row['nombre'];
        $tramitesModel = new TramitesModel();
        $fila = $tramitesModel->find($id);
        if($fila){
            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Editar Registro';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['fila'] = $fila;
            $contenido['tipos_hr'] = $tipos_hr;
            $contenido['subtitulo'] = 'Editar Registro';
            $contenido['accion'] = $this->controlador.'guardar_editar';
            $contenido['controlador'] = $this->controlador;
            $data['content'] = view($this->carpeta.'editar', $contenido);
            $data['menu_actual'] = $this->menuActual;
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['validacion_js'] = 'tramites-validation.js';
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador);
        }
    }
    public function guardar_editar(){
        $id = $this->request->getPost('id');
        if(isset($id) && $id>0){
            $tipos_hr = array();
            foreach($this->tipo_hoja_ruta() as $row)
                $tipos_hr[$row['id']] = $row['nombre'];
            $tramitesModel = new TramitesModel();
            $validation = $this->validate([
                'nombre' => [
                    'rules' => 'required',
                ],
                'controlador' => [
                    'rules' => 'required',
                ],
                'menu' => [
                    'rules' => 'required',
                ],
                'correlativo' => [
                    'rules' => 'required',
                ],
                'fk_tipo_hoja_ruta' => [
                    'rules' => 'required|is_natural',
                ],
            ]);
            if(!$validation){
                $fila = $tramitesModel->find($id);
                $cabera['titulo'] = $this->titulo;
                $cabera['navegador'] = true;
                $cabera['subtitulo'] = 'Editar Registro';
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['fila'] = $fila;
                $contenido['tipos_hr'] = $tipos_hr;
                $contenido['subtitulo'] = 'Editar Registro';
                $contenido['accion'] = $this->controlador.'guardar_editar';
                $contenido['controlador'] = $this->controlador;
                $data['content'] = view($this->carpeta.'editar', $contenido);
                $data['menu_actual'] = $this->menuActual;
                $data['tramites_menu'] = $this->tramitesMenu();
                $data['validacion_js'] = 'tramites-validation.js';
                echo view('templates/template', $data);
            }else{
                $data = array(
                    'id' => $id,
                    'nombre' => $this->request->getPost('nombre'),
                    'menu' => $this->request->getPost('menu'),
                    'controlador' => $this->request->getPost('controlador'),
                    'correlativo' => mb_strtoupper($this->request->getPost('correlativo')),
                    'fk_tipo_hoja_ruta' => $this->request->getPost('fk_tipo_hoja_ruta'),
                    'fk_usuario_editor' => session()->get('registroUser'),
                );
                if($tramitesModel->save($data) === false)
                    session()->setFlashdata('fail', $tramitesModel->errors());
                else
                    session()->setFlashdata('success', 'Se Actualizo Correctamente la Información.');
                return redirect()->to($this->controlador);
            }
        }
    }
    public function eliminar($id){
        $tramitesModel = new TramitesModel();
        $fila = $tramitesModel->find($id);
        if($fila){
            $tramitesModel->delete($fila['id']);
            session()->setFlashdata('success', 'Se elimino correctamente al Registro.');
        }else{
            session()->setFlashdata('fail', 'El Registro no existe.');
        }
        return redirect()->to($this->controlador);
    }
    public function estado($id){
        $tramitesModel = new TramitesModel();
        $fila = $tramitesModel->find($id);
        if($fila){            
            $data = array(
                'id' => $fila['id'],
                'activo' => ($fila['activo']=='t' ? 'false':'true')
            );
            if($tramitesModel->save($data) === false)
                session()->setFlashdata('fail', $tramitesModel->errors());
            else
                session()->setFlashdata('success', 'Se Actualizo Correctamente la Información.');
        }else{
            session()->setFlashdata('fail', 'El Registro no existe.');
        }
        return redirect()->to($this->controlador);
    }
}