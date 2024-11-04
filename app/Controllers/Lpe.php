<?php
namespace App\Controllers;

use App\Models\CorrespondenciaExternaModel;
use App\Models\DatosAreaMineraLPEModel;
use App\Models\DerivacionLPEModel;
use App\Models\DerivacionSincobolModel;
use App\Models\DocumentosModel;
use App\Models\EstadoTramiteModel;
use App\Models\HojaRutaLPEModel;
use App\Models\OficinasModel;
use App\Models\SolicitudLicenciaContratoModel;

class Lpe extends BaseController
{
    protected $titulo = 'Licencias de Prospección y Exploración';
    protected $controlador = 'lpe/';
    protected $carpeta = 'lpe/';
    protected $idTramite = 3;
    protected $menuActual = 'lpe/';
    protected $rutaArchivos = 'archivos/lpe/';
    protected $urlSincobol = 'https://sincobol.autoridadminera.gob.bo/sincobol/';
    protected $alpha = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
    protected $fontPDF = 'helvetica';
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

    public function listadoRecepcion()
    {
        $db = \Config\Database::connect();
        $campos_listar=array(
            'Fecha', 'Días<br>Pasados', 'H.R. Madre', 'Remitente', 'Instrucción', 'Ultimo(s) Documento(s) Anexado(s)', 'Estado Tramite', 'Responsable Trámite', 'APM Presento', 'Codigo Unico','Denominacion','Representante Legal','Solicitante','Departamentos',
        );
        $campos_reales=array(
            'ultimo_fecha_derivacion','dias', 'correlativo', 'remitente', 'ultimo_instruccion', 'ultimos_documentos', 'estado_tramite', 'responsable', 'apm_presento', 'codigo_unico', 'denominacion','representante_legal','titular', 'departamentos',
        );
        $campos = array(
            'hr.id','hr.fk_area_minera','hr.ultimo_fk_documentos','hr.ultimo_estado',"to_char(hr.ultimo_fecha_derivacion, 'DD/MM/YYYY') as ultimo_fecha_derivacion","(CURRENT_DATE - hr.ultimo_fecha_derivacion::date) as dias",'hr.correlativo',
            "CONCAT(ur.nombre_completo,'<br><b>',pr.nombre,'<b>') as remitente",'hr.ultimo_instruccion',
            "CASE WHEN hr.ultimo_fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'. ',etp.nombre,'<br>',etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE CONCAT(etp.orden,'. ',etp.nombre) END as estado_tramite",
            "CONCAT(ua.nombre_completo,'<br><b>',pa.nombre,'<b>') as responsable",
            'hr.ultimo_recurso_jerarquico', 'hr.ultimo_recurso_revocatoria', 'hr.ultimo_oposicion',
            'dam.codigo_unico','dam.denominacion','dam.representante_legal','dam.titular','dam.departamentos'
        );
        $where = array(
            'hr.deleted_at' => NULL,
            'hr.ultimo_fk_usuario_destinatario' => session()->get('registroUser'),
        );
        $builder = $db->table('licencia_prospeccion_exploracion.hoja_ruta as hr')
        ->select($campos)
        ->join('licencia_prospeccion_exploracion.datos_area_minera as dam', 'hr.id = dam.fk_hoja_ruta', 'left')
        ->join('usuarios as ur', 'hr.ultimo_fk_usuario_remitente = ur.id', 'left')
        ->join('perfiles as pr', 'ur.fk_perfil = pr.id', 'left')
        ->join('estado_tramite as etp', 'hr.ultimo_fk_estado_tramite_padre = etp.id', 'left')
        ->join('estado_tramite as eth', 'hr.ultimo_fk_estado_tramite_hijo = eth.id', 'left')
        ->join('usuarios as ua', 'hr.ultimo_fk_usuario_responsable = ua.id', 'left')
        ->join('perfiles as pa', 'ua.fk_perfil = pa.id', 'left')
        ->where($where)
        ->whereIn('hr.ultimo_estado', array('DERIVADO','MIGRADO'))
        ->orderBY('hr.id', 'DESC');
        $datos = $this->obtenerUltimosDocumentos($builder->get()->getResult('array'));
        $datos = $this->obtenerCorrespondenciaExterna($datos);
        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Listado de Hojas de Ruta Derivados';
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
            'Estado','Fecha','Días<br>Pasados','H.R. Madre', 'Remitente', 'Destinatario', 'Instrucción', 'Ultimo(s) Documento(s) Anexado(s)', 'Estado Tramite', 'Responsable Trámite', 'APM Presento',
            'Codigo Unico','Denominacion','Representante Legal','Solicitante','Departamentos','Provincias','Municipios',
        );
        $campos_reales=array(
            'ultimo_estado','ultimo_fecha_derivacion','dias','correlativo', 'remitente', 'destinatario', 'ultimo_instruccion', 'ultimos_documentos', 'estado_tramite', 'responsable', 'apm_presento',
            'codigo_unico', 'denominacion','representante_legal','titular', 'departamentos','provincias','municipios',
        );
        $campos = array(
            'hr.id','hr.fk_area_minera','hr.ultimo_fk_documentos','hr.ultimo_estado',"to_char(hr.ultimo_fecha_derivacion, 'DD/MM/YYYY') as ultimo_fecha_derivacion","(CURRENT_DATE - hr.ultimo_fecha_derivacion::date) as dias",'hr.correlativo',
            "CONCAT(ur.nombre_completo,'<br><b>',pr.nombre,'<b>') as remitente","CONCAT(ud.nombre_completo,'<br><b>',pd.nombre,'<b>') as destinatario",'hr.ultimo_instruccion',
            "CASE WHEN hr.ultimo_fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'. ',etp.nombre,'<br>',etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE CONCAT(etp.orden,'. ',etp.nombre) END as estado_tramite",
            "CONCAT(ua.nombre_completo,'<br><b>',pa.nombre,'<b>') as responsable",
            'hr.ultimo_recurso_jerarquico', 'hr.ultimo_recurso_revocatoria', 'hr.ultimo_oposicion',
            'dam.codigo_unico','dam.denominacion','dam.representante_legal','dam.titular','dam.departamentos','dam.provincias', 'dam.municipios',
            "CASE WHEN hr.ultimo_fk_estado_tramite_hijo > 0 AND eth.finalizar THEN 'SI' WHEN etp.finalizar THEN 'SI'  ELSE 'NO' END as finalizar",'hr.editar'
        );
        $where = array(
            'hr.deleted_at' => NULL,
            'hr.fk_usuario_actual' => session()->get('registroUser'),
        );
        $builder = $db->table('licencia_prospeccion_exploracion.hoja_ruta as hr')
        ->select($campos)
        ->join('licencia_prospeccion_exploracion.datos_area_minera as dam', 'hr.id = dam.fk_hoja_ruta', 'left')
        ->join('usuarios as ur', 'hr.ultimo_fk_usuario_remitente = ur.id', 'left')
        ->join('perfiles as pr', 'ur.fk_perfil = pr.id', 'left')
        ->join('usuarios as ud', 'hr.ultimo_fk_usuario_destinatario = ud.id', 'left')
        ->join('perfiles as pd', 'ud.fk_perfil = pd.id', 'left')
        ->join('estado_tramite as etp', 'hr.ultimo_fk_estado_tramite_padre = etp.id', 'left')
        ->join('estado_tramite as eth', 'hr.ultimo_fk_estado_tramite_hijo = eth.id', 'left')
        ->join('usuarios as ua', 'hr.ultimo_fk_usuario_responsable = ua.id', 'left')
        ->join('perfiles as pa', 'ua.fk_perfil = pa.id', 'left')
        ->where($where)
        ->whereIn('hr.ultimo_estado',array('MIGRADO', 'DERIVADO', 'RECIBIDO', 'EN ESPERA', 'DEVUELTO'))
        ->orderBY('hr.id', 'DESC');
        $datos = $this->obtenerUltimosDocumentos($builder->get()->getResult('array'));
        $datos = $this->obtenerCorrespondenciaExterna($datos);
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
                $hojaRutaModel = new HojaRutaLPEModel();
                $derivacionModel = new DerivacionLPEModel();
                $datosAreaMineraModel = new DatosAreaMineraLPEModel();
                $idSolicitudContrato = $this->request->getPost('fk_solicitud_licencia_contrato');
                $derivacionSincobolModel = new DerivacionSincobolModel();

                if($solicitudLicencia = $solicitudLicenciaContratoModel->find($idSolicitudContrato)){
                    $dataHR = array(
                        'fk_solicitud_licencia_contrato' => $solicitudLicencia['id'],
                        'fk_area_minera' => $solicitudLicencia['fk_area_minera'],
                        'fk_hoja_ruta_sincobol' => $solicitudLicencia['fk_hoja_ruta'],
                        'fk_oficina' => session()->get('registroOficina'),
                        'correlativo' => $solicitudLicencia['correlativo'],
                        'fecha_mecanizada' => $this->request->getPost('fecha_mecanizada'),
                        'ultimo_estado' => $estado,
                        'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                        'ultimo_fk_estado_tramite_padre' => $this->request->getPost('fk_estado_tramite'),
                        'ultimo_fk_estado_tramite_hijo' => ((!empty($this->request->getPost('fk_estado_tramite_hijo'))) ? $this->request->getPost('fk_estado_tramite_hijo') : NULL),
                        'ultimo_fecha_actualizacion_estado' => date('Y-m-d H:i:s'),
                        'ultimo_instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                        'ultimo_fk_usuario_remitente' => session()->get('registroUser'),
                        'ultimo_fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                        'ultimo_fk_usuario_responsable' => $this->request->getPost('fk_usuario_destinatario'),
                        'ultimo_recurso_jerarquico' => ($this->request->getPost('recurso_jerarquico') ? 'true' : 'false'),
                        'ultimo_recurso_revocatoria' => ($this->request->getPost('recurso_revocatoria') ? 'true' : 'false'),
                        'ultimo_oposicion' => ($this->request->getPost('oposicion') ? 'true' : 'false'),
                        'fk_usuario_actual' => session()->get('registroUser'),
                        'fk_usuario_creador' => session()->get('registroUser'),
                        'estado_tramite_apm' => $this->obtenerEstadoTramiteAPM($this->request->getPost('fk_estado_tramite'), $this->request->getPost('fk_estado_tramite_hijo')),
                    );
                    if($hojaRutaModel->insert($dataHR) === false){
                        session()->setFlashdata('fail', $hojaRutaModel->errors());
                    }else{
                        $idHR = $hojaRutaModel->getInsertID();
                        $dataDatosAreaMinera = array(
                            'fk_hoja_ruta' => $idHR,
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
                            'the_geom' => $this->obtenerPoligonoAreaMinera($solicitudLicencia['fk_area_minera']),
                        );
                        if($datosAreaMineraModel->insert($dataDatosAreaMinera) === false)
                            session()->setFlashdata('fail', $datosAreaMineraModel->errors());

                        $dataDerivacion = array(
                            'fk_hoja_ruta' => $idHR,
                            'fk_estado_tramite_padre' => $this->request->getPost('fk_estado_tramite'),
                            'fk_estado_tramite_hijo' => ((!empty($this->request->getPost('fk_estado_tramite_hijo'))) ? $this->request->getPost('fk_estado_tramite_hijo') : NULL),
                            'estado' => $estado,
                            'domicilio_legal' => mb_strtoupper($this->request->getPost('domicilio_legal')),
                            'domicilio_procesal' => mb_strtoupper($this->request->getPost('domicilio_procesal')),
                            'telefono_solicitante' => mb_strtoupper($this->request->getPost('telefono_solicitante')),
                            'observaciones' => mb_strtoupper($this->request->getPost('observaciones')),
                            'instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                            'fecha_actualizacion_estado' => date('Y-m-d H:i:s'),
                            'recurso_jerarquico' => ($this->request->getPost('recurso_jerarquico') ? 'true' : 'false'),
                            'recurso_revocatoria' => ($this->request->getPost('recurso_revocatoria') ? 'true' : 'false'),
                            'oposicion' => ($this->request->getPost('oposicion') ? 'true' : 'false'),
                            'fk_usuario_remitente' => session()->get('registroUser'),
                            'fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                            'fk_usuario_responsable' => $this->request->getPost('fk_usuario_destinatario'),
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
                            $solicitudLicencia['fk_acto_administrativo'] = $idHR;
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
        $cabera['subtitulo'] = 'Migrar Licencia de Prospección y Exploración - SINCOBOL';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['estadosTramites'] = $estadosTramites;
        $contenido['accion'] = $this->controlador.'agregar';
        $contenido['controlador'] = $this->controlador;
        $data['content'] = view($this->carpeta.'agregar', $contenido);
        $data['menu_actual'] = $this->menuActual.'agregar';
        $data['validacion_js'] = $this->carpeta.'lpe-agregar-validation.js';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['alertas'] = $this->alertasTramites();
        echo view('templates/template', $data);
    }
    public function recibir($id_hoja_ruta){
        $hojaRutaModel = new HojaRutaLPEModel();
        $derivacionModel = new DerivacionLPEModel();
        $where = array(
            'id' => $id_hoja_ruta,
            'deleted_at' => NULL,
            'ultimo_fk_usuario_destinatario' => session()->get('registroUser'),
        );
        if($fila = $hojaRutaModel->where($where)->whereIn('ultimo_estado', array('DERIVADO','MIGRADO'))->first()){
            $estado = 'RECIBIDO';
            $data = array(
                'id' => $fila['id'],
                'ultimo_estado' => $estado,
                'fk_usuario_actual' => $fila['ultimo_fk_usuario_destinatario'],
                'editar' => true,
            );
            if($hojaRutaModel->save($data) === false)
                session()->setFlashdata('fail', $hojaRutaModel->errors());
            $where = array(
                'fk_hoja_ruta' => $fila['id'],
            );
            $derivacion = $derivacionModel->where($where)->orderBY('id', 'DESC')->first();
            $dataDerivacion = array(
                'id' => $derivacion['id'],
                'estado' => $estado,
                'fecha_recepcion' => date('Y-m-d H:i:s'),
            );
            if($derivacionModel->save($dataDerivacion) === false)
                session()->setFlashdata('fail', $derivacionModel->errors());
        }
        return redirect()->to($this->controlador.'listado_recepcion');
    }
    public function recibirMultiple(){
        if ($this->request->getPost()) {
            if($ids_hojas_rutas = $this->request->getPost('recibir')){
                foreach($ids_hojas_rutas as $id_hoja_ruta)
                    $this->recibirTramite($id_hoja_ruta);
                session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información.');
                return redirect()->to($this->controlador.'listado_recepcion');
            }
        }
        session()->setFlashdata('fail', 'No se pudo recepcionar los trámites.');
        return redirect()->to($this->controlador.'mis_tramites');
    }
    public function recibirTramite($id_hoja_ruta){
        $hojaRutaModel = new HojaRutaLPEModel();
        $derivacionModel = new DerivacionLPEModel();
        $where = array(
            'id' => $id_hoja_ruta,
            'deleted_at' => NULL,
            'ultimo_fk_usuario_destinatario' => session()->get('registroUser'),
        );
        if($fila = $hojaRutaModel->where($where)->whereIn('ultimo_estado', array('DERIVADO','MIGRADO'))->first()){
            $estado = 'RECIBIDO';
            $dataHR = array(
                'id' => $fila['id'],
                'ultimo_estado' => $estado,
                'fk_usuario_actual' => $fila['ultimo_fk_usuario_destinatario'],
                'editar' => true,
            );
            if($hojaRutaModel->save($dataHR) === false)
                session()->setFlashdata('fail', $hojaRutaModel->errors());
            $where = array(
                'fk_hoja_ruta' => $fila['id'],
            );
            $derivacion = $derivacionModel->where($where)->orderBY('id', 'DESC')->first();
            $dataDerivacion = array(
                'id' => $derivacion['id'],
                'estado' => $estado,
                'fecha_recepcion' => date('Y-m-d H:i:s'),
            );
            if($derivacionModel->save($dataDerivacion) === false)
                session()->setFlashdata('fail', $derivacionModel->errors());
        }
        return true;
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
                    'fk_hoja_ruta' => $row['id'],
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
                'slc.fk_tipo_solicitud' => 2,
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
                    'text' => 'No se encuentra la hoja de ruta'
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
        $hojaRutaModel = new HojaRutaLPEModel();
        $resultado = array(
            'error' => 'No se guardo la información',
        );
        $where = array(
            'id' => $this->request->getPost('id'),
            'ultimo_fk_usuario_destinatario' => session()->get('registroUser'),
            'deleted_at' => NULL,
        );
        if($fila = $hojaRutaModel->where($where)->first()){
            $derivacionModel = new DerivacionLPEModel();
            $estado = 'DEVUELTO';
            $motivo_devolucion = mb_strtoupper($this->request->getPost('motivo_devolucion'));
            switch($fila['ultimo_estado']){
                case 'MIGRADO':
                    $where = array(
                        'fk_hoja_ruta' => $fila['id'],
                    );
                    $derivacion = $derivacionModel->where($where)->orderBy('id', 'DESC')->first();
                    $dataHR = array(
                        'id' => $fila['id'],
                        'fk_usuario_actual' => $derivacion['fk_usuario_remitente'],
                        'ultimo_estado' => $estado,
                        'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                        'ultimo_instruccion' => $motivo_devolucion,
                        'ultimo_fk_usuario_remitente' => session()->get('registroUser'),
                        'ultimo_fk_usuario_destinatario' => $derivacion['fk_usuario_remitente'],
                        'ultimo_fk_usuario_responsable'=>$derivacion['fk_usuario_remitente'],
                    );
                    if($hojaRutaModel->save($dataHR) === false){
                        session()->setFlashdata('fail', $hojaRutaModel->errors());
                    }else{
                        $dataDerivacion = array(
                            'fk_hoja_ruta' => $fila['id'],
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
                        'fk_hoja_ruta' => $fila['id'],
                    );
                    $derivaciones = $derivacionModel->where($where)->orderBy('id', 'DESC')->findAll(2);
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
                        'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                        'ultimo_instruccion' => $motivo_devolucion,
                        'ultimo_fk_usuario_remitente' => session()->get('registroUser'),
                        'ultimo_fk_usuario_destinatario' => $derivacion_restaurar['fk_usuario_destinatario'],
                        'ultimo_recurso_jerarquico' => (($derivacion_restaurar['recurso_jerarquico']=='t') ? 'true' : 'false'),
                        'ultimo_recurso_revocatoria' => (($derivacion_restaurar['recurso_revocatoria']=='t') ? 'true' : 'false'),
                        'ultimo_oposicion' => (($derivacion_restaurar['oposicion']=='t') ? 'true' : 'false'),
                        'ultimo_fk_documentos' => '',
                    );
                    if($hojaRutaModel->save($dataHR) === false){
                        session()->setFlashdata('fail', $hojaRutaModel->errors());
                    }else{
                        $dataDerivacion = array(
                            'fk_hoja_ruta' => $fila['id'],
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

}