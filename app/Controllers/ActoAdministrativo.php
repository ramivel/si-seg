<?php

namespace App\Controllers;
use App\Models\EstadoTramiteModel;
use App\Models\TipoSolicitudModel;
use App\Models\ActoAdministrativoModel;
use App\Models\DerivacionModel;
use App\Models\DocumentosModel;
use App\Models\SolicitudLicenciaContratoModel;

class ActoAdministrativo extends BaseController
{
    protected $titulo = 'Solicitudes Contratos Administrativos Mineros';
    protected $controlador = 'acto_administrativo/';
    protected $carpeta = 'acto_administrativo/';

    public function index()
    {
        $db = \Config\Database::connect();
        $campos = array('ac.id', 'ac.fk_area_minera', 'ac.fk_hoja_ruta', 'ac.correlativo', 'ua.nombre_completo as responsable',
        'ac.ultimo_estado_tramite', "to_char(ac.ultimo_fecha_emision, 'DD/MM/YYYY') as ultimo_fecha_emision",
        "to_char(ac.ultimo_fecha_notificacion, 'DD/MM/YYYY') as ultimo_fecha_notificacion", "to_char(ac.created_at, 'DD/MM/YYYY HH24:MI') as fecha_creacion",
        "to_char(ac.ultimo_fecha_derivacion, 'DD/MM/YYYY HH24:MI') as ultimo_fecha_derivacion");
        $where = array(
            'ac.deleted_at' => NULL
            //'ac.fk_usuario_actual' => session()->get('registroUser'),
        );
        $builder = $db->table('public.acto_administrativo as ac')
        ->select($campos)
        ->join('usuarios as ua', 'ac.fk_usuario_actual = ua.id', 'left')
        ->join('documentos as d', 'ac.ultimo_fk_documento = d.id', 'left')
        ->where($where)
        ->orderBY('ac.id', 'DESC');
        $datos = $builder->get()->getResult('array');
        if($datos){
            $dbSincobol = \Config\Database::connect('sincobol');
            $campos = array('am.codigo_unico', 'am.nombre as denominacion', 'acm.nombre as titular',
            'tam.nombre as clasificacion', 'am.departamentos');
            foreach($datos as $n=>$row){
                $where = array(
                    'am.id' => $row['fk_area_minera']
                );
                $builder = $dbSincobol->table('contratos_licencias.area_minera as am')
                ->select($campos)
                ->join('contratos_licencias.actor_minero as acm', 'am.fk_actor_minero_poseedor = acm.id', 'left')
                ->join('contratos_licencias.tipo_actor_minero as tam', 'acm.fk_tipo_actor_minero = tam.id', 'left')                
                ->where($where);
                $area = $builder->get()->getRowArray();
                $datos[$n]['codigo_unico'] = $area['codigo_unico'];
                $datos[$n]['denominacion'] = $area['denominacion'];
                $datos[$n]['titular'] = $area['titular'];
                $datos[$n]['clasificacion'] = $area['clasificacion'];
                $datos[$n]['departamentos'] = $area['departamentos'];                
            }
        }        
        $campos_listar=array(
            'Codigo Unico','Denominacion','Titular','Clasificacion','Departamentos','H.R. Madre','Persona Responsable',
            'Estado Tramite', 'Acto Administrativo', 'Fecha Emision', 'Fecha Notificacion', 'Fecha Derivacion', 'Fecha Creacion'
        );
        $campos_reales=array(
            'codigo_unico','denominacion','titular','clasificacion','departamentos','correlativo','responsable',
            'ultimo_estado_tramite','correlativo','ultimo_fecha_emision','ultimo_fecha_notificacion','ultimo_fecha_derivacion', 'fecha_creacion'
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
        $data['content'] = view($this->carpeta.'index', $contenido);
        $data['menu_actual'] = 'tramite_proceso_cam';
        echo view('templates/template', $data);
    }

    public function ajaxHojaRutaMadre(){
        $cadena = strtoupper($this->request->getPost('q'));
        if(!empty($cadena)){
            $data = array();
            $db = \Config\Database::connect('sincobol');
            $campos = array('slc.id', "CONCAT(am.nombre, ' - ', am.codigo_unico, ' (',slc.correlativo,')') as nombre");
            $where = array(
                'slc.estado_general' => 'EN TRAMITE',
                'slc.fk_tipo_solicitud' => 1,
                'slc.fk_hoja_ruta>' => 0,
                'slc.fk_acto_administrativo' => NULL,
            );
            $builder = $db->table('contratos_licencias.solicitud_licencia_contrato as slc')
            ->select($campos)
            ->join('contratos_licencias.area_minera as am', 'slc.fk_area_minera = am.id', 'left')
            ->where($where)
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
            $data = array();
            $db = \Config\Database::connect('sincobol');
            $campos = array('slc.id','slc.fk_area_minera','hr.referencia', "to_char(slc.fecha_ingreso, 'DD/MM/YYYY') as fecha_ingreso");
            $where = array(
                'slc.id' => $idSolicitud,
            );
            $builder = $db->table('contratos_licencias.solicitud_licencia_contrato as slc')
            ->select($campos)
            ->join('sincobol.hoja_ruta as hr', 'slc.fk_hoja_ruta = hr.id', 'left')
            ->where($where);
            $datos = $builder->get()->getFirstRow('array');
            $campos = array(
                'am.nombre', 'am.codigo_unico', 'ROUND(am.extension) as extension', 'am.unidad', 'am.departamentos',
                'am.provincias', 'am.municipios', 'acm.nombre as titular', 'tam.nombre as clasificacion',
                'dcam.domicilio_procesal', 'dcam.telefonos'
            );
            $where = array(
                'am.id' => $datos['fk_area_minera'],
            );
            $query = $db->table('contratos_licencias.area_minera as am')
            ->select($campos)
            ->join('contratos_licencias.actor_minero as acm', 'am.fk_actor_minero_poseedor = acm.id', 'left')
            ->join('sincobol.datos_contacto_actor_minero as dcam', 'acm.id = dcam.fk_actor_minero', 'left')
            ->join('contratos_licencias.tipo_actor_minero as tam', 'acm.fk_tipo_actor_minero = tam.id', 'left')
            ->where($where);
            $area_minera = $query->get()->getFirstRow('array');
            if($datos && $area_minera){
                $data=array(
                    'id' => $datos['id'],
                    'referencia' => $datos['referencia'],
                    'fecha_ingreso' => $datos['fecha_ingreso'],
                    'denominacion' => $area_minera['nombre'],
                    'codigo_unico' => $area_minera['codigo_unico'],
                    'extension' => $area_minera['extension'],
                    'unidad' => $area_minera['unidad'],
                    'titular' => $area_minera['titular'],
                    'clasificacion' => $area_minera['clasificacion'],
                    'direccion_titular' => $area_minera['domicilio_procesal'],
                    'telefono_titular' => $area_minera['telefonos'],
                    'departamentos' => $area_minera['departamentos'],
                    'municipios' => $area_minera['municipios'],
                    'provincias' => $area_minera['provincias'],

                );
                echo json_encode($data);
            }
        }
    }

    public function ajaxAnalistaDestinatario(){
        $cadena = strtoupper($this->request->getPost('texto'));
        if(!empty($cadena)){
            $data = array();
            $db = \Config\Database::connect();
            $campos = array(
                'u.id', "CONCAT(u.nombre_completo, ' (',u.cargo,' - ',o.nombre,')') as nombre"
            );
            $where = array(
                'u.activo' => true,
            );
            $builder = $db->table('usuarios as u')
            ->select($campos)
            ->join('oficinas as o', 'u.fk_oficina = o.id', 'left')
            ->where($where)
            ->like("CONCAT(u.nombre_completo, ' (',u.cargo,' - ',o.nombre,')')", $cadena)
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

    public function obtenerEstadosTramites($tipo){
        $estadoTramiteModel = new EstadoTramiteModel();
        $where = array(
            'fk_tipo_solicitud' => $tipo
        );
        $estadosTramites = $estadoTramiteModel->where($where)->orderBy('orden', 'ASC')->findAll();
        $temporal = array();
        $temporal[''] = 'SELECCIONE UNA OPCIÓN';
        foreach($estadosTramites as $row)
            $temporal[$row['id']] = $row['nombre'];
        return $temporal;
    }

    public function agregar(){
        $estadosTramites = $this->obtenerEstadosTramites(1);
        if ($this->request->getPost()) {
            $validation = $this->validate([
                'fk_solicitud_licencia_contrato' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar un Tipo de solicitud.'
                    ]
                ],
                'direccion_titular' => [
                    'rules' => 'required',
                ],
                'telefono_titular' => [
                    'rules' => 'required',
                ],
                'fk_estado_tramite' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar un Estado de Tramite.'
                    ]
                ],
                /*'fecha_emision' => [
                    'rules' => 'required',
                ],
                'fecha_notificacion' => [
                    'rules' => 'required',
                ],*/
                'fk_usuario_destinatario' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar un Estado de Tramite.'
                    ]
                ],
            ]);
            if(!$validation){
                $cabera['titulo'] = $this->titulo;
                $cabera['navegador'] = true;
                $cabera['subtitulo'] = 'Agregar Seguimiento CAM';
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['estadosTramites'] = $estadosTramites;
                $contenido['subtitulo'] = 'Agregar Seguimiento CAM';
                $contenido['accion'] = $this->controlador.'agregar';
                $data['content'] = view($this->carpeta.'agregar', $contenido);
                $data['menu_actual'] = 'crear_seguimiento_cam';
                $data['validacion_js'] = 'acto-administrativo-validation.js';
                echo view('templates/template', $data);
            }else{
                $adjuntoPDF = $this->request->getFile('adjunto_pdf');
                if(!empty($adjuntoPDF) && $adjuntoPDF->getSize()>0){
                    $nombreAdjunto = $adjuntoPDF->getRandomName();
                    $adjuntoPDF->move('archivos/documentos',$nombreAdjunto);
                }
                $idSolicitudContrato = $this->request->getPost('fk_solicitud_licencia_contrato');
                $solicitudLicenciaContratoModel = new SolicitudLicenciaContratoModel();
                $solicitudLicencia = $solicitudLicenciaContratoModel->find($idSolicitudContrato);
                $actoAdministrativoModel = new ActoAdministrativoModel();
                $data = array(
                    'fk_solicitud_licencia_contrato' => $solicitudLicencia['id'],
                    'fk_area_minera' => $solicitudLicencia['fk_area_minera'],
                    'fk_tipo_solicitud' => $solicitudLicencia['fk_tipo_solicitud'],
                    'fk_hoja_ruta' => $solicitudLicencia['fk_hoja_ruta'],
                    'correlativo' => $solicitudLicencia['correlativo'],
                    'fk_usuario_actual' => $this->request->getPost('fk_usuario_destinatario'),
                    'ultimo_estado_tramite' => $estadosTramites[$this->request->getPost('fk_estado_tramite')],
                    'ultimo_fk_documento'=>((!empty($this->request->getPost('fk_documento'))) ? $this->request->getPost('fk_documento') : NULL),
                    'ultimo_fecha_emision'=>((!empty($this->request->getPost('fecha_emision'))) ? $this->request->getPost('fecha_emision') : NULL),
                    'ultimo_fecha_notificacion'=>((!empty($this->request->getPost('fecha_notificacion'))) ? $this->request->getPost('fecha_notificacion') : NULL),
                    'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                    'fk_usuario_creador' => session()->get('registroUser'),
                );
                if($actoAdministrativoModel->insert($data) === false){
                    session()->setFlashdata('fail', $actoAdministrativoModel->errors());
                }else{
                    $idActoAdministrativo = $actoAdministrativoModel->getInsertID();
                    $derivacionModel = new DerivacionModel();
                    $dataDerivacion = array(
                        'fk_acto_administrativo' => $idActoAdministrativo,
                        'direccion_titular' => strtoupper($this->request->getPost('direccion_titular')),
                        'telefono_titular' => strtoupper($this->request->getPost('telefono_titular')),
                        'fk_estado_tramite' => $this->request->getPost('fk_estado_tramite'),
                        'fk_documento'=>((!empty($this->request->getPost('fk_documento'))) ? $this->request->getPost('fk_documento') : NULL),
                        'adjunto_pdf'=>((!empty($nombreAdjunto)) ? $nombreAdjunto : NULL),
                        'fecha_emision'=>((!empty($this->request->getPost('fecha_emision'))) ? $this->request->getPost('fecha_emision') : NULL),
                        'fecha_notificacion'=>((!empty($this->request->getPost('fecha_notificacion'))) ? $this->request->getPost('fecha_notificacion') : NULL),
                        'observaciones' => strtoupper($this->request->getPost('observaciones')),
                        'fk_usuario_remitente' => session()->get('registroUser'),
                        'fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                        'instruccion' => $estadosTramites[$this->request->getPost('fk_estado_tramite')],
                        'estado' => 'ENVIADO',
                        'fk_usuario_creador' => session()->get('registroUser'),
                    );
                    if($derivacionModel->insert($dataDerivacion) === false){
                        session()->setFlashdata('fail', $derivacionModel->errors());
                    }else{
                        $idDerivacion = $derivacionModel->getInsertID();
                        if($this->request->getPost('fk_documento')){
                            $documentosModel = new DocumentosModel();
                            $dataDocumento = array(
                                'id' => $this->request->getPost('fk_documento'),
                                'fk_derivacion' => $idDerivacion,
                            );
                            if($documentosModel->save($dataDocumento) === false){
                                session()->setFlashdata('fail', $documentosModel->errors());
                            }
                        }
                        $solicitudLicencia['fk_acto_administrativo'] = $idActoAdministrativo;
                        if($solicitudLicenciaContratoModel->save($solicitudLicencia) === false)
                            session()->setFlashdata('fail', $solicitudLicenciaContratoModel->errors());
                        else
                            session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información.');
                    }
                }
                return redirect()->to($this->controlador);
            }
        }else{
            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Agregar Seguimiento CAM';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['estadosTramites'] = $estadosTramites;
            $contenido['subtitulo'] = 'Agregar Seguimiento CAM';
            $contenido['accion'] = $this->controlador.'agregar';
            $data['content'] = view($this->carpeta.'agregar', $contenido);
            $data['menu_actual'] = 'crear_seguimiento_cam';
            $data['validacion_js'] = 'acto-administrativo-validation.js';
            echo view('templates/template', $data);
        }
    }
    public function atender($id){
        $actoAdministrativoModel = new ActoAdministrativoModel();
        $fila = $actoAdministrativoModel->find($id);
        if($fila){
            $db = \Config\Database::connect();
            $dbSincobol = \Config\Database::connect('sincobol');
            $campos = array('slc.id','slc.fk_area_minera','hr.referencia', "to_char(slc.fecha_ingreso, 'DD/MM/YYYY') as fecha_ingreso");
            $where = array(
                'slc.id' => $fila['fk_solicitud_licencia_contrato'],
            );
            $builder = $dbSincobol->table('contratos_licencias.solicitud_licencia_contrato as slc')
            ->select($campos)
            ->join('sincobol.hoja_ruta as hr', 'slc.fk_hoja_ruta = hr.id', 'left')
            ->where($where);
            $solicitudLicencia = $builder->get()->getFirstRow('array');
            $campos = array(
                'am.nombre', 'am.codigo_unico', 'ROUND(am.extension) as extension', 'am.unidad', 'am.departamentos',
                'am.provincias', 'am.municipios', 'acm.nombre as titular', 'tam.nombre as clasificacion',
                'dcam.domicilio_procesal', 'dcam.telefonos'
            );
            $where = array(
                'am.id' => $fila['fk_area_minera'],
            );
            $query = $dbSincobol->table('contratos_licencias.area_minera as am')
            ->select($campos)
            ->join('contratos_licencias.actor_minero as acm', 'am.fk_actor_minero_poseedor = acm.id', 'left')
            ->join('sincobol.datos_contacto_actor_minero as dcam', 'acm.id = dcam.fk_actor_minero', 'left')
            ->join('contratos_licencias.tipo_actor_minero as tam', 'acm.fk_tipo_actor_minero = tam.id', 'left')
            ->where($where);
            $areaMinera = $query->get()->getFirstRow('array');
            $campos = array(
                'de.direccion_titular','de.telefono_titular','de.fk_estado_tramite', 'do.correlativo', 'de.adjunto_pdf',
                "to_char(de.fecha_emision, 'DD/MM/YYYY') as fecha_emision", "to_char(de.fecha_notificacion, 'DD/MM/YYYY') as fecha_notificacion",
                'de.observaciones');
            $where = array(
                'fk_acto_administrativo' => $fila['id'],
            );
            $query = $db->table('derivacion as de')
            ->select($campos)
            ->join('documentos as do', 'de.fk_documento = do.id', 'left')
            ->where($where)
            ->orderBY('de.id', 'DESC');
            $ultimaDerivacion = $query->get()->getFirstRow('array');
            $estadosTramites = $this->obtenerEstadosTramites(1);
            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Atender Registro';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['fila'] = $fila;
            $contenido['solicitud_licencia'] = $solicitudLicencia;
            $contenido['area_minera'] = $areaMinera;
            $contenido['estadosTramites'] = $estadosTramites;
            $contenido['ultima_derivacion'] = $ultimaDerivacion;
            $contenido['subtitulo'] = 'Atender Registro';
            $contenido['accion'] = $this->controlador.'guardar_atender';
            $data['content'] = view($this->carpeta.'atender', $contenido);
            $data['menu_actual'] = 'tramite_proceso_cam';
            $data['validacion_js'] = 'acto-administrativo-atender-validation.js';
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador);
        }
    }
    public function guardar_atender(){
        $id = $this->request->getPost('id');
        if(isset($id) && $id>0){
            $actoAdministrativoModel = new ActoAdministrativoModel();
            $documentosModel = new DocumentosModel();
            $fila = $actoAdministrativoModel->find($id);
            $estadosTramites = $this->obtenerEstadosTramites(1);
            $validation = $this->validate([
                'direccion_titular' => [
                    'rules' => 'required',
                ],
                'telefono_titular' => [
                    'rules' => 'required',
                ],
                'fk_estado_tramite' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar un Estado de Tramite.'
                    ]
                ],
                'fk_documento' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar el Acto Administrativo.'
                    ]
                ],
                'fecha_emision' => [
                    'rules' => 'required',
                ],
                'fecha_notificacion' => [
                    'rules' => 'required',
                ],
                'fk_usuario_destinatario' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar un Estado de Tramite.'
                    ]
                ],
            ]);
            if(!$validation){
                $db = \Config\Database::connect();
                $campos = array('slc.id','slc.fk_area_minera','hr.referencia', "to_char(slc.fecha_ingreso, 'DD/MM/YYYY') as fecha_ingreso");
                $where = array(
                    'slc.id' => $fila['fk_solicitud_licencia_contrato'],
                );
                $builder = $db->table('contratos_licencias.solicitud_licencia_contrato as slc')
                ->select($campos)
                ->join('sincobol.hoja_ruta as hr', 'slc.fk_hoja_ruta = hr.id', 'left')
                ->where($where);
                $solicitudLicencia = $builder->get()->getFirstRow('array');
                $campos = array(
                    'am.nombre', 'am.codigo_unico', 'ROUND(am.extension) as extension', 'am.unidad', 'am.departamentos',
                    'am.provincias', 'am.municipios', 'acm.nombre as titular', 'tam.nombre as clasificacion',
                    'dcam.domicilio_procesal', 'dcam.telefonos'
                );
                $where = array(
                    'am.id' => $fila['fk_area_minera'],
                );
                $query = $db->table('contratos_licencias.area_minera as am')
                ->select($campos)
                ->join('contratos_licencias.actor_minero as acm', 'am.fk_actor_minero_poseedor = acm.id', 'left')
                ->join('sincobol.datos_contacto_actor_minero as dcam', 'acm.id = dcam.fk_actor_minero', 'left')
                ->join('contratos_licencias.tipo_actor_minero as tam', 'acm.fk_tipo_actor_minero = tam.id', 'left')
                ->where($where);
                $areaMinera = $query->get()->getFirstRow('array');
                $campos = array(
                    'de.direccion_titular','de.telefono_titular','de.fk_estado_tramite', 'do.correlativo', 'de.adjunto_pdf',
                    "to_char(de.fecha_emision, 'DD/MM/YYYY') as fecha_emision", "to_char(de.fecha_notificacion, 'DD/MM/YYYY') as fecha_notificacion",
                    'de.observaciones');
                $where = array(
                    'fk_acto_administrativo' => $fila['id'],
                );
                $query = $db->table('derivacion as de')
                ->select($campos)
                ->join('documentos as do', 'de.fk_documento = do.id', 'left')
                ->where($where)
                ->orderBY('de.id', 'DESC');
                $ultimaDerivacion = $query->get()->getFirstRow('array');
                $actoAdministrativo = $documentosModel->find($this->request->getPost('fk_documento'));
                $db = \Config\Database::connect();
                $campos = array(
                    'u.id', "CONCAT(u.nombre_completo, ' (',u.cargo,' - ',o.nombre,')') as nombre"
                );
                $where = array(
                    'u.id' => $this->request->getPost('fk_usuario_destinatario'),
                );
                $builder = $db->table('usuarios as u')
                ->select($campos)
                ->join('oficinas as o', 'u.fk_oficina = o.id', 'left')
                ->where($where);
                $destinatario = $builder->get()->getFirstRow('array');
                $cabera['titulo'] = $this->titulo;
                $cabera['navegador'] = true;
                $cabera['subtitulo'] = 'Atender Registro';
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['fila'] = $fila;
                $contenido['solicitud_licencia'] = $solicitudLicencia;
                $contenido['area_minera'] = $areaMinera;
                $contenido['estadosTramites'] = $estadosTramites;
                $contenido['ultima_derivacion'] = $ultimaDerivacion;
                $contenido['acto_administrativo'] = $actoAdministrativo;
                $contenido['destinatario'] = $destinatario;
                $contenido['subtitulo'] = 'Atender Registro';
                $contenido['accion'] = $this->controlador.'guardar_atender';
                $contenido['validation'] = $this->validator;
                $data['content'] = view($this->carpeta.'atender', $contenido);
                $data['menu_actual'] = 'tramite_proceso_cam';
                $data['validacion_js'] = 'acto-administrativo-atender-validation.js';
                echo view('templates/template', $data);
            }else{
                $adjuntoPDF = $this->request->getFile('adjunto_pdf');
                if(!empty($adjuntoPDF) && $adjuntoPDF->getSize()>0){
                    $nombreAdjunto = $adjuntoPDF->getRandomName();
                    $adjuntoPDF->move('archivos/documentos',$nombreAdjunto);
                }
                $data = array(
                    'id' => $id,
                    'fk_usuario_actual' => $this->request->getPost('fk_usuario_destinatario'),
                    'ultimo_estado_tramite' => $estadosTramites[$this->request->getPost('fk_estado_tramite')],
                    'ultimo_fk_documento'=>((!empty($this->request->getPost('fk_documento'))) ? $this->request->getPost('fk_documento') : NULL),
                    'ultimo_fecha_emision'=>((!empty($this->request->getPost('fecha_emision'))) ? $this->request->getPost('fecha_emision') : NULL),
                    'ultimo_fecha_notificacion'=>((!empty($this->request->getPost('fecha_notificacion'))) ? $this->request->getPost('fecha_notificacion') : NULL),
                    'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                );
                if($actoAdministrativoModel->save($data) === false){
                    session()->setFlashdata('fail', $actoAdministrativoModel->errors());
                }else{
                    $derivacionModel = new DerivacionModel();
                    $dataDerivacion = array(
                        'fk_acto_administrativo' => $id,
                        'direccion_titular' => strtoupper($this->request->getPost('direccion_titular')),
                        'telefono_titular' => strtoupper($this->request->getPost('telefono_titular')),
                        'fk_estado_tramite' => $this->request->getPost('fk_estado_tramite'),
                        'fk_documento'=>((!empty($this->request->getPost('fk_documento'))) ? $this->request->getPost('fk_documento') : NULL),
                        'adjunto_pdf'=>((!empty($nombreAdjunto)) ? $nombreAdjunto : NULL),
                        'fecha_emision'=>((!empty($this->request->getPost('fecha_emision'))) ? $this->request->getPost('fecha_emision') : NULL),
                        'fecha_notificacion'=>((!empty($this->request->getPost('fecha_notificacion'))) ? $this->request->getPost('fecha_notificacion') : NULL),
                        'observaciones' => strtoupper($this->request->getPost('observaciones')),
                        'fk_usuario_remitente' => session()->get('registroUser'),
                        'fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                        'instruccion' => $estadosTramites[$this->request->getPost('fk_estado_tramite')],
                        'estado' => 'ENVIADO',
                        'fk_usuario_creador' => session()->get('registroUser'),
                    );
                    if($derivacionModel->insert($dataDerivacion) === false){
                        session()->setFlashdata('fail', $derivacionModel->errors());
                    }else{
                        $idDerivacion = $derivacionModel->getInsertID();
                        $dataDocumento = array(
                            'id' => $this->request->getPost('fk_documento'),
                            'fk_derivacion' => $idDerivacion,
                        );
                        if($documentosModel->save($dataDocumento) === false)
                            session()->setFlashdata('fail', $documentosModel->errors());
                        else
                            session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información.');
                    }
                    return redirect()->to($this->controlador);
                }
            }
        }
    }
    public function modificar($id){
        $actoAdministrativoModel = new ActoAdministrativoModel();
        $fila = $actoAdministrativoModel->find($id);
        if($fila){
            $db = \Config\Database::connect();
            $dbSincobol = \Config\Database::connect('sincobol');
            $campos = array('slc.id','slc.fk_area_minera','hr.referencia', "to_char(slc.fecha_ingreso, 'DD/MM/YYYY') as fecha_ingreso");
            $where = array(
                'slc.id' => $fila['fk_solicitud_licencia_contrato'],
            );
            $builder = $dbSincobol->table('contratos_licencias.solicitud_licencia_contrato as slc')
            ->select($campos)
            ->join('sincobol.hoja_ruta as hr', 'slc.fk_hoja_ruta = hr.id', 'left')
            ->where($where);
            $solicitudLicencia = $builder->get()->getFirstRow('array');            
            $campos = array(
                'am.nombre', 'am.codigo_unico', 'ROUND(am.extension) as extension', 'am.unidad', 'am.departamentos',
                'am.provincias', 'am.municipios', 'acm.nombre as titular', 'tam.nombre as clasificacion',
                'dcam.domicilio_procesal', 'dcam.telefonos'
            );
            $where = array(
                'am.id' => $fila['fk_area_minera'],
            );
            $query = $dbSincobol->table('contratos_licencias.area_minera as am')
            ->select($campos)
            ->join('contratos_licencias.actor_minero as acm', 'am.fk_actor_minero_poseedor = acm.id', 'left')
            ->join('sincobol.datos_contacto_actor_minero as dcam', 'acm.id = dcam.fk_actor_minero', 'left')
            ->join('contratos_licencias.tipo_actor_minero as tam', 'acm.fk_tipo_actor_minero = tam.id', 'left')
            ->where($where);
            $areaMinera = $query->get()->getFirstRow('array');            
            $where = array(
                'fk_acto_administrativo' => $fila['id'],
            );
            $query = $db->table('derivacion')
            ->where($where)
            ->orderBY('id', 'DESC');
            $derivacion = $query->get()->getFirstRow('array');
            $campos = array('u.id', 'u.nombre_completo', 'u.cargo', 'o.nombre as oficina');
            $where = array(
                'u.id' => $derivacion['fk_usuario_destinatario'],
            );
            $query = $db->table('usuarios as u')
            ->select($campos)
            ->join('oficinas as o', 'u.fk_oficina = o.id', 'left')
            ->where($where);
            $usuario = $query->get()->getFirstRow('array');            

            $documentosModel = new DocumentosModel();
            $actoAdministrativo = ($derivacion['fk_documento']) ? $documentosModel->find($derivacion['fk_documento']):'';            
            $estadosTramites = $this->obtenerEstadosTramites(1);
            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Modificar Registro';
            $contenido['title'] = view('templates/title',$cabera);            
            $contenido['fila'] = $fila;
            $contenido['solicitud_licencia'] = $solicitudLicencia;
            $contenido['area_minera'] = $areaMinera;
            $contenido['estadosTramites'] = $estadosTramites;
            $contenido['derivacion'] = $derivacion;
            $contenido['usuario'] = $usuario;
            $contenido['acto_administrativo'] = $actoAdministrativo;
            $contenido['subtitulo'] = 'Modificar Registro';
            $contenido['accion'] = $this->controlador.'guardar_modificar';            
            $data['content'] = view($this->carpeta.'modificar', $contenido);            
            $data['menu_actual'] = 'tramite_proceso_cam';
            $data['validacion_js'] = 'acto-administrativo-modificar-validation.js';
            
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador);
        }
    }
    public function guardar_modificar(){
        $id = $this->request->getPost('id');
        $idDerivacion = $this->request->getPost('id_derivacion');
        if(isset($id) && $id>0){
            $actoAdministrativoModel = new ActoAdministrativoModel();
            $documentosModel = new DocumentosModel();
            $fila = $actoAdministrativoModel->find($id);
            $estadosTramites = $this->obtenerEstadosTramites(1);
            $validation = $this->validate([
                'direccion_titular' => [
                    'rules' => 'required',
                ],
                'telefono_titular' => [
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
                        'required' => 'Debe seleccionar un Estado de Tramite.'
                    ]
                ],
            ]);
            if(!$validation){
                $db = \Config\Database::connect();
                $campos = array('slc.id','slc.fk_area_minera','hr.referencia', "to_char(slc.fecha_ingreso, 'DD/MM/YYYY') as fecha_ingreso");
                $where = array(
                    'slc.id' => $fila['fk_solicitud_licencia_contrato'],
                );
                $builder = $db->table('contratos_licencias.solicitud_licencia_contrato as slc')
                ->select($campos)
                ->join('sincobol.hoja_ruta as hr', 'slc.fk_hoja_ruta = hr.id', 'left')
                ->where($where);
                $solicitudLicencia = $builder->get()->getFirstRow('array');
                $campos = array(
                    'am.nombre', 'am.codigo_unico', 'ROUND(am.extension) as extension', 'am.unidad', 'am.departamentos',
                    'am.provincias', 'am.municipios', 'acm.nombre as titular', 'tam.nombre as clasificacion',
                    'dcam.domicilio_procesal', 'dcam.telefonos'
                );
                $where = array(
                    'am.id' => $fila['fk_area_minera'],
                );
                $query = $db->table('contratos_licencias.area_minera as am')
                ->select($campos)
                ->join('contratos_licencias.actor_minero as acm', 'am.fk_actor_minero_poseedor = acm.id', 'left')
                ->join('sincobol.datos_contacto_actor_minero as dcam', 'acm.id = dcam.fk_actor_minero', 'left')
                ->join('contratos_licencias.tipo_actor_minero as tam', 'acm.fk_tipo_actor_minero = tam.id', 'left')
                ->where($where);
                $areaMinera = $query->get()->getFirstRow('array');
                $campos = array(
                    'direccion_titular','telefono_titular','fk_estado_tramite', 'acto_administrativo',
                    "to_char(fecha_emision, 'DD/MM/YYYY') as fecha_emision", "to_char(fecha_notificacion, 'DD/MM/YYYY') as fecha_notificacion",
                    'observaciones');
                $where = array(
                    'fk_acto_administrativo' => $fila['id'],
                );
                $query = $db->table('derivacion')
                ->select($campos)
                ->where($where)
                ->orderBY('id', 'DESC');
                $derivacion = $query->get()->getFirstRow('array');
                $where = array(
                    'id' => $derivacion['fk_usuario_destinatario'],
                );
                $query = $db->table('usuarios')
                ->where($where);
                $usuario = $query->get()->getFirstRow('array');
                $cabera['titulo'] = $this->titulo;
                $cabera['navegador'] = true;
                $cabera['subtitulo'] = 'Modificar Registro';
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['fila'] = $fila;
                $contenido['solicitud_licencia'] = $solicitudLicencia;
                $contenido['area_minera'] = $areaMinera;
                $contenido['estadosTramites'] = $estadosTramites;
                $contenido['derivacion'] = $derivacion;
                $contenido['usuario'] = $usuario;
                $contenido['subtitulo'] = 'Modificar Registro';
                $contenido['accion'] = $this->controlador.'guardar_modificar';
                $data['content'] = view($this->carpeta.'modificar', $contenido);
                $data['menu_actual'] = 'tramite_proceso_cam';
                $data['validacion_js'] = 'acto-administrativo-modificar-validation.js';
                echo view('templates/template', $data);
            }else{
                $adjuntoPDF = $this->request->getFile('adjunto_pdf');
                if(!empty($adjuntoPDF) && $adjuntoPDF->getSize()>0){
                    unlink('archivos/documentos/'.$this->request->getPost('adjunto_pdf_ant'));
                    $nombreAdjunto = $adjuntoPDF->getRandomName();
                    $adjuntoPDF->move('archivos/documentos',$nombreAdjunto);
                }else{
                    $nombreAdjunto = $this->request->getPost('adjunto_pdf_ant');
                }
                $data = array(
                    'id' => $id,
                    'fk_usuario_actual' => $this->request->getPost('fk_usuario_destinatario'),
                    'ultimo_estado_tramite' => $estadosTramites[$this->request->getPost('fk_estado_tramite')],
                    'ultimo_fk_documento'=>((!empty($this->request->getPost('fk_documento'))) ? $this->request->getPost('fk_documento') : NULL),
                    'ultimo_fecha_emision'=>((!empty($this->request->getPost('fecha_emision'))) ? $this->request->getPost('fecha_emision') : NULL),
                    'ultimo_fecha_notificacion'=>((!empty($this->request->getPost('fecha_notificacion'))) ? $this->request->getPost('fecha_notificacion') : NULL),
                );
                if($actoAdministrativoModel->save($data) === false){
                    session()->setFlashdata('fail', $actoAdministrativoModel->errors());
                }else{
                    $derivacionModel = new DerivacionModel();
                    $dataDerivacion = array(
                        'id' => $idDerivacion,
                        'direccion_titular' => strtoupper($this->request->getPost('direccion_titular')),
                        'telefono_titular' => strtoupper($this->request->getPost('telefono_titular')),
                        'fk_estado_tramite' => $this->request->getPost('fk_estado_tramite'),
                        'fk_documento'=>((!empty($this->request->getPost('fk_documento'))) ? $this->request->getPost('fk_documento') : NULL),
                        'adjunto_pdf'=>((!empty($nombreAdjunto)) ? $nombreAdjunto : NULL),
                        'fecha_emision'=>((!empty($this->request->getPost('fecha_emision'))) ? $this->request->getPost('fecha_emision') : NULL),
                        'fecha_notificacion'=>((!empty($this->request->getPost('fecha_notificacion'))) ? $this->request->getPost('fecha_notificacion') : NULL),
                        'observaciones' => strtoupper($this->request->getPost('observaciones')),
                        'fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                        'instruccion' => $estadosTramites[$this->request->getPost('fk_estado_tramite')],
                        'fk_usuario_modificador' => session()->get('registroUser'),
                    );
                    if($derivacionModel->save($dataDerivacion) === false){
                        session()->setFlashdata('fail', $derivacionModel->errors());
                    }else{
                        if($this->request->getPost('fk_documento') != $this->request->getPost('fk_documento_ant')){
                            $dataDocumentoAnt = array(
                                'id' => $this->request->getPost('fk_documento_ant'),
                                'fk_derivacion' => NULL,
                            );
                            if($documentosModel->save($dataDocumentoAnt) === false){
                                session()->setFlashdata('fail', $documentosModel->errors());
                            }else{
                                $dataDocumento = array(
                                    'id' => $this->request->getPost('fk_documento'),
                                    'fk_derivacion' => $idDerivacion,
                                );
                                if($documentosModel->save($dataDocumento) === false)
                                    session()->setFlashdata('fail', $documentosModel->errors());
                                else
                                    session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información.');
                            }
                        }
                    }
                    return redirect()->to($this->controlador);
                }
            }
        }
    }
    public function ver($id){
        $actoAdministrativoModel = new ActoAdministrativoModel();
        $fila = $actoAdministrativoModel->find($id);
        if($fila){
            $db = \Config\Database::connect();
            $dbSincobol = \Config\Database::connect('sincobol');
            $campos = array('slc.id','slc.fk_area_minera','hr.referencia', "to_char(slc.fecha_ingreso, 'DD/MM/YYYY') as fecha_ingreso");
            $where = array(
                'slc.id' => $fila['fk_solicitud_licencia_contrato'],
            );
            $builder = $dbSincobol->table('contratos_licencias.solicitud_licencia_contrato as slc')
            ->select($campos)
            ->join('sincobol.hoja_ruta as hr', 'slc.fk_hoja_ruta = hr.id', 'left')
            ->where($where);
            $solicitudLicencia = $builder->get()->getFirstRow('array');            
            $campos = array(
                'am.nombre', 'am.codigo_unico', 'ROUND(am.extension) as extension', 'am.unidad', 'am.departamentos',
                'am.provincias', 'am.municipios', 'acm.nombre as titular', 'tam.nombre as clasificacion',
                'dcam.domicilio_procesal', 'dcam.telefonos'
            );
            $where = array(
                'am.id' => $fila['fk_area_minera'],
            );
            $query = $dbSincobol->table('contratos_licencias.area_minera as am')
            ->select($campos)
            ->join('contratos_licencias.actor_minero as acm', 'am.fk_actor_minero_poseedor = acm.id', 'left')
            ->join('sincobol.datos_contacto_actor_minero as dcam', 'acm.id = dcam.fk_actor_minero', 'left')
            ->join('contratos_licencias.tipo_actor_minero as tam', 'acm.fk_tipo_actor_minero = tam.id', 'left')
            ->where($where);
            $areaMinera = $query->get()->getFirstRow('array');            
            
            $campos = array(
                'et.nombre as estado', 'd.direccion_titular', 'd.telefono_titular', "to_char(d.fecha_emision, 'DD/MM/YYYY') as fecha_emision",
                "to_char(d.fecha_notificacion, 'DD/MM/YYYY') as fecha_notificacion", 'ua.nombre_completo as usuario_remitente', 'ua.cargo as cargo_remitente',
                'ub.nombre_completo as usuario_destinatario', 'ub.cargo as cargo_destinatario', 'd.adjunto_pdf',
                "to_char(d.created_at, 'DD/MM/YYYY HH24:MI') as fecha_atencion", 'd.observaciones', 'doc.correlativo'
            );
            $where = array(
                'fk_acto_administrativo' => $fila['id'],
            );
            $query = $db->table('derivacion as d')
            ->select($campos)
            ->join('estado_tramite as et', 'd.fk_estado_tramite = et.id', 'left')
            ->join('usuarios as ua', 'd.fk_usuario_remitente = ua.id', 'left')
            ->join('usuarios as ub', 'd.fk_usuario_destinatario = ub.id', 'left')
            ->join('documentos as doc', 'd.fk_documento = doc.id', 'left')
            ->where($where)
            ->orderBY('d.id', 'ASC');
            $derivaciones = $query->get()->getResultArray();
            $cabecera_derivacion = array(
                'Fecha Atención',
                'Usuario Remitente',
                'Cargo Remitente',
                'Usuario Destinatario',
                'Cargo Destinatario',
                'Estado Tramite',
                'Acto Administrativo',
                'Documento Adjunto',
                'Dirección Titular',
                'Teléfono Titular',
                'Fecha Emisión',
                'Fecha Notificación',
                'Observaciones',
            );
            $campos_derivacion = array(
                'fecha_atencion',
                'usuario_remitente',
                'cargo_remitente',
                'usuario_destinatario',
                'cargo_destinatario',
                'estado',
                'correlativo',
                'adjunto_pdf',
                'direccion_titular',
                'telefono_titular',
                'fecha_emision',
                'fecha_notificacion',
                'observaciones',
            );

            $estadosTramites = $this->obtenerEstadosTramites(1);
            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Ver Estado Tramite';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['fila'] = $fila;
            $contenido['solicitud_licencia'] = $solicitudLicencia;
            $contenido['area_minera'] = $areaMinera;
            $contenido['estadosTramites'] = $estadosTramites;
            $contenido['derivaciones'] = $derivaciones;
            $contenido['cabecera_derivacion'] = $cabecera_derivacion;
            $contenido['campos_derivacion'] = $campos_derivacion;
            $contenido['subtitulo'] = 'Ver Estado Tramite';
            $contenido['controlador'] = $this->controlador;
            $data['content'] = view($this->carpeta.'ver', $contenido);
            $data['menu_actual'] = 'tramite_proceso_cam';
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador);
        }
    }
    /*
    public function editar($id){
        $estadoTramiteModel = new EstadoTramiteModel();
        $fila = $estadoTramiteModel->find($id);
        if($fila){
            $tipoSolicitudes = $this->obtenerTipoSolicitudes();
            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Editar Registro';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['fila'] = $fila;
            $contenido['tipoSolicitudes'] = $tipoSolicitudes;
            $contenido['subtitulo'] = 'Editar Registro';
            $contenido['accion'] = $this->controlador.'guardar_editar';
            $data['content'] = view($this->carpeta.'editar', $contenido);
            $data['validacion_js'] = 'estado-tramite-validation.js';
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador);
        }
    }
    public function guardar_editar(){
        $id = $this->request->getPost('id');
        if(isset($id) && $id>0){
            $estadoTramiteModel = new EstadoTramiteModel();
            $validation = $this->validate([
                'fk_tipo_solicitud' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar un Tipo de solicitud.'
                    ]
                ],
                'nombre' => [
                    'rules' => 'required',
                ],
            ]);
            if(!$validation){
                $fila = $usuariosModel->find($id);
                $tipoSolicitudes = $this->obtenerTipoSolicitudes();
                $cabera['titulo'] = $this->titulo;
                $cabera['navegador'] = true;
                $cabera['subtitulo'] = 'Editar Registro';
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['fila'] = $fila;
                $contenido['tipoSolicitudes'] = $tipoSolicitudes;
                $contenido['subtitulo'] = 'Editar Registro';
                $contenido['accion'] = $this->controlador.'guardar_editar';
                $contenido['validation'] = $this->validator;
                $data['content'] = view($this->carpeta.'editar', $contenido);
                $data['validacion_js'] = 'estado-tramite-validation.js';
                echo view('templates/template', $data);
            }else{
                $data = array(
                    'id' => $id,
                    'fk_tipo_solicitud' => $this->request->getPost('fk_tipo_solicitud'),
                    'nombre' => strtoupper($this->request->getPost('nombre')),
                    'descripcion' => strtoupper($this->request->getPost('descripcion')),
                );
                if($estadoTramiteModel->save($data) === false)
                    session()->setFlashdata('fail', $estadoTramiteModel->errors());
                else
                    session()->setFlashdata('success', 'Se Actualizo Correctamente la Información.');
                return redirect()->to($this->controlador);
            }
        }
    }
    public function eliminar($id){
        $estadoTramiteModel = new EstadoTramiteModel();
        $fila = $estadoTramiteModel->find($id);
        if($fila){
            $estadoTramiteModel->delete($fila['id']);
            session()->setFlashdata('success', 'Se elimino correctamente al Registro.');
        }else{
            session()->setFlashdata('fail', 'El Registro no existe.');
        }
        return redirect()->to($this->controlador);
    }
    */
}