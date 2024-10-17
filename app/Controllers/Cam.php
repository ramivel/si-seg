<?php

namespace App\Controllers;

use App\Libraries\CodigoSeguimientoPdf;
use App\Libraries\HojaRutaPdf;
use App\Models\EstadoTramiteModel;
use App\Models\TipoSolicitudModel;
use App\Models\ActoAdministrativoModel;
use App\Models\ActoRegistradoModel;
use App\Models\CarpetaArchivoSincobolModel;
use App\Models\CodigoSeguimientoModel;
use App\Models\CorrespondenciaExternaModel;
use App\Models\DatosAreaMineraModel;
use App\Models\DerivacionModel;
use App\Models\DerivacionSincobolModel;
use App\Models\DocumentosModel;
use App\Models\HojaRutaSisegModel;
use App\Models\HrAnexadasModel;
use App\Models\LogsBuscadoresModel;
use App\Models\MigrarCAMModel;
use App\Models\OficinasModel;
use App\Models\SolicitudLicenciaContratoModel;
use App\Models\TipoDocumentoExternoModel;
use App\Models\TipoDocumentoModel;
use App\Models\TramitesModel;
use App\Models\UsuariosModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Cam extends BaseController
{
    protected $titulo = 'Contratos Administrativos Mineros';
    protected $controlador = 'cam/';
    protected $carpeta = 'cam/';
    protected $idTramite = 1;
    protected $menuActual = 'cam/';
    protected $rutaArchivos = 'archivos/cam/';
    protected $urlSincobol = 'https://sincobol.autoridadminera.gob.bo/sincobol/';
    protected $alpha = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
    protected $fontPDF = 'helvetica';
    protected $tipo_hoja_ruta = array(
        1 => 'SOLICITUD CONTRATO ADMINISTRATIVO MINERO',
        2 => 'CONTRATO MINERO NACIONAL',
        3 => 'CONTRATO MINERO COMIBOL',
    );
    protected $acciones = array(
        'Para su conocimiento y consideración',
        'Analizar',
        'Procesar conforme a normativa',
        'Preparar respuesta',
        'Atender lo solicitado',
        'Preparar informe',
        'Elaborar resolución',
        'Elaborar contrato',
        'Proceder conforme a reglamento',
        'Proyectar providencia',
        'Archivar',
    );

    public function misTramites()
    {
        $db = \Config\Database::connect();
        $campos = array('ac.id', 'ac.fk_area_minera', 'ac.correlativo', "to_char(ac.fecha_mecanizada, 'DD/MM/YYYY') as fecha_mecanizada", 'ac.ultimo_instruccion', 'ac.ultimo_estado',
        'dam.codigo_unico', 'dam.denominacion', 'dam.titular', 'dam.departamentos', 'dam.provincias', 'dam.municipios', "CONCAT(ur.nombre_completo,'<br><b>',pr.nombre,'<b>') as remitente", "CONCAT(ud.nombre_completo,'<br><b>',pd.nombre,'<b>') as destinatario",
        "CASE WHEN ac.ultimo_fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'. ',etp.nombre,'<br>',etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE CONCAT(etp.orden,'. ',etp.nombre) END as estado_tramite",
        "CASE WHEN ac.ultimo_fk_estado_tramite_hijo > 0 AND eth.finalizar THEN 'SI' WHEN etp.finalizar THEN 'SI'  ELSE 'NO' END as finalizar",
        "to_char(ac.ultimo_fecha_derivacion, 'DD/MM/YYYY') as ultimo_fecha_derivacion", "(CURRENT_DATE - ac.ultimo_fecha_derivacion::date) as dias", 'etp.dias_intermedio', 'etp.dias_limite', 'etp.notificar',
        'ac.ultimo_fk_documentos', "CONCAT(ua.nombre_completo,'<br><b>',pa.nombre,'<b>') as responsable", 'dam.representante_legal', 'ac.ultimo_recurso_jerarquico', 'ac.ultimo_recurso_revocatoria', 'ac.ultimo_oposicion', 'ac.editar'
        );
        $where = array(
            'ac.deleted_at' => NULL,
            'ac.fk_usuario_actual' => session()->get('registroUser'),
        );
        $builder = $db->table('public.acto_administrativo as ac')
        ->select($campos)
        ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
        ->join('usuarios as ur', 'ac.ultimo_fk_usuario_remitente = ur.id', 'left')
        ->join('perfiles as pr', 'ur.fk_perfil = pr.id', 'left')
        ->join('usuarios as ud', 'ac.ultimo_fk_usuario_destinatario = ud.id', 'left')
        ->join('perfiles as pd', 'ud.fk_perfil = pd.id', 'left')
        ->join('estado_tramite as etp', 'ac.ultimo_fk_estado_tramite_padre = etp.id', 'left')
        ->join('estado_tramite as eth', 'ac.ultimo_fk_estado_tramite_hijo = eth.id', 'left')
        ->join('usuarios as ua', 'ac.ultimo_fk_usuario_responsable = ua.id', 'left')
        ->join('perfiles as pa', 'ua.fk_perfil = pa.id', 'left')
        ->where($where)
        ->whereIn('ac.ultimo_estado',array('MIGRADO', 'DERIVADO', 'RECIBIDO', 'EN ESPERA', 'DEVUELTO'))
        ->orderBY('ac.id', 'DESC');
        $datos = $this->obtenerUltimosDocumentos($builder->get()->getResult('array'));
        $datos = $this->obtenerCorrespondenciaExterna($datos);
        $campos_listar=array(
            'Estado','Fecha','Días<br>Pasados','H.R. Madre', 'Remitente', 'Destinatario', 'Instrucción', 'Ultimo(s) Documento(s) Anexado(s)', 'Estado Tramite', 'Responsable Trámite', 'APM Presento',
            'Codigo Unico','Denominacion','Representante Legal','Solicitante','Departamentos','Provincias','Municipios',
        );
        $campos_reales=array(
            'ultimo_estado','ultimo_fecha_derivacion','dias','correlativo', 'remitente', 'destinatario', 'ultimo_instruccion', 'ultimos_documentos', 'estado_tramite', 'responsable', 'apm_presento',
            'codigo_unico', 'denominacion','representante_legal','titular', 'departamentos','provincias','municipios',
        );

        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Listado de Tramites en Curso';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['datos'] = $datos;
        $contenido['campos_listar'] = $campos_listar;
        $contenido['campos_reales'] = $campos_reales;
        $contenido['subtitulo'] = 'Listado de Tramites en Curso';
        $contenido['controlador'] = $this->controlador;
        $contenido['id_tramite'] = $this->idTramite;
        $data['content'] = view($this->carpeta.'index', $contenido);
        $data['menu_actual'] = $this->menuActual.'mis_tramites';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['alertas'] = $this->alertasTramites();
        echo view('templates/template', $data);
    }
    public function misTramitesExcel(){
        $db = \Config\Database::connect();
        $campos_listar=array(
            ' ','Fecha','Días Pasados','H.R. Madre','Remitente','Destinatario','Responsable Trámite','Instrucción','Estado Tramite','Subestado Tramite',
            'Codigo Unico','Denominacion','Representante Legal','Solicitante','Departamentos','Provincias','Municipios'
        );
        $campos_reales=array(
            'ultimo_estado','ultimo_fecha_derivacion','dias','correlativo','remitente','destinatario','responsable','ultimo_instruccion','estado_tramite','sub_estado_tramite',
            'codigo_unico', 'denominacion','representante_legal','titular','departamentos','provincias','municipios'
        );
        $campos = array(
            'ac.ultimo_estado', "to_char(ac.ultimo_fecha_derivacion, 'DD/MM/YYYY') as ultimo_fecha_derivacion", "(CURRENT_DATE - ac.ultimo_fecha_derivacion::date) as dias", 'ac.correlativo',
            "CONCAT(ur.nombre_completo,' - ',pr.nombre) as remitente", "CONCAT(ud.nombre_completo,' - ',pd.nombre) as destinatario", "CONCAT(ua.nombre_completo,' - ',pa.nombre) as responsable",
            'ac.ultimo_instruccion',"CONCAT(etp.orden,'. ',etp.nombre) as estado_tramite",
            "CASE WHEN ac.ultimo_fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE '' END as sub_estado_tramite",
            'dam.codigo_unico', 'dam.denominacion','dam.representante_legal','dam.titular', 'dam.departamentos','dam.provincias','dam.municipios',
        );
        $where = array(
            'ac.deleted_at' => NULL,
            'ac.fk_usuario_actual' => session()->get('registroUser'),
        );
        $builder = $db->table('public.acto_administrativo as ac')
        ->select($campos)
        ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
        ->join('usuarios as ur', 'ac.ultimo_fk_usuario_remitente = ur.id', 'left')
        ->join('perfiles as pr', 'ur.fk_perfil = pr.id', 'left')
        ->join('usuarios as ud', 'ac.ultimo_fk_usuario_destinatario = ud.id', 'left')
        ->join('perfiles as pd', 'ud.fk_perfil = pd.id', 'left')
        ->join('estado_tramite as etp', 'ac.ultimo_fk_estado_tramite_padre = etp.id', 'left')
        ->join('estado_tramite as eth', 'ac.ultimo_fk_estado_tramite_hijo = eth.id', 'left')
        ->join('usuarios as ua', 'ac.ultimo_fk_usuario_responsable = ua.id', 'left')
        ->join('perfiles as pa', 'ua.fk_perfil = pa.id', 'left')
        ->where($where)
        ->orderBY('ac.id', 'DESC');
        if($datos = $builder->get()->getResultArray()){
            helper('security');
            $file_name = sanitize_filename(mb_strtolower(session()->get('registroUserName'))).' - bandeja - '.date('YmdHis').'.xlsx';
            $this->exportarXLS($campos_listar, $campos_reales, $datos, $file_name);
        }else{
            return redirect()->to($this->controlador.'mis_tramites');
        }
    }

    public function listadoRecepcion()
    {
        $db = \Config\Database::connect();
        $campos = array('ac.id', 'ac.fk_area_minera', 'ac.correlativo', "to_char(ac.fecha_mecanizada, 'DD/MM/YYYY') as fecha_mecanizada", 'ac.ultimo_instruccion', 'ac.ultimo_estado',
        'dam.codigo_unico', 'dam.denominacion', 'dam.titular', 'dam.departamentos', "CONCAT(ur.nombre_completo,'<br><b>',pr.nombre,'<b>') as remitente",
        "CASE WHEN ac.ultimo_fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'. ',etp.nombre,'<br>',etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE CONCAT(etp.orden,'. ',etp.nombre) END as estado_tramite",
        "to_char(ac.ultimo_fecha_derivacion, 'DD/MM/YYYY') as ultimo_fecha_derivacion", "(CURRENT_DATE - ac.ultimo_fecha_derivacion::date) as dias", 'etp.dias_intermedio', 'etp.dias_limite', 'etp.notificar',
        'ac.ultimo_fk_documentos', "CONCAT(ua.nombre_completo,'<br><b>',pa.nombre,'<b>') as responsable", 'dam.representante_legal', 'ac.ultimo_recurso_jerarquico', 'ac.ultimo_recurso_revocatoria', 'ac.ultimo_oposicion');
        $where = array(
            'ac.deleted_at' => NULL,
            'ac.ultimo_fk_usuario_destinatario' => session()->get('registroUser'),
        );
        $builder = $db->table('public.acto_administrativo as ac')
        ->select($campos)
        ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
        ->join('usuarios as ur', 'ac.ultimo_fk_usuario_remitente = ur.id', 'left')
        ->join('perfiles as pr', 'ur.fk_perfil = pr.id', 'left')
        ->join('estado_tramite as etp', 'ac.ultimo_fk_estado_tramite_padre = etp.id', 'left')
        ->join('estado_tramite as eth', 'ac.ultimo_fk_estado_tramite_hijo = eth.id', 'left')
        ->join('usuarios as ua', 'ac.ultimo_fk_usuario_responsable = ua.id', 'left')
        ->join('perfiles as pa', 'ua.fk_perfil = pa.id', 'left')
        ->where($where)
        ->whereIn('ac.ultimo_estado', array('DERIVADO','MIGRADO'))
        ->orderBY('ac.id', 'DESC');
        $datos = $this->obtenerUltimosDocumentos($builder->get()->getResult('array'));
        $datos = $this->obtenerCorrespondenciaExterna($datos);
        $campos_listar=array(
            'Fecha', 'Días<br>Pasados', 'H.R. Madre', 'Remitente', 'Instrucción', 'Ultimo(s) Documento(s) Anexado(s)', 'Estado Tramite', 'Responsable Trámite', 'APM Presento', 'Codigo Unico','Denominacion','Representante Legal','Solicitante','Departamentos',
        );
        $campos_reales=array(
            'ultimo_fecha_derivacion','dias', 'correlativo', 'remitente', 'ultimo_instruccion', 'ultimos_documentos', 'estado_tramite', 'responsable', 'apm_presento', 'codigo_unico', 'denominacion','representante_legal','titular', 'departamentos',
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
                                $correlativos .= "<a href='".base_url($this->rutaArchivos.$row['fk_area_minera'].'/'.$doc['doc_digital'])."' target='_blank' title='Ver Documento'>".$doc['correlativo']."</a><br>";
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

    private function obtenerCorrespondenciaExterna($datos){
        if($datos){
            $correspondenciaExternaModel = new CorrespondenciaExternaModel();
            foreach($datos as $i=>$row){
                $where = array(
                    'deleted_at' => NULL,
                    'fk_tramite' => $this->idTramite,
                    'fk_acto_administrativo' => $row['id'],
                    'estado' => 'INGRESADO',
                );
                if($correspondencia_externa = $correspondenciaExternaModel->where($where)->findAll())
                    $datos[$i]['n_correspondencia_externa'] = count($correspondencia_externa);
                else
                    $datos[$i]['n_correspondencia_externa'] = 0;
            }
        }
        return $datos;
    }

    public function agregar(){
        $estadosTramites = $this->obtenerEstadosTramites($this->idTramite);
        if ($this->request->getPost()) {
            $validation = $this->validate([
                'fk_solicitud_licencia_contrato' => [
                    'rules' => 'required|verificar_hr',
                    'errors' => [
                        'required' => 'Debe seleccionar la Hoja de Ruta Madre.',
                        'verificar_hr' => 'La Hoja de Ruta Madre ya fue migrada.',
                    ]
                ],
                'domicilio_legal' => [
                    'rules' => 'required',
                ],
                'domicilio_procesal' => [
                    'rules' => 'required',
                ],
                'telefono_solicitante' => [
                    'rules' => 'required',
                ],
                'fk_estado_tramite' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar el Estado del Tramite.'
                    ]
                ],
                'observaciones' => [
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
                $contenido['hr_madre'] = $this->obtenerDatosHojaRutaMadre($this->request->getPost('fk_solicitud_licencia_contrato'));
                $contenido['id_estado_padre'] = $this->request->getPost('fk_estado_tramite');
                $contenido['estadosTramitesHijo'] = $this->obtenerEstadosTramitesHijo($this->request->getPost('fk_estado_tramite'));
                $contenido['id_estado_hijo'] = $this->request->getPost('fk_estado_tramite_hijo');
                $contenido['usu_destinatario'] = $this->obtenerUsuarioDestinatario($this->request->getPost('fk_usuario_destinatario'));
                $contenido['validation'] = $this->validator;
            }else{
                $estado = 'MIGRADO';
                $solicitudLicenciaContratoModel = new SolicitudLicenciaContratoModel();
                $actoAdministrativoModel = new ActoAdministrativoModel();
                $datosAreaMineraModel = new DatosAreaMineraModel();
                $derivacionModel = new DerivacionModel();
                $idSolicitudContrato = $this->request->getPost('fk_solicitud_licencia_contrato');
                $derivacionSincobolModel = new DerivacionSincobolModel();

                if($solicitudLicencia = $solicitudLicenciaContratoModel->find($idSolicitudContrato)){
                    $data = array(
                        'fk_solicitud_licencia_contrato' => $solicitudLicencia['id'],
                        'fk_area_minera' => $solicitudLicencia['fk_area_minera'],
                        'fk_hoja_ruta' => $solicitudLicencia['fk_hoja_ruta'],
                        'fk_oficina' => session()->get('registroOficina'),
                        'correlativo' => $solicitudLicencia['correlativo'],
                        'fecha_mecanizada' => $this->request->getPost('fecha_mecanizada'),
                        'fk_usuario_actual' => session()->get('registroUser'),
                        'ultimo_fk_usuario_responsable' => $this->request->getPost('fk_usuario_destinatario'),
                        'ultimo_estado' => $estado,
                        'ultimo_fk_estado_tramite_padre' => $this->request->getPost('fk_estado_tramite'),
                        'ultimo_fk_estado_tramite_hijo' => ((!empty($this->request->getPost('fk_estado_tramite_hijo'))) ? $this->request->getPost('fk_estado_tramite_hijo') : NULL),
                        'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                        'fk_usuario_creador' => session()->get('registroUser'),
                        'ultimo_instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                        'ultimo_fk_usuario_remitente' => session()->get('registroUser'),
                        'ultimo_fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                        'fk_tipo_hoja_ruta' => 1,
                        'estado_tramite_apm' => $this->obtenerEstadoTramiteAPM($this->request->getPost('fk_estado_tramite'), $this->request->getPost('fk_estado_tramite_hijo')),
                        'ultimo_recurso_jerarquico' => ($this->request->getPost('recurso_jerarquico') ? 'true' : 'false'),
                        'ultimo_recurso_revocatoria' => ($this->request->getPost('recurso_revocatoria') ? 'true' : 'false'),
                        'ultimo_oposicion' => ($this->request->getPost('oposicion') ? 'true' : 'false'),
                    );

                    if($actoAdministrativoModel->insert($data) === false){
                        session()->setFlashdata('fail', $actoAdministrativoModel->errors());
                    }else{
                        $idActoAdministrativo = $actoAdministrativoModel->getInsertID();
                        $dataDatosAreaMinera = array(
                            'fk_acto_administrativo' => $idActoAdministrativo,
                            'codigo_unico' => $this->request->getPost('codigo_unico'),
                            'denominacion' => $this->request->getPost('denominacion'),
                            'extension' => $this->request->getPost('extension'),
                            'departamentos' => $this->request->getPost('departamentos'),
                            'provincias' => $this->request->getPost('provincias'),
                            'municipios' => $this->request->getPost('municipios'),
                            'regional' => $this->request->getPost('regional'),
                            'area_protegida' => $this->request->getPost('area_protegida'),
                            'representante_legal' => $this->request->getPost('representante_legal'),
                            'nacionalidad' => $this->request->getPost('nacionalidad'),
                            'titular' => $this->request->getPost('titular'),
                            'clasificacion_titular' => $this->request->getPost('clasificacion'),
                        );

                        if($datosAreaMineraModel->insert($dataDatosAreaMinera) === false)
                            session()->setFlashdata('fail', $datosAreaMineraModel->errors());

                        $dataDerivacion = array(
                            'fk_acto_administrativo' => $idActoAdministrativo,
                            'domicilio_legal' => mb_strtoupper($this->request->getPost('domicilio_legal')),
                            'domicilio_procesal' => mb_strtoupper($this->request->getPost('domicilio_procesal')),
                            'telefono_solicitante' => mb_strtoupper($this->request->getPost('telefono_solicitante')),
                            'fk_estado_tramite_padre' => $this->request->getPost('fk_estado_tramite'),
                            'fk_estado_tramite_hijo' => ((!empty($this->request->getPost('fk_estado_tramite_hijo'))) ? $this->request->getPost('fk_estado_tramite_hijo') : NULL),
                            'observaciones' => mb_strtoupper($this->request->getPost('observaciones')),
                            'fk_usuario_remitente' => session()->get('registroUser'),
                            'fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                            'fk_usuario_responsable' => $this->request->getPost('fk_usuario_destinatario'),
                            'instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                            'recurso_jerarquico' => ($this->request->getPost('recurso_jerarquico') ? 'true' : 'false'),
                            'recurso_revocatoria' => ($this->request->getPost('recurso_revocatoria') ? 'true' : 'false'),
                            'oposicion' => ($this->request->getPost('oposicion') ? 'true' : 'false'),
                            'estado' => $estado,
                            'fk_usuario_creador' => session()->get('registroUser'),
                        );
                        if($derivacionModel->insert($dataDerivacion) === false){
                            session()->setFlashdata('fail', $derivacionModel->errors());
                        }else{
                            $campos = 'id, fk_hoja_ruta';
                            $where = "tipo_documento_derivado = 'ORIGINAL' AND fk_hoja_ruta = ".$solicitudLicencia['fk_hoja_ruta'];
                            $ultima_derivacion = $derivacionSincobolModel->select($campos)->where($where)->orderBY('id', 'DESC')->first();
                            $dataDerivacionSincobol = array(
                                'id' => $ultima_derivacion['id'],
                                'estado' => 'CONCLUIDO',
                                'fecha_conclusion' => date('Y-m-d h:i:s'),
                                'motivo_conclusion' => 'MIGRADO AL SISTEMA DE CONTROL Y SEGUIMIENTO DE TRAMITES',
                            );
                            if($derivacionSincobolModel->save($dataDerivacionSincobol) === false){
                                session()->setFlashdata('fail', $derivacionSincobolModel->errors());
                            }
                            $solicitudLicencia['fk_acto_administrativo'] = $idActoAdministrativo;
                            if($solicitudLicenciaContratoModel->save($solicitudLicencia) === false)
                                session()->setFlashdata('fail', $solicitudLicenciaContratoModel->errors());
                            else
                                session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información.');

                        }
                    }
                }
                return redirect()->to($this->controlador.'mis_tramites');
            }
        }
        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Migrar Contrato Administrativo Minero - SINCOBOL';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['estadosTramites'] = $estadosTramites;
        $contenido['subtitulo'] = 'Migrar Contrato Administrativo Minero - SINCOBOL';
        $contenido['accion'] = $this->controlador.'agregar';
        $contenido['controlador'] = $this->controlador;
        $data['content'] = view($this->carpeta.'agregar', $contenido);
        $data['menu_actual'] = $this->menuActual.'agregar';
        $data['validacion_js'] = 'cam-agregar-validation.js';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['alertas'] = $this->alertasTramites();
        echo view('templates/template', $data);
    }
    public function agregarCmnCmc(){
        $estadosTramites = $this->obtenerEstadosTramites($this->idTramite);
        if ($this->request->getPost()) {
            $validation = $this->validate([
                'fk_hoja_ruta' => [
                    'rules' => 'required|verificar_hr_cmn_cmc',
                    'errors' => [
                        'required' => 'Debe seleccionar la Hoja de Ruta.',
                        'verificar_hr_cmn_cmc' => 'La Hoja de Ruta ya fue migrada.',
                    ]
                ],
                'fk_area_minera' => [
                    'rules' => 'required|verificar_area_minera_cmn_cmc',
                    'errors' => [
                        'required' => 'Debe seleccionar el Área minera.',
                        'verificar_area_minera_cmn_cmc' => 'El Área Minera ya fue migrada.',
                    ]
                ],
                'domicilio_legal' => [
                    'rules' => 'required',
                ],
                'domicilio_procesal' => [
                    'rules' => 'required',
                ],
                'telefono_solicitante' => [
                    'rules' => 'required',
                ],
                'fk_estado_tramite' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar el Estado del Tramite.'
                    ]
                ],
                'observaciones' => [
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
                $contenido['hr_cmn_cmc'] = $this->obtenerDatosHojaRutaCmnCmc($this->request->getPost('fk_hoja_ruta'));
                $contenido['area_minera'] = $this->obtenerDatosAreaMineraCmnCmc($this->request->getPost('fk_area_minera'));
                $contenido['id_estado_padre'] = $this->request->getPost('fk_estado_tramite');
                $contenido['estadosTramitesHijo'] = $this->obtenerEstadosTramitesHijo($this->request->getPost('fk_estado_tramite'));
                $contenido['id_estado_hijo'] = $this->request->getPost('fk_estado_tramite_hijo');
                $contenido['usu_destinatario'] = $this->obtenerUsuarioDestinatario($this->request->getPost('fk_usuario_destinatario'));
                $contenido['validation'] = $this->validator;
            }else{
                $estado = 'MIGRADO';
                $actoAdministrativoModel = new ActoAdministrativoModel();
                $datosAreaMineraModel = new DatosAreaMineraModel();
                $derivacionModel = new DerivacionModel();
                $derivacionSincobolModel = new DerivacionSincobolModel();
                $hoja_ruta = $this->obtenerDatosHojaRutaCmnCmc($this->request->getPost('fk_hoja_ruta'));
                $fk_tipo_hoja_ruta = 0;
                if(strpos($hoja_ruta['correlativo'],'CMN'))
                    $fk_tipo_hoja_ruta = 2;
                elseif(strpos($hoja_ruta['correlativo'],'CMC'))
                    $fk_tipo_hoja_ruta = 3;
                $data = array(
                    'fk_area_minera' => $this->request->getPost('fk_area_minera'),
                    'fk_hoja_ruta' => $this->request->getPost('fk_hoja_ruta'),
                    'fk_oficina' => session()->get('registroOficina'),
                    'correlativo' => $hoja_ruta['correlativo'],
                    'fecha_mecanizada' => $this->request->getPost('fecha_mecanizada'),
                    'fk_usuario_actual' => session()->get('registroUser'),
                    'ultimo_fk_usuario_responsable' => $this->request->getPost('fk_usuario_destinatario'),
                    'ultimo_estado' => $estado,
                    'ultimo_fk_estado_tramite_padre' => $this->request->getPost('fk_estado_tramite'),
                    'ultimo_fk_estado_tramite_hijo' => ((!empty($this->request->getPost('fk_estado_tramite_hijo'))) ? $this->request->getPost('fk_estado_tramite_hijo') : NULL),
                    'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                    'fk_usuario_creador' => session()->get('registroUser'),
                    'ultimo_instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                    'ultimo_fk_usuario_remitente' => session()->get('registroUser'),
                    'ultimo_fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                    'fk_tipo_hoja_ruta' => $fk_tipo_hoja_ruta,
                );

                if($actoAdministrativoModel->insert($data) === false){
                    session()->setFlashdata('fail', $actoAdministrativoModel->errors());
                }else{
                    $idActoAdministrativo = $actoAdministrativoModel->getInsertID();
                    $dataDatosAreaMinera = array(
                        'fk_acto_administrativo' => $idActoAdministrativo,
                        'codigo_unico' => $this->request->getPost('codigo_unico'),
                        'denominacion' => $this->request->getPost('denominacion'),
                        'extension' => $this->request->getPost('extension'),
                        'departamentos' => $this->request->getPost('departamentos'),
                        'provincias' => $this->request->getPost('provincias'),
                        'municipios' => $this->request->getPost('municipios'),
                        'regional' => $this->request->getPost('regional'),
                        'area_protegida' => $this->request->getPost('area_protegida'),
                        'representante_legal' => $this->request->getPost('representante_legal'),
                        'nacionalidad' => $this->request->getPost('nacionalidad'),
                        'titular' => $this->request->getPost('titular'),
                        'clasificacion_titular' => $this->request->getPost('clasificacion'),
                    );

                    if($datosAreaMineraModel->insert($dataDatosAreaMinera) === false)
                        session()->setFlashdata('fail', $datosAreaMineraModel->errors());

                    $dataDerivacion = array(
                        'fk_acto_administrativo' => $idActoAdministrativo,
                        'domicilio_legal' => mb_strtoupper($this->request->getPost('domicilio_legal')),
                        'domicilio_procesal' => mb_strtoupper($this->request->getPost('domicilio_procesal')),
                        'telefono_solicitante' => mb_strtoupper($this->request->getPost('telefono_solicitante')),
                        'fk_estado_tramite_padre' => $this->request->getPost('fk_estado_tramite'),
                        'fk_estado_tramite_hijo' => ((!empty($this->request->getPost('fk_estado_tramite_hijo'))) ? $this->request->getPost('fk_estado_tramite_hijo') : NULL),
                        'observaciones' => mb_strtoupper($this->request->getPost('observaciones')),
                        'fk_usuario_remitente' => session()->get('registroUser'),
                        'fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                        'fk_usuario_responsable' => $this->request->getPost('fk_usuario_destinatario'),
                        'instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                        'estado' => $estado,
                        'fk_usuario_creador' => session()->get('registroUser'),
                    );
                    if($derivacionModel->insert($dataDerivacion) === false){
                        session()->setFlashdata('fail', $derivacionModel->errors());
                    }else{
                        $campos = 'id, fk_hoja_ruta';
                        $where = "fk_hoja_ruta = ".$this->request->getPost('fk_hoja_ruta');
                        $ultima_derivacion = $derivacionSincobolModel->select($campos)->where($where)->orderBY('id', 'DESC')->first();
                        $dataDerivacionSincobol = array(
                            'id' => $ultima_derivacion['id'],
                            'estado' => 'CONCLUIDO',
                            'fecha_conclusion' => date('Y-m-d h:i:s'),
                            'motivo_conclusion' => 'MIGRADO AL SISTEMA DE CONTROL Y SEGUIMIENTO DE TRAMITES',
                        );
                        if($derivacionSincobolModel->save($dataDerivacionSincobol) === false){
                            session()->setFlashdata('fail', $derivacionSincobolModel->errors());
                        }
                        session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información.');
                    }
                }

                return redirect()->to($this->controlador.'mis_tramites');
            }
        }
        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Migrar CMN/CMC - SINCOBOL';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['estadosTramites'] = $estadosTramites;
        $contenido['subtitulo'] = 'Migrar CMN/CMC - SINCOBOL';
        $contenido['accion'] = $this->controlador.'agregar_cmn_cmc';
        $contenido['controlador'] = $this->controlador;
        $data['content'] = view($this->carpeta.'agregar_cmn_cmc', $contenido);
        $data['menu_actual'] = $this->menuActual.'agregar_cmn_cmc';
        $data['validacion_js'] = 'cam-agregar-cmc-cmn-validation.js';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['alertas'] = $this->alertasTramites();
        echo view('templates/template', $data);
    }

    public function atender($id){
        $db = \Config\Database::connect();
        $campos = array('ac.id', 'ac.fk_solicitud_licencia_contrato', 'ac.fk_hoja_ruta', 'ac.fk_area_minera', 'ac.fk_tipo_hoja_ruta', 'ac.correlativo', 'ac.ultimo_instruccion',
        'ac.ultimo_fk_estado_tramite_padre','ac.ultimo_fk_estado_tramite_hijo', 'ac.ultimo_fk_usuario_responsable', 'dam.area_protegida_adicional', 'dam.representante_legal', 'dam.nacionalidad');
        $where = array(
            'ac.deleted_at' => NULL,
            'ac.fk_usuario_actual' => session()->get('registroUser'),
            'ac.id' => $id
        );
        $builder = $db->table('public.acto_administrativo as ac')
        ->select($campos)
        ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
        ->where($where)
        ->orderBY('ac.id', 'DESC');
        if($fila = $builder->get()->getRowArray()){
            $campos = array(
                'der.id', 'der.domicilio_legal', 'der.domicilio_procesal', 'der.telefono_solicitante', 'der.observaciones as ultima_observacion',
                'ur.nombre_completo as ultimo_remitente', 'per.nombre as ultimo_cargo', "to_char(der.created_at, 'DD/MM/YYYY') as ultimo_fecha_derivacion",
                'der.instruccion as ultimo_instruccion',
                "CASE WHEN der.fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'. ',etp.nombre,' - ',etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE CONCAT(etp.orden,'. ',etp.nombre) END as estado_actual_tramite",
                'der.fk_estado_tramite_padre', 'der.fk_estado_tramite_hijo', "CONCAT(urt.nombre_completo,' - ',prt.nombre) as usuario_responsable",
                'etp.anexar_documentos as anexar_documentos_padre', 'eth.anexar_documentos as anexar_documentos_hijo',
                'der.recurso_jerarquico', 'der.recurso_revocatoria', 'der.oposicion'
            );
            $where = array(
                'der.deleted_at' => NULL,
                'der.fk_acto_administrativo' => $fila['id'],
            );
            $query = $db->table('derivacion as der')
            ->select($campos)
            ->join('usuarios as ur', 'der.fk_usuario_remitente = ur.id', 'left')
            ->join('perfiles as per', 'ur.fk_perfil = per.id', 'left')
            ->join('usuarios as urt', 'der.fk_usuario_responsable = urt.id', 'left')
            ->join('perfiles as prt', 'urt.fk_perfil = prt.id', 'left')
            ->join('estado_tramite as etp', 'der.fk_estado_tramite_padre = etp.id', 'left')
            ->join('estado_tramite as eth', 'der.fk_estado_tramite_hijo = eth.id', 'left')
            ->where($where)
            ->orderBY('der.id', 'DESC');
            $ultima_derivacion = $query->get()->getFirstRow('array');

            if($fila['fk_tipo_hoja_ruta'] == 1)
                $datos = $this->informacionAreaMinera($fila['fk_solicitud_licencia_contrato']);
            elseif($fila['fk_tipo_hoja_ruta'] == 2 || $fila['fk_tipo_hoja_ruta'] == 3)
                $datos = array_merge($this->informacionHRCmnCmc($fila['fk_hoja_ruta']), $this->informacionAreaMineraCmnCmc($fila['fk_area_minera']));

            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Atender Tramite';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['id'] = $fila['id'];
            $contenido['correlativo'] = $fila['correlativo'];
            $contenido['codigo_unico'] = $datos['codigo_unico'];
            $contenido['denominacion'] = $datos['denominacion'];
            $contenido['fecha_mecanizada'] = $datos['fecha_mecanizada'];
            $contenido['responsable_tramite'] = $ultima_derivacion['usuario_responsable'];
            $contenido['estado_actual_tramite'] = $ultima_derivacion['estado_actual_tramite'];
            $contenido['fila'] = $fila;
            $contenido['datos'] = $datos;
            $contenido['ultima_derivacion'] = $ultima_derivacion;
            $contenido['estadosTramites'] = $this->obtenerEstadosTramites($this->idTramite);
            $contenido['id_estado_padre'] = $ultima_derivacion['fk_estado_tramite_padre'];
            if($ultima_derivacion['fk_estado_tramite_hijo']){
                $contenido['estadosTramitesHijo'] = $this->obtenerEstadosTramitesHijo($ultima_derivacion['fk_estado_tramite_padre']);
                $contenido['id_estado_hijo'] = $ultima_derivacion['fk_estado_tramite_hijo'];
                $contenido['anexar_documentos'] = $ultima_derivacion['anexar_documentos_hijo'];
            }else{
                $contenido['anexar_documentos'] = $ultima_derivacion['anexar_documentos_padre'];
            }
            $contenido['documentos'] = $this->obtenerDocumentosAtender($fila['id']);
            $contenido['correspondencia_externa'] = $this->informacionHRExterna($fila['id']);
            $contenido['subtitulo'] = 'Atender Tramite';
            $contenido['accion'] = $this->controlador.'guardar_atender';
            $contenido['ruta_archivos'] = $this->rutaArchivos;
            $contenido['controlador'] = $this->controlador;
            $contenido['id_tramite'] = $this->idTramite;
            $data['content'] = view($this->carpeta.'atender', $contenido);
            $data['menu_actual'] = $this->menuActual.'mis_tramites';
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['alertas'] = $this->alertasTramites();
            $data['validacion_js'] = 'cam-atender-validation.js';
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador);
        }
    }
    public function guardarAtender(){
        if ($this->request->getPost()) {
            $id = $this->request->getPost('id');
            $documentos = $this->obtenerDocumentosAtender($id);
            $correspondencia_externa = $this->informacionHRExterna($id);
            $validation = $this->validate([
                'id' => [
                    'rules' => 'required',
                ],
                'id_derivacion' => [
                    'rules' => 'required',
                ],
                'representante_legal' => [
                    'rules' => 'required',
                ],
                'nacionalidad' => [
                    'rules' => 'required',
                ],
                'domicilio_legal' => [
                    'rules' => 'required',
                ],
                'domicilio_procesal' => [
                    'rules' => 'required',
                ],
                'telefono_solicitante' => [
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
                if($correspondencia_externa && count($correspondencia_externa) > 0){
                    $contenido['atendido'] = $this->request->getPost('atendido');
                    $contenido['observaciones_ce'] = $this->request->getPost('observaciones_ce');
                }

                $cabera['titulo'] = $this->titulo;
                $cabera['navegador'] = true;
                $cabera['subtitulo'] = 'Atender Tramite';
                $contenido['correlativo'] = $this->request->getPost('correlativo');
                $contenido['codigo_unico'] = $this->request->getPost('codigo_unico');
                $contenido['denominacion'] = $this->request->getPost('denominacion');
                $contenido['fecha_mecanizada'] = $this->request->getPost('fecha_mecanizada');
                $contenido['responsable_tramite'] = $this->request->getPost('responsable_tramite');
                $contenido['estado_actual_tramite'] = $this->request->getPost('estado_actual_tramite');
                $contenido['id_estado_padre'] = $this->request->getPost('fk_estado_tramite');
                $contenido['estadosTramitesHijo'] = $this->obtenerEstadosTramitesHijo($this->request->getPost('fk_estado_tramite'));
                $contenido['id_estado_hijo'] = $this->request->getPost('fk_estado_tramite_hijo');
                $contenido['documentos'] = $documentos;
                $contenido['correspondencia_externa'] = $correspondencia_externa;
                $contenido['usu_destinatario'] = $this->obtenerUsuarioDestinatario($this->request->getPost('fk_usuario_destinatario'));
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['id'] = $id;
                $contenido['estadosTramites'] = $this->obtenerEstadosTramites($this->idTramite);
                $contenido['subtitulo'] = 'Atender Tramite';
                $contenido['accion'] = $this->controlador.'guardar_atender';
                $contenido['validation'] = $this->validator;
                $contenido['ruta_archivos'] = $this->rutaArchivos;
                $contenido['controlador'] = $this->controlador;
                $contenido['id_tramite'] = $this->idTramite;
                $data['content'] = view($this->carpeta.'atender', $contenido);
                $data['menu_actual'] = $this->menuActual.'mis_tramites';
                $data['tramites_menu'] = $this->tramitesMenu();
                $data['alertas'] = $this->alertasTramites();
                $data['validacion_js'] = 'cam-atender-validation.js';
                echo view('templates/template', $data);
            }else{
                $estado = 'DERIVADO';
                $actoAdministrativoModel = new ActoAdministrativoModel();
                $datosAreaMineraModel = new DatosAreaMineraModel();
                $derivacionModel = new DerivacionModel();
                $documentosModel = new DocumentosModel();
                $correspondenciaExternaModel = new CorrespondenciaExternaModel();

                $data = array(
                    'id' => $id,
                    'ultimo_estado' => $estado,
                    'ultimo_fk_estado_tramite_padre' => $this->request->getPost('fk_estado_tramite'),
                    'ultimo_fk_estado_tramite_hijo' => ((!empty($this->request->getPost('fk_estado_tramite_hijo'))) ? $this->request->getPost('fk_estado_tramite_hijo') : NULL),
                    'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                    'ultimo_instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                    'ultimo_fk_usuario_remitente' => session()->get('registroUser'),
                    'ultimo_fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                    'ultimo_fk_usuario_responsable'=>($this->request->getPost('responsable') ? $this->request->getPost('fk_usuario_destinatario') : $this->request->getPost('ultimo_fk_usuario_responsable')),
                    'editar' => 'true',
                    'ultimo_recurso_jerarquico' => ($this->request->getPost('recurso_jerarquico') ? 'true' : 'false'),
                    'ultimo_recurso_revocatoria' => ($this->request->getPost('recurso_revocatoria') ? 'true' : 'false'),
                    'ultimo_oposicion' => ($this->request->getPost('oposicion') ? 'true' : 'false'),
                );
                if(count($documentos)>0){
                    $ultimo_fk_documentos = '';
                    foreach($documentos as $row)
                        $ultimo_fk_documentos .= $row['id'].',';
                    $data['ultimo_fk_documentos'] = substr($ultimo_fk_documentos, 0, -1);
                }
                if(in_array(10, session()->get('registroPermisos'))){
                    $fila = $actoAdministrativoModel->find($id);
                    $data['estado_tramite_apm'] = $this->obtenerEstadoTramiteAPM($this->request->getPost('fk_estado_tramite'), $this->request->getPost('fk_estado_tramite_hijo'));
                    if($fila['ultimo_estado'] == 'RECIBIDO')
                        $data['documentos_apm'] = $this->obtenerDocumentosAPM($id);
                }
                if($actoAdministrativoModel->save($data) === false){
                    session()->setFlashdata('fail', $actoAdministrativoModel->errors());
                }else{

                    $dataDatosAreaMinera = array(
                        'fk_acto_administrativo' => $id,
                        'extension' => $this->request->getPost('extension'),
                        'departamentos' => $this->request->getPost('departamentos'),
                        'provincias' => $this->request->getPost('provincias'),
                        'municipios' => $this->request->getPost('municipios'),
                        'regional' => $this->request->getPost('regional'),
                        'area_protegida' => $this->request->getPost('area_protegida'),
                        'area_protegida_adicional' => mb_strtoupper($this->request->getPost('area_protegida_adicional')),
                        'representante_legal' => mb_strtoupper($this->request->getPost('representante_legal')),
                        'nacionalidad' => mb_strtoupper($this->request->getPost('nacionalidad')),
                        'titular' => $this->request->getPost('titular'),
                        'clasificacion_titular' => $this->request->getPost('clasificacion'),
                        'the_geom' => $this->obtenerPoligonoAreaMinera($this->request->getPost('fk_area_minera')),
                    );

                    if($datosAreaMineraModel->save($dataDatosAreaMinera) === false)
                        session()->setFlashdata('fail', $datosAreaMineraModel->errors());

                    if($correspondencia_externa && count($correspondencia_externa)>0){
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
                    }

                    $dataDerivacion = array(
                        'fk_acto_administrativo' => $id,
                        'domicilio_legal' => mb_strtoupper($this->request->getPost('domicilio_legal')),
                        'domicilio_procesal' => mb_strtoupper($this->request->getPost('domicilio_procesal')),
                        'telefono_solicitante' => mb_strtoupper($this->request->getPost('telefono_solicitante')),
                        'fk_estado_tramite_padre' => $this->request->getPost('fk_estado_tramite'),
                        'fk_estado_tramite_hijo' => ((!empty($this->request->getPost('fk_estado_tramite_hijo'))) ? $this->request->getPost('fk_estado_tramite_hijo') : NULL),
                        'observaciones' => mb_strtoupper($this->request->getPost('observaciones')),
                        'fk_usuario_remitente' => session()->get('registroUser'),
                        'fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                        'instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                        'recurso_jerarquico' => ($this->request->getPost('recurso_jerarquico') ? 'true' : 'false'),
                        'recurso_revocatoria' => ($this->request->getPost('recurso_revocatoria') ? 'true' : 'false'),
                        'oposicion' => ($this->request->getPost('oposicion') ? 'true' : 'false'),
                        'estado' => $estado,
                        'fk_usuario_creador' => session()->get('registroUser'),
                        'fk_usuario_responsable'=>($this->request->getPost('responsable') ? $this->request->getPost('fk_usuario_destinatario') : $this->request->getPost('ultimo_fk_usuario_responsable')),
                    );

                    if($derivacionModel->insert($dataDerivacion) === false){
                        session()->setFlashdata('fail', $derivacionModel->errors());
                    }else{
                        $id_derivacion = $derivacionModel->getInsertID();
                        if(count($documentos)>0){
                            foreach($documentos as $documento){
                                $dataDocumento = array(
                                    'id' => $documento['id'],
                                    'estado' => 'ANEXADO',
                                    'fk_derivacion' => $id_derivacion,
                                    'fecha_notificacion'=>((!empty($this->request->getPost('fecha_notificacion'.$documento['id']))) ? $this->request->getPost('fecha_notificacion'.$documento['id']) : NULL),
                                );
                                if($documentosModel->save($dataDocumento) === false){
                                    session()->setFlashdata('fail', $documentosModel->errors());
                                }
                            }
                        }

                        $dataDerivacionActualizacion = array(
                            'id' => $this->request->getPost('id_derivacion'),
                            'estado' => 'ATENDIDO',
                            'fecha_atencion' => date('Y-m-d H:i:s'),
                        );
                        if($derivacionModel->save($dataDerivacionActualizacion) === false)
                            session()->setFlashdata('fail', $derivacionModel->errors());
                        else
                            session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información.');
                    }
                    if(in_array(10, session()->get('registroPermisos')))
                        return redirect()->to($this->controlador.'subir_documentos/'.$id);
                    else
                        return redirect()->to($this->controlador.'mis_tramites');
                }
            }
        }
    }
    public function editar($id){
        $db = \Config\Database::connect();
        $campos = array('ac.id', 'ac.fk_solicitud_licencia_contrato', 'ac.fk_hoja_ruta', 'ac.fk_area_minera', 'ac.fk_tipo_hoja_ruta', 'ac.correlativo', 'ac.ultimo_instruccion',
        'ac.ultimo_fk_estado_tramite_padre','ac.ultimo_fk_estado_tramite_hijo', 'ac.ultimo_fk_usuario_responsable', 'dam.area_protegida_adicional', 'dam.representante_legal', 'dam.nacionalidad');
        $where = array(
            'ac.deleted_at' => NULL,
            'ac.fk_usuario_actual' => session()->get('registroUser'),
            'ac.id' => $id,
            'ac.editar' => TRUE,
        );
        $builder = $db->table('public.acto_administrativo as ac')
        ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
        ->select($campos)
        ->where($where)
        ->orderBY('ac.id', 'DESC');
        if($fila = $builder->get()->getRowArray()){
            $campos = array(
                'der.id', 'der.domicilio_legal', 'der.domicilio_procesal', 'der.telefono_solicitante', 'der.fk_estado_tramite_padre', 'der.fk_estado_tramite_hijo',
                'der.observaciones', 'der.fk_usuario_destinatario', 'der.instruccion', 'der.motivo_anexo', 'etp.anexar_documentos as anexar_documentos_padre',
                'eth.anexar_documentos as anexar_documentos_hijo', 'der.recurso_jerarquico', 'der.recurso_revocatoria', 'der.oposicion',
                "CASE WHEN der.fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'. ',etp.nombre,' - ',etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE CONCAT(etp.orden,'. ',etp.nombre) END as estado_actual_tramite",
                "CONCAT(urt.nombre_completo,' - ',prt.nombre) as usuario_responsable",
            );
            $where = array(
                'der.fk_acto_administrativo' => $fila['id'],
            );
            $query = $db->table('derivacion as der')
            ->select($campos)
            ->join('usuarios as urt', 'der.fk_usuario_responsable = urt.id', 'left')
            ->join('perfiles as prt', 'urt.fk_perfil = prt.id', 'left')
            ->join('estado_tramite as etp', 'der.fk_estado_tramite_padre = etp.id', 'left')
            ->join('estado_tramite as eth', 'der.fk_estado_tramite_hijo = eth.id', 'left')
            ->where($where)
            ->orderBY('der.id', 'DESC');
            $derivacion = $query->get()->getFirstRow('array');

            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Editar Derivación';
            $contenido['title'] = view('templates/title',$cabera);

            if($fila['fk_tipo_hoja_ruta'] == 1)
                $datos = $this->informacionAreaMinera($fila['fk_solicitud_licencia_contrato']);
            elseif($fila['fk_tipo_hoja_ruta'] == 2 || $fila['fk_tipo_hoja_ruta'] == 3)
                $datos = array_merge($this->informacionHRCmnCmc($fila['fk_hoja_ruta']), $this->informacionAreaMineraCmnCmc($fila['fk_area_minera']));

            $contenido['id'] = $fila['id'];
            $contenido['correlativo'] = $fila['correlativo'];
            $contenido['codigo_unico'] = $datos['codigo_unico'];
            $contenido['denominacion'] = $datos['denominacion'];
            $contenido['fecha_mecanizada'] = $datos['fecha_mecanizada'];
            $contenido['responsable_tramite'] = $derivacion['usuario_responsable'];
            $contenido['estado_actual_tramite'] = $derivacion['estado_actual_tramite'];
            $contenido['fila'] = $fila;
            $contenido['datos'] = $datos;
            $contenido['derivacion'] = $derivacion;
            $contenido['estadosTramites'] = $this->obtenerEstadosTramites($this->idTramite);
            $contenido['id_estado_padre'] = $derivacion['fk_estado_tramite_padre'];
            if($derivacion['fk_estado_tramite_hijo']){
                $contenido['estadosTramitesHijo'] = $this->obtenerEstadosTramitesHijo($derivacion['fk_estado_tramite_padre']);
                $contenido['id_estado_hijo'] = $derivacion['fk_estado_tramite_hijo'];
                $contenido['anexar_documentos'] = $derivacion['anexar_documentos_hijo'];
            }else{
                $contenido['anexar_documentos'] = $derivacion['anexar_documentos_padre'];
            }
            $contenido['documentos'] = $this->obtenerDocumentosEditar($fila['id'], $derivacion['id']);
            $contenido['usu_destinatario'] = $this->obtenerUsuarioDestinatario($derivacion['fk_usuario_destinatario']);
            $contenido['subtitulo'] = 'Editar Derivación';
            $contenido['accion'] = $this->controlador.'guardar_editar';
            $contenido['ruta_archivos'] = $this->rutaArchivos;
            $contenido['controlador'] = $this->controlador;
            $contenido['id_tramite'] = $this->idTramite;
            $data['content'] = view($this->carpeta.'editar', $contenido);
            $data['menu_actual'] = $this->menuActual.'mis_tramites';
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['alertas'] = $this->alertasTramites();
            $data['validacion_js'] = 'cam-editar-validation.js';
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador);
        }
    }
    public function guardarEditar(){
        if ($this->request->getPost()) {
            $id = $this->request->getPost('id');
            $id_derivacion = $this->request->getPost('id_derivacion');
            $documentos =  $this->obtenerDocumentosEditar($id, $id_derivacion);
            $validation = $this->validate([
                'id' => [
                    'rules' => 'required',
                ],
                'id_derivacion' => [
                    'rules' => 'required',
                ],
                'representante_legal' => [
                    'rules' => 'required',
                ],
                'nacionalidad' => [
                    'rules' => 'required',
                ],
                'domicilio_legal' => [
                    'rules' => 'required',
                ],
                'domicilio_procesal' => [
                    'rules' => 'required',
                ],
                'telefono_solicitante' => [
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
                $cabera['navegador'] = true;
                $cabera['subtitulo'] = 'Editar Derivación';
                $contenido['correlativo'] = $this->request->getPost('correlativo');
                $contenido['codigo_unico'] = $this->request->getPost('codigo_unico');
                $contenido['denominacion'] = $this->request->getPost('denominacion');
                $contenido['fecha_mecanizada'] = $this->request->getPost('fecha_mecanizada');
                $contenido['responsable_tramite'] = $this->request->getPost('responsable_tramite');
                $contenido['estado_actual_tramite'] = $this->request->getPost('estado_actual_tramite');
                $contenido['id_estado_padre'] = $this->request->getPost('fk_estado_tramite');
                $contenido['estadosTramitesHijo'] = $this->obtenerEstadosTramitesHijo($this->request->getPost('fk_estado_tramite'));
                $contenido['id_estado_hijo'] = $this->request->getPost('fk_estado_tramite_hijo');
                $contenido['documentos'] = $documentos;
                $contenido['usu_destinatario'] = $this->obtenerUsuarioDestinatario($this->request->getPost('fk_usuario_destinatario'));
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['id'] = $id;
                $contenido['estadosTramites'] = $this->obtenerEstadosTramites($this->idTramite);
                $contenido['subtitulo'] = 'Editar Derivación';
                $contenido['accion'] = $this->controlador.'guardar_editar';
                $contenido['validation'] = $this->validator;
                $contenido['ruta_archivos'] = $this->rutaArchivos;
                $contenido['controlador'] = $this->controlador;
                $contenido['id_tramite'] = $this->idTramite;
                $data['content'] = view($this->carpeta.'editar', $contenido);
                $data['menu_actual'] = $this->menuActual.'mis_tramites';
                $data['tramites_menu'] = $this->tramitesMenu();
                $data['alertas'] = $this->alertasTramites();
                $data['validacion_js'] = 'cam-editar-validation.js';
                echo view('templates/template', $data);
            }else{
                $estado = 'DERIVADO';
                $actoAdministrativoModel = new ActoAdministrativoModel();
                $datosAreaMineraModel = new DatosAreaMineraModel();
                $derivacionModel = new DerivacionModel();
                $documentosModel = new DocumentosModel();
                $motivo_anexo = mb_strtoupper($this->request->getPost('motivo_anexo'));

                $data = array(
                    'id' => $id,
                    'ultimo_estado' => $estado,
                    'ultimo_fk_estado_tramite_padre' => $this->request->getPost('fk_estado_tramite'),
                    'ultimo_fk_estado_tramite_hijo' => ((!empty($this->request->getPost('fk_estado_tramite_hijo'))) ? $this->request->getPost('fk_estado_tramite_hijo') : NULL),
                    'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                    'ultimo_instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                    'ultimo_fk_usuario_remitente' => session()->get('registroUser'),
                    'ultimo_fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                    'ultimo_fk_usuario_responsable'=>($this->request->getPost('responsable') ? $this->request->getPost('fk_usuario_destinatario') : $this->request->getPost('ultimo_fk_usuario_responsable')),
                    'editar' => 'false',
                    'ultimo_recurso_jerarquico' => ($this->request->getPost('recurso_jerarquico') ? 'true' : 'false'),
                    'ultimo_recurso_revocatoria' => ($this->request->getPost('recurso_revocatoria') ? 'true' : 'false'),
                    'ultimo_oposicion' => ($this->request->getPost('oposicion') ? 'true' : 'false'),
                );

                if(in_array(10, session()->get('registroPermisos'))){
                    $data['estado_tramite_apm'] = $this->obtenerEstadoTramiteAPM($this->request->getPost('fk_estado_tramite'), $this->request->getPost('fk_estado_tramite_hijo'));
                    //$data['documentos_apm'] = $this->obtenerDocumentosAPM($id);
                }

                if($actoAdministrativoModel->save($data) === false){
                    session()->setFlashdata('fail', $actoAdministrativoModel->errors());
                }else{

                    $dataDatosAreaMinera = array(
                        'fk_acto_administrativo' => $id,
                        'extension' => $this->request->getPost('extension'),
                        'departamentos' => $this->request->getPost('departamentos'),
                        'provincias' => $this->request->getPost('provincias'),
                        'municipios' => $this->request->getPost('municipios'),
                        'regional' => $this->request->getPost('regional'),
                        'area_protegida' => $this->request->getPost('area_protegida'),
                        'area_protegida_adicional' => mb_strtoupper($this->request->getPost('area_protegida_adicional')),
                        'representante_legal' => $this->request->getPost('representante_legal'),
                        'nacionalidad' => $this->request->getPost('nacionalidad'),
                        'titular' => $this->request->getPost('titular'),
                        'clasificacion_titular' => $this->request->getPost('clasificacion'),
                        'the_geom' => $this->obtenerPoligonoAreaMinera($this->request->getPost('fk_area_minera')),
                    );

                    if($datosAreaMineraModel->save($dataDatosAreaMinera) === false)
                        session()->setFlashdata('fail', $datosAreaMineraModel->errors());

                    $dataDerivacion = array(
                        'id' => $id_derivacion,
                        'domicilio_legal' => mb_strtoupper($this->request->getPost('domicilio_legal')),
                        'domicilio_procesal' => mb_strtoupper($this->request->getPost('domicilio_procesal')),
                        'telefono_solicitante' => mb_strtoupper($this->request->getPost('telefono_solicitante')),
                        'fk_estado_tramite_padre' => $this->request->getPost('fk_estado_tramite'),
                        'fk_estado_tramite_hijo' => ((!empty($this->request->getPost('fk_estado_tramite_hijo'))) ? $this->request->getPost('fk_estado_tramite_hijo') : NULL),
                        'observaciones' => mb_strtoupper($this->request->getPost('observaciones')),
                        'fk_usuario_remitente' => session()->get('registroUser'),
                        'fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                        'instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                        'motivo_anexo' => $motivo_anexo,
                        'recurso_jerarquico' => ($this->request->getPost('recurso_jerarquico') ? 'true' : 'false'),
                        'recurso_revocatoria' => ($this->request->getPost('recurso_revocatoria') ? 'true' : 'false'),
                        'oposicion' => ($this->request->getPost('oposicion') ? 'true' : 'false'),
                        'estado' => $estado,
                        'fk_usuario_creador' => session()->get('registroUser'),
                        'fk_usuario_responsable'=>($this->request->getPost('responsable') ? $this->request->getPost('fk_usuario_destinatario') : $this->request->getPost('ultimo_fk_usuario_responsable')),
                    );

                    if($derivacionModel->save($dataDerivacion) === false){
                        session()->setFlashdata('fail', $derivacionModel->errors());
                    }else{

                        if(count($documentos)>0){
                            foreach($documentos as $documento){
                                $dataDocumento = array(
                                    'id' => $documento['id'],
                                    'estado' => 'ANEXADO',
                                    'fk_derivacion' => $id_derivacion,
                                    'fecha_notificacion'=>((!empty($this->request->getPost('fecha_notificacion'.$documento['id']))) ? $this->request->getPost('fecha_notificacion'.$documento['id']) : NULL),
                                );
                                if($documentosModel->save($dataDocumento) === false){
                                    session()->setFlashdata('fail', $documentosModel->errors());
                                }
                            }
                        }

                    }
                    if(in_array(10, session()->get('registroPermisos')))
                        return redirect()->to($this->controlador.'subir_documentos/'.$id);
                    else
                        return redirect()->to($this->controlador.'mis_tramites');
                }
            }
        }
    }

    public function recibir($id_tramite){
        $actoAdministrativoModel = new ActoAdministrativoModel();
        $derivacionModel = new DerivacionModel();
        $where = array(
            'id' => $id_tramite,
            'deleted_at' => NULL,
            'ultimo_fk_usuario_destinatario' => session()->get('registroUser'),
        );
        if($fila = $actoAdministrativoModel->where($where)->whereIn('ultimo_estado', array('DERIVADO','MIGRADO'))->first()){
            $estado = 'RECIBIDO';
            $data = array(
                'id' => $fila['id'],
                'ultimo_estado' => $estado,
                'fk_usuario_actual' => $fila['ultimo_fk_usuario_destinatario'],
                'editar' => true,
            );

            if($actoAdministrativoModel->save($data) === false){
                session()->setFlashdata('fail', $actoAdministrativoModel->errors());
            }

            $where = array(
                'fk_acto_administrativo' => $fila['id'],
            );
            $derivacion = $derivacionModel->where($where)->orderBY('id', 'DESC')->first();
            $dataDerivacion = array(
                'id' => $derivacion['id'],
                'estado' => $estado,
                'fecha_recepcion' => date('Y-m-d H:i:s'),
            );

            if($derivacionModel->save($dataDerivacion) === false){
                session()->setFlashdata('fail', $derivacionModel->errors());
            }
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
        $actoAdministrativoModel = new ActoAdministrativoModel();
        $derivacionModel = new DerivacionModel();
        $where = array(
            'id' => $id_tramite,
            'deleted_at' => NULL,
            'ultimo_fk_usuario_destinatario' => session()->get('registroUser'),
        );
        if($fila = $actoAdministrativoModel->where($where)->whereIn('ultimo_estado', array('DERIVADO','MIGRADO'))->first()){
            $estado = 'RECIBIDO';
            $data = array(
                'id' => $fila['id'],
                'ultimo_estado' => $estado,
                'fk_usuario_actual' => $fila['ultimo_fk_usuario_destinatario'],
                'editar' => true,
            );

            if($actoAdministrativoModel->save($data) === false){
                session()->setFlashdata('fail', $actoAdministrativoModel->errors());
            }

            $where = array(
                'fk_acto_administrativo' => $fila['id'],
            );
            $derivacion = $derivacionModel->where($where)->orderBY('id', 'DESC')->first();
            $dataDerivacion = array(
                'id' => $derivacion['id'],
                'estado' => $estado,
                'fecha_recepcion' => date('Y-m-d H:i:s'),
            );

            if($derivacionModel->save($dataDerivacion) === false){
                session()->setFlashdata('fail', $derivacionModel->errors());
            }
        }
        return true;
    }

    public function ajaxGuardarDevolver(){
        $actoAdministrativoModel = new ActoAdministrativoModel();
        $resultado = array(
            'error' => 'No se guardo la información',
        );
        $where = array(
            'id' => $this->request->getPost('id'),
            'ultimo_fk_usuario_destinatario' => session()->get('registroUser'),
            'deleted_at' => NULL,
        );
        if($fila = $actoAdministrativoModel->where($where)->first()){
            $derivacionModel = new DerivacionModel();
            $estado = 'DEVUELTO';
            $motivo_devolucion = mb_strtoupper($this->request->getPost('motivo_devolucion'));
            switch($fila['ultimo_estado']){
                case 'MIGRADO':
                    $where = array(
                        'fk_acto_administrativo' => $fila['id'],
                    );
                    $derivacion = $derivacionModel->where($where)->orderBy('id', 'DESC')->first();
                    $data = array(
                        'id' => $fila['id'],
                        'fk_usuario_actual' => $derivacion['fk_usuario_remitente'],
                        'ultimo_estado' => $estado,
                        'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                        'ultimo_instruccion' => $motivo_devolucion,
                        'ultimo_fk_usuario_remitente' => session()->get('registroUser'),
                        'ultimo_fk_usuario_destinatario' => $derivacion['fk_usuario_remitente'],
                        'ultimo_fk_usuario_responsable'=>$derivacion['fk_usuario_remitente'],
                    );
                    if($actoAdministrativoModel->save($data) === false){
                        session()->setFlashdata('fail', $actoAdministrativoModel->errors());
                    }else{
                        $dataDerivacion = array(
                            'fk_acto_administrativo' => $fila['id'],
                            'domicilio_legal' => $derivacion['domicilio_legal'],
                            'domicilio_procesal' => $derivacion['domicilio_procesal'],
                            'telefono_solicitante' => $derivacion['telefono_solicitante'],
                            'fk_estado_tramite_padre' => $derivacion['fk_estado_tramite_padre'],
                            'fk_estado_tramite_hijo' => $derivacion['fk_estado_tramite_hijo'],
                            'observaciones' => $derivacion['observaciones'],
                            'fk_usuario_remitente' => session()->get('registroUser'),
                            'fk_usuario_destinatario' => $derivacion['fk_usuario_remitente'],
                            'instruccion' => $motivo_devolucion,
                            'motivo_anexo' => $derivacion['motivo_anexo'],
                            'estado' => $estado,
                            'fk_usuario_creador' => session()->get('registroUser'),
                            'fk_usuario_responsable' => $derivacion['fk_usuario_remitente'],
                        );
                        if($derivacionModel->insert($dataDerivacion) === false){
                            session()->setFlashdata('fail', $derivacionModel->errors());
                        }else{
                            $dataDerivacionActualizacion = array(
                                'id' => $derivacion['id'],
                                'fecha_devolucion' => date('Y-m-d H:i:s'),
                            );
                            if($derivacionModel->save($dataDerivacionActualizacion) === false)
                                session()->setFlashdata('fail', $derivacionModel->errors());

                            $resultado = array(
                                'idtra' => $fila['id']
                            );
                        }
                    }
                    break;
                case 'DERIVADO':
                    $documentosModel = new DocumentosModel();
                    $where = array(
                        'fk_acto_administrativo' => $fila['id'],
                    );
                    $derivaciones = $derivacionModel->where($where)->orderBy('id', 'DESC')->findAll(2);
                    $derivacion_actual = $derivaciones[0];
                    $derivacion_restaurar = $derivaciones[1];
                    $where = array(
                        'fk_derivacion' => $derivacion_actual['id'],
                        'fk_acto_administrativo' => $fila['id'],
                    );
                    $documentos_anexados = $documentosModel->where($where)->findAll();
                    $data = array(
                        'id' => $fila['id'],
                        'ultimo_estado' => $estado,
                        'ultimo_fk_estado_tramite_padre' => $derivacion_restaurar['fk_estado_tramite_padre'],
                        'ultimo_fk_estado_tramite_hijo' => $derivacion_restaurar['fk_estado_tramite_hijo'],
                        'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                        'ultimo_instruccion' => $motivo_devolucion,
                        'ultimo_fk_usuario_remitente' => session()->get('registroUser'),
                        'ultimo_fk_usuario_destinatario' => $derivacion_restaurar['fk_usuario_destinatario'],
                        'ultimo_recurso_jerarquico' => (($derivacion_restaurar['recurso_jerarquico']=='t') ? 'true' : 'false'),
                        'ultimo_recurso_revocatoria' => (($derivacion_restaurar['recurso_revocatoria']=='t') ? 'true' : 'false'),
                        'ultimo_oposicion' => (($derivacion_restaurar['oposicion']=='t') ? 'true' : 'false'),
                        'ultimo_fk_documentos' => '',
                    );
                    if($actoAdministrativoModel->save($data) === false){
                        session()->setFlashdata('fail', $actoAdministrativoModel->errors());
                    }else{
                        $dataDerivacion = array(
                            'fk_acto_administrativo' => $fila['id'],
                            'domicilio_legal' => $derivacion_restaurar['domicilio_legal'],
                            'domicilio_procesal' => $derivacion_restaurar['domicilio_procesal'],
                            'telefono_solicitante' => $derivacion_restaurar['telefono_solicitante'],
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
                            'recurso_jerarquico' => (($derivacion_restaurar['recurso_jerarquico']=='t') ? 'true' : 'false'),
                            'recurso_revocatoria' => (($derivacion_restaurar['recurso_revocatoria']=='t') ? 'true' : 'false'),
                            'oposicion' => (($derivacion_restaurar['oposicion']=='t') ? 'true' : 'false'),
                        );
                        if($derivacionModel->insert($dataDerivacion) === false){
                            session()->setFlashdata('fail', $derivacionModel->errors());
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
                            if($derivacionModel->save($dataDerivacionActualizacion) === false)
                                session()->setFlashdata('fail', $derivacionModel->errors());

                            $resultado = array(
                                'idtra' => $fila['id']
                            );

                        }
                    }
                    break;
            }
        }
        echo json_encode($resultado);
    }

    public function anexarSincobol($id){
        $db = \Config\Database::connect();
        $campos = array('ac.id', 'ac.fecha_mecanizada', 'ac.fk_solicitud_licencia_contrato', 'ac.fk_hoja_ruta', 'ac.fk_area_minera', 'ac.ultimo_fk_usuario_remitente', 'ac.correlativo',
        'ac.ultimo_fk_estado_tramite_padre', 'ac.ultimo_fk_estado_tramite_hijo', 'ac.ultimo_fk_usuario_responsable',
        'dam.codigo_unico', 'dam.denominacion', 'dam.extension', 'dam.departamentos', 'dam.provincias', 'dam.municipios', 'dam.regional',
        'dam.area_protegida', 'dam.area_protegida_adicional', 'dam.representante_legal', 'dam.nacionalidad', 'dam.titular', 'dam.clasificacion_titular'
        );
        $where = array(
            'ac.deleted_at' => NULL,
            'ac.fk_usuario_actual' => session()->get('registroUser'),
            'ac.id' => $id
        );
        $builder = $db->table('public.acto_administrativo as ac')
        ->select($campos)
        ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
        ->join('usuarios as ur', 'ac.ultimo_fk_usuario_remitente = ur.id', 'left')
        ->join('perfiles as per', 'ur.fk_perfil = per.id', 'left')
        ->join('estado_tramite as etp', 'ac.ultimo_fk_estado_tramite_padre = etp.id', 'left')
        ->join('estado_tramite as eth', 'ac.ultimo_fk_estado_tramite_hijo = eth.id', 'left')
        ->where($where)
        ->orderBY('ac.id', 'DESC');

        if($fila = $builder->get()->getRowArray()){

            $dbSincobol = \Config\Database::connect('sincobol');
            $campos = array('hr.correlativo','hr.cantidad_fojas','hr.referencia',"CONCAT(p.nombres,' ',p.apellido_paterno,' ',p.apellido_materno) as nombre_completo",'e.cargo','e.institucion');
            $where = array(
                'hr.id' => $fila['fk_hoja_ruta'],
            );
            $builder = $dbSincobol->table('sincobol.hoja_ruta as hr')
            ->select($campos)
            ->join('sincobol.externo as e', 'hr.fk_externo_remitente = e.id', 'left')
            ->join('sincobol.persona as p', 'e.fk_persona = p.id', 'left')
            ->where($where);
            $hr_remitente = $builder->get()->getFirstRow('array');

            $campos = array(
                'der.id', 'der.domicilio_legal', 'der.domicilio_procesal', 'der.telefono_solicitante', 'der.observaciones as ultima_observacion',
                'ur.nombre_completo as ultimo_remitente', 'per.nombre as ultimo_cargo', "to_char(der.created_at, 'DD/MM/YYYY') as ultimo_fecha_derivacion",
                'der.instruccion as ultimo_instruccion', "CONCAT(etp.orden,'. ',etp.nombre) as ultimo_estado_tramite_padre",
                "CASE WHEN der.fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE '' END as ultimo_estado_tramite_hijo",
            );
            $where = array(
                'der.fk_acto_administrativo' => $fila['id'],
            );
            $query = $db->table('derivacion as der')
            ->select($campos)
            ->join('usuarios as ur', 'der.fk_usuario_remitente = ur.id', 'left')
            ->join('perfiles as per', 'ur.fk_perfil = per.id', 'left')
            ->join('estado_tramite as etp', 'der.fk_estado_tramite_padre = etp.id', 'left')
            ->join('estado_tramite as eth', 'der.fk_estado_tramite_hijo = eth.id', 'left')
            ->where($where)
            ->orderBY('der.id', 'DESC');
            $ultima_derivacion = $query->get()->getFirstRow('array');

            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Anexar Hoja de Ruta Interna o Externa del SINCOBOL';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['fila'] = $fila;
            $contenido['ultima_derivacion'] = $ultima_derivacion;
            $contenido['hr_remitente'] = $hr_remitente;
            $contenido['subtitulo'] = 'Anexar Hoja de Ruta Interna o Externa del SINCOBOL';
            $contenido['accion'] = $this->controlador.'guardar_anexar_sincobol';
            $contenido['controlador'] = $this->controlador;
            $data['content'] = view($this->carpeta.'anexar_sincobol', $contenido);
            $data['menu_actual'] = $this->menuActual.'mis_tramites';
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['alertas'] = $this->alertasTramites();
            $data['validacion_js'] = 'cam-anexar-sincobol-validation.js';
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador);
        }
    }
    public function guardarAnexarSincobol(){
        if ($this->request->getPost()) {
            $id = $this->request->getPost('id');
            $anexar_hr = $this->request->getPost('anexar_hr');
            $validation = $this->validate([
                'id' => [
                    'rules' => 'required',
                ],
                'motivo_anexo' => [
                    'rules' => 'required',
                ],
            ]);
            if(!$validation){
                if(isset($anexar_hr) && count($anexar_hr) > 0){
                    $hojas_ruta_anexadas = array();
                    foreach($anexar_hr as $id_hoja_ruta)
                        $hojas_ruta_anexadas[] = $this->obtenerDatosSelectHrInExSincobol($id_hoja_ruta);
                    $contenido['hojas_ruta_anexadas'] = $hojas_ruta_anexadas;
                }
                $cabera['titulo'] = $this->titulo;
                $cabera['navegador'] = true;
                $cabera['subtitulo'] = 'Anexar Hoja de Ruta Interna o Externa del SINCOBOL';
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['subtitulo'] = 'Anexar Hoja de Ruta Interna o Externa del SINCOBOL';
                $contenido['accion'] = $this->controlador.'guardar_anexar_sincobol';
                $contenido['validation'] = $this->validator;
                $contenido['controlador'] = $this->controlador;
                $data['content'] = view($this->carpeta.'anexar_sincobol', $contenido);
                $data['menu_actual'] = $this->menuActual.'mis_tramites';
                $data['tramites_menu'] = $this->tramitesMenu();
                $data['alertas'] = $this->alertasTramites();
                $data['validacion_js'] = 'cam-anexar-sincobol-validation.js';
                echo view('templates/template', $data);
            }else{
                $hrAnexadasModel = new HrAnexadasModel();
                if($anexar_hr){
                    foreach($anexar_hr as $fk_hoja_ruta){
                        if($this->archivarHrSincobolMejorado($fk_hoja_ruta, $id, session()->get('registroUserName'))){
                            $dataDerivacion = array(
                                'fk_acto_administrativo' => $id,
                                'fk_hoja_ruta_sincobol' => $fk_hoja_ruta,
                                'motivo_anexo' => mb_strtoupper($this->request->getPost('motivo_anexo')),
                                'fk_usuario_creador' => session()->get('registroUser'),
                            );
                            if($hrAnexadasModel->save($dataDerivacion) === false)
                                session()->setFlashdata('fail', $hrAnexadasModel->errors());
                        }
                    }
                    session()->setFlashdata('success', 'Se Anexo Correctamente la(s) Hoja(s) de Ruta(s) del sistema SINCOBOL.');
                }
                return redirect()->to($this->controlador.'mis_tramites');
            }
        }
    }
    public function finalizar($id){
        $db = \Config\Database::connect();
        $campos = array('ac.id', 'ac.fecha_mecanizada', 'ac.fk_solicitud_licencia_contrato', 'ac.fk_hoja_ruta', 'ac.fk_area_minera', 'ac.ultimo_fk_usuario_remitente', 'ac.correlativo',
        'ac.ultimo_fk_estado_tramite_padre', 'ac.ultimo_fk_estado_tramite_hijo', 'ac.ultimo_fk_usuario_responsable',
        'dam.codigo_unico', 'dam.denominacion', 'dam.extension', 'dam.departamentos', 'dam.provincias', 'dam.municipios', 'dam.regional',
        'dam.area_protegida', 'dam.area_protegida_adicional', 'dam.representante_legal', 'dam.nacionalidad', 'dam.titular', 'dam.clasificacion_titular'
        );
        $where = array(
            'ac.deleted_at' => NULL,
            'ac.fk_usuario_actual' => session()->get('registroUser'),
            'ac.id' => $id
        );
        $builder = $db->table('public.acto_administrativo as ac')
        ->select($campos)
        ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
        ->join('usuarios as ur', 'ac.ultimo_fk_usuario_remitente = ur.id', 'left')
        ->join('perfiles as per', 'ur.fk_perfil = per.id', 'left')
        ->join('estado_tramite as etp', 'ac.ultimo_fk_estado_tramite_padre = etp.id', 'left')
        ->join('estado_tramite as eth', 'ac.ultimo_fk_estado_tramite_hijo = eth.id', 'left')
        ->where($where)
        ->orderBY('ac.id', 'DESC');

        if($fila = $builder->get()->getRowArray()){

            $dbSincobol = \Config\Database::connect('sincobol');
            $campos = array('hr.correlativo','hr.cantidad_fojas','hr.referencia',"CONCAT(p.nombres,' ',p.apellido_paterno,' ',p.apellido_materno) as nombre_completo",'e.cargo','e.institucion');
            $where = array(
                'hr.id' => $fila['fk_hoja_ruta'],
            );
            $builder = $dbSincobol->table('sincobol.hoja_ruta as hr')
            ->select($campos)
            ->join('sincobol.externo as e', 'hr.fk_externo_remitente = e.id', 'left')
            ->join('sincobol.persona as p', 'e.fk_persona = p.id', 'left')
            ->where($where);
            $hr_remitente = $builder->get()->getFirstRow('array');
            $contenido['hr_remitente'] = $hr_remitente;

            $campos = array(
                'der.id', 'der.domicilio_legal', 'der.domicilio_procesal', 'der.telefono_solicitante', 'der.observaciones as ultima_observacion',
                'ur.nombre_completo as ultimo_remitente', 'per.nombre as ultimo_cargo', "to_char(der.created_at, 'DD/MM/YYYY') as ultimo_fecha_derivacion",
                'der.instruccion as ultimo_instruccion', "CONCAT(etp.orden,'. ',etp.nombre) as ultimo_estado_tramite_padre",
                "CASE WHEN der.fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE '' END as ultimo_estado_tramite_hijo",
            );
            $where = array(
                'der.fk_acto_administrativo' => $fila['id'],
            );
            $query = $db->table('derivacion as der')
            ->select($campos)
            ->join('usuarios as ur', 'der.fk_usuario_remitente = ur.id', 'left')
            ->join('perfiles as per', 'ur.fk_perfil = per.id', 'left')
            ->join('estado_tramite as etp', 'der.fk_estado_tramite_padre = etp.id', 'left')
            ->join('estado_tramite as eth', 'der.fk_estado_tramite_hijo = eth.id', 'left')
            ->where($where)
            ->orderBY('der.id', 'DESC');
            $ultima_derivacion = $query->get()->getFirstRow('array');
            $contenido['ultima_derivacion'] = $ultima_derivacion;

            $campos = array(
                'doc.id', 'doc.correlativo', "TO_CHAR(doc.fecha, 'DD/MM/YYYY') as fecha", 'td.nombre as tipo_documento'
            );
            $where = array(
                'doc.fk_derivacion' => $ultima_derivacion['id'],
                'doc.estado' => 'ANEXADO',
            );
            $query = $db->table('documentos AS doc')
            ->select($campos)
            ->join('tipo_documento AS td', 'doc.fk_tipo_documento = td.id', 'left')
            ->where($where);
            $contenido['documentos_anexados'] = $query->get()->getResultArray();

            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Finalizar Tramite';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['fila'] = $fila;
            $contenido['subtitulo'] = 'Finalizar Tramite';
            $contenido['accion'] = $this->controlador.'guardar_finalizar';
            $contenido['ruta_archivos'] = $this->rutaArchivos;
            $contenido['controlador'] = $this->controlador;
            $data['content'] = view($this->carpeta.'finalizar', $contenido);
            $data['menu_actual'] = $this->menuActual.'mis_tramites';
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['alertas'] = $this->alertasTramites();
            $data['validacion_js'] = 'cam-finalizar-validation.js';
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador);
        }
    }
    public function guardarFinalizar(){
        if ($this->request->getPost()) {
            $validation = $this->validate([
                'id' => [
                    'rules' => 'required',
                ],
                'caja_documental' => [
                    'rules' => 'required',
                ],
                'gestion_archivo' => [
                    'rules' => 'required',
                ],
                'fojas' => [
                    'rules' => 'required',
                ],
                'motivo_finalizar' => [
                    'rules' => 'required',
                ],
            ]);
            if(!$validation){
                $cabera['titulo'] = $this->titulo;
                $cabera['navegador'] = true;
                $cabera['subtitulo'] = 'Finalizar Tramite';
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['subtitulo'] = 'Finalizar Tramite';
                $contenido['accion'] = $this->controlador.'guardar_finalizar';
                $contenido['validation'] = $this->validator;
                $contenido['ruta_archivos'] = $this->rutaArchivos;
                $contenido['controlador'] = $this->controlador;
                $data['content'] = view($this->carpeta.'finalizar', $contenido);
                $data['menu_actual'] = $this->menuActual.'mis_tramites';
                $data['tramites_menu'] = $this->tramitesMenu();
                $data['alertas'] = $this->alertasTramites();
                $data['validacion_js'] = 'cam-finalizar-validation.js';
                echo view('templates/template', $data);
            }else{
                $estado = 'FINALIZADO';
                $actoAdministrativoModel = new ActoAdministrativoModel();
                $datosAreaMineraModel = new DatosAreaMineraModel();
                $derivacionModel = new DerivacionModel();
                $derivacion_actual = $derivacionModel->find($this->request->getPost('id_derivacion'));
                $motivo_finalizar = mb_strtoupper($this->request->getPost('motivo_finalizar'));
                $data = array(
                    'id' => $this->request->getPost('id'),
                    'fk_usuario_actual' => session()->get('registroUser'),
                    'ultimo_estado' => $estado,
                    'ultimo_fk_estado_tramite_padre' => $derivacion_actual['fk_estado_tramite_padre'],
                    'ultimo_fk_estado_tramite_hijo' => $derivacion_actual['fk_estado_tramite_hijo'],
                    'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                    'ultimo_instruccion' => $motivo_finalizar,
                    'ultimo_fk_usuario_remitente' => session()->get('registroUser'),
                    'ultimo_fk_usuario_responsable'=>($this->request->getPost('responsable') ? $this->request->getPost('fk_usuario_destinatario') : $this->request->getPost('ultimo_fk_usuario_responsable')),
                    'caja_documental' => mb_strtoupper($this->request->getPost('caja_documental')),
                    'gestion_archivo' => $this->request->getPost('gestion_archivo'),
                    'fojas' => $this->request->getPost('fojas'),
                    'estado_tramite_apm' => $this->obtenerEstadoTramiteAPM($derivacion_actual['fk_estado_tramite_padre'], $derivacion_actual['fk_estado_tramite_hijo']),
                    'documentos_apm' => $this->obtenerDocumentosAPM($this->request->getPost('id')),
                );

                if($actoAdministrativoModel->save($data) === false){
                    session()->setFlashdata('fail', $actoAdministrativoModel->errors());
                }else{

                    $dataDatosAreaMinera = array(
                        'fk_acto_administrativo' => $this->request->getPost('id'),
                        'extension' => $this->request->getPost('extension'),
                        'departamentos' => $this->request->getPost('departamentos'),
                        'provincias' => $this->request->getPost('provincias'),
                        'municipios' => $this->request->getPost('municipios'),
                        'regional' => $this->request->getPost('regional'),
                        'area_protegida' => $this->request->getPost('area_protegida'),
                        'representante_legal' => mb_strtoupper($this->request->getPost('representante_legal')),
                        'nacionalidad' => mb_strtoupper($this->request->getPost('nacionalidad')),
                        'titular' => $this->request->getPost('titular'),
                        'clasificacion_titular' => $this->request->getPost('clasificacion_titular'),
                        'the_geom' => $this->obtenerPoligonoAreaMinera($this->request->getPost('fk_area_minera')),
                    );
                    if($datosAreaMineraModel->save($dataDatosAreaMinera) === false)
                        session()->setFlashdata('fail', $datosAreaMineraModel->errors());

                    $dataDerivacion = array(
                        'fk_acto_administrativo' => $this->request->getPost('id'),
                        'domicilio_legal' => $derivacion_actual['domicilio_legal'],
                        'domicilio_procesal' => $derivacion_actual['domicilio_procesal'],
                        'telefono_solicitante' => $derivacion_actual['telefono_solicitante'],
                        'fk_estado_tramite_padre' => $derivacion_actual['fk_estado_tramite_padre'],
                        'fk_estado_tramite_hijo' => $derivacion_actual['fk_estado_tramite_hijo'],
                        'fk_usuario_remitente' => session()->get('registroUser'),
                        'fk_usuario_destinatario' => session()->get('registroUser'),
                        'observaciones' => $motivo_finalizar,
                        'instruccion' => $motivo_finalizar,
                        'estado' => $estado,
                        'fk_usuario_creador' => session()->get('registroUser'),
                        'fk_usuario_responsable'=>($this->request->getPost('responsable') ? $this->request->getPost('fk_usuario_destinatario') : $this->request->getPost('ultimo_fk_usuario_responsable')),
                        'fecha_atencion' => date('Y-m-d H:i:s'),
                    );

                    if($derivacionModel->insert($dataDerivacion) === false){
                        session()->setFlashdata('fail', $derivacionModel->errors());
                    }else{
                        $dataDerivacionActualizacion = array(
                            'id' => $this->request->getPost('id_derivacion'),
                            'estado' => 'ATENDIDO',
                            'fecha_atencion' => date('Y-m-d H:i:s'),
                        );
                        if($derivacionModel->save($dataDerivacionActualizacion) === false)
                            session()->setFlashdata('fail', $derivacionModel->errors());
                        else
                            session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información.');
                    }
                    return redirect()->to($this->controlador.'mis_tramites');
                }
            }
        }
    }

    public function espera($id){
        $db = \Config\Database::connect();
        $campos = array('ac.id', 'ac.fecha_mecanizada', 'ac.fk_solicitud_licencia_contrato', 'ac.fk_hoja_ruta', 'ac.fk_area_minera', 'ac.ultimo_fk_usuario_remitente', 'ac.correlativo',
        'ac.ultimo_fk_estado_tramite_padre', 'ac.ultimo_fk_estado_tramite_hijo', 'ac.ultimo_fk_usuario_responsable',
        'dam.codigo_unico', 'dam.denominacion', 'dam.extension', 'dam.departamentos', 'dam.provincias', 'dam.municipios', 'dam.regional',
        'dam.area_protegida', 'dam.area_protegida_adicional', 'dam.representante_legal', 'dam.nacionalidad', 'dam.titular', 'dam.clasificacion_titular'
        );
        $where = array(
            'ac.deleted_at' => NULL,
            'ac.fk_usuario_actual' => session()->get('registroUser'),
            'ac.id' => $id
        );
        $builder = $db->table('public.acto_administrativo as ac')
        ->select($campos)
        ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
        ->join('usuarios as ur', 'ac.ultimo_fk_usuario_remitente = ur.id', 'left')
        ->join('perfiles as per', 'ur.fk_perfil = per.id', 'left')
        ->join('estado_tramite as etp', 'ac.ultimo_fk_estado_tramite_padre = etp.id', 'left')
        ->join('estado_tramite as eth', 'ac.ultimo_fk_estado_tramite_hijo = eth.id', 'left')
        ->where($where)
        ->orderBY('ac.id', 'DESC');

        if($fila = $builder->get()->getRowArray()){
            $dbSincobol = \Config\Database::connect('sincobol');
            $campos = array('hr.correlativo','hr.cantidad_fojas','hr.referencia',"CONCAT(p.nombres,' ',p.apellido_paterno,' ',p.apellido_materno) as nombre_completo",'e.cargo','e.institucion');
            $where = array(
                'hr.id' => $fila['fk_hoja_ruta'],
            );
            $builder = $dbSincobol->table('sincobol.hoja_ruta as hr')
            ->select($campos)
            ->join('sincobol.externo as e', 'hr.fk_externo_remitente = e.id', 'left')
            ->join('sincobol.persona as p', 'e.fk_persona = p.id', 'left')
            ->where($where);
            $hr_remitente = $builder->get()->getFirstRow('array');
            $contenido['hr_remitente'] = $hr_remitente;

            $campos = array(
                'der.id', 'der.domicilio_legal', 'der.domicilio_procesal', 'der.telefono_solicitante', 'der.observaciones as ultima_observacion',
                'ur.nombre_completo as ultimo_remitente', 'per.nombre as ultimo_cargo', "to_char(der.created_at, 'DD/MM/YYYY') as ultimo_fecha_derivacion",
                'der.instruccion as ultimo_instruccion', "CONCAT(etp.orden,'. ',etp.nombre) as ultimo_estado_tramite_padre",
                "CASE WHEN der.fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE '' END as ultimo_estado_tramite_hijo",
            );
            $where = array(
                'der.fk_acto_administrativo' => $fila['id'],
            );
            $query = $db->table('derivacion as der')
            ->select($campos)
            ->join('usuarios as ur', 'der.fk_usuario_remitente = ur.id', 'left')
            ->join('perfiles as per', 'ur.fk_perfil = per.id', 'left')
            ->join('estado_tramite as etp', 'der.fk_estado_tramite_padre = etp.id', 'left')
            ->join('estado_tramite as eth', 'der.fk_estado_tramite_hijo = eth.id', 'left')
            ->where($where)
            ->orderBY('der.id', 'DESC');
            $ultima_derivacion = $query->get()->getFirstRow('array');
            $contenido['ultima_derivacion'] = $ultima_derivacion;

            $campos = array(
                'doc.id', 'doc.correlativo', "TO_CHAR(doc.fecha, 'DD/MM/YYYY') as fecha", 'td.nombre as tipo_documento'
            );
            $where = array(
                'doc.fk_derivacion' => $ultima_derivacion['id'],
                'doc.estado' => 'ANEXADO',
            );
            $query = $db->table('documentos AS doc')
            ->select($campos)
            ->join('tipo_documento AS td', 'doc.fk_tipo_documento = td.id', 'left')
            ->where($where);
            $contenido['documentos_anexados'] = $query->get()->getResultArray();

            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'En Espera del Trámite';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['fila'] = $fila;
            $contenido['subtitulo'] = 'En Espera del Trámite';
            $contenido['accion'] = $this->controlador.'guardar_espera';
            $contenido['ruta_archivos'] = $this->rutaArchivos;
            $contenido['controlador'] = $this->controlador;
            $data['content'] = view($this->carpeta.'espera', $contenido);
            $data['menu_actual'] = $this->menuActual.'mis_tramites';
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['alertas'] = $this->alertasTramites();
            $data['validacion_js'] = 'cam-espera-validation.js';
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador);
        }
    }
    public function guardarEspera(){
        if ($this->request->getPost()) {
            $validation = $this->validate([
                'id' => [
                    'rules' => 'required',
                ],
                'motivo_espera' => [
                    'rules' => 'required',
                ],
            ]);
            if(!$validation){
                $cabera['titulo'] = $this->titulo;
                $cabera['navegador'] = true;
                $cabera['subtitulo'] = 'En Espera del Trámite';
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['subtitulo'] = 'En Espera del Trámite';
                $contenido['accion'] = $this->controlador.'guardar_espera';
                $contenido['validation'] = $this->validator;
                $contenido['ruta_archivos'] = $this->rutaArchivos;
                $contenido['controlador'] = $this->controlador;
                $data['content'] = view($this->carpeta.'espera', $contenido);
                $data['menu_actual'] = $this->menuActual.'mis_tramites';
                $data['tramites_menu'] = $this->tramitesMenu();
                $data['alertas'] = $this->alertasTramites();
                $data['validacion_js'] = 'cam-espera-validation.js';
                echo view('templates/template', $data);
            }else{
                $estado = 'EN ESPERA';
                $actoAdministrativoModel = new ActoAdministrativoModel();
                $datosAreaMineraModel = new DatosAreaMineraModel();
                $derivacionModel = new DerivacionModel();
                $derivacion_actual = $derivacionModel->find($this->request->getPost('id_derivacion'));
                $motivo_espera = mb_strtoupper($this->request->getPost('motivo_espera'));
                $data = array(
                    'id' => $this->request->getPost('id'),
                    'fk_usuario_actual' => session()->get('registroUser'),
                    'ultimo_estado' => $estado,
                    'ultimo_fk_estado_tramite_padre' => $derivacion_actual['fk_estado_tramite_padre'],
                    'ultimo_fk_estado_tramite_hijo' => $derivacion_actual['fk_estado_tramite_hijo'],
                    'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                    'ultimo_instruccion' => $motivo_espera,
                    'ultimo_fk_usuario_remitente' => session()->get('registroUser'),
                    'ultimo_fk_usuario_responsable'=>($this->request->getPost('responsable') ? $this->request->getPost('fk_usuario_destinatario') : $this->request->getPost('ultimo_fk_usuario_responsable')),
                );
                if($actoAdministrativoModel->save($data) === false){
                    session()->setFlashdata('fail', $actoAdministrativoModel->errors());
                }else{

                    $dataDatosAreaMinera = array(
                        'fk_acto_administrativo' => $this->request->getPost('id'),
                        'extension' => $this->request->getPost('extension'),
                        'departamentos' => $this->request->getPost('departamentos'),
                        'provincias' => $this->request->getPost('provincias'),
                        'municipios' => $this->request->getPost('municipios'),
                        'regional' => $this->request->getPost('regional'),
                        'area_protegida' => $this->request->getPost('area_protegida'),
                        'representante_legal' => mb_strtoupper($this->request->getPost('representante_legal')),
                        'nacionalidad' => mb_strtoupper($this->request->getPost('nacionalidad')),
                        'titular' => $this->request->getPost('titular'),
                        'clasificacion_titular' => $this->request->getPost('clasificacion_titular'),
                        'the_geom' => $this->obtenerPoligonoAreaMinera($this->request->getPost('fk_area_minera')),
                    );
                    if($datosAreaMineraModel->save($dataDatosAreaMinera) === false)
                        session()->setFlashdata('fail', $datosAreaMineraModel->errors());

                    $dataDerivacion = array(
                        'fk_acto_administrativo' => $this->request->getPost('id'),
                        'domicilio_legal' => $derivacion_actual['domicilio_legal'],
                        'domicilio_procesal' => $derivacion_actual['domicilio_procesal'],
                        'telefono_solicitante' => $derivacion_actual['telefono_solicitante'],
                        'fk_estado_tramite_padre' => $derivacion_actual['fk_estado_tramite_padre'],
                        'fk_estado_tramite_hijo' => $derivacion_actual['fk_estado_tramite_hijo'],
                        'fk_usuario_remitente' => session()->get('registroUser'),
                        'fk_usuario_destinatario' => session()->get('registroUser'),
                        'observaciones' => $motivo_espera,
                        'instruccion' => $motivo_espera,
                        'estado' => $estado,
                        'fk_usuario_creador' => session()->get('registroUser'),
                        'fk_usuario_responsable'=>($this->request->getPost('responsable') ? $this->request->getPost('fk_usuario_destinatario') : $this->request->getPost('ultimo_fk_usuario_responsable')),
                        'fecha_atencion' => date('Y-m-d H:i:s'),
                    );

                    if($derivacionModel->insert($dataDerivacion) === false){
                        session()->setFlashdata('fail', $derivacionModel->errors());
                    }else{
                        $dataDerivacionActualizacion = array(
                            'id' => $this->request->getPost('id_derivacion'),
                            'estado' => 'ATENDIDO',
                            'fecha_atencion' => date('Y-m-d H:i:s'),
                        );
                        if($derivacionModel->save($dataDerivacionActualizacion) === false)
                            session()->setFlashdata('fail', $derivacionModel->errors());
                        else
                            session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información.');
                    }
                    return redirect()->to($this->controlador.'mis_tramites');
                }
            }
        }
    }

    public function anexarHrSincobol($id_derivacion, $fk_hoja_ruta, $motivo, $fk_hoja_ruta_solicitud){
        $hrAnexadasModel = new HrAnexadasModel();
        $derivacionSincobolModel = new DerivacionSincobolModel();
        $where = array(
            'tipo_documento_derivado' => 'ORIGINAL',
            'fk_hoja_ruta'=> $fk_hoja_ruta,
        );
        $ultima_derivacion = $derivacionSincobolModel->where($where)->orderBy('id','DESC')->first();
        if($ultima_derivacion['estado'] != 'CONCLUIDO'){
            $ultima_derivacion['estado'] = 'CONCLUIDO';
            $ultima_derivacion['fecha_conclusion'] = date('Y-m-d H:i:s');
            $ultima_derivacion['motivo_conclusion'] = $motivo;
            $ultima_derivacion['fk_hoja_ruta_adjuntado'] = $fk_hoja_ruta_solicitud;
            if($derivacionSincobolModel->save($ultima_derivacion) === false){
                session()->setFlashdata('fail', $derivacionSincobolModel->errors());
            }else{
                $data = array(
                    'fk_derivacion' => $id_derivacion,
                    'fk_hoja_ruta' => $fk_hoja_ruta,
                );
                if($hrAnexadasModel->save($data) === false){
                    session()->setFlashdata('fail', $hrAnexadasModel->errors());
                }else{
                    return true;
                }
            }
        }
        return false;
    }

    public function buscadorMisTramites()
    {
        $estado = $this->request->getPost('estado');
        $subestado = $this->request->getPost('subestado');
        $estadosTramites = $this->obtenerEstadosTramites($this->idTramite);
        $estados = array('' => 'TODOS LOS ESTADOS');
        foreach($estadosTramites as $row){
            if(is_numeric($row['id']) && $row['id'] > 0)
                $estados[$row['id']] = $row['texto'];
        }
        $subestados = array('' => 'TODOS LOS SUBESTADOS');
        if($estadosTramitesHijo = $this->obtenerEstadosTramitesHijo($estado)){
            foreach($estadosTramitesHijo as $row){
                if(is_numeric($row['id']) && $row['id'] > 0)
                    $subestados[$row['id']] = $row['texto'];
            }
        }
        $datos = array();
        $campos_listar=array(
            ' ', 'Fecha Mecanizada','H.R. Madre','Fecha Derivación/Devolución', 'Remitente', 'Destinatario', 'Instrucción','Estado Trámite', 'Código Único','Denominación','Extensión',
            'Representante Legal','Nacionalidad','Solicitante','Clasificación APM','Departamento(s)','Provincia(s)','Municipio(s)','Área Protegida',
        );
        $campos_reales=array(
            'ultimo_estado','fecha_mecanizada','correlativo','ultimo_fecha_derivacion','remitente', 'destinatario', 'ultimo_instruccion', 'estado_tramite', 'codigo_unico','denominacion','extension',
            'representante_legal','nacionalidad','titular','clasificacion_titular','departamentos','provincias','municipios','area_protegida',
        );

        if ($this->request->getPost()) {
            $db = \Config\Database::connect();
            $campos = array('ac.id', 'ac.ultimo_estado',"to_char(ac.fecha_mecanizada, 'DD/MM/YYYY') as fecha_mecanizada",
            'ac.correlativo', 'dam.codigo_unico', 'dam.denominacion', 'dam.extension', 'dam.departamentos', 'dam.provincias', 'dam.municipios', 'dam.area_protegida',
            'dam.representante_legal','dam.nacionalidad','dam.titular','dam.clasificacion_titular',
            "CONCAT(ur.nombre_completo, '<br><b>',pr.nombre,'</b>') as remitente", "CONCAT(ud.nombre_completo,'<br><b>',pd.nombre,'<b>') as destinatario", 'ac.ultimo_instruccion', "to_char(ac.ultimo_fecha_derivacion, 'DD/MM/YYYY') as ultimo_fecha_derivacion",
            "CASE WHEN ac.ultimo_fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'. ',etp.nombre,'<br>',etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE CONCAT(etp.orden,'. ',etp.nombre) END as estado_tramite");
            $where = array(
                'ac.deleted_at' => NULL,
                'ac.ultimo_fk_usuario_responsable' => session()->get('registroUser'),
            );
            if($this->request->getPost('fecha_inicio') && $this->request->getPost('fecha_fin')){
                $where['ac.fecha_mecanizada >='] = $this->request->getPost('fecha_inicio');
                $where['ac.fecha_mecanizada <='] = $this->request->getPost('fecha_fin');
            }
            if(is_numeric($estado) && $estado > 0)
                $where['ac.ultimo_fk_estado_tramite_padre'] = $estado;
            if(is_numeric($subestado) && $subestado > 0)
                $where['ac.ultimo_fk_estado_tramite_hijo'] = $subestado;

            $builder = $db->table('public.acto_administrativo as ac')
            ->select($campos)
            ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
            ->join('usuarios as ur', 'ac.ultimo_fk_usuario_remitente = ur.id', 'left')
            ->join('perfiles as pr', 'ur.fk_perfil=pr.id', 'left')
            ->join('usuarios as ud', 'ac.ultimo_fk_usuario_destinatario = ud.id', 'left')
            ->join('perfiles as pd', 'ud.fk_perfil = pd.id', 'left')
            ->join('estado_tramite as etp', 'ac.ultimo_fk_estado_tramite_padre = etp.id', 'left')
            ->join('estado_tramite as eth', 'ac.ultimo_fk_estado_tramite_hijo = eth.id', 'left')
            ->where($where)
            ->orderBY('ac.id', 'DESC');
            $datos = $builder->get()->getResultArray();
            if ($this->request->getPost('enviar')=='excel') {
                $file_name = 'reporte_mis_tramites-'.date('YmdHis').'.xlsx';
                $this->exportarXLS($campos_listar, $campos_reales, $datos, $file_name);
            }
        }

        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Reporte de Mis Tramites como Responsable';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['estados'] = $estados;
        $contenido['subestados'] = $subestados;
        $contenido['datos'] = $datos;
        $contenido['campos_listar'] = $campos_listar;
        $contenido['campos_reales'] = $campos_reales;
        $contenido['subtitulo'] = 'Reporte de Mis Tramites como Responsable';
        $contenido['accion'] = $this->controlador.'buscador_mis_tramites';
        $contenido['controlador'] = $this->controlador;
        $data['content'] = view($this->carpeta.'buscador_mis_tramites', $contenido);
        $data['menu_actual'] = $this->menuActual.'buscador_mis_tramites';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['alertas'] = $this->alertasTramites();
        echo view('templates/template', $data);
    }

    public function exportarXLS($campos_listar, $campos_reales, $datos, $file_name, $titulo = '', $subtitulo='')
    {
        $alpha = array('A','B','C','D','E','F','G','H','I','J','K', 'L','M','N','O','P','Q','R','S','T','U','V','W','X ','Y','Z');
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setTitle("Mis Tramites");
        $styleTitulo = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '000000'),
                'size'  => 12,
                'name'  => 'Verdana'
            ),
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        );
        $styleSubTitulo = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '000000'),
                'size'  => 11,
                'name'  => 'Verdana'
            ),
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        );
        $styleHeader = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '000000'),
                'size'  => 10,
                'name'  => 'Verdana'
            ),
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        );
        $styleBody = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
            ),
            'font'  => array(
                'bold'  => false,
                'color' => array('rgb' => '000000'),
                'size'  => 10,
                'name'  => 'Verdana'
            )
        );

        $nColumnas = 1;
        if($titulo){
            $activeWorksheet->setCellValue('A'.$nColumnas, $titulo);
            $activeWorksheet->mergeCells('A'.$nColumnas.':'.$alpha[count($campos_listar)-1].$nColumnas);
            $activeWorksheet->getStyle('A'.$nColumnas.':'.$alpha[count($campos_listar)-1].$nColumnas)->applyFromArray($styleTitulo);
            $nColumnas++;
        }
        if($subtitulo){
            $activeWorksheet->setCellValue('A'.$nColumnas, $subtitulo);
            $activeWorksheet->mergeCells('A'.$nColumnas.':'.$alpha[count($campos_listar)-1].$nColumnas);
            $activeWorksheet->getStyle('A'.$nColumnas.':'.$alpha[count($campos_listar)-1].$nColumnas)->applyFromArray($styleSubTitulo);
            $nColumnas++;
        }

        $activeWorksheet->fromArray($campos_listar, NULL, 'A'.$nColumnas);
        $activeWorksheet->getStyle('A'.$nColumnas.':'.$activeWorksheet->getHighestColumn().$nColumnas)->applyFromArray($styleHeader);
        $nColumnas++;
        if($datos){
            foreach($datos as $fila){
                $data = array();
                foreach($campos_reales as $row)
                    $data[] = str_replace('<br><b>',' - ', str_replace('</b>','',$fila[$row]));
                $activeWorksheet->fromArray($data,NULL,'A'.$nColumnas);
                $activeWorksheet->getStyle('A'.$nColumnas.':'.$activeWorksheet->getHighestColumn().$nColumnas)->applyFromArray($styleBody);
                $nColumnas++;
            }
        }
        foreach (range('A', $activeWorksheet->getHighestColumn()) as $col)
            $activeWorksheet->getColumnDimension($col)->setAutoSize(true);
        $writer = new Xlsx($spreadsheet);
        $writer->save($file_name);

		header("Content-Type: application/vnd.ms-excel");
		header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length:' . filesize($file_name));
		flush();
		readfile($file_name);
        @unlink($file_name);
		exit;
    }

    public function buscador()
    {
        $campos_buscar=array(
            'correlativo' => 'H.R. Madre',
            'codigo_unico' => 'Código Único',
            'denominacion' => 'Denominación',
            'solicitante' => 'Solicitante',
        );
        if ($this->request->getPost()) {
            $validation = $this->validate([
                'texto' => [
                    'rules' => 'required',
                ],
                'campo' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar el campo a filtrar.',
                    ]
                ],
            ]);
            if(!$validation){
                $contenido['validation'] = $this->validator;
            }else{
                $validate = true;
                $db = \Config\Database::connect();
                $logsBuscadoresModel = new LogsBuscadoresModel();
                $campos = array('ac.id', 'ac.ultimo_estado', 'ac.correlativo', 'ofi.nombre as oficina', 'dam.codigo_unico', 'dam.denominacion', 'dam.titular as solicitante',
                "CONCAT(ur.nombre_completo, '<br><b>',pr.nombre,'</b>') as remitente", "CONCAT(ud.nombre_completo, '<br><b>',pd.nombre,'</b>') as destinatario",
                'ac.ultimo_instruccion', "CASE WHEN ac.ultimo_fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'. ',etp.nombre,'<br>',etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE CONCAT(etp.orden,'. ',etp.nombre) END as estado_tramite",
                "to_char(ac.ultimo_fecha_derivacion, 'DD/MM/YYYY') as ultimo_fecha_derivacion");
                $builder = $db->table('public.acto_administrativo as ac')
                ->select($campos)
                ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
                ->join('public.oficinas as ofi', 'ac.fk_oficina = ofi.id', 'left')
                ->join('usuarios as ur', 'ac.ultimo_fk_usuario_remitente = ur.id', 'left')
                ->join('perfiles as pr', 'ur.fk_perfil=pr.id', 'left')
                ->join('usuarios as ud', 'ac.ultimo_fk_usuario_destinatario = ud.id', 'left')
                ->join('perfiles as pd', 'ud.fk_perfil=pd.id', 'left')
                ->join('estado_tramite as etp', 'ac.ultimo_fk_estado_tramite_padre = etp.id', 'left')
                ->join('estado_tramite as eth', 'ac.ultimo_fk_estado_tramite_hijo = eth.id', 'left')
                ->orderBY('ac.fecha_mecanizada', 'DESC')
                ->limit(100);
                $where = array(
                    'ac.deleted_at' => NULL,
                    'ac.fk_oficina' => session()->get('registroOficina')
                );
                if(in_array(3, session()->get('registroPermisos')))
                    $where = array(
                        'ac.deleted_at' => NULL,
                    );
                else
                    $where = array(
                        'ac.deleted_at' => NULL,
                        'ac.fk_oficina' => session()->get('registroOficina')
                    );

                $texto = mb_strtoupper(trim($this->request->getPost('texto')));

                switch($this->request->getPost('campo')){
                    case 'correlativo':
                        $query = $builder->where($where)->like('ac.correlativo', $texto);
                        break;
                    case 'codigo_unico':
                        if(is_numeric($texto)){
                            $where['dam.codigo_unico'] = $texto;
                            $query = $builder->where($where);
                        }else{
                            $validate = false;
                        }
                        break;
                    case 'denominacion':
                        $query = $builder->where($where)->like('dam.denominacion', $texto);
                        break;
                    case 'solicitante':
                        $query = $builder->where($where)->like('dam.titular', $texto);
                        break;
                }
                $dataLog = array(
                    'tramite' => 'CAM',
                    'modulo' => 'Buscador de Trámites',
                    'fk_usuario' => session()->get('registroUser'),
                    'texto' => $texto,
                    'campo' => $campos_buscar[$this->request->getPost('campo')],
                    'fecha' => date('Y-m-d H:i:s'),
                );
                $logsBuscadoresModel->insert($dataLog);
                if($validate){
                    $datos = $query->get()->getResultArray();
                    $contenido['datos'] = $datos;
                }
            }
        }

        $campos_listar=array(
            ' ','Fecha Derivación<br>o Devolución','H.R. Madre','Departamental o Regional','Código Único','Denominación','Solicitante','Remitente','Destinatario','Instrucción','Estado Trámite',
        );
        $campos_reales=array(
            'ultimo_estado','ultimo_fecha_derivacion','correlativo','oficina','codigo_unico','denominacion','solicitante','remitente','destinatario','ultimo_instruccion','estado_tramite',
        );
        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Buscador de Contratos Administrativos Mineros';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['campos_listar'] = $campos_listar;
        $contenido['campos_reales'] = $campos_reales;
        $contenido['campos_buscar'] = $campos_buscar;
        $contenido['subtitulo'] = 'Buscador de Contratos Administrativos Mineros';
        $contenido['accion'] = $this->controlador.'buscador';
        $contenido['controlador'] = $this->controlador;
        $data['content'] = view($this->carpeta.'buscador', $contenido);
        $data['menu_actual'] = $this->menuActual.'buscador';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['alertas'] = $this->alertasTramites();
        echo view('templates/template', $data);
    }

    public function buscadorVentanilla()
    {
        $campos_buscar=array(
            'correlativo' => 'H.R. Madre',
            'codigo_unico' => 'Código Único',
            'denominacion' => 'Denominación',
            'titular' => 'Solicitante'
        );
        if ($this->request->getPost()) {
            $validation = $this->validate([
                'texto' => [
                    'rules' => 'required',
                ],
                'campo' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar el campo a filtrar.',
                    ]
                ],
            ]);
            if(!$validation){
                $contenido['validation'] = $this->validator;
            }else{
                $validate = true;
                $db = \Config\Database::connect();
                $logsBuscadoresModel = new LogsBuscadoresModel();
                $campos = array('ac.id', 'ac.fk_hoja_ruta', 'ac.ultimo_estado', 'ac.correlativo', 'dam.codigo_unico', 'dam.denominacion', 'dam.representante_legal', 'dam.nacionalidad', 'dam.titular', 'dam.clasificacion_titular',
                "CONCAT(ur.nombre_completo, '<br><b>',pr.nombre,'</b>') as remitente", "CONCAT(ud.nombre_completo, '<br><b>',pd.nombre,'</b>') as destinatario",
                'ac.ultimo_instruccion', "CASE WHEN ac.ultimo_fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'. ',etp.nombre,'<br>',etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE CONCAT(etp.orden,'. ',etp.nombre) END as estado_tramite",
                "to_char(ac.ultimo_fecha_derivacion, 'DD/MM/YYYY') as ultimo_fecha_derivacion", "CONCAT(ua.nombre_completo,'<br><b>',pa.nombre,'<b>') as responsable");
                $builder = $db->table('public.acto_administrativo as ac')
                ->select($campos)
                ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
                ->join('usuarios as ur', 'ac.ultimo_fk_usuario_remitente = ur.id', 'left')
                ->join('perfiles as pr', 'ur.fk_perfil=pr.id', 'left')
                ->join('usuarios as ud', 'ac.fk_usuario_actual = ud.id', 'left')
                ->join('perfiles as pd', 'ud.fk_perfil=pd.id', 'left')
                ->join('usuarios as ua', 'ac.ultimo_fk_usuario_responsable = ua.id', 'left')
                ->join('perfiles as pa', 'ua.fk_perfil = pa.id', 'left')
                ->join('estado_tramite as etp', 'ac.ultimo_fk_estado_tramite_padre = etp.id', 'left')
                ->join('estado_tramite as eth', 'ac.ultimo_fk_estado_tramite_hijo = eth.id', 'left')
                ->orderBY('ac.fecha_mecanizada', 'DESC');
                $where = array(
                    'ac.deleted_at' => NULL,
                    //'ac.fk_oficina' => session()->get('registroOficina')
                );
                $texto = mb_strtoupper(trim($this->request->getPost('texto')));

                switch($this->request->getPost('campo')){
                    case 'correlativo':
                        $query = $builder->where($where)->like('ac.correlativo', $texto);
                        break;
                    case 'codigo_unico':
                        if(is_numeric($texto)){
                            $where['dam.codigo_unico'] = $texto;
                            $query = $builder->where($where);
                        }else{
                            $validate = false;
                        }
                        break;
                    case 'denominacion':
                        $query = $builder->where($where)->like('dam.denominacion', $texto);
                        break;
                    case 'titular':
                        $query = $builder->where($where)->like('dam.titular', $texto);
                        break;
                }
                $dataLog = array(
                    'tramite' => 'CAM',
                    'modulo' => 'Buscador de Trámites - Ventanilla Única',
                    'fk_usuario' => session()->get('registroUser'),
                    'texto' => $texto,
                    'campo' => $campos_buscar[$this->request->getPost('campo')],
                    'fecha' => date('Y-m-d H:i:s'),
                );
                $logsBuscadoresModel->insert($dataLog);
                if($validate){
                    $datos = $query->get()->getResultArray();
                    $contenido['datos'] = $datos;
                }
            }
        }
        $campos_listar=array(
            ' ','Fecha Derivación/Devolución','H.R. Madre', 'Remitente','Destinatario','Responsable Trámite','Estado Trámite','Código Único','Denominación', 'Representante Legal', 'Nacionalidad', 'Solicitante', 'Clasificación APM'
        );
        $campos_reales=array(
            'ultimo_estado','ultimo_fecha_derivacion','correlativo','remitente','destinatario','responsable','estado_tramite','codigo_unico','denominacion', 'representante_legal', 'nacionalidad', 'titular', 'clasificacion_titular'
        );
        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Buscador de Contratos Administrativos Mineros';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['campos_listar'] = $campos_listar;
        $contenido['campos_reales'] = $campos_reales;
        $contenido['campos_buscar'] = $campos_buscar;
        $contenido['subtitulo'] = 'Buscador de Contratos Administrativos Mineros';
        $contenido['accion'] = $this->controlador.'buscador_ventanilla';
        $contenido['controlador'] = $this->controlador;
        $contenido['sincobol'] = $this->urlSincobol;
        $data['content'] = view($this->carpeta.'buscador_ventanilla', $contenido);
        $data['menu_actual'] = 'correspondencia_externa/buscador_tramites_cam';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['alertas'] = $this->alertasTramites();
        echo view('templates/template', $data);
    }

    public function reporteUsuarios()
    {
        $db = \Config\Database::connect();
        $campos = array(
            'u.id', "CONCAT(u.nombre_completo, ' (',p.nombre,' - ',o.nombre,')') as nombre"
        );
        $where = array(
            'u.activo' => true,
            'u.fk_oficina' => session()->get('registroOficina'),
            'u.derivacion' => true,
        );
        $builder = $db->table('usuarios as u')
        ->select($campos)
        ->join('oficinas as o', 'u.fk_oficina = o.id', 'left')
        ->join('perfiles as p', 'u.fk_perfil = p.id', 'left')
        ->where($where)
        //->like("u.tramites", $this->idTramite)
        ->orderBy('u.nombre_completo','ASC');
        $usuarios = $builder->get()->getResultArray();
        $arrayUsuarios = array(''=>'DEBE SELECCIONAR UNA OPCIÓN');
        foreach($usuarios as $usuario)
            $arrayUsuarios[$usuario['id']] = $usuario['nombre'];

        if ($this->request->getPost()) {
            $validation = $this->validate([
                'id_usuario' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar una opción.',
                    ]
                ],
            ]);
            if(!$validation){
                $contenido['validation'] = $this->validator;
            }else{
                $campos_listar=array(
                    ' ', 'Fecha Derivación/Devolución', 'H.R. Madre','Fecha Mecanizada','Código Único','Denominación','Representante Legal','Solicitante','Extensión','Departamento(s)','Provincia(s)','Municipio(s)','Área Protegida',
                    'Estado Trámite', 'Documentos'
                );
                $campos_reales=array(
                    'ultimo_estado','ultimo_fecha_derivacion','correlativo','fecha_mecanizada','codigo_unico','denominacion','representante_legal','titular','extension','departamentos','provincias','municipios','area_protegida',
                    'estado_tramite', 'documentos'
                );
                $campos = array(
                    'ac.id', 'ac.ultimo_estado', 'ac.correlativo', "to_char(ac.fecha_mecanizada, 'DD/MM/YYYY') as fecha_mecanizada",
                    'dam.codigo_unico', 'dam.denominacion', 'dam.representante_legal','dam.titular','dam.extension', 'dam.departamentos', 'dam.provincias', 'dam.municipios', 'dam.area_protegida',
                    "CASE WHEN ac.ultimo_fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'. ',etp.nombre,'<br>',etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE CONCAT(etp.orden,'. ',etp.nombre) END as estado_tramite",
                    "CONCAT(etp.orden,'. ',etp.nombre) as estado_tramite_excel",
                    "CASE WHEN ac.ultimo_fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE '' END as sub_estado_tramite_excel",
                    "to_char(ac.ultimo_fecha_derivacion, 'DD/MM/YYYY') as ultimo_fecha_derivacion"
                );
                $where = array(
                    'ac.deleted_at' => NULL,
                    'ac.fk_usuario_actual' => $this->request->getPost('id_usuario'),
                );
                $builder = $db->table('public.acto_administrativo as ac')
                ->select($campos)
                ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
                ->join('estado_tramite as etp', 'ac.ultimo_fk_estado_tramite_padre = etp.id', 'left')
                ->join('estado_tramite as eth', 'ac.ultimo_fk_estado_tramite_hijo = eth.id', 'left')
                ->where($where)
                ->orderBY('ac.fecha_mecanizada', 'ASC');
                $datos = $builder->get()->getResultArray();
                $datos = $this->obtenerDocumentosResponsable($datos, $this->request->getPost('id_usuario'));
                $contenido['datos'] = $datos;
                $contenido['campos_listar'] = $campos_listar;
                $contenido['campos_reales'] = $campos_reales;

                if ($this->request->getPost() && $this->request->getPost('enviar')=='excel') {
                    $campos_listar_excel=array(
                        ' ', 'Fecha Derivación/Devolución', 'H.R. Madre','Fecha Mecanizada','Código Único','Denominación','Representante Legal','Solicitante','Extensión','Departamento(s)','Provincia(s)','Municipio(s)','Área Protegida',
                        'Estado Trámite', 'Sub Estado Trámite', 'Documentos'
                    );
                    $campos_reales_excel=array(
                        'ultimo_estado','ultimo_fecha_derivacion','correlativo','fecha_mecanizada','codigo_unico','denominacion','representante_legal','titular','extension','departamentos','provincias','municipios','area_protegida',
                        'estado_tramite_excel', 'sub_estado_tramite_excel', 'documentos_excel'
                    );
                    $this->exportarReporteUsuarios($campos_listar_excel, $campos_reales_excel, $datos,$arrayUsuarios[$this->request->getPost('id_usuario')]);
                }

            }
        }

        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Reporte Trámites por Usuario';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['array_usuarios'] = $arrayUsuarios;
        $contenido['subtitulo'] = 'Reporte Trámites por Usuario';
        $contenido['accion'] = $this->controlador.'reporte_usuarios';
        $contenido['controlador'] = $this->controlador;
        $data['content'] = view($this->carpeta.'reporte_usuarios', $contenido);
        $data['menu_actual'] = $this->menuActual.'reporte_usuarios';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['alertas'] = $this->alertasTramites();
        echo view('templates/template', $data);
    }
    private function obtenerDocumentosResponsable($datos, $id_usuario){
        if($datos && count($datos)>0){
            $db = \Config\Database::connect();
            foreach($datos as $i=>$row){
                $documentos = '';
                $documentos_excel = '';
                $campos = array(
                    "doc.id", "tdoc.nombre as tipo_documento", "doc.correlativo", "to_char(doc.fecha, 'DD/MM/YYYY') as fecha_documento", "doc.estado as estado_documento"
                );
                $where = array(
                    'doc.fk_tramite' => $this->idTramite,
                    'doc.deleted_at' => NULL,
                    'doc.fk_usuario_creador' => $id_usuario,
                    'doc.fk_acto_administrativo' => $row['id'],
                );
                $builder = $db->table('public.documentos as doc')
                ->select($campos)
                ->join('public.tipo_documento as tdoc', 'doc.fk_tipo_documento = tdoc.id', 'left')
                ->where($where)
                ->whereIn('doc.estado',array('SUELTO', 'ANEXADO'))
                ->orderBY('doc.id', 'DESC');
                if($tmpDocumentos = $builder->get()->getResultArray()){
                    foreach($tmpDocumentos as $documento){
                        $documentos .= $documento['fecha_documento'].' - '.$documento['tipo_documento'].' - '.$documento['correlativo'].'<br>';
                        $documentos_excel .= $documento['fecha_documento'].' - '.$documento['tipo_documento'].' - '.$documento['correlativo'].PHP_EOL;
                    }

                }
                $datos[$i]['documentos'] = $documentos;
                $datos[$i]['documentos_excel'] = $documentos_excel;
            }
        }
        return $datos;
    }

    public function exportarReporteUsuarios($campos_listar, $campos_reales, $datos, $usuario){
        $tmpNombre = explode("(", $usuario);
        helper('security');
        $file_name = sanitize_filename(mb_strtolower($tmpNombre[0])).'-'.date('YmdHis').'.xlsx';
        //$file_name = 'reporte-'.$usu.'-'.date('YmdHis').'.xlsx';
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setTitle("Reporte Tramites");
        $styleHeader = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '000000'),
                'size'  => 10,
                'name'  => 'Verdana'
            ),
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        );
        $styleBody = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
            ),
            'font'  => array(
                'bold'  => false,
                'color' => array('rgb' => '000000'),
                'size'  => 10,
                'name'  => 'Verdana'
            )
        );
        $nColumnas = 1;
        $activeWorksheet->setCellValue('A'.$nColumnas, $usuario);
        $activeWorksheet->mergeCells('A1:M1');
        $activeWorksheet->getStyle('A1:M1')->applyFromArray($styleHeader);

        $nColumnas++;
        $activeWorksheet->fromArray($campos_listar,NULL,'A'.$nColumnas);
        $activeWorksheet->getStyle('A'.$nColumnas.':'.$activeWorksheet->getHighestColumn().$nColumnas)->applyFromArray($styleHeader);
        if($datos){
            $nColumnas++;
            foreach($datos as $fila){
                $data = array();
                foreach($campos_reales as $row)
                    $data[] = str_replace('<br><b>',' - ', str_replace('</b>','',$fila[$row]));
                $activeWorksheet->fromArray($data,NULL,'A'.$nColumnas);
                $activeWorksheet->getStyle('A'.$nColumnas.':'.$activeWorksheet->getHighestColumn().$nColumnas)->applyFromArray($styleBody);
                $activeWorksheet->getStyle('L'.$nColumnas)->getAlignment()->setWrapText(true);
                $activeWorksheet->getStyle('M'.$nColumnas)->getAlignment()->setWrapText(true);
                $nColumnas++;
            }
        }
        foreach (range('A', $activeWorksheet->getHighestColumn()) as $col)
            $activeWorksheet->getColumnDimension($col)->setAutoSize(true);
        $writer = new Xlsx($spreadsheet);
        $writer->save($file_name);

		header("Content-Type: application/vnd.ms-excel");
		header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length:' . filesize($file_name));
		flush();
		readfile($file_name);
        @unlink($file_name);
		exit;
    }

    public function ver($back,$id){
        $db = \Config\Database::connect();
        $where = array(
            'ac.deleted_at' => NULL,
            'ac.id' => $id,
        );
        $builder = $db->table('public.acto_administrativo as ac')
        ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
        ->where($where);
        if($fila = $builder->get()->getRowArray()){
            $dbSincobol = \Config\Database::connect('sincobol');
            $campos = array('hr.correlativo','hr.cantidad_fojas','hr.referencia',"CONCAT(p.nombres,' ',p.apellido_paterno,' ',p.apellido_materno) as nombre_completo",'e.cargo','e.institucion');
            $where = array(
                'hr.id' => $fila['fk_hoja_ruta'],
            );
            $builder = $dbSincobol->table('sincobol.hoja_ruta as hr')
            ->select($campos)
            ->join('sincobol.externo as e', 'hr.fk_externo_remitente = e.id', 'left')
            ->join('sincobol.persona as p', 'e.fk_persona = p.id', 'left')
            ->where($where);
            $hr_remitente = $builder->get()->getFirstRow('array');

            $where = array(
                'id' => $fila['fk_area_minera'],
            );
            $builder = $dbSincobol->table('contratos_licencias.area_minera')->where($where);
            $area_minera = $builder->get()->getFirstRow('array');

            $campos = array(
                "CONCAT(urt.nombre_completo,'<br><b>',prt.nombre,'</b>') as usuario_responsable",
                "CASE WHEN der.fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'. ',etp.nombre,'<br>',etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE CONCAT(etp.orden,'. ',etp.nombre) END as estado_actual_tramite",
                'der.domicilio_legal','der.domicilio_procesal','der.telefono_solicitante','der.recurso_jerarquico', 'der.recurso_revocatoria', 'der.oposicion'
            );
            $where = array(
                'der.deleted_at' => NULL,
                'der.fk_acto_administrativo' => $fila['id'],
            );
            $query = $db->table('derivacion as der')
            ->select($campos)
            ->join('usuarios as urt', 'der.fk_usuario_responsable = urt.id', 'left')
            ->join('perfiles as prt', 'urt.fk_perfil = prt.id', 'left')
            ->join('estado_tramite as etp', 'der.fk_estado_tramite_padre = etp.id', 'left')
            ->join('estado_tramite as eth', 'der.fk_estado_tramite_hijo = eth.id', 'left')
            ->where($where)
            ->orderBY('der.id', 'DESC');
            $ultima_derivacion = $query->get()->getFirstRow('array');

            $where = array(
                'id_area_minera' => $fila['fk_area_minera'],
            );
            $builder = $dbSincobol->table('contabilidad.vista_deuda_anio_total')->where($where);
            $deuda = $builder->get()->getFirstRow('array');

            $builder = $dbSincobol->table('siremi.reporte_general_cam')->where($where)->whereIn('estado', array('INSCRITO','FINALIZADO','HISTORICO'));
            $registro_minero = $builder->get()->getFirstRow('array');

            switch($back){
                case 1:
                    $url_atras = base_url($this->controlador.'buscador');
                    $menuActual = $this->menuActual.'buscador';
                    break;
                case 2:
                    $url_atras = base_url($this->controlador.'mis_tramites');
                    $menuActual = $this->menuActual.'mis_tramites';
                    break;
                case 3:
                    $url_atras = base_url($this->controlador.'buscador_mis_tramites');
                    $menuActual = $this->menuActual.'buscador_mis_tramites';
                    break;
                case 4:
                    $url_atras = base_url($this->controlador.'buscador');
                    $menuActual = $this->menuActual.'buscador';
                    break;
            }

            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Ver Estado Tramite';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['subtitulo'] = 'Ver Estado Tramite';
            $contenido['controlador'] = $this->controlador;
            $contenido['fila'] = $fila;
            $contenido['hr_remitente'] = $hr_remitente;
            $contenido['area_minera'] = $area_minera;
            $contenido['ultima_derivacion'] = $ultima_derivacion;
            $contenido['deuda'] = $deuda;
            $contenido['registro_minero'] = $registro_minero;
            $contenido['seccion'] = $this->obtenerVerSeguimientoTramite($back, $fila['id']);
            $contenido['url_atras'] = $url_atras;
            $data['content'] = view($this->carpeta.'ver', $contenido);
            $data['menu_actual'] = $menuActual;
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['alertas'] = $this->alertasTramites();
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador.'buscador');
        }
    }
    public function verCorrespondenciaExterna($back,$id){
        $db = \Config\Database::connect();
        $where = array(
            'ac.deleted_at' => NULL,
            'ac.id' => $id,
        );
        $builder = $db->table('public.acto_administrativo as ac')
        ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
        ->where($where);
        if($fila = $builder->get()->getRowArray()){
            $db = \Config\Database::connect();
            $dbSincobol = \Config\Database::connect('sincobol');
            $campos = array('hr.correlativo','hr.cantidad_fojas','hr.referencia',"CONCAT(p.nombres,' ',p.apellido_paterno,' ',p.apellido_materno) as nombre_completo",'e.cargo','e.institucion');
            $where = array(
                'hr.id' => $fila['fk_hoja_ruta'],
            );
            $builder = $dbSincobol->table('sincobol.hoja_ruta as hr')
            ->select($campos)
            ->join('sincobol.externo as e', 'hr.fk_externo_remitente = e.id', 'left')
            ->join('sincobol.persona as p', 'e.fk_persona = p.id', 'left')
            ->where($where);
            $hr_remitente = $builder->get()->getFirstRow('array');

            $where = array(
                'id' => $fila['fk_area_minera'],
            );
            $builder = $dbSincobol->table('contratos_licencias.area_minera')->where($where);
            $area_minera = $builder->get()->getFirstRow('array');

            $campos = array(
                "CONCAT(urt.nombre_completo,'<br><b>',prt.nombre,'</b>') as usuario_responsable",
                "CASE WHEN der.fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'. ',etp.nombre,'<br>',etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE CONCAT(etp.orden,'. ',etp.nombre) END as estado_actual_tramite",
                'der.domicilio_legal','der.domicilio_procesal','der.telefono_solicitante','der.recurso_jerarquico', 'der.recurso_revocatoria', 'der.oposicion'
            );
            $where = array(
                'der.deleted_at' => NULL,
                'der.fk_acto_administrativo' => $fila['id'],
            );
            $query = $db->table('derivacion as der')
            ->select($campos)
            ->join('usuarios as urt', 'der.fk_usuario_responsable = urt.id', 'left')
            ->join('perfiles as prt', 'urt.fk_perfil = prt.id', 'left')
            ->join('estado_tramite as etp', 'der.fk_estado_tramite_padre = etp.id', 'left')
            ->join('estado_tramite as eth', 'der.fk_estado_tramite_hijo = eth.id', 'left')
            ->where($where)
            ->orderBY('der.id', 'DESC');
            $ultima_derivacion = $query->get()->getFirstRow('array');

            $where = array(
                'id_area_minera' => $fila['fk_area_minera'],
            );
            $builder = $dbSincobol->table('contabilidad.vista_deuda_anio_total')->where($where);
            $deuda = $builder->get()->getFirstRow('array');

            $builder = $dbSincobol->table('siremi.reporte_general_cam')->where($where)->whereIn('estado', array('INSCRITO','FINALIZADO','HISTORICO'));
            $registro_minero = $builder->get()->getFirstRow('array');

            switch($back){
                case 1:
                    $url_atras = base_url($this->controlador.'buscador');
                    $menuActual = $this->menuActual.'buscador';
                    break;
                case 2:
                    $url_atras = base_url($this->controlador.'mis_tramites');
                    $menuActual = $this->menuActual.'mis_tramites';
                    break;
                case 3:
                    $url_atras = base_url($this->controlador.'buscador_mis_tramites');
                    $menuActual = $this->menuActual.'buscador_mis_tramites';
                    break;
                case 4:
                    $url_atras = base_url($this->controlador.'buscador');
                    $menuActual = $this->menuActual.'buscador';
                    break;
            }

            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Ver Estado Tramite';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['subtitulo'] = 'Ver Estado Tramite';
            $contenido['controlador'] = $this->controlador;
            $contenido['fila'] = $fila;
            $contenido['hr_remitente'] = $hr_remitente;
            $contenido['area_minera'] = $area_minera;
            $contenido['ultima_derivacion'] = $ultima_derivacion;
            $contenido['deuda'] = $deuda;
            $contenido['registro_minero'] = $registro_minero;
            $contenido['seccion'] = $this->obtenerVerCorrespondenciaExterna($back, $fila['id']);
            $contenido['url_atras'] = $url_atras;
            $data['content'] = view($this->carpeta.'ver', $contenido);
            $data['menu_actual'] = $menuActual;
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['alertas'] = $this->alertasTramites();
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador.'buscador');
        }
    }
    public function verDocumentosGenerados($back,$id){
        $db = \Config\Database::connect();
        $where = array(
            'ac.deleted_at' => NULL,
            'ac.id' => $id,
        );
        $builder = $db->table('public.acto_administrativo as ac')
        ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
        ->where($where);
        if($fila = $builder->get()->getRowArray()){
            $dbSincobol = \Config\Database::connect('sincobol');
            $campos = array('hr.correlativo','hr.cantidad_fojas','hr.referencia',"CONCAT(p.nombres,' ',p.apellido_paterno,' ',p.apellido_materno) as nombre_completo",'e.cargo','e.institucion');
            $where = array(
                'hr.id' => $fila['fk_hoja_ruta'],
            );
            $builder = $dbSincobol->table('sincobol.hoja_ruta as hr')
            ->select($campos)
            ->join('sincobol.externo as e', 'hr.fk_externo_remitente = e.id', 'left')
            ->join('sincobol.persona as p', 'e.fk_persona = p.id', 'left')
            ->where($where);
            $hr_remitente = $builder->get()->getFirstRow('array');

            $where = array(
                'id' => $fila['fk_area_minera'],
            );
            $builder = $dbSincobol->table('contratos_licencias.area_minera')->where($where);
            $area_minera = $builder->get()->getFirstRow('array');

            $campos = array(
                "CONCAT(urt.nombre_completo,'<br><b>',prt.nombre,'</b>') as usuario_responsable",
                "CASE WHEN der.fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'. ',etp.nombre,'<br>',etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE CONCAT(etp.orden,'. ',etp.nombre) END as estado_actual_tramite",
                'der.domicilio_legal','der.domicilio_procesal','der.telefono_solicitante','der.recurso_jerarquico', 'der.recurso_revocatoria', 'der.oposicion'
            );
            $where = array(
                'der.deleted_at' => NULL,
                'der.fk_acto_administrativo' => $fila['id'],
            );
            $query = $db->table('derivacion as der')
            ->select($campos)
            ->join('usuarios as urt', 'der.fk_usuario_responsable = urt.id', 'left')
            ->join('perfiles as prt', 'urt.fk_perfil = prt.id', 'left')
            ->join('estado_tramite as etp', 'der.fk_estado_tramite_padre = etp.id', 'left')
            ->join('estado_tramite as eth', 'der.fk_estado_tramite_hijo = eth.id', 'left')
            ->where($where)
            ->orderBY('der.id', 'DESC');
            $ultima_derivacion = $query->get()->getFirstRow('array');

            $where = array(
                'id_area_minera' => $fila['fk_area_minera'],
            );
            $builder = $dbSincobol->table('contabilidad.vista_deuda_anio_total')->where($where);
            $deuda = $builder->get()->getFirstRow('array');

            $builder = $dbSincobol->table('siremi.reporte_general_cam')->where($where)->whereIn('estado', array('INSCRITO','FINALIZADO','HISTORICO'));
            $registro_minero = $builder->get()->getFirstRow('array');

            switch($back){
                case 1:
                    $url_atras = base_url($this->controlador.'buscador');
                    $menuActual = $this->menuActual.'buscador';
                    break;
                case 2:
                    $url_atras = base_url($this->controlador.'mis_tramites');
                    $menuActual = $this->menuActual.'mis_tramites';
                    break;
                case 3:
                    $url_atras = base_url($this->controlador.'buscador_mis_tramites');
                    $menuActual = $this->menuActual.'buscador_mis_tramites';
                    break;
                case 4:
                    $url_atras = base_url($this->controlador.'buscador');
                    $menuActual = $this->menuActual.'buscador';
                    break;
            }

            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Ver Estado Tramite';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['subtitulo'] = 'Ver Estado Tramite';
            $contenido['controlador'] = $this->controlador;
            $contenido['fila'] = $fila;
            $contenido['hr_remitente'] = $hr_remitente;
            $contenido['area_minera'] = $area_minera;
            $contenido['ultima_derivacion'] = $ultima_derivacion;
            $contenido['deuda'] = $deuda;
            $contenido['registro_minero'] = $registro_minero;
            $contenido['seccion'] = $this->obtenerVerDocumentosGenerados($back, $fila['id'], $fila['ultimo_fk_usuario_responsable']);
            $contenido['url_atras'] = $url_atras;
            $data['content'] = view($this->carpeta.'ver', $contenido);
            $data['menu_actual'] = $menuActual;
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['alertas'] = $this->alertasTramites();
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador.'buscador');
        }
    }
    public function verHojasRutaAnexadas($back,$id){
        $db = \Config\Database::connect();
        $where = array(
            'ac.deleted_at' => NULL,
            'ac.id' => $id,
        );
        $builder = $db->table('public.acto_administrativo as ac')
        ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
        ->where($where);
        if($fila = $builder->get()->getRowArray()){
            $dbSincobol = \Config\Database::connect('sincobol');
            $campos = array('hr.correlativo','hr.cantidad_fojas','hr.referencia',"CONCAT(p.nombres,' ',p.apellido_paterno,' ',p.apellido_materno) as nombre_completo",'e.cargo','e.institucion');
            $where = array(
                'hr.id' => $fila['fk_hoja_ruta'],
            );
            $builder = $dbSincobol->table('sincobol.hoja_ruta as hr')
            ->select($campos)
            ->join('sincobol.externo as e', 'hr.fk_externo_remitente = e.id', 'left')
            ->join('sincobol.persona as p', 'e.fk_persona = p.id', 'left')
            ->where($where);
            $hr_remitente = $builder->get()->getFirstRow('array');

            $where = array(
                'id' => $fila['fk_area_minera'],
            );
            $builder = $dbSincobol->table('contratos_licencias.area_minera')->where($where);
            $area_minera = $builder->get()->getFirstRow('array');

            $campos = array(
                "CONCAT(urt.nombre_completo,'<br><b>',prt.nombre,'</b>') as usuario_responsable",
                "CASE WHEN der.fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'. ',etp.nombre,'<br>',etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE CONCAT(etp.orden,'. ',etp.nombre) END as estado_actual_tramite",
                'der.domicilio_legal','der.domicilio_procesal','der.telefono_solicitante','der.recurso_jerarquico', 'der.recurso_revocatoria', 'der.oposicion'
            );
            $where = array(
                'der.deleted_at' => NULL,
                'der.fk_acto_administrativo' => $fila['id'],
            );
            $query = $db->table('derivacion as der')
            ->select($campos)
            ->join('usuarios as urt', 'der.fk_usuario_responsable = urt.id', 'left')
            ->join('perfiles as prt', 'urt.fk_perfil = prt.id', 'left')
            ->join('estado_tramite as etp', 'der.fk_estado_tramite_padre = etp.id', 'left')
            ->join('estado_tramite as eth', 'der.fk_estado_tramite_hijo = eth.id', 'left')
            ->where($where)
            ->orderBY('der.id', 'DESC');
            $ultima_derivacion = $query->get()->getFirstRow('array');

            $where = array(
                'id_area_minera' => $fila['fk_area_minera'],
            );
            $builder = $dbSincobol->table('contabilidad.vista_deuda_anio_total')->where($where);
            $deuda = $builder->get()->getFirstRow('array');

            $builder = $dbSincobol->table('siremi.reporte_general_cam')->where($where)->whereIn('estado', array('INSCRITO','FINALIZADO','HISTORICO'));
            $registro_minero = $builder->get()->getFirstRow('array');

            switch($back){
                case 1:
                    $url_atras = base_url($this->controlador.'buscador');
                    $menuActual = $this->menuActual.'buscador';
                    break;
                case 2:
                    $url_atras = base_url($this->controlador.'mis_tramites');
                    $menuActual = $this->menuActual.'mis_tramites';
                    break;
                case 3:
                    $url_atras = base_url($this->controlador.'buscador_mis_tramites');
                    $menuActual = $this->menuActual.'buscador_mis_tramites';
                    break;
                case 4:
                    $url_atras = base_url($this->controlador.'buscador');
                    $menuActual = $this->menuActual.'buscador';
                    break;
            }

            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Ver Estado Tramite';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['subtitulo'] = 'Ver Estado Tramite';
            $contenido['controlador'] = $this->controlador;
            $contenido['fila'] = $fila;
            $contenido['hr_remitente'] = $hr_remitente;
            $contenido['area_minera'] = $area_minera;
            $contenido['ultima_derivacion'] = $ultima_derivacion;
            $contenido['deuda'] = $deuda;
            $contenido['registro_minero'] = $registro_minero;
            $contenido['seccion'] = $this->obtenerVerHojasRutaAnexadas($back, $fila['id']);
            $contenido['url_atras'] = $url_atras;
            $data['content'] = view($this->carpeta.'ver', $contenido);
            $data['menu_actual'] = $menuActual;
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['alertas'] = $this->alertasTramites();
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador.'buscador');
        }
    }
    public function verHistoricoSincobol($back,$id){
        $db = \Config\Database::connect();
        $where = array(
            'ac.deleted_at' => NULL,
            'ac.id' => $id,
        );
        $builder = $db->table('public.acto_administrativo as ac')
        ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
        ->where($where);
        if($fila = $builder->get()->getRowArray()){
            $dbSincobol = \Config\Database::connect('sincobol');
            $campos = array('hr.correlativo','hr.cantidad_fojas','hr.referencia',"CONCAT(p.nombres,' ',p.apellido_paterno,' ',p.apellido_materno) as nombre_completo",'e.cargo','e.institucion');
            $where = array(
                'hr.id' => $fila['fk_hoja_ruta'],
            );
            $builder = $dbSincobol->table('sincobol.hoja_ruta as hr')
            ->select($campos)
            ->join('sincobol.externo as e', 'hr.fk_externo_remitente = e.id', 'left')
            ->join('sincobol.persona as p', 'e.fk_persona = p.id', 'left')
            ->where($where);
            $hr_remitente = $builder->get()->getFirstRow('array');

            $campos = array('hr.id', "TO_CHAR(hr.fecha, 'DD/MM/YYYY') as fecha", 'hr.correlativo', 'hr.referencia');
            $where = array(
                'd.estado' => 'CONCLUIDO',
                'd.fk_hoja_ruta_adjuntado' => $fila['fk_hoja_ruta'],
            );
            $builder = $dbSincobol->table('sincobol.derivacion AS d')
            ->select($campos)
            ->join('sincobol.hoja_ruta AS hr', 'd.fk_hoja_ruta = hr.id', 'left')
            ->where($where)
            ->orderBY('hr.id', 'ASC');
            $hr_anexadas = $builder->get()->getResultArray();

            $where = array(
                'id' => $fila['fk_area_minera'],
            );
            $builder = $dbSincobol->table('contratos_licencias.area_minera')->where($where);
            $area_minera = $builder->get()->getFirstRow('array');

            $campos = array(
                "CONCAT(urt.nombre_completo,'<br><b>',prt.nombre,'</b>') as usuario_responsable",
                "CASE WHEN der.fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'. ',etp.nombre,'<br>',etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE CONCAT(etp.orden,'. ',etp.nombre) END as estado_actual_tramite",
                'der.domicilio_legal','der.domicilio_procesal','der.telefono_solicitante','der.recurso_jerarquico', 'der.recurso_revocatoria', 'der.oposicion'
            );
            $where = array(
                'der.deleted_at' => NULL,
                'der.fk_acto_administrativo' => $fila['id'],
            );
            $query = $db->table('derivacion as der')
            ->select($campos)
            ->join('usuarios as urt', 'der.fk_usuario_responsable = urt.id', 'left')
            ->join('perfiles as prt', 'urt.fk_perfil = prt.id', 'left')
            ->join('estado_tramite as etp', 'der.fk_estado_tramite_padre = etp.id', 'left')
            ->join('estado_tramite as eth', 'der.fk_estado_tramite_hijo = eth.id', 'left')
            ->where($where)
            ->orderBY('der.id', 'DESC');
            $ultima_derivacion = $query->get()->getFirstRow('array');

            $where = array(
                'id_area_minera' => $fila['fk_area_minera'],
            );
            $builder = $dbSincobol->table('contabilidad.vista_deuda_anio_total')->where($where);
            $deuda = $builder->get()->getFirstRow('array');

            $builder = $dbSincobol->table('siremi.reporte_general_cam')->where($where)->whereIn('estado', array('INSCRITO','FINALIZADO','HISTORICO'));
            $registro_minero = $builder->get()->getFirstRow('array');

            switch($back){
                case 1:
                    $url_atras = base_url($this->controlador.'buscador');
                    $menuActual = $this->menuActual.'buscador';
                    break;
                case 2:
                    $url_atras = base_url($this->controlador.'mis_tramites');
                    $menuActual = $this->menuActual.'mis_tramites';
                    break;
                case 3:
                    $url_atras = base_url($this->controlador.'buscador_mis_tramites');
                    $menuActual = $this->menuActual.'buscador_mis_tramites';
                    break;
                case 4:
                    $url_atras = base_url($this->controlador.'buscador');
                    $menuActual = $this->menuActual.'buscador';
                    break;
            }

            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Ver Estado Tramite';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['subtitulo'] = 'Ver Estado Tramite';
            $contenido['controlador'] = $this->controlador;
            $contenido['fila'] = $fila;
            $contenido['hr_remitente'] = $hr_remitente;
            $contenido['hr_anexadas'] = $hr_anexadas;
            $contenido['area_minera'] = $area_minera;
            $contenido['ultima_derivacion'] = $ultima_derivacion;
            $contenido['deuda'] = $deuda;
            $contenido['registro_minero'] = $registro_minero;
            $contenido['seccion'] = $this->obtenerVerHistoricoSincobol($back, $fila['id'], $fila['fk_hoja_ruta']);
            $contenido['url_atras'] = $url_atras;
            $data['content'] = view($this->carpeta.'ver', $contenido);
            $data['menu_actual'] = $menuActual;
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['alertas'] = $this->alertasTramites();
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador.'buscador');
        }
    }
    public function verSeguimientoHistoricoSincobol($back,$id){
        $db = \Config\Database::connect();
        $where = array(
            'ac.deleted_at' => NULL,
            'ac.id' => $id,
        );
        $builder = $db->table('public.acto_administrativo as ac')
        ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
        ->where($where);
        if($fila = $builder->get()->getRowArray()){
            $dbSincobol = \Config\Database::connect('sincobol');
            $campos = array('hr.correlativo','hr.cantidad_fojas','hr.referencia',"CONCAT(p.nombres,' ',p.apellido_paterno,' ',p.apellido_materno) as nombre_completo",'e.cargo','e.institucion');
            $where = array(
                'hr.id' => $fila['fk_hoja_ruta'],
            );
            $builder = $dbSincobol->table('sincobol.hoja_ruta as hr')
            ->select($campos)
            ->join('sincobol.externo as e', 'hr.fk_externo_remitente = e.id', 'left')
            ->join('sincobol.persona as p', 'e.fk_persona = p.id', 'left')
            ->where($where);
            $hr_remitente = $builder->get()->getFirstRow('array');

            $where = array(
                'id' => $fila['fk_area_minera'],
            );
            $builder = $dbSincobol->table('contratos_licencias.area_minera')->where($where);
            $area_minera = $builder->get()->getFirstRow('array');

            $campos = array(
                "CONCAT(urt.nombre_completo,'<br><b>',prt.nombre,'</b>') as usuario_responsable",
                "CASE WHEN der.fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'. ',etp.nombre,'<br>',etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE CONCAT(etp.orden,'. ',etp.nombre) END as estado_actual_tramite",
                'der.domicilio_legal','der.domicilio_procesal','der.telefono_solicitante','der.recurso_jerarquico', 'der.recurso_revocatoria', 'der.oposicion'
            );
            $where = array(
                'der.deleted_at' => NULL,
                'der.fk_acto_administrativo' => $fila['id'],
            );
            $query = $db->table('derivacion as der')
            ->select($campos)
            ->join('usuarios as urt', 'der.fk_usuario_responsable = urt.id', 'left')
            ->join('perfiles as prt', 'urt.fk_perfil = prt.id', 'left')
            ->join('estado_tramite as etp', 'der.fk_estado_tramite_padre = etp.id', 'left')
            ->join('estado_tramite as eth', 'der.fk_estado_tramite_hijo = eth.id', 'left')
            ->where($where)
            ->orderBY('der.id', 'DESC');
            $ultima_derivacion = $query->get()->getFirstRow('array');

            $where = array(
                'id_area_minera' => $fila['fk_area_minera'],
            );
            $builder = $dbSincobol->table('contabilidad.vista_deuda_anio_total')->where($where);
            $deuda = $builder->get()->getFirstRow('array');

            $builder = $dbSincobol->table('siremi.reporte_general_cam')->where($where)->whereIn('estado', array('INSCRITO','FINALIZADO','HISTORICO'));
            $registro_minero = $builder->get()->getFirstRow('array');

            switch($back){
                case 1:
                    $url_atras = base_url($this->controlador.'buscador');
                    $menuActual = $this->menuActual.'buscador';
                    break;
                case 2:
                    $url_atras = base_url($this->controlador.'mis_tramites');
                    $menuActual = $this->menuActual.'mis_tramites';
                    break;
                case 3:
                    $url_atras = base_url($this->controlador.'buscador_mis_tramites');
                    $menuActual = $this->menuActual.'buscador_mis_tramites';
                    break;
                case 4:
                    $url_atras = base_url($this->controlador.'buscador');
                    $menuActual = $this->menuActual.'buscador';
                    break;
            }

            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Ver Estado Tramite';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['subtitulo'] = 'Ver Estado Tramite';
            $contenido['controlador'] = $this->controlador;
            $contenido['fila'] = $fila;
            $contenido['hr_remitente'] = $hr_remitente;
            $contenido['area_minera'] = $area_minera;
            $contenido['ultima_derivacion'] = $ultima_derivacion;
            $contenido['deuda'] = $deuda;
            $contenido['registro_minero'] = $registro_minero;
            $contenido['seccion'] = $this->obtenerVerSeguimientoHistoricoSincobol($back, $fila['id'], $fila['fk_area_minera']);
            $contenido['url_atras'] = $url_atras;
            $data['content'] = view($this->carpeta.'ver', $contenido);
            $data['menu_actual'] = $menuActual;
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['alertas'] = $this->alertasTramites();
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador.'buscador');
        }
    }

    private function obtenerVerSeguimientoTramite($back, $id_acto_administrativo){
        $db = \Config\Database::connect();
        $cabecera_listado = array(
            '',
            'Remitente',
            'Destinatario',
            'Responsable Trámite',
            'Instrucción',
            'Estado Tramite',
            'APM Presento',
            'Fecha Derivación',
            'Fecha Recepción',
            'Fecha Atención',
            'Fecha Devolución',
            'Domicilio Legal',
            'Domicilio Procesal',
            'Teléfono(s) Solicitante',
        );
        $campos_listado = array(
            'estado',
            'remitente',
            'destinatario',
            'responsable',
            'instruccion',
            'estado_tramite',
            'apm_presento',
            'fecha_derivacion',
            'fecha_recepcion',
            'fecha_atencion',
            'fecha_devolucion',
            'domicilio_legal',
            'domicilio_procesal',
            'telefono_solicitante',
        );
        $campos = array(
            'd.id','d.estado',"CONCAT(ur.nombre_completo,'<br><b>',pr.nombre,'<b>') as remitente", "CONCAT(ud.nombre_completo,'<br><b>',pd.nombre,'<b>') as destinatario",
            "CONCAT(ua.nombre_completo,'<br><b>',pa.nombre,'<b>') as responsable",'d.instruccion',
            "CASE WHEN d.fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'. ',etp.nombre,'<br>',etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE CONCAT(etp.orden,'. ',etp.nombre) END as estado_tramite",
            "to_char(d.created_at, 'DD/MM/YYYY HH24:MI') as fecha_derivacion", "to_char(d.fecha_recepcion, 'DD/MM/YYYY HH24:MI') as fecha_recepcion", "to_char(d.fecha_atencion, 'DD/MM/YYYY HH24:MI') as fecha_atencion",
            'motivo_anexo', "to_char(d.fecha_devolucion, 'DD/MM/YYYY HH24:MI') as fecha_devolucion", 'd.domicilio_legal', 'd.domicilio_procesal', 'd.telefono_solicitante',
            'd.recurso_jerarquico', 'd.recurso_revocatoria', 'd.oposicion',
            );
        $where = array(
            'd.fk_acto_administrativo' => $id_acto_administrativo,
            'd.deleted_at' => NULL,
        );
        $query = $db->table('derivacion as d')
        ->select($campos)
        ->join('estado_tramite as etp', 'd.fk_estado_tramite_padre = etp.id', 'left')
        ->join('estado_tramite as eth', 'd.fk_estado_tramite_hijo = eth.id', 'left')
        ->join('usuarios as ur', 'd.fk_usuario_remitente = ur.id', 'left')
        ->join('perfiles as pr', 'ur.fk_perfil = pr.id', 'left')
        ->join('usuarios as ud', 'd.fk_usuario_destinatario = ud.id', 'left')
        ->join('perfiles as pd', 'ud.fk_perfil = pd.id', 'left')
        ->join('usuarios as ua', 'd.fk_usuario_responsable = ua.id', 'left')
        ->join('perfiles as pa', 'ua.fk_perfil = pa.id', 'left')
        ->where($where)
        ->orderBY('d.id', 'ASC');
        $datos = $query->get()->getResultArray();
        $contenido['datos'] = $datos;
        $contenido['cabecera_listado'] = $cabecera_listado;
        $contenido['campos_listado'] = $campos_listado;
        $contenido['tabs'] = $this->obtenerCabeceraVer($back, $id_acto_administrativo, 'SEGUIMIENTO TRÁMITE');
        return view($this->carpeta.'ver_seguimiento_tramite', $contenido);
    }
    private function obtenerVerCorrespondenciaExterna($back, $id_acto_administrativo){
        $db = \Config\Database::connect();
        $cabecera_listado = array(
            '',
            'Fecha Ingreso',
            'Fecha Recepción',
            'Fecha Atención',
            'Ingresado Por',
            'Recepcionado Por',
            'Atendido Por',
            'Documento Externo',
            'Doc. Digital',
            'Observaciones',
        );
        $campos_listado = array(
            'estado',
            'fecha_ingreso',
            'fecha_recepcion',
            'fecha_atencion',
            'ingreso',
            'recepcion',
            'atencion',
            'documento_externo',
            'doc_digital',
            'observacion_atencion',
        );
        $campos = array(
            'ce.id', 'ce.estado', "to_char(ce.created_at, 'DD/MM/YYYY') as fecha_ingreso", "to_char(ce.fecha_recepcion, 'DD/MM/YYYY') as fecha_recepcion","to_char(ce.fecha_atencion, 'DD/MM/YYYY') as fecha_atencion",
            "CONCAT(ui.nombre_completo,'<br><b>',pi.nombre,'<b>') as ingreso", "CONCAT(ur.nombre_completo,'<br><b>',pr.nombre,'<b>') as recepcion","CONCAT(ua.nombre_completo,'<br><b>',pa.nombre,'<b>') as atencion",
            "CONCAT('CITE: ',ce.cite,'<br>Fecha: ',to_char(ce.fecha_cite, 'DD/MM/YYYY'),'<br>Remitente: ',CONCAT(pe.nombres, ' ', pe.apellidos, ' (', pe.institucion, ' - ',pe.cargo,')'),'<br>Referencia: ',ce.referencia) as documento_externo",
            'ce.doc_digital','ce.observacion_atencion'
        );
        $where = array(
            'ce.deleted_at' => NULL,
            'ce.fk_tramite' => $this->idTramite,
            'ce.fk_acto_administrativo' => $id_acto_administrativo
        );
        $builder = $db->table('public.correspondencia_externa AS ce')
        ->join('public.usuarios AS ui', 'ce.fk_usuario_creador = ui.id', 'left')
        ->join('public.perfiles AS pi', 'ui.fk_perfil = pi.id', 'left')
        ->join('public.usuarios AS ur', 'ce.fk_usuario_recepcion = ur.id', 'left')
        ->join('public.perfiles AS pr', 'ur.fk_perfil = pr.id', 'left')
        ->join('public.usuarios AS ua', 'ce.fk_usuario_atencion = ua.id', 'left')
        ->join('public.perfiles AS pa', 'ua.fk_perfil = pa.id', 'left')
        ->join('public.persona_externa AS pe', 'ce.fk_persona_externa = pe.id', 'left')
        ->select($campos)
        ->where($where)
        ->orderBy('ce.id', 'ASC');
        $datos = $builder->get()->getResultArray();
        $contenido['datos'] = $datos;
        $contenido['cabecera_listado'] = $cabecera_listado;
        $contenido['campos_listado'] = $campos_listado;
        $contenido['tabs'] = $this->obtenerCabeceraVer($back, $id_acto_administrativo, 'CORRESPONDENCIA EXTERNA');
        return view($this->carpeta.'ver_correspondencia_externa', $contenido);
    }
    private function obtenerVerDocumentosGenerados($back, $id_acto_administrativo, $id_usuario_responsable){
        $db = \Config\Database::connect();
        $cabecera_listado = array(
            'Estado',
            'Fecha Anexado',
            'Correlativo',
            'Fecha',
            'Tipo Documento',
            'Fecha Notificación',
            'Generado Por',
        );
        $campos_listado = array(
            'estado',
            'fecha_anexado',
            'correlativo',
            'fecha_documento',
            'tipo_documento',
            'fecha_notificacion',
            'usuario_creador',
        );
        if($id_usuario_responsable == session()->get('registroUser') || in_array(22, session()->get('registroPermisos'))){
            $cabecera_listado[] = 'Documento Digital';
            $campos_listado[] = 'doc_digital';
        }

        $campos = array(
            "doc.id", "doc.estado", "to_char(der.created_at, 'DD/MM/YYYY') as fecha_anexado", "to_char(doc.fecha, 'DD/MM/YYYY') as fecha_documento",
            "doc.correlativo", "tdoc.nombre as tipo_documento", "to_char(doc.fecha_notificacion, 'DD/MM/YYYY') as fecha_notificacion", "doc.doc_digital",
            "CONCAT(uc.nombre_completo,'<br><b>',pc.nombre,'<b>') as usuario_creador",
        );
        $where = array(
            "doc.deleted_at" => NULL,
            "doc.fk_tramite" => $this->idTramite,
            'doc.fk_acto_administrativo' => $id_acto_administrativo,
        );
        $query = $db->table('public.documentos as doc')
        ->select($campos)
        ->join('public.tipo_documento as tdoc', 'doc.fk_tipo_documento = tdoc.id', 'left')
        ->join('usuarios as uc', 'doc.fk_usuario_creador = uc.id', 'left')
        ->join('perfiles as pc', 'uc.fk_perfil=pc.id', 'left')
        ->join('public.derivacion as der', 'doc.fk_derivacion = der.id', 'left')
        ->where($where)
        ->whereIn('doc.estado',array('SUELTO','ANEXADO'))
        ->orderBY('doc.id', 'ASC');
        $datos = $query->get()->getResultArray();
        $contenido['datos'] = $datos;
        $contenido['cabecera_listado'] = $cabecera_listado;
        $contenido['campos_listado'] = $campos_listado;
        $contenido['tabs'] = $this->obtenerCabeceraVer($back, $id_acto_administrativo, 'DOCUMENTO(S) GENERADO(S)');
        return view($this->carpeta.'ver_documentos_generados', $contenido);
    }
    private function obtenerVerHojasRutaAnexadas($back, $id_acto_administrativo){
        $db = \Config\Database::connect();
        $cabecera_listado = array(
            'Fecha Anexo',
            'Motivo Anexo',
            'Anexado Por',
            'Tipo H.R.',
            'Correlativo',
            'Fecha',
            'Referencia',
            'Remitente Externo/Interno',
            'Cite Externo/Interno',
        );
        $campos_listado = array(
            'fecha_anexado',
            'motivo_anexo',
            'usuario_anexado',
            'tipo_hoja_ruta',
            'correlativo',
            'fecha',
            'referencia',
            'remitente',
            'cite',
        );
        $campos = array(
            "ha.fk_hoja_ruta_sincobol", "to_char(ha.created_at, 'DD/MM/YYYY') as fecha_anexado", "ha.motivo_anexo",
            "CONCAT(ua.nombre_completo,'<br><b>',pa.nombre,'<b>') as usuario_anexado"
        );
        $where = array(
            "ha.deleted_at" => NULL,
            'ha.fk_acto_administrativo' => $id_acto_administrativo,
        );
        $query = $db->table('public.hr_anexadas ha')
        ->select($campos)
        ->join('usuarios as ua', 'ha.fk_usuario_creador = ua.id', 'left')
        ->join('perfiles as pa', 'ua.fk_perfil=pa.id', 'left')
        ->where($where)
        ->orderBY('ha.id', 'ASC');
        $datos = array();
        if($resultado = $query->get()->getResultArray()){
            foreach($resultado as $row){
                if($fila = $this->obtenerDatosHrInExSincobol($row['fk_hoja_ruta_sincobol']))
                    $datos[] = array_merge($row, $fila);
            }
        }
        $contenido['datos'] = $datos;
        $contenido['cabecera_listado'] = $cabecera_listado;
        $contenido['campos_listado'] = $campos_listado;
        $contenido['tabs'] = $this->obtenerCabeceraVer($back, $id_acto_administrativo, 'HOJA(S) DE RUTA ANEXADA(S)');
        return view($this->carpeta.'ver_hojas_ruta_anexadas', $contenido);
    }
    private function obtenerVerHistoricoSincobol($back, $id_acto_administrativo, $id_hoja_ruta){
        $dbSincobol = \Config\Database::connect('sincobol');
        $cabecera_listado = array(
            'Tipo de documento',
            'Remitente',
            'Destinatario',
            'Instrucción',
            'Fecha derivación',
            'Fecha recepción',
            'Fecha conclusión',
            'Estado',
        );
        $campos_listado = array(
            'tipo_documento_derivado',
            'remitente',
            'destinatario',
            'instruccion',
            'fecha_envio',
            'fecha_recepcion',
            'fecha_conclusion',
            'estado',
        );
        $campos = array(
            'd.id', 'd.tipo_documento_derivado', "UPPER(CONCAT(pr.nombres,' ',pr.apellido_paterno,' ',pr.apellido_materno, '<br>',cr.nombre)) as remitente",
            "UPPER(CONCAT(pd.nombres,' ',pd.apellido_paterno,' ',pd.apellido_materno, '<br>',cd.nombre)) as destinatario", 'UPPER(d.instruccion) as instruccion',
            "TO_CHAR(d.fecha_envio, 'DD/MM/YYYY') as fecha_envio", "TO_CHAR(d.fecha_recepcion, 'DD/MM/YYYY') as fecha_recepcion", "TO_CHAR(d.fecha_conclusion, 'DD/MM/YYYY') as fecha_conclusion",
            'd.estado'
        );
        $where = array(
            'd.fk_hoja_ruta' => $id_hoja_ruta,
        );
        $builder = $dbSincobol->table('sincobol.derivacion AS d')
        ->select($campos)
        ->join('sincobol.asignacion_cargo AS acr', 'd.fk_asignacion_cargo_remitente = acr.id', 'left')
        ->join('sincobol.cargo AS cr', 'acr.fk_cargo = cr.id', 'left')
        ->join('sincobol.persona AS pr', 'acr.fk_persona = pr.id', 'left')
        ->join('sincobol.asignacion_cargo AS acd', 'd.fk_asignacion_cargo_destinatario = acd.id', 'left')
        ->join('sincobol.cargo AS cd', 'acd.fk_cargo = cd.id', 'left')
        ->join('sincobol.persona AS pd', 'acd.fk_persona = pd.id', 'left')
        ->where($where)
        ->orderBY('d.id', 'ASC');
        $datos = $builder->get()->getResultArray();
        $contenido['datos'] = $datos;
        $contenido['cabecera_listado'] = $cabecera_listado;
        $contenido['campos_listado'] = $campos_listado;
        $contenido['tabs'] = $this->obtenerCabeceraVer($back, $id_acto_administrativo, 'HISTÓRICO SINCOBOL');
        return view($this->carpeta.'ver_historico_sincobol', $contenido);
    }
    private function obtenerVerSeguimientoHistoricoSincobol($back, $id_acto_administrativo, $id_area_minera){
        $dbSincobol = \Config\Database::connect('sincobol');
        $campos = array(
            "persona_responsable", "cargo_responsable", "oficina_responsable","estado_tramite", "acto_administrativo", "TO_CHAR(fecha_emision, 'DD/MM/YYYY') as fecha_emision",
            "TO_CHAR(fecha_notificacion, 'DD/MM/YYYY') as fecha_notificacion", "observaciones", "TO_CHAR(fecha_actualizacion, 'DD/MM/YYYY') as fecha_actualizacion",
        );
        $where = array(
            'fk_area_minera' => $id_area_minera,
        );
        $builder = $dbSincobol->table('contratos_licencias.asignacion_estado_solicitud_contrato')
        ->select($campos)
        ->where($where)
        ->orderBY('id_asessoco', 'DESC');
        $datos = $builder->get()->getRowArray();
        $contenido['datos'] = $datos;
        $contenido['tabs'] = $this->obtenerCabeceraVer($back, $id_acto_administrativo, 'SEGUIMIENTO HISTÓRICO SINCOBOL');
        return view($this->carpeta.'ver_seguimiento_historico_sincobol', $contenido);
    }
    private function obtenerCabeceraVer($back, $id_acto_administrativo, $activo){
        $url_seguimiento_tramite = base_url($this->controlador.'ver/'.$back.'/'.$id_acto_administrativo);
        $url_correspondencia_externa = base_url($this->controlador.'ver_correspondencia_externa/'.$back.'/'.$id_acto_administrativo);
        $url_documentos_generados = base_url($this->controlador.'ver_documentos_generados/'.$back.'/'.$id_acto_administrativo);
        $url_hojas_ruta_anexadas = base_url($this->controlador.'ver_hojas_ruta_anexadas/'.$back.'/'.$id_acto_administrativo);
        $url_historico_sincobol = base_url($this->controlador.'ver_historico_sincobol/'.$back.'/'.$id_acto_administrativo);
        $url_seguimiento_historico_sincobol = base_url($this->controlador.'ver_seguimiento_historico_sincobol/'.$back.'/'.$id_acto_administrativo);
        $contenido['url_seguimiento_tramite'] = $url_seguimiento_tramite;
        $contenido['url_correspondencia_externa'] = $url_correspondencia_externa;
        $contenido['url_documentos_generados'] = $url_documentos_generados;
        $contenido['url_hojas_ruta_anexadas'] = $url_hojas_ruta_anexadas;
        $contenido['url_historico_sincobol'] = $url_historico_sincobol;
        $contenido['url_seguimiento_historico_sincobol'] = $url_seguimiento_historico_sincobol;
        $contenido['activo'] = $activo;
        return view($this->carpeta.'ver_cabecera', $contenido);
    }

    public function reporte(){
        $oficina = $this->request->getPost('oficina');
        $oficinas = $this->obtenerOficinasReporte();
        $estados_tramites = $this->obtenerEstadosTramites($this->idTramite);
        $clasificaciones = $this->obtenerClasificacionesTitulares();
        if ($this->request->getPost()) {
            $camposValidacion = array(
                'fecha_inicio' => [
                    'rules' => 'required|valid_date[Y-m-d]',
                ],
                'fecha_fin' => [
                    'rules' => 'required|valid_date[Y-m-d]',
                ],
            );
            if(!$this->validate($camposValidacion)){
                $contenido['validation'] = $this->validator;
            }else{
                $db = \Config\Database::connect();
                if($oficina > 0){
                    /* consulta de oficina */
                    $campos = array('dam.clasificacion_titular', "CONCAT(etp.orden,'. ', etp.nombre) as estado_padre", 'count(ad.correlativo) as n');
                    $where = array(
                        'ad.deleted_at' => NULL,
                        'ad.fk_oficina' => $oficina,
                        'ad.fecha_mecanizada >=' => $this->request->getPost('fecha_inicio'),
                        'ad.fecha_mecanizada <=' => $this->request->getPost('fecha_fin'),
                    );
                    $builder = $db->table('public.acto_administrativo as ad')
                    ->select($campos)
                    ->join('public.datos_area_minera as dam', 'ad.id = dam.fk_acto_administrativo', 'left')
                    ->join('public.estado_tramite as etp', 'ad.ultimo_fk_estado_tramite_padre = etp.id', 'left')
                    ->join('public.estado_tramite as eth', 'ad.ultimo_fk_estado_tramite_hijo = eth.id', 'left')
                    ->where($where)
                    ->groupBy(array('dam.clasificacion_titular', 'etp.orden', 'estado_padre'))
                    ->orderBY('dam.clasificacion_titular ASC, etp.orden ASC');
                    $datos = $builder->get()->getResultArray();
                    $resultado = array();
                    $total_clasificaciones = array();
                    if($datos){
                        foreach($datos as $row){
                            if(!isset($total_clasificaciones[$row['clasificacion_titular']]))
                                $total_clasificaciones[$row['clasificacion_titular']] = $row['n'];
                            else
                                $total_clasificaciones[$row['clasificacion_titular']] += $row['n'];
                            $resultado[$row['clasificacion_titular']][$row['estado_padre']] = $row['n'];
                        }
                    }

                    /* Datos JSON*/
                    $data_js = array();
                    $tmp_header = array('ESTADO');
                    foreach($clasificaciones as $clasificacion)
                        $tmp_header[] = $clasificacion;

                    $data_js[] = $tmp_header;
                    foreach($estados_tramites as $estado){
                        if($estado['id'] > 0){
                            $tmp_estado = array($estado['orden']);
                            foreach($clasificaciones as $clasificacion){
                                $tmp_estado[] = intval((isset($resultado[$clasificacion][$estado['texto']]) && $resultado[$clasificacion][$estado['texto']] > 0) ? $resultado[$clasificacion][$estado['texto']] : 0);
                            }
                            $data_js[] = $tmp_estado;
                        }
                    }

                    $contenido['total_clasificaciones'] = $total_clasificaciones;
                    $contenido['resultado_oficina'] = $resultado;
                    $contenido['oficina'] = $oficina;
                    $data['data_chart'] = json_encode($data_js);
                    $data['charts_js'] = 'chart_oficina.js';

                    if ($this->request->getPost() && $this->request->getPost('enviar')=='excel') {
                        $this->exportarReporteOficina($estados_tramites, $clasificaciones, $resultado, $oficinas[$oficina]);
                    }

                }else{
                    $campos = array('o.nombre as oficina', "CONCAT(etp.orden,'. ', etp.nombre) as estado_padre", 'count(ad.correlativo) as n');
                    $where = array(
                        'ad.deleted_at' => NULL,
                        'ad.fecha_mecanizada >=' => $this->request->getPost('fecha_inicio'),
                        'ad.fecha_mecanizada <=' => $this->request->getPost('fecha_fin'),
                    );
                    $builder = $db->table('public.acto_administrativo as ad')
                    ->select($campos)
                    ->join('public.oficinas as o', 'ad.fk_oficina = o.id', 'left')
                    ->join('public.estado_tramite as etp', 'ad.ultimo_fk_estado_tramite_padre = etp.id', 'left')
                    ->join('public.estado_tramite as eth', 'ad.ultimo_fk_estado_tramite_hijo = eth.id', 'left')
                    ->where($where)
                    ->groupBy(array('oficina', 'etp.orden', 'estado_padre'))
                    ->orderBY('o.nombre ASC, etp.orden ASC');
                    $datos = $builder->get()->getResultArray();
                    $resultado = array();
                    $total_oficinas = array();
                    if($datos){
                        foreach($datos as $row){
                            if(!isset($total_oficinas[$row['oficina']]))
                                $total_oficinas[$row['oficina']] = $row['n'];
                            else
                                $total_oficinas[$row['oficina']] += $row['n'];
                            $resultado[$row['oficina']][$row['estado_padre']] = $row['n'];
                        }
                    }
                    /* Datos JSON*/
                    $data_js = array();
                    $tmp_header = array('ESTADO');
                    foreach($oficinas as $idOficina => $oficina){
                        if($idOficina > 0)
                            $tmp_header[] = $oficina;
                    }
                    $data_js[] = $tmp_header;
                    foreach($estados_tramites as $estado){
                        if($estado['id'] > 0){
                            $tmp_estado = array($estado['orden']);
                            foreach($oficinas as $idOficina => $oficina){
                                if($idOficina > 0)
                                    $tmp_estado[] = intval((isset($resultado[$oficina][$estado['texto']]) && $resultado[$oficina][$estado['texto']] > 0) ? $resultado[$oficina][$estado['texto']] : 0);
                            }
                            $data_js[] = $tmp_estado;
                        }
                    }
                    $contenido['total_oficinas'] = $total_oficinas;
                    $contenido['resultado_general'] = $resultado;
                    $data['data_chart'] = json_encode($data_js);
                    $data['charts_js'] = 'chart_general.js';

                    if ($this->request->getPost() && $this->request->getPost('enviar')=='excel') {
                        $this->exportarReporteGeneral($estados_tramites, $oficinas, $resultado);
                    }

                }
            }
        }
        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Reporte de Estado de Tramites';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['clasificaciones'] = $clasificaciones;
        $contenido['estados_tramites'] = $estados_tramites;
        $contenido['oficinas'] = $oficinas;
        $contenido['subtitulo'] = 'Reporte de Estado de Tramites';
        $contenido['controlador'] = $this->controlador;
        $contenido['accion'] = $this->controlador.'reporte';
        $data['content'] = view($this->carpeta.'reporte', $contenido);
        $data['menu_actual'] = $this->menuActual.'reporte';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['alertas'] = $this->alertasTramites();
        echo view('templates/template', $data);
    }

    public function exportarReporteGeneral($estados, $oficinas, $datos){
        $file_name = 'reporte_general-'.date('YmdHis').'.xlsx';
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setTitle("Reporte Estado CAM");
        $styleHeader = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '000000'),
                'size'  => 10,
                'name'  => 'Verdana'
            ),
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        );
        $styleBody = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
            ),
            'font'  => array(
                'bold'  => false,
                'color' => array('rgb' => '000000'),
                'size'  => 10,
                'name'  => 'Verdana'
            )
        );

        $tmp_header = array('ESTADO');
        foreach($oficinas as $idOficina => $oficina){
            if($idOficina > 0)
                $tmp_header[] = $oficina;
        }

        $nColumnas = 1;
        $activeWorksheet->setCellValue('A'.$nColumnas, 'REPORTE GENERAL CONTRATOS ADMINISTRATIVOS MINEROS');
        $activeWorksheet->mergeCells('A'.$nColumnas.':'.$this->alpha[count($tmp_header)-1].$nColumnas);
        $activeWorksheet->getStyle('A'.$nColumnas)->applyFromArray($styleHeader);

        $nColumnas++;
        $activeWorksheet->fromArray($tmp_header,NULL,'A'.$nColumnas);
        $activeWorksheet->getStyle('A'.$nColumnas.':'.$activeWorksheet->getHighestColumn().$nColumnas)->applyFromArray($styleHeader);
        if($datos){
            foreach($estados as $estado){
                if($estado['id'] > 0){
                    $nColumnas++;
                    $tmp_dato = array($estado['texto']);
                    foreach($oficinas as $idOficina => $oficina){
                        if($idOficina > 0)
                            $tmp_dato[] = (isset($datos[$oficina][$estado['texto']]) && $datos[$oficina][$estado['texto']] > 0) ? $datos[$oficina][$estado['texto']] : '0';
                    }
                    $activeWorksheet->fromArray($tmp_dato,NULL,'A'.$nColumnas);
                    $activeWorksheet->getStyle('A'.$nColumnas.':'.$activeWorksheet->getHighestColumn().$nColumnas)->applyFromArray($styleBody);

                }
            }
        }
        foreach (range('A', $activeWorksheet->getHighestColumn()) as $col)
            $activeWorksheet->getColumnDimension($col)->setAutoSize(true);
        $writer = new Xlsx($spreadsheet);
        $writer->save($file_name);

		header("Content-Type: application/vnd.ms-excel");
		header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length:' . filesize($file_name));
		flush();
		readfile($file_name);
        @unlink($file_name);
		exit;
    }

    public function exportarReporteOficina($estados, $clasificaciones, $datos, $direccion){
        $file_name = 'reporte_direccion-'.date('YmdHis').'.xlsx';
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setTitle("Reporte Estado CAM");
        $styleHeader = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '000000'),
                'size'  => 10,
                'name'  => 'Verdana'
            ),
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        );
        $styleBody = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
            ),
            'font'  => array(
                'bold'  => false,
                'color' => array('rgb' => '000000'),
                'size'  => 10,
                'name'  => 'Verdana'
            )
        );

        $tmp_header = array('ESTADO');
        foreach($clasificaciones as $clasificacion)
            $tmp_header[] = $clasificacion;


        $nColumnas = 1;
        $activeWorksheet->setCellValue('A'.$nColumnas, 'REPORTE - '.$direccion);
        $activeWorksheet->mergeCells('A'.$nColumnas.':'.$this->alpha[count($tmp_header)-1].$nColumnas);
        $activeWorksheet->getStyle('A'.$nColumnas)->applyFromArray($styleHeader);

        $nColumnas++;
        $activeWorksheet->fromArray($tmp_header,NULL,'A'.$nColumnas);
        $activeWorksheet->getStyle('A'.$nColumnas.':'.$activeWorksheet->getHighestColumn().$nColumnas)->applyFromArray($styleHeader);
        if($datos){
            foreach($estados as $estado){
                if($estado['id'] > 0){
                    $nColumnas++;
                    $tmp_dato = array($estado['texto']);
                    foreach($clasificaciones as $clasificacion){
                        $tmp_dato[] = (isset($datos[$clasificacion][$estado['texto']]) && $datos[$clasificacion][$estado['texto']] > 0) ? $datos[$clasificacion][$estado['texto']] : '0';
                    }
                    $activeWorksheet->fromArray($tmp_dato,NULL,'A'.$nColumnas);
                    $activeWorksheet->getStyle('A'.$nColumnas.':'.$activeWorksheet->getHighestColumn().$nColumnas)->applyFromArray($styleBody);

                }
            }
        }
        foreach (range('A', $activeWorksheet->getHighestColumn()) as $col)
            $activeWorksheet->getColumnDimension($col)->setAutoSize(true);
        $writer = new Xlsx($spreadsheet);
        $writer->save($file_name);

		header("Content-Type: application/vnd.ms-excel");
		header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length:' . filesize($file_name));
		flush();
		readfile($file_name);
        @unlink($file_name);
		exit;
    }

    public function documentacionDigital()
    {
        if ($this->request->getPost()) {
            $validation = $this->validate([
                'texto' => [
                    'rules' => 'required',
                ],
                'campo' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar el campo a filtrar.',
                    ]
                ],
            ]);
            if(!$validation){
                $contenido['validation'] = $this->validator;
            }else{
                $db = \Config\Database::connect();
                $campos = array('ac.id', 'ac.correlativo', 'dam.codigo_unico', 'dam.denominacion', 'ac.ultimo_estado', "COUNT(doc.correlativo) AS documentos_generados", "COUNT(doc.doc_digital) AS documentos_digitales");
                $builder = $db->table('public.acto_administrativo as ac')
                ->select($campos)
                ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
                ->join('public.derivacion as der', 'ac.id = der.fk_acto_administrativo', 'left')
                ->join('public.documentos as doc', 'der.id = doc.fk_derivacion', 'left')
                ->groupBy(array('ac.id', 'ac.correlativo', 'dam.codigo_unico', 'dam.denominacion'))
                ->orderBY('ac.id', 'DESC');
                $where = array(
                    'ac.deleted_at' => NULL,
                    'ac.fk_oficina' => session()->get('registroOficina')
                );
                $texto = mb_strtoupper(trim($this->request->getPost('texto')));

                switch($this->request->getPost('campo')){
                    case 'correlativo':
                        $query = $builder->where($where)->like('ac.correlativo', $texto);
                        break;
                    case 'codigo_unico':
                        $where['dam.codigo_unico'] = $texto;
                        $query = $builder->where($where);
                        break;
                    case 'denominacion':
                        $query = $builder->where($where)->like('dam.denominacion', $texto);
                        break;
                }
                $datos = $query->get()->getResultArray();
                $contenido['datos'] = $datos;
            }
        }
        $campos_buscar=array(
            'correlativo' => 'H.R. Madre',
            'codigo_unico' => 'Código Único',
            'denominacion' => 'Denominación',
        );
        $campos_listar=array(
            ' ','H.R. Madre','Código Único','Denominación','Documentos Generados', 'Documentos Digitales'
        );
        $campos_reales=array(
            'ultimo_estado','correlativo','codigo_unico','denominacion','documentos_generados','documentos_digitales'
        );
        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Documentos Digitales de Contratos Administrativos Mineros';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['campos_listar'] = $campos_listar;
        $contenido['campos_reales'] = $campos_reales;
        $contenido['campos_buscar'] = $campos_buscar;
        $contenido['subtitulo'] = 'Documentos Digitales de Contratos Administrativos Mineros';
        $contenido['accion'] = $this->controlador.'documentacion_digital';
        $contenido['controlador'] = $this->controlador;
        $data['content'] = view($this->carpeta.'documentacion_digital', $contenido);
        $data['menu_actual'] = $this->menuActual.'documentacion_digital';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['alertas'] = $this->alertasTramites();
        echo view('templates/template', $data);
    }

    public function subirDocumentos($id){
        $db = \Config\Database::connect();
        $where = array(
            'ac.deleted_at' => NULL,
            'ac.id' => $id,
        );
        $builder = $db->table('public.acto_administrativo as ac')
        ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
        ->where($where);
        if($fila = $builder->get()->getRowArray()){

            $dbSincobol = \Config\Database::connect('sincobol');
            $campos = array('hr.correlativo','hr.cantidad_fojas','hr.referencia',"CONCAT(p.nombres,' ',p.apellido_paterno,' ',p.apellido_materno) as nombre_completo",'e.cargo','e.institucion');
            $where = array(
                'hr.id' => $fila['fk_hoja_ruta'],
            );
            $builder = $dbSincobol->table('sincobol.hoja_ruta as hr')
            ->select($campos)
            ->join('sincobol.externo as e', 'hr.fk_externo_remitente = e.id', 'left')
            ->join('sincobol.persona as p', 'e.fk_persona = p.id', 'left')
            ->where($where);
            $hr_remitente = $builder->get()->getFirstRow('array');
            $contenido['hr_remitente'] = $hr_remitente;

            $where = array(
                'id' => $fila['fk_area_minera'],
            );
            $builder = $dbSincobol->table('contratos_licencias.area_minera')->where($where);
            $area_minera = $builder->get()->getFirstRow('array');
            $contenido['area_minera'] = $area_minera;

            $campos = array('doc.id', 'doc.correlativo', "to_char(doc.fecha, 'DD/MM/YYYY') as fecha", 'doc.referencia', 'tdoc.nombre as tipo_documento', "CONCAT(u.nombre_completo,'<br><b>',p.nombre,'</b>') as usuario",
            "doc.doc_digital");
            $where = array(
                'doc.id >' => 0,
                'der.fk_acto_administrativo' => $fila['id'],
                'doc.doc_digital' => NULL,
                'doc.estado' => 'ANEXADO',
            );
            $query = $db->table('derivacion as der')
            ->select($campos)
            ->join('public.documentos AS doc', 'der.id = doc.fk_derivacion', 'left')
            ->join('public.tipo_documento AS tdoc', 'doc.fk_tipo_documento = tdoc.id', 'left')
            ->join('public.usuarios AS u', 'doc.fk_usuario_creador = u.id', 'left')
            ->join('public.perfiles AS p', 'u.fk_perfil = p.id', 'left')
            ->where($where)
            ->orderBY('doc.fecha', 'ASC');
            $documentos = $query->get()->getResultArray();
            $contenido['documentos'] = $documentos;

            $cabecera_documentos = array(
                'Fecha',
                'Correlativo',
                'Tipo Documento',
                'Usuario',
                'Documento Digital',
            );
            $campos_documentos = array(
                'fecha',
                'correlativo',
                'tipo_documento',
                'usuario',
                'doc_digital',
            );

            $url_atras = base_url($this->controlador.'mis_tramites');
            $menuActual = $this->menuActual.'mis_tramites';

            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Subir Documentos';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['fila'] = $fila;
            $contenido['cabecera_documentos'] = $cabecera_documentos;
            $contenido['campos_documentos'] = $campos_documentos;
            $contenido['subtitulo'] = 'Subir Documentos';
            $contenido['controlador'] = $this->controlador;
            $contenido['url_atras'] = $url_atras;
            $contenido['sincobol'] = $this->urlSincobol;
            $contenido['ruta_archivos'] = $this->rutaArchivos;
            $data['content'] = view($this->carpeta.'subir_documentos', $contenido);
            $data['menu_actual'] = $menuActual;
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['alertas'] = $this->alertasTramites();
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador.'buscador');
        }
    }

    public function correspondenciaExterna($id){
        $db = \Config\Database::connect();
        $where = array(
            'ac.deleted_at' => NULL,
            'ac.id' => $id,
        );
        $builder = $db->table('public.acto_administrativo as ac')
        ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
        ->where($where);
        if($fila = $builder->get()->getRowArray()){
            $db = \Config\Database::connect();
            $dbSincobol = \Config\Database::connect('sincobol');
            $campos = array('hr.correlativo','hr.cantidad_fojas','hr.referencia',"CONCAT(p.nombres,' ',p.apellido_paterno,' ',p.apellido_materno) as nombre_completo",'e.cargo','e.institucion');
            $where = array(
                'hr.id' => $fila['fk_hoja_ruta'],
            );
            $builder = $dbSincobol->table('sincobol.hoja_ruta as hr')
            ->select($campos)
            ->join('sincobol.externo as e', 'hr.fk_externo_remitente = e.id', 'left')
            ->join('sincobol.persona as p', 'e.fk_persona = p.id', 'left')
            ->where($where);
            $hr_remitente = $builder->get()->getFirstRow('array');
            $contenido['hr_remitente'] = $hr_remitente;

            $where = array(
                'id' => $fila['fk_area_minera'],
            );
            $builder = $dbSincobol->table('contratos_licencias.area_minera')->where($where);
            $area_minera = $builder->get()->getFirstRow('array');
            $contenido['area_minera'] = $area_minera;

            $campos = array('ce.id', "to_char(ce.created_at, 'DD/MM/YYYY HH24:MI') as fecha_ingreso", "CONCAT(u.nombre_completo,'<br><b>',p.nombre,'</b>') as ingresado_por",
            "CONCAT('CITE: ',ce.cite,'<br>Fecha: ',to_char(ce.fecha_cite, 'DD/MM/YYYY'),'<br>Remitente: ',CONCAT(pe.nombres, ' ', pe.apellidos, ' (', pe.institucion, ' - ',pe.cargo,')'),'<br>Referencia: ',ce.referencia) as documento_externo",
            'ce.doc_digital');
            $where = array(
                'ce.fk_tramite' => $this->idTramite,
                'ce.estado' => 'INGRESADO',
                'ce.fk_acto_administrativo' => $fila['id'],
            );
            $builder = $db->table('public.correspondencia_externa AS ce')
            ->join('public.persona_externa AS pe', 'ce.fk_persona_externa = pe.id', 'left')
            ->join('public.usuarios AS u', 'ce.fk_usuario_creador = u.id', 'left')
            ->join('public.perfiles AS p', 'u.fk_perfil = p.id', 'left')
            ->select($campos)
            ->where($where)
            ->orderBy('ce.id', 'DESC');
            $datos = $builder->get()->getResultArray();
            $contenido['datos'] = $datos;
            $campos_listar=array('Fecha Ingreso','Ingresado Por', 'Documento Externo', 'Doc. Digital');
            $campos_reales=array('fecha_ingreso','ingresado_por', 'documento_externo', 'doc_digital');

            $menuActual = $this->menuActual.'mis_tramites';

            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Correspondencia Externa';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['fila'] = $fila;
            $contenido['campos_listar'] = $campos_listar;
            $contenido['campos_reales'] = $campos_reales;
            $contenido['subtitulo'] = 'Correspondencia Externa';
            $contenido['controlador'] = $this->controlador;
            $contenido['url_atras'] = base_url($this->controlador.'mis_tramites');
            $contenido['tipos_documentos_externos'] = $this->obtenerTiposDocumentosExternos();
            $contenido['accion'] = 'correspondencia_externa/recibir';
            $data['content'] = view($this->carpeta.'correspondencia_externa', $contenido);
            $data['menu_actual'] = $menuActual;
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['alertas'] = $this->alertasTramites();
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador.'buscador');
        }
    }

    public function obtenerTiposDocumentosExternos(){
        $tipoDocumentoExternoModel = new TipoDocumentoExternoModel();
        $resultado = $tipoDocumentoExternoModel->findAll();
        $temporal = array(''=>'SELECCIONE UNA OPCIÓN');
        foreach($resultado as $row)
            $temporal[$row['id']] = $row['nombre'];
        return $temporal;
    }

    public function ajaxSubirArchivo(){
        $id_documento = $this->request->getPost('idoc');
        $adjuntoPDF = $this->request->getFile('file');
        $documentosModel = new DocumentosModel();
        $resultado = array(
            'id_doc' => $id_documento,
            'finalizar' => false,
            'error' => '',
            'url' => '',
        );

        if($documento = $documentosModel->find($id_documento)){
            $actoAdministrativoModel = new ActoAdministrativoModel();
            $cam = $actoAdministrativoModel->find($documento['fk_acto_administrativo']);
            $path = $this->rutaArchivos.$cam['fk_area_minera'].'/';
            if(!file_exists($path))
                mkdir($path,0777);

            if(file_exists($path.$documento['doc_digital']))
                @unlink($path.$documento['doc_digital']);

            $nombreAdjunto = $adjuntoPDF->getRandomName();
            $adjuntoPDF->move($path,$nombreAdjunto);
            $resultado['url'] = base_url($path.$nombreAdjunto);

            $dataDocumento = array(
                'id' => $documento['id'],
                'doc_digital' => $path.$nombreAdjunto,
                'fk_usuario_doc_digital' => session()->get('registroUser'),
            );

            if($documentosModel->save($dataDocumento) === false)
                $resultado['error'] = $documentosModel->errors();

            $where = array(
                'fk_derivacion >' => 0,
                'fk_acto_administrativo' => $documento['fk_acto_administrativo'],
                'doc_digital' => NULL
            );
            $verificacion = $documentosModel->where($where)->findAll();
            if(count($verificacion) == 0)
                $resultado['finalizar'] = true;

        }

        echo json_encode($resultado);
    }

    public function obtenerNombreEstadosTramites($idTramite){
        $db = \Config\Database::connect();
        $builder = $db->table('public.estado_tramite')
        ->select('id, nombre')
        ->where('deleted_at IS NULL AND fk_tramite = '.$idTramite)
        ->orderBy('id');
        $estadosTramites = $builder->get()->getResult('array');
        $temporal = array();
        foreach($estadosTramites as $row)
            $temporal[$row['id']] = $row['nombre'];

        return $temporal;
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

    public function obtenerClasificacionesTitulares(){
        $db = \Config\Database::connect();
        $builder = $db->table('public.acto_administrativo as ac')
        ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
        ->select('DISTINCT(dam.clasificacion_titular) AS clasificacion')
        ->where("ac.deleted_at IS NULL AND dam.clasificacion_titular <> ''")
        ->orderBy('clasificacion');
        $clasificacionesTitulares = $builder->get()->getResultArray();
        $temporal = array();
        foreach($clasificacionesTitulares as $clasificacion)
            $temporal[] = $clasificacion['clasificacion'];
        return $temporal;
    }

    public function obtenerDatosHojaRutaMadre($id){
        $db = \Config\Database::connect('sincobol');
        $campos = array('slc.id', "CONCAT(am.nombre, ' - ', am.codigo_unico, ' (',slc.correlativo,')') as nombre");
        $where = array(
            'slc.id' => $id,
        );
        $builder = $db->table('contratos_licencias.solicitud_licencia_contrato as slc')
        ->select($campos)
        ->join('contratos_licencias.area_minera as am', 'slc.fk_area_minera = am.id', 'left')
        ->where($where);
        $datos = $builder->get()->getRowArray();
        return $datos;
    }

    public function obtenerDatosHojaRutaCmnCmc($id){
        $db = \Config\Database::connect('sincobol');
        $campos = array('hr.id', "CONCAT(hr.correlativo,' (',p.nombres,' ',p.apellido_paterno,' ',p.apellido_materno,' - ',e.institucion,')') AS nombre, hr.correlativo");
        $where = array(
            'hr.id' => $id,
        );
        $builder = $db->table('sincobol.hoja_ruta AS hr')
        ->select($campos)
        ->join('sincobol.externo AS e', 'hr.fk_externo_remitente = e.id', 'left')
        ->join('sincobol.persona AS p', 'e.fk_persona = p.id', 'left')
        ->where($where);
        $datos = $builder->get()->getRowArray();
        return $datos;
    }

    public function obtenerDatosAreaMineraCmnCmc($id){
        $db = \Config\Database::connect('sincobol');
        $campos = array('am.id', "CONCAT(am.codigo_unico,' - ',am.nombre,' (',acm.nombre,' - ',tam.nombre,')') AS nombre");
        $where = array(
            'am.id' => $id,
        );
        $builder = $db->table('contratos_licencias.area_minera as am')
        ->select($campos)
        ->join('contratos_licencias.actor_minero as acm', 'am.fk_actor_minero_poseedor = acm.id', 'left')
            ->join('contratos_licencias.tipo_actor_minero as tam', 'acm.fk_tipo_actor_minero = tam.id', 'left')
        ->where($where);
        $datos = $builder->get()->getRowArray();
        return $datos;
    }

    public function obtenerPoligonoAreaMinera($id){
        $db = \Config\Database::connect('sincobol');
        $campos = array('the_geom');
        $builder = $db->table('contratos_licencias.poligono_area_minera')
        ->select($campos)
        ->where("activo AND the_geom is not null AND fk_area_minera = ".$id)
        ->orderBy('id', 'DESC');
        if($poligono = $builder->get()->getRowArray())
            return $poligono['the_geom'];
        return NULL;
    }

    public function obtenerUsuarioDestinatario($id){
        $db = \Config\Database::connect();
        $campos = array(
            'u.id', "CONCAT(u.nombre_completo, ' (',p.nombre,' - ',o.nombre,')') as nombre"
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

    public function obtenerDatosDocumento($ids, $fechas){
        $db = \Config\Database::connect();
        $campos = array(
            'doc.id', 'doc.correlativo', "TO_CHAR(doc.fecha, 'DD/MM/YYYY') as fecha", 'td.nombre as tipo_documento', 'td.notificacion'
        );
        $where = array(
            'doc.fk_usuario_creador' => session()->get('registroUser'),
            'doc.estado' => 'SUELTO',
        );
        $query = $db->table('documentos AS doc')
        ->select($campos)
        ->join('tipo_documento AS td', 'doc.fk_tipo_documento = td.id', 'left')
        ->where($where)
        ->whereIn('doc.id', $ids);
        $resultado = $query->get()->getResultArray();
        foreach($resultado as $i => $row){
            $index = array_search($row['id'], $ids);
            $resultado[$i]['fecha_notificacion'] = $fechas[$index];
        }
        return $resultado;
    }

    public function obtenerOficinasReporte(){
        $db = \Config\Database::connect();
        $builder = $db->table('public.oficinas')
        ->select('*')
        ->where('deleted_at IS NULL AND activo AND desconcentrado')
        ->orderBy('nombre');
        $oficinas = $builder->get()->getResult('array');

        $temporal = array();
        $temporal[''] = 'TODOS LAS DIRECCIONES DEPARTAMENTALES Y/O REGIONALES';

        foreach($oficinas as $row)
            $temporal[$row['id']] = $row['nombre'];

        return $temporal;
    }

    public function obtenerOficina($departamento){
        $db = \Config\Database::connect();
        $where = array(
            'departamento' => $departamento,
        );
        $builder = $db->table('oficinas')->where($where);
        if($result = $builder->get()->getRowArray())
            return $result['id'];
        else
            return NULL;
    }

    public function obtenerHrAnexadas($datos){
        if($datos){
            $db = \Config\Database::connect();
            $dbSincobol = \Config\Database::connect('sincobol');
            foreach($datos as $i=>$row){
                $where = array(
                    'fk_derivacion' => $row['id'],
                );
                $builder = $db->table('hr_anexadas')->where($where);
                if($resultado = $builder->get()->getResultArray()){
                    $ids_hr = array();
                    foreach($resultado as $row)
                        $ids_hr[] = $row['fk_hoja_ruta'];
                    $builder_sincobol = $dbSincobol->table('sincobol.hoja_ruta')->whereIn('id', $ids_hr);
                    $hrs = $builder_sincobol->get()->getResultArray();
                    $html = '';
                    foreach($hrs as $hr)
                        $html .='<a href="'.$this->urlSincobol.'correspondencia/hoja_ruta/ver/'.$hr['id'].'" target="_blank" title="Ver Hoja de Ruta">'.$hr['correlativo'].'</a><br>';
                    $datos[$i]['hoja_ruta_anexadas'] = $html;
                }else{
                    $datos[$i]['hoja_ruta_anexadas'] = '';
                }
            }
            return $datos;
        }
    }

    public function obtenerDocumentosAnexados($datos, $fk_area_minera){
        if($datos){
            $documentosModel = new DocumentosModel();
            foreach($datos as $i=>$row){
                $where = array(
                    'fk_derivacion' => $row['id'],
                );
                if($resultado = $documentosModel->where($where)->findAll()){
                    $html = '';
                    foreach($resultado as $doc){
                        if($doc['doc_digital'])
                            $html .= "<a href='".base_url($this->rutaArchivos.$fk_area_minera.'/'.$doc['doc_digital'])."' target='_blank' title='Ver Documento'>".$doc['correlativo']."</a><br>";
                        else
                            $html .= $doc['correlativo'].'<br>';
                    }
                    $datos[$i]['documentos_anexados'] = $html;
                }else{
                    $datos[$i]['documentos_anexados'] = '';
                }
            }
            return $datos;
        }
    }

    private function obtenerEstadoTramiteAPM($id_estado_padre, $id_estado_hijo){
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

    private function obtenerDocumentosAPM($id_acto_administrativo){
        $actoAdministrativoModel = new ActoAdministrativoModel();
        $resultado = '';
        if($fila = $actoAdministrativoModel->find($id_acto_administrativo)){
            if($fila['ultimo_fk_documentos']){
                $db = \Config\Database::connect();
                $campos = array(
                    'doc.correlativo', 'td.nombre as tipo_documento'
                );
                $where = array(
                    'doc.estado' => 'ANEXADO',
                );
                $query = $db->table('documentos AS doc')
                ->select($campos)
                ->join('tipo_documento AS td', 'doc.fk_tipo_documento = td.id', 'left')
                ->where($where)
                ->whereIn('doc.id', explode(',',$fila['ultimo_fk_documentos']));
                $documentos_anexados = $query->get()->getResultArray();
                $resultado = '<ul class="list-group list-group-flush">';
                foreach($documentos_anexados as $documento)
                    $resultado .= '<li class="list-group-item">'.$documento['tipo_documento'].'</li>';
                $resultado .= '</ul>';
            }
        }
        return $resultado;
    }

    public function ajaxHojaRutaMadre(){
        $cadena = mb_strtoupper($this->request->getPost('texto'));
        if(!empty($cadena) && session()->get('registroUser')){
            $data = array();
            $db = \Config\Database::connect();
            $campos = array('u.id', 'o.regional_busqueda');
            $builder = $db->table('public.usuarios as u')
            ->select($campos)
            ->join('public.oficinas as o', 'u.fk_oficina = o.id', 'left')
            ->where('u.activo AND u.id ='.session()->get('registroUser'));
            $usuario = $builder->get()->getRowArray();
            $regionales = explode(',',$usuario['regional_busqueda']);
            $dbSincobol = \Config\Database::connect('sincobol');
            $campos = array('slc.id', "CONCAT(am.nombre, ' - ', am.codigo_unico, ' (',slc.correlativo,')') as nombre");
            $where = array(
                'slc.estado_general' => 'EN TRAMITE',
                'slc.fk_tipo_solicitud' => $this->idTramite,
                'slc.fk_hoja_ruta > ' => 0,
            );
            $builder = $dbSincobol->table('contratos_licencias.solicitud_licencia_contrato as slc')
            ->select($campos)
            ->join('contratos_licencias.area_minera as am', 'slc.fk_area_minera = am.id', 'left')
            ->where($where)
            ->whereIn('am.regional', $regionales)
            ->like("CONCAT(am.nombre, ' - ', am.codigo_unico, ' (',slc.correlativo,')')", $cadena)
            ->orderBy('slc.id','DESC')
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
                    'id' => 0,
                    'text' => 'No se encuentra la hoja de ruta que busca'
                );
            }
            echo json_encode($data);
        }
    }

    public function ajaxDatosHR(){
        $idSolicitud = $this->request->getPost('id');
        if(!empty($idSolicitud)){
            if($data = $this->informacionAreaMinera($idSolicitud))
                echo json_encode($data);
        }
    }

    public function ajaxHojaRutaCmnCmc(){
        $cadena = mb_strtoupper($this->request->getPost('texto'));
        if(!empty($cadena) && session()->get('registroUser')){
            $data = array();
            $dbSincobol = \Config\Database::connect('sincobol');
            $campos = array('hr.id', "CONCAT(hr.correlativo,' (',p.nombres,' ',p.apellido_paterno,' ',p.apellido_materno,' - ',e.institucion,')') AS nombre");
            $where = array(
                'hr.fk_tipo_hoja_ruta' => 7,
            );
            $builder = $dbSincobol->table('sincobol.hoja_ruta AS hr')
            ->select($campos)
            ->join('sincobol.externo AS e', 'hr.fk_externo_remitente = e.id', 'left')
            ->join('sincobol.persona AS p', 'e.fk_persona = p.id', 'left')
            ->where($where)
            ->like("hr.correlativo", $cadena)
            ->orderBy('hr.id','DESC')
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
                    'id' => 0,
                    'text' => 'No se encuentra la hoja de ruta que busca'
                );
            }
            echo json_encode($data);
        }
    }

    public function ajaxDatosHRCmnCmc(){
        $idHR = $this->request->getPost('id');
        if(!empty($idHR)){
            if($data = $this->informacionHRCmnCmc($idHR))
                echo json_encode($data);
        }
    }

    public function ajaxAreaMineraCmnCmc(){
        $cadena = mb_strtoupper($this->request->getPost('texto'));
        if(!empty($cadena) && session()->get('registroUser')){
            $data = array();
            $dbSincobol = \Config\Database::connect('sincobol');
            $campos = array('am.id', "CONCAT(am.codigo_unico,' - ',am.nombre,' (',acm.nombre,' - ',tam.nombre,')') AS nombre");
            $where = array(
                'am.fk_tipo_area_minera' => 11,
                //'am.vigente' => 'true',
            );
            $builder = $dbSincobol->table('contratos_licencias.area_minera as am')
            ->select($campos)
            ->join('contratos_licencias.actor_minero as acm', 'am.fk_actor_minero_poseedor = acm.id', 'left')
            ->join('contratos_licencias.tipo_actor_minero as tam', 'acm.fk_tipo_actor_minero = tam.id', 'left')
            //->where($where)
            ->like("CONCAT(am.codigo_unico,' - ',am.nombre)", $cadena)
            ->whereIn('am.fk_tipo_area_minera', array(1, 11))
            ->orderBy('am.id','DESC')
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
                    'id' => 0,
                    'text' => 'No se encuentra el area minera que busca'
                );
            }
            echo json_encode($data);
        }
    }

    public function ajaxDatosAreaMineraCmnCmc(){
        $idAreaMinera = $this->request->getPost('id');
        if(!empty($idAreaMinera)){
            if($data = $this->informacionAreaMineraCmnCmc($idAreaMinera))
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
                //'u.fk_oficina' => session()->get('registroOficina'),
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

    public function ajaxEstadoTramiteHijo(){
        $id_padre = $this->request->getPost('id_padre');
        $estadoTramiteModel = new EstadoTramiteModel();
        $html = '<option value="">SELECCIONE UNA OPCIÓN</option>';
        if($id_padre){
            $categoria = $estadoTramiteModel->find($id_padre);
            $db = \Config\Database::connect();
            $builder = $db->table('public.estado_tramite')
            ->where('deleted_at IS NULL AND fk_estado_padre = '.$id_padre)
            ->orderBy('orden');
            $datos = $builder->get()->getResult('array');
            if($datos){
                foreach($datos as $row){
                    $html .= '<option value="'.$row['id'].'" data-anexar="'.$row['anexar_documentos'].'" >'.$categoria['orden'].'.'.$row['orden'].'. '.$row['nombre'].'</option>';
                }
            }
        }
        echo $html;
    }

    public function ajaxEstadoTramiteHijoReporte(){
        $id_padre = $this->request->getPost('id_padre');
        $estadoTramiteModel = new EstadoTramiteModel();
        $html = '<option value="">TODOS LOS SUBESTADOS</option>';
        if($id_padre){
            $categoria = $estadoTramiteModel->find($id_padre);
            $db = \Config\Database::connect();
            $builder = $db->table('public.estado_tramite')
            ->where('deleted_at IS NULL AND fk_estado_padre = '.$id_padre)
            ->orderBy('orden');
            $datos = $builder->get()->getResult('array');
            if($datos){
                foreach($datos as $row){
                    $html .= '<option value="'.$row['id'].'">'.$categoria['orden'].'.'.$row['orden'].'. '.$row['nombre'].'</option>';
                }
            }
        }
        echo $html;
    }

    public function ajaxHrInEx(){
        $cadena = mb_strtoupper($this->request->getPost('texto'));
        if(!empty($cadena) && session()->get('registroUser')){
            $data = array();
            $db = \Config\Database::connect();
            $campos = array('u.id', 'o.fk_oficina_sincobol');
            $builder = $db->table('public.usuarios as u')
            ->select($campos)
            ->join('public.oficinas as o', 'u.fk_oficina = o.id', 'left')
            ->where('u.activo AND u.id ='.session()->get('registroUser'));
            $usuario = $builder->get()->getRowArray();
            $oficinas = explode(',',$usuario['fk_oficina_sincobol']);

            $dbSincobol = \Config\Database::connect('sincobol');
            $builder = $dbSincobol->table('sincobol.vista_buscador_hoja_ruta_actualizado')
            ->where("anexado_siseg = 'NO'")
            ->whereIn('fk_oficina', $oficinas)
            ->like("correlativo", $cadena)
            ->orderBy('id','DESC')
            ->limit(10);
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
                    'id' => 0,
                    'text' => 'No se encontro la H.R. o se encuentra Anexada o Archivada en el SINCOBOL'
                );
            }
            echo json_encode($data);
        }
    }

    public function ajaxDatosHrInEx(){
        $resultado = array(
            'estado' => 'error',
            'texto' => 'Envio de peticion erroneo.',
        );
        $id = $this->request->getPost('id');
        if(!empty($id) && session()->get('registroUser')){
            if($fila = $this->obtenerDatosHrInExSincobol($id)){
                $resultado = array(
                    'estado' => 'success',
                );
                echo json_encode(array_merge($resultado, $fila));
            }else{
                $resultado = array(
                    'estado' => 'error',
                    'texto' => 'No se encuentra el area minera.',
                );
                echo json_encode($resultado);
            }
        }else{
            echo json_encode($resultado);
        }
    }

    public function obtenerDatosHrInExSincobol($id){
        if($id){
            $dbSincobol = \Config\Database::connect('sincobol');
            $campos = array('hr.id','thr.nombre as tipo_hoja_ruta', 'hr.correlativo', "TO_CHAR(hr.fecha, 'DD/MM/YYYY') as fecha, hr.referencia",
            "CONCAT(pd.nombres, p.nombres, ' ', pd.apellido_paterno, p.apellido_paterno, ' ', pd.apellido_materno, p.apellido_materno, '<br />', c.nombre , e.cargo, '<br />', a.nombre ,e.institucion) as remitente",
            "CONCAT(d.correlativo, hr.cite_documento_externo, '<br />', TO_CHAR(d.fecha_creacion, 'DD/MM/YYYY'), TO_CHAR(hr.fecha_cite_externo, 'DD/MM/YYYY')) as cite");
            $where = array(
                'hr.id' => $id
            );
            $builder = $dbSincobol->table('sincobol.hoja_ruta as hr')
            ->select($campos)
            ->join('sincobol.tipo_hoja_ruta as thr', 'hr.fk_tipo_hoja_ruta=thr.id', 'left')
            ->join('sincobol.externo as e', 'e.id=hr.fk_externo_remitente', 'left')
            ->join('sincobol.documento as d', 'd.id=hr.fk_documento_original', 'left')
            ->join('sincobol.persona as p', 'p.id=e.fk_persona', 'left')
            ->join('sincobol.asignacion_cargo as ac', 'ac.id=d.fk_asignacion_cargo', 'left')
            ->join('sincobol.cargo as c', 'c.id=ac.fk_cargo', 'left')
            ->join('sincobol.area as a', 'a.id=c.fk_area', 'left')
            ->join('sincobol.persona as pd', 'pd.id=ac.fk_persona', 'left')
            ->where($where);
            if($fila = $builder->get()->getRowArray())
                return $fila;
        }
        return false;
    }

    private function informacionAreaMinera($id_solicitud){
        if(isset($id_solicitud) && $id_solicitud > 0){

            $dbSincobol = \Config\Database::connect('sincobol');
            $campos = array('slc.fk_area_minera', 'hr.referencia', "to_char(slc.fecha_ingreso, 'YYYY-MM-DD HH24:MI') as fecha_mecanizada",
            'am.nombre as denominacion', 'am.codigo_unico', "CONCAT(ROUND(am.extension), ' ', CASE WHEN am.unidad = 'CUADRICULA' THEN 'CUADRICULA(S)' ELSE am.unidad END) AS extension",
            'am.departamentos', 'am.provincias', 'am.municipios', 'am.descripcion_area_protegida as area_protegida', 'am.regional', 'acm.nombre as titular', 'tam.nombre as clasificacion',
            'dcam.domicilio_legal', 'dcam.domicilio_procesal', 'dcam.telefonos as telefono_solicitante', "CONCAT(p.nombres, ' ',p.apellido_paterno, ' ',p.apellido_materno) as representante_legal", 'acm.nacionalidad');
            $where = array(
                'slc.id' => $id_solicitud,
            );
            $builder = $dbSincobol->table('contratos_licencias.solicitud_licencia_contrato as slc')
            ->select($campos)
            ->join('sincobol.hoja_ruta as hr', 'slc.fk_hoja_ruta = hr.id', 'left')
            ->join('contratos_licencias.area_minera as am', 'slc.fk_area_minera = am.id', 'left')
            ->join('contratos_licencias.actor_minero as acm', 'am.fk_actor_minero_poseedor = acm.id', 'left')
            ->join('contratos_licencias.tipo_actor_minero as tam', 'acm.fk_tipo_actor_minero = tam.id', 'left')
            ->join('sincobol.datos_contacto_actor_minero as dcam', 'acm.id = dcam.fk_actor_minero', 'left')
            ->join('contratos_licencias.persona_actor_minero as pacm', "acm.id = pacm.fk_actor_minero AND pacm.tipo_relacion='REPRESENTANTE LEGAL'", 'left')
            ->join('sincobol.persona as p', 'pacm.fk_persona = p.id', 'left')
            ->where($where)
            ->orderBy('pacm.id', 'DESC')
            ->limit(1);
            $datos = $builder->get()->getFirstRow('array');
            if($datos)
                return $datos;
            else
                return false;
        }else{
            return false;
        }
    }

    private function informacionHRCmnCmc($id_hr){
        if(isset($id_hr) && $id_hr > 0){
            $dbSincobol = \Config\Database::connect('sincobol');
            $campos = array('hr.fecha_creacion as fecha_mecanizada', 'hr.referencia', 'hr.cite_documento_externo as cite_documento', 'e.institucion as procedencia',
            "CONCAT(p.nombres,' ',p.apellido_paterno,' ',p.apellido_materno) AS remitente", 'e.cargo', 'hr.correlativo');
            $where = array(
                'hr.id' => $id_hr
            );
            $builder = $dbSincobol->table('sincobol.hoja_ruta AS hr')
            ->select($campos)
            ->join('sincobol.externo AS e', 'hr.fk_externo_remitente = e.id', 'left')
            ->join('sincobol.persona AS p', 'e.fk_persona = p.id', 'left')
            ->where($where);
            $datos = $builder->get()->getFirstRow('array');
            if($datos)
                return $datos;
            else
                return false;
        }else{
            return false;
        }
    }

    private function informacionAreaMineraCmnCmc($id_area_minera){
        if(isset($id_area_minera) && $id_area_minera > 0){
            $dbSincobol = \Config\Database::connect('sincobol');
            $campos = array('am.nombre as denominacion', 'am.codigo_unico', "CONCAT(ROUND(am.extension), ' ', CASE WHEN am.unidad = 'CUADRICULA' THEN 'CUADRICULA(S)' ELSE am.unidad END) AS extension",
            'am.departamentos', 'am.provincias', 'am.municipios', 'am.descripcion_area_protegida as area_protegida', 'am.regional', 'acm.nombre as titular', 'tam.nombre as clasificacion',
            'dcam.domicilio_legal', 'dcam.domicilio_procesal', 'dcam.telefonos as telefono_solicitante', "CONCAT(p.nombres, ' ',p.apellido_paterno, ' ',p.apellido_materno) as representante_legal", 'acm.nacionalidad');
            $where = array(
                'am.id' => $id_area_minera,
            );
            $builder = $dbSincobol->table('contratos_licencias.area_minera as am')
            ->select($campos)
            ->join('contratos_licencias.actor_minero as acm', 'am.fk_actor_minero_poseedor = acm.id', 'left')
            ->join('contratos_licencias.tipo_actor_minero as tam', 'acm.fk_tipo_actor_minero = tam.id', 'left')
            ->join('sincobol.datos_contacto_actor_minero as dcam', 'acm.id = dcam.fk_actor_minero', 'left')
            ->join('contratos_licencias.persona_actor_minero as pacm', "acm.id = pacm.fk_actor_minero AND pacm.tipo_relacion = 'REPRESENTANTE LEGAL'", 'left')
            ->join('sincobol.persona as p', 'pacm.fk_persona = p.id', 'left')
            ->where($where)
            ->orderBy('pacm.id', 'DESC')
            ->limit(1);
            $datos = $builder->get()->getFirstRow('array');
            if($datos)
                return $datos;
            else
                return false;
        }else{
            return false;
        }
    }

    private function informacionHRExterna($id_acto_administrativo){
        if(isset($id_acto_administrativo) && $id_acto_administrativo > 0){
            $db = \Config\Database::connect();
            $campos = array(
                'ce.id',"to_char(ce.created_at, 'DD/MM/YYYY') as fecha_ingreso","CASE WHEN ce.fecha_atencion IS NULL THEN (CURRENT_DATE - ce.created_at::date)::text ELSE '' END as dias_pasados",
                "CONCAT(ui.nombre_completo,'<br><b>',pi.nombre,'<b>') as ingreso","to_char(ce.fecha_recepcion, 'DD/MM/YYYY') as fecha_recepcion","CONCAT(ur.nombre_completo,'<br><b>',pr.nombre,'<b>') as recepcion",
                "CONCAT('Tipo Documento: ',tde.nombre,'<br>CITE: ',ce.cite,'<br>Fecha: ',to_char(ce.fecha_cite, 'DD/MM/YYYY'),'<br>Remitente: ',CONCAT(pe.nombres, ' ', pe.apellidos, ' (', pe.institucion, ' - ',pe.cargo,')'),'<br>Referencia: ',ce.referencia) as documento_externo",
                'ce.doc_digital','ce.observacion_recepcion','ce.observacion_atencion','tde.notificar','tde.dias_intermedio', 'tde.dias_limite',
            );
            $where = array(
                'ce.fk_tramite' => $this->idTramite,
                'ce.fk_acto_administrativo' => $id_acto_administrativo,
                'ce.deleted_at' => NULL,
                'ce.estado' => 'RECIBIDO',
            );
            $builder = $db->table('public.correspondencia_externa AS ce')
            ->join('public.persona_externa AS pe', 'ce.fk_persona_externa = pe.id', 'left')
            ->join('public.tipo_documento_externo AS tde', 'ce.fk_tipo_documento_externo = tde.id', 'left')
            ->join('public.usuarios AS ui', 'ce.fk_usuario_creador = ui.id', 'left')
            ->join('public.perfiles AS pi', 'ui.fk_perfil = pi.id', 'left')
            ->join('public.usuarios AS ur', 'ce.fk_usuario_recepcion = ur.id', 'left')
            ->join('public.perfiles AS pr', 'ur.fk_perfil = pr.id', 'left')
            ->select($campos)
            ->where($where)
            ->orderBy('ce.created_at ASC');
            if($datos = $builder->get()->getResultArray())
                return $datos;
            else
                return false;
        }else{
            return false;
        }
    }

    public function ajaxBuscarTramite(){
        $cadena = mb_strtoupper($this->request->getPost('texto'));
        if(!empty($cadena) && session()->get('registroUser')){
            $db = \Config\Database::connect();
            $campos = array('ac.id', 'ac.correlativo', 'dam.codigo_unico', 'dam.denominacion',
            "CONCAT(ac.correlativo,' (',dam.codigo_unico,' - ',dam.denominacion,')') as hr");
            $where = array(
                'ac.deleted_at' => NULL,
                'ac.fk_oficina' => session()->get('registroOficina')
            );
            $builder = $db->table('public.acto_administrativo as ac')
            ->select($campos)
            ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
            ->where($where)
            ->like("CONCAT(ac.correlativo,' (',dam.codigo_unico,' - ',dam.denominacion,')')", $cadena)
            ->limit(10);
            $datos = $builder->get()->getResultArray();
            if($datos){
                foreach($datos as $row){
                    $data[] = array(
                        'id' => $row['id'],
                        'text' => $row['hr'],
                    );
                }
            }else{
                $data[] = array(
                    'id' => 0,
                    'text' => 'No se encuentra la hoja de ruta que busca'
                );
            }
            echo json_encode($data);
        }
    }

    public function ajaxDatosTramite(){
        $id = $this->request->getPost('id');
        if(!empty($id)){
            $db = \Config\Database::connect();
            $campos = array('ac.id', 'dam.codigo_unico', 'dam.denominacion', 'dam.representante_legal', 'dam.nacionalidad', 'dam.titular', 'dam.clasificacion_titular',
                "CONCAT(ur.nombre_completo, ' - ',pr.nombre) as remitente", "CONCAT(ud.nombre_completo, ' - ',pd.nombre) as destinatario", "CONCAT(ua.nombre_completo,' - ',pa.nombre) as responsable");
            $where = array(
                'ac.deleted_at' => NULL,
                'ac.id' => $id,
            );
            $builder = $db->table('public.acto_administrativo as ac')
            ->select($campos)
            ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
            ->join('usuarios as ur', 'ac.ultimo_fk_usuario_remitente = ur.id', 'left')
            ->join('perfiles as pr', 'ur.fk_perfil=pr.id', 'left')
            ->join('usuarios as ud', 'ac.ultimo_fk_usuario_destinatario = ud.id', 'left')
            ->join('perfiles as pd', 'ud.fk_perfil=pd.id', 'left')
            ->join('usuarios as ua', 'ac.ultimo_fk_usuario_responsable = ua.id', 'left')
            ->join('perfiles as pa', 'ua.fk_perfil = pa.id', 'left')
            ->where($where);
            $resultado = array();
            if($tramite = $builder->get()->getRowArray()){
                $resultado['codigo_unico'] = $tramite['codigo_unico'];
                $resultado['denominacion'] = $tramite['denominacion'];
                $resultado['representante_legal'] = $tramite['representante_legal'];
                $resultado['nacionalidad'] = $tramite['nacionalidad'];
                $resultado['titular'] = $tramite['titular'];
                $resultado['clasificacion'] = $tramite['clasificacion_titular'];
                $resultado['remitente'] = $tramite['remitente'];
                $resultado['destinatario'] = $tramite['destinatario'];
                $resultado['responsable'] = $tramite['responsable'];
            }
            echo json_encode($resultado);
        }
    }

    public function generarCodigoSeguimiento(){

        if ($this->request->getPost()) {
            $actoAdministrativoModel = new ActoAdministrativoModel();
            $codigoSeguimientoModel = new CodigoSeguimientoModel();
            $validation = $this->validate([
                'fk_acto_administrativo' => [
                    'rules' => 'required',
                ],
            ]);
            if(!$validation){
                $db = \Config\Database::connect();
                $campos = array('ac.id', "CONCAT(ac.correlativo,' (',dam.codigo_unico,' - ',dam.denominacion,')') as hr");
                $where = array(
                    'ac.deleted_at' => NULL,
                    'ac.id' => $this->request->getPost('fk_acto_administrativo'),
                );
                $builder = $db->table('public.acto_administrativo as ac')
                ->select($campos)
                ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
                ->where($where);
                $contenido['fk_tramite'] = $this->request->getPost('fk_tramite');
                $contenido['hr_madre'] = $builder->get()->getRowArray();
                $contenido['validation'] = $this->validator;
            }else{
                $acto_administrativo = $actoAdministrativoModel->find($this->request->getPost('fk_acto_administrativo'));
                $codido_seguimiento = substr(str_shuffle("0123456789"),0,6);
                $data = array(
                    'id' => $acto_administrativo['id'],
                    'codigo_seguimiento' => $codido_seguimiento,
                );
                if($actoAdministrativoModel->save($data) === false){
                    session()->setFlashdata('fail', $actoAdministrativoModel->errors());
                }else{
                    $dataSeguimiento = array(
                        'fk_acto_administrativo' => $acto_administrativo['id'],
                        'codigo_seguimiento' => $codido_seguimiento,
                        'fk_usuario_creador' => session()->get('registroUser'),
                    );
                    if($codigoSeguimientoModel->insert($dataSeguimiento) === false)
                        session()->setFlashdata('fail', $codigoSeguimientoModel->errors());
                    else
                    session()->setFlashdata('success', 'Se ha Generado Correctamente el Código de Seguimiento. Puede descargarlo haciendo  <a href="'.base_url($this->controlador.'pdf_seguimiento/'.$acto_administrativo['id']).'" target="_blank" class="btn btn-inverse">Click Aquí</a>');
                }

                return redirect()->to($this->controlador.'generar_codigo_seguimiento');
            }
        }

        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Generar Código de Seguimiento';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['accion'] = $this->controlador.'generar_codigo_seguimiento';
        $contenido['validation'] = $this->validator;
        $contenido['controlador'] = $this->controlador;
        $data['content'] = view($this->carpeta.'generar_codigo_seguimiento', $contenido);
        $data['menu_actual'] = 'correspondencia_externa/generar_codigo_seguimiento';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['validacion_js'] = 'generar_codigo_seguimiento-validation.js';
        echo view('templates/template', $data);
    }

    private function obtenerTramites(){
        $tramitesModel = new TramitesModel();
        return $tramitesModel->where('activo = true')->orderBy('nombre', 'ASC')->findAll();
    }

    public function pdfCodigoSeguimiento($id){
        $db = \Config\Database::connect();
        $where = array(
            'ac.deleted_at' => NULL,
            'ac.id' => $id,
        );
        $builder = $db->table('public.acto_administrativo as ac')
        ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
        ->where($where);
        if($fila = $builder->get()->getRowArray()){
            $codigoSeguimientoModel = new CodigoSeguimientoModel();
            $fontPDF = 'helvetica';
            $file_name = str_replace('/','-',$fila['correlativo']).'.pdf';
            $contenido['fila'] = $fila;
            $contenido['codigo_seguimiento'] = $codigoSeguimientoModel->select(array('codigo_seguimiento', "TO_CHAR(created_at, 'DD/MM/YYYY HH24:MI') as fecha"))->where(array('fk_acto_administrativo'=>$fila['id'], 'codigo_seguimiento'=>$fila['codigo_seguimiento']))->first();
            $contenido['color'] = '#06FFEC';
            $html = view($this->carpeta.'pdf_codigo_seguimiento', $contenido);

            $pdf = new CodigoSeguimientoPdf('P', 'mm', 'Letter', true, 'UTF-8', false);

            $pdf->SetCreator('GARNET');
            $pdf->SetAuthor('Desarrollo de UTIC');
            $pdf->SetTitle('Código de Seguimiento CAM');
            $pdf->SetKeywords('Contrato, Administrativo, Minero');

            //establecer margenes
            $pdf->SetMargins(10, 45, 10);
            $pdf->SetAutoPageBreak(true, 35); //Margin botto

            $pdf->AddPage('P','Letter');
            // Titulo de paginas
            $pdf->SetFont($fontPDF, 'B', 16);
            $pdf->Cell(0,0,'CÓDIGO DE SEGUIMIENTO',0,0,'C');
            $pdf->SetFont($fontPDF, '', 11);
            $pdf->Ln(10);
            $pdf->MultiCell(0,0, 'El presente documento contiene el código de seguridad que deberá utilizar para realizar el seguimiento de su trámite de Contrato Administrativo Minero a través de internet.', 0, 'J');
            $pdf->Ln(5);
            $pdf->writeHTML($html, true, false, false, false, '');

            $pdf->Output($file_name);
            exit();
        }
    }

    public function migrarSolCam(){
        $migrarModel = new MigrarCAMModel();
        $actoAdministrativoModel = new ActoAdministrativoModel();
        $datosAreaMineraModel = new DatosAreaMineraModel();
        $derivacionModel = new DerivacionModel();
        $derivacionSincobolModel = new DerivacionSincobolModel();
        $solicitudLicenciaContratoModel = new SolicitudLicenciaContratoModel();
        $where = array(
            'tipo' => 'SOL-CAM',
            'migrado' => false,
            'fk_oficina' => 2,
        );
        if($datos = $migrarModel->where($where)->findAll(200)){
            foreach($datos as $n => $row){
                $hoja_ruta = $this->obtenerDatosMigracion($row['fk_hoja_ruta_sincobol']);
                $errorActoAdministrativo = 'NO';
                $errorDatosAreaMinera = 'NO';
                $errorDerivacion = 'NO';
                $errorDerivacionSincobol = 'NO';
                $errorSolicitudLicencia = 'NO';
                $errorMigracion = 'NO';
                $estado = 'MIGRADO';
                $fk_estado_tramite_padre = $this->obtenerIdEstadoPadre($row['codigo_estado']);
                $fk_estado_tramite_hijo = '';
                if($row['codigo_subestado']>0)
                    $fk_estado_tramite_hijo = $this->obtenerIdEstadoHijo($fk_estado_tramite_padre, $row['codigo_subestado']);
                $data = array(
                    'fk_solicitud_licencia_contrato' => $hoja_ruta['fk_solicitud_licencia_contrato'],
                    'fk_area_minera' => $hoja_ruta['fk_area_minera'],
                    'fk_hoja_ruta' => $hoja_ruta['fk_hoja_ruta'],
                    'fk_oficina' => $row['fk_oficina'],
                    'correlativo' => $hoja_ruta['correlativo'],
                    'fecha_mecanizada' => $hoja_ruta['fecha_mecanizada'],
                    'fk_usuario_actual' => $row['fk_usuario_remitente'],
                    'ultimo_fk_usuario_responsable' => $row['fk_usuario_destinatario'],
                    'ultimo_estado' => $estado,
                    'ultimo_fk_estado_tramite_padre' => $fk_estado_tramite_padre,
                    'ultimo_fk_estado_tramite_hijo' => (!empty($fk_estado_tramite_hijo) ? $fk_estado_tramite_hijo : NULL),
                    'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                    'fk_usuario_creador' => $row['fk_usuario_remitente'],
                    'ultimo_instruccion' => 'PARA SU ATENCIÓN',
                    'ultimo_fk_usuario_remitente' => $row['fk_usuario_remitente'],
                    'ultimo_fk_usuario_destinatario' => $row['fk_usuario_destinatario'],
                    'fk_tipo_hoja_ruta' => 1,
                    'estado_tramite_apm' => $this->obtenerEstadoTramiteAPM($fk_estado_tramite_padre, $fk_estado_tramite_hijo),
                );
                if($actoAdministrativoModel->insert($data) === false){
                    $errorActoAdministrativo = 'SI';
                }else{
                    $idActoAdministrativo = $actoAdministrativoModel->getInsertID();
                    $dataDatosAreaMinera = array(
                        'fk_acto_administrativo' => $idActoAdministrativo,
                        'codigo_unico' => $hoja_ruta['codigo_unico'],
                        'denominacion' => $hoja_ruta['denominacion'],
                        'extension' => $hoja_ruta['extension'],
                        'departamentos' => $hoja_ruta['departamentos'],
                        'provincias' => $hoja_ruta['provincias'],
                        'municipios' => $hoja_ruta['municipios'],
                        'regional' => $hoja_ruta['regional'],
                        'area_protegida' => $hoja_ruta['area_protegida'],
                        'representante_legal' => $hoja_ruta['representante_legal'],
                        'nacionalidad' => $hoja_ruta['nacionalidad'],
                        'titular' => $hoja_ruta['titular'],
                        'clasificacion_titular' => $hoja_ruta['clasificacion_titular'],
                    );
                    if($datosAreaMineraModel->insert($dataDatosAreaMinera) === false){
                        $errorDatosAreaMinera = 'SI';
                    }else{
                        $dataDerivacion = array(
                            'fk_acto_administrativo' => $idActoAdministrativo,
                            'domicilio_legal' => $hoja_ruta['domicilio_legal'],
                            'domicilio_procesal' => $hoja_ruta['domicilio_procesal'],
                            'telefono_solicitante' => $hoja_ruta['telefono_solicitante'],
                            'fk_estado_tramite_padre' => $fk_estado_tramite_padre,
                            'fk_estado_tramite_hijo' => (!empty($fk_estado_tramite_hijo) ? $fk_estado_tramite_hijo : NULL),
                            'fk_usuario_remitente' => $row['fk_usuario_remitente'],
                            'fk_usuario_destinatario' => $row['fk_usuario_destinatario'],
                            'fk_usuario_responsable' => $row['fk_usuario_destinatario'],
                            'instruccion' => 'PARA SU ATENCIÓN',
                            'estado' => $estado,
                            'fk_usuario_creador' => $row['fk_usuario_remitente'],
                        );
                        if($derivacionModel->insert($dataDerivacion) === false){
                            $errorDerivacion = 'SI';
                        }else{
                            $campos = array('id', 'fk_hoja_ruta');
                            $where = array(
                                'fk_hoja_ruta' => $hoja_ruta['fk_hoja_ruta'],
                            );
                            $ultima_derivacion = $derivacionSincobolModel->select($campos)->where($where)->orderBY('id', 'DESC')->first();
                            $dataDerivacionSincobol = array(
                                'id' => $ultima_derivacion['id'],
                                'estado' => 'CONCLUIDO',
                                'fecha_conclusion' => date('Y-m-d h:i:s'),
                                'motivo_conclusion' => 'MIGRADO AL SISTEMA DE CONTROL Y SEGUIMIENTO DE TRAMITES',
                            );
                            if($derivacionSincobolModel->save($dataDerivacionSincobol) === false){
                                $errorDerivacionSincobol = 'SI';
                            }else{
                                $dataSolicitudLicencia = array(
                                    'id' => $hoja_ruta['fk_solicitud_licencia_contrato'],
                                    'fk_acto_administrativo' => $idActoAdministrativo,
                                );
                                if($solicitudLicenciaContratoModel->save($dataSolicitudLicencia) === false){
                                    $errorSolicitudLicencia = 'SI';
                                }else{
                                    $dataMigracion = array(
                                        'id' => $row['id'],
                                        'migrado' => 'true',
                                    );
                                    if($migrarModel->save($dataMigracion) === false)
                                        $errorMigracion = 'SI';
                                }
                            }
                        }
                    }
                }
                echo ($n+1).'. La H.R. '.$hoja_ruta['correlativo'].' tuvo errores en A:'.$errorActoAdministrativo.' B:'.$errorDatosAreaMinera.' C:'.$errorDerivacion.' D:'.$errorDerivacionSincobol.' E:'.$errorSolicitudLicencia.' F:'.$errorMigracion.'<br>';
            }
        }
    }
    public function migrarCmcCmc(){
        $migrarModel = new MigrarCAMModel();
        $actoAdministrativoModel = new ActoAdministrativoModel();
        $datosAreaMineraModel = new DatosAreaMineraModel();
        $derivacionModel = new DerivacionModel();
        $derivacionSincobolModel = new DerivacionSincobolModel();
        $where = array(
            'tipo' => 'CMN-CMC',
            'migrado' => false,
            'fk_oficina' => 2,
        );
        if($datos = $migrarModel->where($where)->findAll(200)){
            foreach($datos as $n => $row){
                $hoja_ruta = $this->informacionHRCmnCmc($row['fk_hoja_ruta_sincobol']);
                $area_minera = $this->informacionAreaMineraCmnCmc($row['fk_area_minera']);
                $errorActoAdministrativo = 'NO';
                $errorDatosAreaMinera = 'NO';
                $errorDerivacion = 'NO';
                $errorDerivacionSincobol = 'NO';
                $errorMigracion = 'NO';
                $estado = 'MIGRADO';
                $fk_tipo_hoja_ruta = 0;
                if(strpos($hoja_ruta['correlativo'],'CMN'))
                    $fk_tipo_hoja_ruta = 2;
                elseif(strpos($hoja_ruta['correlativo'],'CMC'))
                    $fk_tipo_hoja_ruta = 3;
                $fk_estado_tramite_padre = $this->obtenerIdEstadoPadre($row['codigo_estado']);
                $fk_estado_tramite_hijo = '';
                if($row['codigo_subestado']>0)
                    $fk_estado_tramite_hijo = $this->obtenerIdEstadoHijo($fk_estado_tramite_padre, $row['codigo_subestado']);
                $data = array(
                    'fk_area_minera' => $row['fk_area_minera'],
                    'fk_hoja_ruta' => $row['fk_hoja_ruta_sincobol'],
                    'fk_oficina' => $row['fk_oficina'],
                    'correlativo' => $hoja_ruta['correlativo'],
                    'fecha_mecanizada' => $hoja_ruta['fecha_mecanizada'],
                    'fk_usuario_actual' => $row['fk_usuario_remitente'],
                    'ultimo_fk_usuario_responsable' => $row['fk_usuario_destinatario'],
                    'ultimo_estado' => $estado,
                    'ultimo_fk_estado_tramite_padre' => $fk_estado_tramite_padre,
                    'ultimo_fk_estado_tramite_hijo' => (!empty($fk_estado_tramite_hijo) ? $fk_estado_tramite_hijo : NULL),
                    'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                    'fk_usuario_creador' => $row['fk_usuario_remitente'],
                    'ultimo_instruccion' => 'PARA SU ATENCIÓN',
                    'ultimo_fk_usuario_remitente' => $row['fk_usuario_remitente'],
                    'ultimo_fk_usuario_destinatario' => $row['fk_usuario_destinatario'],
                    'fk_tipo_hoja_ruta' => $fk_tipo_hoja_ruta,
                    'estado_tramite_apm' => $this->obtenerEstadoTramiteAPM($fk_estado_tramite_padre, $fk_estado_tramite_hijo),
                );
                if($actoAdministrativoModel->insert($data) === false){
                    $errorActoAdministrativo = 'SI';
                }else{
                    $idActoAdministrativo = $actoAdministrativoModel->getInsertID();
                    $dataDatosAreaMinera = array(
                        'fk_acto_administrativo' => $idActoAdministrativo,
                        'codigo_unico' => $area_minera['codigo_unico'],
                        'denominacion' => $area_minera['denominacion'],
                        'extension' => $area_minera['extension'],
                        'departamentos' => $area_minera['departamentos'],
                        'provincias' => $area_minera['provincias'],
                        'municipios' => $area_minera['municipios'],
                        'regional' => $area_minera['regional'],
                        'area_protegida' => $area_minera['area_protegida'],
                        'representante_legal' => $area_minera['representante_legal'],
                        'nacionalidad' => $area_minera['nacionalidad'],
                        'titular' => $area_minera['titular'],
                        'clasificacion_titular' => $area_minera['clasificacion'],
                    );
                    if($datosAreaMineraModel->insert($dataDatosAreaMinera) === false){
                        $errorDatosAreaMinera = 'SI';
                    }else{
                        $dataDerivacion = array(
                            'fk_acto_administrativo' => $idActoAdministrativo,
                            'domicilio_legal' => $area_minera['domicilio_legal'],
                            'domicilio_procesal' => $area_minera['domicilio_procesal'],
                            'telefono_solicitante' => $area_minera['telefono_solicitante'],
                            'fk_estado_tramite_padre' => $fk_estado_tramite_padre,
                            'fk_estado_tramite_hijo' => (!empty($fk_estado_tramite_hijo) ? $fk_estado_tramite_hijo : NULL),
                            'fk_usuario_remitente' => $row['fk_usuario_remitente'],
                            'fk_usuario_destinatario' => $row['fk_usuario_destinatario'],
                            'fk_usuario_responsable' => $row['fk_usuario_destinatario'],
                            'instruccion' => 'PARA SU ATENCIÓN',
                            'estado' => $estado,
                            'fk_usuario_creador' => $row['fk_usuario_remitente'],
                        );
                        if($derivacionModel->insert($dataDerivacion) === false){
                            $errorDerivacion = 'SI';
                        }else{
                            $campos = array('id', 'fk_hoja_ruta');
                            $where = array(
                                'fk_hoja_ruta' => $row['fk_hoja_ruta_sincobol'],
                            );
                            $ultima_derivacion = $derivacionSincobolModel->select($campos)->where($where)->orderBY('id', 'DESC')->first();
                            $dataDerivacionSincobol = array(
                                'id' => $ultima_derivacion['id'],
                                'estado' => 'CONCLUIDO',
                                'fecha_conclusion' => date('Y-m-d h:i:s'),
                                'motivo_conclusion' => 'MIGRADO AL SISTEMA DE CONTROL Y SEGUIMIENTO DE TRAMITES',
                            );
                            if($derivacionSincobolModel->save($dataDerivacionSincobol) === false){
                                $errorDerivacionSincobol = 'SI';
                            }else{
                                $dataMigracion = array(
                                    'id' => $row['id'],
                                    'migrado' => 'true',
                                );
                                if($migrarModel->save($dataMigracion) === false)
                                    $errorMigracion = 'SI';
                            }
                        }
                    }
                }
                echo ($n+1).'. La H.R. '.$hoja_ruta['correlativo'].' A.M. '.$area_minera['codigo_unico'].' tuvo errores en A:'.$errorActoAdministrativo.' B:'.$errorDatosAreaMinera.' C:'.$errorDerivacion.' D:'.$errorDerivacionSincobol.' E:'.$errorMigracion.'<br>';
            }
        }
    }
    private function obtenerDatosMigracion($id_hoja_ruta){
        $dbSincobol = \Config\Database::connect('sincobol');
        $campos = array(
        'slc.id as fk_solicitud_licencia_contrato', 'am.id as fk_area_minera', 'hr.id as fk_hoja_ruta', 'hr.correlativo',
        "to_char(slc.fecha_ingreso, 'YYYY-MM-DD HH24:MI') as fecha_mecanizada",
        'am.nombre as denominacion', 'am.codigo_unico', "CONCAT(ROUND(am.extension), ' ', CASE WHEN am.unidad = 'CUADRICULA' THEN 'CUADRICULA(S)' ELSE am.unidad END) AS extension",
        'am.departamentos', 'am.provincias', 'am.municipios', 'am.descripcion_area_protegida as area_protegida', 'am.regional', 'acm.nombre as titular', 'tam.nombre as clasificacion_titular',
        'dcam.domicilio_legal', 'dcam.domicilio_procesal', 'dcam.telefonos as telefono_solicitante', "CONCAT(p.nombres, ' ',p.apellido_paterno, ' ',p.apellido_materno) as representante_legal", 'acm.nacionalidad'
        );
        $builder = $dbSincobol->table('sincobol.hoja_ruta as hr')
        ->select($campos)
        ->join('contratos_licencias.solicitud_licencia_contrato as slc', 'hr.fk_solicitud_licencia_contrato = slc.id', 'left')
        ->join('contratos_licencias.area_minera as am', ' slc.fk_area_minera = am.id', 'left')
        ->join('contratos_licencias.actor_minero as acm', 'am.fk_actor_minero_poseedor = acm.id', 'left')
        ->join('contratos_licencias.tipo_actor_minero as tam', 'acm.fk_tipo_actor_minero = tam.id', 'left')
        ->join('sincobol.datos_contacto_actor_minero as dcam', 'acm.id = dcam.fk_actor_minero', 'left')
        ->join('contratos_licencias.persona_actor_minero as pacm', 'acm.id = pacm.fk_actor_minero', 'left')
        ->join('sincobol.persona as p', 'pacm.fk_persona = p.id', 'left')
        ->where(array('hr.id' => $id_hoja_ruta));
        return $builder->get()->getRowArray();
    }
    private function obtenerIdEstadoPadre($orden){
        $estadoTramiteModel = new EstadoTramiteModel();
        $where = array(
            'deleted_at' => NULL,
            'fk_estado_padre' => NULL,
            'fk_tramite' => $this->idTramite,
            'orden' => $orden,
        );
        $estado = $estadoTramiteModel->where($where)->first();
        return $estado['id'];
    }
    private function obtenerIdEstadoHijo($fk_estado_tramite_padre, $orden){
        $estadoTramiteModel = new EstadoTramiteModel();
        $where = array(
            'deleted_at' => NULL,
            'fk_estado_padre' => $fk_estado_tramite_padre,
            'fk_tramite' => $this->idTramite,
            'orden' => $orden,
        );
        $estado = $estadoTramiteModel->where($where)->first();
        return $estado['id'];
    }
    private function obtenerDocumentosAtender($fk_acto_administrativo){
        $db = \Config\Database::connect();
        $campos = array('doc.id', 'doc.correlativo', "TO_CHAR(doc.fecha, 'DD/MM/YYYY') as fecha", 'td.nombre as tipo_documento', 'td.notificacion');
        $where = array(
            'doc.fk_usuario_creador' => session()->get('registroUser'),
            'doc.fk_derivacion' => NULL,
            'doc.estado' => 'SUELTO',
            'doc.fk_acto_administrativo' => $fk_acto_administrativo,
        );
        $builder = $db->table('public.documentos AS doc')
        ->select($campos)
        ->join('public.tipo_documento AS td', 'doc.fk_tipo_documento = td.id', 'left')
        ->where($where)
        ->orderBY('doc.id', 'ASC');
        return $builder->get()->getResultArray();
    }
    private function obtenerDocumentosEditar($fk_acto_administrativo, $id_derivacion){
        $db = \Config\Database::connect();
        $campos = array('doc.id', 'doc.correlativo', 'doc.fecha_notificacion', "TO_CHAR(doc.fecha, 'DD/MM/YYYY') as fecha", 'td.nombre as tipo_documento', 'td.notificacion');
        $where = array(
            'doc.fk_usuario_creador' => session()->get('registroUser'),
            'doc.fk_derivacion' => $id_derivacion,
            'doc.fk_acto_administrativo' => $fk_acto_administrativo,
        );
        $builder = $db->table('public.documentos AS doc')
        ->select($campos)
        ->join('public.tipo_documento AS td', 'doc.fk_tipo_documento = td.id', 'left')
        ->where($where)
        ->orderBY('doc.id', 'ASC');
        return $builder->get()->getResultArray();
    }
    public function obtenerDatosSelectHrInExSincobol($idHojaRuta){
        $dbSincobol = \Config\Database::connect('sincobol');
        $where = array(
            'anexado_siseg' => 'NO',
            'id' => $idHojaRuta
        );
        $builder = $dbSincobol->table('sincobol.vista_buscador_hoja_ruta_actualizado')
        ->where($where);
        return $builder->get()->getRowArray();
    }
    public function obtenerDatosSelectHrInExSincobolEditar($idHojaRuta){
        $dbSincobol = \Config\Database::connect('sincobol');
        $where = array(
            'id' => $idHojaRuta
        );
        $builder = $dbSincobol->table('sincobol.vista_buscador_hoja_ruta_actualizado')
        ->where($where);
        return $builder->get()->getRowArray();
    }
    public function anexarHrSincobolMejorado($id_derivacion, $fk_hoja_ruta, $fk_acto_administrativo, $usuario){
        if($this->archivarHrSincobolMejorado($fk_hoja_ruta, $fk_acto_administrativo, $usuario)){
            $hrAnexadasModel = new HrAnexadasModel();
            $dataDerivacion = array(
                'fk_derivacion' => $id_derivacion,
                'fk_hoja_ruta' => $fk_hoja_ruta,
            );
            if($hrAnexadasModel->save($dataDerivacion) === false)
                session()->setFlashdata('fail', $hrAnexadasModel->errors());
            else
                return true;
        }
        return false;
    }
    public function archivarHrSincobolMejorado($fk_hoja_ruta, $fk_acto_administrativo, $usuario){
        $motivo = 'ANEXADO AL SISTEMA DE SEGUIMIENTO Y CONTROL DE TRAMITES - CAM POR '.$usuario;
        $hojaRutaSisegModel = new HojaRutaSisegModel();
        $derivacionSincobolModel = new DerivacionSincobolModel();
        $where = array(
            'fk_hoja_ruta'=> $fk_hoja_ruta,
        );
        if(!$hojaRutaSisegModel->where($where)->first()){
            $dataHojaRutaSiseg = array(
                'fk_hoja_ruta' => $fk_hoja_ruta,
                'fk_tramite' => $this->idTramite,
                'fk_siseg' => $fk_acto_administrativo,
                'usuario' => $usuario,
                'fecha' => date('Y-m-d H:i:s'),
                'tabla_siseg' => 'public.acto_administrativo',
            );
            if($hojaRutaSisegModel->insert($dataHojaRutaSiseg) === false){
                session()->setFlashdata('fail', $hojaRutaSisegModel->errors());
            }else{
                if($ultima_derivacion = $derivacionSincobolModel->where($where)->orderBy('id','DESC')->first()){
                    $dataDerivacion = array(
                        'id' => $ultima_derivacion['id'],
                        'estado' => 'CONCLUIDO',
                        'fecha_conclusion' => date('Y-m-d H:i:s'),
                        'motivo_conclusion' => $motivo,
                    );
                    if($derivacionSincobolModel->save($dataDerivacion) === false)
                        session()->setFlashdata('fail', $derivacionSincobolModel->errors());
                    else
                        return true;
                }
            }
        }
        return false;
    }

    /* Reportes Administración */
    public function reporteResponsable()
    {
        $oficinaModel = new OficinasModel();
        $tmpOficinas = $oficinaModel->findAll();
        $oficinas = array('' => 'SELECCIONE UNA DIRECCIÓN');
        $usuarios = array('' => 'SELECCIONE UN USUARIO');
        foreach($tmpOficinas as $row)
            $oficinas[$row['id']] = $row['nombre'];
        $datos = array();
        $campos_listar=array(
            'Estado','Fecha','Días<br>Pasados','H.R. Madre', 'Remitente', 'Destinatario', 'Instrucción', 'Estado Tramite', 'Responsable Trámite', 'Codigo Unico','Denominacion','Representante Legal','Solicitante','Departamentos',
        );
        $campos_reales=array(
            'ultimo_estado','ultimo_fecha_derivacion','dias','correlativo', 'remitente', 'destinatario', 'ultimo_instruccion', 'estado_tramite', 'responsable', 'codigo_unico', 'denominacion','representante_legal','titular', 'departamentos',
        );

        if ($this->request->getPost()) {
            $oficina = $this->request->getPost('oficina');
            $usuario = $this->request->getPost('usuario');
            if(!empty($oficina))
                $usuarios = $usuarios + $this->obtenerUsuariosOficina($oficina);
            $camposValidacion = array(
                'oficina' => [
                    'rules' => 'required',
                ],
                'usuario' => [
                    'rules' => 'required',
                ],
            );
            if(!$this->validate($camposValidacion)){
                $contenido['validation'] = $this->validator;
            }else{

                $db = \Config\Database::connect();
                $campos = array('ac.id', 'ac.fk_area_minera', 'ac.correlativo', "to_char(ac.fecha_mecanizada, 'DD/MM/YYYY') as fecha_mecanizada", 'ac.ultimo_instruccion', 'ac.ultimo_estado',
                'dam.codigo_unico', 'dam.denominacion', 'dam.titular', 'dam.departamentos', "CONCAT(ur.nombre_completo,'<br><b>',pr.nombre,'<b>') as remitente", "CONCAT(ud.nombre_completo,'<br><b>',pd.nombre,'<b>') as destinatario",
                "CASE WHEN ac.ultimo_fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'. ',etp.nombre,'<br>',etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE CONCAT(etp.orden,'. ',etp.nombre) END as estado_tramite",
                "CASE WHEN ac.ultimo_fk_estado_tramite_hijo > 0 AND eth.finalizar THEN 'SI' WHEN etp.finalizar THEN 'SI'  ELSE 'NO' END as finalizar",
                "to_char(ac.ultimo_fecha_derivacion, 'DD/MM/YYYY') as ultimo_fecha_derivacion", "(CURRENT_DATE - ac.ultimo_fecha_derivacion::date) as dias", 'etp.dias_intermedio', 'etp.dias_limite', 'etp.notificar',
                'ac.ultimo_fk_documentos', "CONCAT(ua.nombre_completo,'<br><b>',pa.nombre,'<b>') as responsable", 'dam.representante_legal', 'ac.ultimo_recurso_jerarquico', 'ac.ultimo_recurso_revocatoria', 'ac.ultimo_oposicion', 'ac.editar'
                );
                $where = array(
                    'ac.deleted_at' => NULL,
                    'ac.ultimo_fk_usuario_responsable' => $usuario,
                );
                $builder = $db->table('public.acto_administrativo as ac')
                ->select($campos)
                ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
                ->join('usuarios as ur', 'ac.ultimo_fk_usuario_remitente = ur.id', 'left')
                ->join('perfiles as pr', 'ur.fk_perfil = pr.id', 'left')
                ->join('usuarios as ud', 'ac.ultimo_fk_usuario_destinatario = ud.id', 'left')
                ->join('perfiles as pd', 'ud.fk_perfil = pd.id', 'left')
                ->join('estado_tramite as etp', 'ac.ultimo_fk_estado_tramite_padre = etp.id', 'left')
                ->join('estado_tramite as eth', 'ac.ultimo_fk_estado_tramite_hijo = eth.id', 'left')
                ->join('usuarios as ua', 'ac.ultimo_fk_usuario_responsable = ua.id', 'left')
                ->join('perfiles as pa', 'ua.fk_perfil = pa.id', 'left')
                ->where($where)
                ->orderBY('ac.id', 'DESC');
                $datos = $builder->get()->getResultArray();
                if ($this->request->getPost('enviar')=='excel') {
                    helper('security');
                    $file_name = sanitize_filename(mb_strtolower($usuarios[$usuario])).' - responsable - '.date('YmdHis').'.xlsx';
                    $this->exportarXLS($campos_listar, $campos_reales, $datos, $file_name);
                }
            }
        }
        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Reporte de Trámites por Responsable';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['oficinas'] = $oficinas;
        $contenido['usuarios'] = $usuarios;
        $contenido['subtitulo'] = 'Reporte de Trámites por Responsable';
        $contenido['datos'] = $datos;
        $contenido['campos_listar'] = $campos_listar;
        $contenido['campos_reales'] = $campos_reales;
        $contenido['controlador'] = $this->controlador;
        $contenido['accion'] = $this->controlador.'reporte_responsable';
        $contenido['idtramite'] = $this->idTramite;
        $data['content'] = view($this->carpeta.'reporte_responsable', $contenido);
        $data['menu_actual'] = $this->menuActual.'reporte_responsable';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['alertas'] = $this->alertasTramites();
        echo view('templates/template', $data);
    }
    public function reporteMisTramites()
    {
        $oficinaModel = new OficinasModel();
        $tmpOficinas = $oficinaModel->findAll();
        $oficinas = array('' => 'SELECCIONE UNA DIRECCIÓN');
        $usuarios = array('' => 'SELECCIONE UN USUARIO');
        foreach($tmpOficinas as $row)
            $oficinas[$row['id']] = $row['nombre'];
        $datos = array();
        $campos_listar=array(
            'Estado','Fecha','Días<br>Pasados','H.R. Madre', 'Remitente', 'Destinatario', 'Instrucción', 'Estado Tramite', 'Responsable Trámite', 'Codigo Unico','Denominacion','Representante Legal','Solicitante','Departamentos',
        );
        $campos_reales=array(
            'ultimo_estado','ultimo_fecha_derivacion','dias','correlativo', 'remitente', 'destinatario', 'ultimo_instruccion', 'estado_tramite', 'responsable', 'codigo_unico', 'denominacion','representante_legal','titular', 'departamentos',
        );

        if ($this->request->getPost()) {
            $oficina = $this->request->getPost('oficina');
            $usuario = $this->request->getPost('usuario');
            if(!empty($oficina))
                $usuarios = $usuarios + $this->obtenerUsuariosOficina($oficina);
            $camposValidacion = array(
                'oficina' => [
                    'rules' => 'required',
                ],
                'usuario' => [
                    'rules' => 'required',
                ],
            );
            if(!$this->validate($camposValidacion)){
                $contenido['validation'] = $this->validator;
            }else{
                $db = \Config\Database::connect();
                $campos = array('ac.id', 'ac.fk_area_minera', 'ac.correlativo', "to_char(ac.fecha_mecanizada, 'DD/MM/YYYY') as fecha_mecanizada", 'ac.ultimo_instruccion', 'ac.ultimo_estado',
                'dam.codigo_unico', 'dam.denominacion', 'dam.titular', 'dam.departamentos', "CONCAT(ur.nombre_completo,'<br><b>',pr.nombre,'<b>') as remitente", "CONCAT(ud.nombre_completo,'<br><b>',pd.nombre,'<b>') as destinatario",
                "CASE WHEN ac.ultimo_fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'. ',etp.nombre,'<br>',etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE CONCAT(etp.orden,'. ',etp.nombre) END as estado_tramite",
                "CASE WHEN ac.ultimo_fk_estado_tramite_hijo > 0 AND eth.finalizar THEN 'SI' WHEN etp.finalizar THEN 'SI'  ELSE 'NO' END as finalizar",
                "to_char(ac.ultimo_fecha_derivacion, 'DD/MM/YYYY') as ultimo_fecha_derivacion", "(CURRENT_DATE - ac.ultimo_fecha_derivacion::date) as dias", 'etp.dias_intermedio', 'etp.dias_limite', 'etp.notificar',
                'ac.ultimo_fk_documentos', "CONCAT(ua.nombre_completo,'<br><b>',pa.nombre,'<b>') as responsable", 'dam.representante_legal', 'ac.ultimo_recurso_jerarquico', 'ac.ultimo_recurso_revocatoria', 'ac.ultimo_oposicion', 'ac.editar'
                );
                $where = array(
                    'ac.deleted_at' => NULL,
                    'ac.fk_usuario_actual' => $usuario,
                );
                $builder = $db->table('public.acto_administrativo as ac')
                ->select($campos)
                ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
                ->join('usuarios as ur', 'ac.ultimo_fk_usuario_remitente = ur.id', 'left')
                ->join('perfiles as pr', 'ur.fk_perfil = pr.id', 'left')
                ->join('usuarios as ud', 'ac.ultimo_fk_usuario_destinatario = ud.id', 'left')
                ->join('perfiles as pd', 'ud.fk_perfil = pd.id', 'left')
                ->join('estado_tramite as etp', 'ac.ultimo_fk_estado_tramite_padre = etp.id', 'left')
                ->join('estado_tramite as eth', 'ac.ultimo_fk_estado_tramite_hijo = eth.id', 'left')
                ->join('usuarios as ua', 'ac.ultimo_fk_usuario_responsable = ua.id', 'left')
                ->join('perfiles as pa', 'ua.fk_perfil = pa.id', 'left')
                ->where($where)
                ->orderBY('ac.id', 'DESC');
                $datos = $builder->get()->getResultArray();
                if ($this->request->getPost('enviar')=='excel') {
                    helper('security');
                    $file_name = sanitize_filename(mb_strtolower($usuarios[$usuario])).' - bandeja - '.date('YmdHis').'.xlsx';
                    $this->exportarXLS($campos_listar, $campos_reales, $datos, $file_name);
                }
            }
        }
        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Reporte de Trámites por Bandeja';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['oficinas'] = $oficinas;
        $contenido['usuarios'] = $usuarios;
        $contenido['subtitulo'] = 'Reporte de Trámites por Bandeja';
        $contenido['datos'] = $datos;
        $contenido['campos_listar'] = $campos_listar;
        $contenido['campos_reales'] = $campos_reales;
        $contenido['controlador'] = $this->controlador;
        $contenido['accion'] = $this->controlador.'reporte_mis_tramites';
        $contenido['idtramite'] = $this->idTramite;
        $data['content'] = view($this->carpeta.'reporte_mis_tramites', $contenido);
        $data['menu_actual'] = $this->menuActual.'reporte_mis_tramites';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['alertas'] = $this->alertasTramites();
        echo view('templates/template', $data);
    }
    public function reporteFechaMecanizada()
    {
        $oficina = $this->request->getPost('oficina');
        $estado = $this->request->getPost('estado');
        $subestado = $this->request->getPost('subestado');
        $oficinaModel = new OficinasModel();
        $tmpOficinas = $oficinaModel->where(array('desconcentrado' => 'true'))->findAll();
        $oficinas = array('' => 'TODAS LAS DIRECCIONES DEPARTAMENTALES Y REGIONAL');
        foreach($tmpOficinas as $row)
            $oficinas[$row['id']] = $row['nombre'];
        $estadosTramites = $this->obtenerEstadosTramites($this->idTramite);
        $estados = array('' => 'TODOS LOS ESTADOS');
        foreach($estadosTramites as $row){
            if(is_numeric($row['id']) && $row['id'] > 0)
                $estados[$row['id']] = $row['texto'];
        }
        $subestados = array('' => 'TODOS LOS SUBESTADOS');
        if($estadosTramitesHijo = $this->obtenerEstadosTramitesHijo($estado)){
            foreach($estadosTramitesHijo as $row){
                if(is_numeric($row['id']) && $row['id'] > 0)
                    $subestados[$row['id']] = $row['texto'];
            }
        }
        $datos = array();
        $campos_listar=array(
            'Fecha Mecanizada','H.R. Madre','Estado Tramite','Fecha Derivación','Responsable Trámite','Remitente','Destinatario','Instrucción','Codigo Unico','Denominacion','Representante Legal','Solicitante','Clasificación APM','Departamentos', 'Dirección Departamental/Regional'
        );
        $campos_reales=array(
            'fecha_mecanizada','correlativo','estado_tramite','ultimo_fecha_derivacion','responsable','remitente','destinatario','ultimo_instruccion','codigo_unico','denominacion','representante_legal','titular','clasificacion_titular','departamentos', 'regional'
        );

        if ($this->request->getPost()) {

            $camposValidacion = array(
                'fecha_inicio' => [
                    'rules' => 'required|valid_date[Y-m-d]',
                ],
                'fecha_fin' => [
                    'rules' => 'required|valid_date[Y-m-d]',
                ],
            );
            if(!$this->validate($camposValidacion)){
                $contenido['validation'] = $this->validator;
            }else{
                $db = \Config\Database::connect();
                $campos = array('ac.id',
                "to_char(ac.fecha_mecanizada, 'DD/MM/YYYY') as fecha_mecanizada", 'ac.correlativo', "to_char(ac.ultimo_fecha_derivacion, 'DD/MM/YYYY') as ultimo_fecha_derivacion",
                "CONCAT(ua.nombre_completo,'<br><b>',pa.nombre,'<b>') as responsable", "CONCAT(ur.nombre_completo,'<br><b>',pr.nombre,'<b>') as remitente", "CONCAT(ud.nombre_completo,'<br><b>',pd.nombre,'<b>') as destinatario",
                'ac.ultimo_instruccion', "CASE WHEN ac.ultimo_fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'. ',etp.nombre,'<br>',etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE CONCAT(etp.orden,'. ',etp.nombre) END as estado_tramite",
                'dam.codigo_unico', 'dam.denominacion', 'dam.representante_legal', 'dam.titular', 'dam.clasificacion_titular', 'dam.departamentos',
                "CONCAT(etp.orden,'. ',etp.nombre) as estado_tramite_excel",
                "CASE WHEN ac.ultimo_fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE '' END as sub_estado_tramite_excel",
                "CONCAT(ua.nombre_completo,' - ',pa.nombre) as responsable_excel", "CONCAT(ur.nombre_completo,' - ',pr.nombre) as remitente_excel", "CONCAT(ud.nombre_completo,' - ',pd.nombre) as destinatario_excel",
                "o.nombre as regional"
                );
                $where = array(
                    'ac.deleted_at' => NULL,
                    'ac.fecha_mecanizada >=' => $this->request->getPost('fecha_inicio'),
                    'ac.fecha_mecanizada <=' => $this->request->getPost('fecha_fin'),
                );
                if(is_numeric($oficina) && $oficina > 0)
                    $where['ac.fk_oficina'] = $oficina;
                if(is_numeric($estado) && $estado > 0)
                    $where['ac.ultimo_fk_estado_tramite_padre'] = $estado;
                if(is_numeric($subestado) && $subestado > 0)
                    $where['ac.ultimo_fk_estado_tramite_hijo'] = $subestado;

                $builder = $db->table('public.acto_administrativo as ac')
                ->select($campos)
                ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
                ->join('usuarios as ur', 'ac.ultimo_fk_usuario_remitente = ur.id', 'left')
                ->join('perfiles as pr', 'ur.fk_perfil = pr.id', 'left')
                ->join('usuarios as ud', 'ac.ultimo_fk_usuario_destinatario = ud.id', 'left')
                ->join('perfiles as pd', 'ud.fk_perfil = pd.id', 'left')
                ->join('estado_tramite as etp', 'ac.ultimo_fk_estado_tramite_padre = etp.id', 'left')
                ->join('estado_tramite as eth', 'ac.ultimo_fk_estado_tramite_hijo = eth.id', 'left')
                ->join('usuarios as ua', 'ac.ultimo_fk_usuario_responsable = ua.id', 'left')
                ->join('perfiles as pa', 'ua.fk_perfil = pa.id', 'left')
                ->join('public.oficinas as o', 'ac.fk_oficina = o.id', 'left')
                ->where($where)
                ->orderBY('o.nombre ASC, ac.fecha_mecanizada ASC');
                $datos = $builder->get()->getResultArray();
                if ($this->request->getPost('enviar')=='excel') {
                    helper('security');
                    $campos_listar_excel=array(
                        'Fecha Mecanizada','H.R. Madre','Estado Tramite','Sub Estado Tramite','Fecha Derivación','Responsable Trámite','Remitente','Destinatario','Instrucción','Codigo Unico','Denominacion','Representante Legal','Solicitante','Clasificación APM','Departamentos', 'Dirección Departamental/Regional'
                    );
                    $campos_reales_excel=array(
                        'fecha_mecanizada','correlativo','estado_tramite_excel','sub_estado_tramite_excel','ultimo_fecha_derivacion','responsable_excel','remitente_excel','destinatario_excel','ultimo_instruccion','codigo_unico','denominacion','representante_legal','titular','clasificacion_titular','departamentos', 'regional'
                    );
                    $file_name = 'CAM_'.$this->request->getPost('fecha_inicio').'_'.$this->request->getPost('fecha_fin').'.xlsx';
                    $this->exportarXLS($campos_listar_excel, $campos_reales_excel, $datos, $file_name, $oficinas[$oficina], 'DE : '.$this->request->getPost('fecha_inicio').' A :'.$this->request->getPost('fecha_fin'));
                }
            }
        }
        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Reporte de Trámites por Fecha Mecanizada';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['oficinas'] = $oficinas;
        $contenido['estados'] = $estados;
        $contenido['subestados'] = $subestados;
        $contenido['subtitulo'] = 'Reporte de Trámites por Fecha Mecanizada';
        $contenido['datos'] = $datos;
        $contenido['campos_listar'] = $campos_listar;
        $contenido['campos_reales'] = $campos_reales;
        $contenido['controlador'] = $this->controlador;
        $contenido['accion'] = $this->controlador.'reporte_fecha_mecanizada';
        $data['content'] = view($this->carpeta.'reporte_fecha_mecanizada', $contenido);
        $data['menu_actual'] = $this->menuActual.'reporte_fecha_mecanizada';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['alertas'] = $this->alertasTramites();
        echo view('templates/template', $data);
    }

    private function obtenerUsuariosOficina($oficina){
        $db = \Config\Database::connect();
        $campos = array('u.id', "CONCAT(u.nombre_completo, ' - ', p.nombre) as usuario");
        $where = array(
            'u.fk_oficina' => $oficina,
            'u.deleted_at' => NULL,
        );
        $builder = $db->table('public.usuarios as u')
        ->select($campos)
        ->join('public.perfiles AS p', 'u.fk_perfil = p.id', 'left')
        ->where($where)
        ->like('u.tramites', $this->idTramite)
        ->orderBy('usuario','ASC');
        $resultado = array();
        if($tmpUsuarios = $builder->get()->getResultArray()){
            foreach($tmpUsuarios as $row)
                $resultado[$row['id']] = $row['usuario'];
        }
        return $resultado;
    }

    public function hojaRutaPdf($id_acto_administrativo){
        $db = \Config\Database::connect();
        $campos = array(
            "ac.fk_hoja_ruta","o.descripcion_oficina","ac.correlativo","dam.denominacion","dam.codigo_unico","dam.clasificacion_titular","dam.titular","dam.representante_legal",
            "to_char(ac.fecha_mecanizada, 'DD-MM-YYYY') as fecha_mecanizada", "to_char(ac.fecha_mecanizada, 'HH24:MI') as hora_mecanizada"
        );
        $where = array(
            'ac.deleted_at' => NULL,
            'ac.id' => $id_acto_administrativo,
        );
        $builder = $db->table('public.acto_administrativo as ac')
        ->select($campos)
        ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
        ->join('public.oficinas as o', 'ac.fk_oficina = o.id', 'left')
        ->where($where);
        if($fila = $builder->get()->getRowArray()){
            $dbSincobol = \Config\Database::connect('sincobol');
            $builder = $dbSincobol->table('sincobol.hoja_ruta as hr')
            ->select("hr.cantidad_fojas, CONCAT(p.nombres,' ',p.apellido_paterno,' ',p.apellido_materno,' (',c.nombre,')') as usuario_creador")
            ->join('sincobol.asignacion_cargo as ac', 'hr.fk_asignacion_cargo_creador = ac.id', 'left')
            ->join('sincobol.persona as p', 'ac.fk_persona = p.id', 'left')
            ->join('sincobol.cargo as c', 'ac.fk_cargo = c.id', 'left')
            ->where(array('hr.id' => $fila['fk_hoja_ruta']));
            $otros = $builder->get()->getRowArray();

            $file_name = str_replace('/','-',$fila['correlativo']).'.pdf';
            $pdf = new HojaRutaPdf('P', 'mm', array(216, 279), true, 'UTF-8', false);

            $pdf->SetCreator('GARNET');
            $pdf->SetAuthor('Desarrollo de UTIC');
            $pdf->SetTitle('Hoja de Ruta SOL CAM');
            $pdf->SetKeywords('Hoja, Ruta, CAM');

            //establecer margenes
            $pdf->SetMargins(10, 8, 10);
            $pdf->SetAutoPageBreak(true, 8); //Margin botton
            $pdf->setFontSubsetting(false);

            $pdf->AddPage();
            $pdf->SetTextColor(0, 0, 0);
            $pdf->setCellPaddings(0, 1, 0, 0);
            $pdf->setCellMargins(0, 0, 0, 0);
            $pdf->SetFillColor(128, 217, 255);

            $pdf->setCellPadding(1);
            $pdf->Image('assets/images/hoja_ruta/logo_ajam.png', 13, 9, 48, 0);
            $pdf->MultiCell(55, 22, "", 1, 'C', 0, 0);
            $pdf->SetFont($this->fontPDF, '', 8);
            $pdf->MultiCell(140,6, $fila['descripcion_oficina'], 'TRL', 'C', true, 1);
            $pdf->setx(65);
            $pdf->MultiCell(140,6, 'SOLICITUD CONTRATO ADMINISTRATIVO MINERO', 'RL', 'C', true, 1);
            $pdf->setx(65);
            $pdf->SetFont($this->fontPDF, 'B', 14);
            $pdf->MultiCell(140,10, $fila['correlativo'], 'LBR', 'C', true, 1, '', '', true, 0, false, true, 0, 'M');

            // DIRECCION
            $this->crearFila($pdf, 'DIRECCIÓN:', $fila['descripcion_oficina']);
            $this->crearFila($pdf, 'TRÁMITE:', 'CONTRATO ADMINISTRATIVO MINERO');
            $this->crearFila($pdf, 'ÁREA SOLICITADA:', $fila['denominacion']);
            $this->crearFila($pdf, 'CÓDIGO ÚNICO:', $fila['codigo_unico']);
            $this->crearFila($pdf, 'ACTOR PRODUCTIVO MINERO:', $fila['clasificacion_titular']);
            $this->crearFila($pdf, 'DENOMINACIÓN O RAZON SOCIAL:', $fila['titular']);
            $this->crearFila($pdf, 'REPRESENTANTE LEGAL:', $fila['representante_legal']);
            $this->crearFila($pdf, 'N° DE HOJAS:', $otros['cantidad_fojas']);
            $pdf->SetFont($this->fontPDF, 'B', 8);
            $pdf->MultiCell(55, 5, 'FECHA MECANIZADA:', 1, 'R', true, 0);
            $pdf->SetFont($this->fontPDF, '', 8);
            $pdf->MultiCell(50, 5, $fila['fecha_mecanizada'], 1, 'L', false, 0);
            $pdf->SetFont($this->fontPDF, 'B', 8);
            $pdf->MultiCell(45, 5, 'HORA MECANIZADA:', 1, 'R', true, 0);
            $pdf->SetFont($this->fontPDF, '', 8);
            $pdf->MultiCell(45, 5, $fila['hora_mecanizada'], 1, 'L', false, 1);
            $this->crearFila($pdf, 'USUARIO CREADOR:', $otros['usuario_creador']);

            $this->crearDerivacion($pdf, $this->fontPDF, $this->acciones);
            $this->crearDerivacion($pdf, $this->fontPDF, $this->acciones);
            $this->crearDerivacion($pdf, $this->fontPDF, $this->acciones);

            $pdf->AddPage();

            $pdf->SetFont($this->fontPDF, 'B', 8);
            $pdf->MultiCell(55, 8, 'HOJA DE RUTA:', 1, 'R', true, 0, '', '', true, 0, false, false, 5, 'M', true);
            $pdf->MultiCell(140, 8, '', 1, 'L', false, 1, '', '', true, 0, false, false, 5, 'T', true);

            $this->crearDerivacion($pdf, $this->fontPDF, $this->acciones);
            $this->crearDerivacion($pdf, $this->fontPDF, $this->acciones);
            $this->crearDerivacion($pdf, $this->fontPDF, $this->acciones);
            $this->crearDerivacion($pdf, $this->fontPDF, $this->acciones);

            $pdf->setPage(1, true);

            $pdf->Output($file_name, 'I');
            exit();
        }else{
            session()->setFlashdata('fail', 'No se ha encontrado la hoja de ruta');
            return redirect()->to($this->controlador.'mis_tramites');
        }
    }

    private function crearFila(&$pdf, $label, $texto){
        $pdf->SetFont($this->fontPDF, 'B', 8);
        $pdf->MultiCell(55, 5, $label, 1, 'R', true, 0);
        $pdf->SetFont($this->fontPDF, '', 8);
        $pdf->MultiCell(140, 5, $texto, 1, 'L', false, 1);
    }

    private function crearDerivacion(&$pdf, $tipo_letra, $acciones) {
        $pdf->setCellPadding(1);
        $pdf->ln(1);
        // FILA 1
        $pdf->SetFont($tipo_letra, 'B', 6);
        $pdf->MultiCell(55, 0, 'ACCIÓN', 1, 'C', true, 0);
        $pdf->MultiCell(40, 0, 'DESTINATARIO:', 1, 'R', true, 0);
        $pdf->MultiCell(100, 0, '', 1, 'C', false, 1);

        $count = 0;
        foreach($acciones as $accion) {
            //(w, h, txt, border = 0, align = 'J', fill = 0, ln = 1, x = '', y = '', reseth = true, stretch = 0, ishtml = false, autopadding = true, maxh = 0)
            $pdf->MultiCell(50, 0, $accion, 1, 'L', true, 0);
            $pdf->MultiCell(5, 0, ' ', 1, 'L', false, 0);
            $pdf->MultiCell(69, 0, ' ', 'R', 'L', false, 0);
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
        $pdf->MultiCell(44, 0, '', 1, 'L', false, 0);
        $pdf->MultiCell(46, 0, 'CON COPIA A:', 1, 'R', true, 0);
        $pdf->MultiCell(50, 0, '', 1, 'C', false, 1);
    }

    public function derivarTramites()
    {
        $actoAdministrativoModel = new ActoAdministrativoModel();
        $derivacionModel = new DerivacionModel();
        //$id_usuario_original = 100;
        $id_usuario_destino = 180;
        $estado = 'DERIVADO';
        $instruccion = 'REASIGNADO A SOLICITUD DE LA DIR. DEPARTAMENTAL LA PAZ (MARIA LAYME)';
        $tramites = array();

        if(count($tramites) > 0){
            foreach($tramites as $id_acto_administrativo){
                if($cam = $actoAdministrativoModel->find($id_acto_administrativo)){
                    $ultima_derivacion = $derivacionModel->where(array('fk_acto_administrativo' => $cam['id']))->orderBy('id','DESC')->first();
                    $dataDerivacion = array(
                        'fk_acto_administrativo' => $id_acto_administrativo,
                        'domicilio_legal' => $ultima_derivacion['domicilio_legal'],
                        'domicilio_procesal' => $ultima_derivacion['domicilio_procesal'],
                        'telefono_solicitante' => $ultima_derivacion['telefono_solicitante'],
                        'fk_estado_tramite_padre' => $ultima_derivacion['fk_estado_tramite_padre'],
                        'fk_estado_tramite_hijo' => $ultima_derivacion['fk_estado_tramite_hijo'],
                        'observaciones' => $ultima_derivacion['observaciones'],
                        'instruccion' => $instruccion,
                        'recurso_jerarquico' => $ultima_derivacion['recurso_jerarquico'],
                        'recurso_revocatoria' => $ultima_derivacion['recurso_revocatoria'],
                        'oposicion' => $ultima_derivacion['oposicion'],
                        'fk_usuario_remitente' => $ultima_derivacion['fk_usuario_destinatario'],
                        'fk_usuario_destinatario' => $id_usuario_destino,
                        'estado' => $estado,
                        'fk_usuario_creador' => $ultima_derivacion['fk_usuario_destinatario'],
                        'fk_usuario_responsable' => $id_usuario_destino,
                    );
                    if($derivacionModel->insert($dataDerivacion) === false){
                        echo 'Error en '.$cam['correlativo'].' <br>';
                    }else{
                        $dataActoAdministrativo = array(
                            'id' => $cam['id'],
                            'ultimo_estado' => $estado,
                            'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                            'ultimo_instruccion' => $instruccion,
                            'ultimo_fk_usuario_remitente' => $ultima_derivacion['fk_usuario_destinatario'],
                            'ultimo_fk_usuario_destinatario' => $id_usuario_destino,
                            'ultimo_fk_usuario_responsable' => $id_usuario_destino,
                            'ultimo_recurso_jerarquico' => $cam['ultimo_recurso_jerarquico'],
                            'ultimo_recurso_revocatoria' => $cam['ultimo_recurso_revocatoria'],
                            'ultimo_oposicion' => $cam['ultimo_oposicion'],
                        );

                        if($actoAdministrativoModel->save($dataActoAdministrativo) === false){
                            echo 'Error en '.$cam['correlativo'].' <br>';
                        }else{
                            $dataDerivacionActualizacion = array(
                                'id' => $ultima_derivacion['id'],
                                'estado' => 'ATENDIDO',
                                'fecha_atencion' => date('Y-m-d H:i:s'),
                            );
                            if($derivacionModel->save($dataDerivacionActualizacion) === false)
                                echo 'Error en '.$cam['correlativo'].' <br>';
                            else
                                echo 'Reasignado correctamente '.$cam['correlativo'].' <br>';
                        }

                    }
                }
            }
        }

    }

    /*public function actualizarPoligonoAreaMinera(){
        $datosAreaMineraModel = new DatosAreaMineraModel();
        $db = \Config\Database::connect();
        $campos = array(
            'dam.fk_acto_administrativo', 'ac.fk_area_minera', 'dam.codigo_unico',
        );
        $where = array(
            'dam.the_geom' => NULL,
        );
        $builder = $db->table('public.datos_area_minera as dam')
        ->select($campos)
        ->join('public.acto_administrativo as ac', 'dam.fk_acto_administrativo = ac.id', 'left')
        ->where($where)
        ->orderBy('ac.fk_area_minera', 'ASC')
        ->limit(1000);
        if($datos = $builder->get()->getResultArray()){
            foreach($datos as $row){
                if($poligono = $this->obtenerPoligonoAreaMineraUno($row['fk_area_minera'])){
                    $dataAreaMinera = array(
                        'fk_acto_administrativo' => $row['fk_acto_administrativo'],
                        'the_geom' => $poligono,
                    );
                    if($datosAreaMineraModel->save($dataAreaMinera) === false)
                        echo "No se actualizo poligono de id_area_minera: ".$row['fk_area_minera']." codigo_unico: ".$row['codigo_unico']."<br>";
                    else
                        echo "Se actualizo poligono de id_area_minera: ".$row['fk_area_minera']." codigo_unico: ".$row['codigo_unico']."<br>";

                }else{
                    echo "No existe poligono de id_area_minera: ".$row['fk_area_minera']." codigo_unico: ".$row['codigo_unico']."<br>";
                }

            }
        }
    }
    public function obtenerPoligonoAreaMineraUno($id){
        $db = \Config\Database::connect('sincobol');
        $campos = array('the_geom');
        $builder = $db->table('contratos_licencias.poligono_area_minera')
        ->select($campos)
        ->where("the_geom is not null AND fk_area_minera = ".$id)
        ->orderBy('id', 'DESC');
        if($poligono = $builder->get()->getRowArray())
            return $poligono['the_geom'];
        return '';
    }*/

}