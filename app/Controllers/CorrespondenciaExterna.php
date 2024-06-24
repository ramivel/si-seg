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
    protected $urlSincobol = 'https://sincobol.autoridadminera.gob.bo/sincobol/';

    public function misIngresos()
    {
        $db = \Config\Database::connect();
        $campos = array('ce.id', 'ce.editar', 't.controlador', 'ac.fk_area_minera', 'ac.fk_hoja_ruta', 'ce.estado', 't.nombre as tipo_tramite', 'ac.correlativo', 'ce.cite', "to_char(ce.fecha_cite, 'DD/MM/YYYY') as fecha_cite",
        "CONCAT(pe.nombres, ' ', pe.apellidos, ' (', pe.institucion, ' - ',pe.cargo,')') as remitente", 'ce.referencia', 'ce.doc_digital',
        "CONCAT('CITE: ',ce.cite,'<br>Fecha: ',to_char(ce.fecha_cite, 'DD/MM/YYYY'),'<br>Remitente: ',CONCAT(pe.nombres, ' ', pe.apellidos, ' (', pe.institucion, ' - ',pe.cargo,')'),'<br>Referencia: ',ce.referencia) as documento_externo",
        'ce.editar', "to_char(ce.created_at, 'DD/MM/YYYY HH24:MI') as fecha_ingreso",
        "to_char(ce.fecha_recepcion, 'DD/MM/YYYY HH24:MI') as fecha_recepcion", "CONCAT(ur.nombre_completo,'<br><b>',pr.nombre,'<b>') as recepcion",
        "CONCAT(ud.nombre_completo,'<br><b>',pd.nombre,'<b>') as usuario_actual",
        );
        $where = array(
            'ce.fk_tramite' => 1,
            'ce.deleted_at' => NULL,
            'ce.fk_usuario_creador' => session()->get('registroUser')
        );
        $builder = $db->table('public.correspondencia_externa AS ce')
        ->join('public.tramites AS t', 'ce.fk_tramite = t.id', 'left')
        ->join('public.acto_administrativo AS ac', 'ce.fk_acto_administrativo = ac.id', 'left')
        ->join('public.persona_externa AS pe', 'ce.fk_persona_externa = pe.id', 'left')
        ->join('public.usuarios AS ur', 'ce.fk_usuario_recepcion = ur.id', 'left')
        ->join('public.perfiles AS pr', 'ur.fk_perfil = pr.id', 'left')
        ->join('usuarios as ud', 'ac.ultimo_fk_usuario_destinatario = ud.id', 'left')
        ->join('perfiles as pd', 'ud.fk_perfil = pd.id', 'left')
        ->select($campos)
        ->where($where)
        ->orderBy('ce.id', 'DESC');
        $datos = $builder->get()->getResultArray();
        $campos_listar=array('Estado','Fecha Ingreso', 'Fecha Recepción', 'Recepcionado Por','Correlativo', 'Usuario Actual','Documento Externo', 'Doc. Digital', );
        $campos_reales=array('estado','fecha_ingreso','fecha_recepcion','recepcion','correlativo','usuario_actual','documento_externo', 'doc_digital', );
        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Mis Ingresos';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['datos'] = $datos;
        $contenido['campos_listar'] = $campos_listar;
        $contenido['campos_reales'] = $campos_reales;
        $contenido['subtitulo'] = 'Mis Ingresos';
        $contenido['controlador'] = $this->controlador;
        $contenido['sincobol'] = $this->urlSincobol;
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
                    'max_size[doc_digital,20000]',
                ],
            ]);
            if(!$validation){
                $personaExternaModel = new PersonaExternaModel();
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
                    'fk_tramite' => 1,
                    'fk_acto_administrativo' => $this->request->getPost('fk_acto_administrativo'),
                    'fk_persona_externa' => $this->request->getPost('fk_persona_externa'),
                    'cite' => mb_strtoupper($this->request->getPost('cite')),
                    'fecha_cite' => $this->request->getPost('fecha_cite'),
                    'referencia' => mb_strtoupper($this->request->getPost('referencia')),
                    'fojas' => $this->request->getPost('fojas'),
                    'adjuntos' => mb_strtoupper($this->request->getPost('adjuntos')),
                    'doc_digital' => $path.$nombreAdjunto,
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
        $contenido['modal_remitente'] = view($this->carpeta.'nuevo_remitente', array('expedidos'=>$this->expedidos));
        $data['content'] = view($this->carpeta.'agregar', $contenido);
        $data['menu_actual'] = $this->menuActual.'agregar';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['validacion_js'] = 'correspondencia-externa-validation.js';
        echo view('templates/template', $data);
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

            $contenido['acto_administrativo'] = $this->datosActoAdministrativo($fila['fk_acto_administrativo']);
            $campos = array('id', "CONCAT(documento_identidad, ' ', expedido, ' - ', nombres, ' ', apellidos, ' (', institucion, ' - ',cargo,')') as nombre");
            $contenido['persona_externa'] = $personaExternaModel->select($campos)->find($fila['fk_persona_externa']);
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['fila'] = $fila;
            $contenido['doc_digital_anterior'] = $fila['doc_digital'];
            $contenido['tramite'] = $tramitesModel->find($fila['fk_tramite']);
            $contenido['accion'] = $this->controlador.'guardar_editar';
            $contenido['validation'] = $this->validator;
            $contenido['controlador'] = $this->controlador;
            $contenido['modal_remitente'] = view($this->carpeta.'nuevo_remitente', array('expedidos'=>$this->expedidos));
            $data['content'] = view($this->carpeta.'editar', $contenido);
            $data['menu_actual'] = $this->menuActual.'mis_ingresos';
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['validacion_js'] = 'correspondencia-externa-editar-validation.js';
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador.'mis_ingresos');
        }
    }

    public function guardarEditar(){

        if ($this->request->getPost()) {
            $id = $this->request->getPost('id');
            $correspondenciaExternaModel = new CorrespondenciaExternaModel();
            $actoAdministrativoModel = new ActoAdministrativoModel();
            $personaExternaModel = new PersonaExternaModel();
            $validation = $this->validate([
                'id' => [
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
                    'mime_in[doc_digital,application/pdf]',
                    'max_size[doc_digital,20000]',
                ],
            ]);
            if(!$validation){
                $db = \Config\Database::connect();
                $campos = array('id', "CONCAT(correlativo,' (',codigo_unico,' - ',denominacion,')') as hr");
                $where = array(
                    'ac.deleted_at' => NULL,
                    'ac.id' => $this->request->getPost('fk_acto_administrativo'),
                );
                $builder = $db->table('public.acto_administrativo as ac')
                ->select($campos)
                ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
                ->where($where);
                $contenido['hr_madre'] = $builder->get()->getRowArray();
                $cabera['titulo'] = $this->titulo;
                $cabera['navegador'] = true;
                $cabera['subtitulo'] = 'Editar Registro';
                $campos = array('id', "CONCAT(documento_identidad, ' ', expedido, ' - ', nombres, ' ', apellidos, ' (', institucion, ' - ',cargo,')') as nombre");
                $contenido['persona_externa'] = $personaExternaModel->select($campos)->find($this->request->getPost('fk_persona_externa'));
                $contenido['doc_digital_anterior'] = $this->request->getPost('doc_digital_anterior');
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['accion'] = $this->controlador.'guardar_editar';
                $contenido['validation'] = $this->validator;
                $contenido['controlador'] = $this->controlador;
                $contenido['modal_remitente'] = view($this->carpeta.'nuevo_remitente', array('expedidos'=>$this->expedidos));
                $data['content'] = view($this->carpeta.'editar', $contenido);
                $data['menu_actual'] = $this->menuActual.'mis_ingresos';
                $data['tramites_menu'] = $this->tramitesMenu();
                $data['validacion_js'] = 'correspondencia-externa-editar-validation.js';
                echo view('templates/template', $data);
            }else{
                $acto_administrativo = $actoAdministrativoModel->find($this->request->getPost('fk_acto_administrativo'));
                $data = array(
                    'id' => $id,
                    'fk_acto_administrativo' => $this->request->getPost('fk_acto_administrativo'),
                    'fk_persona_externa' => $this->request->getPost('fk_persona_externa'),
                    'cite' => mb_strtoupper($this->request->getPost('cite')),
                    'fecha_cite' => $this->request->getPost('fecha_cite'),
                    'referencia' => mb_strtoupper($this->request->getPost('referencia')),
                    'fojas' => $this->request->getPost('fojas'),
                    'adjuntos' => mb_strtoupper($this->request->getPost('adjuntos')),
                    'editar' => 'FALSE',
                );
                $docDigital = $this->request->getFile('doc_digital');
                if(!empty($docDigital) && $docDigital->getSize()>0){
                    if(file_exists($this->request->getPost('doc_digital_anterior')))
                        @unlink($this->request->getPost('doc_digital_anterior'));

                    $path = 'archivos/cam/'.$acto_administrativo['fk_area_minera'].'/externo/';
                    $nombreAdjunto = $docDigital->getRandomName();
                    $docDigital->move($path,$nombreAdjunto);
                    $data['doc_digital'] = $path.$nombreAdjunto;
                }
                if($correspondenciaExternaModel->save($data) === false)
                    session()->setFlashdata('fail', $correspondenciaExternaModel->errors());
                else
                    session()->setFlashdata('success', 'Se Actualizo Correctamente la Información.');
                return redirect()->to($this->controlador.'mis_ingresos');
            }
        }
    }

    public function misIngresosMinilegal(){

        $db = \Config\Database::connect();
        $campos = array('ce.id', 'ce.editar', 'ce.estado', "to_char(ce.created_at, 'DD/MM/YYYY HH24:MI') as fecha_ingreso",
        "to_char(ce.fecha_recepcion, 'DD/MM/YYYY HH24:MI') as fecha_recepcion", "CONCAT(ur.nombre_completo,'<br><b>',pr.nombre,'<b>') as recepcion", 'hr.correlativo',
        "CONCAT(ud.nombre_completo,'<br><b>',pd.nombre,'<b>') as usuario_actual",
        "CONCAT('CITE: ',ce.cite,'<br>Fecha: ',to_char(ce.fecha_cite, 'DD/MM/YYYY'),'<br>Remitente: ',CONCAT(pe.nombres, ' ', pe.apellidos, ' (', pe.institucion, ' - ',pe.cargo,')'),'<br>Referencia: ',ce.referencia) as documento_externo",
        'ce.doc_digital',
        );
        $where = array(
            'ce.fk_tramite' => 2,
            'ce.deleted_at' => NULL,
            'ce.fk_usuario_creador' => session()->get('registroUser'),
        );
        $builder = $db->table('public.correspondencia_externa AS ce')
        ->join('public.tramites AS t', 'ce.fk_tramite = t.id', 'left')
        ->join('mineria_ilegal.hoja_ruta AS hr', 'ce.fk_hoja_ruta = hr.id', 'left')
        ->join('public.persona_externa AS pe', 'ce.fk_persona_externa = pe.id', 'left')
        ->join('public.usuarios AS ur', 'ce.fk_usuario_recepcion = ur.id', 'left')
        ->join('public.perfiles AS pr', 'ur.fk_perfil = pr.id', 'left')
        ->join('public.usuarios as ud', 'hr.fk_usuario_actual = ud.id', 'left')
        ->join('public.perfiles as pd', 'ud.fk_perfil = pd.id', 'left')
        ->select($campos)
        ->where($where)
        ->orderBy('ce.id', 'DESC');
        $datos = $builder->get()->getResultArray();
        $campos_listar=array('Estado','Fecha Ingreso', 'Fecha Recepción', 'Recepcionado Por','Correlativo', 'Usuario Actual','Documento Externo', 'Doc. Digital', );
        $campos_reales=array('estado','fecha_ingreso','fecha_recepcion','recepcion','correlativo','usuario_actual','documento_externo', 'doc_digital', );
        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Mis Ingresos';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['datos'] = $datos;
        $contenido['campos_listar'] = $campos_listar;
        $contenido['campos_reales'] = $campos_reales;
        $contenido['subtitulo'] = 'Mis Ingresos';
        $contenido['controlador'] = $this->controlador;
        $data['content'] = view($this->carpeta.'mis_ingresos_minilegal', $contenido);
        $data['menu_actual'] = 'mineria_ilegal/mis_ingresos_minilegal';
        $data['tramites_menu'] = $this->tramitesMenu();
        echo view('templates/template', $data);
    }
    public function agregarMinilegal(){

        if ($this->request->getPost()) {
            $correspondenciaExternaModel = new CorrespondenciaExternaModel();
            $validation = $this->validate([
                'fk_hoja_ruta' => [
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
                    'max_size[doc_digital,20000]', //kilobytes
                ],
            ]);
            if(!$validation){
                $db = \Config\Database::connect();
                $personaExternaModel = new PersonaExternaModel();
                $campos = array('hr.id', 'd.fk_tipo_denuncia', 'hr.correlativo', "to_char(hr.fecha_hoja_ruta, 'DD/MM/YYYY') as fecha_hoja_ruta");
                $where = array(
                    'hr.id' => $this->request->getPost('fk_hoja_ruta'),
                );
                $builder = $db->table('mineria_ilegal.hoja_ruta as hr')
                ->select($campos)
                ->join('mineria_ilegal.denuncias as d', 'hr.fk_denuncia = d.id', 'left')
                ->where($where);
                $contenido['hr_minilegal'] = $builder->get()->getRowArray();
                $campos = array('id', "CONCAT(documento_identidad, ' ', expedido, ' - ', nombres, ' ', apellidos, ' (', institucion, ' - ',cargo,')') as nombre");
                $contenido['persona_externa'] = $personaExternaModel->select($campos)->find($this->request->getPost('fk_persona_externa'));
                $contenido['validation'] = $this->validator;
            }else{
                $path = 'archivos/mineria_ilegal/correspondencia_externa/';
                if(!file_exists($path))
                    mkdir($path,0777);
                $docDigital = $this->request->getFile('doc_digital');
                $nombreAdjunto = $docDigital->getRandomName();
                $docDigital->move($path,$nombreAdjunto);
                $nombreAdjunto = $path.$nombreAdjunto;

                $data = array(
                    'fk_tramite' => 2,
                    'fk_hoja_ruta' => $this->request->getPost('fk_hoja_ruta'),
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
                return redirect()->to($this->controlador.'mis_ingresos_minilegal');
            }
        }

        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Agregar Nuevo';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['accion'] = $this->controlador.'agregar_minilegal';
        $contenido['validation'] = $this->validator;
        $contenido['controlador'] = $this->controlador;
        $contenido['tipos_denuncias'] = array(
            1 => 'PÁGINA WEB',
            2 => 'VENTANILLA ÚNICA',
            3 => 'VERIFICACIÓN DE OFICIO',
        );
        $contenido['modal_remitente'] = view($this->carpeta.'nuevo_remitente', array('expedidos'=>$this->expedidos));
        $data['content'] = view($this->carpeta.'agregar_minilegal', $contenido);
        $data['menu_actual'] = 'mineria_ilegal/agregar_minilegal';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['validacion_js'] = 'correspondencia-externa-minilegal-validation.js';
        echo view('templates/template', $data);
    }
    public function editarMinilegal($id){
        $correspondenciaExternaModel = new CorrespondenciaExternaModel();
        if($fila = $correspondenciaExternaModel->find($id)){
            $db = \Config\Database::connect();
            $personaExternaModel = new PersonaExternaModel();

            $campos = array('hr.id', 'd.fk_tipo_denuncia', 'hr.correlativo', "to_char(hr.fecha_hoja_ruta, 'DD/MM/YYYY') as fecha_hoja_ruta");
            $where = array(
                'hr.id' => $fila['fk_hoja_ruta'],
            );
            $builder = $db->table('mineria_ilegal.hoja_ruta as hr')
            ->select($campos)
            ->join('mineria_ilegal.denuncias as d', 'hr.fk_denuncia = d.id', 'left')
            ->where($where);
            $contenido['hr_minilegal'] = $builder->get()->getRowArray();

            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Editar Registro';
            $campos = array('id', "CONCAT(documento_identidad, ' ', expedido, ' - ', nombres, ' ', apellidos, ' (', institucion, ' - ',cargo,')') as nombre");
            $contenido['persona_externa'] = $personaExternaModel->select($campos)->find($fila['fk_persona_externa']);
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['fila'] = $fila;
            $contenido['datos_minilegal'] = $this->datosMineriaIlegal($fila['fk_hoja_ruta']);
            $contenido['doc_digital_anterior'] = $fila['doc_digital'];
            $contenido['accion'] = $this->controlador.'guardar_editar_minilegal';
            $contenido['validation'] = $this->validator;
            $contenido['controlador'] = $this->controlador;
            $contenido['tipos_denuncias'] = array(
                1 => 'PÁGINA WEB',
                2 => 'VENTANILLA ÚNICA',
                3 => 'VERIFICACIÓN DE OFICIO',
            );
            $contenido['modal_remitente'] = view($this->carpeta.'nuevo_remitente', array('expedidos'=>$this->expedidos));
            $contenido['ruta_archivos'] = $this->rutaArchivos;
            $data['content'] = view($this->carpeta.'editar_minilegal', $contenido);
            $data['menu_actual'] = 'mineria_ilegal/mis_ingresos_minilegal';
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['validacion_js'] = 'correspondencia-externa-editar-minilegal-validation.js';
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador.'mis_ingresos_minilegal');
        }
    }
    public function guardarEditarMinilegal(){

        if ($this->request->getPost()) {
            $id = $this->request->getPost('id');
            $validation = $this->validate([
                'id' => [
                    'rules' => 'required',
                ],
                'fk_hoja_ruta' => [
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
                    'max_size[doc_digital,20000]',
                ],
            ]);
            if(!$validation){
                $db = \Config\Database::connect();
                $personaExternaModel = new PersonaExternaModel();
                $campos = array('hr.id', 'd.fk_tipo_denuncia', 'hr.correlativo', "to_char(hr.fecha_hoja_ruta, 'DD/MM/YYYY') as fecha_hoja_ruta");
                $where = array(
                    'hr.id' => $this->request->getPost('fk_hoja_ruta'),
                );
                $builder = $db->table('mineria_ilegal.hoja_ruta as hr')
                ->select($campos)
                ->join('mineria_ilegal.denuncias as d', 'hr.fk_denuncia = d.id', 'left')
                ->where($where);
                $contenido['hr_minilegal'] = $builder->get()->getRowArray();
                $cabera['titulo'] = $this->titulo;
                $cabera['navegador'] = true;
                $cabera['subtitulo'] = 'Editar Registro';
                $campos = array('id', "CONCAT(documento_identidad, ' ', expedido, ' - ', nombres, ' ', apellidos, ' (', institucion, ' - ',cargo,')') as nombre");
                $contenido['persona_externa'] = $personaExternaModel->select($campos)->find($this->request->getPost('fk_persona_externa'));
                $contenido['doc_digital_anterior'] = $this->request->getPost('doc_digital_anterior');
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['accion'] = $this->controlador.'guardar_editar_minilegal';
                $contenido['validation'] = $this->validator;
                $contenido['controlador'] = $this->controlador;
                $contenido['tipos_denuncias'] = array(
                    1 => 'PÁGINA WEB',
                    2 => 'VENTANILLA ÚNICA',
                    3 => 'VERIFICACIÓN DE OFICIO',
                );
                $contenido['modal_remitente'] = view($this->carpeta.'nuevo_remitente', array('expedidos'=>$this->expedidos));
                $data['content'] = view($this->carpeta.'editar_minilegal', $contenido);
                $data['menu_actual'] = 'mineria_ilegal/mis_ingresos_minilegal';
                $data['tramites_menu'] = $this->tramitesMenu();
                $data['validacion_js'] = 'correspondencia-externa-editar-minilegal-validation.js';
                echo view('templates/template', $data);
            }else{
                $correspondenciaExternaModel = new CorrespondenciaExternaModel();
                $data = array(
                    'id' => $id,
                    'fk_hoja_ruta' => $this->request->getPost('fk_hoja_ruta'),
                    'fk_persona_externa' => $this->request->getPost('fk_persona_externa'),
                    'cite' => mb_strtoupper($this->request->getPost('cite')),
                    'fecha_cite' => $this->request->getPost('fecha_cite'),
                    'referencia' => mb_strtoupper($this->request->getPost('referencia')),
                    'fojas' => $this->request->getPost('fojas'),
                    'adjuntos' => mb_strtoupper($this->request->getPost('adjuntos')),
                    'editar' => 'FALSE',
                );
                $docDigital = $this->request->getFile('doc_digital');
                if(!empty($docDigital) && $docDigital->getSize()>0){
                    if(file_exists($this->request->getPost('doc_digital_anterior')))
                        @unlink($this->request->getPost('doc_digital_anterior'));

                    $path = 'archivos/mineria_ilegal/correspondencia_externa/';
                    $nombreAdjunto = $docDigital->getRandomName();
                    $docDigital->move($path,$nombreAdjunto);
                    $data['doc_digital'] = $path.$nombreAdjunto;
                }
                if($correspondenciaExternaModel->save($data) === false)
                    session()->setFlashdata('fail', $correspondenciaExternaModel->errors());
                else
                    session()->setFlashdata('success', 'Se Actualizo Correctamente la Información.');
                return redirect()->to($this->controlador.'mis_ingresos_minilegal');
            }
        }
    }

    private function obtenerTramites(){
        $tramitesModel = new TramitesModel();
        return $tramitesModel->where('activo = true')->orderBy('nombre', 'ASC')->findAll();
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

    private function datosActoAdministrativo($id_acto_administrativo){
        $db = \Config\Database::connect();
        $campos = array(
            'ac.fk_area_minera','dam.codigo_unico', 'dam.denominacion', 'dam.representante_legal', 'dam.nacionalidad', 'dam.titular','dam.clasificacion_titular as clasificacion',
            "CONCAT(ur.nombre_completo, ' - ',pr.nombre) as remitente", "CONCAT(ud.nombre_completo, ' - ',pd.nombre) as destinatario", "CONCAT(ua.nombre_completo,' - ',pa.nombre) as responsable");
        $where = array(
            'ac.id' => $id_acto_administrativo,
        );
        $builder = $db->table('public.acto_administrativo as ac')
        ->select($campos)
        ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
        ->join('usuarios as ur', 'ac.ultimo_fk_usuario_remitente = ur.id', 'left')
        ->join('perfiles as pr', 'ur.fk_perfil=pr.id', 'left')
        ->join('usuarios as ud', 'ac.fk_usuario_actual = ud.id', 'left')
        ->join('perfiles as pd', 'ud.fk_perfil=pd.id', 'left')
        ->join('usuarios as ua', 'ac.fk_usuario_actual = ua.id', 'left')
        ->join('perfiles as pa', 'ua.fk_perfil = pa.id', 'left')
        ->where($where);
        return $builder->get()->getRowArray();
    }

    private function datosMineriaIlegal($id_hoja_ruta){
        $db = \Config\Database::connect();
        $campos = array(
            'hr.fk_denuncia', 'hr.correlativo as correlativo_hr', "to_char(hr.fecha_hoja_ruta, 'DD/MM/YYYY HH24:MI') as fecha_hr",
            "den.fk_tipo_denuncia","den.correlativo as correlativo_denuncia", "to_char(den.fecha_denuncia, 'DD/MM/YYYY HH24:MI') as fecha_denuncia", "den.departamento", "den.provincia", "den.municipio",
            "CONCAT(ur.nombre_completo, ' - ',pr.nombre) as remitente", "CONCAT(ud.nombre_completo, ' - ',pd.nombre) as destinatario", "CONCAT(ua.nombre_completo,' - ',pa.nombre) as responsable");
        $where = array(
            'hr.id' => $id_hoja_ruta,
        );
        $builder = $db->table('mineria_ilegal.hoja_ruta as hr')
        ->select($campos)
        ->join('mineria_ilegal.denuncias as den', 'hr.fk_denuncia = den.id', 'left')
        ->join('usuarios as ur', 'hr.ultimo_fk_usuario_remitente = ur.id', 'left')
        ->join('perfiles as pr', 'ur.fk_perfil=pr.id', 'left')
        ->join('usuarios as ud', 'hr.fk_usuario_actual = ud.id', 'left')
        ->join('perfiles as pd', 'ud.fk_perfil=pd.id', 'left')
        ->join('usuarios as ua', 'hr.fk_usuario_actual = ua.id', 'left')
        ->join('perfiles as pa', 'ua.fk_perfil = pa.id', 'left')
        ->where($where);
        return $builder->get()->getRowArray();
    }

    public function actualizarPath(){
        $correspondenciaExternaModel = new CorrespondenciaExternaModel();
        $db = \Config\Database::connect();
        $campos = array(
            'ce.id', 'ac.fk_area_minera', 'ce.doc_digital',
        );
        $where = array(
            'ce.fk_tramite' => 1,
        );
        $builder = $db->table('public.correspondencia_externa as ce')
        ->select($campos)
        ->join('public.acto_administrativo as ac', 'ce.fk_acto_administrativo = ac.id', 'left')
        ->where($where)
        ->notLike('ce.doc_digital', '/')
        ->orderBy('ce.id', 'ASC');
        if($datos = $builder->get()->getResultArray()){
            foreach($datos as $row){
                $dataCorrespondencia = array(
                    'id' => $row['id'],
                    'doc_digital' => 'archivos/cam/'.$row['fk_area_minera'].'/externo/'.$row['doc_digital'],
                );
                if($correspondenciaExternaModel->save($dataCorrespondencia) === false)
                    echo "No se guardo el id: ".$row['id']."<br>";
                else
                    echo "Se guardo el id: ".$row['id']."<br>";
            }
        }

    }

}