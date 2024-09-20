<?php

namespace App\Controllers;

use App\Libraries\HojaRutaPdf;
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
        /*$campos = array(
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
        ->whereIn('sdp.ultimo_estado',array('DERIVADO'))
        ->orderBY('sdp.id', 'ASC');
        $datos = $builder->get()->getResult('array');*/

        $campos_listar=array(
            'Estado','Fecha Ingreso','Días Pasados','Hoja de Ruta','Remitente','Destinatario','Instrucción','Documento Externo','Doc. Digital',
        );
        $campos_reales=array(
            'ultimo_estado','fecha_derivacion', 'dias', 'correlativo', 'remitente', 'destinatario', 'ultimo_instruccion', 'documento_externo', 'doc_digital'
        );

        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Listado de Licencias de Comercialización';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['datos'] = $datos;
        $contenido['campos_listar'] = $campos_listar;
        $contenido['campos_reales'] = $campos_reales;
        $contenido['subtitulo'] = 'Listado de Licencias de Comercialización';
        $contenido['controlador'] = $this->controlador;
        $contenido['id_tramite'] = $this->idTramite;
        $data['content'] = view($this->carpeta.'mis_ingresos', $contenido);
        $data['menu_actual'] = $this->menuActual.'mis_ingresos';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['alertas'] = $this->alertasTramites();
        echo view('templates/template', $data);
    }

    public function agregarVentanilla(){

        if ($this->request->getPost()) {
            $id_areas_mineras = $this->request->getPost('id_areas_mineras');
            $extensiones = $this->request->getPost('extensiones');
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
                $licenciaComercializacionHojaRutaModel = new LicenciaComercializacionHojaRutaModel();
                $licenciaComercializacionDocumentoExternoModel = new LicenciaComercializacionDocumentoExternoModel();
                $licenciaComercializacionDerivacionModel = new LicenciaComercializacionDerivacionModel();

                $estado = 'DERIVADO';
                $oficina = $oficinaModel->find(session()->get('registroOficina'));
                $correlativo = $this->obtenerCorrelativo($oficina['correlativo'].'LCOM/');
                $instruccion = 'LICENCIA DE COMERCIALIZACIÓN - '.$correlativo;

                $dataLC = array(
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
                            'fk_estado_tramite_padre' => 0,
                            'instruccion' => $instruccion,
                            'estado' => $estado,
                            'fk_usuario_responsable' => $this->request->getPost('fk_usuario_destinatario'),
                            'fk_usuario_remitente' => session()->get('registroUser'),
                            'fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                            'fk_usuario_creador' => session()->get('registroUser'),
                        );
                        if($licenciaComercializacionDerivacionModel->insert($dataDerivacion) === false){
                            session()->setFlashdata('fail', $licenciaComercializacionDerivacionModel->errors());
                        }else{
                            $this->actualizarCorrelativo($oficina['correlativo'].'LCOM/');
                            session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información. <code><a href="'.base_url($this->controlador.'hoja_ruta_solicitud_pdf/'.$idHR).'" target="_blank">Descargar Hoja de Ruta</a></code>');
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

    public function hojaRutaPdf($id_hoja_ruta){
        $licenciaComercializacionHojaRutaModel = new LicenciaComercializacionHojaRutaModel();
        $campos = array(
            'id','fk_oficina','fk_usuario_creador','fk_usuario_destino',"to_char(created_at, 'DD/MM/YYYY HH24:MI') as fecha_creacion","correlativo",
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
            $pdf->MultiCell(50,5, $hoja_ruta['fecha_creacion'], 1, 'C', false, 1, '', '', true, 0, false, false, 5, 'M', true);
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