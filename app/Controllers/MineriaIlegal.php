<?php

namespace App\Controllers;

use App\Libraries\DenunciaPdf;
use App\Libraries\FormularioMineriaIlegalPdf;
use App\Libraries\HojaRutaPdf;
use App\Models\EstadoTramiteModel;
use App\Models\TipoSolicitudModel;
use App\Models\ActoAdministrativoModel;
use App\Models\ActoRegistradoModel;
use App\Models\AdjuntosMineriaIlegalModel;
use App\Models\CarpetaArchivoSincobolModel;
use App\Models\CoordenadasMineriaIlegalModel;
use App\Models\CoordenadasWebMineriaIlegalModel;
use App\Models\CorrelativosMineriaIlegalModel;
use App\Models\CorrespondenciaExternaModel;
use App\Models\DenunciantesMineriaIlegalModel;
use App\Models\DenunciasAreasMinerasMineriaIlegalModel;
use App\Models\DenunciasMineriaIlegalModel;
use App\Models\DenunciasDenunciantesMineriaIlegalModel;
use App\Models\DenunciasHrSincobolMineriaIlegalModel;
use App\Models\DenunciasWebMineriaIlegalModel;
use App\Models\DerivacionMineriaIlegalModel;
use App\Models\DerivacionModel;
use App\Models\DerivacionSincobolModel;
use App\Models\DocumentosModel;
use App\Models\HojaRutaMineriaIlegalModel;
use App\Models\HojaRutaSisegModel;
use App\Models\HojasRutaAnexadasMineriaIlegalModel;
use App\Models\HrAnexadasModel;
use App\Models\OficinasModel;
use App\Models\SolicitudLicenciaContratoModel;
use App\Models\TipoDocumentoExternoModel;
use App\Models\TipoDocumentoModel;
use App\Models\UsuariosModel;
use App\Models\MunicipiosModel;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MineriaIlegal extends BaseController
{
    protected $titulo = 'Míneria Ilegal';
    protected $controlador = 'mineria_ilegal/';
    protected $carpeta = 'mineria_ilegal/';
    protected $idTramite = 2;
    protected $menuActual = 'mineria_ilegal/';
    protected $rutaArchivos = 'archivos/mineria_ilegal/';
    protected $rutaDocumentos = 'archivos/mineria_ilegal/documentos/';
    protected $rutaArchivosDenunciante = 'archivos/mineria_ilegal/denunciante/';
    protected $urlSincobol = 'https://sincobol.autoridadminera.gob.bo/sincobol/';
    protected $alpha = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
    protected $tipoDenuncias = array(
        1 => 'PÁGINA WEB',
        2 => 'VENTANILLA ÚNICA',
        3 => 'VERIFICACIÓN DE OFICIO',
    );
    protected $fontPDF = 'helvetica';
    protected $acciones = array(
        'Para su conocimiento y consideración',
        'Verificar Requisitos',
        'Requerir Informe Técnico',
        'Preparar informe',
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
    protected $tiposAdjuntos = array(
        'IMAGEN' => 'IMAGEN',
        'DOCUMENTO' => 'DOCUMENTO',
        'VIDEO' => 'VIDEO',
        'AUDIO' => 'AUDIO',
    );
    protected $tiposOrigenOficio = array(
        'HOJA DE RUTA EXTERNA/INTERNA' => 'HOJA DE RUTA EXTERNA/INTERNA',
        'NOTICIA' => 'NOTICIA',
        'REDES SOCIALES' => 'REDES SOCIALES',
    );

    public function denunciasWeb()
    {
        $db = \Config\Database::connect();
        $campos = array('dw.id', "to_char(dw.created_at, 'DD/MM/YYYY HH24:MI') as fecha_denuncia", 'dw.correlativo', "CONCAT(dw.nombres, ' ', dw.apellidos) as nombre_completo", "CONCAT(dw.documento_identidad, ' ', dw.expedido) AS dni",
        'dw.telefonos', 'm.departamento', 'm.provincia', 'm.municipio', 'dw.comunidad_localidad', 'dw.estado');
        $where = array(
            'dw.deleted_at' => NULL,
            //'dw.estado' => 'PRESENTADO',
        );
        $builder = $db->table('mineria_ilegal.denuncias_web AS dw')
        ->select($campos)
        ->join('mineria_ilegal.municipios AS m', 'dw.fk_municipio = m.id', 'left')
        ->where($where)
        ->orderBY('dw.id', 'DESC');
        $datos = $builder->get()->getResult('array');
        $campos_listar=array(
            'Estado', 'Fecha Denuncia', 'Correlativo', 'Denunciante', 'Documento Identidad', 'Celular', 'Departamento', 'Provincia', 'Municipio', 'Comunidad/Localidad'
        );
        $campos_reales=array(
            'estado', 'fecha_denuncia','correlativo','nombre_completo', 'dni', 'telefonos', 'departamento', 'provincia', 'municipio', 'comunidad_localidad'
        );

        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Listado de Formularios de Denuncias de Minería Ilegal - WEB';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['datos'] = $datos;
        $contenido['campos_listar'] = $campos_listar;
        $contenido['campos_reales'] = $campos_reales;
        $contenido['subtitulo'] = 'Listado de Formularios de Denuncias de Minería Ilegal - WEB';
        $contenido['controlador'] = $this->controlador;
        $data['content'] = view($this->carpeta.'denuncias_web', $contenido);
        $data['menu_actual'] = $this->menuActual.'denuncias_web';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['alertas'] = $this->alertasTramites();
        echo view('templates/template', $data);
    }

    public function atenderDenunciaWeb($idDenunciaWeb){
        $denunciasWebMineriaIlegalModel = new DenunciasWebMineriaIlegalModel();
        if($denuncia = $denunciasWebMineriaIlegalModel->select("*, to_char(created_at, 'DD/MM/YYYY HH24:MI') as fecha_denuncia")->where(array('id' => $idDenunciaWeb, 'deleted_at' => NULL, 'estado' => 'PRESENTADO'))->first()){
            $municipiosModel = new MunicipiosModel();
            $coordenadasWebMineriaIlegal = new CoordenadasWebMineriaIlegalModel();
            $municipio = $municipiosModel->find($denuncia['fk_municipio']);
            $coordenadas = $coordenadasWebMineriaIlegal->where(array('fk_denuncia_web' => $denuncia['id']))->findAll();
            $provincias = $this->obtenerProvincias($municipio['departamento']);
            $municipios = $this->obtenerMunicipios($municipio['departamento'], $municipio['provincia']);
            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Atender Formulario de Denuncia de Minería Ilegal - WEB';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['subtitulo'] = 'Atender Formulario de Denuncia de Minería Ilegal - WEB';
            $contenido['accion'] = $this->controlador.'aprobar_denuncia_web';
            $contenido['accion_archivado'] = $this->controlador.'archivar_denuncia_web';
            $contenido['controlador'] = $this->controlador;
            $contenido['denuncia'] = $denuncia;
            $contenido['municipio'] = $municipio;
            $contenido['coordenadas'] = $this->transformarCoordenadas($coordenadas);
            $contenido['expedidos'] = $this->expedidos;
            $contenido['departamentos'] = array_merge(array(''=>'SELECCIONE EL DEPARTAMENTO'), $this->obtenerDepartamentos());
            $contenido['provincias'] = array_merge(array(''=>'SELECCIONE LA PROVINCIA'),$provincias);
            $contenido['municipios'] = array(''=>'SELECCIONE EL MUNICIPIO') + $municipios;
            $contenido['oficinas'] = array(''=>'SELECCIONE LA DIRECCIÓN DEPARTAMENTAL O REGIONAL') + $this->obtenerDireccionesRegionales();
            $data['content'] = view($this->carpeta.'atender_denuncia_web', $contenido);
            $data['menu_actual'] = $this->menuActual.'denuncias_web';
            $data['validacion_js'] = 'mineria-ilegal-denuncia-web-validation.js';
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['alertas'] = $this->alertasTramites();
            $data['mapas'] = true;
            $data['puntos'] = $coordenadas;
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'La denuncia no existe');
            return redirect()->to($this->controlador.'denuncias_web');
        }
    }

    public function aprobarDenunciaWeb(){
        if ($this->request->getPost()) {
            $validation = $this->validate([
                'id' => [
                    'rules' => 'required',
                ],
                'fk_oficina' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar la Dirección Departamental o Regional.',
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
                $contenido['usu_destinatario'] = $this->obtenerUsuario($this->request->getPost('fk_usuario_destinatario'));
                $contenido['validation'] = $this->validator;
            }else{
                $denunciasWebMineriaIlegalModel = new DenunciasWebMineriaIlegalModel();
                $idDenunciaWeb = $this->request->getPost('id');
                if($denunciaWeb = $denunciasWebMineriaIlegalModel->where(array('id' => $idDenunciaWeb, 'deleted_at' => NULL, 'estado' => 'PRESENTADO'))->first()){
                    $oficinaModel = new OficinasModel();
                    $municipiosModel = new MunicipiosModel();
                    $denunciasMineriaIlegalModel = new DenunciasMineriaIlegalModel();
                    $denunciantesMineriaIlegalModel = new DenunciantesMineriaIlegalModel();
                    $denunciasDenunciantesMineriaIleglaModel = new DenunciasDenunciantesMineriaIlegalModel();
                    $coordenadasMineriaIlegalModel = new CoordenadasMineriaIlegalModel();
                    $adjuntosMineriaIlegalModel = new AdjuntosMineriaIlegalModel();
                    $estadoTramiteModel = new EstadoTramiteModel();
                    $hojaRutaMineriaIlegalModel = new HojaRutaMineriaIlegalModel();
                    $derivacionMineriaIlegalModel = new DerivacionMineriaIlegalModel();
                    $ubicacion = $municipiosModel->find($denunciaWeb['fk_municipio']);
                    $oficinaDepartamento = $oficinaModel->like('departamentos_atencion', $ubicacion['departamento'])->first();
                    $correlativoDenuncia = $this->obtenerCorrelativo($oficinaDepartamento['correlativo'].'FMI/');
                    $oficina = $oficinaModel->find($this->request->getPost('fk_oficina'));
                    $correlativoHR = $this->obtenerCorrelativo($oficina['correlativo'].'MIN-ILEGAL/');
                    $data = array(
                        'fk_municipio' => $denunciaWeb['fk_municipio'],
                        'fk_tipo_denuncia' => 1,
                        'correlativo' => $correlativoDenuncia,
                        'comunidad_localidad' => $denunciaWeb['comunidad_localidad'],
                        'descripcion_lugar' => $denunciaWeb['descripcion_lugar'],
                        'autores' => $denunciaWeb['autores'],
                        'persona_juridica' => $denunciaWeb['persona_juridica'],
                        'descripcion_materiales' => $denunciaWeb['descripcion_materiales'],
                        'areas_denunciadas' => $denunciaWeb['areas_denunciadas'],
                        'fk_usuario_creador' => session()->get('registroUser'),
                        'departamento' => $ubicacion['departamento'],
                        'provincia' => $ubicacion['provincia'],
                        'municipio' => $ubicacion['municipio'],
                        'fecha_denuncia' => $denunciaWeb['created_at'],
                    );
                    if($denunciasMineriaIlegalModel->insert($data) === false){
                        session()->setFlashdata('fail', $denunciasMineriaIlegalModel->errors());
                    }else{
                        $idDenuncia = $denunciasMineriaIlegalModel->getInsertID();
                        $dataDenunciante = array(
                            'nombres' => $denunciaWeb['nombres'],
                            'apellidos' => $denunciaWeb['apellidos'],
                            'documento_identidad' => $denunciaWeb['documento_identidad'],
                            'expedido' => $denunciaWeb['expedido'],
                            'telefonos' => $denunciaWeb['telefonos'],
                            'direccion' => $denunciaWeb['direccion'],
                            'email' => $denunciaWeb['email'],
                            'documento_identidad_digital' => $denunciaWeb['documento_identidad_digital'],
                            'fk_usuario_creador' => session()->get('registroUser'),
                        );
                        if($denunciantesMineriaIlegalModel->insert($dataDenunciante) === false){
                            session()->setFlashdata('fail', $denunciantesMineriaIlegalModel->errors());
                        }else{
                            $idDenunciante = $denunciantesMineriaIlegalModel->getInsertID();
                            $dataDenunciaDenunciante = array(
                                'fk_denuncia' => $idDenuncia,
                                'fk_denunciante' => $idDenunciante,
                            );
                            if($denunciasDenunciantesMineriaIleglaModel->insert($dataDenunciaDenunciante) === false){
                                session()->setFlashdata('fail', $denunciasDenunciantesMineriaIleglaModel->errors());
                            }
                            $coordenadas = $this->obtenerCoordenadas($this->request->getPost('coordenadas'));
                            if(count($coordenadas)>0){
                                foreach($coordenadas as $coordenada){
                                    $dataCoordenada = array(
                                        'fk_denuncia' => $idDenuncia,
                                        'latitud' => $coordenada['latitud'],
                                        'longitud' => $coordenada['longitud'],
                                    );
                                    if($coordenadasMineriaIlegalModel->insert($dataCoordenada) === false){
                                        session()->setFlashdata('fail', $coordenadasMineriaIlegalModel->errors());
                                    }
                                }
                            }

                            $dataAdjunto = array(
                                'fk_denuncia' => $idDenuncia,
                                'nombre' => 'Fotografía 1',
                                'tipo' => 'IMAGEN',
                                'adjunto' => $denunciaWeb['fotografia_uno'],
                            );
                            if($adjuntosMineriaIlegalModel->insert($dataAdjunto) === false){
                                session()->setFlashdata('fail', $adjuntosMineriaIlegalModel->errors());
                            }

                            if($denunciaWeb['fotografia_dos']){
                                $dataAdjunto = array(
                                    'fk_denuncia' => $idDenuncia,
                                    'nombre' => 'Fotografía 2',
                                    'tipo' => 'IMAGEN',
                                    'adjunto' => $denunciaWeb['fotografia_dos'],
                                );
                                if($adjuntosMineriaIlegalModel->insert($dataAdjunto) === false){
                                    session()->setFlashdata('fail', $adjuntosMineriaIlegalModel->errors());
                                }
                            }

                            if($denunciaWeb['fotografia_tres']){
                                $dataAdjunto = array(
                                    'fk_denuncia' => $idDenuncia,
                                    'nombre' => 'Fotografía 3',
                                    'tipo' => 'IMAGEN',
                                    'adjunto' => $denunciaWeb['fotografia_tres'],
                                );
                                if($adjuntosMineriaIlegalModel->insert($dataAdjunto) === false){
                                    session()->setFlashdata('fail', $adjuntosMineriaIlegalModel->errors());
                                }
                            }

                            $where = array('deleted_at' => NULL, 'fk_estado_padre' => NULL, 'fk_tramite' =>$this->idTramite);
                            $primerEstado = $estadoTramiteModel->where($where)->orderBy('orden', 'ASC')->first();
                            $estado = 'DERIVADO';
                            $dataHojaRuta = array(
                                'fk_denuncia' => $idDenuncia,
                                'fk_oficina' => $oficina['id'],
                                'correlativo' => $correlativoHR,
                                'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                                'ultimo_estado' => $estado,
                                'ultimo_fk_estado_tramite_padre' => $primerEstado['id'],
                                'ultimo_instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                                'fk_usuario_actual' => session()->get('registroUser'),
                                'ultimo_fk_usuario_responsable' => $this->request->getPost('fk_usuario_destinatario'),
                                'ultimo_fk_usuario_remitente' => session()->get('registroUser'),
                                'ultimo_fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                                'fk_usuario_creador' => session()->get('registroUser'),
                                'fk_usuario_destino' => $this->request->getPost('fk_usuario_destinatario'),
                                'fecha_hoja_ruta' => date('Y-m-d H:i:s'),
                            );
                            if($hojaRutaMineriaIlegalModel->insert($dataHojaRuta) === false){
                                session()->setFlashdata('fail', $hojaRutaMineriaIlegalModel->errors());
                            }else{
                                $idHojaRuta = $hojaRutaMineriaIlegalModel->getInsertID();
                                $dataDerivacion = array(
                                    'fk_hoja_ruta' => $idHojaRuta,
                                    'fk_estado_tramite_padre' => $primerEstado['id'],
                                    'instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                                    'estado' => $estado,
                                    'fk_usuario_responsable' => $this->request->getPost('fk_usuario_destinatario'),
                                    'fk_usuario_remitente' => session()->get('registroUser'),
                                    'fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                                    'fk_usuario_creador' => session()->get('registroUser'),
                                );
                                if($derivacionMineriaIlegalModel->insert($dataDerivacion) === false){
                                    session()->setFlashdata('fail', $derivacionMineriaIlegalModel->errors());
                                }else{

                                    $dataDenunciaWeb = array(
                                        'id' => $denunciaWeb['id'],
                                        'estado' => 'PROCESADO',
                                        'fk_usuario_atencion' => session()->get('registroUser'),
                                        'fk_denuncia' => $idDenuncia,
                                    );

                                    if($denunciasWebMineriaIlegalModel->save($dataDenunciaWeb) === false){
                                        session()->setFlashdata('fail', $denunciasWebMineriaIlegalModel->errors());
                                    }else{
                                        $this->actualizarCorrelativo($oficinaDepartamento['correlativo'].'FMI/');
                                        $this->actualizarCorrelativo($oficina['correlativo'].'MIN-ILEGAL/');
                                        session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información. <code><a href="'.base_url($this->controlador.'formulario_denuncia_pdf/'.$idDenuncia).'" target="_blank">Descargar Formulario de Denuncia</a></code>  <code><a href="'.base_url($this->controlador.'hoja_ruta_pdf/'.$idHojaRuta).'" target="_blank">Descargar Hoja de Ruta</a></code>');
                                    }
                                }
                            }
                        }
                    }
                }
                return redirect()->to($this->controlador.'mis_tramites');
            }
        }
    }

    public function archivarDenunciaWeb(){
        if ($this->request->getPost()) {
            $validation = $this->validate([
                'id_denuncia' => [
                    'rules' => 'required',
                ],
                'motivo_archivado' => [
                    'rules' => 'required',
                ],
            ]);
            if(!$validation){
                session()->setFlashdata('fail', 'No se pudo rechazar el formulario de denuncia de minería ilegal.');
            }else{
                $denunciasWebMineriaIlegalModel = new DenunciasWebMineriaIlegalModel();
                $idDenunciaWeb = $this->request->getPost('id_denuncia');
                if($denunciaWeb = $denunciasWebMineriaIlegalModel->where(array('id' => $idDenunciaWeb, 'deleted_at' => NULL, 'estado' => 'PRESENTADO'))->first()){
                    $dataDenunciaWeb = array(
                        'id' => $denunciaWeb['id'],
                        'estado' => 'ARCHIVADO',
                        'motivo_archivado' => mb_strtoupper($this->request->getPost('motivo_archivado')),
                        'fk_usuario_archivado' => session()->get('registroUser'),
                    );
                    if($denunciasWebMineriaIlegalModel->save($dataDenunciaWeb) === false)
                        session()->setFlashdata('fail', $denunciasWebMineriaIlegalModel->errors());
                    else
                        session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información.');
                }
            }
            return redirect()->to($this->controlador.'denuncias_web');
        }
    }

    private function transformarCoordenadas($coordenadas){
        $resultado = '';
        if(count($coordenadas) > 0){
            foreach($coordenadas as $coordenada)
                $resultado .= '('.$coordenada['latitud'].'|'.$coordenada['longitud'].')';
        }
        return $resultado;
    }

    private function obtenerCoordenadas($coordenadas){
        $resultado = array();
        $tmp = array_diff(explode(')',$coordenadas), array(""));
        if($tmp){
            foreach($tmp as $row){
                $coordenadas = explode('|',substr($row, 1));
                $resultado[] = array(
                    'latitud' => $coordenadas[0],
                    'longitud' => $coordenadas[1],
                );
            }
        }
        return $resultado;
    }

    private function vaciarCoordenadas($fk_denuncia){
        $coordenadasMineriaIlegalModel = new CoordenadasMineriaIlegalModel();
        $where = array('fk_denuncia' => $fk_denuncia);
        if($coordenadas = $coordenadasMineriaIlegalModel->where($where)->findAll()){
            return $coordenadasMineriaIlegalModel->where($where)->delete();
        }
        return false;
    }



    public function hojaRutaPdf($id_hoja_ruta){
        $hojaRutaMineriaIlegalModel = new HojaRutaMineriaIlegalModel();
        $campos = array(
            'fk_denuncia','correlativo',"to_char(fecha_hoja_ruta, 'DD/MM/YYYY HH24:MI') as fecha", 'fk_usuario_creador', 'fk_usuario_destino', 'fojas', 'adjuntos'
        );
        if($hoja_ruta = $hojaRutaMineriaIlegalModel->select($campos)->find($id_hoja_ruta)){
            $db = \Config\Database::connect();
            $denunciasMineriaIlegalModel = new DenunciasMineriaIlegalModel();
            $usuarioCreador = $this->obtenerUsuario($hoja_ruta['fk_usuario_creador']);
            $usuarioDestino = $this->obtenerUsuarioHR($hoja_ruta['fk_usuario_destino']);
            $campos = array(
                'id','correlativo',"to_char(fecha_denuncia, 'DD/MM/YYYY HH24:MI') as fecha", 'fk_tipo_denuncia', 'origen_oficio', 'descripcion_oficio'
            );
            $denuncia = $denunciasMineriaIlegalModel->select($campos)->find($hoja_ruta['fk_denuncia']);

            $campos = array("CONCAT(d.nombres,' ',d.apellidos) as denunciante", "CONCAT(d.documento_identidad,' ',d.expedido) as dni",'d.telefonos');
            $where = array(
                'dd.fk_denuncia' => $denuncia['id'],
            );
            $builder = $db->table('mineria_ilegal.denuncias_denunciantes AS dd')
            ->select($campos)
            ->join('mineria_ilegal.denunciantes as d', 'dd.fk_denunciante = d.id', 'left')
            ->where($where)
            ->orderBY('dd.id', 'ASC');
            $denunciantes = $builder->get()->getResult('array');
            $txt_denunciante = '';
            $txt_telefonos = '';
            foreach($denunciantes as $denunciante){
                $txt_denunciante .= $denunciante['denunciante'].', ';
                $txt_telefonos .= $denunciante['telefonos'].', ';
            }

            $file_name = str_replace('/','-',$hoja_ruta['correlativo']).'.pdf';
            $pdf = new HojaRutaPdf('P', 'mm', 'Letter', true, 'UTF-8', false);

            //
            $pdf->SetCreator('GARNET');
            $pdf->SetAuthor('Desarrollo de UTIC');
            $pdf->SetTitle('Hoja de Ruta Mineria Ilegal');
            $pdf->SetKeywords('Hoja, Ruta, Mineria, Ilegal');

            //establecer margenes
            $pdf->SetMargins(10, 5, 10);
            $pdf->SetAutoPageBreak(true, 5); //Margin botton
            $pdf->setFontSubsetting(false);

            $pdf->AddPage('P','Letter');
            $pdf->SetTextColor(0, 0, 0);
            $pdf->setCellPaddings(0, 1, 0, 0);
            $pdf->setCellMargins(0, 0, 0, 0);
            $pdf->SetFillColor(255, 255, 255);

            $pdf->setCellPadding(1);
            $pdf->Image('assets/images/hoja_ruta/logo_ajam.png', 13, 9, 30, 0);
            $pdf->MultiCell(35, 22, "", 1, 'C', 0, 0);
            $pdf->SetFont($this->fontPDF, 'B', 8);
            $pdf->MultiCell(111,5, 'AUTORIDAD JURISDICCIONAL ADMINISTRATIVA MINERA', 'TRL', 'C', true, 0);
            $pdf->MultiCell(50,5, 'FECHA Y HORA DE CREACIÓN', 1, 'C', true, 1);
            $pdf->setx(45);
            $pdf->SetFont($this->fontPDF, '', 10);
            $pdf->MultiCell(111,5, 'HOJA DE RUTA - MINERÍA ILEGAL', 'RL', 'C', true, 0);
            $pdf->SetFont($this->fontPDF, '', 8);
            $pdf->MultiCell(50,5, $hoja_ruta['fecha'], 1, 'C', false, 1, '', '', true, 0, false, false, 5, 'M', true); //cite
            $pdf->setx(45);
            $pdf->SetFont($this->fontPDF, 'B', 12);
            $pdf->MultiCell(111,10,  $hoja_ruta['correlativo'], 'LBR', 'C', true, 0, '', '', true, 0, false, false, 10, 'M');

            $pdf->ln();

            $pdf->SetFont($this->fontPDF, 'B', 8);
            $pdf->MultiCell(35, 5, "FORMULARIO:", 1, 'R', true, 0);
            $pdf->SetFont($this->fontPDF, '', 8);
            $pdf->MultiCell(111,5, $denuncia['correlativo'], 1, 'L', false, 0, '', '', true, 0, false, false, 5, 'M', true);
            $pdf->SetFont($this->fontPDF, 'B', 8);
            $pdf->MultiCell(50,5,  "FECHA Y HORA DEL FORMULARIO", 1, 'C', true, 1, '', '', true, 0, false, false, 5, 'M', true);
            $pdf->SetFont($this->fontPDF, 'B', 8);
            $pdf->MultiCell(35, 5, "TIPO:", 1, 'R', true, 0);
            $pdf->SetFont($this->fontPDF, '', 8);
            $pdf->MultiCell(111,5, $this->tipoDenuncias[$denuncia['fk_tipo_denuncia']], 1, 'L', false, 0, '', '', true, 0, false, false, 5, 'M', true);
            $pdf->SetFont($this->fontPDF, '', 8);
            $pdf->MultiCell(50,5, $denuncia['fecha'], 1, 'C', false, 1, '', '', true, 0, false, false, 5, 'M', true);

            switch($denuncia['fk_tipo_denuncia']){
                case 3:
                    $pdf->SetFont($this->fontPDF, 'B', 8);
                    $pdf->MultiCell(35, 5, "TIPO ORIGEN:", 1, 'R', true, 0);
                    $pdf->SetFont($this->fontPDF, '', 8);
                    $pdf->MultiCell(161,5, $denuncia['origen_oficio'], 1, 'L', false, 1, '', '', true, 0, false, false, 5, 'M', true);
                    $pdf->SetFont($this->fontPDF, 'B', 8);
                    $pdf->MultiCell(35, 5, "BREVE DESCRIPCIÓN", 1, 'R', true, 0);
                    $pdf->SetFont($this->fontPDF, '', 8);
                    $pdf->MultiCell(161,5, $denuncia['descripcion_oficio'], 1, 'L', false, 1, '', '', true, 0, false, false, 5, 'M', true);
                    break;
                default:
                    $pdf->SetFont($this->fontPDF, 'B', 8);
                    $pdf->MultiCell(35, 5, "DENUNCIANTE(S):", 1, 'R', true, 0);
                    $pdf->SetFont($this->fontPDF, '', 8);
                    $pdf->MultiCell(161,5, substr($txt_denunciante, 0, -2), 1, 'L', false, 1, '', '', true, 0, false, false, 5, 'M', true);
                    $pdf->SetFont($this->fontPDF, 'B', 8);
                    $pdf->MultiCell(35, 5, "TELÉFONO(S):", 1, 'R', true, 0);
                    $pdf->SetFont($this->fontPDF, '', 8);
                    $pdf->MultiCell(161,5, substr($txt_telefonos, 0, -2), 1, 'L', false, 1, '', '', true, 0, false, false, 5, 'M', true);
                    break;
            }


            $pdf->SetFont($this->fontPDF, 'B', 8);
            $pdf->MultiCell(35, 10, "AREA DESTINO:", 1, 'R', true, 0);
            $pdf->SetFont($this->fontPDF, '', 8);
            $pdf->MultiCell(76, 10, $usuarioDestino['oficina'], 1, 'L', false, 0, '', '', true, 0, false, false, 10, 'T', true);
            $pdf->SetFont($this->fontPDF, 'B', 8);
            $pdf->MultiCell(20,5, 'NOMBRE:', 1, 'R', true, 0, '', '', true, 0, false, false, 5, 'M', true);
            $pdf->SetFont($this->fontPDF, '', 8);
            $pdf->MultiCell(65,5, $usuarioDestino['usuario'], 1, 'L', false, 1, '', '', true, 0, false, false, 5, 'M', true);
            $pdf->setx(121);
            $pdf->SetFont($this->fontPDF, 'B', 8);
            $pdf->MultiCell(20,5, 'CARGO:', 1, 'R', true, 0, '', '', true, 0, false, false, 5, 'M', true);
            $pdf->SetFont($this->fontPDF, '', 8);
            $pdf->MultiCell(65,5, $usuarioDestino['cargo'], 1, 'L', false, 1, '', '', true, 0, false, false, 5, 'M', true);
            $pdf->SetFont($this->fontPDF, 'B', 8);
            $pdf->MultiCell(35,5, 'USUARIO CREADOR:', 1, 'R', true, 0, '', '', true, 0, false, false, 5, 'T', true);
            $pdf->SetFont($this->fontPDF, '', 8);
            $pdf->MultiCell(161,5, $usuarioCreador['nombre'], 1, 'L', false, 1, '', '', true, 0, false, false, 5, 'T', true);

            $this->crearDerivacion($pdf, $this->fontPDF, $this->acciones);
            $this->crearDerivacion($pdf, $this->fontPDF, $this->acciones);
            $this->crearDerivacion($pdf, $this->fontPDF, $this->acciones);

            $pdf->AddPage('P','Letter');

            $pdf->SetFont($this->fontPDF, 'B', 8);
            $pdf->MultiCell(55, 8, 'HOJA DE RUTA:', 1, 'R', true, 0, '', '', true, 0, false, false, 5, 'M', true);
            $pdf->MultiCell(141, 8, '', 1, 'L', false, 1, '', '', true, 0, false, false, 5, 'T', true);

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
        $pdf->ln(1);
        // FILA 1
        $pdf->SetFont($tipo_letra, 'B', 7);
        $pdf->MultiCell(55, 0, 'ACCIÓN', 1, 'C', true, 0);
        $pdf->MultiCell(40, 0, 'DESTINATARIO:', 1, 'R', true, 0);
        $pdf->MultiCell(101, 0, '', 1, 'C', false, 1);

        $count = 0;
        foreach($acciones as $accion) {
            $pdf->MultiCell(50, 0, $accion, 1, 'L', true, 0);
            $pdf->MultiCell(5, 0, '', 1, 'L', false, 0);
            $pdf->MultiCell(70, 0, '', 'R', 'L', false, 0);
            if(++$count == count($acciones) - 1) {
                $pdf->MultiCell(31, 0, 'SELLO Y FIRMA', 'RT', 'C', true, 0);
                $pdf->MultiCell(20, 0, 'FECHA', 1, 'C', true, 0);
                $pdf->MultiCell(20, 0, 'HORA', 1, 'C', true, 1);
            }
            else if($count == count($acciones)) {
                $pdf->MultiCell(31, 0, '', 'R', 'C', true, 0);
                $pdf->MultiCell(20, 0, '', 1, 'C', false, 0);
                $pdf->MultiCell(20, 0, '', 1, 'C', false, 1);
            }
            else {
                $pdf->MultiCell(71, 0, '', 'R', 'C', false, 1);
            }
        }
        $pdf->MultiCell(55, 0, 'COORDINAR CON:', 1, 'R', true, 0);
        $pdf->MultiCell(40, 0, '', 1, 'L', false, 0);
        $pdf->MultiCell(51, 0, 'CON COPIA A:', 1, 'R', true, 0);
        $pdf->MultiCell(50, 0, '', 1, 'C', false, 1);
    }

    public function formularioDenunciaPdf($id_denuncia){
        $denunciasMineriaIlegalModel = new DenunciasMineriaIlegalModel();
        $campos = array('*', "to_char(fecha_denuncia, 'DD/MM/YYYY HH24:MI') as fecha_denuncia",
        "CONCAT(informe_tecnico_numero, ' DE ', TO_CHAR(informe_tecnico_fecha, 'DD/MM/YYYY')) AS informe_tecnico");
        if($denuncia = $denunciasMineriaIlegalModel->select($campos)->find($id_denuncia)){
            $coordenadasMineriaIlegalModel = new CoordenadasMineriaIlegalModel();
            $denunciasHrSincobolMineriaIlegalModel = new DenunciasHrSincobolMineriaIlegalModel();
            $denunciasAreasMinerasMineriaIlegalModel = new DenunciasAreasMinerasMineriaIlegalModel();
            $adjuntosMineriaIlegalModel = new AdjuntosMineriaIlegalModel();

            if($idHojasRutasSincobol = $denunciasHrSincobolMineriaIlegalModel->where(array('fk_denuncia'=>$denuncia['id']))->findAll()){
                $hojas_ruta_sincobol = '';
                foreach($idHojasRutasSincobol as $idHojaRutaSincobol){
                    $tmp = $this->obtenerDatosHrInExSincobol($idHojaRutaSincobol['fk_hoja_ruta']);
                    $hojas_ruta_sincobol .= $tmp['correlativo'].', ';
                }
                $contenido['hojas_ruta_sincobol'] = substr($hojas_ruta_sincobol, 0, -2);
            }

            if($idAreasMineras = $denunciasAreasMinerasMineriaIlegalModel->where(array('fk_denuncia'=>$denuncia['id']))->findAll()){
                $areas_mineras = array();
                foreach($idAreasMineras as $idAreaMinera)
                    $areas_mineras[] = $this->obtenerDatosAreaMineraMineriaIlegal($idAreaMinera['fk_area_minera']);
                $contenido['areas_mineras'] = $areas_mineras;
            }

            $contenido['denuncia'] = $denuncia;
            $contenido['coordenadas'] = $coordenadasMineriaIlegalModel->where(array('fk_denuncia'=>$denuncia['id']))->findAll();
            $contenido['tipo_denuncias'] = $this->tipoDenuncias;
            $contenido['color'] = '#F7CECE';
            switch($denuncia['fk_tipo_denuncia']){
                case 3:
                    $html = view($this->carpeta.'pdf_formulario_denuncia_oficio', $contenido);
                    break;
                default:
                    $denunciantesMineriaIlegalModel = new DenunciantesMineriaIlegalModel();
                    $denunciasDenunciantesMineriaIleglaModel = new DenunciasDenunciantesMineriaIlegalModel();
                    $denunciaDenunciante = $denunciasDenunciantesMineriaIleglaModel->where(array('fk_denuncia'=>$denuncia['id']))->first();
                    $denunciante = $denunciantesMineriaIlegalModel->find($denunciaDenunciante['fk_denunciante']);
                    $contenido['denunciante'] = $denunciante;
                    $html = view($this->carpeta.'pdf_formulario_denuncia_denunciante', $contenido);
                    break;
            }
            $adjuntos = $adjuntosMineriaIlegalModel->where(array('fk_denuncia'=>$denuncia['id']))->findAll();
            $html_adjuntos = '';
            if(count($adjuntos)>0){
                $contenido['adjuntos'] = $adjuntos;
                $html_adjuntos = view($this->carpeta.'pdf_adjuntos', $contenido);
            }

            $file_name = str_replace('/','-',$denuncia['correlativo']).'.pdf';
            $pdf = new FormularioMineriaIlegalPdf('P', 'mm', 'Letter', true, 'UTF-8', false);

            $pdf->SetCreator('GARNET');
            $pdf->SetAuthor('Desarrollo de UTIC');
            $pdf->SetTitle('Formulario de Mineria Ilegal');
            $pdf->SetKeywords('Mineria, Ilegal');

            //establecer margenes
            $pdf->SetMargins(10, 30, 10);
            $pdf->SetAutoPageBreak(true, 35); //Margin button

            $pdf->AddPage('P','Letter');
            // Titulo de paginas
            $pdf->SetFont($this->fontPDF, 'B', 14);
            $pdf->Cell(0,0,'FORMULARIO DE MINERÍA ILEGAL',0,0,'C');
            $pdf->Ln();
            $pdf->SetFont($this->fontPDF, 'B', 12);
            $pdf->Cell(0,0,$denuncia['correlativo'],0,0,'C');
            $pdf->SetFont($this->fontPDF, '', 8);
            $pdf->Ln(8);
            $pdf->writeHTML($html, true, false, false, false, '');

            if($html_adjuntos){
                $pdf->AddPage('P','Letter');
                // Titulo de paginas
                $pdf->SetFont($this->fontPDF, 'B', 14);
                $pdf->Cell(0,0,'FORMULARIO DE MINERÍA ILEGAL',0,0,'C');
                $pdf->Ln();
                $pdf->SetFont($this->fontPDF, 'B', 12);
                $pdf->Cell(0,0,$denuncia['correlativo'],0,0,'C');
                $pdf->SetFont($this->fontPDF, '', 8);
                $pdf->Ln(8);
                $pdf->writeHTML($html_adjuntos, true, false, false, false, '');
            }

            $pdf->Output($file_name);
            exit();
        }else{
            session()->setFlashdata('fail', 'No se ha encontrado la denuncia');
            return redirect()->to($this->controlador.'mis_tramites');
        }
    }

    public function misTramites()
    {
        $db = \Config\Database::connect();
        $campos = array('d.id as id_denuncia','hr.id as id_hoja_ruta','hr.ultimo_estado', "to_char(hr.ultimo_fecha_derivacion, 'DD/MM/YYYY') as fecha_derivacion", 'hr.correlativo as correlativo_hoja_ruta',
        "CONCAT(ures.nombre_completo,'<br><b>',pres.nombre,'<b>') as responsable", "CONCAT(urem.nombre_completo,'<br><b>',prem.nombre,'<b>') as remitente", "CONCAT(udes.nombre_completo,'<br><b>',pdes.nombre,'<b>') as destinatario",
        "hr.ultimo_instruccion, (CURRENT_DATE - hr.ultimo_fecha_derivacion::date) as dias",
        "to_char(d.created_at, 'DD/MM/YYYY') as fecha_denuncia", 'd.correlativo as correlativo_denuncia', 'd.fk_tipo_denuncia', 'd.departamento',
        "CASE WHEN hr.ultimo_fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'. ',etp.nombre,'<br>',etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE CONCAT(etp.orden,'. ',etp.nombre) END as estado_tramite",
        'hr.editar'
        );
        $where = array(
            'd.deleted_at' => NULL,
            'hr.fk_usuario_actual' => session()->get('registroUser'),
        );
        $builder = $db->table('mineria_ilegal.denuncias AS d')
        ->select($campos)
        ->join('mineria_ilegal.hoja_ruta AS hr', 'd.id = hr.fk_denuncia', 'left')
        ->join('usuarios as ures', 'hr.ultimo_fk_usuario_responsable = ures.id', 'left')
        ->join('perfiles as pres', 'ures.fk_perfil = pres.id', 'left')
        ->join('usuarios as urem', 'hr.ultimo_fk_usuario_remitente = urem.id', 'left')
        ->join('perfiles as prem', 'urem.fk_perfil = prem.id', 'left')
        ->join('usuarios as udes', 'hr.ultimo_fk_usuario_destinatario = udes.id', 'left')
        ->join('perfiles as pdes', 'udes.fk_perfil = pdes.id', 'left')
        ->join('estado_tramite as etp', 'hr.ultimo_fk_estado_tramite_padre = etp.id', 'left')
        ->join('estado_tramite as eth', 'hr.ultimo_fk_estado_tramite_hijo = eth.id', 'left')
        ->where($where)
        ->whereIn('hr.ultimo_estado',array('REGULARIZACIÓN', 'DERIVADO', 'RECIBIDO', 'EN ESPERA', 'DEVUELTO'))
        ->orderBY('d.id', 'DESC');
        $datos = $builder->get()->getResult('array');
        $datos = $this->obtenerDenunciantes($datos);
        $campos_listar=array(
            'Estado', 'Fecha', 'Días Pasados', 'Hoja de Ruta', 'Remitente', 'Destinatario', 'Instrucción', 'Responsable', 'Estado', 'Fecha Denuncia', 'Denuncia', 'Tipo',  'Denunciante', 'Departamento'
        );
        $campos_reales=array(
            'ultimo_estado','fecha_derivacion', 'dias', 'correlativo_hoja_ruta', 'remitente', 'destinatario', 'ultimo_instruccion', 'responsable', 'estado_tramite', 'fecha_denuncia', 'correlativo_denuncia', 'fk_tipo_denuncia', 'denunciante', 'departamento'
        );

        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Listado de H.R. Minería Ilegal';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['datos'] = $datos;
        $contenido['campos_listar'] = $campos_listar;
        $contenido['campos_reales'] = $campos_reales;
        $contenido['subtitulo'] = 'Listado de H.R. Minería Ilegal';
        $contenido['controlador'] = $this->controlador;
        $contenido['id_tramite'] = $this->idTramite;
        $contenido['tipo_denuncias'] = $this->tipoDenuncias;
        $data['content'] = view($this->carpeta.'index', $contenido);
        $data['menu_actual'] = $this->menuActual.'mis_tramites';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['alertas'] = $this->alertasTramites();
        echo view('templates/template', $data);
    }

    private function obtenerDenunciantes($datos){
        if(count($datos)>0){
            $db = \Config\Database::connect();
            foreach($datos as $i => $row){
                $txt_denunciante = '';
                $campos = array("CONCAT(de.nombres, ' ', de.apellidos,'<br>',de.documento_identidad,' ',de.expedido,'<br>',de.telefonos) as denunciante");
                $where = array(
                    'fk_denuncia' => $row['id_denuncia'],
                );
                $builder = $db->table('mineria_ilegal.denuncias_denunciantes AS dd')
                ->select($campos)
                ->join('mineria_ilegal.denunciantes AS de', 'dd.fk_denunciante = de.id', 'left')
                ->where($where)
                ->orderBY('dd.id', 'DESC');
                if( $denunciantes = $builder->get()->getResultArray() ){
                    foreach($denunciantes as $denunciante)
                        $txt_denunciante .= $denunciante['denunciante'].'<br>';
                }
                $datos[$i]['denunciante'] = $txt_denunciante;
            }
        }
        return $datos;
    }

    public function agregarVentanilla(){
        $provincias = array();
        $municipios = array();
        if ($this->request->getPost()) {
            $denunciantesMineriaIlegalModel = new DenunciantesMineriaIlegalModel();
            $validation = $this->validate([
                'denunciantes_anexados' => [
                    'rules' => 'required',
                ],
                'fk_municipio' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar el Municipio.',
                    ]
                ],
                'comunidad_localidad' => [
                    'rules' => 'required',
                ],
                'descripcion_lugar' => [
                    'rules' => 'required',
                ],
                'croquis_digital' => [
                    'uploaded[croquis_digital]',
                    'max_size[croquis_digital,20480]',
                ],
                'documento_digital' => [
                    'uploaded[documento_digital]',
                    'max_size[documento_digital,20480]',
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
                $provincias = $this->obtenerProvincias($this->request->getPost('departamento'));
                $municipios = $this->obtenerMunicipios($this->request->getPost('departamento'), $this->request->getPost('provincia'));
                $contenido['denunciantes'] = $denunciantesMineriaIlegalModel->whereIn('id', $this->request->getPost('id_denunciantes'))->findAll();
                $contenido['usu_destinatario'] = $this->obtenerUsuario($this->request->getPost('fk_usuario_destinatario'));
                $contenido['validation'] = $this->validator;
                $data['puntos'] = $this->obtenerCoordenadas($this->request->getPost('coordenadas'));
            }else{
                $oficinaModel = new OficinasModel();
                $municipiosModel = new MunicipiosModel();
                $denunciasMineriaIlegalModel = new DenunciasMineriaIlegalModel();
                $denunciasDenunciantesMineriaIleglaModel = new DenunciasDenunciantesMineriaIlegalModel();
                $adjuntosMineriaIlegalModel = new AdjuntosMineriaIlegalModel();
                $estadoTramiteModel = new EstadoTramiteModel();
                $hojaRutaMineriaIlegalModel = new HojaRutaMineriaIlegalModel();
                $derivacionMineriaIlegalModel = new DerivacionMineriaIlegalModel();
                $coordenadasMineriaIlegalModel = new CoordenadasMineriaIlegalModel();

                $ubicacion = $municipiosModel->find($this->request->getPost('fk_municipio'));
                $oficinaDepartamento = $oficinaModel->like('departamentos_atencion', $ubicacion['departamento'])->first();
                $correlativoDenuncia = $this->obtenerCorrelativo($oficinaDepartamento['correlativo'].'FMI/');
                $oficina = $oficinaModel->find(session()->get('registroOficina'));
                $correlativoHR = $this->obtenerCorrelativo($oficina['correlativo'].'MIN-ILEGAL/');

                $data = array(
                    'fk_municipio' => $this->request->getPost('fk_municipio'),
                    'fk_tipo_denuncia' => 2,
                    'correlativo' => $correlativoDenuncia,
                    'comunidad_localidad' => mb_strtoupper($this->request->getPost('comunidad_localidad')),
                    'descripcion_lugar' => mb_strtoupper($this->request->getPost('descripcion_lugar')),
                    'fk_usuario_creador' => session()->get('registroUser'),
                    'departamento' => $ubicacion['departamento'],
                    'provincia' => $ubicacion['provincia'],
                    'municipio' => $ubicacion['municipio'],
                    'fecha_denuncia' => date('Y-m-d H:i:s'),
                );
                if($denunciasMineriaIlegalModel->insert($data) === false){
                    session()->setFlashdata('fail', $denunciasMineriaIlegalModel->errors());
                }else{
                    $idDenuncia = $denunciasMineriaIlegalModel->getInsertID();

                    $id_denunciantes = $this->request->getPost('id_denunciantes');
                    foreach($id_denunciantes as $id_denunciante){
                        $dataDenunciaDenunciante = array(
                            'fk_denuncia' => $idDenuncia,
                            'fk_denunciante' => $id_denunciante,
                        );
                        if($denunciasDenunciantesMineriaIleglaModel->insert($dataDenunciaDenunciante) === false)
                            session()->setFlashdata('fail', $denunciasDenunciantesMineriaIleglaModel->errors());
                    }

                    $coordenadas = $this->obtenerCoordenadas($this->request->getPost('coordenadas'));
                    if(count($coordenadas)>0){
                        foreach($coordenadas as $coordenada){
                            $dataCoordenada = array(
                                'fk_denuncia' => $idDenuncia,
                                'latitud' => $coordenada['latitud'],
                                'longitud' => $coordenada['longitud'],
                            );
                            if($coordenadasMineriaIlegalModel->insert($dataCoordenada) === false)
                                session()->setFlashdata('fail', $coordenadasMineriaIlegalModel->errors());
                        }
                    }

                    $croquisDigital = $this->request->getFile('croquis_digital');
                    $tipoCroquisDigital = $this->obtenerTipoArchivo($croquisDigital->guessExtension());
                    $nombreCroquisDigital = $croquisDigital->getRandomName();
                    $croquisDigital->move($this->rutaArchivos,$nombreCroquisDigital);
                    $nombreCroquisDigital = $this->rutaArchivos.$nombreCroquisDigital;

                    $dataAdjunto = array(
                        'fk_denuncia' => $idDenuncia,
                        'nombre' => 'CROQUIS DE LA DENUNCIA',
                        'tipo' => $tipoCroquisDigital,
                        'adjunto' => $nombreCroquisDigital,
                        'fk_usuario_creador' => session()->get('registroUser'),
                    );
                    if($adjuntosMineriaIlegalModel->insert($dataAdjunto) === false)
                        session()->setFlashdata('fail', $adjuntosMineriaIlegalModel->errors());

                    $documentoDigital = $this->request->getFile('documento_digital');
                    $tipoDocumentoDigital = $this->obtenerTipoArchivo($documentoDigital->guessExtension());
                    $nombreDocumentoDigital = $documentoDigital->getRandomName();
                    $documentoDigital->move($this->rutaArchivos,$nombreDocumentoDigital);
                    $nombreDocumentoDigital = $this->rutaArchivos.$nombreDocumentoDigital;

                    $dataAdjunto = array(
                        'fk_denuncia' => $idDenuncia,
                        'nombre' => 'DENUNCIA O DOCUMENTO EXTERNO',
                        'tipo' => $tipoDocumentoDigital,
                        'adjunto' => $nombreDocumentoDigital,
                        'cite' => mb_strtoupper($this->request->getPost('documento_numero')),
                        'fecha_cite'=>((!empty($this->request->getPost('documento_fecha'))) ? $this->request->getPost('documento_fecha') : NULL),
                        'fk_usuario_creador' => session()->get('registroUser'),
                    );
                    if($adjuntosMineriaIlegalModel->insert($dataAdjunto) === false)
                        session()->setFlashdata('fail', $adjuntosMineriaIlegalModel->errors());

                    $where = array('deleted_at' => NULL, 'fk_estado_padre' => NULL, 'fk_tramite' =>$this->idTramite);
                    $primerEstado = $estadoTramiteModel->where($where)->orderBy('orden', 'ASC')->first();
                    $estado = 'DERIVADO';
                    $dataHojaRuta = array(
                        'fk_denuncia' => $idDenuncia,
                        'fk_oficina' => $oficina['id'],
                        'correlativo' => $correlativoHR,
                        'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                        'ultimo_estado' => $estado,
                        'ultimo_fk_estado_tramite_padre' => $primerEstado['id'],
                        'ultimo_instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                        'fk_usuario_actual' => session()->get('registroUser'),
                        'ultimo_fk_usuario_responsable' => $this->request->getPost('fk_usuario_destinatario'),
                        'ultimo_fk_usuario_remitente' => session()->get('registroUser'),
                        'ultimo_fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                        'fk_usuario_creador' => session()->get('registroUser'),
                        'fk_usuario_destino' => $this->request->getPost('fk_usuario_destinatario'),
                        'fecha_hoja_ruta' => date('Y-m-d H:i:s'),
                    );
                    if($hojaRutaMineriaIlegalModel->insert($dataHojaRuta) === false){
                        session()->setFlashdata('fail', $hojaRutaMineriaIlegalModel->errors());
                    }else{
                        $idHojaRuta = $hojaRutaMineriaIlegalModel->getInsertID();
                        $dataDerivacion = array(
                            'fk_hoja_ruta' => $idHojaRuta,
                            'fk_estado_tramite_padre' => $primerEstado['id'],
                            'instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                            'estado' => $estado,
                            'fk_usuario_responsable' => $this->request->getPost('fk_usuario_destinatario'),
                            'fk_usuario_remitente' => session()->get('registroUser'),
                            'fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                            'fk_usuario_creador' => session()->get('registroUser'),
                        );
                        if($derivacionMineriaIlegalModel->insert($dataDerivacion) === false){
                            session()->setFlashdata('fail', $derivacionMineriaIlegalModel->errors());
                        }else{
                            $this->actualizarCorrelativo($oficinaDepartamento['correlativo'].'FMI/');
                            $this->actualizarCorrelativo($oficina['correlativo'].'MIN-ILEGAL/');
                            session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información. <code><a href="'.base_url($this->controlador.'hoja_ruta_pdf/'.$idHojaRuta).'" target="_blank">Descargar Hoja de Ruta</a></code>');
                        }
                    }
                }
                return redirect()->to($this->controlador.'mis_ingresos');
            }
        }
        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Nueva Denuncia Minería Ilegal';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['subtitulo'] = 'Nueva Denuncia Minería Ilegal';
        $contenido['expedidos'] = $this->expedidos;
        $contenido['departamentos'] = array_merge(array(''=>'SELECCIONE EL DEPARTAMENTO'), $this->obtenerDepartamentos());
        $contenido['provincias'] = array_merge(array(''=>'SELECCIONE LA PROVINCIA'),$provincias);
        $contenido['municipios'] = array(''=>'SELECCIONE EL MUNICIPIO') + $municipios;
        $contenido['accion'] = $this->controlador.'agregar_ventanilla';
        $contenido['controlador'] = $this->controlador;
        $contenido['nuevo_denunciante'] = view($this->carpeta.'nuevo_denunciante', $contenido);
        $data['content'] = view($this->carpeta.'agregar_ventanilla', $contenido);
        $data['menu_actual'] = $this->menuActual.'agregar_ventanilla';
        $data['validacion_js'] = 'mineria-ilegal-ventanilla-validation.js';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['alertas'] = $this->alertasTramites();
        $data['mapas'] = true;
        echo view('templates/template', $data);
    }
    public function editarVentanilla($id_hoja_ruta){
        $hojaRutaMineriaIlegalModel = new HojaRutaMineriaIlegalModel();
        $campos = array('*', 'fk_denuncia', 'correlativo as correlativo_hr', "to_char(created_at, 'DD/MM/YYYY HH24:MI') as fecha_hr");
        if($hoja_ruta = $hojaRutaMineriaIlegalModel->select($campos)->find($id_hoja_ruta)){
            $db = \Config\Database::connect();
            $denunciasMineriaIlegalModel = new DenunciasMineriaIlegalModel();
            $adjuntosMineriaIlegalModel = new AdjuntosMineriaIlegalModel();
            $derivacionMineriaIlegalModel = new DerivacionMineriaIlegalModel();
            $coordenadasMineriaIlegalModel = new CoordenadasMineriaIlegalModel();
            $croquis = $adjuntosMineriaIlegalModel->where(array('fk_usuario_creador'=>session()->get('registroUser'),'fk_denuncia'=>$hoja_ruta['fk_denuncia'],'nombre'=>'CROQUIS DE LA DENUNCIA'))->first();
            $documento_externo = $adjuntosMineriaIlegalModel->where(array('fk_usuario_creador'=>session()->get('registroUser'),'fk_denuncia'=>$hoja_ruta['fk_denuncia'],'nombre'=>'DENUNCIA O DOCUMENTO EXTERNO'))->first();
            $denuncia = $denunciasMineriaIlegalModel->select("*, correlativo as correlativo_denuncia, to_char(created_at, 'DD/MM/YYYY HH24:MI') as fecha_denuncia")->find($hoja_ruta['fk_denuncia']);
            $coordenadas = $coordenadasMineriaIlegalModel->where(array('fk_denuncia' => $hoja_ruta['fk_denuncia']))->findAll();
            $derivacion = $derivacionMineriaIlegalModel->where(array('fk_usuario_creador'=>session()->get('registroUser'),'fk_hoja_ruta'=>$hoja_ruta['id']))->orderBy('id','DESC')->first();
            $campos = array('de.id', 'de.nombres', 'de.apellidos', 'de.documento_identidad', 'de.expedido', "de.telefonos", "de.email", 'de.direccion', 'de.documento_identidad_digital');
            $where = array(
                'dd.fk_denuncia' => $hoja_ruta['fk_denuncia'],
            );
            $builder = $db->table('mineria_ilegal.denuncias_denunciantes AS dd')
            ->select($campos)
            ->join('mineria_ilegal.denunciantes AS de', 'dd.fk_denunciante = de.id', 'left')
            ->where($where)
            ->orderBY('dd.id', 'ASC');
            $denunciantes = $builder->get()->getResultArray();
            $id_denunciantes_ant = '';
            foreach($denunciantes as $denunciante)
                $id_denunciantes_ant .= $denunciante['id'].',';

            $provincias = $this->obtenerProvincias($denuncia['departamento']);
            $municipios = $this->obtenerMunicipios($denuncia['departamento'], $denuncia['provincia']);
            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Editar Denuncia Minería Ilegal';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['subtitulo'] = 'Editar Denuncia Minería Ilegal';
            $contenido['hoja_ruta'] = $hoja_ruta;
            $contenido['derivacion'] = $derivacion;
            $contenido['denuncia'] = $denuncia;
            $contenido['id_denunciantes_ant'] = substr($id_denunciantes_ant, 0, -1);
            $contenido['denunciantes'] = $denunciantes;
            $contenido['coordenadas'] = $this->transformarCoordenadas($coordenadas);
            $contenido['croquis'] = $croquis;
            $contenido['documento_externo'] = $documento_externo;
            $contenido['expedidos'] = $this->expedidos;
            $contenido['departamentos'] = array_merge(array(''=>'SELECCIONE EL DEPARTAMENTO'), $this->obtenerDepartamentos());
            $contenido['provincias'] = array_merge(array(''=>'SELECCIONE LA PROVINCIA'),$provincias);
            $contenido['municipios'] = array(''=>'SELECCIONE EL MUNICIPIO') + $municipios;
            $contenido['usu_destinatario'] = $this->obtenerUsuario($derivacion['fk_usuario_destinatario']);
            $contenido['accion'] = $this->controlador.'guardar_editar_ventanilla';
            $contenido['controlador'] = $this->controlador;
            $contenido['nuevo_denunciante'] = view($this->carpeta.'nuevo_denunciante', $contenido);
            $data['content'] = view($this->carpeta.'editar_ventanilla', $contenido);
            $data['menu_actual'] = $this->menuActual.'mis_ingresos';
            $data['validacion_js'] = 'mineria-ilegal-editar-ventanilla-validation.js';
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['alertas'] = $this->alertasTramites();
            $data['mapas'] = true;
            $data['puntos'] = $coordenadas;
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador);
        }
    }
    public function guardarEditarVentanilla(){
        if ($this->request->getPost()) {
            $denunciantesMineriaIlegalModel = new DenunciantesMineriaIlegalModel();
            $adjuntosMineriaIlegalModel = new AdjuntosMineriaIlegalModel();
            $validation = $this->validate([
                'fk_municipio' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar el Municipio.',
                    ]
                ],
                'comunidad_localidad' => [
                    'rules' => 'required',
                ],
                'descripcion_lugar' => [
                    'rules' => 'required',
                ],
                'croquis_digital' => [
                    'max_size[croquis_digital,20480]',
                ],
                'documento_digital' => [
                    'max_size[documento_digital,20480]',
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
                $croquis = $adjuntosMineriaIlegalModel->where(array('fk_usuario_creador'=>session()->get('registroUser'),'fk_denuncia'=>$this->request->getPost('id_denuncia'),'nombre'=>'CROQUIS DE LA DENUNCIA'))->first();
                $documento_externo = $adjuntosMineriaIlegalModel->where(array('fk_usuario_creador'=>session()->get('registroUser'),'fk_denuncia'=>$this->request->getPost('id_denuncia'),'nombre'=>'DENUNCIA O DOCUMENTO EXTERNO'))->first();
                $provincias = $this->obtenerProvincias($this->request->getPost('departamento'));
                $municipios = $this->obtenerMunicipios($this->request->getPost('departamento'), $this->request->getPost('provincia'));
                $cabera['titulo'] = $this->titulo;
                $cabera['navegador'] = true;
                $cabera['subtitulo'] = 'Editar Denuncia Minería Ilegal';
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['subtitulo'] = 'Editar Denuncia Minería Ilegal';
                $contenido['croquis'] = $croquis;
                $contenido['documento_externo'] = $documento_externo;
                $contenido['expedidos'] = $this->expedidos;
                $contenido['departamentos'] = array_merge(array(''=>'SELECCIONE EL DEPARTAMENTO'), $this->obtenerDepartamentos());
                $contenido['provincias'] = array_merge(array(''=>'SELECCIONE LA PROVINCIA'),$provincias);
                $contenido['municipios'] = array(''=>'SELECCIONE EL MUNICIPIO') + $municipios;
                $contenido['denunciantes'] = $denunciantesMineriaIlegalModel->whereIn('id', $this->request->getPost('id_denunciantes'))->findAll();
                $contenido['usu_destinatario'] = $this->obtenerUsuario($this->request->getPost('fk_usuario_destinatario'));
                $contenido['accion'] = $this->controlador.'guardar_editar_ventanilla';
                $contenido['controlador'] = $this->controlador;
                $contenido['nuevo_denunciante'] = view($this->carpeta.'nuevo_denunciante', $contenido);
                $data['content'] = view($this->carpeta.'editar_ventanilla', $contenido);
                $data['menu_actual'] = $this->menuActual.'mis_ingresos';
                $data['validacion_js'] = 'mineria-ilegal-editar-ventanilla-validation.js';
                $data['tramites_menu'] = $this->tramitesMenu();
                $data['alertas'] = $this->alertasTramites();
                $data['mapas'] = true;
                $data['puntos'] = $this->obtenerCoordenadas($this->request->getPost('coordenadas'));
                echo view('templates/template', $data);
            }else{
                $municipiosModel = new MunicipiosModel();
                $denunciasMineriaIlegalModel = new DenunciasMineriaIlegalModel();
                $denunciasDenunciantesMineriaIleglaModel = new DenunciasDenunciantesMineriaIlegalModel();
                $coordenadasMineriaIlegalModel = new CoordenadasMineriaIlegalModel();
                $adjuntosMineriaIlegalModel = new AdjuntosMineriaIlegalModel();
                $hojaRutaMineriaIlegalModel = new HojaRutaMineriaIlegalModel();
                $derivacionMineriaIlegalModel = new DerivacionMineriaIlegalModel();

                $ubicacion = $municipiosModel->find($this->request->getPost('fk_municipio'));
                $data = array(
                    'id' => $this->request->getPost('id_denuncia'),
                    'fk_municipio' => $this->request->getPost('fk_municipio'),
                    'comunidad_localidad' => mb_strtoupper($this->request->getPost('comunidad_localidad')),
                    'descripcion_lugar' => mb_strtoupper($this->request->getPost('descripcion_lugar')),
                    'fk_usuario_editor' => session()->get('registroUser'),
                    'departamento' => $ubicacion['departamento'],
                    'provincia' => $ubicacion['provincia'],
                    'municipio' => $ubicacion['municipio'],
                );
                if($denunciasMineriaIlegalModel->save($data) === false){
                    session()->setFlashdata('fail', $denunciasMineriaIlegalModel->errors());
                }else{
                    $id_denunciantes = $this->request->getPost('id_denunciantes');
                    if(implode(',',$id_denunciantes) != $this->request->getPost('id_denunciantes_ant')){
                        $this->liberarDenunciantes($this->request->getPost('id_denuncia'));
                        foreach($id_denunciantes as $id_denunciante){
                            $dataDenunciaDenunciante = array(
                                'fk_denuncia' => $this->request->getPost('id_denuncia'),
                                'fk_denunciante' => $id_denunciante,
                            );
                            if($denunciasDenunciantesMineriaIleglaModel->insert($dataDenunciaDenunciante) === false)
                                session()->setFlashdata('fail', $denunciasDenunciantesMineriaIleglaModel->errors());
                        }
                    }

                    if($this->obtenerCoordenadas($this->request->getPost('coordenadas')) !== $this->obtenerCoordenadas($this->request->getPost('coordenadas_ant'))){
                        $this->vaciarCoordenadas($this->request->getPost('id_denuncia'));
                        $coordenadas = $this->obtenerCoordenadas($this->request->getPost('coordenadas'));
                        if(count($coordenadas)>0){
                            foreach($coordenadas as $coordenada){
                                $dataCoordenada = array(
                                    'fk_denuncia' => $this->request->getPost('id_denuncia'),
                                    'latitud' => $coordenada['latitud'],
                                    'longitud' => $coordenada['longitud'],
                                );
                                if($coordenadasMineriaIlegalModel->insert($dataCoordenada) === false)
                                    session()->setFlashdata('fail', $coordenadasMineriaIlegalModel->errors());
                            }
                        }
                    }

                    $croquisDigital = $this->request->getFile('croquis_digital');
                    if(!empty($croquisDigital) && $croquisDigital->getSize()>0){
                        $croquis = $adjuntosMineriaIlegalModel->find($this->request->getPost('id_croquis'));
                        if(file_exists($croquis['adjunto']))
                            @unlink($croquis['adjunto']);
                        $tipoCroquisDigital = $this->obtenerTipoArchivo($croquisDigital->guessExtension());
                        $nombreCroquisDigital = $croquisDigital->getRandomName();
                        $croquisDigital->move($this->rutaArchivos,$nombreCroquisDigital);
                        $nombreCroquisDigital = $this->rutaArchivos.$nombreCroquisDigital;
                        $dataAdjunto = array(
                            'id' => $croquis['id'],
                            'tipo' => $tipoCroquisDigital,
                            'adjunto' => $nombreCroquisDigital,
                        );
                        if($adjuntosMineriaIlegalModel->save($dataAdjunto) === false)
                            session()->setFlashdata('fail', $adjuntosMineriaIlegalModel->errors());
                    }

                    $documentoDigital = $this->request->getFile('documento_digital');
                    if(!empty($documentoDigital) && $documentoDigital->getSize()>0){
                        $documento_externo = $adjuntosMineriaIlegalModel->find($this->request->getPost('id_documento_externo'));
                        if(file_exists($documento_externo['adjunto']))
                            @unlink($documento_externo['adjunto']);
                        $tipoDocumentoDigital = $this->obtenerTipoArchivo($documentoDigital->guessExtension());
                        $nombreDocumentoDigital = $documentoDigital->getRandomName();
                        $documentoDigital->move($this->rutaArchivos,$nombreDocumentoDigital);
                        $nombreDocumentoDigital = $this->rutaArchivos.$nombreDocumentoDigital;

                        $dataAdjunto = array(
                            'id' => $documento_externo['id'],
                            'tipo' => $tipoDocumentoDigital,
                            'adjunto' => $nombreDocumentoDigital,
                        );
                        if($adjuntosMineriaIlegalModel->save($dataAdjunto) === false)
                            session()->setFlashdata('fail', $adjuntosMineriaIlegalModel->errors());
                    }

                    $dataHojaRuta = array(
                        'id' => $this->request->getPost('id_hoja_ruta'),
                        'ultimo_instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                        'ultimo_fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                        'ultimo_fk_usuario_responsable' => $this->request->getPost('fk_usuario_destinatario'),
                        'fk_usuario_destino' => $this->request->getPost('fk_usuario_destinatario'),
                        'editar' => 'false',
                    );
                    if($hojaRutaMineriaIlegalModel->save($dataHojaRuta) === false)
                        session()->setFlashdata('fail', $hojaRutaMineriaIlegalModel->errors());

                    $dataDerivacion = array(
                        'id' => $this->request->getPost('id_derivacion'),
                        'instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                        'fk_usuario_responsable' => $this->request->getPost('fk_usuario_destinatario'),
                        'fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                    );
                    if($derivacionMineriaIlegalModel->save($dataDerivacion) === false)
                        session()->setFlashdata('fail', $derivacionMineriaIlegalModel->errors());
                }
                session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información. <code><a href="'.base_url($this->controlador.'hoja_ruta_pdf/'.$this->request->getPost('id_hoja_ruta')).'" target="_blank">Descargar Hoja de Ruta</a></code>');
                return redirect()->to($this->controlador.'mis_ingresos');
            }
        }
    }

    public function agregarFiscalizacion(){
        $provincias = array();
        $municipios = array();
        if ($this->request->getPost()) {
            $validation = $this->validate([
                'nombres' => [
                    'rules' => 'required',
                ],
                'apellidos' => [
                    'rules' => 'required',
                ],
                'documento_identidad' => [
                    'rules' => 'required',
                ],
                'expedido' => [
                    'rules' => 'required',
                ],
                'direccion' => [
                    'rules' => 'required',
                ],
                'telefonos' => [
                    'rules' => 'required',
                ],
                'fk_municipio' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar el Municipio.',
                    ]
                ],
                'comunidad_localidad' => [
                    'rules' => 'required',
                ],
                'descripcion_lugar' => [
                    'rules' => 'required',
                ],
                'fk_oficina' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar la Dirección Departamental o Regional.',
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
                $provincias = $this->obtenerProvincias($this->request->getPost('departamento'));
                $municipios = $this->obtenerMunicipios($this->request->getPost('departamento'), $this->request->getPost('provincia'));
                $contenido['usu_destinatario'] = $this->obtenerUsuario($this->request->getPost('fk_usuario_destinatario'));
                $contenido['validation'] = $this->validator;
            }else{
                $oficinaModel = new OficinasModel();
                $municipiosModel = new MunicipiosModel();
                $oficina = $oficinaModel->find($this->request->getPost('fk_oficina'));
                $correlativoFormularioDenuncia = $this->obtenerCorrelativo($oficina['correlativo'].'FMI/');
                $denunciasMineriaIlegalModel = new DenunciasMineriaIlegalModel();
                $denunciantesMineriaIlegalModel = new DenunciantesMineriaIlegalModel();
                $denunciasDenunciantesMineriaIleglaModel = new DenunciasDenunciantesMineriaIlegalModel();
                $coordenadasMineriaIlegalModel = new CoordenadasMineriaIlegalModel();
                $adjuntosMineriaIlegalModel = new AdjuntosMineriaIlegalModel();
                $hojaRutaMineriaIlegalModel = new HojaRutaMineriaIlegalModel();
                $derivacionMineriaIlegalModel = new DerivacionMineriaIlegalModel();
                $ubicacion = $municipiosModel->find($this->request->getPost('fk_municipio'));

                $data = array(
                    'fk_municipio' => $this->request->getPost('fk_municipio'),
                    'fk_tipo_denuncia' => 3,
                    'correlativo' => $correlativoFormularioDenuncia,
                    'comunidad_localidad' => mb_strtoupper($this->request->getPost('comunidad_localidad')),
                    'descripcion_lugar' => mb_strtoupper($this->request->getPost('descripcion_lugar')),
                    'autores' => mb_strtoupper($this->request->getPost('autores')),
                    'persona_juridica' => mb_strtoupper($this->request->getPost('persona_juridica')),
                    'descripcion_materiales' => mb_strtoupper($this->request->getPost('descripcion_materiales')),
                    'fk_usuario_creador' => session()->get('registroUser'),
                    'departamento' => $ubicacion['departamento'],
                    'provincia' => $ubicacion['provincia'],
                    'municipio' => $ubicacion['municipio'],
                );
                if($denunciasMineriaIlegalModel->insert($data) === false){
                    session()->setFlashdata('fail', $denunciasMineriaIlegalModel->errors());
                }else{
                    $idDenuncia = $denunciasMineriaIlegalModel->getInsertID();
                    if($this->request->getPost('id_denunciante')){
                        if($denunciante = $denunciantesMineriaIlegalModel->find($this->request->getPost('id_denunciante')))
                            $idDenunciante = $denunciante['id'];
                    }else{
                        $documentoIdentidadDigital = $this->request->getFile('documento_identidad_digital');
                        $nombreDocumentoIdentidadDigital = $documentoIdentidadDigital->getRandomName();
                        $documentoIdentidadDigital->move($this->rutaArchivosDenunciante,$nombreDocumentoIdentidadDigital);
                        $nombreDocumentoIdentidadDigital = $this->rutaArchivosDenunciante.$nombreDocumentoIdentidadDigital;

                        $dataDenunciante = array(
                            'nombres' => mb_strtoupper($this->request->getPost('nombres')),
                            'apellidos' => mb_strtoupper($this->request->getPost('apellidos')),
                            'documento_identidad' => $this->request->getPost('documento_identidad'),
                            'expedido' => $this->request->getPost('expedido'),
                            'telefonos' => $this->request->getPost('telefonos'),
                            'direccion' => mb_strtoupper($this->request->getPost('direccion')),
                            'email' => $this->request->getPost('email'),
                            'documento_identidad_digital' => $nombreDocumentoIdentidadDigital,
                            'fk_usuario_creador' => session()->get('registroUser'),
                        );
                        if($denunciantesMineriaIlegalModel->insert($dataDenunciante) === false)
                            session()->setFlashdata('fail', $denunciantesMineriaIlegalModel->errors());
                        else
                            $idDenunciante = $denunciantesMineriaIlegalModel->getInsertID();
                    }

                    $dataDenunciaDenunciante = array(
                        'fk_denuncia' => $idDenuncia,
                        'fk_denunciante' => $idDenunciante,
                    );
                    if($denunciasDenunciantesMineriaIleglaModel->insert($dataDenunciaDenunciante) === false){
                        session()->setFlashdata('fail', $denunciasDenunciantesMineriaIleglaModel->errors());
                    }

                    $coordenadas = $this->obtenerCoordenadas($this->request->getPost('coordenadas'));
                    if(count($coordenadas)>0){
                        foreach($coordenadas as $coordenada){
                            $dataCoordenada = array(
                                'fk_denuncia' => $idDenuncia,
                                'latitud' => $coordenada['latitud'],
                                'longitud' => $coordenada['longitud'],
                            );
                            if($coordenadasMineriaIlegalModel->insert($dataCoordenada) === false){
                                session()->setFlashdata('fail', $coordenadasMineriaIlegalModel->errors());
                            }
                        }
                    }

                    if ($adjuntos = $this->request->getFiles()) {
                        foreach($adjuntos as $nombre => $adjunto){
                            if($nombre == 'adjunto' && count($adjunto) > 0){
                                $tipo = $this->request->getPost('tipo');
                                $nombre = $this->request->getPost('nombre');
                                foreach($adjunto as $i => $archivo){
                                    $nombreDocDigital = $archivo->getRandomName();
                                    $archivo->move($this->rutaArchivos,$nombreDocDigital);
                                    $nombreDocDigital = $this->rutaArchivos.$nombreDocDigital;
                                    $dataAdjunto = array(
                                        'fk_denuncia' => $idDenuncia,
                                        'nombre' => mb_strtoupper($nombre[$i]),
                                        'tipo' => $tipo[$i],
                                        'adjunto' => $nombreDocDigital,
                                    );
                                    if($adjuntosMineriaIlegalModel->insert($dataAdjunto) === false)
                                        session()->setFlashdata('fail', $adjuntosMineriaIlegalModel->errors());
                                }
                            }
                        }
                    }

                    $correlativoHR = $this->obtenerCorrelativo($oficina['correlativo'].'MIN-ILEGAL/');
                    $estado = 'DERIVADO';
                    $dataHojaRuta = array(
                        'fk_denuncia' => $idDenuncia,
                        'fk_oficina' => $oficina['id'],
                        'correlativo' => $correlativoHR,
                        'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                        'ultimo_estado' => $estado,
                        'ultimo_instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                        'fk_usuario_actual' => session()->get('registroUser'),
                        'ultimo_fk_usuario_responsable' => $this->request->getPost('fk_usuario_destinatario'),
                        'ultimo_fk_usuario_remitente' => session()->get('registroUser'),
                        'ultimo_fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                        'fk_usuario_creador' => session()->get('registroUser'),
                        'fk_usuario_destino' => $this->request->getPost('fk_usuario_destinatario'),
                    );
                    if($hojaRutaMineriaIlegalModel->insert($dataHojaRuta) === false){
                        session()->setFlashdata('fail', $hojaRutaMineriaIlegalModel->errors());
                    }else{
                        $idHojaRuta = $hojaRutaMineriaIlegalModel->getInsertID();
                        $dataDerivacion = array(
                            'fk_hoja_ruta' => $idHojaRuta,
                            'instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                            'estado' => $estado,
                            'fk_usuario_responsable' => $this->request->getPost('fk_usuario_destinatario'),
                            'fk_usuario_remitente' => session()->get('registroUser'),
                            'fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                            'fk_usuario_creador' => session()->get('registroUser'),
                        );
                        if($derivacionMineriaIlegalModel->insert($dataDerivacion) === false){
                            session()->setFlashdata('fail', $derivacionMineriaIlegalModel->errors());
                        }else{
                            $this->actualizarCorrelativo($oficina['correlativo'].'FMI/');
                            $this->actualizarCorrelativo($oficina['correlativo'].'MIN-ILEGAL/');
                            session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información. <code><a href="'.base_url($this->controlador.'formulario_denuncia_pdf/'.$idDenuncia).'" target="_blank">Descargar Formulario de Denuncia</a></code>  <code><a href="'.base_url($this->controlador.'hoja_ruta_pdf/'.$idHojaRuta).'" target="_blank">Descargar Hoja de Ruta</a></code>');
                        }
                    }

                }
                return redirect()->to($this->controlador.'mis_tramites');
            }
        }
        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Nueva Denuncia - DFCCI';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['subtitulo'] = 'Nueva Denuncia - DFCCI';
        $contenido['accion'] = $this->controlador.'agregar_fiscalizacion';
        $contenido['controlador'] = $this->controlador;
        $contenido['departamentos'] = array_merge(array(''=>'SELECCIONE EL DEPARTAMENTO'), $this->obtenerDepartamentos());
        $contenido['provincias'] = array_merge(array(''=>'SELECCIONE LA PROVINCIA'),$provincias);
        $contenido['municipios'] = array_merge(array(''=>'SELECCIONE EL MUNICIPIO'), $municipios);
        $contenido['expedidos'] = $this->expedidos;
        $contenido['tipos_adjuntos'] = $this->tiposAdjuntos;
        $contenido['oficinas'] = array_merge(array(''=>'SELECCIONE LA DIRECCIÓN DEPARTAMENTAL O REGIONAL'), $this->obtenerDireccionesRegionales());
        $data['content'] = view($this->carpeta.'agregar_fiscalizacion', $contenido);
        $data['menu_actual'] = $this->menuActual.'agregar_fiscalizacion';
        $data['validacion_js'] = 'mineria-ilegal-fiscalizacion-validation.js';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['alertas'] = $this->alertasTramites();
        $data['mapas'] = true;
        echo view('templates/template', $data);
    }

    public function agregarOficio(){
        $provincias = array();
        $municipios = array();
        if ($this->request->getPost()) {
            $id_areas_mineras = $this->request->getPost('id_areas_mineras');
            $id_hojas_rutas = $this->request->getPost('id_hojas_rutas');
            $validation = $this->validate([
                'origen_oficio' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar una opción.',
                    ]
                ],
                'informe_tecnico_numero' => [
                    'rules' => 'required',
                ],
                'informe_tecnico_fecha' => [
                    'rules' => 'required',
                ],
                'informe_tecnico_digital' => [
                    'uploaded[informe_tecnico_digital]',
                    'max_size[informe_tecnico_digital,20480]',
                ],
                'descripcion_oficio' => [
                    'rules' => 'required',
                ],
                'fk_municipio' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar el Municipio.',
                    ]
                ],
                'comunidad_localidad' => [
                    'rules' => 'required',
                ],
                'descripcion_lugar' => [
                    'rules' => 'required',
                ],
                'fk_oficina' => [
                    'rules' => 'required',
                ],
                'fk_usuario_destinatario' => [
                    'rules' => 'required',
                ],
                'instruccion' => [
                    'rules' => 'required',
                ],
            ]);
            if(!$validation){
                if(isset($id_hojas_rutas) && count($id_hojas_rutas) > 0){
                    $hojas_rutas = array();
                    foreach($id_hojas_rutas as $id_hoja_ruta)
                        $hojas_rutas[] = $this->obtenerDatosHrInExSincobol($id_hoja_ruta);
                    $contenido['hojas_rutas'] = $hojas_rutas;
                }
                if(isset($id_areas_mineras) && count($id_areas_mineras) > 0){
                    $areas_mineras = array();
                    foreach($id_areas_mineras as $id_area_minera)
                        $areas_mineras[] = $this->obtenerDatosAreaMineraMineriaIlegal($id_area_minera);
                    $contenido['areas_mineras'] = $areas_mineras;
                }
                $provincias = $this->obtenerProvincias($this->request->getPost('departamento'));
                $municipios = $this->obtenerMunicipios($this->request->getPost('departamento'), $this->request->getPost('provincia'));
                $contenido['usu_destinatario'] = $this->obtenerUsuario($this->request->getPost('fk_usuario_destinatario'));
                $contenido['validation'] = $this->validator;
            }else{
                $oficinaModel = new OficinasModel();
                $municipiosModel = new MunicipiosModel();
                $denunciasMineriaIlegalModel = new DenunciasMineriaIlegalModel();
                $denunciasHrSincobolMineriaIlegalModel = new DenunciasHrSincobolMineriaIlegalModel();
                $denunciasAreasMinerasMineriaIlegalModel = new DenunciasAreasMinerasMineriaIlegalModel();
                $coordenadasMineriaIlegalModel = new CoordenadasMineriaIlegalModel();
                $estadoTramiteModel = new EstadoTramiteModel();
                $adjuntosMineriaIlegalModel = new AdjuntosMineriaIlegalModel();
                $hojaRutaMineriaIlegalModel = new HojaRutaMineriaIlegalModel();
                $derivacionMineriaIlegalModel = new DerivacionMineriaIlegalModel();

                $ubicacion = $municipiosModel->find($this->request->getPost('fk_municipio'));
                $oficinaDepartamento = $oficinaModel->like('departamentos_atencion', $ubicacion['departamento'])->first();
                $correlativoDenuncia = $this->obtenerCorrelativo($oficinaDepartamento['correlativo'].'FMI/');
                $oficina = $oficinaModel->find($this->request->getPost('fk_oficina'));
                $correlativoHR = $this->obtenerCorrelativo($oficina['correlativo'].'MIN-ILEGAL/');

                /* INFORME TECNICO*/
                $informeTecnicoDigital = $this->request->getFile('informe_tecnico_digital');
                $nombreInformeTecnicoDigital = $informeTecnicoDigital->getRandomName();
                $informeTecnicoDigital->move($this->rutaArchivos,$nombreInformeTecnicoDigital);
                $nombreInformeTecnicoDigital = $this->rutaArchivos.$nombreInformeTecnicoDigital;

                $data = array(
                    'fk_municipio' => $this->request->getPost('fk_municipio'),
                    'fk_tipo_denuncia' => 3,
                    'correlativo' => $correlativoDenuncia,
                    'origen_oficio' => $this->request->getPost('origen_oficio'),
                    'enlace' => $this->request->getPost('enlace'),
                    'informe_tecnico_numero' => mb_strtoupper($this->request->getPost('informe_tecnico_numero')),
                    'informe_tecnico_fecha' => $this->request->getPost('informe_tecnico_fecha'),
                    'informe_tecnico_digital' => $nombreInformeTecnicoDigital,
                    'descripcion_oficio' => mb_strtoupper($this->request->getPost('descripcion_oficio')),
                    'comunidad_localidad' => mb_strtoupper($this->request->getPost('comunidad_localidad')),
                    'descripcion_lugar' => mb_strtoupper($this->request->getPost('descripcion_lugar')),
                    'autores' => mb_strtoupper($this->request->getPost('autores')),
                    'persona_juridica' => mb_strtoupper($this->request->getPost('persona_juridica')),
                    'descripcion_materiales' => mb_strtoupper($this->request->getPost('descripcion_materiales')),
                    'fk_usuario_creador' => session()->get('registroUser'),
                    'departamento' => $ubicacion['departamento'],
                    'provincia' => $ubicacion['provincia'],
                    'municipio' => $ubicacion['municipio'],
                    'tiene_area_minera' => ( isset($id_areas_mineras) && count($id_areas_mineras)>0) ? 'true' : 'false',
                    'fecha_denuncia' => date('Y-m-d H:i:s'),
                );
                if($denunciasMineriaIlegalModel->insert($data) === false){
                    session()->setFlashdata('fail', $denunciasMineriaIlegalModel->errors());
                }else{
                    $idDenuncia = $denunciasMineriaIlegalModel->getInsertID();

                    if(isset($id_hojas_rutas) && count($id_hojas_rutas) > 0){
                        foreach($id_hojas_rutas as $id_hoja_ruta){
                            $dataHojaRutaSincobol = array(
                                'fk_denuncia' => $idDenuncia,
                                'fk_hoja_ruta' => $id_hoja_ruta,
                            );
                            if($denunciasHrSincobolMineriaIlegalModel->insert($dataHojaRutaSincobol) === false)
                                session()->setFlashdata('fail', $denunciasHrSincobolMineriaIlegalModel->errors());
                            else
                                $this->archivarHrSincobol($id_hoja_ruta, 'ANEXADO AL SISTEMA DE SEGUIMIENTO Y CONTROL DE TRAMITES POR '.session()->get('registroUserName'));
                        }
                    }

                    if(isset($id_areas_mineras) && count($id_areas_mineras) > 0){
                        foreach($id_areas_mineras as $id_area_minera){
                            $dataAreaMinera = array(
                                'fk_denuncia' => $idDenuncia,
                                'fk_area_minera' => $id_area_minera,
                            );
                            if($denunciasAreasMinerasMineriaIlegalModel->insert($dataAreaMinera) === false)
                                session()->setFlashdata('fail', $denunciasAreasMinerasMineriaIlegalModel->errors());
                        }
                    }

                    $coordenadas = $this->obtenerCoordenadas($this->request->getPost('coordenadas'));
                    if(count($coordenadas)>0){
                        foreach($coordenadas as $coordenada){
                            $dataCoordenada = array(
                                'fk_denuncia' => $idDenuncia,
                                'latitud' => $coordenada['latitud'],
                                'longitud' => $coordenada['longitud'],
                            );
                            if($coordenadasMineriaIlegalModel->insert($dataCoordenada) === false)
                                session()->setFlashdata('fail', $coordenadasMineriaIlegalModel->errors());
                        }
                    }

                    if ($adjuntos = $this->request->getFiles()) {
                        foreach($adjuntos as $nombre => $adjunto){
                            if($nombre == 'adjuntos' && count($adjunto) > 0){
                                $nombres = $this->request->getPost('nombres');
                                $cites = $this->request->getPost('cites');
                                $fecha_cites = $this->request->getPost('fecha_cites');
                                foreach($adjunto as $i => $archivo){
                                    $tipoDocDigital = $this->obtenerTipoArchivo($archivo->guessExtension());
                                    $nombreDocDigital = $archivo->getRandomName();
                                    $archivo->move($this->rutaArchivos,$nombreDocDigital);
                                    $nombreDocDigital = $this->rutaArchivos.$nombreDocDigital;
                                    $dataAdjunto = array(
                                        'fk_denuncia' => $idDenuncia,
                                        'nombre' => mb_strtoupper($nombres[$i]),
                                        'cite' => mb_strtoupper($cites[$i]),
                                        'fecha_cite'=>((!empty($fecha_cites[$i])) ? $fecha_cites[$i] : NULL),
                                        'tipo' => $tipoDocDigital,
                                        'adjunto' => $nombreDocDigital,
                                        'fk_usuario_creador' => session()->get('registroUser'),
                                    );
                                    if($adjuntosMineriaIlegalModel->insert($dataAdjunto) === false)
                                        session()->setFlashdata('fail', $adjuntosMineriaIlegalModel->errors());
                                }
                            }
                        }
                    }

                    $where = array('deleted_at' => NULL, 'fk_estado_padre' => NULL, 'fk_tramite' =>$this->idTramite);
                    $primerEstado = $estadoTramiteModel->where($where)->orderBy('orden', 'ASC')->first();
                    $estado = 'DERIVADO';
                    $dataHojaRuta = array(
                        'fk_denuncia' => $idDenuncia,
                        'fk_oficina' => $oficina['id'],
                        'correlativo' => $correlativoHR,
                        'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                        'ultimo_estado' => $estado,
                        'ultimo_fk_estado_tramite_padre' => $primerEstado['id'],
                        'ultimo_instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                        'fk_usuario_actual' => session()->get('registroUser'),
                        'ultimo_fk_usuario_responsable' => $this->request->getPost('fk_usuario_destinatario'),
                        'ultimo_fk_usuario_remitente' => session()->get('registroUser'),
                        'ultimo_fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                        'fk_usuario_creador' => session()->get('registroUser'),
                        'fk_usuario_destino' => $this->request->getPost('fk_usuario_destinatario'),
                        'fecha_hoja_ruta' => date('Y-m-d H:i:s'),
                    );
                    if($hojaRutaMineriaIlegalModel->insert($dataHojaRuta) === false){
                        session()->setFlashdata('fail', $hojaRutaMineriaIlegalModel->errors());
                    }else{
                        $idHojaRuta = $hojaRutaMineriaIlegalModel->getInsertID();
                        $dataDerivacion = array(
                            'fk_hoja_ruta' => $idHojaRuta,
                            'fk_estado_tramite_padre' => $primerEstado['id'],
                            'instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                            'estado' => $estado,
                            'fk_usuario_responsable' => $this->request->getPost('fk_usuario_destinatario'),
                            'fk_usuario_remitente' => session()->get('registroUser'),
                            'fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                            'fk_usuario_creador' => session()->get('registroUser'),
                        );
                        if($derivacionMineriaIlegalModel->insert($dataDerivacion) === false){
                            session()->setFlashdata('fail', $derivacionMineriaIlegalModel->errors());
                        }else{
                            $this->actualizarCorrelativo($oficinaDepartamento['correlativo'].'FMI/');
                            $this->actualizarCorrelativo($oficina['correlativo'].'MIN-ILEGAL/');
                            session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información. <code><a href="'.base_url($this->controlador.'formulario_denuncia_pdf/'.$idDenuncia).'" target="_blank">Descargar Formulario de Denuncia</a></code>  <code><a href="'.base_url($this->controlador.'hoja_ruta_pdf/'.$idHojaRuta).'" target="_blank">Descargar Hoja de Ruta</a></code>');
                        }
                    }
                    return redirect()->to($this->controlador.'mis_tramites');
                }

            }
        }
        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Nueva Verificación de Oficio';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['subtitulo'] = 'Nueva Verificación de Oficio';
        $contenido['accion'] = $this->controlador.'agregar_oficio';
        $contenido['controlador'] = $this->controlador;
        $contenido['tipos_origen_oficio'] = array_merge(array(''=>'SELECCIONE UNA OPCIÓN'), $this->tiposOrigenOficio);
        $contenido['departamentos'] = array_merge(array(''=>'SELECCIONE EL DEPARTAMENTO'), $this->obtenerDepartamentos());
        $contenido['provincias'] = array_merge(array(''=>'SELECCIONE LA PROVINCIA'),$provincias);
        $contenido['municipios'] = array(''=>'SELECCIONE EL MUNICIPIO') + $municipios;
        $contenido['oficinas'] = array(''=>'SELECCIONE LA DIRECCIÓN DEPARTAMENTAL O REGIONAL') + $this->obtenerDireccionesRegionales();
        $data['content'] = view($this->carpeta.'agregar_oficio', $contenido);
        $data['menu_actual'] = $this->menuActual.'agregar_oficio';
        $data['validacion_js'] = 'mineria-ilegal-oficio-validation.js';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['alertas'] = $this->alertasTramites();
        $data['mapas'] = true;
        echo view('templates/template', $data);
    }

    private function obtenerDepartamentos(){
        $db = \Config\Database::connect();
        $resultado = array();
        $builder = $db->table('mineria_ilegal.municipios')->select('DISTINCT(departamento) AS departamento')->where('activo = true')->orderBY('departamento', 'ASC');
        if($departamentos = $builder->get()->getResultArray()){
            foreach($departamentos as $departamento)
                $resultado[$departamento['departamento']] = $departamento['departamento'];
        }
        return $resultado;
    }

    private function obtenerProvincias($departamento){
        $resultado = array();
        if($departamento){
            $db = \Config\Database::connect();
            $where = array(
                'activo' => 'true',
                'departamento' => $departamento,
            );
            $builder = $db->table('mineria_ilegal.municipios')->select('DISTINCT(provincia) AS provincia')->where($where)->orderBY('provincia', 'ASC');
            if($provincias = $builder->get()->getResultArray()){
                foreach($provincias as $provincia)
                    $resultado[$provincia['provincia']] = $provincia['provincia'];
            }
        }
        return $resultado;
    }

    private function obtenerMunicipios($departamento, $provincia){
        $resultado = array();
        if($departamento && $provincia){
            $db = \Config\Database::connect();
            $where = array(
                'activo' => 'true',
                'departamento' => $departamento,
                'provincia' => $provincia,
            );
            $builder = $db->table('mineria_ilegal.municipios')->select('id, municipio')->where($where)->orderBY('municipio', 'ASC');
            if($municipios = $builder->get()->getResultArray()){
                foreach($municipios as $municipio)
                    $resultado[$municipio['id']] = $municipio['municipio'];
            }
        }
        return $resultado;
    }

    private function obtenerDireccionesRegionales(){
        $resultado = array();
        $db = \Config\Database::connect();
        $where = array(
            'desconcentrado' => 'true',
            'activo' => 'true',
        );
        $builder = $db->table('public.oficinas')->select('id, nombre')->where($where)->orderBY('nombre', 'ASC');
        if($oficinas = $builder->get()->getResultArray()){
            foreach($oficinas as $oficina)
                $resultado[$oficina['id']] = $oficina['nombre'];
        }
        return $resultado;
    }
    private function obtenerDireccionesRegionalesManual(){
        $resultado = array();
        $db = \Config\Database::connect();
        $where = array(
            'activo' => 'true',
        );
        $builder = $db->table('public.oficinas')->select('id, nombre')->where($where)->orderBY('nombre', 'ASC');
        if($oficinas = $builder->get()->getResultArray()){
            foreach($oficinas as $oficina)
                $resultado[$oficina['id']] = $oficina['nombre'];
        }
        return $resultado;
    }

    private function obtenerUsuario($id){
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

    private function obtenerCorrelativo($sigla){
        $correlativosMineriaIlegalModel = new CorrelativosMineriaIlegalModel();
        $correlativo = '';
        $where = array(
            'gestion' => date('Y'),
            'sigla' => $sigla,
        );

        if($correlativoActual = $correlativosMineriaIlegalModel->where($where)->first())
            $correlativo = $sigla.($correlativoActual['correlativo_actual']+1).'/'.date('Y');
        else
            $correlativo = $sigla.'1'.'/'.date('Y');

        return $correlativo;
    }

    public function obtenerDatosAreaMineraMineriaIlegal($id){
        if($id){
            $dbSincobol = \Config\Database::connect('sincobol');
            $campos = array('am.id', 'am.codigo_unico', 'am.nombre as area_minera', 'tam.nombre as tipo_area_minera', "CONCAT(ROUND(am.extension,0), ' ', am.unidad) as extension",
            'acm.nombre as titular', 'tacm.nombre as clasificacion',  'am.departamentos', 'am.provincias', 'am.municipios');
            $where = array(
                'am.id' => $id
            );
            $builder = $dbSincobol->table('contratos_licencias.area_minera as am')
            ->select($campos)
            ->join('contratos_licencias.tipo_area_minera as tam', 'am.fk_tipo_area_minera = tam.id', 'left')
            ->join('contratos_licencias.actor_minero as acm', 'am.fk_actor_minero_poseedor = acm.id', 'left')
            ->join('contratos_licencias.tipo_actor_minero as tacm', 'acm.fk_tipo_actor_minero = tacm.id', 'left')
            ->where($where);
            if($fila = $builder->get()->getRowArray())
                return $fila;
        }
        return false;
    }

    public function obtenerTipoArchivo($extension){
        switch($extension){
            case 'jpg':
            case 'jpeg':
            case 'gif':
            case 'bmp':
            case 'png':
                return 'IMAGEN';
                break;
            case 'pdf':
            case 'docx':
            case 'doc':
            case 'txt':
                return 'DOCUMENTO';
                break;
            case 'avi':
            case 'mp4':
            case 'wmv':
            case 'mkv':
                return 'VIDEO';
                break;
            case 'mp3':
            case 'wav':
            case 'wma':
                return 'AUDIO';
                break;
            default:
                return 'OTRO';
            break;
        }
    }
    private function actualizarCorrelativo($sigla){
        $correlativosMineriaIlegalModel = new CorrelativosMineriaIlegalModel();
        $where = array(
            'gestion' => date('Y'),
            'sigla' => $sigla,
        );

        if($dataCorrelativo = $correlativosMineriaIlegalModel->where($where)->first())
            $dataCorrelativo['correlativo_actual'] +=1;
        else
            $dataCorrelativo = array_merge(array('correlativo_actual' => 1), $where);

        if($correlativosMineriaIlegalModel->save($dataCorrelativo) === false)
            return $correlativosMineriaIlegalModel->errors();

        return true;
    }
    private function liberarDenunciantes($fk_denuncia){
        $denunciasDenunciantesMineriaIleglaModel = new DenunciasDenunciantesMineriaIlegalModel();
        $where = array(
            'fk_denuncia' => $fk_denuncia,
        );
        return $denunciasDenunciantesMineriaIleglaModel->where($where)->delete();
    }
    private function liberarAreasMineras($fk_denuncia){
        $denunciasAreasMinerasMineriaIlegalModel = new DenunciasAreasMinerasMineriaIlegalModel();
        $where = array(
            'fk_denuncia' => $fk_denuncia,
        );
        return $denunciasAreasMinerasMineriaIlegalModel->where($where)->delete();
    }
    private function liberarHojasRutaSincobol($fk_denuncia){
        $denunciasHrSincobolMineriaIlegalModel = new DenunciasHrSincobolMineriaIlegalModel();
        $where = array(
            'fk_denuncia' => $fk_denuncia,
        );
        return $denunciasHrSincobolMineriaIlegalModel->where($where)->delete();
    }

    public function ajaxProvincias(){
        $departamento = $this->request->getPost('departamento');
        $html = '<option value="">SELECCIONE LA PROVINCIA</option>';
        if($departamento){
            if($provincias = $this->obtenerProvincias($departamento)){
                foreach($provincias as $provincia)
                    $html .= '<option value="'.$provincia.'" >'.$provincia.'</option>';
            }
        }
        echo $html;
    }

    public function ajaxMunicipios(){
        $departamento = $this->request->getPost('departamento');
        $provincia = $this->request->getPost('provincia');
        $html = '<option value="">SELECCIONE EL MUNICIPIO</option>';
        if($departamento && $provincia){
            if($municipios = $this->obtenerMunicipios($departamento, $provincia)){
                foreach($municipios as $id => $municipio)
                    $html .= '<option value="'.$id.'" >'.$municipio.'</option>';
            }
        }
        echo $html;
    }

    public function ajaxDenunciante(){
        $cadena = mb_strtoupper($this->request->getPost('texto'));
        if(!empty($cadena) && session()->get('registroUser')){
            $data = array();
            $db = \Config\Database::connect();
            $campos = array('id', "CONCAT(documento_identidad,' ',expedido, ' - ', nombres, ' ', apellidos) as denunciante");
            $where = array(
                'deleted_at' => NULL,
            );
            $builder = $db->table('mineria_ilegal.denunciantes')
            ->select($campos)
            ->where($where)
            ->like("CONCAT(documento_identidad,' ',expedido, ' - ', nombres, ' ', apellidos)", $cadena)
            ->orderBy('id','DESC')
            ->limit(20);
            $datos = $builder->get()->getResultArray();
            if($datos){
                foreach($datos as $row){
                    $data[] = array(
                        'id' => $row['id'],
                        'text' => $row['denunciante'],
                    );
                }
            }else{
                $data[] = array(
                    'id' => 0,
                    'text' => 'No se encuentra a la persona que busca'
                );
            }
            echo json_encode($data);
        }
    }

    public function ajaxDatosDenunciante(){
        $idDenunciante = $this->request->getPost('id');
        if(!empty($idDenunciante) && $idDenunciante>0){
            $denunciantesMineriaIlegalModel = new DenunciantesMineriaIlegalModel();
            if($data = $denunciantesMineriaIlegalModel->find($idDenunciante))
                echo json_encode($data);
        }
    }

    public function ajaxTrAdjunto(){
        $n = $this->request->getPost('n');
        $tipo = $this->request->getPost('tipo');
        $tipo_input_cite = 'hidden';
        $tipo_input_fecha_cite = 'hidden';
        if($tipo == 'DOCUMENTO'){
            $tipo_input_cite = 'text';
            $tipo_input_fecha_cite = 'date';
        }

        $html = "<tr id='adj$n'>
            <td class='text-cente form-group'>
                <input type='hidden' name='id_adjuntos[]' value='SIN' />
                <input type='hidden' name='tipos[]' value='$tipo' readonly />
                <span class='messages'></span>
                $tipo
            </td>
            <td class='text-center form-group'>
                <input type='text' name='nombres[]' class='form-control form-control-uppercase'>
                <span class='messages'></span>
            </td>
            <td class='text-center form-group'>
                <input type='$tipo_input_cite' name='cites[]' class='form-control form-control-uppercase'>
                <span class='messages'></span>
            </td>
            <td class='text-center form-group'>
                <input type='$tipo_input_fecha_cite' name='fecha_cites[]' class='form-control form-control-uppercase'>
                <span class='messages'></span>
            </td>
            <td class='text-center form-group'>
                <input type='file' name='adjuntos[]' class='form-control'>
                <span class='messages'></span>
            </td>
            <td class='text-center'>
                <button type='button' class='btn btn-sm btn-danger' title='Eliminar Adjunto' onclick='eliminar_adjunto($n);'>
                <span class='icofont icofont-ui-delete'></span>
                </button>
            </td>
        </tr>";
        echo $html;
    }

    public function ajaxAgregarDenunciante(){

        $denunciantesMineriaIlegalModel = new DenunciantesMineriaIlegalModel();
        $docDigital = $this->request->getFile('d_documento_identidad_digital');
        $nombreDocDigital = $docDigital->getRandomName();
        $docDigital->move($this->rutaArchivosDenunciante,$nombreDocDigital);
        $nombreDocDigital = $this->rutaArchivosDenunciante.$nombreDocDigital;

        $dataDenunciante = array(
            'nombres' => trim(mb_strtoupper($this->request->getPost('d_nombres'))),
            'apellidos' => trim(mb_strtoupper($this->request->getPost('d_apellidos'))),
            'documento_identidad' => trim(mb_strtoupper($this->request->getPost('d_documento_identidad'))),
            'expedido' => trim(mb_strtoupper($this->request->getPost('d_expedido'))),
            'telefonos' => trim(mb_strtoupper($this->request->getPost('d_telefonos'))),
            'direccion' => trim(mb_strtoupper($this->request->getPost('d_direccion'))),
            'email' => trim($this->request->getPost('d_email')),
            'documento_identidad_digital' => $nombreDocDigital,
            'fk_usuario_creador' => session()->get('registroUser'),
        );
        if($denunciantesMineriaIlegalModel->insert($dataDenunciante) === false)
            echo json_encode(array('estado' => 'error', 'texto' => $denunciantesMineriaIlegalModel->errors()));
        else
            echo json_encode(array_merge(array('estado' => 'success', 'id' => $denunciantesMineriaIlegalModel->getInsertID()),$dataDenunciante));
    }

    public function listadoRecepcion()
    {
        $db = \Config\Database::connect();
        $campos = array('hr.id as id_hoja_ruta', 'dn.id as id_denuncia', "to_char(hr.ultimo_fecha_derivacion, 'DD/MM/YYYY') as ultimo_fecha_derivacion", "(CURRENT_DATE - hr.ultimo_fecha_derivacion::date) as dias", 'hr.correlativo as correlativo_hr',
        "CONCAT(ur.nombre_completo,'<br><b>',pr.nombre,'<b>') as remitente", 'hr.ultimo_instruccion', "CONCAT(ua.nombre_completo,'<br><b>',pa.nombre,'<b>') as responsable",
        'dn.correlativo as correlativo_denuncia', 'dn.fk_tipo_denuncia', 'dn.departamento',
        "to_char(dn.created_at, 'DD/MM/YYYY') as fecha_denuncia",
        "CASE WHEN hr.ultimo_fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'. ',etp.nombre,'<br>',etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE CONCAT(etp.orden,'. ',etp.nombre) END as estado_tramite"
        );
        $where = array(
            'hr.deleted_at' => NULL,
            'hr.ultimo_fk_usuario_destinatario' => session()->get('registroUser'),
            'hr.ultimo_estado' => 'DERIVADO',
        );
        $builder = $db->table('mineria_ilegal.hoja_ruta AS hr')
        ->select($campos)
        ->join('mineria_ilegal.denuncias AS dn', 'hr.fk_denuncia = dn.id', 'left')
        ->join('usuarios AS ur', 'hr.ultimo_fk_usuario_remitente = ur.id', 'left')
        ->join('perfiles AS pr', 'ur.fk_perfil = pr.id', 'left')
        ->join('usuarios as ua', 'hr.ultimo_fk_usuario_responsable = ua.id', 'left')
        ->join('perfiles as pa', 'ua.fk_perfil = pa.id', 'left')
        ->join('estado_tramite as etp', 'hr.ultimo_fk_estado_tramite_padre = etp.id', 'left')
        ->join('estado_tramite as eth', 'hr.ultimo_fk_estado_tramite_hijo = eth.id', 'left')
        ->where($where)
        ->orderBY('hr.id', 'DESC');
        $datos = $builder->get()->getResult('array');
        $datos = $this->obtenerDenunciantes($datos);
        //$datos = $this->obtenerCorrespondenciaExterna($datos);
        $campos_listar=array(
            'Fecha', 'Días<br>Pasados', 'Hoja de Ruta', 'Remitente', 'Instrucción', 'Responsable Trámite', 'Estado', 'Fecha Denuncia', 'Denuncia', 'Tipo',  'Denunciante', 'Departamento'
        );
        $campos_reales=array(
            'ultimo_fecha_derivacion', 'dias', 'correlativo_hr', 'remitente', 'ultimo_instruccion', 'responsable', 'estado_tramite', 'fecha_denuncia', 'correlativo_denuncia', 'fk_tipo_denuncia', 'denunciante', 'departamento'
        );

        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Listado de Hojas de Ruta de Minería Ilegal Derivados';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['datos'] = $datos;
        $contenido['campos_listar'] = $campos_listar;
        $contenido['campos_reales'] = $campos_reales;
        $contenido['subtitulo'] = 'Listado de Hojas de Ruta de Minería Ilegal Derivados';
        $contenido['controlador'] = $this->controlador;
        $contenido['id_tramite'] = $this->idTramite;
        $contenido['tipo_denuncias'] = $this->tipoDenuncias;
        $data['content'] = view($this->carpeta.'listado_recepcion', $contenido);
        $data['menu_actual'] = $this->menuActual.'listado_recepcion';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['alertas'] = $this->alertasTramites();
        echo view('templates/template', $data);
    }

    public function recibir($id_hoja_ruta){
        $hojaRutaMineriaIlegalModel = new HojaRutaMineriaIlegalModel();
        $derivacionMineriaIlegalModel = new DerivacionMineriaIlegalModel();
        $where = array(
            'id' => $id_hoja_ruta,
            'ultimo_estado' => 'DERIVADO',
            'deleted_at' => NULL,
            'ultimo_fk_usuario_destinatario' => session()->get('registroUser'),
        );
        if($fila = $hojaRutaMineriaIlegalModel->where($where)->first()){
            $estado = 'RECIBIDO';
            $data = array(
                'id' => $fila['id'],
                'ultimo_estado' => $estado,
                'fk_usuario_actual' => $fila['ultimo_fk_usuario_destinatario'],
                'editar' => true,
            );

            if($hojaRutaMineriaIlegalModel->save($data) === false){
                session()->setFlashdata('fail', $hojaRutaMineriaIlegalModel->errors());
            }

            $where = array(
                'fk_hoja_ruta' => $fila['id'],
            );
            $derivacion = $derivacionMineriaIlegalModel->where($where)->orderBY('id', 'DESC')->first();
            $dataDerivacion = array(
                'id' => $derivacion['id'],
                'estado' => $estado,
                'fecha_recepcion' => date('Y-m-d H:i:s'),
            );

            if($derivacionMineriaIlegalModel->save($dataDerivacion) === false){
                session()->setFlashdata('fail', $derivacionMineriaIlegalModel->errors());
            }

        }
        return redirect()->to($this->controlador.'listado_recepcion');

    }

    public function atender($id_hoja_ruta){
        $hojaRutaMineriaIlegalModel = new HojaRutaMineriaIlegalModel();
        $campos = array('*', 'fk_denuncia', 'correlativo as correlativo_hr', "to_char(fecha_hoja_ruta, 'DD/MM/YYYY HH24:MI') as fecha_hr");
        if($hoja_ruta = $hojaRutaMineriaIlegalModel->select($campos)->find($id_hoja_ruta)){
            $db = \Config\Database::connect();
            $derivacionMineriaIlegalModel = new DerivacionMineriaIlegalModel();
            $denunciasMineriaIlegalModel = new DenunciasMineriaIlegalModel();
            $denunciasHrSincobolMineriaIlegalModel = new DenunciasHrSincobolMineriaIlegalModel();
            $denunciasAreasMinerasMineriaIlegalModel = new DenunciasAreasMinerasMineriaIlegalModel();
            $coordenadasMineriaIlegalModel = new CoordenadasMineriaIlegalModel();
            $adjuntosMineriaIlegalModel = new AdjuntosMineriaIlegalModel();
            $denuncia = $denunciasMineriaIlegalModel->select("*, correlativo as correlativo_denuncia, to_char(fecha_denuncia, 'DD/MM/YYYY HH24:MI') as fecha_denuncia")->find($hoja_ruta['fk_denuncia']);
            $coordenadas = $coordenadasMineriaIlegalModel->where(array('fk_denuncia' => $hoja_ruta['fk_denuncia']))->findAll();

            $hojas_rutas = array();
            $id_hojas_ruta_ant = '';
            if($denunciaHojaRutaSincobol = $denunciasHrSincobolMineriaIlegalModel->where(array('fk_denuncia' => $hoja_ruta['fk_denuncia']))->findAll()){
                foreach($denunciaHojaRutaSincobol as $row){
                    $hojas_rutas[] = $this->obtenerDatosHrInExSincobol($row['fk_hoja_ruta']);
                    $id_hojas_ruta_ant .= $row['fk_hoja_ruta'].',';
                }
            }

            $campos = array('de.id', 'de.nombres', 'de.apellidos', 'de.documento_identidad', 'de.expedido', "de.telefonos", "de.email", 'de.direccion', 'de.documento_identidad_digital');
            $where = array(
                'dd.fk_denuncia' => $hoja_ruta['fk_denuncia'],
            );
            $builder = $db->table('mineria_ilegal.denuncias_denunciantes AS dd')
            ->select($campos)
            ->join('mineria_ilegal.denunciantes AS de', 'dd.fk_denunciante = de.id', 'left')
            ->where($where)
            ->orderBY('dd.id', 'ASC');
            $denunciantes = $builder->get()->getResultArray();
            $id_denunciantes_ant = '';
            foreach($denunciantes as $denunciante)
                $id_denunciantes_ant .= $denunciante['id'].',';

            $denunciasAreasMineras = $denunciasAreasMinerasMineriaIlegalModel->where(array('fk_denuncia'=>$hoja_ruta['fk_denuncia']))->orderBy('id', 'ASC')->findAll();
            $areas_mineras = array();
            $id_areas_mineras_ant = '';
            if($denunciasAreasMineras){
                foreach($denunciasAreasMineras as $row){
                    $areas_mineras[] = $this->obtenerDatosAreaMineraMineriaIlegal($row['fk_area_minera']);
                    $id_areas_mineras_ant .= $row['fk_area_minera'].',';
                }
            }

            $campos = array('de.id', 'de.nombres', 'de.apellidos', 'de.documento_identidad', 'de.expedido', "de.telefonos", "de.email", 'de.direccion', 'de.documento_identidad_digital');
            $where = array(
                'dd.fk_denuncia' => $hoja_ruta['fk_denuncia'],
            );
            $builder = $db->table('mineria_ilegal.denuncias_denunciantes AS dd')
            ->select($campos)
            ->join('mineria_ilegal.denunciantes AS de', 'dd.fk_denunciante = de.id', 'left')
            ->where($where)
            ->orderBY('dd.id', 'ASC');
            $denunciantes = $builder->get()->getResultArray();
            $id_denunciantes_ant = '';
            foreach($denunciantes as $denunciante)
                $id_denunciantes_ant .= $denunciante['id'].',';

            $provincias = $this->obtenerProvincias($denuncia['departamento']);
            $municipios = $this->obtenerMunicipios($denuncia['departamento'], $denuncia['provincia']);
            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Atender Hoja de Ruta de Minería Ilegal';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['subtitulo'] = 'Atender Hoja de Ruta de Minería Ilegal';
            $contenido['accion'] = $this->controlador.'guardar_atender';
            $contenido['controlador'] = $this->controlador;
            $contenido['hoja_ruta'] = $hoja_ruta;
            $contenido['ultima_derivacion'] = $derivacionMineriaIlegalModel->where(array('fk_hoja_ruta' => $hoja_ruta['id']))->orderBy('id', 'DESC')->first();
            $contenido['denuncia'] = $denuncia;
            $contenido['hojas_rutas'] = $hojas_rutas;
            $contenido['tipo_denuncia'] = $denuncia['fk_tipo_denuncia'];
            $contenido['informe_tecnico_digital'] = $denuncia['informe_tecnico_digital'];
            $contenido['id_hoja_ruta'] = $hoja_ruta['id'];
            $contenido['id_denunciantes_ant'] = substr($id_denunciantes_ant, 0, -1);
            $contenido['id_hojas_ruta_ant'] = substr($id_hojas_ruta_ant, 0, -1);
            $contenido['id_areas_mineras_ant'] = substr($id_areas_mineras_ant, 0, -1);
            $contenido['areas_mineras'] = $areas_mineras;
            $contenido['denunciantes'] = $denunciantes;
            $contenido['coordenadas'] = $this->transformarCoordenadas($coordenadas);
            $contenido['adjuntos'] = $adjuntosMineriaIlegalModel->where(array('fk_denuncia' => $hoja_ruta['fk_denuncia']))->findAll();
            $contenido['documentos'] = $this->obtenerDocumentosAtender($hoja_ruta['id']);
            $contenido['expedidos'] = $this->expedidos;
            $contenido['tipo_denuncias'] = $this->tipoDenuncias;
            $contenido['tipos_origen_oficio'] = array_merge(array(''=>'SELECCIONE UNA OPCIÓN'), $this->tiposOrigenOficio);
            $contenido['departamentos'] = array_merge(array(''=>'SELECCIONE EL DEPARTAMENTO'), $this->obtenerDepartamentos());
            $contenido['provincias'] = array_merge(array(''=>'SELECCIONE LA PROVINCIA'),$provincias);
            $contenido['municipios'] = array(''=>'SELECCIONE EL MUNICIPIO') + $municipios;
            $contenido['estadosTramites'] = $this->obtenerEstadosTramites($this->idTramite);
            $contenido['id_estado_padre'] = $hoja_ruta['ultimo_fk_estado_tramite_padre'];
            if($hoja_ruta['ultimo_fk_estado_tramite_hijo']){
                $contenido['estadosTramitesHijo'] = $this->obtenerEstadosTramitesHijo($hoja_ruta['ultimo_fk_estado_tramite_padre']);
                $contenido['id_estado_hijo'] = $hoja_ruta['ultimo_fk_estado_tramite_hijo'];
                //$contenido['anexar_documentos'] = $hoja_ruta['anexar_documentos_hijo'];
                $contenido['anexar_documentos'] = '';
            }else{
                $contenido['anexar_documentos'] = '';
                //$contenido['anexar_documentos'] = $ultima_derivacion['anexar_documentos_padre'];
            }
            $contenido['oficinas'] = array_merge(array(''=>'SELECCIONE LA DIRECCIÓN DEPARTAMENTAL O REGIONAL'), $this->obtenerDireccionesRegionales());
            $contenido['id_tramite'] = $this->idTramite;
            $contenido['nuevo_denunciante'] = view($this->carpeta.'nuevo_denunciante', $contenido);
            $data['content'] = view($this->carpeta.'atender', $contenido);
            $data['menu_actual'] = $this->menuActual.'mis_tramites';
            switch($denuncia['fk_tipo_denuncia']){
                case 1:
                case 2:
                    $data['validacion_js'] = 'mineria-ilegal-atender-denunciante-validation.js';
                    break;
                case 3:
                    $data['validacion_js'] = 'mineria-ilegal-atender-origen-validation.js';
                    break;
            }
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['alertas'] = $this->alertasTramites();
            $data['mapas'] = true;
            $data['puntos'] = $coordenadas;
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador);
        }
    }
    public function guardarAtender(){
        if ($this->request->getPost()) {
            $denunciantesMineriaIlegalModel = new DenunciantesMineriaIlegalModel();
            $adjuntosMineriaIlegalModel = new AdjuntosMineriaIlegalModel();
            $id_hoja_ruta = $this->request->getPost('id_hoja_ruta');
            $id_denuncia = $this->request->getPost('id_denuncia');
            $id_denunciantes = $this->request->getPost('id_denunciantes');
            $id_areas_mineras = $this->request->getPost('id_areas_mineras');
            $tipo_denuncia = $this->request->getPost('tipo_denuncia');
            $id_hojas_rutas = $this->request->getPost('id_hojas_rutas');
            $anexar_hr = $this->request->getPost('anexar_hr');
            $documentos = $this->obtenerDocumentosAtender($id_hoja_ruta);
            $camposValidacion = array(
                'id_hoja_ruta' => [
                    'rules' => 'required',
                ],
                'id_denuncia' => [
                    'rules' => 'required',
                ],
                'fk_municipio' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar el Municipio.',
                    ]
                ],
                'comunidad_localidad' => [
                    'rules' => 'required',
                ],
                'descripcion_lugar' => [
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
            );
            if($tipo_denuncia==3){
                $camposValidacion = array_merge($camposValidacion, array(
                    'origen_oficio' => [
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'Debe seleccionar una opción.',
                        ]
                    ],
                    'informe_tecnico_numero' => [
                        'rules' => 'required',
                    ],
                    'informe_tecnico_fecha' => [
                        'rules' => 'required',
                    ],
                    'descripcion_oficio' => [
                        'rules' => 'required',
                    ],
                ));
            }

            if(!$this->validate($camposValidacion)){
                if(isset($id_hojas_rutas) && count($id_hojas_rutas) > 0){
                    $hojas_rutas = array();
                    foreach($id_hojas_rutas as $id_hoja_ruta)
                        $hojas_rutas[] = $this->obtenerDatosHrInExSincobol($id_hoja_ruta);
                    $contenido['hojas_rutas'] = $hojas_rutas;
                }
                if(isset($id_areas_mineras) && count($id_areas_mineras) > 0){
                    $areas_mineras = array();
                    foreach($id_areas_mineras as $id_area_minera)
                        $areas_mineras[] = $this->obtenerDatosAreaMineraMineriaIlegal($id_area_minera);
                    $contenido['areas_mineras'] = $areas_mineras;
                }
                if(isset($anexar_hr) && count($anexar_hr) > 0){
                    $hojas_ruta_anexadas = array();
                    foreach($anexar_hr as $id_hoja_ruta)
                        $hojas_ruta_anexadas[] = $this->obtnerDatosSelectHrInExSincobol($id_hoja_ruta);
                    $contenido['hojas_ruta_anexadas'] = $hojas_ruta_anexadas;
                }

                $provincias = $this->obtenerProvincias($this->request->getPost('departamento'));
                $municipios = $this->obtenerMunicipios($this->request->getPost('departamento'), $this->request->getPost('provincia'));

                $cabera['titulo'] = $this->titulo;
                $cabera['navegador'] = true;
                $cabera['subtitulo'] = 'Atender Hoja de Ruta de Minería Ilegal';
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['subtitulo'] = 'Atender Hoja de Ruta de Minería Ilegal';
                $contenido['id_hoja_ruta'] = $id_hoja_ruta;
                $contenido['tipo_denuncia'] = $tipo_denuncia;
                $contenido['informe_tecnico_digital'] = $this->request->getPost('informe_tecnico_digital');
                $contenido['documentos'] = $documentos;
                if($tipo_denuncia!=3)
                    $contenido['denunciantes'] = $denunciantesMineriaIlegalModel->whereIn('id', $id_denunciantes)->findAll();
                $contenido['usu_destinatario'] = $this->obtenerUsuario($this->request->getPost('fk_usuario_destinatario'));
                $contenido['accion'] = $this->controlador.'guardar_atender';
                $contenido['controlador'] = $this->controlador;
                $contenido['adjuntos'] = $adjuntosMineriaIlegalModel->where(array('fk_denuncia' => $id_denuncia))->findAll();
                $contenido['expedidos'] = $this->expedidos;
                $contenido['tipo_denuncias'] = $this->tipoDenuncias;
                $contenido['tipos_origen_oficio'] = array_merge(array(''=>'SELECCIONE UNA OPCIÓN'), $this->tiposOrigenOficio);
                $contenido['departamentos'] = array_merge(array(''=>'SELECCIONE EL DEPARTAMENTO'), $this->obtenerDepartamentos());
                $contenido['provincias'] = array_merge(array(''=>'SELECCIONE LA PROVINCIA'),$provincias);
                $contenido['municipios'] = array(''=>'SELECCIONE EL MUNICIPIO') + $municipios;
                $contenido['oficinas'] = array_merge(array(''=>'SELECCIONE LA DIRECCIÓN DEPARTAMENTAL O REGIONAL'), $this->obtenerDireccionesRegionales());
                $contenido['id_tramite'] = $this->idTramite;
                $contenido['estadosTramites'] = $this->obtenerEstadosTramites($this->idTramite);
                $contenido['id_estado_padre'] = $this->request->getPost('fk_estado_tramite');
                $contenido['estadosTramitesHijo'] = $this->obtenerEstadosTramitesHijo($this->request->getPost('fk_estado_tramite'));
                $contenido['id_estado_hijo'] = $this->request->getPost('fk_estado_tramite_hijo');
                $contenido['nuevo_denunciante'] = view($this->carpeta.'nuevo_denunciante', $contenido);
                $data['content'] = view($this->carpeta.'atender', $contenido);
                $data['menu_actual'] = $this->menuActual.'mis_tramites';
                switch($tipo_denuncia){
                    case 1:
                    case 2:
                        $data['validacion_js'] = 'mineria-ilegal-atender-denunciante-validation.js';
                        break;
                    case 3:
                        $data['validacion_js'] = 'mineria-ilegal-atender-origen-validation.js';
                        break;
                }
                $data['tramites_menu'] = $this->tramitesMenu();
                $data['alertas'] = $this->alertasTramites();
                $data['mapas'] = true;
                $data['puntos'] = $this->obtenerCoordenadas($this->request->getPost('coordenadas'));
                echo view('templates/template', $data);
            }else{
                $municipiosModel = new MunicipiosModel();
                $denunciasMineriaIlegalModel = new DenunciasMineriaIlegalModel();
                $denunciasHrSincobolMineriaIlegalModel = new DenunciasHrSincobolMineriaIlegalModel();
                $denunciasDenunciantesMineriaIleglaModel = new DenunciasDenunciantesMineriaIlegalModel();
                $denunciasAreasMinerasMineriaIlegalModel = new DenunciasAreasMinerasMineriaIlegalModel();
                $coordenadasMineriaIlegalModel = new CoordenadasMineriaIlegalModel();
                $hojaRutaMineriaIlegalModel = new HojaRutaMineriaIlegalModel();
                $derivacionMineriaIlegalModel = new DerivacionMineriaIlegalModel();
                $documentosModel = new DocumentosModel();

                $ubicacion = $municipiosModel->find($this->request->getPost('fk_municipio'));
                if($tipo_denuncia==3)
                    $dataDenuncia = array(
                        'id' => $id_denuncia,
                        'fk_municipio' => $this->request->getPost('fk_municipio'),
                        'origen_oficio' => $this->request->getPost('origen_oficio'),
                        'enlace' => $this->request->getPost('enlace'),
                        'informe_tecnico_numero' => mb_strtoupper($this->request->getPost('informe_tecnico_numero')),
                        'informe_tecnico_fecha' => $this->request->getPost('informe_tecnico_fecha'),
                        'descripcion_oficio' => mb_strtoupper($this->request->getPost('descripcion_oficio')),
                        'comunidad_localidad' => mb_strtoupper($this->request->getPost('comunidad_localidad')),
                        'descripcion_lugar' => mb_strtoupper($this->request->getPost('descripcion_lugar')),
                        'autores' => mb_strtoupper($this->request->getPost('autores')),
                        'persona_juridica' => mb_strtoupper($this->request->getPost('persona_juridica')),
                        'descripcion_materiales' => mb_strtoupper($this->request->getPost('descripcion_materiales')),
                        'fk_usuario_editor' => session()->get('registroUser'),
                        'departamento' => $ubicacion['departamento'],
                        'provincia' => $ubicacion['provincia'],
                        'municipio' => $ubicacion['municipio'],
                        'tiene_area_minera' => ( isset($id_areas_mineras) && count($id_areas_mineras)>0) ? 'true' : 'false',
                    );
                else
                    $dataDenuncia = array(
                        'id' => $id_denuncia,
                        'fk_municipio' => $this->request->getPost('fk_municipio'),
                        'comunidad_localidad' => mb_strtoupper($this->request->getPost('comunidad_localidad')),
                        'descripcion_lugar' => mb_strtoupper($this->request->getPost('descripcion_lugar')),
                        'autores' => mb_strtoupper($this->request->getPost('autores')),
                        'persona_juridica' => mb_strtoupper($this->request->getPost('persona_juridica')),
                        'descripcion_materiales' => mb_strtoupper($this->request->getPost('descripcion_materiales')),
                        'fk_usuario_editor' => session()->get('registroUser'),
                        'departamento' => $ubicacion['departamento'],
                        'provincia' => $ubicacion['provincia'],
                        'municipio' => $ubicacion['municipio'],
                        'tiene_area_minera' => ( isset($id_areas_mineras) && count($id_areas_mineras)>0 ) ? 'true' : 'false',
                    );

                if($denunciasMineriaIlegalModel->save($dataDenuncia) === false)
                    session()->setFlashdata('fail', $denunciasMineriaIlegalModel->errors());

                if(isset($id_denunciantes) && implode(',',$id_denunciantes) != $this->request->getPost('id_denunciantes_ant')){
                    $this->liberarDenunciantes($id_denuncia);
                    foreach($id_denunciantes as $id_denunciante){
                        $dataDenunciaDenunciante = array(
                            'fk_denuncia' => $id_denuncia,
                            'fk_denunciante' => $id_denunciante,
                        );
                        if($denunciasDenunciantesMineriaIleglaModel->insert($dataDenunciaDenunciante) === false)
                            session()->setFlashdata('fail', $denunciasDenunciantesMineriaIleglaModel->errors());
                    }
                }

                if(isset($id_hojas_rutas) && implode(',',$id_hojas_rutas) != $this->request->getPost('id_hojas_ruta_ant')){
                    $this->liberarHojasRutaSincobol($id_denuncia);
                    foreach($id_hojas_rutas as $id_hoja_ruta){
                        $dataHojaRutaSincobol = array(
                            'fk_denuncia' => $id_denuncia,
                            'fk_hoja_ruta' => $id_hoja_ruta,
                        );
                        if($denunciasHrSincobolMineriaIlegalModel->insert($dataHojaRutaSincobol) === false)
                            session()->setFlashdata('fail', $denunciasHrSincobolMineriaIlegalModel->errors());
                        else
                            $this->archivarHrSincobol($id_hoja_ruta, 'ANEXADO AL SISTEMA DE SEGUIMIENTO Y CONTROL DE TRAMITES POR '.session()->get('registroUserName'));
                    }
                }

                if(isset($id_areas_mineras) && implode(',',$id_areas_mineras) != $this->request->getPost('id_areas_mineras_ant')){
                    $this->liberarAreasMineras($id_denuncia);
                    foreach($id_areas_mineras as $id_area_minera){
                        $dataAreaMinera = array(
                            'fk_denuncia' => $id_denuncia,
                            'fk_area_minera' => $id_area_minera,
                        );
                        if($denunciasAreasMinerasMineriaIlegalModel->insert($dataAreaMinera) === false)
                            session()->setFlashdata('fail', $denunciasAreasMinerasMineriaIlegalModel->errors());
                    }
                }

                if($this->obtenerCoordenadas($this->request->getPost('coordenadas')) !== $this->obtenerCoordenadas($this->request->getPost('coordenadas_ant'))){
                    $this->vaciarCoordenadas($id_denuncia);
                    $coordenadas = $this->obtenerCoordenadas($this->request->getPost('coordenadas'));
                    if(count($coordenadas)>0){
                        foreach($coordenadas as $coordenada){
                            $dataCoordenada = array(
                                'fk_denuncia' => $id_denuncia,
                                'latitud' => $coordenada['latitud'],
                                'longitud' => $coordenada['longitud'],
                            );
                            if($coordenadasMineriaIlegalModel->insert($dataCoordenada) === false)
                                session()->setFlashdata('fail', $coordenadasMineriaIlegalModel->errors());
                        }
                    }
                }

                if ($adjuntos = $this->request->getFiles()) {
                    foreach($adjuntos as $nombre => $adjunto){
                        if($nombre == 'adjuntos' && count($adjunto) > 0){
                            $nombres = $this->request->getPost('nombres');
                            $cites = $this->request->getPost('cites');
                            $fecha_cites = $this->request->getPost('fecha_cites');
                            foreach($adjunto as $i => $archivo){
                                $tipoDocDigital = $this->obtenerTipoArchivo($archivo->guessExtension());
                                $nombreDocDigital = $archivo->getRandomName();
                                $archivo->move($this->rutaArchivos,$nombreDocDigital);
                                $nombreDocDigital = $this->rutaArchivos.$nombreDocDigital;
                                $dataAdjunto = array(
                                    'fk_denuncia' => $id_denuncia,
                                    'nombre' => mb_strtoupper($nombres[$i]),
                                    'cite' => mb_strtoupper($cites[$i]),
                                    'fecha_cite'=>((!empty($fecha_cites[$i])) ? $fecha_cites[$i] : NULL),
                                    'tipo' => $tipoDocDigital,
                                    'adjunto' => $nombreDocDigital,
                                    'fk_usuario_creador' => session()->get('registroUser'),
                                );
                                if($adjuntosMineriaIlegalModel->insert($dataAdjunto) === false)
                                    session()->setFlashdata('fail', $adjuntosMineriaIlegalModel->errors());
                            }
                        }
                    }
                }

                $estado = 'DERIVADO';
                $dataHojaRuta = array(
                    'id' => $id_hoja_ruta,
                    'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                    'ultimo_estado' => $estado,
                    'ultimo_fk_estado_tramite_padre' => $this->request->getPost('fk_estado_tramite'),
                    'ultimo_fk_estado_tramite_hijo' => ((!empty($this->request->getPost('fk_estado_tramite_hijo'))) ? $this->request->getPost('fk_estado_tramite_hijo') : NULL),
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
                    $data['ultimo_fk_documentos'] = substr($ultimo_fk_documentos, 0, -1);
                }

                if($hojaRutaMineriaIlegalModel->save($dataHojaRuta) === false){
                    session()->setFlashdata('fail', $hojaRutaMineriaIlegalModel->errors());
                }else{
                    $dataDerivacion = array(
                        'fk_hoja_ruta' => $id_hoja_ruta,
                        'fk_estado_tramite_padre' => $this->request->getPost('fk_estado_tramite'),
                        'fk_estado_tramite_hijo' => ((!empty($this->request->getPost('fk_estado_tramite_hijo'))) ? $this->request->getPost('fk_estado_tramite_hijo') : NULL),
                        'observaciones' => mb_strtoupper($this->request->getPost('observaciones')),
                        'instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                        'motivo_anexo' => mb_strtoupper($this->request->getPost('motivo_anexo')),
                        'fk_usuario_remitente' => session()->get('registroUser'),
                        'fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                        'estado' => $estado,
                        'fk_usuario_creador' => session()->get('registroUser'),
                        'fk_usuario_responsable'=>($this->request->getPost('responsable') ? $this->request->getPost('fk_usuario_destinatario') : $this->request->getPost('ultimo_fk_usuario_responsable')),
                    );

                    if($derivacionMineriaIlegalModel->insert($dataDerivacion) === false){
                        session()->setFlashdata('fail', $derivacionMineriaIlegalModel->errors());
                    }else{
                        $id_derivacion = $derivacionMineriaIlegalModel->getInsertID();
                        if(count($documentos)>0){
                            foreach($documentos as $documento){
                                $docDigital = $this->request->getFile('adjunto'.$documento['id']);
                                $tipoDocDigital = $this->obtenerTipoArchivo($docDigital->guessExtension());
                                $nombreDocDigital = $docDigital->getRandomName();
                                $docDigital->move($this->rutaDocumentos,$nombreDocDigital);
                                $nombreDocDigital = $this->rutaDocumentos.$nombreDocDigital;
                                $dataDocumento = array(
                                    'id' => $documento['id'],
                                    'estado' => 'ANEXADO',
                                    'fk_derivacion' => $id_derivacion,
                                    'fecha_notificacion'=>((!empty($this->request->getPost('fecha_notificacion'.$documento['id']))) ? $this->request->getPost('fecha_notificacion'.$documento['id']) : NULL),
                                    'doc_digital' => $nombreDocDigital,
                                );
                                if($documentosModel->save($dataDocumento) === false){
                                    session()->setFlashdata('fail', $documentosModel->errors());
                                }
                            }
                        }

                        if($anexar_hr){
                            foreach($anexar_hr as $fk_hoja_ruta){
                                if(!$this->anexarHrSincobolMejorado($id_derivacion, $fk_hoja_ruta, $id_denuncia, session()->get('registroUserName')))
                                    session()->setFlashdata('fail', 'No se anexo la H.R.'.$fk_hoja_ruta);
                            }
                        }

                        $dataDerivacionActualizacion = array(
                            'id' => $this->request->getPost('id_derivacion'),
                            'estado' => 'ATENDIDO',
                            'fecha_atencion' => date('Y-m-d H:i:s'),
                        );
                        if($derivacionMineriaIlegalModel->save($dataDerivacionActualizacion) === false)
                            session()->setFlashdata('fail', $derivacionMineriaIlegalModel->errors());
                        else
                            session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información.');

                    }
                    session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información.');
                }
                return redirect()->to($this->controlador.'mis_tramites');
            }
        }
    }

    public function editar($id_hoja_ruta){
        $hojaRutaMineriaIlegalModel = new HojaRutaMineriaIlegalModel();
        $campos = array('*', 'fk_denuncia', 'correlativo as correlativo_hr', "to_char(fecha_hoja_ruta, 'DD/MM/YYYY HH24:MI') as fecha_hr");
        if($hoja_ruta = $hojaRutaMineriaIlegalModel->select($campos)->find($id_hoja_ruta)){
            $db = \Config\Database::connect();
            $derivacionMineriaIlegalModel = new DerivacionMineriaIlegalModel();
            $denunciasMineriaIlegalModel = new DenunciasMineriaIlegalModel();
            $denunciasHrSincobolMineriaIlegalModel = new DenunciasHrSincobolMineriaIlegalModel();
            $denunciasAreasMinerasMineriaIlegalModel = new DenunciasAreasMinerasMineriaIlegalModel();
            $coordenadasMineriaIlegalModel = new CoordenadasMineriaIlegalModel();
            $adjuntosMineriaIlegalModel = new AdjuntosMineriaIlegalModel();
            $hojasRutaAnexadasMineriaIlegalModel = new HojasRutaAnexadasMineriaIlegalModel();
            $denuncia = $denunciasMineriaIlegalModel->select("*, correlativo as correlativo_denuncia, to_char(fecha_denuncia, 'DD/MM/YYYY HH24:MI') as fecha_denuncia")->find($hoja_ruta['fk_denuncia']);
            $coordenadas = $coordenadasMineriaIlegalModel->where(array('fk_denuncia' => $hoja_ruta['fk_denuncia']))->findAll();

            $derivacion = $derivacionMineriaIlegalModel->where(array('fk_hoja_ruta' => $hoja_ruta['id']))->orderBy('id', 'DESC')->first();
            $hojas_ruta_anexadas = array();
            $id_hojas_ruta_anexadas_ant = '';
            if($hr_anexadas = $hojasRutaAnexadasMineriaIlegalModel->where(array('fk_derivacion' => $derivacion['id']))->orderBy('id', 'ASC')->findAll()){
                foreach($hr_anexadas as $hr_anexada){
                    $hojas_ruta_anexadas[] = $this->obtnerDatosSelectHrInExSincobolEditar($hr_anexada['fk_hoja_ruta']);
                    $id_hojas_ruta_anexadas_ant .= $hr_anexada['fk_hoja_ruta'].',';
                }
            }

            $hojas_rutas = array();
            $id_hojas_ruta_ant = '';
            if($denunciaHojaRutaSincobol = $denunciasHrSincobolMineriaIlegalModel->where(array('fk_denuncia' => $hoja_ruta['fk_denuncia']))->findAll()){
                foreach($denunciaHojaRutaSincobol as $row){
                    $hojas_rutas[] = $this->obtenerDatosHrInExSincobol($row['fk_hoja_ruta']);
                    $id_hojas_ruta_ant .= $row['fk_hoja_ruta'].',';
                }
            }

            $campos = array('de.id', 'de.nombres', 'de.apellidos', 'de.documento_identidad', 'de.expedido', "de.telefonos", "de.email", 'de.direccion', 'de.documento_identidad_digital');
            $where = array(
                'dd.fk_denuncia' => $hoja_ruta['fk_denuncia'],
            );
            $builder = $db->table('mineria_ilegal.denuncias_denunciantes AS dd')
            ->select($campos)
            ->join('mineria_ilegal.denunciantes AS de', 'dd.fk_denunciante = de.id', 'left')
            ->where($where)
            ->orderBY('dd.id', 'ASC');
            $denunciantes = $builder->get()->getResultArray();
            $id_denunciantes_ant = '';
            foreach($denunciantes as $denunciante)
                $id_denunciantes_ant .= $denunciante['id'].',';

            $denunciasAreasMineras = $denunciasAreasMinerasMineriaIlegalModel->where(array('fk_denuncia'=>$hoja_ruta['fk_denuncia']))->orderBy('id', 'ASC')->findAll();
            $areas_mineras = array();
            $id_areas_mineras_ant = '';
            if($denunciasAreasMineras){
                foreach($denunciasAreasMineras as $row){
                    $areas_mineras[] = $this->obtenerDatosAreaMineraMineriaIlegal($row['fk_area_minera']);
                    $id_areas_mineras_ant .= $row['fk_area_minera'].',';
                }
            }

            $campos = array('de.id', 'de.nombres', 'de.apellidos', 'de.documento_identidad', 'de.expedido', "de.telefonos", "de.email", 'de.direccion', 'de.documento_identidad_digital');
            $where = array(
                'dd.fk_denuncia' => $hoja_ruta['fk_denuncia'],
            );
            $builder = $db->table('mineria_ilegal.denuncias_denunciantes AS dd')
            ->select($campos)
            ->join('mineria_ilegal.denunciantes AS de', 'dd.fk_denunciante = de.id', 'left')
            ->where($where)
            ->orderBY('dd.id', 'ASC');
            $denunciantes = $builder->get()->getResultArray();
            $id_denunciantes_ant = '';
            foreach($denunciantes as $denunciante)
                $id_denunciantes_ant .= $denunciante['id'].',';

            $provincias = $this->obtenerProvincias($denuncia['departamento']);
            $municipios = $this->obtenerMunicipios($denuncia['departamento'], $denuncia['provincia']);
            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Editar Hoja de Ruta de Minería Ilegal';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['subtitulo'] = 'Editar Hoja de Ruta de Minería Ilegal';
            $contenido['accion'] = $this->controlador.'guardar_editar';
            $contenido['controlador'] = $this->controlador;
            $contenido['hoja_ruta'] = $hoja_ruta;
            $contenido['derivacion'] = $derivacion;
            $contenido['denuncia'] = $denuncia;
            $contenido['hojas_rutas'] = $hojas_rutas;
            $contenido['hojas_ruta_anexadas'] = $hojas_ruta_anexadas;
            $contenido['tipo_denuncia'] = $denuncia['fk_tipo_denuncia'];
            $contenido['informe_tecnico_digital'] = $denuncia['informe_tecnico_digital'];
            $contenido['id_hoja_ruta'] = $hoja_ruta['id'];
            $contenido['id_denunciantes_ant'] = substr($id_denunciantes_ant, 0, -1);
            $contenido['id_hojas_ruta_ant'] = substr($id_hojas_ruta_ant, 0, -1);
            $contenido['id_areas_mineras_ant'] = substr($id_areas_mineras_ant, 0, -1);
            $contenido['id_hojas_ruta_anexadas_ant'] = substr($id_hojas_ruta_anexadas_ant, 0, -1);
            $contenido['areas_mineras'] = $areas_mineras;
            $contenido['denunciantes'] = $denunciantes;
            $contenido['coordenadas'] = $this->transformarCoordenadas($coordenadas);
            $contenido['adjuntos'] = $adjuntosMineriaIlegalModel->where(array('fk_denuncia' => $hoja_ruta['fk_denuncia']))->findAll();
            $contenido['documentos'] = $this->obtenerDocumentosEditar($hoja_ruta['id'], $derivacion['id']);
            $contenido['expedidos'] = $this->expedidos;
            $contenido['tipo_denuncias'] = $this->tipoDenuncias;
            $contenido['tipos_origen_oficio'] = array_merge(array(''=>'SELECCIONE UNA OPCIÓN'), $this->tiposOrigenOficio);
            $contenido['departamentos'] = array_merge(array(''=>'SELECCIONE EL DEPARTAMENTO'), $this->obtenerDepartamentos());
            $contenido['provincias'] = array_merge(array(''=>'SELECCIONE LA PROVINCIA'),$provincias);
            $contenido['municipios'] = array(''=>'SELECCIONE EL MUNICIPIO') + $municipios;
            $contenido['estadosTramites'] = $this->obtenerEstadosTramites($this->idTramite);
            $contenido['id_estado_padre'] = $hoja_ruta['ultimo_fk_estado_tramite_padre'];
            if($hoja_ruta['ultimo_fk_estado_tramite_hijo']){
                $contenido['estadosTramitesHijo'] = $this->obtenerEstadosTramitesHijo($hoja_ruta['ultimo_fk_estado_tramite_padre']);
                $contenido['id_estado_hijo'] = $hoja_ruta['ultimo_fk_estado_tramite_hijo'];
                //$contenido['anexar_documentos'] = $hoja_ruta['anexar_documentos_hijo'];
                $contenido['anexar_documentos'] = '';
            }else{
                $contenido['anexar_documentos'] = '';
                //$contenido['anexar_documentos'] = $ultima_derivacion['anexar_documentos_padre'];
            }
            $contenido['usu_destinatario'] = $this->obtenerUsuario($derivacion['fk_usuario_destinatario']);
            $contenido['oficinas'] = array_merge(array(''=>'SELECCIONE LA DIRECCIÓN DEPARTAMENTAL O REGIONAL'), $this->obtenerDireccionesRegionales());
            $contenido['id_tramite'] = $this->idTramite;
            $contenido['nuevo_denunciante'] = view($this->carpeta.'nuevo_denunciante', $contenido);
            $data['content'] = view($this->carpeta.'editar', $contenido);
            $data['menu_actual'] = $this->menuActual.'mis_tramites';
            switch($denuncia['fk_tipo_denuncia']){
                case 1:
                case 2:
                    $data['validacion_js'] = 'mineria-ilegal-editar-denunciante-validation.js';
                    break;
                case 3:
                    $data['validacion_js'] = 'mineria-ilegal-editar-origen-validation.js';
                    break;
            }
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['alertas'] = $this->alertasTramites();
            $data['mapas'] = true;
            $data['puntos'] = $coordenadas;
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador);
        }
    }
    public function guardarEditar(){
        if ($this->request->getPost()) {
            $denunciantesMineriaIlegalModel = new DenunciantesMineriaIlegalModel();
            $adjuntosMineriaIlegalModel = new AdjuntosMineriaIlegalModel();
            $id_hoja_ruta = $this->request->getPost('id_hoja_ruta');
            $id_derivacion = $this->request->getPost('id_derivacion');
            $id_denuncia = $this->request->getPost('id_denuncia');
            $id_denunciantes = $this->request->getPost('id_denunciantes');
            $id_areas_mineras = $this->request->getPost('id_areas_mineras');
            $tipo_denuncia = $this->request->getPost('tipo_denuncia');
            $id_hojas_rutas = $this->request->getPost('id_hojas_rutas');
            $documentos = $this->obtenerDocumentosEditar($id_hoja_ruta, $id_derivacion);
            $anexar_hr = $this->request->getPost('anexar_hr');
            $camposValidacion = array(
                'id_hoja_ruta' => [
                    'rules' => 'required',
                ],
                'id_derivacion' => [
                    'rules' => 'required',
                ],
                'id_denuncia' => [
                    'rules' => 'required',
                ],
                'fk_municipio' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar el Municipio.',
                    ]
                ],
                'comunidad_localidad' => [
                    'rules' => 'required',
                ],
                'descripcion_lugar' => [
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
            );
            if($tipo_denuncia==3){
                $camposValidacion = array_merge($camposValidacion, array(
                    'origen_oficio' => [
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'Debe seleccionar una opción.',
                        ]
                    ],
                    'informe_tecnico_numero' => [
                        'rules' => 'required',
                    ],
                    'informe_tecnico_fecha' => [
                        'rules' => 'required',
                    ],
                    'descripcion_oficio' => [
                        'rules' => 'required',
                    ],
                ));
            }

            if(!$this->validate($camposValidacion)){
                if(isset($id_hojas_rutas) && count($id_hojas_rutas) > 0){
                    $hojas_rutas = array();
                    foreach($id_hojas_rutas as $id_hoja_ruta)
                        $hojas_rutas[] = $this->obtenerDatosHrInExSincobol($id_hoja_ruta);
                    $contenido['hojas_rutas'] = $hojas_rutas;
                }
                if(isset($id_areas_mineras) && count($id_areas_mineras) > 0){
                    $areas_mineras = array();
                    foreach($id_areas_mineras as $id_area_minera)
                        $areas_mineras[] = $this->obtenerDatosAreaMineraMineriaIlegal($id_area_minera);
                    $contenido['areas_mineras'] = $areas_mineras;
                }
                if(isset($anexar_hr) && count($anexar_hr) > 0){
                    $hojas_ruta_anexadas = array();
                    foreach($anexar_hr as $id_hoja_ruta)
                        $hojas_ruta_anexadas[] = $this->obtnerDatosSelectHrInExSincobolEditar($id_hoja_ruta);
                    $contenido['hojas_ruta_anexadas'] = $hojas_ruta_anexadas;
                }

                $provincias = $this->obtenerProvincias($this->request->getPost('departamento'));
                $municipios = $this->obtenerMunicipios($this->request->getPost('departamento'), $this->request->getPost('provincia'));

                $cabera['titulo'] = $this->titulo;
                $cabera['navegador'] = true;
                $cabera['subtitulo'] = 'Editar Hoja de Ruta de Minería Ilegal';
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['subtitulo'] = 'Editar Hoja de Ruta de Minería Ilegal';
                $contenido['id_hoja_ruta'] = $id_hoja_ruta;
                $contenido['tipo_denuncia'] = $tipo_denuncia;
                $contenido['informe_tecnico_digital'] = $this->request->getPost('informe_tecnico_digital');
                $contenido['documentos'] = $documentos;
                if($tipo_denuncia!=3)
                    $contenido['denunciantes'] = $denunciantesMineriaIlegalModel->whereIn('id', $id_denunciantes)->findAll();
                $contenido['usu_destinatario'] = $this->obtenerUsuario($this->request->getPost('fk_usuario_destinatario'));
                $contenido['accion'] = $this->controlador.'guardar_editar';
                $contenido['controlador'] = $this->controlador;
                $contenido['adjuntos'] = $adjuntosMineriaIlegalModel->where(array('fk_denuncia' => $id_denuncia))->findAll();
                $contenido['expedidos'] = $this->expedidos;
                $contenido['tipo_denuncias'] = $this->tipoDenuncias;
                $contenido['tipos_origen_oficio'] = array_merge(array(''=>'SELECCIONE UNA OPCIÓN'), $this->tiposOrigenOficio);
                $contenido['departamentos'] = array_merge(array(''=>'SELECCIONE EL DEPARTAMENTO'), $this->obtenerDepartamentos());
                $contenido['provincias'] = array_merge(array(''=>'SELECCIONE LA PROVINCIA'),$provincias);
                $contenido['municipios'] = array(''=>'SELECCIONE EL MUNICIPIO') + $municipios;
                $contenido['oficinas'] = array_merge(array(''=>'SELECCIONE LA DIRECCIÓN DEPARTAMENTAL O REGIONAL'), $this->obtenerDireccionesRegionales());
                $contenido['id_tramite'] = $this->idTramite;
                $contenido['estadosTramites'] = $this->obtenerEstadosTramites($this->idTramite);
                $contenido['id_estado_padre'] = $this->request->getPost('fk_estado_tramite');
                $contenido['estadosTramitesHijo'] = $this->obtenerEstadosTramitesHijo($this->request->getPost('fk_estado_tramite'));
                $contenido['id_estado_hijo'] = $this->request->getPost('fk_estado_tramite_hijo');
                $contenido['nuevo_denunciante'] = view($this->carpeta.'nuevo_denunciante', $contenido);
                $data['content'] = view($this->carpeta.'editar', $contenido);
                $data['menu_actual'] = $this->menuActual.'mis_tramites';
                switch($tipo_denuncia){
                    case 1:
                    case 2:
                        $data['validacion_js'] = 'mineria-ilegal-editar-denunciante-validation.js';
                        break;
                    case 3:
                        $data['validacion_js'] = 'mineria-ilegal-editar-origen-validation.js';
                        break;
                }
                $data['tramites_menu'] = $this->tramitesMenu();
                $data['alertas'] = $this->alertasTramites();
                $data['mapas'] = true;
                $data['puntos'] = $this->obtenerCoordenadas($this->request->getPost('coordenadas'));
                echo view('templates/template', $data);
            }else{
                $municipiosModel = new MunicipiosModel();
                $denunciasMineriaIlegalModel = new DenunciasMineriaIlegalModel();
                $denunciasHrSincobolMineriaIlegalModel = new DenunciasHrSincobolMineriaIlegalModel();
                $denunciasDenunciantesMineriaIleglaModel = new DenunciasDenunciantesMineriaIlegalModel();
                $denunciasAreasMinerasMineriaIlegalModel = new DenunciasAreasMinerasMineriaIlegalModel();
                $coordenadasMineriaIlegalModel = new CoordenadasMineriaIlegalModel();
                $hojaRutaMineriaIlegalModel = new HojaRutaMineriaIlegalModel();
                $derivacionMineriaIlegalModel = new DerivacionMineriaIlegalModel();
                $documentosModel = new DocumentosModel();

                $ubicacion = $municipiosModel->find($this->request->getPost('fk_municipio'));
                if($tipo_denuncia==3)
                    $dataDenuncia = array(
                        'id' => $id_denuncia,
                        'fk_municipio' => $this->request->getPost('fk_municipio'),
                        'origen_oficio' => $this->request->getPost('origen_oficio'),
                        'enlace' => $this->request->getPost('enlace'),
                        'informe_tecnico_numero' => mb_strtoupper($this->request->getPost('informe_tecnico_numero')),
                        'informe_tecnico_fecha' => $this->request->getPost('informe_tecnico_fecha'),
                        'descripcion_oficio' => mb_strtoupper($this->request->getPost('descripcion_oficio')),
                        'comunidad_localidad' => mb_strtoupper($this->request->getPost('comunidad_localidad')),
                        'descripcion_lugar' => mb_strtoupper($this->request->getPost('descripcion_lugar')),
                        'autores' => mb_strtoupper($this->request->getPost('autores')),
                        'persona_juridica' => mb_strtoupper($this->request->getPost('persona_juridica')),
                        'descripcion_materiales' => mb_strtoupper($this->request->getPost('descripcion_materiales')),
                        'fk_usuario_editor' => session()->get('registroUser'),
                        'departamento' => $ubicacion['departamento'],
                        'provincia' => $ubicacion['provincia'],
                        'municipio' => $ubicacion['municipio'],
                        'tiene_area_minera' => ( isset($id_areas_mineras) && count($id_areas_mineras)>0) ? 'true' : 'false',
                    );
                else
                    $dataDenuncia = array(
                        'id' => $id_denuncia,
                        'fk_municipio' => $this->request->getPost('fk_municipio'),
                        'comunidad_localidad' => mb_strtoupper($this->request->getPost('comunidad_localidad')),
                        'descripcion_lugar' => mb_strtoupper($this->request->getPost('descripcion_lugar')),
                        'autores' => mb_strtoupper($this->request->getPost('autores')),
                        'persona_juridica' => mb_strtoupper($this->request->getPost('persona_juridica')),
                        'descripcion_materiales' => mb_strtoupper($this->request->getPost('descripcion_materiales')),
                        'fk_usuario_editor' => session()->get('registroUser'),
                        'departamento' => $ubicacion['departamento'],
                        'provincia' => $ubicacion['provincia'],
                        'municipio' => $ubicacion['municipio'],
                        'tiene_area_minera' => ( isset($id_areas_mineras) && count($id_areas_mineras)>0 ) ? 'true' : 'false',
                    );

                if($denunciasMineriaIlegalModel->save($dataDenuncia) === false)
                    session()->setFlashdata('fail', $denunciasMineriaIlegalModel->errors());

                if(isset($id_denunciantes) && implode(',',$id_denunciantes) != $this->request->getPost('id_denunciantes_ant')){
                    $this->liberarDenunciantes($id_denuncia);
                    foreach($id_denunciantes as $id_denunciante){
                        $dataDenunciaDenunciante = array(
                            'fk_denuncia' => $id_denuncia,
                            'fk_denunciante' => $id_denunciante,
                        );
                        if($denunciasDenunciantesMineriaIleglaModel->insert($dataDenunciaDenunciante) === false)
                            session()->setFlashdata('fail', $denunciasDenunciantesMineriaIleglaModel->errors());
                    }
                }

                if(isset($id_hojas_rutas) && implode(',',$id_hojas_rutas) != $this->request->getPost('id_hojas_ruta_ant')){
                    $this->liberarHojasRutaSincobol($id_denuncia);
                    foreach($id_hojas_rutas as $id_hoja_ruta){
                        $dataHojaRutaSincobol = array(
                            'fk_denuncia' => $id_denuncia,
                            'fk_hoja_ruta' => $id_hoja_ruta,
                        );
                        if($denunciasHrSincobolMineriaIlegalModel->insert($dataHojaRutaSincobol) === false)
                            session()->setFlashdata('fail', $denunciasHrSincobolMineriaIlegalModel->errors());
                        else
                            $this->archivarHrSincobol($id_hoja_ruta, 'ANEXADO AL SISTEMA DE SEGUIMIENTO Y CONTROL DE TRAMITES POR '.session()->get('registroUserName'));
                    }
                }

                if(isset($id_areas_mineras) && implode(',',$id_areas_mineras) != $this->request->getPost('id_areas_mineras_ant')){
                    $this->liberarAreasMineras($id_denuncia);
                    foreach($id_areas_mineras as $id_area_minera){
                        $dataAreaMinera = array(
                            'fk_denuncia' => $id_denuncia,
                            'fk_area_minera' => $id_area_minera,
                        );
                        if($denunciasAreasMinerasMineriaIlegalModel->insert($dataAreaMinera) === false)
                            session()->setFlashdata('fail', $denunciasAreasMinerasMineriaIlegalModel->errors());
                    }
                }

                if($this->obtenerCoordenadas($this->request->getPost('coordenadas')) !== $this->obtenerCoordenadas($this->request->getPost('coordenadas_ant'))){
                    $this->vaciarCoordenadas($id_denuncia);
                    $coordenadas = $this->obtenerCoordenadas($this->request->getPost('coordenadas'));
                    if(count($coordenadas)>0){
                        foreach($coordenadas as $coordenada){
                            $dataCoordenada = array(
                                'fk_denuncia' => $id_denuncia,
                                'latitud' => $coordenada['latitud'],
                                'longitud' => $coordenada['longitud'],
                            );
                            if($coordenadasMineriaIlegalModel->insert($dataCoordenada) === false)
                                session()->setFlashdata('fail', $coordenadasMineriaIlegalModel->errors());
                        }
                    }
                }

                if ($adjuntos = $this->request->getFiles()) {
                    foreach($adjuntos as $nombre => $adjunto){
                        if($nombre == 'adjuntos' && count($adjunto) > 0){
                            $nombres = $this->request->getPost('nombres');
                            $cites = $this->request->getPost('cites');
                            $fecha_cites = $this->request->getPost('fecha_cites');
                            foreach($adjunto as $i => $archivo){
                                $tipoDocDigital = $this->obtenerTipoArchivo($archivo->guessExtension());
                                $nombreDocDigital = $archivo->getRandomName();
                                $archivo->move($this->rutaArchivos,$nombreDocDigital);
                                $nombreDocDigital = $this->rutaArchivos.$nombreDocDigital;
                                $dataAdjunto = array(
                                    'fk_denuncia' => $id_denuncia,
                                    'nombre' => mb_strtoupper($nombres[$i]),
                                    'cite' => mb_strtoupper($cites[$i]),
                                    'fecha_cite'=>((!empty($fecha_cites[$i])) ? $fecha_cites[$i] : NULL),
                                    'tipo' => $tipoDocDigital,
                                    'adjunto' => $nombreDocDigital,
                                    'fk_usuario_creador' => session()->get('registroUser'),
                                );
                                if($adjuntosMineriaIlegalModel->insert($dataAdjunto) === false)
                                    session()->setFlashdata('fail', $adjuntosMineriaIlegalModel->errors());
                            }
                        }
                    }
                }

                $dataHojaRuta = array(
                    'id' => $id_hoja_ruta,
                    'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                    'ultimo_fk_estado_tramite_padre' => $this->request->getPost('fk_estado_tramite'),
                    'ultimo_fk_estado_tramite_hijo' => ((!empty($this->request->getPost('fk_estado_tramite_hijo'))) ? $this->request->getPost('fk_estado_tramite_hijo') : NULL),
                    'ultimo_instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                    'ultimo_fk_usuario_remitente' => session()->get('registroUser'),
                    'ultimo_fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                    'ultimo_fk_usuario_responsable'=>($this->request->getPost('responsable') ? $this->request->getPost('fk_usuario_destinatario') : $this->request->getPost('ultimo_fk_usuario_responsable')),
                    'editar' => 'false',
                );

                if($hojaRutaMineriaIlegalModel->save($dataHojaRuta) === false){
                    session()->setFlashdata('fail', $hojaRutaMineriaIlegalModel->errors());
                }else{
                    $dataDerivacion = array(
                        'id' => $id_derivacion,
                        'fk_estado_tramite_padre' => $this->request->getPost('fk_estado_tramite'),
                        'fk_estado_tramite_hijo' => ((!empty($this->request->getPost('fk_estado_tramite_hijo'))) ? $this->request->getPost('fk_estado_tramite_hijo') : NULL),
                        'observaciones' => mb_strtoupper($this->request->getPost('observaciones')),
                        'instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                        'motivo_anexo' => mb_strtoupper($this->request->getPost('motivo_anexo')),
                        'fk_usuario_remitente' => session()->get('registroUser'),
                        'fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                        'fk_usuario_creador' => session()->get('registroUser'),
                        'fk_usuario_responsable'=>($this->request->getPost('responsable') ? $this->request->getPost('fk_usuario_destinatario') : $this->request->getPost('ultimo_fk_usuario_responsable')),
                    );

                    if($derivacionMineriaIlegalModel->save($dataDerivacion) === false){
                        session()->setFlashdata('fail', $derivacionMineriaIlegalModel->errors());
                    }else{

                        if(count($documentos)>0){
                            foreach($documentos as $documento){
                                $docDigital = $this->request->getFile('adjunto'.$documento['id']);
                                if(!empty($docDigital) && $docDigital->getSize()>0){
                                    if(file_exists($documento['doc_digital']))
                                        @unlink($documento['doc_digital']);
                                    $tipoDocDigital = $this->obtenerTipoArchivo($docDigital->guessExtension());
                                    $nombreDocDigital = $docDigital->getRandomName();
                                    $docDigital->move($this->rutaDocumentos,$nombreDocDigital);
                                    $nombreDocDigital = $this->rutaDocumentos.$nombreDocDigital;
                                    $dataDocumento = array(
                                        'id' => $documento['id'],
                                        'fecha_notificacion'=>((!empty($this->request->getPost('fecha_notificacion'.$documento['id']))) ? $this->request->getPost('fecha_notificacion'.$documento['id']) : NULL),
                                        'doc_digital' => $nombreDocDigital,
                                    );
                                    if($documentosModel->save($dataDocumento) === false)
                                        session()->setFlashdata('fail', $documentosModel->errors());
                                }
                            }
                        }

                        /* mejorar
                        if($anexar_hr){
                            foreach($anexar_hr as $fk_hoja_ruta){
                                if(!$this->anexarHrSincobolMejorado($idDerivacion, $fk_hoja_ruta, $id_denuncia, session()->get('registroUserName')))
                                    session()->setFlashdata('fail', 'No se anexo la H.R.'.$fk_hoja_ruta);
                            }
                        }
                        */
                    }
                    session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información.');
                }
                return redirect()->to($this->controlador.'mis_tramites');
            }
        }
    }

    public function ajaxAreaMineraMineriaIlegal(){
        $cadena = mb_strtoupper($this->request->getPost('texto'));
        if(!empty($cadena) && session()->get('registroUser')){
            $data = array();
            $dbSincobol = \Config\Database::connect('sincobol');
            $campos = array('am.id', "CONCAT(am.codigo_unico,' - ',am.nombre, ' - ', tam.nombre,' (',acm.nombre,' - ',tacm.nombre,')') AS nombre");
            $where = array(
                'am.vigente' => 'true',
            );
            $builder = $dbSincobol->table('contratos_licencias.area_minera as am')
            ->select($campos)
            ->join('contratos_licencias.actor_minero as acm', 'am.fk_actor_minero_poseedor = acm.id', 'left')
            ->join('contratos_licencias.tipo_actor_minero as tacm', 'acm.fk_tipo_actor_minero = tacm.id', 'left')
            ->join('contratos_licencias.tipo_area_minera as tam', 'am.fk_tipo_area_minera = tam.id', 'left')
            ->where($where)
            ->whereIn('am.fk_tipo_area_minera', array(2,3,4,7,8,9,10,11,12,14))
            ->like("CONCAT(am.codigo_unico,' - ',am.nombre)", $cadena)
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
    public function ajaxDatosAreaMineraMineriaIlegal(){
        $resultado = array(
            'estado' => 'error',
            'texto' => 'Envio de peticion erroneo.',
        );
        $id = $this->request->getPost('id');
        if(!empty($id) && session()->get('registroUser')){
            if($fila = $this->obtenerDatosAreaMineraMineriaIlegal($id)){
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
    public function recibirTramite($id_hoja_ruta){
        $hojaRutaMineriaIlegalModel = new HojaRutaMineriaIlegalModel();
        $derivacionMineriaIlegalModel = new DerivacionMineriaIlegalModel();
        $where = array(
            'id' => $id_hoja_ruta,
            'ultimo_estado' => 'DERIVADO',
            'deleted_at' => NULL,
            'ultimo_fk_usuario_destinatario' => session()->get('registroUser'),
        );
        if($fila = $hojaRutaMineriaIlegalModel->where($where)->first()){
            $estado = 'RECIBIDO';
            $data = array(
                'id' => $fila['id'],
                'ultimo_estado' => $estado,
                'fk_usuario_actual' => $fila['ultimo_fk_usuario_destinatario'],
                'editar' => true,
            );

            if($hojaRutaMineriaIlegalModel->save($data) === false){
                session()->setFlashdata('fail', $hojaRutaMineriaIlegalModel->errors());
            }

            $where = array(
                'fk_hoja_ruta' => $fila['id'],
            );
            $derivacion = $derivacionMineriaIlegalModel->where($where)->orderBY('id', 'DESC')->first();
            $dataDerivacion = array(
                'id' => $derivacion['id'],
                'estado' => $estado,
                'fecha_recepcion' => date('Y-m-d H:i:s'),
            );

            if($derivacionMineriaIlegalModel->save($dataDerivacion) === false){
                session()->setFlashdata('fail', $derivacionMineriaIlegalModel->errors());
            }
        }
        return true;
    }
    public function ajaxGuardarDevolver(){

        $hojaRutaMineriaIlegalModel = new HojaRutaMineriaIlegalModel();
        $derivacionMineriaIlegalModel = new DerivacionMineriaIlegalModel();
        $documentosModel = new DocumentosModel();
        $resultado = array(
            'error' => 'No se guardo la información',
        );
        $where = array(
            'id' => $this->request->getPost('id'),
            'ultimo_fk_usuario_destinatario' => session()->get('registroUser'),
            'deleted_at' => NULL,
        );
        if($hoja_ruta = $hojaRutaMineriaIlegalModel->where($where)->first()){

            $estado = 'DEVUELTO';
            $motivo_devolucion = mb_strtoupper($this->request->getPost('motivo_devolucion'));

            $where = array(
                'fk_hoja_ruta' => $hoja_ruta['id'],
            );
            $derivaciones = $derivacionMineriaIlegalModel->where($where)->orderBy('id', 'DESC')->findAll(2);
            $derivacion_actual = $derivaciones[0];
            $derivacion_restaurar = $derivaciones[1];
            $where = array(
                'fk_derivacion' => $derivacion_actual['id'],
                'fk_hoja_ruta' => $hoja_ruta['id'],
            );
            $documentos_anexados = $documentosModel->where($where)->findAll();
            $dataHojaRuta = array(
                'id' => $hoja_ruta['id'],
                'ultimo_estado' => $estado,
                'ultimo_fk_estado_tramite_padre' => $derivacion_restaurar['fk_estado_tramite_padre'],
                'ultimo_fk_estado_tramite_hijo' => $derivacion_restaurar['fk_estado_tramite_hijo'],
                'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                'ultimo_instruccion' => $motivo_devolucion,
                'ultimo_fk_usuario_remitente' => session()->get('registroUser'),
                'ultimo_fk_usuario_destinatario' => $derivacion_restaurar['fk_usuario_destinatario'],
                'ultimo_fk_documentos' => '',
            );
            if($hojaRutaMineriaIlegalModel->save($dataHojaRuta) === false){
                session()->setFlashdata('fail', $hojaRutaMineriaIlegalModel->errors());
            }else{
                $dataDerivacion = array(
                    'fk_hoja_ruta' => $hoja_ruta['id'],
                    'fk_estado_tramite_padre' => $derivacion_restaurar['fk_estado_tramite_padre'],
                    'fk_estado_tramite_hijo' => $derivacion_restaurar['fk_estado_tramite_hijo'],
                    'observaciones' => $derivacion_restaurar['observaciones'],
                    'instruccion' => $motivo_devolucion,
                    'motivo_anexo' => $derivacion_restaurar['motivo_anexo'],
                    'fk_usuario_remitente' => session()->get('registroUser'),
                    'fk_usuario_destinatario' => $derivacion_actual['fk_usuario_remitente'],
                    'estado' => $estado,
                    'fk_usuario_creador' => session()->get('registroUser'),
                    'fk_usuario_responsable' => $derivacion_actual['fk_usuario_responsable'],
                );

                if($derivacionMineriaIlegalModel->insert($dataDerivacion) === false){
                    session()->setFlashdata('fail', $derivacionMineriaIlegalModel->errors());
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
                    if($derivacionMineriaIlegalModel->save($dataDerivacionActualizacion) === false)
                        session()->setFlashdata('fail', $derivacionMineriaIlegalModel->errors());

                    $resultado = array(
                        'idtra' => $hoja_ruta['id']
                    );
                }
            }
        }
        echo json_encode($resultado);
    }

    public function ver($back,$id_hoja_ruta){

        $hojaRutaMineriaIlegalModel = new HojaRutaMineriaIlegalModel();
        $campos = array('*', 'fk_denuncia', 'correlativo as correlativo_hr', "to_char(fecha_hoja_ruta, 'DD/MM/YYYY HH24:MI') as fecha_hr");
        if($hoja_ruta = $hojaRutaMineriaIlegalModel->select($campos)->find($id_hoja_ruta)){
            $db = \Config\Database::connect();
            $denunciasMineriaIlegalModel = new DenunciasMineriaIlegalModel();
            $denunciasHrSincobolMineriaIlegalModel = new DenunciasHrSincobolMineriaIlegalModel();
            $denunciasAreasMinerasMineriaIlegalModel = new DenunciasAreasMinerasMineriaIlegalModel();
            $coordenadasMineriaIlegalModel = new CoordenadasMineriaIlegalModel();
            $adjuntosMineriaIlegalModel = new AdjuntosMineriaIlegalModel();
            $denuncia = $denunciasMineriaIlegalModel->select("*, correlativo as correlativo_denuncia, to_char(fecha_denuncia, 'DD/MM/YYYY HH24:MI') as fecha_denuncia, to_char(informe_tecnico_fecha, 'DD/MM/YYYY') as informe_tecnico_fecha")->find($hoja_ruta['fk_denuncia']);
            $coordenadas = $coordenadasMineriaIlegalModel->where(array('fk_denuncia' => $hoja_ruta['fk_denuncia']))->findAll();

            $hojas_rutas = array();
            if($denunciaHojaRutaSincobol = $denunciasHrSincobolMineriaIlegalModel->where(array('fk_denuncia' => $hoja_ruta['fk_denuncia']))->findAll()){
                foreach($denunciaHojaRutaSincobol as $row)
                    $hojas_rutas[] = $this->obtenerDatosHrInExSincobol($row['fk_hoja_ruta']);
            }

            $campos = array('de.id', 'de.nombres', 'de.apellidos', 'de.documento_identidad', 'de.expedido', "de.telefonos", "de.email", 'de.direccion', 'de.documento_identidad_digital');
            $where = array(
                'dd.fk_denuncia' => $hoja_ruta['fk_denuncia'],
            );
            $builder = $db->table('mineria_ilegal.denuncias_denunciantes AS dd')
            ->select($campos)
            ->join('mineria_ilegal.denunciantes AS de', 'dd.fk_denunciante = de.id', 'left')
            ->where($where)
            ->orderBY('dd.id', 'ASC');
            $denunciantes = $builder->get()->getResultArray();

            $denunciasAreasMineras = $denunciasAreasMinerasMineriaIlegalModel->where(array('fk_denuncia'=>$hoja_ruta['fk_denuncia']))->orderBy('id', 'ASC')->findAll();
            $areas_mineras = array();
            if($denunciasAreasMineras){
                foreach($denunciasAreasMineras as $row)
                    $areas_mineras[] = $this->obtenerDatosAreaMineraMineriaIlegal($row['fk_area_minera']);
            }

            $campos = array(
                'd.id',"CONCAT(ur.nombre_completo,'<br><b>',pr.nombre,'<b>') as remitente", "CONCAT(ud.nombre_completo,'<br><b>',pd.nombre,'<b>') as destinatario",
                "CONCAT(ua.nombre_completo,'<br><b>',pa.nombre,'<b>') as responsable",'d.instruccion',
                "CASE WHEN d.fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'. ',etp.nombre,'<br>',etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE CONCAT(etp.orden,'. ',etp.nombre) END as estado_tramite",
                "to_char(d.created_at, 'DD/MM/YYYY HH24:MI') as fecha_derivacion", "to_char(d.fecha_recepcion, 'DD/MM/YYYY HH24:MI') as fecha_recepcion", "to_char(d.fecha_atencion, 'DD/MM/YYYY HH24:MI') as fecha_atencion",
                'motivo_anexo', "to_char(d.fecha_devolucion, 'DD/MM/YYYY HH24:MI') as fecha_devolucion", 'd.estado'
                );
            $where = array(
                'd.fk_hoja_ruta' => $hoja_ruta['id'],
            );
            $query = $db->table('mineria_ilegal.derivacion as d')
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
            $derivaciones = $query->get()->getResultArray();
            $derivaciones = $this->obtenerHrAnexadas($derivaciones);
            $derivaciones = $this->obtenerDocumentosAnexados($derivaciones);
            $contenido['derivaciones'] = $derivaciones;

            $cabecera_derivacion = array(
                '',
                'Remitente',
                'Destinatario',
                'Responsable Trámite',
                'Instrucción',
                'Estado Tramite',
                'Documentos Anexados',
                'H.R. Anexada(s)',
                'Fecha Derivación',
                'Fecha Recepción',
                'Fecha Atención',
                'Fecha Devolución',
            );
            $campos_derivacion = array(
                'estado',
                'remitente',
                'destinatario',
                'responsable',
                'instruccion',
                'estado_tramite',
                'documentos_anexados',
                'hoja_ruta_anexadas',
                'fecha_derivacion',
                'fecha_recepcion',
                'fecha_atencion',
                'fecha_devolucion'
            );

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
            $cabera['subtitulo'] = 'Ver Hoja de Ruta de Minería Ilegal';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['subtitulo'] = 'Ver Hoja de Ruta de Minería Ilegal';
            $contenido['id_tramite'] = $this->idTramite;
            $contenido['controlador'] = $this->controlador;
            $contenido['tipo_denuncias'] = $this->tipoDenuncias;
            $contenido['url_atras'] = $url_atras;
            $contenido['hoja_ruta'] = $hoja_ruta;
            $contenido['denuncia'] = $denuncia;
            $contenido['denunciantes'] = $denunciantes;
            $contenido['hojas_rutas'] = $hojas_rutas;
            $contenido['areas_mineras'] = $areas_mineras;
            $contenido['coordenadas'] = $coordenadas;
            $contenido['adjuntos'] = $adjuntosMineriaIlegalModel->where(array('fk_denuncia' => $hoja_ruta['fk_denuncia']))->findAll();
            $contenido['cabecera_derivacion'] = $cabecera_derivacion;
            $contenido['campos_derivacion'] = $campos_derivacion;
            $data['content'] = view($this->carpeta.'ver', $contenido);
            $data['menu_actual'] = $menuActual;
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['alertas'] = $this->alertasTramites();
            $data['mapas'] = true;
            $data['puntos'] = $coordenadas;
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador.'buscador');
        }
    }
    public function misIngresos()
    {
        $db = \Config\Database::connect();
        $campos = array('d.id as id_denuncia','hr.id as id_hoja_ruta','hr.ultimo_estado', "to_char(hr.ultimo_fecha_derivacion, 'DD/MM/YYYY') as fecha_derivacion", 'hr.correlativo as correlativo_hoja_ruta',
        "CONCAT(ures.nombre_completo,'<br><b>',pres.nombre,'<b>') as responsable", "CONCAT(urem.nombre_completo,'<br><b>',prem.nombre,'<b>') as remitente", "CONCAT(udes.nombre_completo,'<br><b>',pdes.nombre,'<b>') as destinatario",
        "hr.ultimo_instruccion, (CURRENT_DATE - hr.ultimo_fecha_derivacion::date) as dias",
        "to_char(d.created_at, 'DD/MM/YYYY') as fecha_denuncia", 'd.correlativo as correlativo_denuncia', 'd.fk_tipo_denuncia', 'd.departamento', 'hr.editar'
        );
        $where = array(
            'd.deleted_at' => NULL,
            'hr.fk_usuario_actual' => session()->get('registroUser'),
        );
        $builder = $db->table('mineria_ilegal.denuncias AS d')
        ->select($campos)
        ->join('mineria_ilegal.hoja_ruta AS hr', 'd.id = hr.fk_denuncia', 'left')
        ->join('usuarios as ures', 'hr.ultimo_fk_usuario_responsable = ures.id', 'left')
        ->join('perfiles as pres', 'ures.fk_perfil = pres.id', 'left')
        ->join('usuarios as urem', 'hr.ultimo_fk_usuario_remitente = urem.id', 'left')
        ->join('perfiles as prem', 'urem.fk_perfil = prem.id', 'left')
        ->join('usuarios as udes', 'hr.ultimo_fk_usuario_destinatario = udes.id', 'left')
        ->join('perfiles as pdes', 'udes.fk_perfil = pdes.id', 'left')
        ->where($where)
        ->whereIn('hr.ultimo_estado',array('MIGRADO', 'DERIVADO', 'RECIBIDO', 'EN ESPERA'))
        ->orderBY('d.id', 'DESC');
        $datos = $builder->get()->getResult('array');
        $datos = $this->obtenerDenunciantes($datos);
        $campos_listar=array(
            'Estado', 'Fecha', 'Días Pasados', 'Hoja de Ruta', 'Remitente', 'Destinatario', 'Instrucción', 'Responsable', 'Denunciante', 'Departamento'
        );
        $campos_reales=array(
            'ultimo_estado','fecha_derivacion', 'dias', 'correlativo_hoja_ruta', 'remitente', 'destinatario', 'ultimo_instruccion', 'responsable', 'denunciante', 'departamento'
        );

        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Listado de H.R. Minería Ilegal';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['datos'] = $datos;
        $contenido['campos_listar'] = $campos_listar;
        $contenido['campos_reales'] = $campos_reales;
        $contenido['subtitulo'] = 'Listado de H.R. Minería Ilegal';
        $contenido['controlador'] = $this->controlador;
        $contenido['id_tramite'] = $this->idTramite;
        $contenido['tipo_denuncias'] = $this->tipoDenuncias;
        $data['content'] = view($this->carpeta.'mis_ingresos', $contenido);
        $data['menu_actual'] = $this->menuActual.'mis_ingresos';
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



    public function devolver($id){
        $db = \Config\Database::connect();
        $campos = array('ac.id', 'ac.fk_solicitud_licencia_contrato', 'ac.fk_hoja_ruta', 'ac.fk_area_minera', 'ac.ultimo_fk_usuario_remitente', 'ac.correlativo',
        'ac.ultimo_fk_estado_tramite_padre', 'ac.ultimo_fk_estado_tramite_hijo', 'ac.ultimo_fk_usuario_responsable', 'ac.area_protegida_adicional');
        $where = array(
            'ac.deleted_at' => NULL,
            'ac.ultimo_fk_usuario_destinatario' => session()->get('registroUser'),
            'ac.id' => $id
        );
        $builder = $db->table('public.acto_administrativo as ac')
        ->select($campos)
        ->join('usuarios as ur', 'ac.ultimo_fk_usuario_remitente = ur.id', 'left')
        ->join('perfiles as per', 'ur.fk_perfil = per.id', 'left')
        ->join('estado_tramite as etp', 'ac.ultimo_fk_estado_tramite_padre = etp.id', 'left')
        ->join('estado_tramite as eth', 'ac.ultimo_fk_estado_tramite_hijo = eth.id', 'left')
        ->where($where)
        ->orderBY('ac.id', 'DESC');

        if($fila = $builder->get()->getRowArray()){

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
            $cabera['subtitulo'] = 'Devolución Tramite';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['datos'] = $this->informacionAreaMinera($fila['fk_solicitud_licencia_contrato']);
            $contenido['fila'] = $fila;
            $contenido['subtitulo'] = 'Devolución Tramite';
            $contenido['accion'] = $this->controlador.'guardar_devolver';
            $contenido['ruta_archivos'] = $this->rutaArchivos;
            $contenido['controlador'] = $this->controlador;
            $data['content'] = view($this->carpeta.'devolver', $contenido);
            $data['menu_actual'] = $this->menuActual.'listado_recepcion';
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['validacion_js'] = 'cam-devolver-validation.js';
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador);
        }
    }
    public function guardarDevolver(){
        if ($this->request->getPost()) {
            $validation = $this->validate([
                'id' => [
                    'rules' => 'required',
                ],
                'motivo_devolucion' => [
                    'rules' => 'required',
                ],
            ]);
            if(!$validation){
                $cabera['titulo'] = $this->titulo;
                $cabera['navegador'] = true;
                $cabera['subtitulo'] = 'Devolución Tramite';
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['subtitulo'] = 'Devolución Tramite';
                $contenido['accion'] = $this->controlador.'guardar_devolver';
                $contenido['validation'] = $this->validator;
                $contenido['ruta_archivos'] = $this->rutaArchivos;
                $contenido['controlador'] = $this->controlador;
                $data['content'] = view($this->carpeta.'devolver', $contenido);
                $data['menu_actual'] = $this->menuActual.'listado_recepcion';
                $data['tramites_menu'] = $this->tramitesMenu();
                $data['validacion_js'] = 'cam-devolver-validation.js';
                echo view('templates/template', $data);
            }else{
                $estado = 'DEVUELTO';
                $actoAdministrativoModel = new ActoAdministrativoModel();
                $derivacionModel = new DerivacionModel();
                $documentosModel = new DocumentosModel();
                $motivo_devolucion = mb_strtoupper($this->request->getPost('motivo_devolucion'));
                $where = array(
                    'fk_acto_administrativo' => $this->request->getPost('id'),
                    'id !=' => $this->request->getPost('id_derivacion'),
                );
                $derivacion_restaurar = $derivacionModel->where($where)->first();
                $derivacion_actual = $derivacionModel->find($this->request->getPost('id_derivacion'));
                $documentos_anexados = $documentosModel->where('fk_derivacion = '.$derivacion_actual['id'])->findAll();
                $data = array(
                    'id' => $this->request->getPost('id'),
                    'fk_usuario_actual' => $this->request->getPost('ultimo_fk_usuario_remitente'),
                    'extension' => $this->request->getPost('extension'),
                    'departamentos' => $this->request->getPost('departamentos'),
                    'provincias' => $this->request->getPost('provincias'),
                    'municipios' => $this->request->getPost('municipios'),
                    'area_protegida' => $this->request->getPost('area_protegida'),
                    'regional' => $this->request->getPost('regional'),
                    'titular' => $this->request->getPost('titular'),
                    'clasificacion_titular' => $this->request->getPost('clasificacion'),
                    'ultimo_estado' => $estado,
                    'ultimo_fk_estado_tramite_padre' => $derivacion_restaurar['fk_estado_tramite_padre'],
                    'ultimo_fk_estado_tramite_hijo' => $derivacion_restaurar['fk_estado_tramite_hijo'],
                    'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                    'ultimo_instruccion' => $motivo_devolucion,
                    'ultimo_fk_usuario_remitente' => session()->get('registroUser'),
                    'ultimo_fk_usuario_responsable'=>($this->request->getPost('responsable') ? $this->request->getPost('fk_usuario_destinatario') : $this->request->getPost('ultimo_fk_usuario_responsable')),
                );
                if($actoAdministrativoModel->save($data) === false){
                    session()->setFlashdata('fail', $actoAdministrativoModel->errors());
                }else{
                    $dataDerivacion = array(
                        'fk_acto_administrativo' => $this->request->getPost('id'),
                        'domicilio_legal' => $derivacion_restaurar['domicilio_legal'],
                        'domicilio_procesal' => $derivacion_restaurar['domicilio_procesal'],
                        'telefono_solicitante' => $derivacion_restaurar['telefono_solicitante'],
                        'fk_estado_tramite_padre' => $derivacion_restaurar['fk_estado_tramite_padre'],
                        'fk_estado_tramite_hijo' => $derivacion_restaurar['fk_estado_tramite_hijo'],
                        //'fecha_notificacion' => $derivacion_restaurar['fecha_notificacion'],
                        'observaciones' => $derivacion_restaurar['observaciones'],
                        'fk_usuario_remitente' => session()->get('registroUser'),
                        'fk_usuario_destinatario' => $this->request->getPost('ultimo_fk_usuario_remitente'),
                        'instruccion' => $motivo_devolucion,
                        //'motivo_anexo' => $derivacion_restaurar['motivo_anexo'],
                        'estado' => $estado,
                        'fk_usuario_creador' => session()->get('registroUser'),
                        'fk_usuario_responsable'=>($this->request->getPost('responsable') ? $this->request->getPost('fk_usuario_destinatario') : $this->request->getPost('ultimo_fk_usuario_responsable')),
                    );

                    if($derivacionModel->insert($dataDerivacion) === false){
                        session()->setFlashdata('fail', $derivacionModel->errors());
                    }else{

                        if(count($documentos_anexados)>0){
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
                            'id' => $this->request->getPost('id_derivacion'),
                            'estado' => 'ATENDIDO',
                            'fecha_devolucion' => date('Y-m-d H:i:s'),
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

    public function finalizar($id){
        $db = \Config\Database::connect();
        $campos = array('ac.id', 'ac.fk_solicitud_licencia_contrato', 'ac.fk_hoja_ruta', 'ac.fk_area_minera', 'ac.ultimo_fk_usuario_remitente', 'ac.correlativo',
        'ac.ultimo_fk_estado_tramite_padre', 'ac.ultimo_fk_estado_tramite_hijo', 'ac.ultimo_fk_usuario_responsable', 'ac.area_protegida_adicional');
        $where = array(
            'ac.deleted_at' => NULL,
            'ac.fk_usuario_actual' => session()->get('registroUser'),
            'ac.id' => $id
        );
        $builder = $db->table('public.acto_administrativo as ac')
        ->select($campos)
        ->join('usuarios as ur', 'ac.ultimo_fk_usuario_remitente = ur.id', 'left')
        ->join('perfiles as per', 'ur.fk_perfil = per.id', 'left')
        ->join('estado_tramite as etp', 'ac.ultimo_fk_estado_tramite_padre = etp.id', 'left')
        ->join('estado_tramite as eth', 'ac.ultimo_fk_estado_tramite_hijo = eth.id', 'left')
        ->where($where)
        ->orderBY('ac.id', 'DESC');

        if($fila = $builder->get()->getRowArray()){

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
            $contenido['datos'] = $this->informacionAreaMinera($fila['fk_solicitud_licencia_contrato']);
            $contenido['fila'] = $fila;
            $contenido['subtitulo'] = 'Finalizar Tramite';
            $contenido['accion'] = $this->controlador.'guardar_finalizar';
            $contenido['ruta_archivos'] = $this->rutaArchivos;
            $contenido['controlador'] = $this->controlador;
            $data['content'] = view($this->carpeta.'finalizar', $contenido);
            $data['menu_actual'] = $this->menuActual.'mis_tramites';
            $data['tramites_menu'] = $this->tramitesMenu();
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
                $data['validacion_js'] = 'cam-finalizar-validation.js';
                echo view('templates/template', $data);
            }else{
                $estado = 'FINALIZADO';
                $actoAdministrativoModel = new ActoAdministrativoModel();
                $derivacionModel = new DerivacionModel();
                $derivacion_actual = $derivacionModel->find($this->request->getPost('id_derivacion'));
                $motivo_finalizar = mb_strtoupper($this->request->getPost('motivo_finalizar'));
                $data = array(
                    'id' => $this->request->getPost('id'),
                    'fk_usuario_actual' => session()->get('registroUser'),
                    'extension' => $this->request->getPost('extension'),
                    'departamentos' => $this->request->getPost('departamentos'),
                    'provincias' => $this->request->getPost('provincias'),
                    'municipios' => $this->request->getPost('municipios'),
                    'area_protegida' => $this->request->getPost('area_protegida'),
                    'regional' => $this->request->getPost('regional'),
                    'titular' => $this->request->getPost('titular'),
                    'clasificacion_titular' => $this->request->getPost('clasificacion'),
                    'ultimo_estado' => $estado,
                    'ultimo_fk_estado_tramite_padre' => $derivacion_actual['fk_estado_tramite_padre'],
                    'ultimo_fk_estado_tramite_hijo' => $derivacion_actual['fk_estado_tramite_hijo'],
                    'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                    'ultimo_instruccion' => $motivo_finalizar,
                    'ultimo_fk_usuario_remitente' => session()->get('registroUser'),
                    'ultimo_fk_usuario_responsable'=>($this->request->getPost('responsable') ? $this->request->getPost('fk_usuario_destinatario') : $this->request->getPost('ultimo_fk_usuario_responsable')),
                );
                if($actoAdministrativoModel->save($data) === false){
                    session()->setFlashdata('fail', $actoAdministrativoModel->errors());
                }else{
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
        $campos = array('ac.id', 'ac.fk_solicitud_licencia_contrato', 'ac.fk_hoja_ruta', 'ac.fk_area_minera', 'ac.ultimo_fk_usuario_remitente', 'ac.correlativo',
        'ac.ultimo_fk_estado_tramite_padre', 'ac.ultimo_fk_estado_tramite_hijo', 'ac.ultimo_fk_usuario_responsable', 'ac.area_protegida_adicional');
        $where = array(
            'ac.deleted_at' => NULL,
            'ac.fk_usuario_actual' => session()->get('registroUser'),
            'ac.id' => $id
        );
        $builder = $db->table('public.acto_administrativo as ac')
        ->select($campos)
        ->join('usuarios as ur', 'ac.ultimo_fk_usuario_remitente = ur.id', 'left')
        ->join('perfiles as per', 'ur.fk_perfil = per.id', 'left')
        ->join('estado_tramite as etp', 'ac.ultimo_fk_estado_tramite_padre = etp.id', 'left')
        ->join('estado_tramite as eth', 'ac.ultimo_fk_estado_tramite_hijo = eth.id', 'left')
        ->where($where)
        ->orderBY('ac.id', 'DESC');

        if($fila = $builder->get()->getRowArray()){

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
            $contenido['datos'] = $this->informacionAreaMinera($fila['fk_solicitud_licencia_contrato']);
            $contenido['fila'] = $fila;
            $contenido['subtitulo'] = 'En Espera del Trámite';
            $contenido['accion'] = $this->controlador.'guardar_espera';
            $contenido['ruta_archivos'] = $this->rutaArchivos;
            $contenido['controlador'] = $this->controlador;
            $data['content'] = view($this->carpeta.'espera', $contenido);
            $data['menu_actual'] = $this->menuActual.'mis_tramites';
            $data['tramites_menu'] = $this->tramitesMenu();
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
                $data['validacion_js'] = 'cam-espera-validation.js';
                echo view('templates/template', $data);
            }else{
                $estado = 'EN ESPERA';
                $actoAdministrativoModel = new ActoAdministrativoModel();
                $derivacionModel = new DerivacionModel();
                $derivacion_actual = $derivacionModel->find($this->request->getPost('id_derivacion'));
                $motivo_espera = mb_strtoupper($this->request->getPost('motivo_espera'));
                $data = array(
                    'id' => $this->request->getPost('id'),
                    'fk_usuario_actual' => session()->get('registroUser'),
                    'extension' => $this->request->getPost('extension'),
                    'departamentos' => $this->request->getPost('departamentos'),
                    'provincias' => $this->request->getPost('provincias'),
                    'municipios' => $this->request->getPost('municipios'),
                    'area_protegida' => $this->request->getPost('area_protegida'),
                    'regional' => $this->request->getPost('regional'),
                    'titular' => $this->request->getPost('titular'),
                    'clasificacion_titular' => $this->request->getPost('clasificacion'),
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

    public function anexarHrSincobol($id_derivacion, $fk_hoja_ruta, $motivo){
        $hojasRutaAnexadasModel = new HojasRutaAnexadasMineriaIlegalModel();
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
            if($derivacionSincobolModel->save($ultima_derivacion) === false){
                session()->setFlashdata('fail', $derivacionSincobolModel->errors());
            }else{
                $data = array(
                    'fk_derivacion' => $id_derivacion,
                    'fk_hoja_ruta' => $fk_hoja_ruta,
                );
                if($hojasRutaAnexadasModel->save($data) === false){
                    session()->setFlashdata('fail', $hojasRutaAnexadasModel->errors());
                }else{
                    return true;
                }
            }
        }
        return false;
    }
    public function anexarHrSincobolMejorado($id_derivacion, $fk_hoja_ruta, $fk_denuncia, $usuario){
        $hojasRutaAnexadasMineriaIlegalModel = new HojasRutaAnexadasMineriaIlegalModel();
        if($this->archivarHrSincobolMejorado($fk_hoja_ruta, $fk_denuncia, $usuario)){
            $dataDerivacion = array(
                'fk_derivacion' => $id_derivacion,
                'fk_hoja_ruta' => $fk_hoja_ruta,
            );
            if($hojasRutaAnexadasMineriaIlegalModel->save($dataDerivacion) === false)
                session()->setFlashdata('fail', $hojasRutaAnexadasMineriaIlegalModel->errors());
            else
                return true;
        }
        return false;
    }

    public function archivarHrSincobol($fk_hoja_ruta, $motivo){
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
            if($derivacionSincobolModel->save($ultima_derivacion) === false)
                session()->setFlashdata('fail', $derivacionSincobolModel->errors());
            else
                return true;
        }
        return false;
    }
    public function archivarHrSincobolMejorado($fk_hoja_ruta, $fk_denuncia, $usuario){
        $motivo = 'ANEXADO AL SISTEMA DE SEGUIMIENTO Y CONTROL DE TRAMITES - MINERÍA ILEGAL POR '.$usuario;
        $hojaRutaSisegModel = new HojaRutaSisegModel();
        $derivacionSincobolModel = new DerivacionSincobolModel();
        $where = array(
            'fk_hoja_ruta'=> $fk_hoja_ruta,
        );
        if(!$hojaRutaSisegModel->where($where)->first()){
            $dataHojaRutaSiseg = array(
                'fk_hoja_ruta' => $fk_hoja_ruta,
                'fk_tramite' => $this->idTramite,
                'fk_siseg' => $fk_denuncia,
                'usuario' => $usuario,
                'fecha' => date('Y-m-d H:i:s'),
                'tabla_siseg' => 'mineria_ilegal.denuncias',
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

    public function buscadorMisTramites()
    {
        $db = \Config\Database::connect();
        $campos = array('ac.id', 'ac.ultimo_estado', 'ac.correlativo', 'ac.codigo_unico', 'ac.denominacion', 'ac.extension', 'ac.departamentos', 'ac.provincias', 'ac.municipios', 'ac.area_protegida',
        "CONCAT(ur.nombre_completo, '<br><b>',pr.nombre,'</b>') as remitente", 'ac.ultimo_instruccion', "to_char(ac.ultimo_fecha_derivacion, 'DD/MM/YYYY') as ultimo_fecha_derivacion",
        "CASE WHEN ac.ultimo_fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'. ',etp.nombre,'<br>',etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE CONCAT(etp.orden,'. ',etp.nombre) END as estado_tramite");
        $where = array(
            'ac.deleted_at' => NULL,
            'ac.fk_usuario_actual' => session()->get('registroUser'),
        );
        $builder = $db->table('public.acto_administrativo as ac')
        ->select($campos)
        ->join('usuarios as ur', 'ac.ultimo_fk_usuario_remitente = ur.id', 'left')
        ->join('perfiles as pr', 'ur.fk_perfil=pr.id', 'left')
        ->join('estado_tramite as etp', 'ac.ultimo_fk_estado_tramite_padre = etp.id', 'left')
        ->join('estado_tramite as eth', 'ac.ultimo_fk_estado_tramite_hijo = eth.id', 'left')
        ->where($where)
        ->orderBY('ac.id', 'DESC');
        if ($this->request->getPost() && $this->request->getPost('enviar')=='buscar') {
            if(mb_strtoupper(trim($this->request->getPost('texto')))){
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
                    $texto = mb_strtoupper(trim($this->request->getPost('texto')));
                    switch($this->request->getPost('campo')){
                        case 'correlativo':
                            $query = $builder->like('ac.correlativo', $texto);
                            break;
                        case 'codigo_unico':
                            $where['ac.codigo_unico'] = $texto;
                            $query = $builder->where($where);
                            break;
                        case 'denominacion':
                            $query = $builder->like('ac.denominacion', $texto);
                            break;
                        case 'remitente':
                            $query = $builder->like('ur.nombre_completo', $texto);
                            break;
                    }
                }
            }else{
                $query = $builder;
            }
        }else{
            $query = $builder;
        }
        $datos = $query->get()->getResultArray();

        $campos_buscar=array(
            'correlativo' => 'H.R. Madre',
            'codigo_unico' => 'Código Único',
            'denominacion' => 'Denominación',
            'remitente' => 'Remitente',
        );
        $campos_listar=array(
            ' ', 'Fecha Derivación/Devolución', 'H.R. Madre','Código Único','Denominación','Extensión','Departamento(s)','Provincia(s)','Municipio(s)','Área Protegida','Remitente','Instrucción','Estado Trámite',
        );
        $campos_reales=array(
            'ultimo_estado','ultimo_fecha_derivacion','correlativo','codigo_unico','denominacion','extension','departamentos','provincias','municipios','area_protegida','remitente','ultimo_instruccion','estado_tramite',
        );

        if ($this->request->getPost() && $this->request->getPost('enviar')=='excel') {
            $this->exportarMisTramites($campos_listar, $campos_reales, $datos);
        }

        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Buscador de Mis Tramites';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['datos'] = $datos;
        $contenido['campos_listar'] = $campos_listar;
        $contenido['campos_reales'] = $campos_reales;
        $contenido['campos_buscar'] = $campos_buscar;
        $contenido['subtitulo'] = 'Buscador de Mis Tramites';
        $contenido['accion'] = $this->controlador.'buscador_mis_tramites';
        $contenido['controlador'] = $this->controlador;
        $data['content'] = view($this->carpeta.'buscador_mis_tramites', $contenido);
        $data['menu_actual'] = $this->menuActual.'buscador_mis_tramites';
        $data['tramites_menu'] = $this->tramitesMenu();
        echo view('templates/template', $data);
    }

    public function exportarMisTramites($campos_listar, $campos_reales, $datos){
        $file_name = 'reporte_mis_tramites-'.date('YmdHis').'.xlsx';
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

    public function buscador()
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
                $campos = array('ac.id', 'ac.ultimo_estado', 'ac.correlativo', 'ac.codigo_unico', 'ac.denominacion',
                "CONCAT(ur.nombre_completo, '<br><b>',pr.nombre,'</b>') as remitente", "CONCAT(ud.nombre_completo, '<br><b>',pd.nombre,'</b>') as destinatario",
                'ac.ultimo_instruccion', "CASE WHEN ac.ultimo_fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'. ',etp.nombre,'<br>',etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE CONCAT(etp.orden,'. ',etp.nombre) END as estado_tramite",
                "to_char(ac.ultimo_fecha_derivacion, 'DD/MM/YYYY') as ultimo_fecha_derivacion");
                $builder = $db->table('public.acto_administrativo as ac')
                ->select($campos)
                ->join('usuarios as ur', 'ac.ultimo_fk_usuario_remitente = ur.id', 'left')
                ->join('perfiles as pr', 'ur.fk_perfil=pr.id', 'left')
                ->join('usuarios as ud', 'ac.fk_usuario_actual = ud.id', 'left')
                ->join('perfiles as pd', 'ud.fk_perfil=pd.id', 'left')
                ->join('estado_tramite as etp', 'ac.ultimo_fk_estado_tramite_padre = etp.id', 'left')
                ->join('estado_tramite as eth', 'ac.ultimo_fk_estado_tramite_hijo = eth.id', 'left')
                ->orderBY('ac.fecha_mecanizada', 'DESC');
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
                        $where['ac.codigo_unico'] = $texto;
                        $query = $builder->where($where);
                        break;
                    case 'denominacion':
                        $query = $builder->where($where)->like('ac.denominacion', $texto);
                        break;
                }
                $datos = $query->get()->getResultArray();
                $contenido['datos'] = $datos;
            }
        }
        $campos_buscar=array(
            'correlativo_hoja_ruta' => 'H.R. Madre',
            'correlativo_denuncia' => 'Formulario Minería Ilegal',
        );
        $campos_listar=array(
            ' ','Fecha Derivación/Devolución','Hoja de Ruta', 'Remitente','Destinatario','Instrucción', 'Responsable Trámite', 'Estado', 'Denuncia', 'Tipo', 'Denunciante', 'Departamento'
        );
        $campos_reales=array(
            'ultimo_estado','ultimo_fecha_derivacion','correlativo','codigo_unico','denominacion','remitente','destinatario','ultimo_instruccion','estado_tramite',
        );
        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Buscador de Hojas de Ruta / Formulario de Minería Ilegal';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['campos_listar'] = $campos_listar;
        $contenido['campos_reales'] = $campos_reales;
        $contenido['campos_buscar'] = $campos_buscar;
        $contenido['tipos_denuncias'] = array(''=>'Todos los Tipos de F.M.I.') + $this->tipoDenuncias;
        $contenido['subtitulo'] = 'Buscador de Hojas de Ruta / Formulario de Minería Ilegal';
        $contenido['accion'] = $this->controlador.'buscador';
        $contenido['controlador'] = $this->controlador;
        $data['content'] = view($this->carpeta.'buscador', $contenido);
        $data['menu_actual'] = $this->menuActual.'buscador';
        $data['tramites_menu'] = $this->tramitesMenu();
        echo view('templates/template', $data);
    }

    public function buscadorVentanilla()
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
                $campos = array('ac.id', 'ac.ultimo_estado', 'ac.correlativo', 'ac.codigo_unico', 'ac.denominacion',
                "CONCAT(ur.nombre_completo, '<br><b>',pr.nombre,'</b>') as remitente", "CONCAT(ud.nombre_completo, '<br><b>',pd.nombre,'</b>') as destinatario",
                'ac.ultimo_instruccion', "CASE WHEN ac.ultimo_fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'. ',etp.nombre,'<br>',etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE CONCAT(etp.orden,'. ',etp.nombre) END as estado_tramite",
                "to_char(ac.ultimo_fecha_derivacion, 'DD/MM/YYYY') as ultimo_fecha_derivacion", "CONCAT(ua.nombre_completo,'<br><b>',pa.nombre,'<b>') as responsable");
                $builder = $db->table('public.acto_administrativo as ac')
                ->select($campos)
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
                    'ac.fk_oficina' => session()->get('registroOficina')
                );
                $texto = mb_strtoupper(trim($this->request->getPost('texto')));

                switch($this->request->getPost('campo')){
                    case 'correlativo':
                        $query = $builder->where($where)->like('ac.correlativo', $texto);
                        break;
                    case 'codigo_unico':
                        $where['ac.codigo_unico'] = $texto;
                        $query = $builder->where($where);
                        break;
                    case 'denominacion':
                        $query = $builder->where($where)->like('ac.denominacion', $texto);
                        break;
                }
                $datos = $query->get()->getResultArray();
                $contenido['datos'] = $datos;
            }
        }
        $campos_buscar=array(
            'correlativo_hoja_ruta' => 'H.R. Madre',
            'correlativo_denuncia' => 'Formulario Minería Ilegal',
            //'codigo_unico' => 'Código Único',
            //'denominacion' => 'Denominación',
        );
        $campos_listar=array(
            ' ','Fecha Derivación/Devolución','Hoja de Ruta', 'Remitente','Destinatario','Instrucción', 'Responsable Trámite', 'Estado', 'Denuncia', 'Tipo', 'Denunciante', 'Departamento'
        );
        $campos_reales=array(
            'ultimo_estado','ultimo_fecha_derivacion','correlativo','codigo_unico','denominacion','responsable','remitente','destinatario','ultimo_instruccion','estado_tramite',
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
        $data['content'] = view($this->carpeta.'buscador_ventanilla', $contenido);
        $data['menu_actual'] = 'buscador_tramites_cam';
        $data['tramites_menu'] = $this->tramitesMenu();
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
                    ' ', 'Fecha Derivación/Devolución', 'H.R. Madre','Fecha Mecanizada','Código Único','Denominación','Extensión','Departamento(s)','Provincia(s)','Municipio(s)','Área Protegida','Estado Trámite'
                );
                $campos_reales=array(
                    'ultimo_estado','ultimo_fecha_derivacion','correlativo','fecha_mecanizada','codigo_unico','denominacion','extension','departamentos','provincias','municipios','area_protegida','estado_tramite'
                );
                $campos = array('ac.id', 'ac.ultimo_estado', 'ac.correlativo', "to_char(ac.fecha_mecanizada, 'DD/MM/YYYY') as fecha_mecanizada", 'ac.codigo_unico', 'ac.denominacion', 'ac.extension', 'ac.departamentos', 'ac.provincias', 'ac.municipios', 'ac.area_protegida',
                "CASE WHEN ac.ultimo_fk_estado_tramite_hijo > 0 THEN CONCAT(etp.orden,'. ',etp.nombre,'<br>',etp.orden,'.',eth.orden,'. ',eth.nombre) ELSE CONCAT(etp.orden,'. ',etp.nombre) END as estado_tramite",
                "to_char(ac.ultimo_fecha_derivacion, 'DD/MM/YYYY') as ultimo_fecha_derivacion");
                $where = array(
                    'ac.deleted_at' => NULL,
                    'ac.fk_usuario_actual' => $this->request->getPost('id_usuario'),
                );
                $builder = $db->table('public.acto_administrativo as ac')
                ->select($campos)
                ->join('estado_tramite as etp', 'ac.ultimo_fk_estado_tramite_padre = etp.id', 'left')
                ->join('estado_tramite as eth', 'ac.ultimo_fk_estado_tramite_hijo = eth.id', 'left')
                ->where($where)
                ->orderBY('ac.ultimo_fecha_derivacion', 'ASC');
                $datos = $builder->get()->getResultArray();
                $contenido['datos'] = $datos;
                $contenido['campos_listar'] = $campos_listar;
                $contenido['campos_reales'] = $campos_reales;

                if ($this->request->getPost() && $this->request->getPost('enviar')=='excel') {
                    $this->exportarReporteUsuarios($campos_listar, $campos_reales, $datos,$arrayUsuarios[$this->request->getPost('id_usuario')]);
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
        echo view('templates/template', $data);
    }

    public function exportarReporteUsuarios($campos_listar, $campos_reales, $datos, $usuario){
        $file_name = 'reporte_usuarios-'.date('YmdHis').'.xlsx';
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
        $activeWorksheet->mergeCells('A1:L1');
        $activeWorksheet->getStyle('A1:L1')->applyFromArray($styleHeader);

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
                        'direccion_titular' => mb_strtoupper($this->request->getPost('direccion_titular')),
                        'telefono_titular' => mb_strtoupper($this->request->getPost('telefono_titular')),
                        'fk_estado_tramite' => $this->request->getPost('fk_estado_tramite'),
                        'fk_documento'=>((!empty($this->request->getPost('fk_documento'))) ? $this->request->getPost('fk_documento') : NULL),
                        'adjunto_pdf'=>((!empty($nombreAdjunto)) ? $nombreAdjunto : NULL),
                        'fecha_emision'=>((!empty($this->request->getPost('fecha_emision'))) ? $this->request->getPost('fecha_emision') : NULL),
                        'fecha_notificacion'=>((!empty($this->request->getPost('fecha_notificacion'))) ? $this->request->getPost('fecha_notificacion') : NULL),
                        'observaciones' => mb_strtoupper($this->request->getPost('observaciones')),
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
    public function verCorrespondenciaExterna($back,$id){
        $actoAdministrativoModel = new ActoAdministrativoModel();
        if($fila = $actoAdministrativoModel->find($id)){
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

            $campos = array('ce.id', 'ce.estado', "to_char(ce.created_at, 'DD/MM/YYYY HH24:MI') as fecha_ingreso", "CONCAT(ui.nombre_completo,'<br><b>',pi.nombre,'<b>') as ingreso",
            "to_char(ce.fecha_recepcion, 'DD/MM/YYYY HH24:MI') as fecha_recepcion", "CONCAT(ur.nombre_completo,'<br><b>',pr.nombre,'<b>') as recepcion",
            "CONCAT('CITE: ',ce.cite,'<br>Fecha: ',to_char(ce.fecha_cite, 'DD/MM/YYYY'),'<br>Remitente: ',CONCAT(pe.nombre_completo, ' (', pe.institucion, ' - ',pe.cargo,')'),'<br>Referencia: ',ce.referencia) as documento_externo",
            'ce.doc_digital'
            );
            $where = array(
                'ce.deleted_at' => NULL,
                'ce.fk_tramite' => $this->idTramite,
                'ce.fk_acto_administrativo' => $fila['id']
            );
            $builder = $db->table('public.correspondencia_externa AS ce')
            ->join('public.usuarios AS ui', 'ce.fk_usuario_creador = ui.id', 'left')
            ->join('public.perfiles AS pi', 'ui.fk_perfil = pi.id', 'left')
            ->join('public.usuarios AS ur', 'ce.fk_usuario_recepcion = ur.id', 'left')
            ->join('public.perfiles AS pr', 'ur.fk_perfil = pr.id', 'left')
            ->join('public.persona_externa AS pe', 'ce.fk_persona_externa = pe.id', 'left')
            ->select($campos)
            ->where($where)
            ->orderBy('ce.id', 'ASC');
            $derivaciones = $builder->get()->getResultArray();
            $contenido['derivaciones'] = $derivaciones;

            $cabecera_derivacion = array(
                '',
                'Fecha Ingreso',
                'Ingresado Por',
                'Fecha Recepción',
                'Recepcionado Por',
                'Documento Externo',
                'Doc. Digital',
            );
            $campos_derivacion = array(
                'estado',
                'fecha_ingreso',
                'ingreso',
                'fecha_recepcion',
                'recepcion',
                'documento_externo',
                'doc_digital',
            );

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
            $url_acto_administrativo = base_url($this->controlador.'ver/'.$back.'/'.$id);
            $url_sincobol = base_url($this->controlador.'sincobol/'.$back.'/'.$id);

            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Ver Estado Tramite';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['fila'] = $fila;
            $contenido['cabecera_derivacion'] = $cabecera_derivacion;
            $contenido['campos_derivacion'] = $campos_derivacion;
            $contenido['subtitulo'] = 'Ver Estado Tramite';
            $contenido['controlador'] = $this->controlador;
            $contenido['url_atras'] = $url_atras;
            $contenido['url_acto_administrativo'] = $url_acto_administrativo;
            $contenido['url_sincobol'] = $url_sincobol;
            $contenido['sincobol'] = $this->urlSincobol;
            $contenido['ruta_archivos'] = $this->rutaArchivos.$fila['fk_area_minera'].'/externo/';
            $data['content'] = view($this->carpeta.'ver_correspondencia_externa', $contenido);
            $data['menu_actual'] = $menuActual;
            $data['tramites_menu'] = $this->tramitesMenu();
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador.'buscador');
        }
    }

    public function sincobol($back,$id){
        $actoAdministrativoModel = new ActoAdministrativoModel();
        if($fila = $actoAdministrativoModel->find($id)){

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
            $contenido['hr_anexadas'] = $hr_anexadas;

            $where = array(
                'id' => $fila['fk_area_minera'],
            );
            $builder = $dbSincobol->table('contratos_licencias.area_minera')->where($where);
            $area_minera = $builder->get()->getFirstRow('array');
            $contenido['area_minera'] = $area_minera;

            $campos = array('d.id', 'd.tipo_documento_derivado', "UPPER(CONCAT(pr.nombres,' ',pr.apellido_paterno,' ',pr.apellido_materno, '<br>',cr.nombre)) as remitente",
            "UPPER(CONCAT(pd.nombres,' ',pd.apellido_paterno,' ',pd.apellido_materno, '<br>',cd.nombre)) as destinatario", 'UPPER(d.instruccion) as instruccion',
            "TO_CHAR(d.fecha_envio, 'DD/MM/YYYY') as fecha_envio", "TO_CHAR(d.fecha_recepcion, 'DD/MM/YYYY') as fecha_recepcion", "TO_CHAR(d.fecha_conclusion, 'DD/MM/YYYY') as fecha_conclusion",
            'd.estado');
            $where = array(
                'd.fk_hoja_ruta' => $fila['fk_hoja_ruta'],
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
            $derivaciones = $builder->get()->getResultArray();
            $contenido['derivaciones'] = $derivaciones;

            $cabecera_derivacion = array(
                'Tipo de documento',
                'Remitente',
                'Destinatario',
                'Instrucción',
                'Fecha derivación',
                'Fecha recepción',
                'Fecha conclusión',
                'Estado',
            );
            $campos_derivacion = array(
                'tipo_documento_derivado',
                'remitente',
                'destinatario',
                'instruccion',
                'fecha_envio',
                'fecha_recepcion',
                'fecha_conclusion',
                'estado',
            );

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
            $url_acto_administrativo = base_url($this->controlador.'ver/'.$back.'/'.$id);
            $url_externa = base_url($this->controlador.'ver_correspondencia_externa/'.$back.'/'.$id);

            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Ver Historial SINCOBOL';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['fila'] = $fila;
            $contenido['cabecera_derivacion'] = $cabecera_derivacion;
            $contenido['campos_derivacion'] = $campos_derivacion;
            $contenido['subtitulo'] = 'Ver Historial SINCOBOL';
            $contenido['controlador'] = $this->controlador;
            $contenido['url_atras'] = $url_atras;
            $contenido['url_acto_administrativo'] = $url_acto_administrativo;
            $contenido['url_externa'] = $url_externa;
            $contenido['sincobol'] = $this->urlSincobol;
            $contenido['ruta_archivos'] = $this->rutaArchivos;
            $data['content'] = view($this->carpeta.'sincobol', $contenido);
            $data['menu_actual'] = $menuActual;
            $data['tramites_menu'] = $this->tramitesMenu();
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador.'buscador');
        }
    }

    public function reporte(){
        $oficinas = $this->obtenerOficinasReporte();
        $estados_tramites = $this->obtenerEstadosTramites($this->idTramite);
        $clasificaciones = $this->obtenerClasificacionesTitulares();
        if ($this->request->getPost()) {
            $db = \Config\Database::connect();
            $oficina = $this->request->getPost('oficina');
            if($oficina > 0){
                /* consulta de oficina */
                $campos = array('ad.clasificacion_titular', "CONCAT(etp.orden,'. ', etp.nombre) as estado_padre", 'count(ad.correlativo) as n');
                $where = array(
                    'ad.deleted_at' => NULL,
                    'ad.fk_oficina' => $oficina
                );
                $builder = $db->table('public.acto_administrativo as ad')
                ->select($campos)
                ->join('public.estado_tramite as etp', 'ad.ultimo_fk_estado_tramite_padre = etp.id', 'left')
                ->join('public.estado_tramite as eth', 'ad.ultimo_fk_estado_tramite_hijo = eth.id', 'left')
                ->where($where)
                ->groupBy(array('ad.clasificacion_titular', 'etp.orden', 'estado_padre'))
                ->orderBY('ad.clasificacion_titular ASC, etp.orden ASC');
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
                $data['charts_js'] = 'chart_oficina_cam.js';

                if ($this->request->getPost() && $this->request->getPost('enviar')=='excel') {
                    $this->exportarReporteOficina($estados_tramites, $clasificaciones, $resultado, $oficinas[$oficina]);
                }

            }else{
                $campos = array('o.nombre as oficina', "CONCAT(etp.orden,'. ', etp.nombre) as estado_padre", 'count(ad.correlativo) as n');
                $where = array(
                    'ad.deleted_at' => NULL,
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
                $data['charts_js'] = 'chart_general_cam.js';

                if ($this->request->getPost() && $this->request->getPost('enviar')=='excel') {
                    $this->exportarReporteGeneral($estados_tramites, $oficinas, $resultado);
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
                $campos = array('ac.id', 'ac.correlativo', 'ac.codigo_unico', 'ac.denominacion', 'ac.ultimo_estado', "COUNT(doc.correlativo) AS documentos_generados", "COUNT(doc.doc_digital) AS documentos_digitales");
                $builder = $db->table('public.acto_administrativo as ac')
                ->select($campos)
                ->join('public.derivacion as der', 'ac.id = der.fk_acto_administrativo', 'left')
                ->join('public.documentos as doc', 'der.id = doc.fk_derivacion', 'left')
                ->groupBy(array('ac.id', 'ac.correlativo', 'ac.codigo_unico', 'ac.denominacion'))
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
                        $where['ac.codigo_unico'] = $texto;
                        $query = $builder->where($where);
                        break;
                    case 'denominacion':
                        $query = $builder->where($where)->like('ac.denominacion', $texto);
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
        echo view('templates/template', $data);
    }

    public function subirDocumentos($id){
        $actoAdministrativoModel = new ActoAdministrativoModel();
        if($fila = $actoAdministrativoModel->find($id)){
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

            $campos = array('doc.id', 'doc.correlativo', "to_char(doc.fecha, 'DD/MM/YYYY') as fecha", 'doc.referencia', 'tdoc.nombre as tipo_documento', "CONCAT(u.nombre_completo,'<br><b>',p.nombre,'</b>') as usuario",
            "doc.doc_digital");
            $where = array(
                'doc.id >' => 0,
                'der.fk_acto_administrativo' => $fila['id'],
                'doc.doc_digital' => NULL,
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
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador.'buscador');
        }
    }

    public function correspondenciaExterna($id){
        $actoAdministrativoModel = new ActoAdministrativoModel();
        if($fila = $actoAdministrativoModel->find($id)){
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
            "CONCAT('CITE: ',ce.cite,'<br>Fecha: ',to_char(ce.fecha_cite, 'DD/MM/YYYY'),'<br>Remitente: ',CONCAT(pe.nombre_completo, ' (', pe.institucion, ' - ',pe.cargo,')'),'<br>Referencia: ',ce.referencia) as documento_externo",
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
            $contenido['sincobol'] = $this->urlSincobol;
            $contenido['ruta_archivos'] = $this->rutaArchivos;
            $contenido['tipos_documentos_externos'] = $this->obtenerTiposDocumentosExternos();
            $contenido['accion'] = 'correspondencia_externa/recibir';
            $data['content'] = view($this->carpeta.'correspondencia_externa', $contenido);
            $data['menu_actual'] = $menuActual;
            $data['tramites_menu'] = $this->tramitesMenu();
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
                'doc_digital' => $nombreAdjunto,
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
        $builder = $db->table('public.acto_administrativo')
        ->select('DISTINCT(clasificacion_titular) AS clasificacion')
        ->where('deleted_at IS NULL')
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
                $builder = $db->table('mineria_ilegal.hojas_ruta_anexadas')->where($where);
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

    public function obtenerDocumentosAnexados($datos){
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
                            $html .= "<a href='".base_url($doc['doc_digital'])."' target='_blank' title='Ver Documento'>".$doc['correlativo']."</a><br>";
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
    public function obtnerDatosSelectHrInExSincobol($idHojaRuta){
        $dbSincobol = \Config\Database::connect('sincobol');
        $where = array(
            'anexado_siseg' => 'NO',
            'id' => $idHojaRuta
        );
        $builder = $dbSincobol->table('sincobol.vista_buscador_hoja_ruta_actualizado')
        ->where($where);
        return $builder->get()->getRowArray();
    }
    public function obtnerDatosSelectHrInExSincobolEditar($idHojaRuta){
        $dbSincobol = \Config\Database::connect('sincobol');
        $where = array(
            'id' => $idHojaRuta
        );
        $builder = $dbSincobol->table('sincobol.vista_buscador_hoja_ruta_actualizado')
        ->where($where);
        return $builder->get()->getRowArray();
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
                'pacm.tipo_relacion' => 'REPRESENTANTE LEGAL',
            );
            $builder = $dbSincobol->table('contratos_licencias.solicitud_licencia_contrato as slc')
            ->select($campos)
            ->join('sincobol.hoja_ruta as hr', 'slc.fk_hoja_ruta = hr.id', 'left')
            ->join('contratos_licencias.area_minera as am', 'slc.fk_area_minera = am.id', 'left')
            ->join('contratos_licencias.actor_minero as acm', 'am.fk_actor_minero_poseedor = acm.id', 'left')
            ->join('contratos_licencias.tipo_actor_minero as tam', 'acm.fk_tipo_actor_minero = tam.id', 'left')
            ->join('sincobol.datos_contacto_actor_minero as dcam', 'acm.id = dcam.fk_actor_minero', 'left')
            ->join('contratos_licencias.persona_actor_minero as pacm', 'acm.id = pacm.fk_actor_minero', 'left')
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

    public function ajaxBuscarTramite(){
        $cadena = mb_strtoupper($this->request->getPost('texto'));
        if(!empty($cadena) && session()->get('registroUser')){
            $db = \Config\Database::connect();
            $campos = array('id', 'correlativo', 'codigo_unico', 'denominacion',
            "CONCAT(correlativo,' (',codigo_unico,' - ',denominacion,')') as hr");
            $where = array(
                'deleted_at' => NULL,
                'fk_oficina' => session()->get('registroOficina')
            );
            $builder = $db->table('public.acto_administrativo')
            ->select($campos)
            ->where($where)
            ->like("CONCAT(correlativo,' (',codigo_unico,' - ',denominacion,')')", $cadena)
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
            $actoAdministrativoModel = new ActoAdministrativoModel();
            $resultado = array();
            if($tramite = $actoAdministrativoModel->find($id)){
                $resultado['codigo_unico'] = $tramite['codigo_unico'];
                $resultado['denominacion'] = $tramite['denominacion'];
                $resultado['representante_legal'] = $tramite['representante_legal'];
                $resultado['nacionalidad'] = $tramite['nacionalidad'];
                $resultado['titular'] = $tramite['titular'];
                $resultado['clasificacion'] = $tramite['clasificacion_titular'];
            }
            echo json_encode($resultado);
        }
    }

    public function denunciaManual(){
        $provincias = array();
        $municipios = array();
        if ($this->request->getPost()) {
            $denunciantesMineriaIlegalModel = new DenunciantesMineriaIlegalModel();
            $id_hojas_rutas = $this->request->getPost('id_hojas_rutas');
            $id_denunciantes = $this->request->getPost('id_denunciantes');
            $id_areas_mineras = $this->request->getPost('id_areas_mineras');
            $validation = $this->validate([
                'fk_oficina' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar la Dirección Departamental o Regional.',
                    ]
                ],
                'n_correlativo_hoja_ruta' => [
                    'rules' => 'required',
                ],
                'fecha_hoja_ruta' => [
                    'rules' => 'required',
                ],
                'fk_usuario_destino' => [
                    'rules' => 'required',
                ],
                'n_correlativo_denuncia' => [
                    'rules' => 'required',
                ],
                'fecha_denuncia' => [
                    'rules' => 'required',
                ],
                'fk_tipo_denuncia' => [
                    'rules' => 'required',
                ],
                'hr_anexados' => [
                    'rules' => 'required',
                ],
                'fk_municipio' => [
                    'rules' => 'required',
                ],
                'comunidad_localidad' => [
                    'rules' => 'required',
                ],
                'descripcion_lugar' => [
                    'rules' => 'required',
                ],
                'fk_estado_tramite' => [
                    'rules' => 'required',
                ],
            ]);
            if(!$validation){
                if(isset($id_hojas_rutas) && count($id_hojas_rutas) > 0){
                    $hojas_rutas = array();
                    foreach($id_hojas_rutas as $id_hoja_ruta)
                        $hojas_rutas[] = $this->obtenerDatosHrInExSincobol($id_hoja_ruta);
                    $contenido['hojas_rutas'] = $hojas_rutas;
                }
                if(isset($id_areas_mineras) && count($id_areas_mineras) > 0){
                    $areas_mineras = array();
                    foreach($id_areas_mineras as $id_area_minera)
                        $areas_mineras[] = $this->obtenerDatosAreaMineraMineriaIlegal($id_area_minera);
                    $contenido['areas_mineras'] = $areas_mineras;
                }
                $provincias = $this->obtenerProvincias($this->request->getPost('departamento'));
                $municipios = $this->obtenerMunicipios($this->request->getPost('departamento'), $this->request->getPost('provincia'));
                $contenido['id_estado_padre'] = $this->request->getPost('fk_estado_tramite');
                $contenido['estadosTramitesHijo'] = $this->obtenerEstadosTramitesHijo($this->request->getPost('fk_estado_tramite'));
                $contenido['id_estado_hijo'] = $this->request->getPost('fk_estado_tramite_hijo');
                $contenido['denunciantes'] = $denunciantesMineriaIlegalModel->whereIn('id', $id_denunciantes)->findAll();
                $contenido['usu_destinatario'] = $this->obtenerUsuario($this->request->getPost('fk_usuario_destino'));
                $contenido['validation'] = $this->validator;
            }else{
                $oficinaModel = new OficinasModel();
                $municipiosModel = new MunicipiosModel();
                $denunciasMineriaIlegalModel = new DenunciasMineriaIlegalModel();
                $denunciasHrSincobolMineriaIlegalModel = new DenunciasHrSincobolMineriaIlegalModel();
                $denunciantesMineriaIlegalModel = new DenunciantesMineriaIlegalModel();
                $denunciasDenunciantesMineriaIleglaModel = new DenunciasDenunciantesMineriaIlegalModel();
                $denunciasAreasMinerasMineriaIlegalModel = new DenunciasAreasMinerasMineriaIlegalModel();
                $coordenadasMineriaIlegalModel = new CoordenadasMineriaIlegalModel();
                $adjuntosMineriaIlegalModel = new AdjuntosMineriaIlegalModel();
                $hojaRutaMineriaIlegalModel = new HojaRutaMineriaIlegalModel();
                $derivacionMineriaIlegalModel = new DerivacionMineriaIlegalModel();
                $ubicacion = $municipiosModel->find($this->request->getPost('fk_municipio'));
                $oficinaDepartamento = $oficinaModel->like('departamentos_atencion', $ubicacion['departamento'])->first();
                $oficina = $oficinaModel->find($this->request->getPost('fk_oficina'));
                $n_correlativo_denuncia = $this->request->getPost('n_correlativo_denuncia');
                $fecha_denuncia = $this->request->getPost('fecha_denuncia');
                $correlativoDenuncia = $oficinaDepartamento['correlativo'].'FMI/'.$n_correlativo_denuncia.'/'.date("Y", strtotime($fecha_denuncia));
                $n_correlativo_hoja_ruta = $this->request->getPost('n_correlativo_hoja_ruta');
                $fecha_hoja_ruta = $this->request->getPost('fecha_hoja_ruta');
                $correlativoHR = $oficina['correlativo'].'MIN-ILEGAL/'.$n_correlativo_hoja_ruta.'/'.date("Y", strtotime($fecha_hoja_ruta));

                /* INFORME TECNICO*/
                $informeTecnicoDigital = $this->request->getFile('informe_tecnico_digital');
                $nombreInformeTecnicoDigital = '';
                if($informeTecnicoDigital->getBasename() != ''){
                    $nombreInformeTecnicoDigital = $informeTecnicoDigital->getRandomName();
                    $informeTecnicoDigital->move($this->rutaArchivos,$nombreInformeTecnicoDigital);
                    $nombreInformeTecnicoDigital = $this->rutaArchivos.$nombreInformeTecnicoDigital;
                }

                $data = array(
                    'fk_municipio' => $this->request->getPost('fk_municipio'),
                    'fk_tipo_denuncia' => $this->request->getPost('fk_tipo_denuncia'),
                    'correlativo' => $correlativoDenuncia,
                    'origen_oficio' => $this->request->getPost('origen_oficio'),
                    'enlace' => $this->request->getPost('enlace'),
                    'informe_tecnico_numero' => mb_strtoupper($this->request->getPost('informe_tecnico_numero')),
                    'informe_tecnico_fecha' => ((!empty($this->request->getPost('informe_tecnico_fecha'))) ? $this->request->getPost('informe_tecnico_fecha') : NULL),
                    'informe_tecnico_digital' => $nombreInformeTecnicoDigital,
                    'descripcion_oficio' => mb_strtoupper($this->request->getPost('descripcion_oficio')),
                    'comunidad_localidad' => mb_strtoupper($this->request->getPost('comunidad_localidad')),
                    'descripcion_lugar' => mb_strtoupper($this->request->getPost('descripcion_lugar')),
                    'autores' => mb_strtoupper($this->request->getPost('autores')),
                    'persona_juridica' => mb_strtoupper($this->request->getPost('persona_juridica')),
                    'descripcion_materiales' => mb_strtoupper($this->request->getPost('descripcion_materiales')),
                    'fk_usuario_creador' => session()->get('registroUser'),
                    'departamento' => $ubicacion['departamento'],
                    'provincia' => $ubicacion['provincia'],
                    'municipio' => $ubicacion['municipio'],
                    'tiene_area_minera' => ( isset($id_areas_mineras) && count($id_areas_mineras)>0) ? 'true' : 'false',
                    'manual' => 'true',
                    'fecha_denuncia' => $fecha_denuncia,
                    'estado_manual' => 'INGRESADO',
                    'n_correlativo_hoja_ruta' => $n_correlativo_hoja_ruta,
                    'n_correlativo_denuncia' => $n_correlativo_denuncia,
                );
                if($denunciasMineriaIlegalModel->insert($data) === false){
                    session()->setFlashdata('fail', $denunciasMineriaIlegalModel->errors());
                }else{
                    $idDenuncia = $denunciasMineriaIlegalModel->getInsertID();

                    if(isset($id_hojas_rutas) && count($id_hojas_rutas) > 0){
                        foreach($id_hojas_rutas as $id_hoja_ruta){
                            $datos_hr = $this->obtenerDatosHrInExSincobol($id_hoja_ruta);
                            $tmp_fecha = explode('/', $datos_hr['fecha']);
                            $dataHojaRutaSincobol = array(
                                'fk_denuncia' => $idDenuncia,
                                'fk_hoja_ruta' => $id_hoja_ruta,
                                'correlativo' => $datos_hr['correlativo'],
                                'fecha' => $tmp_fecha[2].'-'.$tmp_fecha[1].'-'.$tmp_fecha[0],
                                'referencia' => $datos_hr['referencia'],
                                'remitente' => $datos_hr['remitente'],
                                'cite' => $datos_hr['cite'],
                                'tipo_hoja_ruta' => $datos_hr['tipo_hoja_ruta'],
                            );
                            if($denunciasHrSincobolMineriaIlegalModel->insert($dataHojaRutaSincobol) === false)
                                session()->setFlashdata('fail', $denunciasHrSincobolMineriaIlegalModel->errors());
                            else
                                $this->archivarHrSincobolMejorado($id_hoja_ruta, $idDenuncia, session()->get('registroUserName'));
                        }
                    }

                    if(isset($id_denunciantes) && count($id_denunciantes) > 0){
                        foreach($id_denunciantes as $id_denunciante){
                            $dataDenunciaDenunciante = array(
                                'fk_denuncia' => $idDenuncia,
                                'fk_denunciante' => $id_denunciante,
                            );
                            if($denunciasDenunciantesMineriaIleglaModel->insert($dataDenunciaDenunciante) === false)
                                session()->setFlashdata('fail', $denunciasDenunciantesMineriaIleglaModel->errors());
                        }
                    }

                    if(isset($id_areas_mineras) && count($id_areas_mineras) > 0){
                        foreach($id_areas_mineras as $id_area_minera){
                            $dataAreaMinera = array(
                                'fk_denuncia' => $idDenuncia,
                                'fk_area_minera' => $id_area_minera,
                            );
                            if($denunciasAreasMinerasMineriaIlegalModel->insert($dataAreaMinera) === false)
                                session()->setFlashdata('fail', $denunciasAreasMinerasMineriaIlegalModel->errors());
                        }
                    }

                    $coordenadas = $this->obtenerCoordenadas($this->request->getPost('coordenadas'));
                    if(count($coordenadas)>0){
                        foreach($coordenadas as $coordenada){
                            $dataCoordenada = array(
                                'fk_denuncia' => $idDenuncia,
                                'latitud' => $coordenada['latitud'],
                                'longitud' => $coordenada['longitud'],
                            );
                            if($coordenadasMineriaIlegalModel->insert($dataCoordenada) === false)
                                session()->setFlashdata('fail', $coordenadasMineriaIlegalModel->errors());
                        }
                    }

                    if ($adjuntos = $this->request->getFiles()) {
                        foreach($adjuntos as $nombre => $adjunto){
                            if($nombre == 'adjuntos' && count($adjunto) > 0){
                                $nombres = $this->request->getPost('nombres');
                                $cites = $this->request->getPost('cites');
                                $fecha_cites = $this->request->getPost('fecha_cites');
                                foreach($adjunto as $i => $archivo){
                                    $tipoDocDigital = $this->obtenerTipoArchivo($archivo->guessExtension());
                                    $nombreDocDigital = $archivo->getRandomName();
                                    $archivo->move($this->rutaArchivos,$nombreDocDigital);
                                    $nombreDocDigital = $this->rutaArchivos.$nombreDocDigital;
                                    $dataAdjunto = array(
                                        'fk_denuncia' => $idDenuncia,
                                        'nombre' => mb_strtoupper($nombres[$i]),
                                        'cite' => mb_strtoupper($cites[$i]),
                                        'fecha_cite'=>((!empty($fecha_cites[$i])) ? $fecha_cites[$i] : NULL),
                                        'tipo' => $tipoDocDigital,
                                        'adjunto' => $nombreDocDigital,
                                        'fk_usuario_creador' => session()->get('registroUser'),
                                    );
                                    if($adjuntosMineriaIlegalModel->insert($dataAdjunto) === false)
                                        session()->setFlashdata('fail', $adjuntosMineriaIlegalModel->errors());
                                }
                            }
                        }
                    }
                    $estado = 'REGULARIZACIÓN';
                    $dataHojaRuta = array(
                        'fk_denuncia' => $idDenuncia,
                        'fk_oficina' => $oficina['id'],
                        'correlativo' => $correlativoHR,
                        'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                        'ultimo_estado' => $estado,
                        'ultimo_fk_estado_tramite_padre' => $this->request->getPost('fk_estado_tramite'),
                        'ultimo_fk_estado_tramite_hijo' => ((!empty($this->request->getPost('fk_estado_tramite_hijo'))) ? $this->request->getPost('fk_estado_tramite_hijo') : NULL),
                        'ultimo_instruccion' => 'REGISTRO MANUAL',
                        'fk_usuario_actual' => session()->get('registroUser'),
                        'ultimo_fk_usuario_responsable' => session()->get('registroUser'),
                        'ultimo_fk_usuario_remitente' => session()->get('registroUser'),
                        'ultimo_fk_usuario_destinatario' => session()->get('registroUser'),
                        'fk_usuario_creador' => session()->get('registroUser'),
                        'fk_usuario_destino' => $this->request->getPost('fk_usuario_destino'),
                        'fecha_hoja_ruta' => $fecha_hoja_ruta,
                    );
                    if($hojaRutaMineriaIlegalModel->insert($dataHojaRuta) === false){
                        session()->setFlashdata('fail', $hojaRutaMineriaIlegalModel->errors());
                    }else{
                        $idHojaRuta = $hojaRutaMineriaIlegalModel->getInsertID();
                        $dataDerivacion = array(
                            'fk_hoja_ruta' => $idHojaRuta,
                            'fk_estado_tramite_padre' => $this->request->getPost('fk_estado_tramite'),
                            'fk_estado_tramite_hijo' => ((!empty($this->request->getPost('fk_estado_tramite_hijo'))) ? $this->request->getPost('fk_estado_tramite_hijo') : NULL),
                            'observaciones' => mb_strtoupper($this->request->getPost('observaciones')),
                            'instruccion' => 'REGISTRO MANUAL',
                            'estado' => $estado,
                            'fk_usuario_responsable' => session()->get('registroUser'),
                            'fk_usuario_remitente' => session()->get('registroUser'),
                            'fk_usuario_destinatario' => session()->get('registroUser'),
                            'fk_usuario_creador' => session()->get('registroUser'),
                        );
                        if($derivacionMineriaIlegalModel->insert($dataDerivacion) === false)
                            session()->setFlashdata('fail', $derivacionMineriaIlegalModel->errors());
                        else
                            session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información. <code><a href="'.base_url($this->controlador.'formulario_denuncia_pdf/'.$idDenuncia).'" target="_blank">Descargar Formulario de Denuncia</a></code>  <code><a href="'.base_url($this->controlador.'hoja_ruta_pdf/'.$idHojaRuta).'" target="_blank">Descargar Hoja de Ruta</a></code>');
                    }
                }
                return redirect()->to($this->controlador.'mis_tramites');
            }
        }

        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Agregar Denuncia Manual de Minería Ilegal';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['subtitulo'] = 'Agregar Denuncia Manual de Minería Ilegal';
        $contenido['accion'] = $this->controlador.'denuncia_manual';
        $contenido['controlador'] = $this->controlador;
        $contenido['expedidos'] = $this->expedidos;
        $contenido['departamentos'] = array_merge(array(''=>'SELECCIONE EL DEPARTAMENTO'), $this->obtenerDepartamentos());
        $contenido['provincias'] = array_merge(array(''=>'SELECCIONE LA PROVINCIA'),$provincias);
        $contenido['municipios'] = array(''=>'SELECCIONE EL MUNICIPIO') + $municipios;
        $contenido['oficinas'] = array(''=>'SELECCIONE LA DIRECCIÓN DEPARTAMENTAL O REGIONAL') + $this->obtenerDireccionesRegionalesManual();
        $contenido['estadosTramites'] = $this->obtenerEstadosTramites($this->idTramite);
        $contenido['nuevo_denunciante'] = view($this->carpeta.'nuevo_denunciante', $contenido);
        $contenido['tipo_denuncias'] = array(''=>'SELECCIONE UNA OPCIÓN') + $this->tipoDenuncias;
        $contenido['tipos_origen_oficio'] = array_merge(array(''=>'SELECCIONE UNA OPCIÓN'), $this->tiposOrigenOficio);
        $data['content'] = view($this->carpeta.'denuncia_manual', $contenido);
        $data['menu_actual'] = $this->menuActual.'denuncia_manual';
        $data['validacion_js'] = 'mineria-ilegal-denuncia-manual-validation.js';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['alertas'] = $this->alertasTramites();
        $data['mapas'] = true;
        echo view('templates/template', $data);
    }
    public function listadoDenunciasManuales()
    {
        $db = \Config\Database::connect();
        $campos = array('d.id as id_denuncia','hr.id as id_hoja_ruta','d.estado_manual',
        "to_char(d.fecha_denuncia, 'DD/MM/YYYY') as fecha_denuncia", 'd.correlativo as correlativo_formulario_mineria_ilegal',
        "to_char(hr.fecha_hoja_ruta, 'DD/MM/YYYY') as fecha_hr", 'hr.correlativo as correlativo_hoja_ruta',
        'd.fk_tipo_denuncia', 'd.departamento',
        "CONCAT(uc.nombre_completo,'<br><b>',pc.nombre,'<b>') as usuario_creador"
        );
        $where = array(
            'd.deleted_at' => NULL,
            'hr.fk_usuario_creador' => session()->get('registroUser'),
            'd.manual' => 'true',
        );
        $builder = $db->table('mineria_ilegal.denuncias AS d')
        ->select($campos)
        ->join('mineria_ilegal.hoja_ruta AS hr', 'd.id = hr.fk_denuncia', 'left')
        ->join('usuarios as uc', 'hr.fk_usuario_creador = uc.id', 'left')
        ->join('perfiles as pc', 'uc.fk_perfil = pc.id', 'left')
        ->where($where)
        ->orderBY('d.id', 'DESC');
        $datos = $builder->get()->getResult('array');
        $datos = $this->obtenerDenunciantes($datos);
        $campos_listar=array(
            //'Estado',
            'Fecha', 'Hoja de Ruta', 'Fecha Denuncia', 'Formulario Minería Ilegal', 'Tipo', 'Denunciante', 'Usuario Creador'
        );
        $campos_reales=array(
            //'estado_manual',
            'fecha_hr', 'correlativo_hoja_ruta', 'fecha_denuncia', 'correlativo_formulario_mineria_ilegal', 'fk_tipo_denuncia', 'denunciante', 'usuario_creador'
        );

        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Listado de H.R. Minería Ilegal Manuales';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['datos'] = $datos;
        $contenido['campos_listar'] = $campos_listar;
        $contenido['campos_reales'] = $campos_reales;
        $contenido['subtitulo'] = 'Listado de H.R. Minería Ilegal Manuales';
        $contenido['controlador'] = $this->controlador;
        $contenido['id_tramite'] = $this->idTramite;
        $contenido['tipo_denuncias'] = $this->tipoDenuncias;
        $data['content'] = view($this->carpeta.'listado_denuncias_manuales', $contenido);
        $data['menu_actual'] = $this->menuActual.'listado_denuncias_manuales';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['alertas'] = $this->alertasTramites();
        echo view('templates/template', $data);
    }

    /*
    public function revisarDenunciaManual($id_hoja_ruta){
        $hojaRutaMineriaIlegalModel = new HojaRutaMineriaIlegalModel();
        $campos = array('*', 'fk_denuncia', 'correlativo as correlativo_hoja_ruta', "to_char(fecha_hoja_ruta, 'YYYY-MM-DD') as fecha_hoja_ruta" );
        if($hoja_ruta = $hojaRutaMineriaIlegalModel->select($campos)->find($id_hoja_ruta)){
            $tmp_correlativo_hr = explode('/', $hoja_ruta['correlativo_hoja_ruta']);
            $n_correlativo_hr = $tmp_correlativo_hr[(count($tmp_correlativo_hr) - 2)];
            $db = \Config\Database::connect();
            $derivacionMineriaIlegalModel = new DerivacionMineriaIlegalModel();
            $denunciasMineriaIlegalModel = new DenunciasMineriaIlegalModel();
            $denunciasHrSincobolMineriaIlegalModel = new DenunciasHrSincobolMineriaIlegalModel();
            $denunciasAreasMinerasMineriaIlegalModel = new DenunciasAreasMinerasMineriaIlegalModel();
            $coordenadasMineriaIlegalModel = new CoordenadasMineriaIlegalModel();
            $adjuntosMineriaIlegalModel = new AdjuntosMineriaIlegalModel();
            $denuncia = $denunciasMineriaIlegalModel->select("*, correlativo as correlativo_denuncia, to_char(fecha_denuncia, 'YYYY-MM-DD') as fecha_denuncia")->find($hoja_ruta['fk_denuncia']);
            $tmp_correlativo_fm = explode('/', $denuncia['correlativo_denuncia']);
            $n_correlativo_fm = $tmp_correlativo_fm[(count($tmp_correlativo_fm) - 2)];
            $coordenadas = $coordenadasMineriaIlegalModel->where(array('fk_denuncia' => $hoja_ruta['fk_denuncia']))->findAll();

            $campos = array('de.id', 'de.nombres', 'de.apellidos', 'de.documento_identidad', 'de.expedido', "de.telefonos", "de.email", 'de.direccion', 'de.documento_identidad_digital');
            $where = array(
                'dd.fk_denuncia' => $hoja_ruta['fk_denuncia'],
            );
            $builder = $db->table('mineria_ilegal.denuncias_denunciantes AS dd')
            ->select($campos)
            ->join('mineria_ilegal.denunciantes AS de', 'dd.fk_denunciante = de.id', 'left')
            ->where($where)
            ->orderBY('dd.id', 'ASC');
            $denunciantes = $builder->get()->getResultArray();
            $id_denunciantes_ant = '';
            foreach($denunciantes as $denunciante)
                $id_denunciantes_ant .= $denunciante['id'].',';

            $denunciasAreasMineras = $denunciasAreasMinerasMineriaIlegalModel->where(array('fk_denuncia'=>$hoja_ruta['fk_denuncia']))->orderBy('id', 'ASC')->findAll();
            $areas_mineras = array();
            $id_areas_mineras_ant = '';
            if($denunciasAreasMineras){
                foreach($denunciasAreasMineras as $row){
                    $areas_mineras[] = $this->obtenerDatosAreaMineraMineriaIlegal($row['fk_area_minera']);
                    $id_areas_mineras_ant .= $row['fk_area_minera'].',';
                }
            }

            $campos = array('de.id', 'de.nombres', 'de.apellidos', 'de.documento_identidad', 'de.expedido', "de.telefonos", "de.email", 'de.direccion', 'de.documento_identidad_digital');
            $where = array(
                'dd.fk_denuncia' => $hoja_ruta['fk_denuncia'],
            );
            $builder = $db->table('mineria_ilegal.denuncias_denunciantes AS dd')
            ->select($campos)
            ->join('mineria_ilegal.denunciantes AS de', 'dd.fk_denunciante = de.id', 'left')
            ->where($where)
            ->orderBY('dd.id', 'ASC');
            $denunciantes = $builder->get()->getResultArray();
            $id_denunciantes_ant = '';
            foreach($denunciantes as $denunciante)
                $id_denunciantes_ant .= $denunciante['id'].',';

            $provincias = $this->obtenerProvincias($denuncia['departamento']);
            $municipios = $this->obtenerMunicipios($denuncia['departamento'], $denuncia['provincia']);
            $cabera['titulo'] = $this->titulo;
            $cabera['navegador'] = true;
            $cabera['subtitulo'] = 'Revisar el Registro Manual de Minería Ilegal';
            $contenido['title'] = view('templates/title',$cabera);
            $contenido['subtitulo'] = 'Revisar el Registro Manual de Minería Ilegal';
            $contenido['accion'] = $this->controlador.'guardar_revisar_denuncia_manual';
            $contenido['controlador'] = $this->controlador;
            $contenido['hoja_ruta'] = $hoja_ruta;
            $contenido['n_correlativo_hr'] = $n_correlativo_hr;
            $contenido['ultima_derivacion'] = $derivacionMineriaIlegalModel->where(array('fk_hoja_ruta' => $hoja_ruta['id']))->orderBy('id', 'DESC')->first();
            $contenido['usu_destinatario'] = $this->obtenerUsuario($hoja_ruta['ultimo_fk_usuario_destinatario']);
            $contenido['denuncia'] = $denuncia;
            $contenido['n_correlativo_fm'] = $n_correlativo_fm;
            $contenido['tipo_denuncia'] = $denuncia['fk_tipo_denuncia'];
            $contenido['id_denunciantes_ant'] = substr($id_denunciantes_ant, 0, -1);
            $contenido['id_areas_mineras_ant'] = substr($id_areas_mineras_ant, 0, -1);
            $contenido['areas_mineras'] = $areas_mineras;
            $contenido['denunciantes'] = $denunciantes;
            $contenido['coordenadas'] = $this->transformarCoordenadas($coordenadas);
            $contenido['adjuntos'] = $adjuntosMineriaIlegalModel->where(array('fk_denuncia' => $hoja_ruta['fk_denuncia']))->findAll();
            $contenido['expedidos'] = $this->expedidos;
            $contenido['tipo_denuncias'] = $this->tipoDenuncias;
            $contenido['departamentos'] = array_merge(array(''=>'SELECCIONE EL DEPARTAMENTO'), $this->obtenerDepartamentos());
            $contenido['provincias'] = array_merge(array(''=>'SELECCIONE LA PROVINCIA'),$provincias);
            $contenido['municipios'] = array(''=>'SELECCIONE EL MUNICIPIO') + $municipios;
            $contenido['estadosTramites'] = $this->obtenerEstadosTramites($this->idTramite);
            $contenido['id_estado_padre'] = $hoja_ruta['ultimo_fk_estado_tramite_padre'];
            if($hoja_ruta['ultimo_fk_estado_tramite_hijo']){
                $contenido['estadosTramitesHijo'] = $this->obtenerEstadosTramitesHijo($hoja_ruta['ultimo_fk_estado_tramite_padre']);
                $contenido['id_estado_hijo'] = $hoja_ruta['ultimo_fk_estado_tramite_hijo'];
                //$contenido['anexar_documentos'] = $hoja_ruta['anexar_documentos_hijo'];
                $contenido['anexar_documentos'] = '';
            }else{
                $contenido['anexar_documentos'] = '';
                //$contenido['anexar_documentos'] = $ultima_derivacion['anexar_documentos_padre'];
            }
            $contenido['oficinas'] = array_merge(array(''=>'SELECCIONE LA DIRECCIÓN DEPARTAMENTAL O REGIONAL'), $this->obtenerDireccionesRegionales());
            $contenido['id_tramite'] = $this->idTramite;
            $contenido['nuevo_denunciante'] = view($this->carpeta.'nuevo_denunciante', $contenido);
            $data['content'] = view($this->carpeta.'revisar_denuncia_manual', $contenido);
            $data['menu_actual'] = $this->menuActual.'listado_denuncias_manuales';
            $data['validacion_js'] = 'mineria-ilegal-denuncia-manual-validation.js';
            $data['tramites_menu'] = $this->tramitesMenu();
            $data['alertas'] = $this->alertasTramites();
            $data['mapas'] = true;
            $data['puntos'] = $coordenadas;
            echo view('templates/template', $data);
        }else{
            session()->setFlashdata('fail', 'El registro no existe.');
            return redirect()->to($this->controlador);
        }
    }
    public function guardarRevisarDenunciaManual(){
        if ($this->request->getPost()) {
            $denunciantesMineriaIlegalModel = new DenunciantesMineriaIlegalModel();
            $adjuntosMineriaIlegalModel = new AdjuntosMineriaIlegalModel();
            $id_hoja_ruta = $this->request->getPost('id_hoja_ruta');
            $id_denuncia = $this->request->getPost('id_denuncia');
            $id_denunciantes = $this->request->getPost('id_denunciantes');
            $id_areas_mineras = $this->request->getPost('id_areas_mineras');
            $tipo_denuncia = $this->request->getPost('tipo_denuncia');
            $id_hojas_rutas = $this->request->getPost('id_hojas_rutas');
            $camposValidacion = array(
                'id_hoja_ruta' => [
                    'rules' => 'required',
                ],
                'id_denuncia' => [
                    'rules' => 'required',
                ],
                'fk_municipio' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Debe seleccionar el Municipio.',
                    ]
                ],
                'comunidad_localidad' => [
                    'rules' => 'required',
                ],
                'descripcion_lugar' => [
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
            );
            if($tipo_denuncia==3){
                $camposValidacion = array_merge($camposValidacion, array(
                    'origen_oficio' => [
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'Debe seleccionar una opción.',
                        ]
                    ],
                    'informe_tecnico_numero' => [
                        'rules' => 'required',
                    ],
                    'informe_tecnico_fecha' => [
                        'rules' => 'required',
                    ],
                    'descripcion_oficio' => [
                        'rules' => 'required',
                    ],
                ));
            }

            if(!$this->validate($camposValidacion)){
                if(isset($id_hojas_rutas) && count($id_hojas_rutas) > 0){
                    $hojas_rutas = array();
                    foreach($id_hojas_rutas as $id_hoja_ruta)
                        $hojas_rutas[] = $this->obtenerDatosHrInExSincobol($id_hoja_ruta);
                    $contenido['hojas_rutas'] = $hojas_rutas;
                }
                if(isset($id_areas_mineras) && count($id_areas_mineras) > 0){
                    $areas_mineras = array();
                    foreach($id_areas_mineras as $id_area_minera)
                        $areas_mineras[] = $this->obtenerDatosAreaMineraMineriaIlegal($id_area_minera);
                    $contenido['areas_mineras'] = $areas_mineras;
                }
                $provincias = $this->obtenerProvincias($this->request->getPost('departamento'));
                $municipios = $this->obtenerMunicipios($this->request->getPost('departamento'), $this->request->getPost('provincia'));

                $cabera['titulo'] = $this->titulo;
                $cabera['navegador'] = true;
                $cabera['subtitulo'] = 'Atender Hoja de Ruta de Minería Ilegal';
                $contenido['title'] = view('templates/title',$cabera);
                $contenido['subtitulo'] = 'Atender Hoja de Ruta de Minería Ilegal';
                $contenido['id_hoja_ruta'] = $id_hoja_ruta;
                $contenido['tipo_denuncia'] = $tipo_denuncia;
                $contenido['informe_tecnico_digital'] = $this->request->getPost('informe_tecnico_digital');
                if($this->request->getPost('id_documentos'))
                    $contenido['documentos'] = $this->obtenerDatosDocumento($this->request->getPost('id_documentos'), $this->request->getPost('fecha_notificaciones'));
                if($tipo_denuncia!=3)
                    $contenido['denunciantes'] = $denunciantesMineriaIlegalModel->whereIn('id', $id_denunciantes)->findAll();
                $contenido['usu_destinatario'] = $this->obtenerUsuario($this->request->getPost('fk_usuario_destinatario'));
                $contenido['accion'] = $this->controlador.'guardar_atender';
                $contenido['controlador'] = $this->controlador;
                $contenido['adjuntos'] = $adjuntosMineriaIlegalModel->where(array('fk_denuncia' => $id_denuncia))->findAll();
                $contenido['expedidos'] = $this->expedidos;
                $contenido['tipo_denuncias'] = $this->tipoDenuncias;
                $contenido['tipos_origen_oficio'] = array_merge(array(''=>'SELECCIONE UNA OPCIÓN'), $this->tiposOrigenOficio);
                $contenido['departamentos'] = array_merge(array(''=>'SELECCIONE EL DEPARTAMENTO'), $this->obtenerDepartamentos());
                $contenido['provincias'] = array_merge(array(''=>'SELECCIONE LA PROVINCIA'),$provincias);
                $contenido['municipios'] = array(''=>'SELECCIONE EL MUNICIPIO') + $municipios;
                $contenido['oficinas'] = array_merge(array(''=>'SELECCIONE LA DIRECCIÓN DEPARTAMENTAL O REGIONAL'), $this->obtenerDireccionesRegionales());
                $contenido['id_tramite'] = $this->idTramite;
                $contenido['estadosTramites'] = $this->obtenerEstadosTramites($this->idTramite);
                $contenido['id_estado_padre'] = $this->request->getPost('fk_estado_tramite');
                $contenido['estadosTramitesHijo'] = $this->obtenerEstadosTramitesHijo($this->request->getPost('fk_estado_tramite'));
                $contenido['id_estado_hijo'] = $this->request->getPost('fk_estado_tramite_hijo');
                $contenido['nuevo_denunciante'] = view($this->carpeta.'nuevo_denunciante', $contenido);
                $data['content'] = view($this->carpeta.'atender', $contenido);
                $data['menu_actual'] = $this->menuActual.'mis_tramites';
                switch($tipo_denuncia){
                    case 1:
                    case 2:
                        $data['validacion_js'] = 'mineria-ilegal-atender-denunciante-validation.js';
                        break;
                    case 3:
                        $data['validacion_js'] = 'mineria-ilegal-atender-origen-validation.js';
                        break;
                }
                $data['validacion_js'] = 'mineria-ilegal-atender-validation.js';
                $data['tramites_menu'] = $this->tramitesMenu();
                $data['alertas'] = $this->alertasTramites();
                $data['mapas'] = true;
                echo view('templates/template', $data);
            }else{
                $municipiosModel = new MunicipiosModel();
                $denunciasMineriaIlegalModel = new DenunciasMineriaIlegalModel();
                $denunciasHrSincobolMineriaIlegalModel = new DenunciasHrSincobolMineriaIlegalModel();
                $denunciasDenunciantesMineriaIleglaModel = new DenunciasDenunciantesMineriaIlegalModel();
                $denunciasAreasMinerasMineriaIlegalModel = new DenunciasAreasMinerasMineriaIlegalModel();
                $coordenadasMineriaIlegalModel = new CoordenadasMineriaIlegalModel();
                $hojaRutaMineriaIlegalModel = new HojaRutaMineriaIlegalModel();
                $derivacionMineriaIlegalModel = new DerivacionMineriaIlegalModel();
                $documentosModel = new DocumentosModel();

                $ubicacion = $municipiosModel->find($this->request->getPost('fk_municipio'));
                $motivo_anexo = mb_strtoupper($this->request->getPost('motivo_anexo'));
                if($tipo_denuncia==3)
                    $dataDenuncia = array(
                        'id' => $id_denuncia,
                        'fk_municipio' => $this->request->getPost('fk_municipio'),
                        'origen_oficio' => $this->request->getPost('origen_oficio'),
                        'enlace' => $this->request->getPost('enlace'),
                        'informe_tecnico_numero' => mb_strtoupper($this->request->getPost('informe_tecnico_numero')),
                        'informe_tecnico_fecha' => $this->request->getPost('informe_tecnico_fecha'),
                        'descripcion_oficio' => mb_strtoupper($this->request->getPost('descripcion_oficio')),
                        'comunidad_localidad' => mb_strtoupper($this->request->getPost('comunidad_localidad')),
                        'descripcion_lugar' => mb_strtoupper($this->request->getPost('descripcion_lugar')),
                        'autores' => mb_strtoupper($this->request->getPost('autores')),
                        'persona_juridica' => mb_strtoupper($this->request->getPost('persona_juridica')),
                        'descripcion_materiales' => mb_strtoupper($this->request->getPost('descripcion_materiales')),
                        'fk_usuario_editor' => session()->get('registroUser'),
                        'departamento' => $ubicacion['departamento'],
                        'provincia' => $ubicacion['provincia'],
                        'municipio' => $ubicacion['municipio'],
                        'tiene_area_minera' => ( isset($id_areas_mineras) && count($id_areas_mineras)>0) ? 'true' : 'false',
                    );
                else
                    $dataDenuncia = array(
                        'id' => $id_denuncia,
                        'fk_municipio' => $this->request->getPost('fk_municipio'),
                        'comunidad_localidad' => mb_strtoupper($this->request->getPost('comunidad_localidad')),
                        'descripcion_lugar' => mb_strtoupper($this->request->getPost('descripcion_lugar')),
                        'autores' => mb_strtoupper($this->request->getPost('autores')),
                        'persona_juridica' => mb_strtoupper($this->request->getPost('persona_juridica')),
                        'descripcion_materiales' => mb_strtoupper($this->request->getPost('descripcion_materiales')),
                        'fk_usuario_editor' => session()->get('registroUser'),
                        'departamento' => $ubicacion['departamento'],
                        'provincia' => $ubicacion['provincia'],
                        'municipio' => $ubicacion['municipio'],
                        'tiene_area_minera' => ( isset($id_areas_mineras) && count($id_areas_mineras)>0) ? 'true' : 'false',
                    );

                if($denunciasMineriaIlegalModel->save($dataDenuncia) === false)
                    session()->setFlashdata('fail', $denunciasMineriaIlegalModel->errors());

                if(isset($id_denunciantes) && implode(',',$id_denunciantes) != $this->request->getPost('id_denunciantes_ant')){
                    $this->liberarDenunciantes($id_denuncia);
                    foreach($id_denunciantes as $id_denunciante){
                        $dataDenunciaDenunciante = array(
                            'fk_denuncia' => $id_denuncia,
                            'fk_denunciante' => $id_denunciante,
                        );
                        if($denunciasDenunciantesMineriaIleglaModel->insert($dataDenunciaDenunciante) === false)
                            session()->setFlashdata('fail', $denunciasDenunciantesMineriaIleglaModel->errors());
                    }
                }

                if(isset($id_hojas_rutas) && implode(',',$id_hojas_rutas) != $this->request->getPost('id_hojas_ruta_ant')){
                    $this->liberarHojasRutaSincobol($id_denuncia);
                    foreach($id_hojas_rutas as $id_hoja_ruta){
                        $dataHojaRutaSincobol = array(
                            'fk_denuncia' => $id_denuncia,
                            'fk_hoja_ruta' => $id_hoja_ruta,
                        );
                        if($denunciasHrSincobolMineriaIlegalModel->insert($dataHojaRutaSincobol) === false)
                            session()->setFlashdata('fail', $denunciasHrSincobolMineriaIlegalModel->errors());
                        else
                            $this->archivarHrSincobol($id_hoja_ruta, 'ANEXADO AL SISTEMA DE SEGUIMIENTO Y CONTROL DE TRAMITES POR '.session()->get('registroUserName'));
                    }
                }

                if(isset($id_areas_mineras) && implode(',',$id_areas_mineras) != $this->request->getPost('id_areas_mineras_ant')){
                    $this->liberarAreasMineras($id_denuncia);
                    foreach($id_areas_mineras as $id_area_minera){
                        $dataAreaMinera = array(
                            'fk_denuncia' => $id_denuncia,
                            'fk_area_minera' => $id_area_minera,
                        );
                        if($denunciasAreasMinerasMineriaIlegalModel->insert($dataAreaMinera) === false)
                            session()->setFlashdata('fail', $denunciasAreasMinerasMineriaIlegalModel->errors());
                    }
                }

                if($this->obtenerCoordenadas($this->request->getPost('coordenadas')) !== $this->obtenerCoordenadas($this->request->getPost('coordenadas_ant'))){
                    $this->vaciarCoordenadas($id_denuncia);
                    $coordenadas = $this->obtenerCoordenadas($this->request->getPost('coordenadas'));
                    if(count($coordenadas)>0){
                        foreach($coordenadas as $coordenada){
                            $dataCoordenada = array(
                                'fk_denuncia' => $id_denuncia,
                                'latitud' => $coordenada['latitud'],
                                'longitud' => $coordenada['longitud'],
                            );
                            if($coordenadasMineriaIlegalModel->insert($dataCoordenada) === false)
                                session()->setFlashdata('fail', $coordenadasMineriaIlegalModel->errors());
                        }
                    }
                }

                if ($adjuntos = $this->request->getFiles()) {
                    foreach($adjuntos as $nombre => $adjunto){
                        if($nombre == 'adjuntos' && count($adjunto) > 0){
                            $nombres = $this->request->getPost('nombres');
                            $cites = $this->request->getPost('cites');
                            $fecha_cites = $this->request->getPost('fecha_cites');
                            foreach($adjunto as $i => $archivo){
                                $tipoDocDigital = $this->obtenerTipoArchivo($archivo->guessExtension());
                                $nombreDocDigital = $archivo->getRandomName();
                                $archivo->move($this->rutaArchivos,$nombreDocDigital);
                                $nombreDocDigital = $this->rutaArchivos.$nombreDocDigital;
                                $dataAdjunto = array(
                                    'fk_denuncia' => $id_denuncia,
                                    'nombre' => mb_strtoupper($nombres[$i]),
                                    'cite' => mb_strtoupper($cites[$i]),
                                    'fecha_cite'=>((!empty($fecha_cites[$i])) ? $fecha_cites[$i] : NULL),
                                    'tipo' => $tipoDocDigital,
                                    'adjunto' => $nombreDocDigital,
                                    'fk_usuario_creador' => session()->get('registroUser'),
                                );
                                if($adjuntosMineriaIlegalModel->insert($dataAdjunto) === false)
                                    session()->setFlashdata('fail', $adjuntosMineriaIlegalModel->errors());
                            }
                        }
                    }
                }

                $estado = 'DERIVADO';
                $dataHojaRuta = array(
                    'id' => $id_hoja_ruta,
                    'ultimo_fecha_derivacion' => date('Y-m-d H:i:s'),
                    'ultimo_estado' => $estado,
                    'ultimo_fk_estado_tramite_padre' => $this->request->getPost('fk_estado_tramite'),
                    'ultimo_fk_estado_tramite_hijo' => ((!empty($this->request->getPost('fk_estado_tramite_hijo'))) ? $this->request->getPost('fk_estado_tramite_hijo') : NULL),
                    'ultimo_instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                    'ultimo_fk_usuario_remitente' => session()->get('registroUser'),
                    'ultimo_fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                    'ultimo_fk_usuario_responsable'=>($this->request->getPost('responsable') ? $this->request->getPost('fk_usuario_destinatario') : $this->request->getPost('ultimo_fk_usuario_responsable')),
                    'editar' => 'true',
                );

                if($hojaRutaMineriaIlegalModel->save($dataHojaRuta) === false){
                    session()->setFlashdata('fail', $hojaRutaMineriaIlegalModel->errors());
                }else{
                    $dataDerivacion = array(
                        'fk_hoja_ruta' => $id_hoja_ruta,
                        'fk_estado_tramite_padre' => $this->request->getPost('fk_estado_tramite'),
                        'fk_estado_tramite_hijo' => ((!empty($this->request->getPost('fk_estado_tramite_hijo'))) ? $this->request->getPost('fk_estado_tramite_hijo') : NULL),
                        'observaciones' => mb_strtoupper($this->request->getPost('observaciones')),
                        'instruccion' => mb_strtoupper($this->request->getPost('instruccion')),
                        'motivo_anexo' => $motivo_anexo,
                        'fk_usuario_remitente' => session()->get('registroUser'),
                        'fk_usuario_destinatario' => $this->request->getPost('fk_usuario_destinatario'),
                        'estado' => $estado,
                        'fk_usuario_creador' => session()->get('registroUser'),
                        'fk_usuario_responsable'=>($this->request->getPost('responsable') ? $this->request->getPost('fk_usuario_destinatario') : $this->request->getPost('ultimo_fk_usuario_responsable')),
                    );

                    if($derivacionMineriaIlegalModel->insert($dataDerivacion) === false){
                        session()->setFlashdata('fail', $derivacionMineriaIlegalModel->errors());
                    }else{
                        $idDerivacion = $derivacionMineriaIlegalModel->getInsertID();
                        if($this->request->getPost('id_documentos')){
                            $documentos = $this->request->getPost('id_documentos');
                            $fecha_notificaciones = $this->request->getPost('fecha_notificaciones');
                            foreach($documentos as $i=>$id_documento){
                                $dataDocumento = array(
                                    'id' => $id_documento,
                                    'estado' => 'ANEXADO',
                                    'fk_derivacion' => $idDerivacion,
                                    'fecha_notificacion'=>((!empty($fecha_notificaciones[$i])) ? $fecha_notificaciones[$i] : NULL),
                                );
                                if($documentosModel->save($dataDocumento) === false){
                                    session()->setFlashdata('fail', $documentosModel->errors());
                                }
                            }
                        }

                        if($this->request->getPost('anexar_hr')){
                            $hr_anexar = $this->request->getPost('anexar_hr');
                            foreach($hr_anexar as $fk_hoja_ruta){
                                if(!$this->anexarHrSincobol($idDerivacion,$fk_hoja_ruta,$motivo_anexo))
                                    session()->setFlashdata('fail', 'No se anexo la H.R.'.$fk_hoja_ruta);
                            }
                        }

                        $dataDerivacionActualizacion = array(
                            'id' => $this->request->getPost('id_derivacion'),
                            'estado' => 'ATENDIDO',
                            'fecha_atencion' => date('Y-m-d H:i:s'),
                        );
                        if($derivacionMineriaIlegalModel->save($dataDerivacionActualizacion) === false)
                            session()->setFlashdata('fail', $derivacionMineriaIlegalModel->errors());
                        else
                            session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información.');

                    }
                    session()->setFlashdata('success', 'Se ha Guardado Correctamente la Información.');
                }
                return redirect()->to($this->controlador.'mis_tramites');
            }
        }
    }
    */

    private function obtenerDocumentosAtender($fk_hoja_ruta){
        $db = \Config\Database::connect();
        $campos = array('doc.id', 'doc.correlativo', "TO_CHAR(doc.fecha, 'DD/MM/YYYY') as fecha", 'td.nombre as tipo_documento', 'td.notificacion');
        $where = array(
            'doc.fk_usuario_creador' => session()->get('registroUser'),
            'doc.fk_derivacion' => NULL,
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
    private function obtenerDocumentosEditar($fk_hoja_ruta, $id_derivacion){
        $db = \Config\Database::connect();
        $campos = array('doc.id', 'doc.correlativo', 'doc.fecha_notificacion', 'doc.doc_digital', "TO_CHAR(doc.fecha, 'DD/MM/YYYY') as fecha", 'td.nombre as tipo_documento', 'td.notificacion');
        $where = array(
            'doc.fk_usuario_creador' => session()->get('registroUser'),
            'doc.fk_derivacion' => $id_derivacion,
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