<?php

namespace App\Controllers;

use App\Models\OficinasModel;

class Oficinas extends BaseController
{
    protected $titulo = 'Direcciones AJAM';
    protected $controlador = 'oficinas/';
    protected $carpeta = 'oficinas/';
    protected $menuActual = 'oficinas';    

    public function index()
    {
        $oficinasModel = new OficinasModel();
        $datos = $oficinasModel->orderBy('nombre', 'asc')->findAll();
        $campos_listar=array('Nombre','Departamento','Correlativo','Dirección','Teléfonos','Estado');
        $campos_reales=array('nombre','departamento','correlativo','direccion','telefonos','activo');
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
                'departamento' => [
                    'rules' => 'required',
                ],
                'correlativo' => [
                    'rules' => 'required',
                ],
                'direccion' => [
                    'rules' => 'required',
                ],
                'regional_busqueda' => [
                    'rules' => 'required',
                ],
                'fk_oficina_sincobol' => [
                    'rules' => 'required',
                ],
            ]);
            if(!$validation){
                $contenido['validation'] = $this->validator;
            }else{
                $oficinasModel = new OficinasModel();
                $data = array(
                    'nombre' => mb_strtoupper($this->request->getPost('nombre')),
                    'departamento' => $this->request->getPost('departamento'),
                    'correlativo' => mb_strtoupper($this->request->getPost('correlativo')),
                    'direccion' => mb_strtoupper($this->request->getPost('direccion')),
                    'telefonos' => mb_strtoupper($this->request->getPost('telefonos')),
                    'regional_busqueda' => implode(',',$this->request->getPost('regional_busqueda')),
                    'fk_oficina_sincobol' => implode(',',$this->request->getPost('fk_oficina_sincobol')),
                    'fk_oficina_derivacion' => ((!empty($this->request->getPost('fk_oficina_derivacion'))) ? implode(',',$this->request->getPost('fk_oficina_derivacion')) : ''),
                    'fk_usuario_creador' => session()->get('registroUser'),
                    'desconcentrado'=>($this->request->getPost('desconcentrado') ? 'true' : 'false'),
                    'departamentos_atencion' => ((!empty($this->request->getPost('departamentos_atencion'))) ? implode(',',$this->request->getPost('departamentos_atencion')) : ''),
                );
                if($oficinasModel->save($data) === false)
                    session()->setFlashdata('fail', $oficinasModel->errors());
                else
                    session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información.');
                return redirect()->to($this->controlador);
            }
        }

        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Agregar Nuevo';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['departamentos'] = $this->obtenerDepartamentos();
        $contenido['regionales'] = $this->regionalesBusqueda();
        $contenido['oficinas_sincobol'] = $this->oficinasSincobolBusqueda();
        $contenido['oficinas_derivacion'] = $this->oficinasDerivacion();
        $contenido['accion'] = $this->controlador.'agregar';
        $contenido['controlador'] = $this->controlador;
        $data['content'] = view($this->carpeta.'agregar', $contenido);
        $data['menu_actual'] = $this->menuActual;
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['validacion_js'] = 'oficinas-validation.js';
        echo view('templates/template', $data);

    }

    public function editar($id){
        $oficinasModel = new OficinasModel();
        $fila = $oficinasModel->find($id);
        if($fila){
            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Editar Registro';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['fila'] = $fila;
            $contenido['departamentos'] = $this->obtenerDepartamentos();
            $contenido['regionales'] = $this->regionalesBusqueda();
            $contenido['regionalesBusqueda'] = explode(',',$fila['regional_busqueda']);
            $contenido['oficinas_sincobol'] = $this->oficinasSincobolBusqueda();
            $contenido['oficinas_derivacion'] = $this->oficinasDerivacion($id);
            $contenido['oficinasSincobol'] = explode(',',$fila['fk_oficina_sincobol']);
            $contenido['oficinasDerivacion'] = explode(',',$fila['fk_oficina_derivacion']);
            $contenido['departamentosAtencion'] = explode(',',$fila['departamentos_atencion']);
            $contenido['subtitulo'] = 'Editar Registro';
            $contenido['accion'] = $this->controlador.'guardar_editar';
            $contenido['controlador'] = $this->controlador;
            $data['content'] = view($this->carpeta.'editar', $contenido);
            $data['menu_actual'] = $this->menuActual;
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['validacion_js'] = 'oficinas-validation.js';
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador);
        }
    }
    public function guardar_editar(){
        $id = $this->request->getPost('id');
        if(isset($id) && $id>0){
            $oficinasModel = new OficinasModel();
            $validation = $this->validate([
                'nombre' => [
                    'rules' => 'required',
                ],
                'departamento' => [
                    'rules' => 'required',
                ],
                'correlativo' => [
                    'rules' => 'required',
                ],
                'direccion' => [
                    'rules' => 'required',
                ],
                'regional_busqueda' => [
                    'rules' => 'required',
                ],
                'fk_oficina_sincobol' => [
                    'rules' => 'required',
                ],
            ]);
            if(!$validation){
                $fila = $oficinasModel->find($id);
                $cabera['titulo'] = $this->titulo;
                $cabera['navegador'] = true;
                $cabera['subtitulo'] = 'Editar Registro';
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['fila'] = $fila;
                $contenido['departamentos'] = $this->obtenerDepartamentos();
                $contenido['regionales'] = $this->regionalesBusqueda();
                $contenido['oficinas_sincobol'] = $this->oficinasSincobolBusqueda();
                $contenido['oficinas_derivacion'] = $this->oficinasDerivacion($id);
                $contenido['subtitulo'] = 'Editar Registro';
                $contenido['accion'] = $this->controlador.'guardar_editar';
                $contenido['controlador'] = $this->controlador;
                $data['content'] = view($this->carpeta.'editar', $contenido);
                $data['menu_actual'] = $this->menuActual;
                $data['tramites_menu'] = $this->tramitesMenu();
                $data['validacion_js'] = 'oficinas-validation.js';
                echo view('templates/template', $data);
            }else{
                $data = array(
                    'id' => $id,
                    'nombre' => mb_strtoupper($this->request->getPost('nombre')),
                    'departamento' => $this->request->getPost('departamento'),
                    'correlativo' => mb_strtoupper($this->request->getPost('correlativo')),
                    'direccion' => mb_strtoupper($this->request->getPost('direccion')),
                    'telefonos' => mb_strtoupper($this->request->getPost('telefonos')),
                    'regional_busqueda' => implode(',',$this->request->getPost('regional_busqueda')),
                    'fk_oficina_sincobol' => implode(',',$this->request->getPost('fk_oficina_sincobol')),
                    'fk_oficina_derivacion' => ((!empty($this->request->getPost('fk_oficina_derivacion'))) ? implode(',',$this->request->getPost('fk_oficina_derivacion')) : ''),
                    'fk_usuario_editor' => session()->get('registroUser'),
                    'desconcentrado'=>($this->request->getPost('desconcentrado') ? 'true' : 'false'),
                    'departamentos_atencion' => ((!empty($this->request->getPost('departamentos_atencion'))) ? implode(',',$this->request->getPost('departamentos_atencion')) : ''),
                );                
                if($oficinasModel->save($data) === false)
                    session()->setFlashdata('fail', $oficinasModel->errors());
                else
                    session()->setFlashdata('success', 'Se Actualizo Correctamente la Información.');
                return redirect()->to($this->controlador);
            }
        }
    }
    public function eliminar($id){
        $oficinasModel = new OficinasModel();
        $fila = $oficinasModel->find($id);
        if($fila){
            $oficinasModel->delete($fila['id']);
            session()->setFlashdata('success', 'Se elimino correctamente al Registro.');
        }else{
            session()->setFlashdata('fail', 'El Registro no existe.');
        }
        return redirect()->to($this->controlador);
    }
    public function estado($id){
        $oficinasModel = new OficinasModel();
        $fila = $oficinasModel->find($id);
        if($fila){
            $data = array(
                'id' => $fila['id'],
                'activo' => ($fila['activo']=='t' ? 'false':'true')
            );
            if($oficinasModel->save($data) === false)
                session()->setFlashdata('fail', $oficinasModel->errors());
            else
                session()->setFlashdata('success', 'Se Actualizo Correctamente la Información.');
        }else{
            session()->setFlashdata('fail', 'El Registro no existe.');
        }
        return redirect()->to($this->controlador);
    }

    public function regionalesBusqueda(){
        $db = \Config\Database::connect('sincobol');
        $builder = $db->table('contratos_licencias.area_minera')
        ->select('DISTINCT(regional) as regional')
        ->where('fk_tipo_area_minera = 2')
        ->orderBy('regional','ASC');
        $regionales = $builder->get()->getResultArray();
        $arrayRegionales = array();
        foreach($regionales as $row){
            $arrayRegionales[$row['regional']] = $row['regional'];
        }
        return $arrayRegionales;
    }

    public function oficinasSincobolBusqueda(){
        $db = \Config\Database::connect('sincobol');
        $builder = $db->table('sincobol.oficina')
        ->select('id, nombre')
        ->where('activo = true')
        ->orderBy('nombre','ASC');
        $oficinas = $builder->get()->getResultArray();
        $arrayOficinas = array();
        foreach($oficinas as $row){
            $arrayOficinas[$row['id']] = $row['nombre'];
        }
        return $arrayOficinas;
    }

    public function oficinasDerivacion($idOficina = ''){
        $oficinasModel = new OficinasModel();
        if($idOficina)
            $oficinas = $oficinasModel->where('activo = true AND id !='.$idOficina)->orderBy('nombre','ASC')->findAll();
        else
            $oficinas = $oficinasModel->where('activo = true')->orderBy('nombre','ASC')->findAll();
        $arrayOficinas = array();
        foreach($oficinas as $row){
            $arrayOficinas[$row['id']] = $row['nombre'];
        }
        return $arrayOficinas;
    }

    private function obtenerDepartamentos(){
        $db = \Config\Database::connect();
        $resultado = array();
        $builder = $db->table('mineria_ilegal.municipios')->select('DISTINCT(departamento) AS departamento')->where('activo = true')->orderBY('departamento', 'ASC');
        if($departamentos = $builder->get()->getResultArray()){
            foreach($departamentos as $departamento)
                $resultado[$departamento['departamento']] = $departamento['departamento'];
        }
        return $resultado;
    }

}