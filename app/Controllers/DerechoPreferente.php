<?php

namespace App\Controllers;

use App\Libraries\HojaRutaPdf;
use App\Models\CorrelativosDerechoPreferenteModel;
use App\Models\DocumentosModel;
use App\Models\OficinasModel;
use App\Models\PersonaExternaModel;
use App\Models\SolicitudDatosAreaMineraModel;
use App\Models\SolicitudDerechoPreferenteModel;
use App\Models\SolicitudDerivacionModel;
use App\Models\SolicitudDocumentoExternoModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class DerechoPreferente extends BaseController
{
    protected $titulo = 'Solicitud CAM - Derecho Preferente';
    protected $controlador = 'derecho_preferente/';
    protected $carpeta = 'derecho_preferente/';
    protected $idTramite = 4;
    protected $menuActual = 'derecho_preferente/';
    protected $rutaArchivos = 'archivos/derecho_preferente/';
    protected $rutaDocumentos = 'archivos/derecho_preferente/documentos/';
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
        $campos = array(
            "sdp.id", "sdp.ultimo_estado", "to_char(sdp.ultimo_fecha_derivacion, 'DD/MM/YYYY') as fecha_derivacion", "(CURRENT_DATE - sdp.ultimo_fecha_derivacion::date) as dias", "sdp.correlativo",
            "CONCAT(urem.nombre_completo,'<br><b>',prem.nombre,'<b>') as remitente", "CONCAT(udes.nombre_completo,'<br><b>',pdes.nombre,'<b>') as destinatario", "sdp.ultimo_instruccion",
            "CONCAT('CITE: ',sde.cite,'<br>Fecha: ',to_char(sde.fecha_cite, 'DD/MM/YYYY'),'<br>Remitente: ',CONCAT(pe.nombres, ' ', pe.apellidos, ' (', pe.institucion, ' - ',pe.cargo,')'),'<br>Referencia: ',sde.referencia) as documento_externo", "sde.doc_digital", "sdp.editar"
        );
        $where = array(
            'sdp.deleted_at' => NULL,
            'sdp.fk_usuario_actual' => session()->get('registroUser'),
        );
        $builder = $db->table('cam_dp.solicitud_derecho_preferente as sdp')
        ->select($campos)
        ->join('usuarios as urem', 'sdp.ultimo_fk_usuario_remitente = urem.id', 'left')
        ->join('perfiles as prem', 'urem.fk_perfil = prem.id', 'left')
        ->join('usuarios as udes', 'sdp.ultimo_fk_usuario_destinatario = udes.id', 'left')
        ->join('perfiles as pdes', 'udes.fk_perfil = pdes.id', 'left')
        ->join('cam_dp.solicitud_documento_externo as sde', 'sdp.id = sde.fk_solicitud_derecho_preferente', 'left')
        ->join('public.persona_externa AS pe', 'sde.fk_persona_externa = pe.id', 'left')
        ->where($where)
        ->whereIn('sdp.ultimo_estado',array('DERIVADO','DEVUELTO'))
        ->orderBY('sdp.id', 'ASC');
        $datos = $builder->get()->getResult('array');

        $campos_listar=array(
            'Estado','Fecha Ingreso','Días Pasados','Hoja de Ruta','Remitente','Destinatario','Instrucción','Documento Externo','Doc. Digital',
        );
        $campos_reales=array(
            'ultimo_estado','fecha_derivacion', 'dias', 'correlativo', 'remitente', 'destinatario', 'ultimo_instruccion', 'documento_externo', 'doc_digital'
        );

        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Listado de Solicitudes de Derecho Preferente';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['datos'] = $datos;
        $contenido['campos_listar'] = $campos_listar;
        $contenido['campos_reales'] = $campos_reales;
        $contenido['subtitulo'] = 'Listado de Solicitudes de Derecho Preferente';
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
        $campos = array(
            "sdp.id", "to_char(sdp.ultimo_fecha_derivacion, 'DD/MM/YYYY') as fecha_derivacion", "(CURRENT_DATE - sdp.ultimo_fecha_derivacion::date) as dias", "sdp.correlativo",
            "CONCAT(urem.nombre_completo,'<br><b>',prem.nombre,'<b>') as remitente", "sdp.ultimo_instruccion",
            "CONCAT('CITE: ',sde.cite,'<br>Fecha: ',to_char(sde.fecha_cite, 'DD/MM/YYYY'),'<br>Remitente: ',CONCAT(pe.nombres, ' ', pe.apellidos, ' (', pe.institucion, ' - ',pe.cargo,')'),'<br>Referencia: ',sde.referencia) as documento_externo",
            "sde.doc_digital",
        );
        $where = array(
            'sdp.deleted_at' => NULL,
            'sdp.ultimo_fk_usuario_destinatario' => session()->get('registroUser'),
        );
        $builder = $db->table('cam_dp.solicitud_derecho_preferente as sdp')
        ->select($campos)
        ->join('usuarios as urem', 'sdp.ultimo_fk_usuario_remitente = urem.id', 'left')
        ->join('perfiles as prem', 'urem.fk_perfil = prem.id', 'left')
        ->join('usuarios as udes', 'sdp.ultimo_fk_usuario_destinatario = udes.id', 'left')
        ->join('perfiles as pdes', 'udes.fk_perfil = pdes.id', 'left')
        ->join('cam_dp.solicitud_documento_externo as sde', 'sdp.id = sde.fk_solicitud_derecho_preferente', 'left')
        ->join('public.persona_externa AS pe', 'sde.fk_persona_externa = pe.id', 'left')
        ->where($where)
        ->whereIn('sdp.ultimo_estado',array('DERIVADO'))
        ->orderBY('sdp.id', 'DESC');
        $datos = $builder->get()->getResult('array');

        //$datos = $this->obtenerUltimosDocumentos($builder->get()->getResult('array'));
        //$datos = $this->obtenerCorrespondenciaExterna($datos);
        $campos_listar=array(
            'Fecha', 'Días<br>Pasados', 'Hoja de Ruta', 'Remitente', 'Instrucción', 'Documento Externo','Doc. Digital',
        );
        $campos_reales=array(
            'fecha_derivacion','dias', 'correlativo', 'remitente', 'ultimo_instruccion', 'documento_externo', 'doc_digital'
        );

        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Listado de Tramites Derivados';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['datos'] = $datos;
        $contenido['campos_listar'] = $campos_listar;
        $contenido['campos_reales'] = $campos_reales;
        $contenido['subtitulo'] = 'Listado de Tramites Derivados';
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
        $campos = array(
            "sdp.id", "sdp.ultimo_estado", "to_char(sdp.ultimo_fecha_derivacion, 'DD/MM/YYYY') as ultimo_fecha_derivacion", "(CURRENT_DATE - sdp.ultimo_fecha_derivacion::date) as dias", "sdp.correlativo",
            "CONCAT(urem.nombre_completo,'<br><b>',prem.nombre,'<b>') as remitente", "CONCAT(udes.nombre_completo,'<br><b>',pdes.nombre,'<b>') as destinatario", "sdp.ultimo_instruccion",
            "CONCAT(ures.nombre_completo,'<br><b>',pres.nombre,'<b>') as responsable",
            "CONCAT('CITE: ',sde.cite,'<br>Fecha: ',to_char(sde.fecha_cite, 'DD/MM/YYYY'),'<br>Remitente: ',CONCAT(pe.nombres, ' ', pe.apellidos, ' (', pe.institucion, ' - ',pe.cargo,')'),'<br>Referencia: ',sde.referencia) as documento_externo",
            "sde.doc_digital", "sdp.editar", "'NO' as finalizar",
        );
        $where = array(
            'sdp.deleted_at' => NULL,
            'sdp.fk_usuario_actual' => session()->get('registroUser'),
        );
        $builder = $db->table('cam_dp.solicitud_derecho_preferente as sdp')
        ->select($campos)
        ->join('usuarios as urem', 'sdp.ultimo_fk_usuario_remitente = urem.id', 'left')
        ->join('perfiles as prem', 'urem.fk_perfil = prem.id', 'left')
        ->join('usuarios as udes', 'sdp.ultimo_fk_usuario_destinatario = udes.id', 'left')
        ->join('perfiles as pdes', 'udes.fk_perfil = pdes.id', 'left')
        ->join('usuarios as ures', 'sdp.ultimo_fk_usuario_responsable = ures.id', 'left')
        ->join('perfiles as pres', 'ures.fk_perfil = pres.id', 'left')
        ->join('cam_dp.solicitud_documento_externo as sde', 'sdp.id = sde.fk_solicitud_derecho_preferente', 'left')
        ->join('public.persona_externa AS pe', 'sde.fk_persona_externa = pe.id', 'left')
        ->where($where)
        ->whereIn('sdp.ultimo_estado',array('MIGRADO', 'DERIVADO', 'RECIBIDO', 'EN ESPERA', 'DEVUELTO'))
        ->orderBY('sdp.id', 'DESC');
        $datos = $builder->get()->getResult('array');

        $campos_listar=array(
            'Estado','Fecha','Días<br>Pasados','Hoja de Ruta', 'Remitente', 'Destinatario', 'Responsable Trámite', 'Instrucción', 'Documento Externo','Doc. Digital',
        );
        $campos_reales=array(
            'ultimo_estado','ultimo_fecha_derivacion','dias','correlativo', 'remitente', 'destinatario', 'responsable', 'ultimo_instruccion', 'documento_externo', 'doc_digital',
        );

        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Listado de Mis Hojas de Rutas';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['datos'] = $datos;
        $contenido['campos_listar'] = $campos_listar;
        $contenido['campos_reales'] = $campos_reales;
        $contenido['subtitulo'] = 'Listado de Mis Hojas de Rutas';
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
            $id_areas_mineras = $this->request->getPost('id_areas_mineras');
            $extensiones = $this->request->getPost('extensiones');
            $validation = $this->validate([
                'areas_mineras_anexadas' => [
                    'rules' => 'required|mismo_titular',
                    'errors' => [
                        'required' => 'Este campo es obligatorio',
                        'mismo_titular' => 'Las Áreas Mineras Referenciales Anexadas deben ser del mismo TITULAR.'
                    ]
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
                'fk_usuario_destinatario' => [
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

                if(isset($id_areas_mineras) && count($id_areas_mineras) > 0){
                    $areas_mineras = array();
                    foreach($id_areas_mineras as $id_area_minera)
                        $areas_mineras[] = $this->informacionAreaMineraSiReg($id_area_minera);

                    $contenido['areas_mineras'] = $areas_mineras;
                    $contenido['extensiones'] = $extensiones;
                }

                if($this->request->getPost('fk_persona_externa')){
                    $campos = array('id', "CONCAT(documento_identidad, ' ', expedido, ' - ', nombres, ' ', apellidos, ' (', institucion, ' - ',cargo,')') as nombre");
                    $contenido['persona_externa'] = $personaExternaModel->select($campos)->find($this->request->getPost('fk_persona_externa'));
                }

                if($this->request->getPost('fk_usuario_destinatario'))
                    $contenido['usu_destinatario'] = $this->obtenerUsuario($this->request->getPost('fk_usuario_destinatario'));

                $contenido['validation'] = $this->validator;
            }else{
                $oficinaModel = new OficinasModel();
                $solicitudDerechoPreferenteModel = new SolicitudDerechoPreferenteModel();
                $solicitudDatosAreaMineraModel = new SolicitudDatosAreaMineraModel();
                $solicitudDocumentoExternoModel = new SolicitudDocumentoExternoModel();
                $solicitudDerivacionModel = new SolicitudDerivacionModel();
                $estado = 'DERIVADO';
                $oficina = $oficinaModel->find(session()->get('registroOficina'));
                $correlativo = $this->obtenerCorrelativo($oficina['correlativo'].'SOL-CAM-DP/');
                $instruccion = 'SOLICITUD DE CONTRATO ADMINISTRATIVO MINERO - DERECHO PREFERENTE '.$correlativo;

                $dataDerechoPreferente = array(
                    'fk_oficina' => session()->get('registroOficina'),
                    'correlativo' => $correlativo,
                    'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                    'ultimo_fk_estado_tramite_padre' => 0,
                    'ultimo_instruccion' => $instruccion,
                    'ultimo_fk_usuario_remitente' => session()->get('registroUser'),
                    'ultimo_fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                    'ultimo_fk_usuario_responsable' => $this->request->getPost('fk_usuario_destinatario'),
                    'ultimo_estado' => $estado,
                    'fk_usuario_actual' => session()->get('registroUser'),
                    'fk_usuario_creador' => session()->get('registroUser'),
                    'fk_usuario_destino' => $this->request->getPost('fk_usuario_destinatario'),
                );
                if($solicitudDerechoPreferenteModel->insert($dataDerechoPreferente) === false){
                    session()->setFlashdata('fail', $solicitudDerechoPreferenteModel->errors());
                }else{
                    $idSolicitud = $solicitudDerechoPreferenteModel->getInsertID();

                    foreach($id_areas_mineras as $i=>$id_area_minera){
                        $area_minera = $this->informacionAreaMineraSiReg($id_area_minera);
                        $dataAreaMinera = array(
                            'fk_solicitud_derecho_preferente' => $idSolicitud,
                            'fk_area_minera' => $area_minera['id_area_minera'],
                            'codigo_unico' => $area_minera['codigo_unico'],
                            'denominacion' => $area_minera['denominacion'],
                            'tipo_area' => 'LICENCIA DE PROSPECCIÓN Y EXPLORACIÓN',
                            'titular' => $area_minera['titular'],
                            'clasificacion_titular' => $area_minera['clasificacion'],
                            'representante_legal' => $area_minera['representante_legal'],
                            'extension_solicitada' => $extensiones[$i],
                        );
                        if($solicitudDatosAreaMineraModel->insert($dataAreaMinera) === false)
                            session()->setFlashdata('fail', $solicitudDatosAreaMineraModel->errors());
                    }

                    $docDigital = $this->request->getFile('doc_digital');
                    $path = $this->rutaArchivos.'solicitudes/';
                    $nombreAdjunto = $docDigital->getRandomName();
                    $docDigital->move($path,$nombreAdjunto);

                    $dataCorrespondenciaExterna = array(
                        'fk_solicitud_derecho_preferente' => $idSolicitud,
                        'fk_persona_externa' => $this->request->getPost('fk_persona_externa'),
                        'cite' => mb_strtoupper($this->request->getPost('cite')),
                        'fecha_cite' => $this->request->getPost('fecha_cite'),
                        'referencia' => mb_strtoupper($this->request->getPost('referencia')),
                        'fojas' => $this->request->getPost('fojas'),
                        'adjuntos' => mb_strtoupper($this->request->getPost('adjuntos')),
                        'doc_digital' => $path.$nombreAdjunto,
                    );

                    if($solicitudDocumentoExternoModel->insert($dataCorrespondenciaExterna) === false){
                        session()->setFlashdata('fail', $solicitudDocumentoExternoModel->errors());
                    }else{

                        $dataDerivacion = array(
                            'fk_solicitud_derecho_preferente' => $idSolicitud,
                            'fk_estado_tramite_padre' => 0,
                            'instruccion' => $instruccion,
                            'estado' => $estado,
                            'fk_usuario_responsable' => $this->request->getPost('fk_usuario_destinatario'),
                            'fk_usuario_remitente' => session()->get('registroUser'),
                            'fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                            'fk_usuario_creador' => session()->get('registroUser'),
                        );
                        if($solicitudDerivacionModel->insert($dataDerivacion) === false){
                            session()->setFlashdata('fail', $solicitudDerivacionModel->errors());
                        }else{
                            $this->actualizarCorrelativo($oficina['correlativo'].'SOL-CAM-DP/');
                            session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información. <code><a href="'.base_url($this->controlador.'hoja_ruta_solicitud_pdf/'.$idSolicitud).'" target="_blank">Descargar Hoja de Ruta</a></code>');
                        }
                    }
                }
                return redirect()->to($this->controlador.'mis_ingresos');
            }
        }

        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Registrar Nueva Solicitud';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['accion'] = $this->controlador.'agregar_ventanilla';
        $contenido['controlador'] = $this->controlador;
        $contenido['modal_remitente'] = view($this->carpeta.'nuevo_remitente', array('expedidos'=>$this->expedidos));
        $data['content'] = view($this->carpeta.'agregar_ventanilla', $contenido);
        $data['menu_actual'] = $this->menuActual.'agregar_ventanilla';
        $data['tramites_menu'] = $this->tramitesMenu();
        //$data['validacion_js'] = 'derecho-preferente-agregar-ventanilla.js';
        echo view('templates/template', $data);
    }

    public function atender($id){
        $db = \Config\Database::connect();
        $campos = array(
            'sdp.id','sdp.correlativo',"to_char(sdp.created_at, 'DD/MM/YYYY HH24:MI') as fecha_solicitud","sde.cite","to_char(fecha_cite, 'DD/MM/YYYY') as fecha_cite",
            "sde.doc_digital", "CONCAT(pe.nombres, ' ', pe.apellidos, ' (', pe.institucion, ' - ',pe.cargo,')') as remitente", "sde.referencia", "CONCAT(ures.nombre_completo,' - ',pres.nombre) as usuario_responsable",
            "sdp.ultimo_fk_usuario_responsable"
        );
        $where = array(
            'sdp.deleted_at' => NULL,
            'sdp.fk_usuario_actual' => session()->get('registroUser'),
            'sdp.id' => $id
        );
        $builder = $db->table('cam_dp.solicitud_derecho_preferente as sdp')
        ->select($campos)
        ->join('cam_dp.solicitud_documento_externo as sde', 'sdp.id = sde.fk_solicitud_derecho_preferente', 'left')
        ->join('public.persona_externa AS pe', 'sde.fk_persona_externa = pe.id', 'left')
        ->join('usuarios as ures', 'sdp.ultimo_fk_usuario_responsable = ures.id', 'left')
        ->join('perfiles as pres', 'ures.fk_perfil = pres.id', 'left')
        ->where($where);
        if($fila = $builder->get()->getRowArray()){
            $solicitudDerivacionModel = new SolicitudDerivacionModel();
            $solicitudDatosAreaMineraModel = new SolicitudDatosAreaMineraModel();
            $where = array(
                'fk_solicitud_derecho_preferente' => $fila['id']
            );
            $ultima_derivacion = $solicitudDerivacionModel->where($where)->orderBy('id', 'DESC')->first();
            $areas_mineras = $solicitudDatosAreaMineraModel->where($where)->findAll();            

            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Atender Tramite';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['fila'] = $fila;
            $contenido['ultima_derivacion'] = $ultima_derivacion;
            $contenido['areas_mineras'] = $areas_mineras;
            $contenido['documentos'] = $this->obtenerDocumentosAtender($fila['id']);
            $contenido['subtitulo'] = 'Atender Tramite';
            $contenido['accion'] = $this->controlador.'guardar_atender';
            $contenido['controlador'] = $this->controlador;
            $data['content'] = view($this->carpeta.'atender', $contenido);
            $data['menu_actual'] = $this->menuActual.'mis_tramites';
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['alertas'] = $this->alertasTramites();
            $data['validacion_js'] = 'derecho-preferente-atender-validation.js';
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador);
        }
    }
    public function guardarAtender(){
        if ($this->request->getPost()) {
            $db = \Config\Database::connect();
            $solicitudDerechoPreferenteModel = new SolicitudDerechoPreferenteModel();
            $solicitudDatosAreaMineraModel = new SolicitudDatosAreaMineraModel();
            $solicitudDerivacionModel = new SolicitudDerivacionModel();
            $id = $this->request->getPost('id');
            $validation = $this->validate([
                'id' => [
                    'rules' => 'required',
                ],
                'id_derivacion' => [
                    'rules' => 'required',
                ],
                'ultimo_fk_usuario_responsable' => [
                    'rules' => 'required',
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
                $campos = array(
                    'sdp.id','sdp.correlativo',"to_char(sdp.created_at, 'DD/MM/YYYY HH24:MI') as fecha_solicitud","sde.cite","to_char(fecha_cite, 'DD/MM/YYYY') as fecha_cite",
                    "sde.doc_digital", "CONCAT(pe.nombres, ' ', pe.apellidos, ' (', pe.institucion, ' - ',pe.cargo,')') as remitente", "sde.referencia", "CONCAT(ures.nombre_completo,' - ',pres.nombre) as usuario_responsable",
                    "sdp.ultimo_fk_usuario_responsable"
                );
                $where = array(
                    'sdp.deleted_at' => NULL,
                    'sdp.fk_usuario_actual' => session()->get('registroUser'),
                    'sdp.id' => $id
                );
                $builder = $db->table('cam_dp.solicitud_derecho_preferente as sdp')
                ->select($campos)
                ->join('cam_dp.solicitud_documento_externo as sde', 'sdp.id = sde.fk_solicitud_derecho_preferente', 'left')
                ->join('public.persona_externa AS pe', 'sde.fk_persona_externa = pe.id', 'left')
                ->join('usuarios as ures', 'sdp.ultimo_fk_usuario_responsable = ures.id', 'left')
                ->join('perfiles as pres', 'ures.fk_perfil = pres.id', 'left')
                ->where($where);
                $fila = $builder->get()->getRowArray();
                $where = array(
                    'fk_solicitud_derecho_preferente' => $fila['id']
                );
                $areas_mineras = $solicitudDatosAreaMineraModel->where($where)->findAll();

                $cabera['titulo'] = $this->titulo;
                $cabera['navegador'] = true;
                $cabera['subtitulo'] = 'Atender Tramite';
                $contenido['fila'] = $fila;
                $contenido['areas_mineras'] = $areas_mineras;
                $contenido['usu_destinatario'] = $this->obtenerUsuario($this->request->getPost('fk_usuario_destinatario'));
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['subtitulo'] = 'Atender Tramite';
                $contenido['accion'] = $this->controlador.'guardar_atender';
                $contenido['validation'] = $this->validator;
                $contenido['controlador'] = $this->controlador;
                $data['content'] = view($this->carpeta.'atender', $contenido);
                $data['menu_actual'] = $this->menuActual.'mis_tramites';
                $data['tramites_menu'] = $this->tramitesMenu();
                $data['alertas'] = $this->alertasTramites();
                $data['validacion_js'] = 'derecho-preferente-atender-validation.js';
                echo view('templates/template', $data);
            }else{
                $estado = 'DERIVADO';
                $dataDerechoPreferente = array(
                    'id' => $id,
                    'ultimo_estado' => $estado,
                    'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                    'ultimo_instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                    'ultimo_fk_usuario_remitente' => session()->get('registroUser'),
                    'ultimo_fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                    'ultimo_fk_usuario_responsable'=>($this->request->getPost('responsable') ? $this->request->getPost('fk_usuario_destinatario') : $this->request->getPost('ultimo_fk_usuario_responsable')),
                    'editar' => 'true',
                );
                if($solicitudDerechoPreferenteModel->save($dataDerechoPreferente) === false){
                    session()->setFlashdata('fail', $solicitudDerechoPreferenteModel->errors());
                }else{
                    $dataDerivacion = array(
                        'fk_solicitud_derecho_preferente' => $id,
                        'fk_estado_tramite_padre' => 0,
                        'instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                        'estado' => $estado,
                        'fk_usuario_responsable' => ($this->request->getPost('responsable') ? $this->request->getPost('fk_usuario_destinatario') : $this->request->getPost('ultimo_fk_usuario_responsable')),
                        'fk_usuario_remitente' => session()->get('registroUser'),
                        'fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                        'fk_usuario_creador' => session()->get('registroUser'),
                    );
                    if($solicitudDerivacionModel->insert($dataDerivacion) === false){
                        session()->setFlashdata('fail', $solicitudDerivacionModel->errors());
                    }else{
                        $dataDerivacionActualizacion = array(
                            'id' => $this->request->getPost('id_derivacion'),
                            'estado' => 'ATENDIDO',
                            'fecha_atencion' => date('Y-m-d H:i:s'),
                        );
                        if($solicitudDerivacionModel->save($dataDerivacionActualizacion) === false)
                            session()->setFlashdata('fail', $solicitudDerivacionModel->errors());
                        else
                            session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información.');
                    }
                }
                return redirect()->to($this->controlador.'mis_tramites');
            }
        }
    }
    public function editar($id){
        $db = \Config\Database::connect();
        $campos = array(
            'sdp.id','sdp.correlativo',"to_char(sdp.created_at, 'DD/MM/YYYY HH24:MI') as fecha_solicitud","sde.cite","to_char(fecha_cite, 'DD/MM/YYYY') as fecha_cite",
            "sde.doc_digital", "CONCAT(pe.nombres, ' ', pe.apellidos, ' (', pe.institucion, ' - ',pe.cargo,')') as remitente", "sde.referencia", "CONCAT(ures.nombre_completo,' - ',pres.nombre) as usuario_responsable",
            "sdp.ultimo_fk_usuario_responsable"
        );
        $where = array(
            'sdp.deleted_at' => NULL,
            'sdp.fk_usuario_actual' => session()->get('registroUser'),
            'sdp.id' => $id,
            'sdp.editar' => TRUE,
        );
        $builder = $db->table('cam_dp.solicitud_derecho_preferente as sdp')
        ->select($campos)
        ->join('cam_dp.solicitud_documento_externo as sde', 'sdp.id = sde.fk_solicitud_derecho_preferente', 'left')
        ->join('public.persona_externa AS pe', 'sde.fk_persona_externa = pe.id', 'left')
        ->join('usuarios as ures', 'sdp.ultimo_fk_usuario_responsable = ures.id', 'left')
        ->join('perfiles as pres', 'ures.fk_perfil = pres.id', 'left')
        ->where($where);
        if($fila = $builder->get()->getRowArray()){
            $solicitudDerivacionModel = new SolicitudDerivacionModel();
            $solicitudDatosAreaMineraModel = new SolicitudDatosAreaMineraModel();
            $where = array(
                'fk_solicitud_derecho_preferente' => $fila['id']
            );
            $derivacion = $solicitudDerivacionModel->where($where)->orderBy('id', 'DESC')->first();
            $areas_mineras = $solicitudDatosAreaMineraModel->where($where)->findAll();

            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Editar Derivación';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['fila'] = $fila;
            $contenido['derivacion'] = $derivacion;
            $contenido['areas_mineras'] = $areas_mineras;
            $contenido['usu_destinatario'] = $this->obtenerUsuario($derivacion['fk_usuario_destinatario']);
            $contenido['subtitulo'] = 'Editar Derivación';
            $contenido['accion'] = $this->controlador.'guardar_editar';
            $contenido['controlador'] = $this->controlador;
            $data['content'] = view($this->carpeta.'editar', $contenido);
            $data['menu_actual'] = $this->menuActual.'mis_tramites';
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['alertas'] = $this->alertasTramites();
            $data['validacion_js'] = 'derecho-preferente-editar-validation.js';
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador);
        }
    }
    public function guardarEditar(){
        if ($this->request->getPost()) {
            $db = \Config\Database::connect();
            $solicitudDerechoPreferenteModel = new SolicitudDerechoPreferenteModel();
            $solicitudDatosAreaMineraModel = new SolicitudDatosAreaMineraModel();
            $solicitudDerivacionModel = new SolicitudDerivacionModel();
            $id = $this->request->getPost('id');
            $id_derivacion = $this->request->getPost('id_derivacion');
            $validation = $this->validate([
                'id' => [
                    'rules' => 'required',
                ],
                'id_derivacion' => [
                    'rules' => 'required',
                ],
                'fk_usuario_destinatario' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar el Destinatario.'
                    ]
                ],
                'ultimo_fk_usuario_responsable' => [
                    'rules' => 'required',
                ],
                'instruccion' => [
                    'rules' => 'required',
                ],
            ]);
            if(!$validation){
                $campos = array(
                    'sdp.id','sdp.correlativo',"to_char(sdp.created_at, 'DD/MM/YYYY HH24:MI') as fecha_solicitud","sde.cite","to_char(fecha_cite, 'DD/MM/YYYY') as fecha_cite",
                    "sde.doc_digital", "CONCAT(pe.nombres, ' ', pe.apellidos, ' (', pe.institucion, ' - ',pe.cargo,')') as remitente", "sde.referencia", "CONCAT(ures.nombre_completo,' - ',pres.nombre) as usuario_responsable",
                    "sdp.ultimo_fk_usuario_responsable"
                );
                $where = array(
                    'sdp.deleted_at' => NULL,
                    'sdp.fk_usuario_actual' => session()->get('registroUser'),
                    'sdp.id' => $id,
                    'sdp.editar' => TRUE,
                );
                $builder = $db->table('cam_dp.solicitud_derecho_preferente as sdp')
                ->select($campos)
                ->join('cam_dp.solicitud_documento_externo as sde', 'sdp.id = sde.fk_solicitud_derecho_preferente', 'left')
                ->join('public.persona_externa AS pe', 'sde.fk_persona_externa = pe.id', 'left')
                ->join('usuarios as ures', 'sdp.ultimo_fk_usuario_responsable = ures.id', 'left')
                ->join('perfiles as pres', 'ures.fk_perfil = pres.id', 'left')
                ->where($where);
                $fila = $builder->get()->getRowArray();
                $where = array(
                    'fk_solicitud_derecho_preferente' => $fila['id']
                );
                $areas_mineras = $solicitudDatosAreaMineraModel->where($where)->findAll();

                $cabera['titulo'] = $this->titulo;
                $cabera['navegador'] = true;
                $cabera['subtitulo'] = 'Editar Derivación';
                $contenido['fila'] = $fila;
                $contenido['areas_mineras'] = $areas_mineras;
                $contenido['usu_destinatario'] = $this->obtenerUsuario($this->request->getPost('fk_usuario_destinatario'));
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['subtitulo'] = 'Editar Derivación';
                $contenido['accion'] = $this->controlador.'guardar_editar';
                $contenido['validation'] = $this->validator;
                $contenido['controlador'] = $this->controlador;
                $data['content'] = view($this->carpeta.'editar', $contenido);
                $data['menu_actual'] = $this->menuActual.'mis_tramites';
                $data['tramites_menu'] = $this->tramitesMenu();
                $data['alertas'] = $this->alertasTramites();
                $data['validacion_js'] = 'derecho-preferente-editar-validation.js';
                echo view('templates/template', $data);
            }else{
                $dataDerechoPreferente = array(
                    'id' => $id,
                    'ultimo_instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                    'ultimo_fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                    'ultimo_fk_usuario_responsable'=>($this->request->getPost('responsable') ? $this->request->getPost('fk_usuario_destinatario') : $this->request->getPost('ultimo_fk_usuario_responsable')),
                    'editar' => 'false',
                );
                if($solicitudDerechoPreferenteModel->save($dataDerechoPreferente) === false){
                    session()->setFlashdata('fail', $solicitudDerechoPreferenteModel->errors());
                }else{
                    $dataDerivacion = array(
                        'id' => $id_derivacion,
                        'instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                        'fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                        'fk_usuario_responsable' => ($this->request->getPost('responsable') ? $this->request->getPost('fk_usuario_destinatario') : $this->request->getPost('ultimo_fk_usuario_responsable')),
                    );
                    if($solicitudDerivacionModel->save($dataDerivacion) === false)
                        session()->setFlashdata('fail', $solicitudDerivacionModel->errors());
                    else
                        session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información.');
                }
                return redirect()->to($this->controlador.'mis_tramites');
            }
        }
    }

    public function recibir($id_tramite){
        $solicitudDerechoPreferenteModel = new SolicitudDerechoPreferenteModel();
        $solicitudDerivacionModel = new SolicitudDerivacionModel();
        $where = array(
            'id' => $id_tramite,
            'deleted_at' => NULL,
            'ultimo_fk_usuario_destinatario' => session()->get('registroUser'),
        );
        if($fila = $solicitudDerechoPreferenteModel->where($where)->whereIn('ultimo_estado', array('DERIVADO','MIGRADO'))->first()){
            $estado = 'RECIBIDO';
            $data = array(
                'id' => $fila['id'],
                'ultimo_estado' => $estado,
                'fk_usuario_actual' => $fila['ultimo_fk_usuario_destinatario'],
                'editar' => true,
            );

            if($solicitudDerechoPreferenteModel->save($data) === false)
                session()->setFlashdata('fail', $solicitudDerechoPreferenteModel->errors());

            $where = array(
                'fk_solicitud_derecho_preferente' => $fila['id'],
            );
            $derivacion = $solicitudDerivacionModel->where($where)->orderBY('id', 'DESC')->first();
            $dataDerivacion = array(
                'id' => $derivacion['id'],
                'estado' => $estado,
                'fecha_recepcion' => date('Y-m-d H:i:s'),
            );

            if($solicitudDerivacionModel->save($dataDerivacion) === false)
                session()->setFlashdata('fail', $solicitudDerivacionModel->errors());

        }
        return redirect()->to($this->controlador.'listado_recepcion');
    }
    public function recibirMultiple(){
        if ($this->request->getPost()) {
            if($ids_tramites = $this->request->getPost('recibir')){
                foreach($ids_tramites as $id_tramite)
                    $this->recibirTramite($id_tramite);
                session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información.');
                return redirect()->to($this->controlador.'listado_recepcion');
            }
        }
        session()->setFlashdata('fail', 'No se pudo recepcionar los trámites.');
        return redirect()->to($this->controlador.'mis_tramites');
    }
    public function recibirTramite($id_tramite){
        $solicitudDerechoPreferenteModel = new SolicitudDerechoPreferenteModel();
        $solicitudDerivacionModel = new SolicitudDerivacionModel();
        $where = array(
            'id' => $id_tramite,
            'deleted_at' => NULL,
            'ultimo_fk_usuario_destinatario' => session()->get('registroUser'),
        );
        if($fila = $solicitudDerechoPreferenteModel->where($where)->whereIn('ultimo_estado', array('DERIVADO'))->first()){
            $estado = 'RECIBIDO';
            $data = array(
                'id' => $fila['id'],
                'ultimo_estado' => $estado,
                'fk_usuario_actual' => $fila['ultimo_fk_usuario_destinatario'],
                'editar' => true,
            );

            if($solicitudDerechoPreferenteModel->save($data) === false){
                session()->setFlashdata('fail', $solicitudDerechoPreferenteModel->errors());
            }

            $where = array(
                'fk_solicitud_derecho_preferente' => $fila['id'],
            );
            $derivacion = $solicitudDerivacionModel->where($where)->orderBY('id', 'DESC')->first();
            $dataDerivacion = array(
                'id' => $derivacion['id'],
                'estado' => $estado,
                'fecha_recepcion' => date('Y-m-d H:i:s'),
            );

            if($solicitudDerivacionModel->save($dataDerivacion) === false){
                session()->setFlashdata('fail', $solicitudDerivacionModel->errors());
            }
        }
        return true;
    }
    public function ajaxGuardarDevolver(){
        $solicitudDerechoPreferenteModel = new SolicitudDerechoPreferenteModel();
        $resultado = array(
            'error' => 'No se guardo la información',
        );
        $where = array(
            'id' => $this->request->getPost('id'),
            'ultimo_fk_usuario_destinatario' => session()->get('registroUser'),
            'deleted_at' => NULL,
        );
        if($fila = $solicitudDerechoPreferenteModel->where($where)->first()){
            $solicitudDerivacionModel = new SolicitudDerivacionModel();
            $estado = 'DEVUELTO';
            $motivo_devolucion = mb_strtoupper($this->request->getPost('motivo_devolucion'));
            $documentosModel = new DocumentosModel();
            $where = array(
                'fk_solicitud_derecho_preferente' => $fila['id'],
            );
            $derivaciones = $solicitudDerivacionModel->where($where)->orderBy('id', 'DESC')->findAll(2);
            if(count($derivaciones) > 1){
                var_dump('hola a'); exit();
                $derivacion_actual = $derivaciones[0];
                $derivacion_restaurar = $derivaciones[1];
                var_dump($derivacion_actual, $derivacion_restaurar); exit();
                /*$where = array(
                    'fk_derivacion' => $derivacion_actual['id'],
                    'fk_acto_administrativo' => $fila['id'],
                );
                $documentos_anexados = $documentosModel->where($where)->findAll();*/
                $data = array(
                    'id' => $fila['id'],
                    'ultimo_estado' => $estado,
                    'ultimo_fk_estado_tramite_padre' => $derivacion_restaurar['fk_estado_tramite_padre'],
                    'ultimo_fk_estado_tramite_hijo' => $derivacion_restaurar['fk_estado_tramite_hijo'],
                    'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                    'ultimo_instruccion' => $motivo_devolucion,
                    'ultimo_fk_usuario_remitente' => session()->get('registroUser'),
                    'ultimo_fk_usuario_destinatario' => $derivacion_restaurar['fk_usuario_destinatario'],
                    'ultimo_fk_documentos' => '',
                );
                if($solicitudDerechoPreferenteModel->save($data) === false){
                    session()->setFlashdata('fail', $solicitudDerechoPreferenteModel->errors());
                }else{
                    $dataDerivacion = array(
                        'fk_solicitud_derecho_preferente' => $fila['id'],
                        'fk_estado_tramite_padre' => $derivacion_restaurar['fk_estado_tramite_padre'],
                        'fk_estado_tramite_hijo' => $derivacion_restaurar['fk_estado_tramite_hijo'],
                        'observaciones' => $derivacion_restaurar['observaciones'],
                        'fk_usuario_remitente' => session()->get('registroUser'),
                        'fk_usuario_destinatario' => $derivacion_actual['fk_usuario_remitente'],
                        'instruccion' => $motivo_devolucion,
                        'motivo_anexo' => $derivacion_restaurar['motivo_anexo'],
                        'estado' => $estado,
                        'fk_usuario_creador' => session()->get('registroUser'),
                        'fk_usuario_responsable' => $derivacion_actual['fk_usuario_responsable'],
                    );
                    if($solicitudDerivacionModel->insert($dataDerivacion) === false){
                        session()->setFlashdata('fail', $solicitudDerivacionModel->errors());
                    }else{

                        $dataDerivacionActualizacion = array(
                            'id' => $derivacion_actual['id'],
                            'estado' => 'ATENDIDO',
                            'fecha_devolucion' => date('Y-m-d H:i:s'),
                        );
                        if($solicitudDerivacionModel->save($dataDerivacionActualizacion) === false)
                            session()->setFlashdata('fail', $solicitudDerivacionModel->errors());

                        $resultado = array(
                            'idtra' => $fila['id']
                        );

                    }
                }
            }else{
                $derivacion_restaurar = $derivaciones[0];
                $data = array(
                    'id' => $fila['id'],
                    'ultimo_estado' => $estado,
                    'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                    'ultimo_instruccion' => $motivo_devolucion,
                    'ultimo_fk_usuario_remitente' => session()->get('registroUser'),
                    'ultimo_fk_usuario_destinatario' => $derivacion_restaurar['fk_usuario_destinatario'],
                );
                if($solicitudDerechoPreferenteModel->save($data) === false){
                    session()->setFlashdata('fail', $solicitudDerechoPreferenteModel->errors());
                }else{
                    $dataDerivacion = array(
                        'fk_solicitud_derecho_preferente' => $fila['id'],
                        'fk_estado_tramite_padre' => $derivacion_restaurar['fk_estado_tramite_padre'],
                        'fk_estado_tramite_hijo' => $derivacion_restaurar['fk_estado_tramite_hijo'],
                        'observaciones' => $derivacion_restaurar['observaciones'],
                        'fk_usuario_remitente' => session()->get('registroUser'),
                        'fk_usuario_destinatario' => $derivacion_restaurar['fk_usuario_remitente'],
                        'instruccion' => $motivo_devolucion,
                        'motivo_anexo' => $derivacion_restaurar['motivo_anexo'],
                        'estado' => $estado,
                        'fk_usuario_creador' => session()->get('registroUser'),
                        'fk_usuario_responsable' => $derivacion_restaurar['fk_usuario_responsable'],
                    );
                    if($solicitudDerivacionModel->insert($dataDerivacion) === false){
                        session()->setFlashdata('fail', $solicitudDerivacionModel->errors());
                    }else{
                        $dataDerivacionActualizacion = array(
                            'id' => $derivacion_restaurar['id'],
                            'estado' => 'ATENDIDO',
                            'fecha_devolucion' => date('Y-m-d H:i:s'),
                        );
                        if($solicitudDerivacionModel->save($dataDerivacionActualizacion) === false)
                            session()->setFlashdata('fail', $solicitudDerivacionModel->errors());

                        $resultado = array(
                            'idtra' => $fila['id']
                        );
                    }
                }
            }
        }
        echo json_encode($resultado);
    }

    public function hojaRutaSolicitudPdf($id_solicitud){
        $solicitudDerechoPreferenteModel = new SolicitudDerechoPreferenteModel();
        $campos = array(
            'id','fk_oficina','fk_usuario_creador','fk_usuario_destino',"to_char(created_at, 'DD/MM/YYYY HH24:MI') as fecha_creacion","correlativo",
        );
        if($solicitud = $solicitudDerechoPreferenteModel->select($campos)->find($id_solicitud)){
            $oficinaModel = new OficinasModel();
            $solicitudDatosAreaMineraModel = new SolicitudDatosAreaMineraModel();
            $solicitudDocumentoExternoModel = new SolicitudDocumentoExternoModel();
            $oficina = $oficinaModel->find($solicitud['fk_oficina']);
            $areasMineras = $solicitudDatosAreaMineraModel->where(array('fk_solicitud_derecho_preferente'=>$solicitud['id']))->orderBy('id','ASC')->findAll();
            $documentoExterno = $solicitudDocumentoExternoModel->select("fojas,cite,to_char(fecha_cite, 'DD/MM/YYYY') as fecha_cite")->find($solicitud['id']);
            $usuarioCreador = $this->obtenerUsuario($solicitud['fk_usuario_creador']);
            $usuarioDestino = $this->obtenerUsuario($solicitud['fk_usuario_destino']);

            $campos_listar = array(
                'Código Único','Denominación','Titular','Clasificación','Representante Legal','Extensión Solicitada'
            );
            $campos_reales=array(
                'codigo_unico','denominacion','titular','clasificacion_titular','representante_legal','extension_solicitada'
            );
            $campos_tamanio = array(15,40,40,25,40,16);

            $contenido['campos_listar'] = $campos_listar;
            $contenido['campos_reales'] = $campos_reales;
            $contenido['campos_tamanio'] = $campos_tamanio;
            $contenido['n_campos'] = count($campos_listar);
            $contenido['areas_mineras'] = $areasMineras;
            $contenido['color'] = '#fff';
            $htmlAreaMinera = view($this->carpeta.'pdf_areas_mineras_hr', $contenido);

            $file_name = str_replace('/','-',$solicitud['correlativo']).'.pdf';
            $pdf = new HojaRutaPdf('P', 'mm', array(216, 279), true, 'UTF-8', false);

            //
            $pdf->SetCreator('GARNET');
            $pdf->SetAuthor('Desarrollo de UTIC');
            $pdf->SetTitle('Hoja de Ruta de Solicitud de Contrato Administrativo Minero - Derecho Preferente');
            $pdf->SetKeywords('Hoja, Ruta, Solicitud, Contrato, Administrativo, Minero Derecho, Preferente');

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
            $pdf->MultiCell(106,5, 'SOLICITUD DE CONTRATO ADMINISTRATIVO MINERO - DERECHO PREFERENTE', 'RL', 'C', true, 0, '', '', true, 0, false, false, 10, 'T');
            /*
            *  CITE DEL DOCUMENTO
            */
            $pdf->MultiCell(50,5, $solicitud['fecha_creacion'], 1, 'C', false, 1, '', '', true, 0, false, false, 5, 'M', true);
            /*
            *  FIN CITE DEL DOCUMENTO
            */
            $pdf->setx(48);
            $pdf->SetFont($this->fontPDF, 'B', 14);
            $pdf->MultiCell(106,10,  $solicitud['correlativo'] , 'LBR', 'C', true, 0, '', '', true, 0, false, false, 10, 'B');
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
            $pdf->SetFont($this->fontPDF, 'B', 8);
            $pdf->MultiCell(50, 5, "DOCUMENTO EXTERNO:", 1, 'R', true, 0);
            $pdf->SetFont($this->fontPDF, '', 8);
            $pdf->MultiCell(86,5, $documentoExterno['cite'], 1, 'L', false, 0);
            $pdf->SetFont($this->fontPDF, 'B', 8);
            $pdf->MultiCell(20, 5, "FECHA:", 1, 'R', false, 0);
            $pdf->SetFont($this->fontPDF, '', 8);
            $pdf->MultiCell(38,5, $documentoExterno['fecha_cite'], 1, 'L', true, 1);

            $pdf->SetFont($this->fontPDF, 'B', 8);
            $pdf->MultiCell(50, 5, "USUARIO DESTINO:", 1, 'R', true, 0);
            $pdf->SetFont($this->fontPDF, '', 8);
            $pdf->MultiCell(144,5, $usuarioDestino['nombre'], 1, 'L', false, 1);
            $pdf->SetFont($this->fontPDF, 'B', 8);
            $pdf->MultiCell(50, 5, "USUARIO CREADOR:", 1, 'R', true, 0);
            $pdf->SetFont($this->fontPDF, '', 8);
            $pdf->MultiCell(144,5, $usuarioCreador['nombre'], 1, 'L', false, 1);

            $pdf->SetFont($this->fontPDF, '', 7);
            $pdf->writeHTML($htmlAreaMinera, true, false, false, false, '');
            $pdf->ln(-5);
            //$pdf->MultiCell(144,5, $pdf->GetY(), 1, 'L', false, 1);


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
    public function ajaxDatosAreaMinera(){
        $idAreaMinera = $this->request->getPost('id');
        if(!empty($idAreaMinera)){
            if($data = $this->informacionAreaMineraSiReg($idAreaMinera))
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


    private function informacionAreaMineraSiReg($id_area_minera){
        if(isset($id_area_minera) && $id_area_minera > 0){
            $dbSincobol = \Config\Database::connect('sincobol');
            $campos = array(
                "rgl.id_area_minera","rgl.codigo_unico","rgl.denominacion","rgl.titular",
                "CASE WHEN tipo_actor_especifico != '' THEN CONCAT(tipo_actor, '<br>',tipo_actor_especifico) ELSE tipo_actor END clasificacion",
                "dra.nombre_completo as representante_legal"
            );
            $where = array(
                'rgl.id_area_minera' => $id_area_minera,
            );
            $builder = $dbSincobol->table('siremi.reporte_general_lpe as rgl')
            ->select($campos)
            ->join('siremi.datos_representante_apoderado as dra', "rgl.id_dato_general = dra.fk_datos_general AND dra.tipo = 'REPRESENTANTE LEGAL'", 'left')
            ->where($where)
            ->whereIn('rgl.estado', array('INSCRITO', 'FINALIZADO', 'HISTORICO'));
            $datos = $builder->get()->getFirstRow('array');
            if($datos)
                return $datos;
            else
                return false;
        }else{
            return false;
        }
    }
    private function obtenerCorrelativo($sigla){
        $correlativosDerechoPreferenteModel = new CorrelativosDerechoPreferenteModel();
        $correlativo = '';
        $where = array(
            'gestion' => date('Y'),
            'sigla' => $sigla,
        );

        if($correlativoActual = $correlativosDerechoPreferenteModel->where($where)->first())
            $correlativo = $sigla.($correlativoActual['correlativo_actual']+1).'/'.date('Y');
        else
            $correlativo = $sigla.'1'.'/'.date('Y');

        return $correlativo;
    }
    private function actualizarCorrelativo($sigla){
        $correlativosDerechoPreferenteModel = new CorrelativosDerechoPreferenteModel();
        $where = array(
            'gestion' => date('Y'),
            'sigla' => $sigla,
        );

        if($dataCorrelativo = $correlativosDerechoPreferenteModel->where($where)->first())
            $dataCorrelativo['correlativo_actual'] +=1;
        else
            $dataCorrelativo = array_merge(array('correlativo_actual' => 1), $where);

        if($correlativosDerechoPreferenteModel->save($dataCorrelativo) === false)
            return $correlativosDerechoPreferenteModel->errors();

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
    private function obtenerDocumentosAtender($fk_hoja_ruta){
        $db = \Config\Database::connect();
        $campos = array('doc.id', 'doc.correlativo', "TO_CHAR(doc.fecha, 'DD/MM/YYYY') as fecha", 'td.nombre as tipo_documento', 'td.notificacion');
        $where = array(
            'doc.fk_tramite' => $this->idTramite,
            'doc.fk_usuario_creador' => session()->get('registroUser'),            
            'doc.estado' => 'SUELTO',
            'doc.fk_hoja_ruta' => $fk_hoja_ruta,

        );
        $builder = $db->table('public.documentos AS doc')
        ->select($campos)
        ->join('public.tipo_documento AS td', 'doc.fk_tipo_documento = td.id', 'left')
        ->where($where)
        ->orderBY('doc.id', 'ASC');
        return $builder->get()->getResultArray();
    }

}