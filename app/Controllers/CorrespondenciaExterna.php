<?php

namespace App\Controllers;

use App\Models\ActoAdministrativoModel;
use App\Models\CorrespondenciaExternaModel;
use App\Models\PerfilesModel;
use App\Models\PersonaExternaModel;
use App\Models\TipoDocumentoModel;
use App\Models\TramitesModel;

class CorrespondenciaExterna extends BaseController
{
    protected $titulo = 'Correspondencia Externa';
    protected $controlador = 'correspondencia_externa/';
    protected $carpeta = 'correspondencia_externa/';
    protected $menuActual = 'correspondencia_externa/';
    protected $rutaArchivos = 'archivos/';
    protected $expedidos = array(
        '' => 'SELECCIONE UNA OPCIÓN',
        'QR' => 'NUEVA CÉDULA CON CÓDIGO QR',
        'LP' => 'LA PAZ',
        'OR' => 'ORURO',
        'PT' => 'POTOSÍ',
        'CB' => 'COCHABAMBA',
        'CH' => 'CHUQUISACA',
        'TJ' => 'TARIJA',
        'SC' => 'SANTA CRUZ',
        'BE' => 'BENI',
        'PD' => 'PANDO',
    );

    public function misIngresos()
    {
        $db = \Config\Database::connect();
        $campos = array('ce.id', 'ce.editar', 't.controlador', 'ac.fk_area_minera', 'ce.estado', 't.nombre as tipo_tramite', 'ac.correlativo', 'ce.cite', "to_char(ce.fecha_cite, 'DD/MM/YYYY') as fecha_cite",
        "CONCAT(pe.nombres, ' ', pe.apellidos, ' (', pe.institucion, ' - ',pe.cargo,')') as remitente", 'ce.referencia', 'ce.doc_digital',
        "CONCAT('CITE: ',ce.cite,'<br>Fecha: ',to_char(ce.fecha_cite, 'DD/MM/YYYY'),'<br>Remitente: ',CONCAT(pe.nombres, ' ', pe.apellidos, ' (', pe.institucion, ' - ',pe.cargo,')'),'<br>Referencia: ',ce.referencia) as documento_externo",
        'ce.editar', "to_char(ce.created_at, 'DD/MM/YYYY HH24:MI') as fecha_ingreso",
        "to_char(ce.fecha_recepcion, 'DD/MM/YYYY HH24:MI') as fecha_recepcion", "CONCAT(ur.nombre_completo,'<br><b>',pr.nombre,'<b>') as recepcion",
        );
        $where = array(
            'ce.deleted_at' => NULL,
            'ce.fk_usuario_creador' => session()->get('registroUser')
        );
        $builder = $db->table('public.correspondencia_externa AS ce')
        ->join('public.tramites AS t', 'ce.fk_tramite = t.id', 'left')
        ->join('public.acto_administrativo AS ac', 'ce.fk_acto_administrativo = ac.id', 'left')
        ->join('public.persona_externa AS pe', 'ce.fk_persona_externa = pe.id', 'left')
        ->join('public.usuarios AS ur', 'ce.fk_usuario_recepcion = ur.id', 'left')
        ->join('public.perfiles AS pr', 'ur.fk_perfil = pr.id', 'left')
        ->select($campos)
        ->where($where)
        ->orderBy('ce.id', 'DESC');
        $datos = $builder->get()->getResultArray();
        $campos_listar=array('Estado','Fecha Ingreso', 'Fecha Recepción', 'Recepcionado Por','Tipo Trámite','Correlativo', 'Documento Externo', 'Doc. Digital', );
        $campos_reales=array('estado','fecha_ingreso','fecha_recepcion','recepcion','tipo_tramite','correlativo', 'documento_externo', 'doc_digital', );
        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Mis Ingresos';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['datos'] = $datos;
        $contenido['campos_listar'] = $campos_listar;
        $contenido['campos_reales'] = $campos_reales;
        $contenido['subtitulo'] = 'Mis Ingresos';
        $contenido['controlador'] = $this->controlador;
        $contenido['ruta_archivos'] = $this->rutaArchivos;
        $data['content'] = view($this->carpeta.'index', $contenido);
        $data['menu_actual'] = $this->menuActual.'mis_ingresos';
        $data['tramites_menu'] = $this->tramitesMenu();
        echo view('templates/template', $data);
    }

    public function agregar(){

        if ($this->request->getPost()) {
            $actoAdministrativoModel = new ActoAdministrativoModel();
            $correspondenciaExternaModel = new CorrespondenciaExternaModel();
            $validation = $this->validate([
                'fk_tramite' => [
                    'rules' => 'required',
                ],
                'fk_acto_administrativo' => [
                    'rules' => 'required',
                ],
                'cite' => [
                    'rules' => 'required',
                ],
                'fecha_cite' => [
                    'rules' => 'required',
                ],
                'fk_persona_externa' => [
                    'rules' => 'required',
                ],
                'referencia' => [
                    'rules' => 'required',
                ],
                'fojas' => [
                    'rules' => 'required',
                ],
                'adjuntos' => [
                    'rules' => 'required',
                ],
                'doc_digital' => [
                    'uploaded[doc_digital]',
                    'mime_in[doc_digital,application/pdf]',
                    'max_size[doc_digital,20480]',
                ],
            ]);
            if(!$validation){
                $personaExternaModel = new PersonaExternaModel();
                $contenido['fk_tramite'] = $this->request->getPost('fk_tramite');
                $campos = array('id', "CONCAT(correlativo,' (',codigo_unico,' - ',denominacion,')') as hr");
                $contenido['hr_madre'] = $actoAdministrativoModel->select($campos)->find($this->request->getPost('fk_acto_administrativo'));
                $campos = array('id', "CONCAT(documento_identidad, ' - ', nombre_completo, ' (', institucion, ' - ',cargo,')') as nombre");
                $contenido['persona_externa'] = $personaExternaModel->select($campos)->find($this->request->getPost('fk_persona_externa'));
                $contenido['validation'] = $this->validator;
            }else{
                $acto_administrativo = $actoAdministrativoModel->find($this->request->getPost('fk_acto_administrativo'));
                $docDigital = $this->request->getFile('doc_digital');
                $path = 'archivos/cam/'.$acto_administrativo['fk_area_minera'].'/';
                if(!file_exists($path))
                    mkdir($path,0777);
                $path = 'archivos/cam/'.$acto_administrativo['fk_area_minera'].'/externo/';
                if(!file_exists($path))
                    mkdir($path,0777);
                $nombreAdjunto = $docDigital->getRandomName();
                $docDigital->move($path,$nombreAdjunto);
                $data = array(
                    'fk_tramite' => $this->request->getPost('fk_tramite'),
                    'fk_acto_administrativo' => $this->request->getPost('fk_acto_administrativo'),
                    'fk_persona_externa' => $this->request->getPost('fk_persona_externa'),
                    'cite' => mb_strtoupper($this->request->getPost('cite')),
                    'fecha_cite' => $this->request->getPost('fecha_cite'),
                    'referencia' => mb_strtoupper($this->request->getPost('referencia')),
                    'fojas' => $this->request->getPost('fojas'),
                    'adjuntos' => mb_strtoupper($this->request->getPost('adjuntos')),
                    'doc_digital' => $nombreAdjunto,
                    'estado' => 'INGRESADO',
                    'fk_usuario_creador' => session()->get('registroUser'),
                );
                if($correspondenciaExternaModel->save($data) === false)
                    session()->setFlashdata('fail', $correspondenciaExternaModel->errors());
                else
                    session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información.');
                return redirect()->to($this->controlador.'mis_ingresos');
            }
        }

        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Agregar Nuevo';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['accion'] = $this->controlador.'agregar';
        $contenido['validation'] = $this->validator;
        $contenido['controlador'] = $this->controlador;
        $contenido['tramites'] = $this->obtenerTramites();
        $contenido['modal_remitente'] = view($this->carpeta.'nuevo_remitente', array('expedidos'=>$this->expedidos));
        $data['content'] = view($this->carpeta.'agregar', $contenido);
        $data['menu_actual'] = $this->menuActual.'agregar';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['validacion_js'] = 'correspondencia-externa-validation.js';
        echo view('templates/template', $data);
    }

    private function obtenerTramites(){
        $tramitesModel = new TramitesModel();
        return $tramitesModel->where('activo = true')->orderBy('nombre', 'ASC')->findAll();
    }

    public function editar($id){
        $correspondenciaExternaModel = new CorrespondenciaExternaModel();
        if($fila = $correspondenciaExternaModel->find($id)){
            $db = \Config\Database::connect();
            $personaExternaModel = new PersonaExternaModel();
            $tramitesModel = new TramitesModel();
            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Editar Registro';
            $contenido['tramite'] = $tramitesModel->find($fila['fk_tramite']);

            $campos = array('id', "CONCAT(correlativo,' (',codigo_unico,' - ',denominacion,')') as hr");
            $where = array(
                'ac.deleted_at' => NULL,
                'ac.id' => $fila['fk_acto_administrativo'],
            );
            $builder = $db->table('public.acto_administrativo as ac')
            ->select($campos)
            ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
            ->where($where);
            $contenido['hr_madre'] = $builder->get()->getRowArray();

            $campos = array('fk_area_minera','codigo_unico', 'denominacion', 'representante_legal', 'nacionalidad', 'titular','clasificacion_titular as clasificacion');
            $builder = $db->table('public.acto_administrativo as ac')
            ->select($campos)
            ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
            ->where($where);
            $contenido['acto_administrativo'] = $builder->get()->getRowArray();
            $campos = array('id', "CONCAT(documento_identidad, ' ', expedido, ' - ', nombres, ' ', apellidos, ' (', institucion, ' - ',cargo,')') as nombre");
            $contenido['persona_externa'] = $personaExternaModel->select($campos)->find($fila['fk_persona_externa']);
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['fila'] = $fila;
            $contenido['accion'] = $this->controlador.'guardar_editar';
            $contenido['validation'] = $this->validator;
            $contenido['controlador'] = $this->controlador;
            $contenido['tramites'] = $this->obtenerTramites();
            $contenido['modal_remitente'] = view($this->carpeta.'nuevo_remitente', array('expedidos'=>$this->expedidos));
            $contenido['ruta_archivos'] = $this->rutaArchivos;
            $data['content'] = view($this->carpeta.'editar', $contenido);
            $data['menu_actual'] = $this->menuActual.'mis_ingresos';
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['validacion_js'] = 'correspondencia-externa-editar-validation.js';
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador);
        }
    }

    public function guardarEditar(){

        if ($this->request->getPost()) {
            $id = $this->request->getPost('id');
            $correspondenciaExternaModel = new CorrespondenciaExternaModel();
            $actoAdministrativoModel = new ActoAdministrativoModel();
            $personaExternaModel = new PersonaExternaModel();
            $tramitesModel = new TramitesModel();
            $fila = $correspondenciaExternaModel->find($id);
            $tramite = $tramitesModel->find($fila['fk_tramite']);
            $acto_administrativo = $actoAdministrativoModel->find($fila['fk_acto_administrativo']);
            $validation = $this->validate([
                'id' => [
                    'rules' => 'required',
                ],
                'cite' => [
                    'rules' => 'required',
                ],
                'fecha_cite' => [
                    'rules' => 'required',
                ],
                'fk_persona_externa' => [
                    'rules' => 'required',
                ],
                'referencia' => [
                    'rules' => 'required',
                ],
                'fojas' => [
                    'rules' => 'required',
                ],
                'adjuntos' => [
                    'rules' => 'required',
                ],
                'doc_digital' => [
                    'mime_in[doc_digital,application/pdf]',
                    'max_size[doc_digital,20480]',
                ],
            ]);
            if(!$validation){
                $cabera['titulo'] = $this->titulo;
                $cabera['navegador'] = true;
                $cabera['subtitulo'] = 'Editar Registro';
                $contenido['tramite'] = $tramite;
                $campos = array('id', "CONCAT(documento_identidad, ' - ', nombre_completo, ' (', institucion, ' - ',cargo,')') as nombre");
                $contenido['persona_externa'] = $personaExternaModel->select($campos)->find($fila['fk_persona_externa']);
                $contenido['acto_administrativo'] = $acto_administrativo;
                $contenido['fila'] = $fila;
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['accion'] = $this->controlador.'guardar_editar';
                $contenido['validation'] = $this->validator;
                $contenido['controlador'] = $this->controlador;
                $contenido['tramites'] = $this->obtenerTramites();
                $contenido['ruta_archivos'] = $this->rutaArchivos;
                $data['content'] = view($this->carpeta.'editar', $contenido);
                $data['menu_actual'] = $this->menuActual.'mis_ingresos';
                $data['tramites_menu'] = $this->tramitesMenu();
                $data['validacion_js'] = 'correspondencia-externa-editar-validation.js';
                echo view('templates/template', $data);
            }else{
                $docDigital = $this->request->getFile('doc_digital');
                $data = array(
                    'id' => $id,
                    'fk_persona_externa' => $this->request->getPost('fk_persona_externa'),
                    'cite' => mb_strtoupper($this->request->getPost('cite')),
                    'fecha_cite' => $this->request->getPost('fecha_cite'),
                    'referencia' => mb_strtoupper($this->request->getPost('referencia')),
                    'fojas' => $this->request->getPost('fojas'),
                    'adjuntos' => mb_strtoupper($this->request->getPost('adjuntos')),
                    'editar' => 'FALSE',
                );
                if(!empty($docDigital) && $docDigital->getSize()>0){
                    $path = $this->rutaArchivos.$tramite['controlador'].$acto_administrativo['fk_area_minera'].'/externo/';
                    if(file_exists($path.$fila['doc_digital']))
                        @unlink($path.$fila['doc_digital']);

                    $nombreAdjunto = $docDigital->getRandomName();
                    $docDigital->move($path,$nombreAdjunto);
                    $data['doc_digital'] = $nombreAdjunto;
                }
                if($correspondenciaExternaModel->save($data) === false)
                    session()->setFlashdata('fail', $correspondenciaExternaModel->errors());
                else
                    session()->setFlashdata('success', 'Se Actualizo Correctamente la Información.');
                return redirect()->to($this->controlador.'mis_ingresos');
            }
        }
    }

    public function recibirAjax(){
        $correspondenciaExternaModel = new CorrespondenciaExternaModel();
        $id = $this->request->getPost('idext');
        $resultado = array(
            'error' => 'No se guardo la información',
        );
        if($fila = $correspondenciaExternaModel->find($id)){
            $data = array(
                'id' => $fila['id'],
                'fk_tipo_documento_externo' => $this->request->getPost('fk_tipo_documento_externo'),
                'observacion_recepcion' => mb_strtoupper(trim($this->request->getPost('observacion_recepcion'))),
                'fk_usuario_recepcion' => session()->get('registroUser'),
                'fecha_recepcion' => date('Y-m-d H:i:s'),
                'estado' => 'RECIBIDO',
            );

            if($correspondenciaExternaModel->save($data) === false)
                $resultado = array('error' => $correspondenciaExternaModel->errors());

            $resultado = array(
                'idext' => $fila['id']
            );
            echo json_encode($resultado);
        }
    }

    /*public function eliminar($id){
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

    }*/
}