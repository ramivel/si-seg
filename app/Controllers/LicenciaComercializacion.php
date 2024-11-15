<?php

namespace App\Controllers;

use App\Libraries\HojaRutaPdf;
use App\Models\DocumentosModel;
use App\Models\EstadoTramiteModel;
use App\Models\LicenciaComercializacionCorrelativosModel;
use App\Models\LicenciaComercializacionDerivacionModel;
use App\Models\LicenciaComercializacionDocumentoExternoModel;
use App\Models\LicenciaComercializacionHojaRutaModel;
use App\Models\OficinasModel;
use App\Models\PersonaExternaModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LicenciaComercializacion extends BaseController
{
    protected $titulo = 'Licencia de Comercialización';
    protected $controlador = 'licencia_comercializacion/';
    protected $carpeta = 'licencia_comercializacion/';
    protected $idTramite = 5;
    protected $menuActual = 'licencia_comercializacion/';
    protected $rutaArchivos = 'archivos/licencia_comercializacion/';
    protected $rutaDocumentos = 'archivos/licencia_comercializacion/documentos/';
    protected $alpha = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
    protected $fontPDF = 'helvetica';
    protected $acciones = array(
        'Para su conocimiento y consideración',
        'Verificar Requisitos',
        'Requerir Informe Técnico',
        'Proceder conforme a reglamento',
        'Archivar',
        '',
        '',
        '',
        '',
        '',
    );
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
        $datos = array();
        $campos = array(
            "hr.id","to_char(hr.fecha_hoja_ruta, 'DD/MM/YYYY') as fecha_hoja_ruta","hr.correlativo","dex.estado",
            "CONCAT('CITE: ',dex.cite,'<br>Fecha: ',to_char(dex.fecha_cite, 'DD/MM/YYYY'),'<br>Remitente: ',CONCAT(pex.nombres, ' ', pex.apellidos, ' (', pex.institucion, ' - ',pex.cargo,')'),'<br>Referencia: ',dex.referencia) as documento_externo",
            "dex.doc_digital","dex.editar","CONCAT(ur.nombre_completo,'<br><b>',pr.nombre,'<b>') as recepcion","to_char(dex.fecha_recepcion, 'DD/MM/YYYY') as fecha_recepcion",
        );
        $where = array(
            'hr.deleted_at' => NULL,
            'hr.fk_usuario_creador' => session()->get('registroUser'),
        );
        $builder = $db->table('licencia_comercializacion.hoja_ruta as hr')
        ->select($campos)
        ->join('licencia_comercializacion.documento_externo as dex', 'hr.id = dex.fk_hoja_ruta', 'left')
        ->join('public.persona_externa as pex', 'dex.fk_persona_externa = pex.id', 'left')
        ->join('public.usuarios AS ur', 'dex.fk_usuario_recepcion = ur.id', 'left')
        ->join('public.perfiles AS pr', 'ur.fk_perfil = pr.id', 'left')
        ->where($where)
        ->orderBY('hr.id', 'DESC');
        $datos = $builder->get()->getResult('array');
        $campos_listar=array(
            'Estado','Fecha Hoja Ruta','Hoja de Ruta','Fecha Recepción','Usuario Recepción','Documento Externo','Doc. Digital',
        );
        $campos_reales=array(
            'estado','fecha_hoja_ruta','correlativo','fecha_recepcion','recepcion','documento_externo', 'doc_digital'
        );

        $cabera['titulo'] = $this->titulo;
        $cabera['subtitulo'] = 'Mis Licencias de Comercialización';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['datos'] = $datos;
        $contenido['campos_listar'] = $campos_listar;
        $contenido['campos_reales'] = $campos_reales;
        $contenido['controlador'] = $this->controlador;
        $contenido['id_tramite'] = $this->idTramite;
        $data['content'] = view($this->carpeta.'mis_ingresos', $contenido);
        $data['menu_actual'] = $this->menuActual.'mis_ingresos';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['alertas'] = $this->alertasTramites();
        echo view('templates/template', $data);
    }
    public function listadoRecepcion()
    {
        $db = \Config\Database::connect();
        $campos_listar=array(
            'Fecha', 'Días<br>Pasados', 'Hoja de Ruta', 'Remitente', 'Instrucción', 'Ultimo(s) Documento(s) Anexado(s)', 'Estado Tramite', 'Responsable Trámite',
        );
        $campos_reales=array(
            'ultimo_fecha_derivacion','dias', 'correlativo', 'remitente', 'ultimo_instruccion', 'ultimos_documentos', 'estado_tramite', 'responsable',
        );
        $campos = array(
            'hr.id','hr.ultimo_fk_documentos',"to_char(hr.ultimo_fecha_derivacion, 'DD/MM/YYYY') as ultimo_fecha_derivacion","(CURRENT_DATE - hr.ultimo_fecha_derivacion::date) as dias",
            'hr.correlativo',"CONCAT(urem.nombre_completo,'<br><b>',prem.nombre,'<b>') as remitente",'hr.ultimo_instruccion',
            "CASE WHEN hr.ultimo_fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'. ',etp.nombre,'<br>',etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE CONCAT(etp.orden,'. ',etp.nombre) END as estado_tramite",
            "CONCAT(ures.nombre_completo,'<br><b>',pres.nombre,'<b>') as responsable","dex.estado as estado_documento_externo"
        );
        $where = array(
            'hr.deleted_at' => NULL,
            'hr.ultimo_fk_usuario_destinatario' => session()->get('registroUser'),
        );
        $builder = $db->table('licencia_comercializacion.hoja_ruta as hr')
        ->select($campos)
        ->join('licencia_comercializacion.documento_externo as dex', 'hr.id = dex.fk_hoja_ruta', 'left')
        ->join('estado_tramite as etp', 'hr.ultimo_fk_estado_tramite_padre = etp.id', 'left')
        ->join('estado_tramite as eth', 'hr.ultimo_fk_estado_tramite_hijo = eth.id', 'left')
        ->join('usuarios as urem', 'hr.ultimo_fk_usuario_remitente = urem.id', 'left')
        ->join('perfiles as prem', 'urem.fk_perfil = prem.id', 'left')
        ->join('usuarios as ures', 'hr.ultimo_fk_usuario_responsable = ures.id', 'left')
        ->join('perfiles as pres', 'ures.fk_perfil = pres.id', 'left')
        ->where($where)
        ->whereIn('hr.ultimo_estado', array('DERIVADO'))
        ->orderBY('hr.id', 'DESC');
        $datos = $this->obtenerUltimosDocumentos($builder->get()->getResult('array'));
        //$datos = $this->obtenerCorrespondenciaExterna($datos);
        $cabera['titulo'] = $this->titulo;
        $cabera['subtitulo'] = 'Listado de Hojas de Ruta Derivadas';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['datos'] = $datos;
        $contenido['campos_listar'] = $campos_listar;
        $contenido['campos_reales'] = $campos_reales;
        $contenido['controlador'] = $this->controlador;
        $contenido['id_tramite'] = $this->idTramite;
        $data['content'] = view($this->carpeta.'listado_recepcion', $contenido);
        $data['menu_actual'] = $this->menuActual.'listado_recepcion';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['alertas'] = $this->alertasTramites();
        echo view('templates/template', $data);
    }
    public function misTramites()
    {
        $db = \Config\Database::connect();
        $campos_listar=array(
            'Estado','Fecha','Días<br>Pasados','Hoja de Ruta','Remitente','Instrucción','Ultimo(s) Documento(s) Anexado(s)','Estado Tramite','Responsable Trámite','Documento Externo','Doc. Digital',
        );
        $campos_reales=array(
            'ultimo_estado','ultimo_fecha_derivacion','dias','correlativo','remitente','ultimo_instruccion','ultimos_documentos','estado_tramite','responsable','documento_externo','doc_digital'
        );
        $campos = array(
            'hr.id','hr.ultimo_fk_documentos',"hr.ultimo_estado","to_char(hr.ultimo_fecha_derivacion, 'DD/MM/YYYY') as ultimo_fecha_derivacion","(CURRENT_DATE - hr.ultimo_fecha_derivacion::date) as dias",
            'hr.correlativo',"CONCAT(urem.nombre_completo,'<br><b>',prem.nombre,'<b>') as remitente","CONCAT(udes.nombre_completo,'<br><b>',pdes.nombre,'<b>') as destinatario",'hr.ultimo_instruccion',
            "CASE WHEN hr.ultimo_fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'. ',etp.nombre,'<br>',etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE CONCAT(etp.orden,'. ',etp.nombre) END as estado_tramite",
            "CASE WHEN hr.ultimo_fk_estado_tramite_hijo > 0 AND eth.finalizar THEN 'SI' WHEN etp.finalizar THEN 'SI'  ELSE 'NO' END as finalizar",
            "CONCAT(ures.nombre_completo,'<br><b>',pres.nombre,'<b>') as responsable",
            "CONCAT('CITE: ',dex.cite,'<br>Fecha: ',to_char(dex.fecha_cite, 'DD/MM/YYYY'),'<br>Remitente: ',CONCAT(pex.nombres, ' ', pex.apellidos, ' (', pex.institucion, ' - ',pex.cargo,')'),'<br>Referencia: ',dex.referencia) as documento_externo",
            "dex.doc_digital","hr.editar"
        );
        $where = array(
            'hr.deleted_at' => NULL,
            'hr.fk_usuario_actual' => session()->get('registroUser'),
        );
        $builder = $db->table('licencia_comercializacion.hoja_ruta as hr')
        ->select($campos)
        ->join('licencia_comercializacion.documento_externo as dex', 'hr.id = dex.fk_hoja_ruta', 'left')
        ->join('public.persona_externa as pex', 'dex.fk_persona_externa = pex.id', 'left')
        ->join('estado_tramite as etp', 'hr.ultimo_fk_estado_tramite_padre = etp.id', 'left')
        ->join('estado_tramite as eth', 'hr.ultimo_fk_estado_tramite_hijo = eth.id', 'left')
        ->join('usuarios as urem', 'hr.ultimo_fk_usuario_remitente = urem.id', 'left')
        ->join('perfiles as prem', 'urem.fk_perfil = prem.id', 'left')
        ->join('usuarios as udes', 'hr.ultimo_fk_usuario_destinatario = udes.id', 'left')
        ->join('perfiles as pdes', 'udes.fk_perfil = pdes.id', 'left')
        ->join('usuarios as ures', 'hr.ultimo_fk_usuario_responsable = ures.id', 'left')
        ->join('perfiles as pres', 'ures.fk_perfil = pres.id', 'left')
        ->where($where)
        ->whereIn('hr.ultimo_estado',array('DERIVADO', 'RECIBIDO', 'EN ESPERA', 'DEVUELTO'))
        ->orderBY('hr.id', 'DESC');
        $datos = $this->obtenerUltimosDocumentos($builder->get()->getResult('array'));
        //$datos = $this->obtenerCorrespondenciaExterna($datos);
        $cabera['titulo'] = $this->titulo;
        $cabera['subtitulo'] = 'Listado de Mis Hojas de Ruta';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['datos'] = $datos;
        $contenido['campos_listar'] = $campos_listar;
        $contenido['campos_reales'] = $campos_reales;
        $contenido['controlador'] = $this->controlador;
        $contenido['id_tramite'] = $this->idTramite;
        $data['content'] = view($this->carpeta.'index', $contenido);
        $data['menu_actual'] = $this->menuActual.'mis_tramites';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['alertas'] = $this->alertasTramites();
        echo view('templates/template', $data);
    }

    public function agregarVentanilla(){
        if ($this->request->getPost()) {
            $validation = $this->validate([
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
                    'max_size[doc_digital,35000]',
                    'errors' => [
                        'uploaded' => 'Este campo es obligatorio.'
                    ]
                ],
            ]);
            if(!$validation){
                $personaExternaModel = new PersonaExternaModel();
                if($this->request->getPost('fk_persona_externa')){
                    $campos = array('id', "CONCAT(documento_identidad, ' ', expedido, ' - ', nombres, ' ', apellidos, ' (', institucion, ' - ',cargo,')') as nombre");
                    $contenido['persona_externa'] = $personaExternaModel->select($campos)->find($this->request->getPost('fk_persona_externa'));
                }
                $contenido['validation'] = $this->validator;
            }else{
                $oficinaModel = new OficinasModel();
                $estadoTramiteModel = new EstadoTramiteModel();
                $licenciaComercializacionHojaRutaModel = new LicenciaComercializacionHojaRutaModel();
                $licenciaComercializacionDocumentoExternoModel = new LicenciaComercializacionDocumentoExternoModel();
                $licenciaComercializacionDerivacionModel = new LicenciaComercializacionDerivacionModel();

                $estado = 'DERIVADO';
                $oficina = $oficinaModel->find(session()->get('registroOficina'));
                $correlativo = $this->obtenerCorrelativo($oficina['correlativo'].'LCOM/');
                $instruccion = 'LICENCIA DE COMERCIALIZACIÓN - '.$correlativo;
                $fk_usuario_destinatario = $this->obtenerDirectorDepartamentalRegional($oficina['id']);
                $where = array('deleted_at' => NULL, 'fk_estado_padre' => NULL, 'fk_tramite' =>$this->idTramite);
                $primerEstado = $estadoTramiteModel->where($where)->orderBy('orden', 'ASC')->first();

                $dataLC = array(
                    'fk_oficina' => session()->get('registroOficina'),
                    'correlativo' => $correlativo,
                    'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                    'ultimo_fk_estado_tramite_padre' => $primerEstado['id'],
                    'ultimo_fecha_actualizacion_estado' => date('Y-m-d H:i:s'),
                    'ultimo_instruccion' => $instruccion,
                    'ultimo_fk_usuario_remitente' => session()->get('registroUser'),
                    'ultimo_fk_usuario_destinatario' => $fk_usuario_destinatario,
                    'ultimo_fk_usuario_responsable' => $fk_usuario_destinatario,
                    'ultimo_estado' => $estado,
                    'fk_usuario_actual' => session()->get('registroUser'),
                    'fk_usuario_creador' => session()->get('registroUser'),
                    'fk_usuario_destino' => $fk_usuario_destinatario,
                    'fecha_hoja_ruta' => date('Y-m-d H:i:s'),
                    'estado_tramite_apm' => $this->obtenerEstadoTramiteAPM($primerEstado['id']),
                );
                if($licenciaComercializacionHojaRutaModel->insert($dataLC) === false){
                    session()->setFlashdata('fail', $licenciaComercializacionHojaRutaModel->errors());
                }else{
                    $idHR = $licenciaComercializacionHojaRutaModel->getInsertID();

                    $docDigital = $this->request->getFile('doc_digital');
                    $nombreAdjunto = $docDigital->getRandomName();
                    $docDigital->move($this->rutaArchivos,$nombreAdjunto);

                    $dataCorrespondenciaExterna = array(
                        'fk_hoja_ruta' => $idHR,
                        'fk_persona_externa' => $this->request->getPost('fk_persona_externa'),
                        'cite' => mb_strtoupper($this->request->getPost('cite')),
                        'fecha_cite' => $this->request->getPost('fecha_cite'),
                        'referencia' => mb_strtoupper($this->request->getPost('referencia')),
                        'fojas' => $this->request->getPost('fojas'),
                        'adjuntos' => mb_strtoupper($this->request->getPost('adjuntos')),
                        'doc_digital' => $this->rutaArchivos.$nombreAdjunto,
                    );

                    if($licenciaComercializacionDocumentoExternoModel->insert($dataCorrespondenciaExterna) === false){
                        session()->setFlashdata('fail', $licenciaComercializacionDocumentoExternoModel->errors());
                    }else{

                        $dataDerivacion = array(
                            'fk_hoja_ruta' => $idHR,
                            'fk_estado_tramite_padre' => $primerEstado['id'],
                            'fecha_actualizacion_estado' => date('Y-m-d H:i:s'),
                            'instruccion' => $instruccion,
                            'estado' => $estado,
                            'fk_usuario_remitente' => session()->get('registroUser'),
                            'fk_usuario_destinatario' => $fk_usuario_destinatario,
                            'fk_usuario_responsable' => $fk_usuario_destinatario,
                            'fk_usuario_creador' => session()->get('registroUser'),
                        );
                        if($licenciaComercializacionDerivacionModel->insert($dataDerivacion) === false){
                            session()->setFlashdata('fail', $licenciaComercializacionDerivacionModel->errors());
                        }else{
                            $this->actualizarCorrelativo($oficina['correlativo'].'LCOM/');
                            session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información. <code><a href="'.base_url($this->controlador.'hoja_ruta_pdf/'.$idHR).'" target="_blank">Descargar Hoja de Ruta</a></code>');
                        }
                    }
                }
                return redirect()->to($this->controlador.'mis_ingresos');
            }
        }

        $cabera['titulo'] = $this->titulo;
        $cabera['subtitulo'] = 'Registrar Nueva Solicitud';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['accion'] = $this->controlador.'agregar_ventanilla';
        $contenido['controlador'] = $this->controlador;
        $contenido['modal_remitente'] = view($this->carpeta.'nuevo_remitente', array('expedidos'=>$this->expedidos));
        $data['content'] = view($this->carpeta.'agregar_ventanilla', $contenido);
        $data['menu_actual'] = $this->menuActual.'agregar_ventanilla';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['validacion_js'] = $this->carpeta.'agregar-ventanilla.js';
        echo view('templates/template', $data);
    }
    public function editarVentanilla($id_hoja_ruta){
        $db = \Config\Database::connect();
        $campos = array(
            'hr.id','dex.cite',"dex.fecha_cite","dex.fk_persona_externa","dex.referencia","dex.fojas","dex.adjuntos","dex.doc_digital"
        );
        $where = array(
            'hr.id' => $id_hoja_ruta,
            'hr.deleted_at' => NULL,
            'hr.fk_usuario_creador' => session()->get('registroUser'),
            'hr.editar' => TRUE,
            'dex.estado' => 'INGRESADO'
        );
        $builder = $db->table('licencia_comercializacion.hoja_ruta as hr')
        ->select($campos)
        ->join('licencia_comercializacion.documento_externo as dex', 'hr.id = dex.fk_hoja_ruta', 'left')
        ->join('public.persona_externa as pex', 'dex.fk_persona_externa = pex.id', 'left')
        ->where($where);
        if($hoja_ruta = $builder->get()->getRowArray()){
            $cabera['titulo'] = $this->titulo;
            $cabera['subtitulo'] = 'Editar Licencia de Comercialización';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['fila'] = $hoja_ruta;
            $contenido['persona_externa'] = $this->obtenerPersonaExternaSelect($hoja_ruta['fk_persona_externa']);
            $contenido['doc_digital_anterior'] = $hoja_ruta['doc_digital'];
            $contenido['accion'] = $this->controlador.'guardar_editar_ventanilla';
            $contenido['controlador'] = $this->controlador;
            $contenido['modal_remitente'] = view($this->carpeta.'nuevo_remitente', array('expedidos'=>$this->expedidos));
            $data['content'] = view($this->carpeta.'editar_ventanilla', $contenido);
            $data['menu_actual'] = $this->menuActual.'mis_ingresos';
            $data['validacion_js'] = $this->carpeta.'editar-ventanilla.js';
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['alertas'] = $this->alertasTramites();
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador.'mis_ingresos');
        }
    }
    public function guardarEditarVentanilla(){
        if ($this->request->getPost()) {
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
                    'max_size[doc_digital,35000]',
                ],
            ]);
            if(!$validation){
                $cabera['titulo'] = $this->titulo;
                $cabera['subtitulo'] = 'Editar Licencia de Comercialización';
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['persona_externa'] = $this->obtenerPersonaExternaSelect($this->request->getPost('fk_persona_externa'));
                $contenido['doc_digital_anterior'] = $this->request->getPost('doc_digital_anterior');
                $contenido['accion'] = $this->controlador.'guardar_editar_ventanilla';
                $contenido['controlador'] = $this->controlador;
                $contenido['modal_remitente'] = view($this->carpeta.'nuevo_remitente', array('expedidos'=>$this->expedidos));
                $data['content'] = view($this->carpeta.'editar_ventanilla', $contenido);
                $data['menu_actual'] = $this->menuActual.'mis_ingresos';
                $data['validacion_js'] = $this->carpeta.'editar-ventanilla.js';
                $data['tramites_menu'] = $this->tramitesMenu();
                $data['alertas'] = $this->alertasTramites();
                echo view('templates/template', $data);
            }else{
                $licenciaComercializacionDocumentoExternoModel = new LicenciaComercializacionDocumentoExternoModel();
                $dataCorrespondenciaExterna = array(
                    'fk_hoja_ruta' => $this->request->getPost('id'),
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
                    $nombreAdjunto = $docDigital->getRandomName();
                    $docDigital->move($this->rutaArchivos,$nombreAdjunto);
                    $dataCorrespondenciaExterna['doc_digital'] = $this->rutaArchivos.$nombreAdjunto;
                }
                if($licenciaComercializacionDocumentoExternoModel->save($dataCorrespondenciaExterna) === false)
                    session()->setFlashdata('fail', $licenciaComercializacionDocumentoExternoModel->errors());
                else
                    session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información. <code><a href="'.base_url($this->controlador.'hoja_ruta_pdf/'.$this->request->getPost('id')).'" target="_blank">Descargar Hoja de Ruta</a></code>');
                return redirect()->to($this->controlador.'mis_ingresos');
            }
        }
    }
    public function recibirMultiple(){
        if ($this->request->getPost()) {
            if($ids_hojas_rutas = $this->request->getPost('recibir')){
                foreach($ids_hojas_rutas as $id_hoja_ruta)
                    $this->recibir($id_hoja_ruta, false);
                session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información.');
                return redirect()->to($this->controlador.'listado_recepcion');
            }
        }
        session()->setFlashdata('fail', 'No se pudo recepcionar los trámites.');
        return redirect()->to($this->controlador.'mis_tramites');
    }
    public function recibir($id_hoja_ruta,$redireccionar=true){
        $licenciaComercializacionHojaRutaModel = new LicenciaComercializacionHojaRutaModel();
        $licenciaComercializacionDocumentoExternoModel = new LicenciaComercializacionDocumentoExternoModel();
        $licenciaComercializacionDerivacionModel = new LicenciaComercializacionDerivacionModel();
        $where = array(
            'id' => $id_hoja_ruta,
            'deleted_at' => NULL,
            'ultimo_fk_usuario_destinatario' => session()->get('registroUser'),
        );
        if($fila = $licenciaComercializacionHojaRutaModel->where($where)->whereIn('ultimo_estado', array('DERIVADO'))->first()){
            $estado = 'RECIBIDO';
            if($documentoExterno = $licenciaComercializacionDocumentoExternoModel->where(array('fk_hoja_ruta'=>$fila['id'],'estado'=>'INGRESADO'))->first()){
                $dataDocumentoExterno = array(
                    'fk_hoja_ruta' => $documentoExterno['fk_hoja_ruta'],
                    'estado' => $estado,
                    'fecha_recepcion' => date('Y-m-d H:i:s'),
                    'fk_usuario_recepcion' => session()->get('registroUser'),
                );
                if($licenciaComercializacionDocumentoExternoModel->save($dataDocumentoExterno) === false)
                    session()->setFlashdata('fail', $licenciaComercializacionDocumentoExternoModel->errors());
            }
            $data = array(
                'id' => $fila['id'],
                'ultimo_estado' => $estado,
                'fk_usuario_actual' => session()->get('registroUser'),
                'editar' => true,
            );
            if($licenciaComercializacionHojaRutaModel->save($data) === false)
                session()->setFlashdata('fail', $licenciaComercializacionHojaRutaModel->errors());
            $where = array(
                'fk_hoja_ruta' => $fila['id'],
            );
            $derivacion = $licenciaComercializacionDerivacionModel->where($where)->orderBY('id', 'DESC')->first();
            $dataDerivacion = array(
                'id' => $derivacion['id'],
                'estado' => $estado,
                'fecha_recepcion' => date('Y-m-d H:i:s'),
            );
            if($licenciaComercializacionDerivacionModel->save($dataDerivacion) === false)
                session()->setFlashdata('fail', $licenciaComercializacionDerivacionModel->errors());
        }
        if($redireccionar)
            return redirect()->to($this->controlador.'listado_recepcion');
        else
            return true;
    }
    public function atender($id_hoja_ruta){
        $licenciaComercializacionHojaRutaModel = new LicenciaComercializacionHojaRutaModel();
        $where = array(
            'deleted_at' => NULL,
            'fk_usuario_actual' => session()->get('registroUser'),
            'id' => $id_hoja_ruta
        );
        if($fila = $licenciaComercializacionHojaRutaModel->where($where)->first()){
            $licenciaComercializacionDerivacionModel = new LicenciaComercializacionDerivacionModel();
            $where = array(
                'deleted_at' => NULL,
                'fk_hoja_ruta' => $id_hoja_ruta,
            );
            $ultima_derivacion = $licenciaComercializacionDerivacionModel->where($where)->orderBy('id','DESC')->first();
            $cabera['titulo'] = $this->titulo;
            $cabera['subtitulo'] = 'Atender Hoja de Ruta';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['correlativo'] = $fila['correlativo'];
            $contenido['informacion_tramite'] = $this->obtenerInformacionTramiteFormulario($fila['id']);
            $contenido['fila'] = $fila;
            $contenido['ultima_derivacion'] = $ultima_derivacion;
            if(in_array(10, session()->get('registroPermisos')))
                $contenido['documentos_cargar'] = $this->obtenerDocumentosCargar($id_hoja_ruta);
            $contenido['documentos'] = $this->obtenerDocumentos($id_hoja_ruta);
            $contenido['estadosTramites'] = $this->obtenerEstadosTramites($this->idTramite);
            $contenido['id_estado_padre'] = $ultima_derivacion['fk_estado_tramite_padre'];
            if($ultima_derivacion['fk_estado_tramite_hijo']){
                $contenido['estadosTramitesHijo'] = $this->obtenerEstadosTramitesHijo($ultima_derivacion['fk_estado_tramite_padre']);
                $contenido['id_estado_hijo'] = $ultima_derivacion['fk_estado_tramite_hijo'];
            }
            $contenido['accion'] = $this->controlador.'guardar_atender';
            $contenido['controlador'] = $this->controlador;
            $contenido['id_tramite'] = $this->idTramite;
            $data['content'] = view($this->carpeta.'atender', $contenido);
            $data['menu_actual'] = $this->menuActual.'mis_tramites';
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['alertas'] = $this->alertasTramites();
            $data['validacion_js'] = $this->carpeta.'atender.js';
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador);
        }
    }
    public function guardarAtender(){
        if ($this->request->getPost()) {
            $id = $this->request->getPost('id_hoja_ruta');
            $documentos = $this->obtenerDocumentos($id);
            if(in_array(10, session()->get('registroPermisos')))
                $documentos_cargar = $this->obtenerDocumentosCargar($id);
            //$correspondencia_externa = $this->informacionHRExterna($id);
            $validation = $this->validate([
                'id_hoja_ruta' => [
                    'rules' => 'required',
                ],
                'id_derivacion' => [
                    'rules' => 'required',
                ],
                'fk_estado_tramite' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar un Estado de Tramite.'
                    ]
                ],
                'fk_usuario_destinatario' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar el Destinatario.'
                    ]
                ],
                'instruccion' => [
                    'rules' => 'required',
                ],
            ]);
            if(!$validation){
                $cabera['titulo'] = $this->titulo;
                $cabera['subtitulo'] = 'Atender Hoja de Ruta';
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['informacion_tramite'] = $this->obtenerInformacionTramiteFormulario($id);
                $contenido['correlativo'] = $this->request->getPost('correlativo');
                $contenido['documentos'] = $documentos;
                if(isset($documentos_cargar) && in_array(10, session()->get('registroPermisos')))
                    $contenido['documentos_cargar'] = $documentos_cargar;
                $contenido['estadosTramites'] = $this->obtenerEstadosTramites($this->idTramite);
                $contenido['estadosTramitesHijo'] = $this->obtenerEstadosTramitesHijo($this->request->getPost('fk_estado_tramite'));
                $contenido['id_estado_padre'] = $this->request->getPost('fk_estado_tramite');
                $contenido['id_estado_hijo'] = $this->request->getPost('fk_estado_tramite_hijo');
                $contenido['usu_destinatario'] = $this->obtenerUsuario($this->request->getPost('fk_usuario_destinatario'));
                $contenido['accion'] = $this->controlador.'guardar_atender';
                $contenido['validation'] = $this->validator;
                $contenido['controlador'] = $this->controlador;
                $contenido['id_tramite'] = $this->idTramite;
                $data['content'] = view($this->carpeta.'atender', $contenido);
                $data['menu_actual'] = $this->menuActual.'mis_tramites';
                $data['tramites_menu'] = $this->tramitesMenu();
                $data['alertas'] = $this->alertasTramites();
                $data['validacion_js'] = $this->carpeta.'atender.js';
                echo view('templates/template', $data);
            }else{
                $licenciaComercializacionHojaRutaModel = new LicenciaComercializacionHojaRutaModel();
                $licenciaComercializacionDerivacionModel = new LicenciaComercializacionDerivacionModel();
                $documentosModel = new DocumentosModel();
                $estado = 'DERIVADO';
                $dataHojaRuta = array(
                    'id' => $id,
                    'ultimo_estado' => $estado,
                    'ultimo_fk_estado_tramite_padre' => $this->request->getPost('fk_estado_tramite'),
                    'ultimo_fk_estado_tramite_hijo' => ((!empty($this->request->getPost('fk_estado_tramite_hijo'))) ? $this->request->getPost('fk_estado_tramite_hijo') : NULL),
                    'ultimo_fecha_actualizacion_estado' => ($this->request->getPost('fk_estado_tramite') != $this->request->getPost('ultimo_fk_estado_tramite_padre') ||  $this->request->getPost('fk_estado_tramite_hijo') != $this->request->getPost('ultimo_fk_estado_tramite_hijo'))? date('Y-m-d H:i:s'):$this->request->getPost('ultimo_fecha_actualizacion_estado'),
                    'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                    'ultimo_instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                    'ultimo_fk_usuario_remitente' => session()->get('registroUser'),
                    'ultimo_fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                    'ultimo_fk_usuario_responsable'=>($this->request->getPost('responsable') ? $this->request->getPost('fk_usuario_destinatario') : $this->request->getPost('ultimo_fk_usuario_responsable')),
                    'editar' => 'true',
                );
                if(count($documentos)>0){
                    $ultimo_fk_documentos = '';
                    foreach($documentos as $row)
                        $ultimo_fk_documentos .= $row['id'].',';
                    $dataHojaRuta['ultimo_fk_documentos'] = substr($ultimo_fk_documentos, 0, -1);
                }
                if(in_array(10, session()->get('registroPermisos'))){
                    $dataHojaRuta['estado_tramite_apm'] = $this->obtenerEstadoTramiteAPM($this->request->getPost('fk_estado_tramite'), $this->request->getPost('fk_estado_tramite_hijo'));
                    if(count($documentos)>0 || (isset($documentos_cargar) && count($documentos_cargar)>0)){
                        $resultado = '<ul class="list-group list-group-flush">';
                        if(count($documentos)>0)
                            foreach($documentos as $documento)
                                $resultado .= '<li class="list-group-item">'.$documento['tipo_documento'].'</li>';
                        if(isset($documentos_cargar) && count($documentos_cargar)>0)
                            foreach($documentos_cargar as $documento)
                                $resultado .= '<li class="list-group-item">'.$documento['tipo_documento'].'</li>';
                        $resultado .= '</ul>';
                        $dataHojaRuta['documentos_apm'] = $resultado;
                    }
                }
                if($licenciaComercializacionHojaRutaModel->save($dataHojaRuta) === false){
                    session()->setFlashdata('fail', $licenciaComercializacionHojaRutaModel->errors());
                }else{
                    /*if($correspondencia_externa && count($correspondencia_externa)>0){
                        $atendido = $this->request->getPost('atendido');
                        $observaciones_ce = $this->request->getPost('observaciones_ce');
                        foreach($correspondencia_externa as $n=>$correspondencia){
                            if(isset($atendido) && $atendido[$n]=='SI'){
                                $dataCorrespondenciaExterna = array(
                                    'id' => $correspondencia['id'],
                                    'estado' => 'ATENDIDO',
                                    'fk_usuario_atencion' => session()->get('registroUser'),
                                    'fecha_atencion' => date('Y-m-d H:i:s'),
                                    'observacion_atencion' => mb_strtoupper($observaciones_ce[$n]),
                                );
                                if($correspondenciaExternaModel->save($dataCorrespondenciaExterna) === false)
                                    session()->setFlashdata('fail', $correspondenciaExternaModel->errors());
                            }
                        }
                    }*/
                    $dataDerivacion = array(
                        'fk_hoja_ruta' => $id,
                        'fk_estado_tramite_padre' => $this->request->getPost('fk_estado_tramite'),
                        'fk_estado_tramite_hijo' => ((!empty($this->request->getPost('fk_estado_tramite_hijo'))) ? $this->request->getPost('fk_estado_tramite_hijo') : NULL),
                        'fecha_actualizacion_estado' => ($this->request->getPost('fk_estado_tramite') != $this->request->getPost('ultimo_fk_estado_tramite_padre') ||  $this->request->getPost('fk_estado_tramite_hijo') != $this->request->getPost('ultimo_fk_estado_tramite_hijo'))? date('Y-m-d H:i:s'):NULL,
                        'observaciones' => mb_strtoupper($this->request->getPost('observaciones')),
                        'fk_usuario_remitente' => session()->get('registroUser'),
                        'fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                        'fk_usuario_responsable'=>($this->request->getPost('responsable') ? $this->request->getPost('fk_usuario_destinatario') : $this->request->getPost('ultimo_fk_usuario_responsable')),
                        'instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                        'estado' => $estado,
                        'fk_usuario_creador' => session()->get('registroUser'),
                    );

                    if($licenciaComercializacionDerivacionModel->insert($dataDerivacion) === false){
                        session()->setFlashdata('fail', $licenciaComercializacionDerivacionModel->errors());
                    }else{
                        $id_derivacion = $licenciaComercializacionDerivacionModel->getInsertID();
                        if(count($documentos)>0){
                            $fecha_notificacion_anexar = $this->request->getPost('fecha_notificacion_anexar');
                            foreach($documentos as $i=>$documento){
                                $dataDocumento = array(
                                    'id' => $documento['id'],
                                    'estado' => 'ANEXADO',
                                    'fk_derivacion' => $id_derivacion,
                                    'fecha_notificacion'=> !empty($fecha_notificacion_anexar[$i]) ? $fecha_notificacion_anexar[$i] : NULL,
                                );
                                if(in_array(10, session()->get('registroPermisos'))){
                                    $docDigital = $this->request->getFile('documentos_anexar.'.$i);
                                    $nombreDocDigital = $docDigital->getRandomName();
                                    $docDigital->move($this->rutaDocumentos,$nombreDocDigital);
                                    $dataDocumento['doc_digital'] = $this->rutaDocumentos.$nombreDocDigital;
                                    $dataDocumento['fk_usuario_doc_digital'] = session()->get('registroUser');
                                }
                                if($documentosModel->save($dataDocumento) === false)
                                    session()->setFlashdata('fail', $documentosModel->errors());
                            }
                        }
                        if(isset($documentos_cargar) && count($documentos_cargar)>0){
                            foreach($documentos_cargar as $i=>$documento){
                                $docDigital = $this->request->getFile('documentos_cargar.'.$i);
                                $nombreDocDigital = $docDigital->getRandomName();
                                $docDigital->move($this->rutaDocumentos,$nombreDocDigital);
                                $dataDocumento = array(
                                    'id' => $documento['id'],
                                    'doc_digital' => $this->rutaDocumentos.$nombreDocDigital,
                                    'fk_usuario_doc_digital' => session()->get('registroUser'),
                                );
                                if($documentosModel->save($dataDocumento) === false)
                                    session()->setFlashdata('fail', $documentosModel->errors());
                            }
                        }
                        $dataDerivacionActualizacion = array(
                            'id' => $this->request->getPost('id_derivacion'),
                            'estado' => 'ATENDIDO',
                            'fecha_atencion' => date('Y-m-d H:i:s'),
                        );
                        if($licenciaComercializacionDerivacionModel->save($dataDerivacionActualizacion) === false)
                            session()->setFlashdata('fail', $licenciaComercializacionDerivacionModel->errors());
                        else
                            session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información.');
                    }
                    return redirect()->to($this->controlador.'mis_tramites');
                }
            }
        }
    }
    public function editar($id_hoja_ruta){
        $licenciaComercializacionHojaRutaModel = new LicenciaComercializacionHojaRutaModel();
        $where = array(
            'deleted_at' => NULL,
            'fk_usuario_actual' => session()->get('registroUser'),
            'id' => $id_hoja_ruta,
            'editar' => TRUE,
        );
        if($fila = $licenciaComercializacionHojaRutaModel->select("*, to_char(updated_at, 'YYYY-MM-DD') as fecha_actualizacion")->where($where)->first()){
            $licenciaComercializacionDerivacionModel = new LicenciaComercializacionDerivacionModel();
            $where = array(
                'fk_hoja_ruta' => $id_hoja_ruta,
            );
            $derivacion = $licenciaComercializacionDerivacionModel->where($where)->orderBy('id','DESC')->first();
            $cabera['titulo'] = $this->titulo;
            $cabera['subtitulo'] = 'Editar Derivación';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['correlativo'] = $fila['correlativo'];
            $contenido['informacion_tramite'] = $this->obtenerInformacionTramiteFormulario($id_hoja_ruta);
            $contenido['fila'] = $fila;
            $contenido['derivacion'] = $derivacion;
            if(in_array(10, session()->get('registroPermisos')))
                $contenido['documentos_cargar'] = $this->obtenerDocumentosCargar($id_hoja_ruta, $fila['fecha_actualizacion']);
            $contenido['documentos'] = $this->obtenerDocumentos($id_hoja_ruta, $derivacion['id']);
            $contenido['estadosTramites'] = $this->obtenerEstadosTramites($this->idTramite);
            $contenido['id_estado_padre'] = $derivacion['fk_estado_tramite_padre'];
            if($derivacion['fk_estado_tramite_hijo']){
                $contenido['estadosTramitesHijo'] = $this->obtenerEstadosTramitesHijo($derivacion['fk_estado_tramite_padre']);
                $contenido['id_estado_hijo'] = $derivacion['fk_estado_tramite_hijo'];
            }
            $contenido['usu_destinatario'] = $this->obtenerUsuario($derivacion['fk_usuario_destinatario']);
            $contenido['accion'] = $this->controlador.'guardar_editar';
            $contenido['controlador'] = $this->controlador;
            $contenido['id_tramite'] = $this->idTramite;
            $data['content'] = view($this->carpeta.'editar', $contenido);
            $data['menu_actual'] = $this->menuActual.'mis_tramites';
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['alertas'] = $this->alertasTramites();
            $data['validacion_js'] = $this->carpeta.'editar.js';
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador);
        }
    }
    public function guardarEditar(){
        if ($this->request->getPost()) {
            $id = $this->request->getPost('id_hoja_ruta');
            $id_derivacion = $this->request->getPost('id_derivacion');
            $documentos = $this->obtenerDocumentos($id, $id_derivacion);
            if(in_array(10, session()->get('registroPermisos')))
                $documentos_cargar = $this->obtenerDocumentosCargar($id, $this->request->getPost('fecha_actualizacion'));
            //$correspondencia_externa = $this->informacionHRExterna($id);
            $validation = $this->validate([
                'id_hoja_ruta' => [
                    'rules' => 'required',
                ],
                'id_derivacion' => [
                    'rules' => 'required',
                ],
                'fk_estado_tramite' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar un Estado de Tramite.'
                    ]
                ],
                'fk_usuario_destinatario' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar el Destinatario.'
                    ]
                ],
                'instruccion' => [
                    'rules' => 'required',
                ],
            ]);
            if(!$validation){
                $cabera['titulo'] = $this->titulo;
                $cabera['subtitulo'] = 'Editar Derivación';
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['informacion_tramite'] = $this->obtenerInformacionTramiteFormulario($id);
                $contenido['correlativo'] = $this->request->getPost('correlativo');
                $contenido['documentos'] = $documentos;
                if(isset($documentos_cargar) && in_array(10, session()->get('registroPermisos')))
                    $contenido['documentos_cargar'] = $documentos_cargar;
                $contenido['estadosTramites'] = $this->obtenerEstadosTramites($this->idTramite);
                $contenido['estadosTramitesHijo'] = $this->obtenerEstadosTramitesHijo($this->request->getPost('fk_estado_tramite'));
                $contenido['id_estado_padre'] = $this->request->getPost('fk_estado_tramite');
                $contenido['id_estado_hijo'] = $this->request->getPost('fk_estado_tramite_hijo');
                $contenido['usu_destinatario'] = $this->obtenerUsuario($this->request->getPost('fk_usuario_destinatario'));
                $contenido['accion'] = $this->controlador.'guardar_editar';
                $contenido['validation'] = $this->validator;
                $contenido['controlador'] = $this->controlador;
                $contenido['id_tramite'] = $this->idTramite;
                $data['content'] = view($this->carpeta.'editar', $contenido);
                $data['menu_actual'] = $this->menuActual.'mis_tramites';
                $data['tramites_menu'] = $this->tramitesMenu();
                $data['alertas'] = $this->alertasTramites();
                $data['validacion_js'] = $this->carpeta.'editar.js';
                echo view('templates/template', $data);
            }else{
                $licenciaComercializacionHojaRutaModel = new LicenciaComercializacionHojaRutaModel();
                $licenciaComercializacionDerivacionModel = new LicenciaComercializacionDerivacionModel();
                $documentosModel = new DocumentosModel();
                $dataHojaRuta = array(
                    'id' => $id,
                    'ultimo_fk_estado_tramite_padre' => $this->request->getPost('fk_estado_tramite'),
                    'ultimo_fk_estado_tramite_hijo' => ((!empty($this->request->getPost('fk_estado_tramite_hijo'))) ? $this->request->getPost('fk_estado_tramite_hijo') : NULL),
                    'ultimo_fecha_actualizacion_estado' => ($this->request->getPost('fk_estado_tramite') != $this->request->getPost('ultimo_fk_estado_tramite_padre') ||  $this->request->getPost('fk_estado_tramite_hijo') != $this->request->getPost('ultimo_fk_estado_tramite_hijo'))? date('Y-m-d H:i:s'):$this->request->getPost('ultimo_fecha_actualizacion_estado'),
                    'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                    'ultimo_instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                    'ultimo_fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                    'ultimo_fk_usuario_responsable'=>($this->request->getPost('responsable') ? $this->request->getPost('fk_usuario_destinatario') : $this->request->getPost('ultimo_fk_usuario_responsable')),
                    'editar' => 'false',
                );
                if(count($documentos)>0){
                    $ultimo_fk_documentos = '';
                    foreach($documentos as $row)
                        $ultimo_fk_documentos .= $row['id'].',';
                    $dataHojaRuta['ultimo_fk_documentos'] = substr($ultimo_fk_documentos, 0, -1);
                }
                if(in_array(10, session()->get('registroPermisos'))){
                    $dataHojaRuta['estado_tramite_apm'] = $this->obtenerEstadoTramiteAPM($this->request->getPost('fk_estado_tramite'), $this->request->getPost('fk_estado_tramite_hijo'));
                    if(count($documentos)>0 || (isset($documentos_cargar) && count($documentos_cargar)>0)){
                        $resultado = '<ul class="list-group list-group-flush">';
                        if(count($documentos)>0)
                            foreach($documentos as $documento)
                                $resultado .= '<li class="list-group-item">'.$documento['tipo_documento'].'</li>';
                        if(isset($documentos_cargar) && count($documentos_cargar)>0)
                            foreach($documentos_cargar as $documento)
                                $resultado .= '<li class="list-group-item">'.$documento['tipo_documento'].'</li>';
                        $resultado .= '</ul>';
                        $dataHojaRuta['documentos_apm'] = $resultado;
                    }
                }
                if($licenciaComercializacionHojaRutaModel->save($dataHojaRuta) === false){
                    session()->setFlashdata('fail', $licenciaComercializacionHojaRutaModel->errors());
                }else{
                    $dataDerivacion = array(
                        'id' => $id_derivacion,
                        'fk_estado_tramite_padre' => $this->request->getPost('fk_estado_tramite'),
                        'fk_estado_tramite_hijo' => ((!empty($this->request->getPost('fk_estado_tramite_hijo'))) ? $this->request->getPost('fk_estado_tramite_hijo') : NULL),
                        'fecha_actualizacion_estado' => ($this->request->getPost('fk_estado_tramite') != $this->request->getPost('ultimo_fk_estado_tramite_padre') ||  $this->request->getPost('fk_estado_tramite_hijo') != $this->request->getPost('ultimo_fk_estado_tramite_hijo'))? date('Y-m-d H:i:s'):NULL,
                        'observaciones' => mb_strtoupper($this->request->getPost('observaciones')),
                        'fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                        'fk_usuario_responsable'=>($this->request->getPost('responsable') ? $this->request->getPost('fk_usuario_destinatario') : $this->request->getPost('ultimo_fk_usuario_responsable')),
                        'instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                    );
                    if($licenciaComercializacionDerivacionModel->save($dataDerivacion) === false){
                        session()->setFlashdata('fail', $licenciaComercializacionDerivacionModel->errors());
                    }else{
                        if(count($documentos)>0){
                            $fecha_notificacion_anexar = $this->request->getPost('fecha_notificacion_anexar');
                            foreach($documentos as $i=>$documento){
                                $dataDocumento = array(
                                    'id' => $documento['id'],
                                    'fecha_notificacion'=> !empty($fecha_notificacion_anexar[$i]) ? $fecha_notificacion_anexar[$i] : NULL,
                                );
                                if(in_array(10, session()->get('registroPermisos'))){
                                    $docDigital = $this->request->getFile('documentos_anexar.'.$i);
                                    if(!empty($docDigital) && $docDigital->getSize()>0){
                                        if(file_exists($documento['doc_digital']))
                                            @unlink($documento['doc_digital']);
                                        $nombreDocDigital = $docDigital->getRandomName();
                                        $docDigital->move($this->rutaDocumentos,$nombreDocDigital);
                                        $dataDocumento['doc_digital'] = $this->rutaDocumentos.$nombreDocDigital;
                                    }
                                }
                                if($documentosModel->save($dataDocumento) === false)
                                    session()->setFlashdata('fail', $documentosModel->errors());
                            }
                        }
                        if(isset($documentos_cargar) && count($documentos_cargar)>0){
                            foreach($documentos_cargar as $i=>$documento){
                                $docDigital = $this->request->getFile('documentos_cargar.'.$i);
                                if(!empty($docDigital) && $docDigital->getSize()>0){
                                    if(file_exists($documento['doc_digital']))
                                        @unlink($documento['doc_digital']);
                                    $nombreDocDigital = $docDigital->getRandomName();
                                    $docDigital->move($this->rutaDocumentos,$nombreDocDigital);
                                    $dataDocumento = array(
                                        'id' => $documento['id'],
                                        'doc_digital' => $this->rutaDocumentos.$nombreDocDigital,
                                    );
                                    if($documentosModel->save($dataDocumento) === false)
                                        session()->setFlashdata('fail', $documentosModel->errors());
                                }
                            }
                        }
                        session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información.');
                    }
                    return redirect()->to($this->controlador.'mis_tramites');
                }
            }
        }
    }

    public function hojaRutaPdf($id_hoja_ruta){
        $licenciaComercializacionHojaRutaModel = new LicenciaComercializacionHojaRutaModel();
        $campos = array(
            'id','fk_oficina','fk_usuario_creador','fk_usuario_destino',"to_char(fecha_hoja_ruta, 'DD/MM/YYYY HH24:MI') as fecha_hoja_ruta","correlativo",
        );
        if($hoja_ruta = $licenciaComercializacionHojaRutaModel->select($campos)->find($id_hoja_ruta)){
            $oficinaModel = new OficinasModel();
            $personaExternaModel = new PersonaExternaModel();
            $licenciaComercializacionDocumentoExternoModel = new LicenciaComercializacionDocumentoExternoModel();
            $oficina = $oficinaModel->find($hoja_ruta['fk_oficina']);
            $documentoExterno = $licenciaComercializacionDocumentoExternoModel->find($hoja_ruta['id']);
            $personaExterna = $personaExternaModel->find($documentoExterno['fk_persona_externa']);
            $usuarioCreador = $this->obtenerUsuario($hoja_ruta['fk_usuario_creador']);
            $usuarioDestino = $this->obtenerUsuario($hoja_ruta['fk_usuario_destino']);

            $file_name = str_replace('/','-',$hoja_ruta['correlativo']).'.pdf';
            $pdf = new HojaRutaPdf('P', 'mm', array(216, 279), true, 'UTF-8', false);

            //
            $pdf->SetCreator('GARNET');
            $pdf->SetAuthor('Desarrollo de UTIC');
            $pdf->SetTitle('Hoja de Ruta de Licencia de Comercialización');
            $pdf->SetKeywords('Hoja, Ruta, Licencia, Comercialización');

            //establecer margenes
            $pdf->SetMargins(10, 8, 12);
            $pdf->SetAutoPageBreak(true, 8); //Margin botton
            $pdf->setFontSubsetting(false);

            $pdf->AddPage();
            $pdf->SetTextColor(0, 0, 0);
            $pdf->setCellPaddings(0, 1, 0, 0);
            $pdf->setCellMargins(0, 0, 0, 0);
            $pdf->SetFillColor(255, 255, 255);

            $pdf->setCellPadding(1);
            $pdf->Image('assets/images/hoja_ruta/logo_ajam.png', 11, 11, 36, 0);

            $pdf->MultiCell(38, 21, "", 1, 'C', 0, 0);

            $pdf->SetFont($this->fontPDF, 'B', 8);
            $pdf->MultiCell(106,5, 'AUTORIDAD JURISDICCIONAL ADMINISTRATIVA MINERA', 'TRL', 'C', true, 0);
            $pdf->MultiCell(50,5, 'FECHA Y HORA DE CREACIÓN', 1, 'C', true, 1);
            $pdf->setx(48);
            $pdf->SetFont($this->fontPDF, '', 10);
            $pdf->MultiCell(106,5, 'LICENCIA DE COMERCIALIZACIÓN', 'RL', 'C', true, 0, '', '', true, 0, false, false, 5, 'M');
            /*
            *  CITE DEL DOCUMENTO
            */
            $pdf->MultiCell(50,5, $hoja_ruta['fecha_hoja_ruta'], 1, 'C', false, 1, '', '', true, 0, false, false, 5, 'M', true);
            /*
            *  FIN CITE DEL DOCUMENTO
            */
            $pdf->setx(48);
            $pdf->SetFont($this->fontPDF, 'B', 14);
            $pdf->MultiCell(106,10,  $hoja_ruta['correlativo'] , 'LBR', 'C', true, 0, '', '', true, 0, false, false, 10, 'M');
            $pdf->SetFont($this->fontPDF, 'B', 8);
            $pdf->MultiCell(50,5, 'N° FOJAS', 1, 'C', true, 1, '', '', true, 0, false, false, 5, 'M', true);
            $pdf->SetFont($this->fontPDF, '', 8);
            /*
            * FIN FECHA CITE
            */
            $pdf->SetFont($this->fontPDF, '', 8);
            $pdf->setx(154);
            $pdf->MultiCell(50,5, $documentoExterno['fojas'], 1, 'C', false, 1, '', '', true, 0, false, false, 5, 'M', true);

            $pdf->SetFont($this->fontPDF, 'B', 8);
            $pdf->MultiCell(50, 5, "DIRECCIÓN:", 1, 'R', true, 0);
            $pdf->SetFont($this->fontPDF, '', 8);
            $pdf->MultiCell(144,5, $oficina['nombre'], 1, 'L', false, 1);
            $pdf->SetFont($this->fontPDF, 'B', 8);
            $pdf->MultiCell(50, 5, "DOCUMENTO EXTERNO:", 1, 'R', true, 0);
            $pdf->SetFont($this->fontPDF, '', 8);
            $pdf->MultiCell(86,5, $documentoExterno['cite'], 1, 'L', false, 0);
            $pdf->SetFont($this->fontPDF, 'B', 8);
            $pdf->MultiCell(20, 5, "FECHA:", 1, 'R', false, 0);
            $pdf->SetFont($this->fontPDF, '', 8);
            $pdf->MultiCell(38,5, $documentoExterno['fecha_cite'], 1, 'L', true, 1);

            $pdf->SetFont($this->fontPDF, 'B', 8);
            $pdf->MultiCell(50, 5, "PROCEDENCIA:", 1, 'R', true, 0);
            $pdf->SetFont($this->fontPDF, '', 8);
            $pdf->MultiCell(144,5, $personaExterna['institucion'], 1, 'L', false, 1);
            $pdf->SetFont($this->fontPDF, 'B', 8);
            $pdf->MultiCell(50, 5, "REMITENTE:", 1, 'R', true, 0);
            $pdf->SetFont($this->fontPDF, '', 8);
            $pdf->MultiCell(144,5, $personaExterna['nombres'].' '.$personaExterna['apellidos'], 1, 'L', false, 1);
            $pdf->SetFont($this->fontPDF, 'B', 8);
            $pdf->MultiCell(50, 5, "CARGO REMITENTE:", 1, 'R', true, 0);
            $pdf->SetFont($this->fontPDF, '', 8);
            $pdf->MultiCell(144,5, $personaExterna['cargo'], 1, 'L', false, 1);
            $pdf->SetFont($this->fontPDF, 'B', 8);
            $pdf->MultiCell(50, 5, "REFERENCIA:", 1, 'R', true, 0);
            $pdf->SetFont($this->fontPDF, '', 8);
            $pdf->MultiCell(144,5, $documentoExterno['referencia'], 1, 'L', false, 1);

            $pdf->SetFont($this->fontPDF, 'B', 8);
            $pdf->MultiCell(50, 5, "USUARIO DESTINO:", 1, 'R', true, 0);
            $pdf->SetFont($this->fontPDF, '', 8);
            $pdf->MultiCell(144,5, $usuarioDestino['nombre'], 1, 'L', false, 1);
            $pdf->SetFont($this->fontPDF, 'B', 8);
            $pdf->MultiCell(50, 5, "USUARIO CREADOR:", 1, 'R', true, 0);
            $pdf->SetFont($this->fontPDF, '', 8);
            $pdf->MultiCell(144,5, $usuarioCreador['nombre'], 1, 'L', false, 1);

            $this->crearDerivacion($pdf, $this->fontPDF, $this->acciones);
            $this->crearDerivacion($pdf, $this->fontPDF, $this->acciones);
            $this->crearDerivacion($pdf, $this->fontPDF, $this->acciones);

            $pdf->AddPage();

            $pdf->SetFont($this->fontPDF, 'B', 8);
            $pdf->MultiCell(55, 8, 'HOJA DE RUTA:', 1, 'R', true, 0, '', '', true, 0, false, false, 5, 'M', true);
            $pdf->MultiCell(139, 8, '', 1, 'L', false, 1, '', '', true, 0, false, false, 5, 'T', true);

            $this->crearDerivacion($pdf, $this->fontPDF, $this->acciones);
            $this->crearDerivacion($pdf, $this->fontPDF, $this->acciones);
            $this->crearDerivacion($pdf, $this->fontPDF, $this->acciones);
            $this->crearDerivacion($pdf, $this->fontPDF, $this->acciones);

            $pdf->setPage(1, true);

            $pdf->Output($file_name);
            exit();
        }else{
            session()->setFlashdata('fail', 'No se ha encontrado la hoja de rut');
            return redirect()->to($this->controlador.'mis_tramites');
        }
    }

    private function crearDerivacion(&$pdf, $tipo_letra, $acciones) {
        $pdf->setCellPadding(1);
        $pdf->ln(1);
        // FILA 1
        $pdf->SetFont($tipo_letra, 'B', 7);
        $pdf->MultiCell(55, 0, 'ACCIÓN', 1, 'C', true, 0);
        $pdf->MultiCell(40, 0, 'DESTINATARIO:', 1, 'R', true, 0);
        $pdf->MultiCell(99, 0, '', 1, 'C', false, 1);

        $count = 0;
        foreach($acciones as $accion) {
            //(w, h, txt, border = 0, align = 'J', fill = 0, ln = 1, x = '', y = '', reseth = true, stretch = 0, ishtml = false, autopadding = true, maxh = 0)
            $pdf->MultiCell(50, 0, $accion, 1, 'L', true, 0);
            $pdf->MultiCell(5, 0, ' ', 1, 'L', false, 0);
            $pdf->MultiCell(68, 0, ' ', 'R', 'L', false, 0);
            if(++$count == count($acciones) - 1) {
                $pdf->MultiCell(31, 0, 'SELLO Y FIRMA', 1, 'C', true, 0);
                $pdf->MultiCell(20, 0, 'FECHA', 1, 'C', true, 0);
                $pdf->MultiCell(20, 0, 'HORA', 1, 'C', true, 1);
            }
            else if($count == count($acciones)) {
                $pdf->MultiCell(31, 0, '', 'L', 'C', true, 0);
                $pdf->MultiCell(20, 0, '', 1, 'C', false, 0);
                $pdf->MultiCell(20, 0, '', 1, 'C', false, 1);
            }
            else {
                $pdf->MultiCell(71, 0, '', 'R', 'C', false, 1);
            }
        }
        $pdf->MultiCell(55, 0, 'COORDINAR CON:', 1, 'R', true, 0);
        $pdf->MultiCell(43, 0, '', 1, 'L', false, 0);
        $pdf->MultiCell(46, 0, 'CON COPIA A:', 1, 'R', true, 0);
        $pdf->MultiCell(50, 0, '', 1, 'C', false, 1);
    }

    public function ajaxAreaMinera(){
        $cadena = mb_strtoupper($this->request->getPost('texto'));
        if(!empty($cadena) && session()->get('registroUser')){
            $data = array();
            $dbSincobol = \Config\Database::connect('sincobol');
            $campos = array('id_area_minera', "CONCAT(codigo_unico,' - ',denominacion,' (',titular,' - ',tipo_actor,')') AS nombre");
            $where = array(
                'vigente' => true,
                'vencido' => '',
            );
            $builder = $dbSincobol->table('siremi.reporte_general_lpe')
            ->select($campos)
            ->where($where)
            ->like("CONCAT(codigo_unico,' - ',denominacion)", $cadena)
            ->whereIn('estado', array('INSCRITO', 'FINALIZADO', 'HISTORICO'))
            ->orderBy('id_area_minera','ASC')
            ->limit(20);
            $datos = $builder->get()->getResultArray();
            if($datos){
                foreach($datos as $row){
                    $data[] = array(
                        'id' => $row['id_area_minera'],
                        'text' => $row['nombre'],
                    );
                }
            }else{
                $data[] = array(
                    'id' => 0,
                    'text' => 'No se encuentra el area minera que busca'
                );
            }
            echo json_encode($data);
        }
    }
    public function ajaxAnalistaDestinatario(){
        $cadena = mb_strtoupper($this->request->getPost('texto'));
        if(!empty($cadena)){
            $oficinaModel = new OficinasModel();
            $oficina = $oficinaModel->find(session()->get('registroOficina'));
            if($oficina['fk_oficina_derivacion'])
                $oficinas = explode(',',$oficina['fk_oficina_derivacion']);
            $oficinas[] = $oficina['id'];
            $data = array();
            $db = \Config\Database::connect();
            $campos = array(
                'u.id', "CONCAT(u.nombre_completo, ' (',p.nombre,' - ',o.nombre,')') as nombre"
            );
            $where = array(
                'u.deleted_at' => NULL,
                'u.activo' => true,
                'u.derivacion' => true,
            );
            $builder = $db->table('usuarios as u')
            ->select($campos)
            ->join('oficinas as o', 'u.fk_oficina = o.id', 'left')
            ->join('perfiles as p', 'u.fk_perfil = p.id', 'left')
            ->where($where)
            ->whereIn('u.fk_oficina', $oficinas)
            ->like("u.tramites", $this->idTramite)
            ->like("CONCAT(u.nombre_completo, ' (',p.nombre,' - ',o.nombre,')')", $cadena)
            ->orderBy('u.id','DESC')
            ->limit(20);
            $datos = $builder->get()->getResultArray();
            if($datos){
                foreach($datos as $row){
                    $data[] = array(
                        'id' => $row['id'],
                        'text' => $row['nombre'],
                    );
                }
            }else{
                $data[] = array(
                    'id' => '',
                    'text' => 'No se encuentra al Analista.'
                );
            }
            echo json_encode($data);
        }
    }
    public function ajaxGuardarDevolver(){
        $licenciaComercializacionHojaRutaModel = new LicenciaComercializacionHojaRutaModel();
        $resultado = array(
            'error' => 'No se guardo la información',
        );
        $where = array(
            'id' => $this->request->getPost('id'),
            'ultimo_fk_usuario_destinatario' => session()->get('registroUser'),
            'deleted_at' => NULL,
        );
        if($fila = $licenciaComercializacionHojaRutaModel->where($where)->first()){
            $licenciaComercializacionDerivacionModel = new LicenciaComercializacionDerivacionModel();
            $documentosModel = new DocumentosModel();
            $estado = 'DEVUELTO';
            $motivo_devolucion = mb_strtoupper($this->request->getPost('motivo_devolucion'));
            $where = array(
                'deleted_at' => NULL,
                'fk_hoja_ruta' => $fila['id'],
            );
            $derivaciones = $licenciaComercializacionDerivacionModel->where($where)->orderBy('id', 'DESC')->findAll(2);
            $derivacion_actual = $derivaciones[0];
            $derivacion_restaurar = $derivaciones[1];
            $where = array(
                'fk_derivacion' => $derivacion_actual['id'],
                'fk_hoja_ruta' => $fila['id'],
                'fk_tramite' => $this->idTramite,
            );
            $documentos_anexados = $documentosModel->where($where)->findAll();
            $dataHR = array(
                'id' => $fila['id'],
                'ultimo_estado' => $estado,
                'ultimo_fk_estado_tramite_padre' => $derivacion_restaurar['fk_estado_tramite_padre'],
                'ultimo_fk_estado_tramite_hijo' => $derivacion_restaurar['fk_estado_tramite_hijo'],
                'ultimo_fecha_actualizacion_estado' => ($derivacion_restaurar['fecha_actualizacion_estado']?$derivacion_restaurar['fecha_actualizacion_estado']:$fila['ultimo_fecha_actualizacion_estado']),
                'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                'ultimo_instruccion' => $motivo_devolucion,
                'ultimo_fk_usuario_remitente' => session()->get('registroUser'),
                'ultimo_fk_usuario_destinatario' => $derivacion_restaurar['fk_usuario_destinatario'],
                'ultimo_fk_usuario_responsable' => $derivacion_restaurar['fk_usuario_responsable'],
                'ultimo_fk_documentos' => (count($documentos_anexados)>0?'':$fila['ultimo_fk_documentos'])
            );
            if($licenciaComercializacionHojaRutaModel->save($dataHR) === false){
                session()->setFlashdata('fail', $licenciaComercializacionHojaRutaModel->errors());
            }else{
                $dataDerivacion = array(
                    'fk_hoja_ruta' => $fila['id'],
                    'fk_estado_tramite_padre' => $derivacion_restaurar['fk_estado_tramite_padre'],
                    'fk_estado_tramite_hijo' => $derivacion_restaurar['fk_estado_tramite_hijo'],
                    'fecha_actualizacion_estado' => ($derivacion_restaurar['fecha_actualizacion_estado']?$derivacion_restaurar['fecha_actualizacion_estado']:NULL),
                    'observaciones' => $derivacion_restaurar['observaciones'],
                    'fk_usuario_remitente' => session()->get('registroUser'),
                    'fk_usuario_destinatario' => $derivacion_restaurar['fk_usuario_destinatario'],
                    'fk_usuario_responsable' => $derivacion_restaurar['fk_usuario_responsable'],
                    'instruccion' => $motivo_devolucion,
                    'estado' => $estado,
                    'fk_usuario_creador' => session()->get('registroUser'),
                );
                if($licenciaComercializacionDerivacionModel->insert($dataDerivacion) === false){
                    session()->setFlashdata('fail', $licenciaComercializacionDerivacionModel->errors());
                }else{
                    if($documentos_anexados && count($documentos_anexados)>0){
                        foreach($documentos_anexados as $documento){
                            $dataDocumento = array(
                                'id' => $documento['id'],
                                'estado' => 'SUELTO',
                                'fk_derivacion' => NULL,
                            );
                            if($documentosModel->save($dataDocumento) === false)
                                session()->setFlashdata('fail', $documentosModel->errors());
                        }
                    }
                    $dataDerivacionActualizacion = array(
                        'id' => $derivacion_actual['id'],
                        'estado' => 'ATENDIDO',
                        'fecha_devolucion' => date('Y-m-d H:i:s'),
                    );
                    if($licenciaComercializacionDerivacionModel->save($dataDerivacionActualizacion) === false)
                        session()->setFlashdata('fail', $licenciaComercializacionDerivacionModel->errors());
                    $resultado = array(
                        'idtra' => $fila['id']
                    );
                }
            }
        }
        echo json_encode($resultado);
    }

    private function obtenerCorrelativo($sigla){
        $licenciaComercializacionCorrelativosModel = new LicenciaComercializacionCorrelativosModel();
        $correlativo = '';
        $where = array(
            'gestion' => date('Y'),
            'sigla' => $sigla,
        );

        if($correlativoActual = $licenciaComercializacionCorrelativosModel->where($where)->first())
            $correlativo = $sigla.($correlativoActual['correlativo_actual']+1).'/'.date('Y');
        else
            $correlativo = $sigla.'1'.'/'.date('Y');

        return $correlativo;
    }
    private function actualizarCorrelativo($sigla){
        $licenciaComercializacionCorrelativosModel = new LicenciaComercializacionCorrelativosModel();
        $where = array(
            'gestion' => date('Y'),
            'sigla' => $sigla,
        );

        if($dataCorrelativo = $licenciaComercializacionCorrelativosModel->where($where)->first())
            $dataCorrelativo['correlativo_actual'] +=1;
        else
            $dataCorrelativo = array_merge(array('correlativo_actual' => 1), $where);

        if($licenciaComercializacionCorrelativosModel->save($dataCorrelativo) === false)
            return $licenciaComercializacionCorrelativosModel->errors();

        return true;
    }
    private function obtenerUsuario($id){
        $db = \Config\Database::connect();
        $campos = array(
            'u.id', "CONCAT(u.nombre_completo, ' (',p.nombre,')') as nombre"
        );
        $where = array(
            'u.id' => $id,
        );
        $builder = $db->table('usuarios as u')
        ->select($campos)
        ->join('oficinas as o', 'u.fk_oficina = o.id', 'left')
        ->join('perfiles as p', 'u.fk_perfil = p.id', 'left')
        ->where($where);
        $datos = $builder->get()->getRowArray();
        return $datos;
    }
    private function obtenerDirectorDepartamentalRegional($id_oficina){
        $db = \Config\Database::connect();
        $campos = array("u.id", "u.nombre_completo", "p.nombre as cargo");
        $where = array(
            'u.activo ' => TRUE,
            'u.fk_oficina' => $id_oficina,
        );
        $builder = $db->table('public.usuarios as u')
        ->select($campos)
        ->join('public.perfiles as p', 'u.fk_perfil = p.id', 'left')
        ->where($where)
        ->like("p.nombre","DIRECTOR(A)%")
        ->orderBY('u.id', 'DESC');
        if($director = $builder->get()->getRowArray())
            return $director['id'];
        else
            return 0;
    }
    private function obtenerEstadoTramiteAPM($id_estado_padre, $id_estado_hijo = ''){
        $estadoTramiteModel = new EstadoTramiteModel();
        $estado = '';
        if($temp = $estadoTramiteModel->find($id_estado_padre))
            $estado .= $temp['nombre'];
        if($id_estado_hijo){
            if($temp = $estadoTramiteModel->find($id_estado_hijo))
                $estado .= ' - '.$temp['nombre'];
        }
        return $estado;
    }
    private function obtenerPersonaExternaSelect($id_persona_externa){
        $personaExternaModel = new PersonaExternaModel();
        $campos = array('id', "CONCAT(documento_identidad, ' ', expedido, ' - ', nombres, ' ', apellidos, ' (', institucion, ' - ',cargo,')') as nombre");
        return $personaExternaModel->select($campos)->find($id_persona_externa);
    }
    public function obtenerEstadosTramites($idTramite){
        $db = \Config\Database::connect();
        $builder = $db->table('public.estado_tramite')
        ->select('*')
        ->where('deleted_at IS NULL AND fk_estado_padre IS NULL AND fk_tramite = '.$idTramite)
        ->orderBy('orden');
        $estadosTramites = $builder->get()->getResult('array');
        $temporal = array();
        $temporal[] = array(
            'id' => '',
            'texto' => 'SELECCIONE UNA OPCIÓN',
            'padre' => 'f',
            'anexar' => '',
        );
        foreach($estadosTramites as $row)
            $temporal[] = array(
                'id' => $row['id'],
                'orden' => $row['orden'],
                'texto' => $row['orden'].'. '.$row['nombre'],
                'padre' => $row['padre'],
                'anexar' => $row['anexar_documentos'],
            );
        return $temporal;
    }
    public function obtenerEstadosTramitesHijo($idCategoria){
        if($idCategoria){
            $estadoTramiteModel = new EstadoTramiteModel();
            $categoria = $estadoTramiteModel->find($idCategoria);
            $db = \Config\Database::connect();
            $builder = $db->table('public.estado_tramite')
            ->where('deleted_at IS NULL AND fk_estado_padre = '.$idCategoria)
            ->orderBy('orden');
            $datos = $builder->get()->getResult('array');
            $temporal = array();
            foreach($datos as $row)
                $temporal[] = array(
                    'id' => $row['id'],
                    'texto' => $categoria['orden'].'.'.$row['orden'].'. '.$row['nombre'],
                    'anexar_documentos' => $row['anexar_documentos'],
                );
            return $temporal;
        }
    }
    private function obtenerInformacionTramiteFormulario($id_hoja_ruta){
        $db = \Config\Database::connect();
        $html = '';
        $campos = array(
            'hr.id','hr.correlativo',"to_char(hr.fecha_hoja_ruta, 'DD/MM/YYYY') as fecha_hoja_ruta","dex.cite","to_char(dex.fecha_cite, 'DD/MM/YYYY') as fecha_cite",
            "CONCAT(pex.nombres, ' ', pex.apellidos, ' (', pex.institucion, ' - ',pex.cargo,')') as remitente", 'dex.referencia', "dex.doc_digital",
            'dex.fojas', "dex.adjuntos","CONCAT(ures.nombre_completo,'<br><b>',pres.nombre,'<b>') as responsable",
            "CASE WHEN hr.ultimo_fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'. ',etp.nombre,'<br>',etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE CONCAT(etp.orden,'. ',etp.nombre) END as estado_tramite",
        );
        $where = array(
            'hr.deleted_at' => NULL,
            'hr.id' => $id_hoja_ruta
        );
        $builder = $db->table('licencia_comercializacion.hoja_ruta as hr')
        ->select($campos)
        ->join('licencia_comercializacion.documento_externo as dex', 'hr.id = dex.fk_hoja_ruta', 'left')
        ->join('public.persona_externa as pex', 'dex.fk_persona_externa = pex.id', 'left')
        ->join('usuarios as ures', 'hr.ultimo_fk_usuario_responsable = ures.id', 'left')
        ->join('perfiles as pres', 'ures.fk_perfil = pres.id', 'left')
        ->join('estado_tramite as etp', 'hr.ultimo_fk_estado_tramite_padre = etp.id', 'left')
        ->join('estado_tramite as eth', 'hr.ultimo_fk_estado_tramite_hijo = eth.id', 'left')
        ->where($where);
        if($fila = $builder->get()->getRowArray()){
            $contenido['fila'] = $fila;
            $html = view($this->carpeta.'informacion_tramite', $contenido);
        }
        return $html;
    }
    private function obtenerDocumentos($id_hoja_ruta, $id_derivacion=''){
        $db = \Config\Database::connect();
        $campos = array('doc.id','doc.correlativo',"TO_CHAR(doc.fecha, 'DD/MM/YYYY') as fecha",'td.nombre as tipo_documento','td.notificacion','doc.doc_digital','doc.fecha_notificacion');
        if($id_derivacion)
            $where = array(
                'doc.fk_usuario_creador' => session()->get('registroUser'),
                'doc.fk_derivacion' => $id_derivacion,
                'doc.fk_hoja_ruta' => $id_hoja_ruta,
                'doc.fk_tramite' => $this->idTramite,
            );
        else
            $where = array(
                'doc.fk_usuario_creador' => session()->get('registroUser'),
                'doc.fk_derivacion' => NULL,
                'doc.estado' => 'SUELTO',
                'doc.fk_hoja_ruta' => $id_hoja_ruta,
                'doc.fk_tramite' => $this->idTramite,
            );
        $builder = $db->table('public.documentos AS doc')
        ->select($campos)
        ->join('public.tipo_documento AS td', 'doc.fk_tipo_documento = td.id', 'left')
        ->where($where)
        ->orderBY('doc.id', 'ASC');
        return $builder->get()->getResultArray();
    }
    private function obtenerDocumentosCargar($id_hoja_ruta, $fecha_actualizacion=''){
        $db = \Config\Database::connect();
        $campos = array('doc.id', 'doc.correlativo', "to_char(doc.fecha, 'DD/MM/YYYY') as fecha", 'tdoc.nombre as tipo_documento', "CONCAT(u.nombre_completo,'<br><b>',p.nombre,'</b>') as usuario",'doc.doc_digital');
        if($fecha_actualizacion)
            $where = array(
                'doc.estado' => 'ANEXADO',
                'doc.fk_hoja_ruta' => $id_hoja_ruta,
                'doc.fk_tramite' => $this->idTramite,
                'doc.fk_usuario_doc_digital' => session()->get('registroUser'),
                'doc.fk_usuario_creador <>' => session()->get('registroUser'),
                'doc.doc_digital <>' => NULL,
                'DATE(doc.updated_at)' => $fecha_actualizacion,
            );
        else
            $where = array(
                'doc.estado' => 'ANEXADO',
                'doc.fk_hoja_ruta' => $id_hoja_ruta,
                'doc.fk_tramite' => $this->idTramite,
                'doc.doc_digital' => NULL,
            );
        $builder = $db->table('public.documentos AS doc')
        ->select($campos)
        ->join('public.tipo_documento AS tdoc', 'doc.fk_tipo_documento = tdoc.id', 'left')
        ->join('public.usuarios AS u', 'doc.fk_usuario_creador = u.id', 'left')
        ->join('public.perfiles AS p', 'u.fk_perfil = p.id', 'left')
        ->where($where)
        ->orderBY('doc.id', 'ASC');
        return $builder->get()->getResultArray();
    }
    private function obtenerUltimosDocumentos($datos){
        if($datos){
            $documentosModel = new DocumentosModel();
            foreach($datos as $i=>$row){
                $correlativos = '';
                if($row['ultimo_fk_documentos']){
                    $documentos = explode(',', $row['ultimo_fk_documentos']);
                    if($result = $documentosModel->whereIn('id', $documentos)->findAll()){
                        foreach($result as $doc){
                            if($doc['doc_digital'])
                                $correlativos .= "<a href='".base_url($doc['doc_digital'])."' target='_blank' title='Ver Documento'>".$doc['correlativo']."</a><br>";
                            else
                                $correlativos .= $doc['correlativo'].'<br>';
                        }
                    }
                }
                $datos[$i]['ultimos_documentos'] = $correlativos;
            }
        }
        return $datos;
    }



    private function obtenerUsuarioHR($id){
        $db = \Config\Database::connect();
        $campos = array(
            'u.id', 'u.nombre_completo as usuario', 'p.nombre as cargo', 'o.nombre as oficina'
        );
        $where = array(
            'u.id' => $id,
        );
        $builder = $db->table('usuarios as u')
        ->select($campos)
        ->join('oficinas as o', 'u.fk_oficina = o.id', 'left')
        ->join('perfiles as p', 'u.fk_perfil = p.id', 'left')
        ->where($where);
        $datos = $builder->get()->getRowArray();
        return $datos;
    }

}