<?php

namespace App\Controllers;

use App\Models\ActoAdministrativoModel;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\IOFactory;

use App\Models\CorrelativoDocumentosModel;
use App\Models\DenunciasHrSincobolMineriaIlegalModel;
use App\Models\DenunciasMineriaIlegalModel;
use App\Models\DerivacionSincobolModel;
use App\Models\TipoDocumentoModel;
use App\Models\DocumentosModel;
use App\Models\HojaRutaMineriaIlegalModel;
use App\Models\HojaRutaSisegModel;
use App\Models\HojasRutaAnexadasMineriaIlegalModel;
use App\Models\HrAnexadasModel;
use App\Models\OficinasModel;
use App\Models\TramitesModel;

use PhpOffice\PhpWord\TemplateProcessor;
/*use HTMLtoOpenXML\Parser;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Shared\Html;*/

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Documentos extends BaseController
{
    protected $titulo = 'Documentos';
    protected $controlador = 'documentos/';
    protected $carpeta = 'documentos/';
    protected $menuActual = '';
    protected $meses = array(
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre'
    );
    protected $tipo_hoja_ruta = array(
        1 => 'SOLICITUD CONTRATO ADMINISTRATIVO MINERO',
        2 => 'CONTRATO MINERO NACIONAL',
        3 => 'CONTRATO MINERO COMIBOL',
    );

    public function index($idTramite)
    {
        if($tramite = $this->tramitesMenu($idTramite)){
            $db = \Config\Database::connect();
            switch($tramite['controlador']){
                case 'cam/':
                    $campos = array('doc.id', 'adm.fk_area_minera', 'doc.correlativo', "to_char(doc.fecha, 'DD/MM/YYYY') as fecha", 'doc.referencia', 'dam.codigo_unico', 'adm.correlativo as hoja_ruta', 'doc.estado', 'td.nombre as tipo_documento', 'doc.doc_digital');
                    $where = array(
                        'doc.fk_tramite' => $idTramite,
                        'doc.fk_usuario_creador' => session()->get('registroUser'),
                    );
                    $builder = $db->table('public.documentos as doc')
                    ->select($campos)
                    ->join('public.tipo_documento as td', 'doc.fk_tipo_documento = td.id', 'left')
                    ->join('public.acto_administrativo as adm', 'doc.fk_acto_administrativo = adm.id', 'left')
                    ->join('public.datos_area_minera as dam', 'adm.id = dam.fk_acto_administrativo', 'left')
                    ->where($where)
                    ->orderBY('doc.id', 'DESC');
                    $datos = $builder->get()->getResult('array');
                    $campos_listar=array(
                        'Estado','Fecha','Correlativo', 'Tipo Documento', 'Referencia','Código Único','H.R. Madre', 'Documento Digital'
                    );
                    $campos_reales=array(
                        'estado','fecha','correlativo', 'tipo_documento', 'referencia','codigo_unico','hoja_ruta', 'doc_digital'
                    );
                    break;
                case 'mineria_ilegal/':
                    $campos = array(
                        'doc.id', 'doc.correlativo', "to_char(doc.fecha, 'DD/MM/YYYY') as fecha", 'doc.referencia', 'hr.correlativo as hoja_ruta', 'doc.estado'
                    );
                    $where = array(
                        'doc.fk_tramite' => $idTramite,
                        'doc.fk_usuario_creador' => session()->get('registroUser'),
                    );

                    $builder = $db->table('documentos AS doc')
                    ->select($campos)
                    ->join('mineria_ilegal.hoja_ruta AS hr', 'doc.fk_hoja_ruta = hr.id', 'left')
                    ->join('mineria_ilegal.denuncias AS den', 'hr.fk_denuncia = den.id', 'left')
                    ->join('usuarios as u', 'doc.fk_usuario_creador = u.id', 'left')
                    ->join('perfiles as p', 'u.fk_perfil = p.id', 'left')
                    ->where($where)
                    ->orderBY('doc.id', 'DESC');
                    $datos = $builder->get()->getResult('array');
                    $campos_listar=array(
                        'Fecha','Correlativo','Referencia','H.R. Madre','Estado'
                    );
                    $campos_reales=array(
                        'fecha','correlativo','referencia','hoja_ruta','estado'
                    );
                    break;
            }

            $this->menuActual = $tramite['controlador'].'documento/listado';
            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Mis Documentos Generados';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['datos'] = $datos;
            $contenido['campos_listar'] = $campos_listar;
            $contenido['campos_reales'] = $campos_reales;
            $contenido['subtitulo'] = 'Mis Documentos Generados';
            $contenido['controlador'] = $this->controlador;
            $contenido['id_tramite'] = $idTramite;
            $contenido['ruta_archivos'] = 'archivos/'.$tramite['controlador'];
            $data['content'] = view($this->carpeta.'index', $contenido);
            $data['menu_actual'] = $this->menuActual;
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['alertas'] = $this->alertasTramites();
            echo view('templates/template', $data);
        }
    }

    public function listadoAnulacion($idTramite)
    {
        if($tramite = $this->tramitesMenu($idTramite)){
            $db = \Config\Database::connect();
            $campos = array(
                'doc.id', 'doc.correlativo', "to_char(doc.fecha, 'DD/MM/YYYY') as fecha", 'doc.referencia', 'dam.codigo_unico', 'adm.correlativo as hoja_ruta', 'doc.estado',
                "CONCAT(u.nombre_completo,'<br><b>',p.nombre,'<b>') as usuario", 'doc.motivo_anulacion'
            );
            $where = array(
                'doc.fk_tramite' => $idTramite,
                'doc.estado' => 'SOLICITUD ANULACIÓN',
                'u.fk_oficina' => session()->get('registroOficina'),
            );

            $builder = $db->table('documentos as doc')
            ->select($campos)
            ->join('acto_administrativo as adm', 'doc.fk_acto_administrativo = adm.id', 'left')
            ->join('public.datos_area_minera as dam', 'adm.id = dam.fk_acto_administrativo', 'left')
            ->join('usuarios as u', 'doc.fk_usuario_creador = u.id', 'left')
            ->join('perfiles as p', 'u.fk_perfil = p.id', 'left')
            ->where($where)
            ->orderBY('doc.id', 'DESC');
            $datos = $builder->get()->getResult('array');
            $campos_listar=array(
                'Fecha','Correlativo','Referencia','H.R. Madre','Usuario', 'Motivo'
            );
            $campos_reales=array(
                'fecha','correlativo','referencia','hoja_ruta', 'usuario', 'motivo_anulacion'
            );
            $this->menuActual = $tramite['controlador'].'documento/listado_anulacion';
            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Anular Documentos';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['datos'] = $datos;
            $contenido['campos_listar'] = $campos_listar;
            $contenido['campos_reales'] = $campos_reales;
            $contenido['subtitulo'] = 'Anular Documentos';
            $contenido['controlador'] = $this->controlador;
            $contenido['id_tramite'] = $idTramite;
            $data['content'] = view($this->carpeta.'listado_anulacion', $contenido);
            $data['menu_actual'] = $this->menuActual;
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['alertas'] = $this->alertasTramites();
            echo view('templates/template', $data);
        }
    }

    private function obtenerTipoDocumentos($idTramite, $idPerfil){
        $tiposDocumentosModel = new TipoDocumentoModel();
        $tiposDocumentos = $tiposDocumentosModel->orderBy('nombre', 'ASC')->findAll();
        $temporal = array();
        $temporal[''] = 'SELECCIONE EL TIPO DE DOCUMENTO';
        if($tiposDocumentos){
            foreach($tiposDocumentos as $row){
                $tramites = explode(',', $row['tramites']);
                $perfiles = explode(',', $row['perfiles']);
                if( in_array($idTramite, $tramites) && in_array($idPerfil, $perfiles) )
                    $temporal[$row['id']] = $row['nombre'];
            }
        }
        return $temporal;
    }

    public function obtenerDatosUsuario($id){
        if(!empty($id)){
            $data = array();
            $db = \Config\Database::connect();
            $campos = array(
                'u.id', 'p', "CONCAT(u.nombre_completo, ' (',p.nombre,' - ',o.nombre,')') as nombre", 'o.departamento', 'o.correlativo', 'u.fk_perfil', 'u.tramites'
            );
            $where = array(
                'u.activo' => true,
                'u.id' => $id,
            );
            $builder = $db->table('usuarios as u')
            ->select($campos)
            ->join('oficinas as o', 'u.fk_oficina = o.id', 'left')
            ->join('perfiles as p', 'u.fk_perfil = p.id', 'left')
            ->where($where);
            $datos = $builder->get()->getFirstRow('array');
            if($datos){
                return $datos;
            }
        }
    }

    public function generarCorrelativo($tipo_documento, $datos_usuario, $idTramite){
        $tipoDocumentoModel = new TipoDocumentoModel();
        $tramiteModel = new TramitesModel();
        $tipoDocumento = $tipoDocumentoModel->find($tipo_documento);
        $tramite = $tramiteModel->find($idTramite);
        $correlativo = $datos_usuario['correlativo'].$tramite['correlativo'].$tipoDocumento['sigla'];
        $correlativoDocumentosModel = new CorrelativoDocumentosModel();
        $where = array(
            'sigla' => $correlativo,
            'gestion' => date('Y'),
        );
        if($correlativo_actual = $correlativoDocumentosModel->where($where)->first()){
            $correlativo_actual['correlativo_actual'] += 1;
            if($correlativoDocumentosModel->save($correlativo_actual))
                return $correlativo.$correlativo_actual['correlativo_actual'].'/'.date('Y');
        }else{
            $data = array(
                'sigla' => $correlativo,
                'gestion' => date('Y'),
                'correlativo_actual' => 1,
            );
            if($correlativoDocumentosModel->save($data))
                return $correlativo.'1/'.date('Y');
        }
    }

    public function ajaxAreaMinera(){
        if($texto = mb_strtoupper($this->request->getPost('texto'))){
            $data = array();
            $db = \Config\Database::connect('sincobol');
            $campos = array('id',"CONCAT(codigo_unico,' - ',nombre) as area_minera");
            $where = array(
                'vigente' => 'true',
                'fk_tipo_area_minera' => 2,
            );
            $builder = $db->table('contratos_licencias.area_minera')
            ->select($campos)
            ->where($where)
            ->like("CONCAT(codigo_unico,' - ',nombre)", $texto)
            ->orderBy('id','DESC')
            ->limit(20);
            $datos = $builder->get()->getResultArray();
            if($datos){
                foreach($datos as $row){
                    $data[] = array(
                        'id' => $row['id'],
                        'text' => $row['area_minera'],
                    );
                }
            }else{
                $data[] = array(
                    'id' => 0,
                    'text' => 'No se encuentra el área minera',
                );
            }
            echo json_encode($data);
        }
    }

    public function obtenerAreaMinera($idAreaMinera){
        if($idAreaMinera){
            $db = \Config\Database::connect('sincobol');
            $campos = array('id',"CONCAT(codigo_unico,' - ',nombre) as area_minera");
            $where = array(
                'vigente' => 'true',
                'fk_tipo_area_minera' => 2,
                'id' => $idAreaMinera
            );
            $builder = $db->table('contratos_licencias.area_minera')
            ->select($campos)
            ->where($where);
            $datos = $builder->get()->getFirstRow('array');
            if($datos)
                return $datos;
        }
    }

    public function agregar($idTramite, $id){

        if($tramite = $this->tramitesMenu($idTramite)){
            $db = \Config\Database::connect();
            switch($tramite['controlador']){
                case 'cam/':
                    $where = array(
                        'ac.deleted_at' => NULL,
                        'ac.id' => $id,
                    );
                    $builder = $db->table('public.acto_administrativo as ac')
                    ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
                    ->where($where);
                    if($fila = $builder->get()->getRowArray()){
                        $datosUsuario = $this->obtenerDatosUsuario(session()->get('registroUser'));
                        if ($this->request->getPost()) {
                            $reglas_validacion = array(
                                'fk_tipo_documento' => array(
                                    'rules' => 'required',
                                    'errors' => array(
                                        'required' => 'Debe seleccionar el Tipo de Documento.'
                                    ),
                                ),
                            );
                            $validation = $this->validate($reglas_validacion);
                            if(!$validation){
                                $contenido['validation'] = $this->validator;
                            }else{
                                $fk_tipo_documento = $this->request->getPost('fk_tipo_documento');
                                $documentosModel = new DocumentosModel();
                                $correlativo = $this->generarCorrelativo($fk_tipo_documento, $datosUsuario, $idTramite);
                                $data = array(
                                    'fk_tramite' => $idTramite,
                                    'fk_acto_administrativo' => $id,
                                    'correlativo' => $correlativo,
                                    'ciudad' => ucwords(strtolower($datosUsuario['departamento'])),
                                    'fecha' => date('Y-m-d'),
                                    'fk_tipo_documento' => $fk_tipo_documento,
                                    'referencia' => mb_strtoupper($this->request->getPost('referencia')),
                                    'fk_usuario_creador' => session()->get('registroUser'),
                                );
                                if($documentosModel->insert($data) === false){
                                    session()->setFlashdata('fail', $documentosModel->errors());
                                }else{
                                    $id = $documentosModel->getInsertID();
                                    session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información. <code><a href="'.base_url($this->controlador.'descargar/'.$id).'" target="_blank">Descargar Documento '.$correlativo.'</a></code>');
                                }
                                return redirect()->to($tramite['controlador'].'mis_tramites');
                            }
                        }
                    }
                    $this->menuActual = $tramite['controlador'].'documento/listado';
                    $cabera['titulo'] = $this->titulo;
                    $cabera['navegador'] = true;
                    $cabera['subtitulo'] = 'Nuevo Documento';
                    $contenido['title'] = view('templates/title',$cabera);
                    $contenido['datosUsuario'] = $datosUsuario;
                    $contenido['fila'] = $fila;
                    $contenido['tiposDocumentos'] = $this->obtenerTipoDocumentos($idTramite, $datosUsuario['fk_perfil']);
                    $contenido['tipo_tramite'] = $tramite['controlador'];
                    $contenido['subtitulo'] = 'Nuevo Documento';
                    $contenido['accion'] = $this->controlador.'agregar/'.$idTramite.'/'.$id;
                    $contenido['retorno'] = $tramite['controlador'].'mis_tramites';
                    $data['content'] = view($this->carpeta.'agregar', $contenido);
                    $data['editor_ck'] = false;
                    $data['validacion_js'] = 'documentos-validation.js';
                    $data['menu_actual'] = $this->menuActual;
                    $data['tramites_menu'] = $this->tramitesMenu();
                    $data['alertas'] = $this->alertasTramites();
                    echo view('templates/template', $data);
                    break;
                case 'mineria_ilegal/':
                    $where = array(
                        'hr.deleted_at' => NULL,
                        'hr.id' => $id,
                    );
                    $builder = $db->table('mineria_ilegal.hoja_ruta AS hr')
                    ->select("*, hr.id as id_hoja_ruta, hr.correlativo as correlativo, d.correlativo as correlativo_formulario, to_char(d.created_at, 'DD/MM/YYYY HH24:MI') as fecha_denuncia")
                    ->join('mineria_ilegal.denuncias as d', 'hr.fk_denuncia = d.id', 'left')
                    ->where($where);
                    if($fila = $builder->get()->getRowArray()){
                        $datosUsuario = $this->obtenerDatosUsuario(session()->get('registroUser'));
                        if ($this->request->getPost()) {
                            $reglas_validacion = array(
                                'fk_tipo_documento' => array(
                                    'rules' => 'required',
                                    'errors' => array(
                                        'required' => 'Debe seleccionar el Tipo de Documento.'
                                    ),
                                ),
                            );
                            $validation = $this->validate($reglas_validacion);
                            if(!$validation){
                                $contenido['validation'] = $this->validator;
                            }else{
                                $fk_tipo_documento = $this->request->getPost('fk_tipo_documento');
                                $documentosModel = new DocumentosModel();
                                $correlativo = $this->generarCorrelativo($fk_tipo_documento, $datosUsuario, $idTramite);
                                $data = array(
                                    'fk_tramite' => $idTramite,
                                    'fk_hoja_ruta' => $id,
                                    'correlativo' => $correlativo,
                                    'ciudad' => ucwords(strtolower($datosUsuario['departamento'])),
                                    'fecha' => date('Y-m-d'),
                                    'fk_tipo_documento' => $fk_tipo_documento,
                                    'referencia' => mb_strtoupper($this->request->getPost('referencia')),
                                    'fk_usuario_creador' => session()->get('registroUser'),
                                );
                                if($documentosModel->insert($data) === false){
                                    session()->setFlashdata('fail', $documentosModel->errors());
                                }else{
                                    $id = $documentosModel->getInsertID();
                                    session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información. <code><a href="'.base_url($this->controlador.'descargar/'.$id).'" target="_blank">Descargar Documento '.$correlativo.'</a></code>');
                                }
                                return redirect()->to($tramite['controlador'].'mis_tramites');
                            }
                        }
                    }
                    $this->menuActual = $tramite['controlador'].'documento/listado';
                    $cabera['titulo'] = $this->titulo;
                    $cabera['navegador'] = true;
                    $cabera['subtitulo'] = 'Nuevo Documento';
                    $contenido['title'] = view('templates/title',$cabera);
                    $contenido['datosUsuario'] = $datosUsuario;
                    $contenido['fila'] = $fila;
                    $contenido['tiposDocumentos'] = $this->obtenerTipoDocumentos($idTramite, $datosUsuario['fk_perfil']);
                    $contenido['tipo_tramite'] = $tramite['controlador'];
                    $contenido['subtitulo'] = 'Nuevo Documento';
                    $contenido['accion'] = $this->controlador.'agregar/'.$idTramite.'/'.$id;
                    $contenido['retorno'] = $tramite['controlador'].'mis_tramites';
                    $data['content'] = view($this->carpeta.'agregar', $contenido);
                    $data['editor_ck'] = false;
                    $data['validacion_js'] = 'documentos-validation.js';
                    $data['menu_actual'] = $this->menuActual;
                    $data['tramites_menu'] = $this->tramitesMenu();
                    $data['alertas'] = $this->alertasTramites();
                    echo view('templates/template', $data);
                    break;
            }
        }
    }

    public function editar($idTramite, $id){
        $db = \Config\Database::connect();
        $campos = array('doc.id', 'doc.correlativo', 'doc.fk_tipo_documento', 'adm.correlativo as hr', 'dam.codigo_unico', 'dam.denominacion', 'doc.ciudad', "TO_CHAR(doc.fecha , 'DD/MM/YYYY') as fecha", 'doc.referencia');
        $where = array(
            'doc.id' => $id,
            'doc.fk_tramite' => $idTramite,
            'doc.fk_usuario_creador' => session()->get('registroUser'),
            'doc.estado' => 'SUELTO'
        );
        $builder = $db->table('documentos as doc')
        ->select($campos)
        ->join('acto_administrativo as adm', 'doc.fk_acto_administrativo = adm.id', 'left')
        ->join('public.datos_area_minera as dam', 'adm.id = dam.fk_acto_administrativo', 'left')
        ->where($where);
        if($fila = $builder->get()->getRowArray()){
            if($tramite = $this->tramitesMenu($idTramite)){
                $datosUsuario = $this->obtenerDatosUsuario(session()->get('registroUser'));
                $this->menuActual = $tramite['controlador'].'documento/listado';
                $cabera['titulo'] = $this->titulo;
                $cabera['navegador'] = true;
                $cabera['subtitulo'] = 'Editar Documento';
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['fila'] = $fila;
                $contenido['id_tramite'] = $idTramite;
                $contenido['tiposDocumentos'] = $this->obtenerTipoDocumentos($idTramite, $datosUsuario['fk_perfil']);
                $contenido['subtitulo'] = 'Editar Documento';
                $contenido['accion'] = $this->controlador.'guardar_editar';
                $contenido['retorno'] = $this->controlador.'listado/'.$idTramite;
                $data['content'] = view($this->carpeta.'editar', $contenido);
                $data['editor_ck'] = false;
                $data['validacion_js'] = 'documentos-editar-validation.js';
                $data['menu_actual'] = $this->menuActual;
                $data['tramites_menu'] = $this->tramitesMenu();
                $data['alertas'] = $this->alertasTramites();
                echo view('templates/template', $data);
            }
        }else{
            session()->setFlashdata('fail', 'El registro no existe o ya cambio de estado.');
            return redirect()->to($this->controlador.'listado/'.$idTramite);
        }
    }
    public function guardarEditar(){

        if ($this->request->getPost()) {
            $idTramite = $this->request->getPost('id_tramite');
            $validation = $this->validate([
                'id' => [
                    'rules' => 'required',
                ],
                'id_tramite' => [
                    'rules' => 'required',
                ],
                /*'referencia' => [
                    'rules' => 'required',
                ]*/
            ]);
            if(!$validation){
                if($tramite = $this->tramitesMenu($idTramite)){
                    $datosUsuario = $this->obtenerDatosUsuario(session()->get('registroUser'));
                    $this->menuActual = $tramite['controlador'].'documento/listado';
                    $cabera['titulo'] = $this->titulo;
                    $cabera['navegador'] = true;
                    $cabera['subtitulo'] = 'Editar Documento';
                    $contenido['title'] = view('templates/title',$cabera);
                    $contenido['id_tramite'] = $idTramite;
                    $contenido['tiposDocumentos'] = $this->obtenerTipoDocumentos($idTramite, $datosUsuario['fk_perfil']);
                    $contenido['subtitulo'] = 'Editar Documento';
                    $contenido['accion'] = $this->controlador.'guardar_editar';
                    $contenido['retorno'] = $this->controlador.'listado/'.$idTramite;
                    $data['content'] = view($this->carpeta.'editar', $contenido);
                    $data['editor_ck'] = false;
                    $data['validacion_js'] = 'documentos-editar-validation.js';
                    $data['menu_actual'] = $this->menuActual;
                    $data['tramites_menu'] = $this->tramitesMenu();
                    $data['alertas'] = $this->alertasTramites();
                    echo view('templates/template', $data);
                }
            }else{
                $id = $this->request->getPost('id');
                $correlativo = $this->request->getPost('correlativo');
                $documentosModel = new DocumentosModel();
                $data = array(
                    'id' => $id,
                    'referencia' => mb_strtoupper($this->request->getPost('referencia')),
                    'fk_usuario_editor' => session()->get('registroUser'),
                );
                if($documentosModel->save($data) === false){
                    session()->setFlashdata('fail', $documentosModel->errors());
                }else{
                    session()->setFlashdata('success', 'Se Actualizo Correctamente la Información. <code><a href="'.base_url($this->controlador.'descargar/'.$id).'" target="_blank">Descargar Documento '.$correlativo.'</a></code>');
                    return redirect()->to($this->controlador.'listado/'.$idTramite);
                }
            }
        }
    }

    public function anular($idTramite, $id){
        $db = \Config\Database::connect();
        $campos = array('doc.id', 'doc.correlativo', 'doc.fk_tipo_documento', 'adm.correlativo as hr', 'dam.codigo_unico', 'dam.denominacion', 'doc.ciudad', "TO_CHAR(doc.fecha , 'DD/MM/YYYY') as fecha", 'doc.referencia');
        $where = array(
            'doc.id' => $id,
            'doc.fk_tramite' => $idTramite,
            'doc.fk_usuario_creador' => session()->get('registroUser'),
            'doc.estado' => 'SUELTO'
        );
        $builder = $db->table('documentos as doc')
        ->select($campos)
        ->join('acto_administrativo as adm', 'doc.fk_acto_administrativo = adm.id', 'left')
        ->join('public.datos_area_minera as dam', 'adm.id = dam.fk_acto_administrativo', 'left')
        ->where($where);
        if($fila = $builder->get()->getRowArray()){
            if($tramite = $this->tramitesMenu($idTramite)){
                $datosUsuario = $this->obtenerDatosUsuario(session()->get('registroUser'));
                $this->menuActual = $tramite['controlador'].'documento/listado';
                $cabera['titulo'] = $this->titulo;
                $cabera['navegador'] = true;
                $cabera['subtitulo'] = 'Solicitar Anulación de Documento';
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['fila'] = $fila;
                $contenido['id_tramite'] = $idTramite;
                $contenido['tiposDocumentos'] = $this->obtenerTipoDocumentos($idTramite, $datosUsuario['fk_perfil']);
                $contenido['subtitulo'] = 'Solicitar Anulación de Documento';
                $contenido['accion'] = $this->controlador.'guardar_anular';
                $contenido['retorno'] = $this->controlador.'listado/'.$idTramite;
                $data['content'] = view($this->carpeta.'anular', $contenido);
                $data['editor_ck'] = false;
                $data['validacion_js'] = 'documentos-anular-validation.js';
                $data['menu_actual'] = $this->menuActual;
                $data['tramites_menu'] = $this->tramitesMenu();
                $data['alertas'] = $this->alertasTramites();
                echo view('templates/template', $data);
            }
        }else{
            session()->setFlashdata('fail', 'El registro no existe o ya cambio de estado.');
            return redirect()->to($this->controlador.'listado/'.$idTramite);
        }
    }
    public function guardarAnular(){
        if ($this->request->getPost()) {
            $idTramite = $this->request->getPost('id_tramite');
            $validation = $this->validate([
                'id' => [
                    'rules' => 'required',
                ],
                'id_tramite' => [
                    'rules' => 'required',
                ],
                'motivo_anulacion' => [
                    'rules' => 'required',
                ]
            ]);
            if(!$validation){
                if($tramite = $this->tramitesMenu($idTramite)){
                    $datosUsuario = $this->obtenerDatosUsuario(session()->get('registroUser'));
                    $this->menuActual = $tramite['controlador'].'documento/listado';
                    $cabera['titulo'] = $this->titulo;
                    $cabera['navegador'] = true;
                    $cabera['subtitulo'] = 'Solicitar Anulación de Documento';
                    $contenido['title'] = view('templates/title',$cabera);
                    $contenido['id_tramite'] = $idTramite;
                    $contenido['tiposDocumentos'] = $this->obtenerTipoDocumentos($idTramite, $datosUsuario['fk_perfil']);
                    $contenido['subtitulo'] = 'Solicitar Anulación de Documento';
                    $contenido['accion'] = $this->controlador.'guardar_anular';
                    $contenido['retorno'] = $this->controlador.'listado/'.$idTramite;
                    $data['content'] = view($this->carpeta.'anular', $contenido);
                    $data['editor_ck'] = false;
                    $data['validacion_js'] = 'documentos-anular-validation.js';
                    $data['menu_actual'] = $this->menuActual;
                    $data['tramites_menu'] = $this->tramitesMenu();
                    $data['alertas'] = $this->alertasTramites();
                    echo view('templates/template', $data);
                }
            }else{
                $id = $this->request->getPost('id');
                $documentosModel = new DocumentosModel();
                $data = array(
                    'id' => $id,
                    'estado' => 'SOLICITUD ANULACIÓN',
                    'motivo_anulacion' => mb_strtoupper($this->request->getPost('motivo_anulacion')),
                    'fk_usuario_sol_anulacion' => session()->get('registroUser'),
                );
                if($documentosModel->save($data) === false){
                    session()->setFlashdata('fail', $documentosModel->errors());
                }else{
                    session()->setFlashdata('success', 'Se Actualizo Correctamente la Información.');
                    return redirect()->to($this->controlador.'listado/'.$idTramite);
                }
            }
        }
    }

    public function aprobarAnulacion($idTramite, $id){
        $documentosModel = new DocumentosModel();
        if($documento = $documentosModel->find($id)){
            $documento['estado'] = 'ANULADO';
            $documento['fk_usuario_aut_anulacion'] = session()->get('registroUser');
            $documento['deleted_at'] = date('Y-m-d H:i:s');
            if($documentosModel->save($documento) === false){
                session()->setFlashdata('fail', $documentosModel->errors());
            }else{
                session()->setFlashdata('success', 'Se Actualizo Correctamente la Información.');
                return redirect()->to($this->controlador.'listado_anulacion/'.$idTramite);
            }
        }
    }

    public function rechazarAnulacion($idTramite, $id){
        $documentosModel = new DocumentosModel();
        if($documento = $documentosModel->find($id)){
            $documento['estado'] = 'SUELTO';
            $documento['fk_usuario_aut_anulacion'] = session()->get('registroUser');
            if($documentosModel->save($documento) === false){
                session()->setFlashdata('fail', $documentosModel->errors());
            }else{
                session()->setFlashdata('success', 'Se Actualizo Correctamente la Información.');
                return redirect()->to($this->controlador.'listado_anulacion/'.$idTramite);
            }
        }
    }

    public function buscador()
    {
        $tramitesModel = new TramitesModel();
        $tmpTramites = $tramitesModel->findAll();
        $tramites = array();
        $datos = array();
        foreach($tmpTramites as $row)
            $tramites[$row['id']] = $row['nombre'];
        $campos_buscar=array(
            'correlativo' => 'Correlativo Documento',
            'hoja_ruta' => 'Hoja de Ruta',
        );
        $campos_listar=array(
            'Estado','Fecha','Correlativo', 'Tipo Documento', 'Referencia','Hoja de Ruta', 'Documento Digital', 'Usuario Creación', 'Usuario Anulación', 'Motivo Anulación'
        );
        $campos_reales=array(
            'estado','fecha','correlativo', 'tipo_documento', 'referencia','hoja_ruta', 'doc_digital', 'usuario_creacion', 'usuario_anulacion', 'motivo_anulacion'
        );
        if ($this->request->getPost()) {
            $validation = $this->validate([
                'texto' => [
                    'rules' => 'required',
                ],
            ]);
            if(!$validation){
                $contenido['validation'] = $this->validator;
            }else{
                $db = \Config\Database::connect();
                $texto = mb_strtoupper(trim($this->request->getPost('texto')));
                $where = array(
                    'doc.deleted_at' => NULL,
                    'doc.fk_tramite' => $this->request->getPost('tramite'),
                );
                switch($this->request->getPost('tramite')){
                    case 1:
                        $campos = array(
                            'doc.id', 'doc.estado', "to_char(doc.fecha, 'DD/MM/YYYY') as fecha", "doc.correlativo", "td.nombre as tipo_documento", "doc.referencia", "adm.correlativo as hoja_ruta",
                            "doc.doc_digital", "CONCAT(uc.nombre_completo, '<br><b>',pc.nombre,'</b>') as usuario_creacion",
                            "CASE WHEN doc.estado = 'ANULADO' THEN CONCAT(ua.nombre_completo, '<br><b>',pa.nombre,'</b>') ELSE '' END as usuario_anulacion", "doc.motivo_anulacion", "adm.fk_area_minera"
                        );
                        $builder = $db->table('documentos AS doc')
                        ->select($campos)
                        ->join('public.tipo_documento AS td', 'doc.fk_tipo_documento = td.id', 'left')
                        ->join('public.acto_administrativo AS adm', 'doc.fk_acto_administrativo = adm.id', 'left')
                        ->join('usuarios AS uc', 'doc.fk_usuario_creador = uc.id', 'left')
                        ->join('perfiles AS pc', 'uc.fk_perfil=pc.id', 'left')
                        ->join('usuarios AS ua', 'doc.fk_usuario_aut_anulacion = ua.id', 'left')
                        ->join('perfiles AS pa', 'ua.fk_perfil=pa.id', 'left')
                        ->where($where)
                        ->like('doc.correlativo', $texto)
                        ->orderBY('doc.fecha', 'DESC');
                        $datos = $builder->get()->getResultArray();
                        break;
                    case 2:
                        $campos = array(
                            'doc.id', 'doc.estado', "to_char(doc.fecha, 'DD/MM/YYYY') as fecha", "doc.correlativo", "td.nombre as tipo_documento", "doc.referencia", "hr.correlativo as hoja_ruta",
                            "doc.doc_digital", "CONCAT(uc.nombre_completo, '<br><b>',pc.nombre,'</b>') as usuario_creacion",
                            "CASE WHEN doc.estado = 'ANULADO' THEN CONCAT(ua.nombre_completo, '<br><b>',pa.nombre,'</b>') ELSE '' END as usuario_anulacion", "doc.motivo_anulacion"
                        );
                        $builder = $db->table('documentos AS doc')
                        ->select($campos)
                        ->join('public.tipo_documento AS td', 'doc.fk_tipo_documento = td.id', 'left')
                        ->join('mineria_ilegal.hoja_ruta AS hr', 'doc.fk_hoja_ruta = hr.id', 'left')
                        ->join('usuarios AS uc', 'doc.fk_usuario_creador = uc.id', 'left')
                        ->join('perfiles AS pc', 'uc.fk_perfil=pc.id', 'left')
                        ->join('usuarios AS ua', 'doc.fk_usuario_aut_anulacion = ua.id', 'left')
                        ->join('perfiles AS pa', 'ua.fk_perfil=pa.id', 'left')
                        ->where($where)
                        ->like('doc.correlativo', $texto)
                        ->orderBY('doc.fecha', 'DESC');
                        $datos = $builder->get()->getResultArray();
                        break;
                }
            }
        }

        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Desanexar Documentos';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['campos_listar'] = $campos_listar;
        $contenido['campos_reales'] = $campos_reales;
        $contenido['datos'] = $datos;
        $contenido['tramites'] = $tramites;
        $contenido['campos_buscar'] = $campos_buscar;
        $contenido['subtitulo'] = 'Desanexar Documentos';
        $contenido['accion'] = $this->controlador.'buscador';
        $contenido['controlador'] = $this->controlador;
        $data['content'] = view($this->carpeta.'buscador', $contenido);
        $data['menu_actual'] = 'documentos/buscador';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['alertas'] = $this->alertasTramites();
        echo view('templates/template', $data);
    }
    public function desanexar($id){
        $documentosModel = new DocumentosModel();
        if($documento = $documentosModel->find($id)){
            if($documento['estado']=='ANEXADO'){
                $dataDocumento = array(
                    'id' => $documento['id'],
                    'fk_derivacion' => NULL,
                    'estado' => 'SUELTO',
                );
                if($documentosModel->save($dataDocumento) === false)
                    session()->setFlashdata('fail', $documentosModel->errors());
                else
                    session()->setFlashdata('success', 'Se actualizo correctamente la Información.');
            }else{
                session()->setFlashdata('fail', 'No existe el documento.');
            }
        }else{
            session()->setFlashdata('fail', 'No existe el documento.');
        }
        return redirect()->to($this->controlador.'buscador');
    }

    public function buscadorSincobol()
    {
        $tramitesModel = new TramitesModel();
        $tmpTramites = $tramitesModel->findAll();
        $tramites = array();
        $datos = array();
        foreach($tmpTramites as $row)
            $tramites[$row['id']] = $row['nombre'];
        $datos = array();
        $campos_buscar=array(
            'correlativo' => 'Correlativo H.R. IN/EX',
        );
        $campos_listar=array(
            'Usuario que Anexo', 'Hoja Ruta Anexada', 'Tipo Trámite', 'Hoja Ruta Interno/Externo','Referencia','Remitente Externo/Interno', 'Cite Externo/Interno',
        );
        $campos_reales=array(
            'usuario', 'correlativo_siseg', 'id_tramite','correlativo','referencia', 'remitente','cite',
        );
        if ($this->request->getPost()) {
            $validation = $this->validate([
                'texto' => [
                    'rules' => 'required',
                ],
            ]);
            if(!$validation){
                $contenido['validation'] = $this->validator;
            }else{
                $actoAdministrativoModel = new ActoAdministrativoModel();
                $hojaRutaMineriaIlegalModel = new HojaRutaMineriaIlegalModel();
                $dbSincobol = \Config\Database::connect('sincobol');
                $texto = mb_strtoupper(trim($this->request->getPost('texto')));
                $campos = array(
                    'hrs.id as id_hoja_ruta_seguimiento', 'hrs.fk_tramite as id_tramite', 'hrs.fk_siseg as id_siseg','hr.id as id_hoja_ruta',
                    'thr.nombre as tipo_hoja_ruta', 'hr.correlativo', "TO_CHAR(hr.fecha, 'DD/MM/YYYY') as fecha, hr.referencia",
                    "CONCAT(pd.nombres, p.nombres, ' ', pd.apellido_paterno, p.apellido_paterno, ' ', pd.apellido_materno, p.apellido_materno, '<br />', c.nombre , e.cargo, '<br />', a.nombre ,e.institucion) as remitente",
                    "CONCAT(d.correlativo, hr.cite_documento_externo, '<br />', TO_CHAR(d.fecha_creacion, 'DD/MM/YYYY'), TO_CHAR(hr.fecha_cite_externo, 'DD/MM/YYYY')) as cite",
                    'hrs.usuario'
                );
                $builder = $dbSincobol->table('sincobol.hoja_ruta_siseg AS hrs')
                ->select($campos)
                ->join('sincobol.hoja_ruta AS hr', 'hrs.fk_hoja_ruta = hr.id', 'left')
                ->join('sincobol.tipo_hoja_ruta as thr', 'hr.fk_tipo_hoja_ruta=thr.id', 'left')
                ->join('sincobol.externo as e', 'e.id=hr.fk_externo_remitente', 'left')
                ->join('sincobol.documento as d', 'd.id=hr.fk_documento_original', 'left')
                ->join('sincobol.persona as p', 'p.id=e.fk_persona', 'left')
                ->join('sincobol.asignacion_cargo as ac', 'ac.id=d.fk_asignacion_cargo', 'left')
                ->join('sincobol.cargo as c', 'c.id=ac.fk_cargo', 'left')
                ->join('sincobol.area as a', 'a.id=c.fk_area', 'left')
                ->join('sincobol.persona as pd', 'pd.id=ac.fk_persona', 'left')
                ->like('hr.correlativo', $texto)
                ->orderBy('fecha', 'ASC');
                if($hr_anexadas = $builder->get()->getResultArray()){
                    foreach($hr_anexadas as $row){
                        switch($row['id_tramite']){
                            case 1:
                                $tmp = $actoAdministrativoModel->select(array('correlativo as correlativo_siseg'))->find($row['id_siseg']);
                                break;
                            case 2:
                                $tmp = $hojaRutaMineriaIlegalModel->select(array('correlativo as correlativo_siseg'))->where(array('fk_denuncia'=>$row['id_siseg']))->first();
                                break;
                        }
                        if(!$tmp)
                            $datos[] = array_merge($row, array('correlativo_siseg'=>''));
                        else
                            $datos[] = array_merge($row, $tmp);
                    }
                }
            }
        }

        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Desanexar Hojas de Ruta Internas/Externas';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['campos_listar'] = $campos_listar;
        $contenido['campos_reales'] = $campos_reales;
        $contenido['datos'] = $datos;
        $contenido['campos_buscar'] = $campos_buscar;
        $contenido['tramites'] = $tramites;
        $contenido['subtitulo'] = 'Desanexar Hojas de Ruta Internas/Externas';
        $contenido['accion'] = $this->controlador.'buscador_sincobol';
        $contenido['controlador'] = $this->controlador;
        $data['content'] = view($this->carpeta.'buscador_sincobol', $contenido);
        $data['menu_actual'] = 'documentos/buscador_sincobol';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['alertas'] = $this->alertasTramites();
        echo view('templates/template', $data);
    }
    public function desanexarSincobol($id_hoja_ruta_seguimiento, $id_tramite, $id_siseg, $id_hoja_ruta){
        $hojaRutaSisegModel = new HojaRutaSisegModel();
        $derivacionSincobolModel = new DerivacionSincobolModel();
        if($hojaRutaSeguimiento = $hojaRutaSisegModel->find($id_hoja_ruta_seguimiento)){

            $ultima_derivacion = $derivacionSincobolModel->select(array('id', 'fecha_recepcion'))->where(array('fk_hoja_ruta' => $id_hoja_ruta, 'estado' => 'CONCLUIDO'))
            ->like('motivo_conclusion', 'ANEXADO AL SISTEMA DE SEGUIMIENTO Y CONTROL DE TRAMITES')->first();
            $dataDerivacion = array(
                'id' => $ultima_derivacion['id'],
                'estado' =>  $ultima_derivacion['fecha_recepcion'] ? 'RECIBIDO' : 'ENVIADO',
                'fecha_conclusion' => NULL,
                'motivo_conclusion' => NULL,
            );
            if($derivacionSincobolModel->save($dataDerivacion) === false)
                session()->setFlashdata('fail', $derivacionSincobolModel->errors());

            switch($id_tramite){
                case 1:
                    $hrAnexadasModel = new HrAnexadasModel();
                    if($hojaRutaAnexada = $hrAnexadasModel->where(array('fk_hoja_ruta' => $id_hoja_ruta))->first())
                        $hrAnexadasModel->delete($hojaRutaAnexada['id']);
                    break;
                case 2:
                    $hojasRutaAnexadasMineriaIlegalModel = new HojasRutaAnexadasMineriaIlegalModel();
                    $denunciasHrSincobolMineriaIlegalModel = new DenunciasHrSincobolMineriaIlegalModel();

                    if($hojaRutaAnexada = $hojasRutaAnexadasMineriaIlegalModel->where(array('fk_hoja_ruta' => $id_hoja_ruta))->first())
                        $hojasRutaAnexadasMineriaIlegalModel->delete($hojaRutaAnexada['id']);

                    if($denunciaHojaRutaAnexada = $denunciasHrSincobolMineriaIlegalModel->where(array('fk_hoja_ruta' => $id_hoja_ruta))->first())
                        $denunciasHrSincobolMineriaIlegalModel->delete($denunciaHojaRutaAnexada['id']);

                    break;
            }
            $hojaRutaSisegModel->delete($hojaRutaSeguimiento['id']);
            session()->setFlashdata('success', 'Se actualizo correctamente la Información.');
        }else{
            session()->setFlashdata('fail', 'No se anexo la hoja de ruta.');
        }        
        return redirect()->to($this->controlador.'buscador_sincobol');
    }


    public function descargar($id){

        $db = \Config\Database::connect();
        $campos = array(
            'tdoc.plantilla', 'tdoc.nombre as tipo_documento', 'doc.correlativo as doc_correlativo', 'doc.ciudad as doc_ciudad', "TO_CHAR(doc.fecha, 'DD/MM/YYYY') as doc_fecha", 'doc.referencia as doc_referencia',
            'u.nombre_completo as usuario', 'p.nombre as cargo', 'doc.fk_tramite', 'doc.fk_acto_administrativo', 'doc.fk_hoja_ruta'
        );
        $where = array(
            //'doc.fk_usuario_creador' => session()->get('registroUser'),
            'doc.id' => $id,
        );
        $builder = $db->table('public.documentos as doc')
        ->select($campos)
        ->join('public.tipo_documento as tdoc', 'doc.fk_tipo_documento = tdoc.id', 'left')
        ->join('public.usuarios as u', 'doc.fk_usuario_creador = u.id', 'left')
        ->join('public.perfiles as p', 'u.fk_perfil = p.id', 'left')
        ->where($where);
        if($documento = $builder->get()->getFirstRow('array')){
            $tramite = $this->tramitesMenu($documento['fk_tramite']);
            switch($tramite['controlador']){
                case 'cam/':
                    $campos = array(
                        'cam.fk_tipo_hoja_ruta','cam.fk_area_minera', 'cam.fk_hoja_ruta', 'cam.fk_solicitud_licencia_contrato', 'cam.correlativo as hr_madre', "TO_CHAR(cam.fecha_mecanizada, 'DD/MM/YYYY') AS fecha_mecanizada",
                        'dam.codigo_unico', 'dam.denominacion', 'dam.extension', 'dam.titular', 'dam.clasificacion_titular', 'dam.regional', 'dam.departamentos', 'dam.provincias', 'dam.municipios',
                    );
                    $where = array(
                        'cam.id' => $documento['fk_acto_administrativo'],
                    );
                    $builder = $db->table('public.acto_administrativo as cam')
                    ->select($campos)
                    ->join('public.datos_area_minera as dam', 'cam.id = dam.fk_acto_administrativo', 'left')
                    ->where($where);
                    if($fila = $builder->get()->getRowArray()){
                        $file_name = str_replace('/','-',$documento['doc_correlativo']).'.docx';
                        $template = base_url('archivos/tipo_documento/'.$documento['plantilla']);
                        $plantillaWord = new TemplateProcessor($template);
                        $plantillaWord->setImageValue('qr', array('path' => $this->generarQRCam($documento,$fila), 'width' => '2cm', 'height' => '2cm', 'ratio' => true));
                        $plantillaWord->setValue('correlativo', $documento['doc_correlativo']);
                        $plantillaWord->setValue('fecha', $documento['doc_ciudad'].', '. $this->formatoFecha($documento['doc_fecha']));
                        $plantillaWord->setValue('referencia', $documento['doc_referencia']);
                        $plantillaWord->setValue('fecha_mecanizada', $this->formatoFecha($fila['fecha_mecanizada']));
                        $plantillaWord->setValue('codigo_unico', $fila['codigo_unico']);
                        $plantillaWord->setValue('denominacion', $fila['denominacion']);
                        $plantillaWord->setValue('extension', $fila['extension']);
                        $plantillaWord->setValue('departamentos', $fila['departamentos']);
                        $plantillaWord->setValue('provincias', $fila['provincias']);
                        $plantillaWord->setValue('municipios', $fila['municipios']);
                        $plantillaWord->setValue('titular', $fila['titular']);
                        $plantillaWord->setValue('hr_madre', $fila['hr_madre']);
                        $plantillaWord->setValue('iniciales', $this->getIniciales($documento['usuario']));
                        switch($fila['fk_tipo_hoja_ruta']){
                            case 1:
                                $cal = $this->certificadoAreaLibre($fila['fk_solicitud_licencia_contrato']);
                                $plantillaWord->setValue('cal', $cal['correlativo']);
                                $plantillaWord->setValue('cal_fecha', $this->formatoFecha($cal['fecha_solicitud']));
                                break;
                            case 2:
                            case 3:
                                break;
                        }

                        $temp_file = tempnam(sys_get_temp_dir(), 'PHPWord');
                        $plantillaWord->saveAs($temp_file);
                        header("Content-Disposition: attachment; filename=$file_name");
                        readfile($temp_file);
                        @unlink($temp_file);
                        exit;
                    }
                    break;
                case 'mineria_ilegal/':
                    $where = array(
                        'hr.id' => $documento['fk_hoja_ruta'],
                    );
                    $builder = $db->table('mineria_ilegal.hoja_ruta AS hr')
                    ->select("*, hr.id as id_hoja_ruta, hr.correlativo as correlativo, d.correlativo as correlativo_formulario, d.created_at as fecha_denuncia")
                    ->join('mineria_ilegal.denuncias as d', 'hr.fk_denuncia = d.id', 'left')
                    ->where($where);
                    if($fila = $builder->get()->getRowArray()){
                        $file_name = str_replace('/','-',$documento['doc_correlativo']).'.docx';
                        $template = base_url('archivos/tipo_documento/'.$documento['plantilla']);
                        $plantillaWord = new TemplateProcessor($template);
                        $plantillaWord->setImageValue('qr', array('path' => $this->generarQRMineriaIlegal($documento, $fila), 'width' => '2cm', 'height' => '2cm', 'ratio' => true));
                        $plantillaWord->setValue('correlativo', $documento['doc_correlativo']);
                        $plantillaWord->setValue('fecha', $documento['doc_ciudad'].', '. $this->formatoFecha($documento['doc_fecha']));
                        $plantillaWord->setValue('referencia', $documento['doc_referencia']);
                        $plantillaWord->setValue('iniciales', $this->getIniciales($documento['usuario']));
                        $temp_file = tempnam(sys_get_temp_dir(), 'PHPWord');
                        $plantillaWord->saveAs($temp_file);
                        header("Content-Disposition: attachment; filename=$file_name");
                        readfile($temp_file);
                        @unlink($temp_file);
                        exit;
                    }
                    break;
            }
        }
    }

    private function generarQRCam($documento, $fila){
        $data = $fila['hr_madre'].'|'.$documento['tipo_documento'].'|CITE:'.$documento['doc_correlativo'].'|FECHA:'.$documento['doc_fecha'].'|CODIGO UNICO:'.$fila['codigo_unico'].
        '|DENOMINACION:'.$fila['denominacion'].'|APM:'.$fila['titular'].'|USUARIO:'.$documento['usuario'].' - '.$documento['cargo'];
        $result = Builder::create()
        ->writer(new PngWriter())
        ->writerOptions([])
        ->data($data)
        ->size(200)
        ->margin(1)
        ->labelText('')
        ->build();
        return $result->getDataUri();
    }
    private function generarQRMineriaIlegal($documento, $fila){
        $data = $fila['correlativo'].'|'.$documento['tipo_documento'].'|CITE:'.$documento['doc_correlativo'].'|FECHA:'.$documento['doc_fecha'].'|FORMULARIO MINERÍA ILEGAL:'.$fila['correlativo_formulario'].
        '|FECHA FORMULARIO:'.$fila['fecha_denuncia'].'|USUARIO:'.$documento['usuario'].' - '.$documento['cargo'];
        $result = Builder::create()
        ->writer(new PngWriter())
        ->writerOptions([])
        ->data($data)
        ->size(200)
        ->margin(1)
        ->labelText('')
        ->build();
        return $result->getDataUri();
    }

    private function formatoFecha($fecha){
        $tmpFecha = explode('/',$fecha);
        $formato = (int)$tmpFecha[0].' de '.$this->meses[(int)$tmpFecha[1]].' del '.$tmpFecha[2];
        return $formato;
    }

    private function certificadoAreaLibre($idCAL){
        $db = \Config\Database::connect('sincobol');
        $campos = array('cal.fk_area_minera', 'cal.correlativo', "TO_CHAR(cal.fecha_solicitud, 'DD/MM/YYYY') as fecha_solicitud", 'cal.lugar_solicitud', 'cal.tiene_solicitud');
        $where = array(
            'slc.id' => $idCAL
        );
        $builder = $db->table('contratos_licencias.solicitud_licencia_contrato as slc')
        ->select($campos)
        ->join('contratos_licencias.certificacion_area_libre as cal', 'slc.fk_certificacion_area_libre = cal.id', 'left')
        ->where($where);
        return $builder->get()->getRowArray();
    }

    private function getIniciales($nombre){
        $name = '';
        $explode = explode(' ',$nombre);
        foreach($explode as $x){
            $name .=  $x[0];
        }
        return $name;
    }

    public function ajaxDocumentos(){
        $cadena = mb_strtoupper($this->request->getPost('texto'));
        $id_acto_administrativo = $this->request->getPost('id_acto_administrativo');
        $data = array();
        $data[] = array(
            'id' => '',
            'text' => 'No se encuentra ningún documento generado para este trámite.'
        );
        if(!empty($cadena) && !empty($id_acto_administrativo)){
            $db = \Config\Database::connect();
            $campos = array('d.id', "CONCAT(td.nombre,': ',d.correlativo,' del ',TO_CHAR(fecha, 'DD/MM/YYYY')) as text");
            $where = array(
                'd.fk_usuario_creador' => session()->get('registroUser'),
                'd.fk_derivacion' => NULL,
                'd.estado' => 'SUELTO',
                'd.fk_acto_administrativo' => $id_acto_administrativo,
            );
            $builder = $db->table('public.documentos AS d')
            ->select($campos)
            ->join('public.tipo_documento AS td', 'd.fk_tipo_documento = td.id', 'left')
            ->where($where)
            ->like("CONCAT(td.nombre,': ',d.correlativo)", $cadena)
            ->orderBY('d.id', 'DESC');
            if($resultado = $builder->get()->getResult('array')){
                $data = array();
                foreach($resultado as $row)
                    $data[] = array(
                        'id' => $row['id'],
                        'text' => $row['text'],
                    );
            }
        }
        echo json_encode($data);
    }

    public function ajaxDatosDocumento(){
        $id_documento = $this->request->getPost('id_documento');
        if(!empty($id_documento) && $id_documento > 0){
            $db = \Config\Database::connect();
            $campos = array(
                'doc.id', 'doc.correlativo', "TO_CHAR(doc.fecha, 'DD/MM/YYYY') as fecha", 'td.nombre as tipo_documento', 'td.notificacion'
            );
            $where = array(
                'doc.id' => $id_documento,
                'doc.estado' => 'SUELTO',
            );
            $query = $db->table('documentos AS doc')
            ->select($campos)
            ->join('tipo_documento AS td', 'doc.fk_tipo_documento = td.id', 'left')
            ->where($where);
            $data = $query->get()->getFirstRow('array');
            echo json_encode($data);
        }
    }
    public function ajaxDocumentosMineriaIlegal(){
        $cadena = mb_strtoupper($this->request->getPost('texto'));
        $id_hoja_ruta = $this->request->getPost('id_hoja_ruta');
        $data = array();
        $data[] = array(
            'id' => '',
            'text' => 'No se encuentra ningún documento generado.'
        );
        if(!empty($cadena) && !empty($id_hoja_ruta)){
            $db = \Config\Database::connect();
            $campos = array('d.id', "CONCAT(td.nombre,': ',d.correlativo,' del ',TO_CHAR(fecha, 'DD/MM/YYYY')) as text");
            $where = array(
                'd.fk_usuario_creador' => session()->get('registroUser'),
                'd.fk_derivacion' => NULL,
                'd.estado' => 'SUELTO',
                'd.fk_hoja_ruta' => $id_hoja_ruta,
            );
            $builder = $db->table('public.documentos AS d')
            ->select($campos)
            ->join('public.tipo_documento AS td', 'd.fk_tipo_documento = td.id', 'left')
            ->where($where)
            ->like("CONCAT(td.nombre,': ',d.correlativo)", $cadena)
            ->orderBY('d.id', 'DESC');
            if($resultado = $builder->get()->getResult('array')){
                $data = array();
                foreach($resultado as $row)
                    $data[] = array(
                        'id' => $row['id'],
                        'text' => $row['text'],
                    );
            }
        }
        echo json_encode($data);
    }


    private function tipoDocumentoReporte($idTramite){
        $tiposDocumentosModel = new TipoDocumentoModel();
        $tiposDocumentos = $tiposDocumentosModel->orderBy('nombre', 'ASC')->findAll();
        $temporal = array();
        $temporal[''] = 'SELECCIONE EL TIPO DE DOCUMENTO';
        if($tiposDocumentos){
            foreach($tiposDocumentos as $row){
                $tramites = explode(',', $row['tramites']);
                if( in_array($idTramite, $tramites) )
                    $temporal[$row['id']] = $row['nombre'];
            }
        }
        return $temporal;
    }

    public function reporte($idTramite)
    {
        $tramiteModel = new TramitesModel();
        if($tramite = $tramiteModel->find($idTramite)){
            $tipos_documentos = $this->tipoDocumentoReporte($tramite['id']);
            if ($this->request->getPost()) {
                $validation = $this->validate([
                    'fecha_inicio' => [
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'La Fecha Inicio es obligatorio.',
                        ]
                    ],
                    'fecha_fin' => [
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'La Fecha Fin es obligatorio.',
                        ]
                    ],
                    'id_tipo_documento' => [
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'Debe seleccionar una Tipo de Documento.',
                        ]
                    ],
                ]);
                if(!$validation){
                    $contenido['validation'] = $this->validator;
                }else{
                    $db = \Config\Database::connect();
                    $campos_listar=array(
                        'Fecha', 'Correlativo', 'Referencia', 'Fecha Notificación', 'Usuario', 'Cargo', 'Oficina', 'Estado', 'Motivo Anulación', 'H.R. Madre','Código Único','Denominación'
                    );
                    $campos_reales=array(
                        'fecha','correlativo','referencia', 'fecha_notificacion', 'nombre_completo','cargo','oficina','estado','motivo_anulacion','hr_madre','codigo_unico','denominacion',
                    );
                    $campos = array("to_char(d.fecha, 'DD/MM/YYYY') as fecha", 'd.correlativo', 'd.referencia', "to_char(d.fecha_notificacion, 'DD/MM/YYYY') as fecha_notificacion", 'u.nombre_completo', 'p.nombre as cargo', 'o.nombre as oficina', 'd.motivo_anulacion', 'd.estado', 'dam.codigo_unico', 'dam.denominacion', 'ad.correlativo as hr_madre');
                    $where = array(
                        'd.fk_tipo_documento' => $this->request->getPost('id_tipo_documento'),
                        'd.fk_tramite' => $idTramite,
                        //'ad.fk_oficina' => session()->get('registroOficina'),
                        "d.fecha >=" => $this->request->getPost('fecha_inicio'),
                        "d.fecha <=" => $this->request->getPost('fecha_fin')
                    );
                    $builder = $db->table('public.documentos as d')
                    ->select($campos)
                    ->join('public.acto_administrativo as ad', 'd.fk_acto_administrativo = ad.id', 'left')
                    ->join('public.datos_area_minera as dam', 'ad.id = dam.fk_acto_administrativo', 'left')
                    ->join('public.usuarios as u', 'd.fk_usuario_creador = u.id', 'left')
                    ->join('public.perfiles as p', 'u.fk_perfil = p.id', 'left')
                    ->join('public.oficinas as o', 'u.fk_oficina = o.id', 'left')
                    ->where($where)
                    ->orderBY('d.created_at', 'ASC');
                    $datos = $builder->get()->getResultArray();
                    $contenido['datos'] = $datos;
                    $contenido['campos_listar'] = $campos_listar;
                    $contenido['campos_reales'] = $campos_reales;

                    if ($this->request->getPost() && $this->request->getPost('enviar')=='excel') {
                        $this->exportarReporte($campos_listar, $campos_reales, $datos, $tipos_documentos[$this->request->getPost('id_tipo_documento')], $this->request->getPost('fecha_inicio'), $this->request->getPost('fecha_fin'));
                    }

                }
            }

            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Reporte Documentos Generados';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['tipos_documentos'] = $tipos_documentos;
            $contenido['subtitulo'] = 'Reporte Documentos Generados';
            $contenido['accion'] = $this->controlador.'reporte/'.$tramite['id'];
            $contenido['controlador'] = $this->controlador;
            $data['content'] = view($this->carpeta.'reporte_generados', $contenido);
            $data['menu_actual'] = $tramite['controlador'].$this->controlador.'reporte';
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['alertas'] = $this->alertasTramites();
            echo view('templates/template', $data);
        }
    }

    public function exportarReporte($campos_listar, $campos_reales, $datos, $tipo_documento, $fecha_inicio, $fecha_fin){
        $file_name = 'reporte_documentos_generados-'.date('YmdHis').'.xlsx';
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setTitle("Reporte Documentos");
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
        $activeWorksheet->setCellValue('A'.$nColumnas, 'REPORTE - '.$tipo_documento);
        $activeWorksheet->mergeCells('A'.$nColumnas.':K'.$nColumnas);
        $activeWorksheet->getStyle('A'.$nColumnas.':K'.$nColumnas)->applyFromArray($styleHeader);
        $nColumnas++;
        $activeWorksheet->setCellValue('A'.$nColumnas, 'FECHA INICIO: '.$fecha_inicio.' AL '.$fecha_fin);
        $activeWorksheet->mergeCells('A'.$nColumnas.':K'.$nColumnas);
        $activeWorksheet->getStyle('A'.$nColumnas.':K'.$nColumnas)->applyFromArray($styleHeader);

        $nColumnas++;
        $activeWorksheet->fromArray($campos_listar,NULL,'A'.$nColumnas);
        $activeWorksheet->getStyle('A'.$nColumnas.':'.$activeWorksheet->getHighestColumn().$nColumnas)->applyFromArray($styleHeader);
        if($datos){
            $nColumnas++;
            foreach($datos as $fila){
                $data = array();
                foreach($campos_reales as $row)
                    $data[] = $fila[$row];
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

    public function reporteDocumentos($idTramite)
    {
        if($tramite = $this->tramitesMenu($idTramite)){
            $oficinaModel = new OficinasModel();
            $tmpOficinas = $oficinaModel->findAll();
            $oficinas = array('' => 'SELECCIONE UNA DIRECCIÓN');
            $usuarios = array('' => 'SELECCIONE UN USUARIO');
            foreach($tmpOficinas as $row)
                $oficinas[$row['id']] = $row['nombre'];
            $datos = array();
            $campos_listar=array();
            $campos_reales=array();
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
                    $where = array(
                        'doc.fk_tramite' => $idTramite,
                        'doc.fk_usuario_creador' => $usuario,
                    );
                    switch($tramite['controlador']){
                        case 'cam/':
                            $campos = array(
                                'doc.id', 'adm.fk_area_minera', 'doc.correlativo', "to_char(doc.fecha, 'DD/MM/YYYY') as fecha",
                                'doc.referencia', 'dam.codigo_unico', 'adm.correlativo as hoja_ruta', 'doc.estado',
                                'td.nombre as tipo_documento', 'doc.doc_digital', "CASE WHEN doc_digital IS NOT NULL THEN 'SI' ELSE 'NO' END as existe_digital"
                            );
                            $builder = $db->table('public.documentos as doc')
                            ->select($campos)
                            ->join('public.tipo_documento as td', 'doc.fk_tipo_documento = td.id', 'left')
                            ->join('public.acto_administrativo as adm', 'doc.fk_acto_administrativo = adm.id', 'left')
                            ->join('public.datos_area_minera as dam', 'adm.id = dam.fk_acto_administrativo', 'left')
                            ->where($where)
                            ->orderBY('doc.id', 'DESC');
                            $datos = $builder->get()->getResult('array');
                            $campos_listar=array(
                                'Estado','Fecha','Correlativo', 'Tipo Documento', 'Referencia','Código Único','H.R. Madre', 'Documento Digital'
                            );
                            $campos_reales=array(
                                'estado','fecha','correlativo', 'tipo_documento', 'referencia','codigo_unico','hoja_ruta', 'doc_digital'
                            );
                            if ($this->request->getPost('enviar')=='excel') {
                                $campos_listar_reporte=array(
                                    'Estado','Fecha','Correlativo', 'Tipo Documento', 'Referencia','Código Único','H.R. Madre', 'Documento Digital Cargado'
                                );
                                $campos_reales_reporte=array(
                                    'estado','fecha','correlativo', 'tipo_documento', 'referencia','codigo_unico','hoja_ruta', 'existe_digital'
                                );
                                helper('security');
                                $file_name = sanitize_filename(mb_strtolower($usuarios[$usuario])).' - documentos generados - '.date('YmdHis').'.xlsx';
                                $this->exportarXLS($campos_listar_reporte, $campos_reales_reporte, $datos, $file_name);
                            }
                            break;
                        case 'mineria_ilegal/':
                            $campos = array(
                                'doc.id', 'doc.correlativo', "to_char(doc.fecha, 'DD/MM/YYYY') as fecha", 'doc.referencia', 'hr.correlativo as hoja_ruta', 'doc.estado',
                                'td.nombre as tipo_documento', 'doc.doc_digital', "CASE WHEN doc_digital IS NOT NULL THEN 'SI' ELSE 'NO' END as existe_digital"
                            );
                            $builder = $db->table('documentos AS doc')
                            ->select($campos)
                            ->join('mineria_ilegal.hoja_ruta AS hr', 'doc.fk_hoja_ruta = hr.id', 'left')
                            ->join('mineria_ilegal.denuncias AS den', 'hr.fk_denuncia = den.id', 'left')
                            ->join('usuarios as u', 'doc.fk_usuario_creador = u.id', 'left')
                            ->join('perfiles as p', 'u.fk_perfil = p.id', 'left')
                            ->where($where)
                            ->orderBY('doc.id', 'DESC');
                            $datos = $builder->get()->getResult('array');
                            $campos_listar=array(
                                'Estado','Fecha','Correlativo','Referencia','H.R. Minería Ilegal','Documento Digital'
                            );
                            $campos_reales=array(
                                'estado','fecha','correlativo','referencia','hoja_ruta','doc_digital'
                            );
                            break;
                    }
                }
            }

            $this->menuActual = $tramite['controlador'].'reporte_documentos';
            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Reporte de Documentos Generados por Usuario';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['oficinas'] = $oficinas;
            $contenido['usuarios'] = $usuarios;
            $contenido['subtitulo'] = 'Reporte de Documentos Generados por Usuario';
            $contenido['datos'] = $datos;
            $contenido['campos_listar'] = $campos_listar;
            $contenido['campos_reales'] = $campos_reales;
            $contenido['controlador'] = $this->controlador;
            $contenido['accion'] = $this->controlador.'reporte_documentos/'.$idTramite;
            $contenido['id_tramite'] = $idTramite;
            $contenido['ruta_archivos'] = 'archivos/'.$tramite['controlador'];
            $data['content'] = view($this->carpeta.'reporte_documentos', $contenido);
            $data['menu_actual'] = $this->menuActual;
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['alertas'] = $this->alertasTramites();
            echo view('templates/template', $data);
        }
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
        ->orderBy('usuario','ASC');
        $resultado = array();
        if($tmpUsuarios = $builder->get()->getResultArray()){
            foreach($tmpUsuarios as $row)
                $resultado[$row['id']] = $row['usuario'];
        }
        return $resultado;
    }
    public function exportarXLS($campos_listar, $campos_reales, $datos, $file_name)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setTitle("Mis Tramites");
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

        $activeWorksheet->fromArray($campos_listar);
        $activeWorksheet->getStyle('A1:'.$activeWorksheet->getHighestColumn().'1')->applyFromArray($styleHeader);
        if($datos){
            $nColumnas = 2;
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

}