<?php

namespace App\Controllers;
use App\Models\EstadoTramiteModel;
use App\Models\TipoSolicitudModel;
use App\Models\TramitesModel;

class EstadoTramite extends BaseController
{
    protected $titulo = 'Administrar Estados de Tramites';
    public $controlador = 'estado_tramite/';
    protected $carpeta = 'estado_tramite/';
    protected $menuActual = 'estado_tramite';

    public function index()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('public.tramites as t')
        ->select('t.id, t.nombre, COUNT(et.id) as n')
        ->join('public.estado_tramite as et', 't.id = et.fk_tramite AND et.deleted_at IS NULL', 'left')
        ->where('t.deleted_at IS NULL AND et.fk_estado_padre IS NULL')
        ->groupBy('t.id')
        ->orderBy('t.nombre');
        $datos = $builder->get()->getResult('array');
        $campos_listar=array('Tramite','Estados');
        $campos_reales=array('nombre','n');
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

    public function categoria($id){
        $tramitesModel = new TramitesModel();
        if($tramite = $tramitesModel->find($id)){
            $db = \Config\Database::connect();
            $campos = array('et.id', 'et.orden', 'et.nombre', 'et.dias_intermedio', 'et.dias_limite', 
            "CASE WHEN et.notificar THEN 'SI' ELSE 'NO' END AS notificar", "CASE WHEN et.anexar_documentos THEN 'SI' ELSE 'NO' END AS anexar_documentos",
            "CASE WHEN et.finalizar THEN 'SI' ELSE 'NO' END AS finalizar",'et.descripcion', "COUNT(etp.id) as n");
            $builder = $db->table('public.estado_tramite as et')
            ->select($campos)
            ->join('public.estado_tramite as etp', 'et.id = etp.fk_estado_padre AND etp.deleted_at IS NULL', 'left')
            ->where('et.deleted_at IS NULL AND et.fk_estado_padre IS NULL AND et.fk_tramite = '.$id)
            ->groupBy('et.id')
            ->orderBy('et.orden');
            $datos = $builder->get()->getResult('array');
            $campos_listar=array('Orden','Estado', 'Sub Estado', 'Anexar Documento(s)', 'Finalizar', 'Notificar', 'Días Intermedio', 'Días Limite', );
            $campos_reales=array('orden','nombre', 'n', 'anexar_documentos', 'finalizar', 'notificar', 'dias_intermedio', 'dias_limite', );
            $cabera['titulo'] = $this->titulo . ' - '.$tramite['nombre'];
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Listado de Estados';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['datos'] = $datos;
            $contenido['tramite'] = $tramite;
            $contenido['campos_listar'] = $campos_listar;
            $contenido['campos_reales'] = $campos_reales;
            $contenido['subtitulo'] = 'Listado de Estados';
            $contenido['controlador'] = $this->controlador;
            $data['content'] = view($this->carpeta.'categoria', $contenido);
            $data['menu_actual'] = $this->menuActual;
            $data['tramites_menu'] = $this->tramitesMenu();
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'No existe el tipo de Tramite');
            return redirect()->to($this->controlador);
        }
    }

    public function obtenerTipoSolicitudes(){
        $tiposolicitudModel = new TipoSolicitudModel();
        $tiposolicitudes = $tiposolicitudModel->orderBy('id','ASC')->findAll();
        $temporal = array();
        $temporal[''] = 'SELECCIONE UNA OPCIÓN';
        foreach($tiposolicitudes as $row)
            $temporal[$row['id']] = $row['nombre'];
        return $temporal;
    }

    public function obtenerOrden($tipo_solicitud){
        $estadoTramiteModel = new EstadoTramiteModel();
        $where = array(
            'fk_tramite' => $tipo_solicitud,
        );
        $orden = $estadoTramiteModel->select('orden')->where($where)
        ->orderBy('id', 'DESC')
        ->first();
        if($orden)
            return $orden['orden'] += 1;
        else
            return 1;
    }

    public function obtenerOrdenPadre($fk_estado_padre){
        $estadoTramiteModel = new EstadoTramiteModel();
        $where = array(
            'fk_estado_padre' => $fk_estado_padre,
        );
        $orden = $estadoTramiteModel->select('orden')->where($where)
        ->orderBy('id', 'DESC')
        ->first();
        if($orden)
            return $orden['orden'] += 1;
        else
            return 1;
    }

    public function agregar_categoria($id_tramite){
        $tramitesModel = new TramitesModel();
        if($tramite = $tramitesModel->find($id_tramite)){
            if ($this->request->getPost()) {
                $validation = $this->validate([
                    'nombre' => [
                        'rules' => 'required',
                    ],
                ]);
                if($validation){
                    $estadoTramiteModel = new EstadoTramiteModel();
                    $orden = $this->obtenerOrden($id_tramite);
                    $data = array(
                        'fk_tramite' => $id_tramite,
                        'nombre' => mb_strtoupper($this->request->getPost('nombre')),
                        'dias_intermedio' => $this->request->getPost('dias_intermedio') ? $this->request->getPost('dias_intermedio') : NULL,
                        'dias_limite' => $this->request->getPost('dias_limite') ? $this->request->getPost('dias_limite') : NULL,
                        'descripcion' => mb_strtoupper($this->request->getPost('descripcion')),
                        'notificar' => $this->request->getPost('notificar')=='true'?'true':'false',
                        'anexar_documentos' => $this->request->getPost('anexar_documentos')=='true'?'true':'false',
                        'finalizar' => $this->request->getPost('finalizar')=='true'?'true':'false',
                        'orden' => $orden,
                    );
                    if($estadoTramiteModel->save($data) === false)
                        session()->setFlashdata('fail', $estadoTramiteModel->errors());
                    else
                        session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información.');
                    return redirect()->to($this->controlador.'categoria/'.$id_tramite);
                }
            }
            $cabera['titulo'] = $this->titulo . ' - '.$tramite['nombre'];
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Agregar Nuevo Estado';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['tramite'] = $tramite;
            $contenido['subtitulo'] = 'Agregar Nuevo Estado';
            $contenido['accion'] = $this->controlador.'agregar_categoria/'.$id_tramite;
            $contenido['validation'] = $this->validator;
            $contenido['controlador'] = $this->controlador;
            $data['content'] = view($this->carpeta.'agregar_categoria', $contenido);
            $data['menu_actual'] = $this->menuActual;
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['validacion_js'] = 'estado-tramite-categoria-validation.js';
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'No existe el tipo de Tramite');
            return redirect()->to($this->controlador);
        }
    }

    public function editar_categoria($id){
        $estadoTramiteModel = new EstadoTramiteModel();
        if($fila = $estadoTramiteModel->find($id)){
            $tramitesModel = new TramitesModel();
            if($tramite = $tramitesModel->find($fila['fk_tramite'])){
                $cabera['titulo'] = $this->titulo . ' - '.$tramite['nombre'];
                $cabera['navegador'] = true;
                $cabera['subtitulo'] = 'Editar Registro Estado';
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['tramite'] = $tramite;
                $contenido['fila'] = $fila;
                $contenido['subtitulo'] = 'Editar Registro Estado';
                $contenido['accion'] = $this->controlador.'guardar_editar_categoria';
                $contenido['validation'] = $this->validator;
                $contenido['controlador'] = $this->controlador;
                $data['content'] = view($this->carpeta.'editar_categoria', $contenido);
                $data['menu_actual'] = $this->menuActual;
                $data['tramites_menu'] = $this->tramitesMenu();
                $data['validacion_js'] = 'estado-tramite-categoria-editar-validation.js';
                echo view('templates/template', $data);
            }
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador);
        }
    }
    public function guardar_editar_categoria(){
        $id = $this->request->getPost('id');
        if(isset($id) && $id>0){
            $estadoTramiteModel = new EstadoTramiteModel();
            $tramitesModel = new TramitesModel();
            if($fila = $estadoTramiteModel->find($id)){
                if($tramite = $tramitesModel->find($fila['fk_tramite'])){
                    $validation = $this->validate([
                        'orden' => [
                            'rules' => 'required',
                        ],
                        'nombre' => [
                            'rules' => 'required',
                        ],
                    ]);
                    if($validation){
                        $data = array(
                            'id' => $id,
                            'nombre' => mb_strtoupper($this->request->getPost('nombre')),
                            'dias_intermedio' => $this->request->getPost('dias_intermedio') ? $this->request->getPost('dias_intermedio') : NULL,
                            'dias_limite' => $this->request->getPost('dias_limite') ? $this->request->getPost('dias_limite') : NULL,
                            'descripcion' => mb_strtoupper($this->request->getPost('descripcion')),
                            'notificar' => $this->request->getPost('notificar')=='true'?'true':'false',
                            'anexar_documentos' => $this->request->getPost('anexar_documentos')=='true'?'true':'false',
                            'finalizar' => $this->request->getPost('finalizar')=='true'?'true':'false',
                            'orden' => $this->request->getPost('orden'),
                        );
                        if($estadoTramiteModel->save($data) === false)
                            session()->setFlashdata('fail', $estadoTramiteModel->errors());
                        else
                            session()->setFlashdata('success', 'Se Actualizo Correctamente la Información.');
                        return redirect()->to($this->controlador.'categoria/'.$tramite['id']);
                    }else{
                        $cabera['titulo'] = $this->titulo . ' - '.$tramite['nombre'];
                        $cabera['navegador'] = true;
                        $cabera['subtitulo'] = 'Editar Registro Estado';
                        $contenido['title'] = view('templates/title',$cabera);
                        $contenido['tramite'] = $tramite;
                        $contenido['fila'] = $fila;
                        $contenido['subtitulo'] = 'Editar Registro Estado';
                        $contenido['accion'] = $this->controlador.'guardar_editar_categoria';
                        $contenido['validation'] = $this->validator;
                        $contenido['controlador'] = $this->controlador;
                        $data['content'] = view($this->carpeta.'editar_categoria', $contenido);
                        $data['menu_actual'] = $this->menuActual;
                        $data['tramites_menu'] = $this->tramitesMenu();
                        $data['validacion_js'] = 'estado-tramite-categoria-editar-validation.js';
                        echo view('templates/template', $data);
                    }
                }
            }
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador);
        }
    }

    public function eliminar_categoria($id){
        $estadoTramiteModel = new EstadoTramiteModel();
        $tramitesModel = new TramitesModel();
        if($fila = $estadoTramiteModel->find($id)){
            if($tramite = $tramitesModel->find($fila['fk_tramite'])){
                $estadoTramiteModel->delete($fila['id']);
                session()->setFlashdata('success', 'Se elimino correctamente al Registro.');
            }else{
                session()->setFlashdata('fail', 'El Registro no existe.');
            }
        }else{
            session()->setFlashdata('fail', 'El Registro no existe.');
        }
        return redirect()->to($this->controlador.'categoria/'.$tramite['id']);
    }

    public function subcategoria($id){
        $estadoTramiteModel = new EstadoTramiteModel();
        if($categoria = $estadoTramiteModel->find($id)){
            $db = \Config\Database::connect();
            $campos = array('id', 'orden', 'nombre', 'dias_intermedio', 'dias_limite', "CASE WHEN notificar THEN 'SI' ELSE 'NO' END AS notificar", 
            "CASE WHEN anexar_documentos THEN 'SI' ELSE 'NO' END AS anexar_documentos", "CASE WHEN finalizar THEN 'SI' ELSE 'NO' END AS finalizar",
            'descripcion');
            $builder = $db->table('public.estado_tramite')
            ->select($campos)
            ->where('deleted_at IS NULL AND fk_estado_padre = '.$id)
            ->orderBy('orden');
            $datos = $builder->get()->getResult('array');
            $campos_listar=array('Orden','Estado','Anexar Documento(s)', 'Finalizar', 'Notificar', 'Días Intermedio', 'Días Limite');
            $campos_reales=array('orden','nombre','anexar_documentos', 'finalizar', 'notificar','dias_intermedio', 'dias_limite');
            $cabera['titulo'] = $this->titulo . ' - '.$categoria['nombre'];
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Listado Subestado';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['datos'] = $datos;
            $contenido['categoria'] = $categoria;
            $contenido['campos_listar'] = $campos_listar;
            $contenido['campos_reales'] = $campos_reales;
            $contenido['subtitulo'] = 'Listado Subestado';
            $contenido['controlador'] = $this->controlador;
            $data['content'] = view($this->carpeta.'subcategoria', $contenido);
            $data['menu_actual'] = $this->menuActual;
            $data['tramites_menu'] = $this->tramitesMenu();
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'No existe el tipo de Tramite');
            return redirect()->to($this->controlador);
        }
    }

    public function agregar_subcategoria($id_estado){
        $estadoTramiteModel = new EstadoTramiteModel();
        if($estado = $estadoTramiteModel->find($id_estado)){
            if ($this->request->getPost()) {
                $validation = $this->validate([
                    'nombre' => [
                        'rules' => 'required',
                    ],
                ]);
                if($validation){
                    $estadoTramiteModel = new EstadoTramiteModel();
                    $orden = $this->obtenerOrdenPadre($id_estado);
                    $data = array(
                        'fk_tramite' => $estado['fk_tramite'],
                        'fk_estado_padre' => $estado['id'],
                        'nombre' => mb_strtoupper($this->request->getPost('nombre')),
                        'dias_intermedio' => $this->request->getPost('dias_intermedio') ? $this->request->getPost('dias_intermedio') : NULL,
                        'dias_limite' => $this->request->getPost('dias_limite') ? $this->request->getPost('dias_limite') : NULL,
                        'descripcion' => mb_strtoupper($this->request->getPost('descripcion')),
                        'notificar' => $this->request->getPost('notificar')=='true'?'true':'false',
                        'anexar_documentos' => $this->request->getPost('anexar_documentos')=='true'?'true':'false',
                        'finalizar' => $this->request->getPost('finalizar')=='true'?'true':'false',
                        'orden' => $orden,
                    );
                    if($estadoTramiteModel->save($data) === false){
                        session()->setFlashdata('fail', $estadoTramiteModel->errors());
                    }else{
                        $estado['padre'] = 'true';
                        if($estadoTramiteModel->save($estado) === false)
                            session()->setFlashdata('fail', $estadoTramiteModel->errors());
                        else
                            session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información.');
                    }
                    return redirect()->to($this->controlador.'subcategoria/'.$id_estado);
                }
            }
            $cabera['titulo'] = $this->titulo . ' - '.$estado['nombre'];
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Agregar Nuevo Subestado';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['estado'] = $estado;
            $contenido['subtitulo'] = 'Agregar Nuevo Subestado';
            $contenido['accion'] = $this->controlador.'agregar_subcategoria/'.$id_estado;
            $contenido['validation'] = $this->validator;
            $contenido['controlador'] = $this->controlador;
            $data['content'] = view($this->carpeta.'agregar_subcategoria', $contenido);
            $data['menu_actual'] = $this->menuActual;
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['validacion_js'] = 'estado-tramite-subcategoria-validation.js';
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'No existe el tipo de Tramite');
            return redirect()->to($this->controlador);
        }
    }

    public function editar_subcategoria($id){
        $estadoTramiteModel = new EstadoTramiteModel();
        if($fila = $estadoTramiteModel->find($id)){
            if($estado = $estadoTramiteModel->find($fila['fk_estado_padre'])){
                $cabera['titulo'] = $this->titulo . ' - '.$estado['nombre'];
                $cabera['navegador'] = true;
                $cabera['subtitulo'] = 'Editar Registro Subestado';
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['estado'] = $estado;
                $contenido['fila'] = $fila;
                $contenido['subtitulo'] = 'Editar Registro Subestado';
                $contenido['accion'] = $this->controlador.'guardar_editar_subcategoria';
                $contenido['validation'] = $this->validator;
                $contenido['controlador'] = $this->controlador;
                $data['content'] = view($this->carpeta.'editar_subcategoria', $contenido);
                $data['menu_actual'] = $this->menuActual;
                $data['tramites_menu'] = $this->tramitesMenu();
                $data['validacion_js'] = 'estado-tramite-subcategoria-editar-validation.js';
                echo view('templates/template', $data);
            }
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador);
        }
    }
    public function guardar_editar_subcategoria(){
        $id = $this->request->getPost('id');
        if(isset($id) && $id>0){
            $estadoTramiteModel = new EstadoTramiteModel();
            if($fila = $estadoTramiteModel->find($id)){
                if($estado = $estadoTramiteModel->find($fila['fk_estado_padre'])){
                    $validation = $this->validate([
                        'orden' => [
                            'rules' => 'required',
                        ],
                        'nombre' => [
                            'rules' => 'required',
                        ],
                    ]);
                    if($validation){
                        $data = array(
                            'id' => $id,
                            'nombre' => mb_strtoupper($this->request->getPost('nombre')),
                            'dias_intermedio' => $this->request->getPost('dias_intermedio') ? $this->request->getPost('dias_intermedio') : NULL,
                            'dias_limite' => $this->request->getPost('dias_limite') ? $this->request->getPost('dias_limite') : NULL,
                            'descripcion' => mb_strtoupper($this->request->getPost('descripcion')),
                            'notificar' => $this->request->getPost('notificar')=='true'?'true':'false',
                            'anexar_documentos' => $this->request->getPost('anexar_documentos')=='true'?'true':'false',
                            'finalizar' => $this->request->getPost('finalizar')=='true'?'true':'false',
                            'orden' => $this->request->getPost('orden'),
                        );
                        if($estadoTramiteModel->save($data) === false)
                            session()->setFlashdata('fail', $estadoTramiteModel->errors());
                        else
                            session()->setFlashdata('success', 'Se Actualizo Correctamente la Información.');
                        return redirect()->to($this->controlador.'subcategoria/'.$estado['id']);
                    }else{
                        $cabera['titulo'] = $this->titulo . ' - '.$estado['nombre'];
                        $cabera['navegador'] = true;
                        $cabera['subtitulo'] = 'Editar Registro Subestado';
                        $contenido['title'] = view('templates/title',$cabera);
                        $contenido['estado'] = $estado;
                        $contenido['fila'] = $fila;
                        $contenido['subtitulo'] = 'Editar Registro Subestado';
                        $contenido['accion'] = $this->controlador.'guardar_editar_subcategoria';
                        $contenido['validation'] = $this->validator;
                        $contenido['controlador'] = $this->controlador;
                        $data['content'] = view($this->carpeta.'editar_subcategoria', $contenido);
                        $data['menu_actual'] = $this->menuActual;
                        $data['tramites_menu'] = $this->tramitesMenu();
                        $data['validacion_js'] = 'estado-tramite-subcategoria-editar-validation.js';
                        echo view('templates/template', $data);
                    }
                }
            }
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador);
        }
    }

    public function eliminar_subcategoria($id){
        $estadoTramiteModel = new EstadoTramiteModel();
        if($fila = $estadoTramiteModel->find($id)){
            if($estado = $estadoTramiteModel->find($fila['fk_estado_padre'])){
                $estadoTramiteModel->delete($fila['id']);
                $nestados = $estadoTramiteModel->where('fk_estado_padre', $estado['id'])->findAll();
                if(!$nestados){
                    $estado['padre'] = 'false';
                    if($estadoTramiteModel->save($estado) === false)
                        session()->setFlashdata('fail', $estadoTramiteModel->errors());
                }
                session()->setFlashdata('success', 'Se elimino correctamente al Registro.');
            }else{
                session()->setFlashdata('fail', 'El Registro no existe.');
            }
        }else{
            session()->setFlashdata('fail', 'El Registro no existe.');
        }
        return redirect()->to($this->controlador.'subcategoria/'.$estado['id']);
    }

}