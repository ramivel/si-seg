<?php

namespace App\Controllers;
use App\Models\UsuariosModel;
use App\Libraries\Hash;
use App\Models\OficinasModel;
use App\Models\PerfilesModel;
use App\Models\TramitesModel;

class Usuarios extends BaseController
{
    protected $titulo = 'Administración de Usuarios';
    protected $controlador = 'usuarios/';
    protected $carpeta = 'usuarios/';
    protected $menuActual = 'usuarios';
    protected $permisos = array(
        100 => 'Administrador de Sistemas',
        1 => 'Migrar SINCOBOL',
        2 => 'Anular Documentos',
        //3 => 'Fecha Notificación',
        4 => 'Asignación Responsable',
        5 => 'Finalizar Trámite',
        6 => 'Reporte por Usuario',
        7 => 'Reporte Documentos',
        8 => 'Reporte General',
        9 => 'Área(s) Protegida(s) Adicional(es)',
        10 => 'Encargado de Cargar Documentación Digital',
        11 => 'Correspondencia Externa',
        12 => 'Responsable Minería Ilegal DFCCI',
        13 => 'Responsable Minería Ilegal Dirección Departamental o Regional',
    );

    public function index()
    {
        $db = \Config\Database::connect();
        $campos = array('u.id', 'u.nombre_completo', 'p.nombre as cargo', 'u.usuario', 'u.activo', 'o.nombre as oficina', 'u.tramites');
        $query = $db->table('usuarios as u')
        ->select($campos)
        ->join('oficinas as o', 'u.fk_oficina = o.id', 'left')
        ->join('perfiles as p', 'u.fk_perfil = p.id', 'left')
        ->where('u.deleted_at IS NULL')
        ->orderBy('u.nombre_completo', 'asc');
        $datos = $query->get()->getResultArray();
        $campos_listar=array('Nombre Completo','Cargo','Oficina','Usuario','Tramites','Estado');
        $campos_reales=array('nombre_completo','cargo','oficina','usuario','tramites','activo');
        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Listado';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['datos'] = $datos;
        $contenido['campos_listar'] = $campos_listar;
        $contenido['campos_reales'] = $campos_reales;
        $contenido['oficinas'] = $this->obtenerOficinas();
        $contenido['tramites'] = $this->obtenerTramites();
        $contenido['subtitulo'] = 'Listado';
        $contenido['controlador'] = $this->controlador;
        $data['content'] = view($this->carpeta.'index', $contenido);
        $data['menu_actual'] = $this->menuActual;
        $data['tramites_menu'] = $this->tramitesMenu();
        echo view('templates/template', $data);
    }

    public function obtenerOficinas(){
        $oficinasModel = new OficinasModel();
        $oficinas = $oficinasModel->where('activo', true)->orderBy('nombre','ASC')->findAll();
        $temporal = array();
        $temporal[''] = 'SELECCIONE UNA OPCIÓN';
        foreach($oficinas as $row)
            $temporal[$row['id']] = $row['nombre'];
        return $temporal;
    }

    public function obtenerPerfiles(){
        $perfilesModel = new PerfilesModel();
        $perfiles = $perfilesModel->where('activo', true)->orderBy('nombre','ASC')->findAll();
        $temporal = array();
        $temporal[''] = 'SELECCIONE UNA OPCIÓN';
        foreach($perfiles as $row)
            $temporal[$row['id']] = $row['nombre'];
        return $temporal;
    }

    public function obtenerTramites(){
        $tramitesModel = new TramitesModel();
        //$tramites = $tramitesModel->where('activo', true)->orderBy('nombre','ASC')->findAll();
        $tramites = $tramitesModel->orderBy('nombre','ASC')->findAll();
        $temporal = array();
        foreach($tramites as $row)
            $temporal[$row['id']] = $row['nombre'];
        return $temporal;
    }

    public function agregar(){
        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Agregar Nuevo Usuario';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['oficinas'] = $this->obtenerOficinas();
        $contenido['perfiles'] = $this->obtenerPerfiles();
        $contenido['tramites'] = $this->obtenerTramites();
        $contenido['permisos'] = $this->permisos;
        $contenido['accion'] = $this->controlador.'guardar';
        $contenido['controlador'] = $this->controlador;
        $data['content'] = view($this->carpeta.'agregar', $contenido);
        $data['menu_actual'] = $this->menuActual;
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['validacion_js'] = 'usuario-agregar-validation.js';
        echo view('templates/template', $data);
    }

    public function guardar(){
        $validation = $this->validate([
            'nombre_completo' => [
                'rules' => 'required',
            ],
            'email' => [
                'rules' => 'required',
            ],
            'fk_oficina' => [
                'rules' => 'required',
            ],
            'fk_perfil' => [
                'rules' => 'required',
            ],
            'usuario' => [
                'rules' => 'required|is_unique_with_schemas[public.usuarios.usuario]',
                'errors' => [
                    'is_unique_with_schemas' => 'El usuario ya esta registrado ingrese otro.'
                ]
            ],
            'pass' => [
                'rules' => 'required|min_length[5]',
            ],
            /*'tramites' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Debe seleccionar al menos un Tramite'
                ]
            ],*/
        ]);
        if(!$validation){
            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Agregar Nuevo Usuario';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['oficinas'] = $this->obtenerOficinas();
            $contenido['perfiles'] = $this->obtenerPerfiles();
            $contenido['tramites'] = $this->obtenerTramites();
            $contenido['accion'] = $this->controlador.'guardar';
            $contenido['validation'] = $this->validator;
            $contenido['controlador'] = $this->controlador;
            $data['content'] = view($this->carpeta.'agregar', $contenido);
            $data['menu_actual'] = $this->menuActual;
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['validacion_js'] = 'usuario-agregar-validation.js';
            echo view('templates/template', $data);
        }else{
            $usuariosModel = new UsuariosModel();
            $data = array(
                'nombre_completo' => mb_strtoupper($this->request->getPost('nombre_completo')),
                'email' => $this->request->getPost('email'),
                'atencion' => mb_strtoupper($this->request->getPost('atencion')),
                'fk_oficina' => $this->request->getPost('fk_oficina'),
                'fk_perfil' => $this->request->getPost('fk_perfil'),
                'usuario' => $this->request->getPost('usuario'),
                'pass' => Hash::make($this->request->getPost('pass')),
                'tramites' => $this->request->getPost('tramites') ? implode(',',$this->request->getPost('tramites')) : '',
                'permisos' => $this->request->getPost('permisos') ? implode(',',$this->request->getPost('permisos')) : '',
            );
            if($usuariosModel->save($data) === false)
                session()->setFlashdata('fail', $usuariosModel->errors());
            else
                session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información.');
            return redirect()->to('usuarios');
        }
    }
    public function editar($id){
        $usuariosModel = new UsuariosModel();
        $fila = $usuariosModel->find($id);
        if($fila){
            $cabera['titulo'] = 'Administracion de Usuarios';
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Editar Usuario';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['oficinas'] = $this->obtenerOficinas();
            $contenido['perfiles'] = $this->obtenerPerfiles();
            $contenido['tramites'] = $this->obtenerTramites();
            $contenido['permisos'] = $this->permisos;
            $contenido['tramites_elegidos'] = explode(',',$fila['tramites']);
            $contenido['permisos_elegidos'] = explode(',',$fila['permisos']);
            $contenido['fila'] = $fila;
            $contenido['accion'] = $this->controlador.'guardar_editar';
            $data['content'] = view($this->carpeta.'editar', $contenido);
            $data['menu_actual'] = $this->menuActual;
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['validacion_js'] = 'usuario-editar-validation.js';
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El usuario no existe.');
            return redirect()->to('usuarios');
        }
    }
    public function guardar_editar(){
        $id = $this->request->getPost('id');
        if(isset($id) && $id>0){
            $usuariosModel = new UsuariosModel();
            $validation = $this->validate([
                'nombre_completo' => [
                    'rules' => 'required',
                ],
                'email' => [
                    'rules' => 'required',
                ],
                'fk_oficina' => [
                    'rules' => 'required',
                ],
                'fk_perfil' => [
                    'rules' => 'required',
                ],
                /*'tramites' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar al menos un Tramite'
                    ]
                ],*/
            ]);
            if(!$validation){
                $fila = $usuariosModel->find($id);
                $cabera['titulo'] = 'Administracion de Usuarios';
                $cabera['navegador'] = true;
                $cabera['subtitulo'] = 'Editar Usuario';
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['oficinas'] = $this->obtenerOficinas();
                $contenido['perfiles'] = $this->obtenerPerfiles();
                $contenido['tramites'] = $this->obtenerTramites();
                $contenido['fila'] = $fila;
                $contenido['accion'] = $this->controlador.'guardar_editar';
                $contenido['validation'] = $this->validator;
                $data['content'] = view($this->carpeta.'editar', $contenido);
                $data['menu_actual'] = $this->menuActual;
                $data['tramites_menu'] = $this->tramitesMenu();
                $data['validacion_js'] = 'usuario-editar-validation.js';
                echo view('templates/template', $data);
            }else{
                $data = array(
                    'id' => $id,
                    'nombre_completo' => mb_strtoupper($this->request->getPost('nombre_completo')),
                    'email' => $this->request->getPost('email'),
                    'atencion' => mb_strtoupper($this->request->getPost('atencion')),
                    'fk_oficina' => $this->request->getPost('fk_oficina'),
                    'fk_perfil' => $this->request->getPost('fk_perfil'),
                    'tramites' => $this->request->getPost('tramites') ? implode(',',$this->request->getPost('tramites')) : '',
                    'permisos' => $this->request->getPost('permisos') ? implode(',',$this->request->getPost('permisos')) : '',
                );
                if($usuariosModel->save($data) === false)
                    session()->setFlashdata('fail', $usuariosModel->errors());
                else
                    session()->setFlashdata('success', 'Se Actualizo Correctamente la Información.');
                return redirect()->to('usuarios');
            }
        }
    }
    public function cambiarContraseña($id){
        $usuariosModel = new UsuariosModel();
        $fila = $usuariosModel->find($id);
        if($fila){
            $cabera['titulo'] = 'Administracion de Usuarios';
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Cambiar la Contraseña Usuario';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['oficinas'] = $this->obtenerOficinas();
            $contenido['perfiles'] = $this->obtenerPerfiles();
            $contenido['fila'] = $fila;
            $contenido['accion'] = $this->controlador.'guardar_cambiar_contraseña';
            $data['content'] = view($this->carpeta.'cambiar_contraseña', $contenido);
            $data['menu_actual'] = $this->menuActual;
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['validacion_js'] = 'usuario-contrasena-validation.js';
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El usuario no existe.');
            return redirect()->to('usuarios');
        }
    }
    public function guardarCambiarContraseña(){
        $id = $this->request->getPost('id');
        if(isset($id) && $id>0){
            $usuariosModel = new UsuariosModel();
            $validation = $this->validate([
                'id' => [
                    'rules' => 'required',
                ],
                'nueva_contrasena' => [
                    'rules' => 'required|min_length[5]',
                ],
            ]);
            if(!$validation){
                $cabera['titulo'] = 'Administracion de Usuarios';
                $cabera['navegador'] = true;
                $cabera['subtitulo'] = 'Cambiar la Contraseña Usuario';
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['oficinas'] = $this->obtenerOficinas();
                $contenido['perfiles'] = $this->obtenerPerfiles();                                
                $contenido['accion'] = $this->controlador.'guardar_cambiar_contraseña';
                $contenido['validation'] = $this->validator;
                $data['content'] = view($this->carpeta.'cambiar_contraseña', $contenido);
                $data['menu_actual'] = $this->menuActual;
                $data['tramites_menu'] = $this->tramitesMenu();
                $data['validacion_js'] = 'usuario-contrasena-validation.js';
                echo view('templates/template', $data);
            }else{                
                $data = array(
                    'id' => $id,
                    'pass' => Hash::make($this->request->getPost('nueva_contrasena')),                    
                );
                if($usuariosModel->save($data) === false)
                    session()->setFlashdata('fail', $usuariosModel->errors());
                else
                    session()->setFlashdata('success', 'Se Actualizo la Contraseña Correctamente.');
                return redirect()->to('usuarios');
            }
        }
    }
    public function eliminar($id){
        $usuariosModel = new UsuariosModel();
        $fila = $usuariosModel->find($id);
        if($fila){
            $usuariosModel->delete($fila['id']);
            session()->setFlashdata('success', 'Se elimino correctamente al Usuario.');
        }else{
            session()->setFlashdata('fail', 'El usuario no existe.');
        }
        return redirect()->to('usuarios');
    }
}