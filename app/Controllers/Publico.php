<?php

namespace App\Controllers;

use App\Libraries\DenunciaPdf;
use App\Models\ActoAdministrativoModel;
use App\Models\AdjuntosMineriaIlegalModel;
use App\Models\CoordenadasMineriaIlegalModel;
use App\Models\CoordenadasWebMineriaIlegalModel;
use App\Models\DocumentosModel;
use App\Models\TramitesModel;
use App\Models\CorrelativosMineriaIlegalModel;
use App\Models\DenunciasMineriaIlegalModel;
use App\Models\DenunciasWebMineriaIlegalModel;
use App\Models\MunicipiosModel;
use App\Models\PersonaExternaModel;

class Publico extends BaseController
{
    protected $controlador = 'publico/';
    protected $carpeta = 'publico/';
    protected $tipoDenuncias = array(
        1 => 'PAGINA WEB',
    );
    protected $fontPDF = 'helvetica';
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

    public function seguimientoCAM(){

        if ($this->request->getPost()) {
            $validation = $this->validate([
                'hr_madre' => [
                    'rules' => 'required',
                ],
                'codigo_unico' => [
                    'rules' => 'required',
                ],
                'codigo_seguimiento' => [
                    'rules' => 'required',
                ],
            ]);
            if(!$validation){
                $data['validation'] = $this->validator;
            }else{
                $db = \Config\Database::connect();
                $campos = array('ac.id','dam.codigo_unico', 'dam.denominacion', "TO_CHAR(ac.ultimo_fecha_derivacion, 'DD/MM/YYYY') as fecha",
                "CONCAT(ua.nombre_completo,'<br><b>',pa.nombre,'<b>') as usuario_actual", 'ac.ultimo_fk_documentos', 'ac.estado_tramite_apm', 'ac.documentos_apm',
                'ac.ultimo_recurso_jerarquico', 'ac.ultimo_recurso_revocatoria', 'ac.ultimo_oposicion', 'ua.atencion');
                $where = array(
                    'ac.deleted_at' => NULL,
                    'ac.correlativo' => trim(mb_strtoupper($this->request->getPost('hr_madre'))),
                    'dam.codigo_unico' => trim($this->request->getPost('codigo_unico')),
                    'ac.codigo_seguimiento' => trim($this->request->getPost('codigo_seguimiento')),
                );
                $builder = $db->table('public.acto_administrativo as ac')
                ->select($campos)
                ->join('public.datos_area_minera as dam', 'ac.id = dam.fk_acto_administrativo', 'left')
                ->join('usuarios as ua', 'ac.fk_usuario_actual = ua.id', 'left')
                ->join('perfiles as pa', 'ua.fk_perfil = pa.id', 'left')
                ->where($where);
                if($tramite = $builder->get()->getRowArray()){
                    $observaciones = '';

                    if($tramite['ultimo_recurso_jerarquico'] == 't')
                        $observaciones .= 'RECURSO JERÁRQUICO <br>';
                    if($tramite['ultimo_recurso_revocatoria'] == 't')
                        $observaciones .= 'RECURSO DE REVOCATORIA <br>';
                    if($tramite['ultimo_oposicion'] == 't')
                        $observaciones .= 'OPOSICIÓN';

                    $data['style'] = 'alert-success';
                    $data['titulo'] = 'SE ENCONTRO EL TRÁMITE';
                    $data['fecha'] = $tramite['fecha'];
                    $data['estado_actual'] = $tramite['estado_tramite_apm'];
                    $data['observaciones'] = $observaciones;
                    $data['documentos'] = $tramite['documentos_apm'];
                    $data['usuario_actual'] = $tramite['usuario_actual'];
                    $data['atencion'] = $tramite['atencion'];
                }else{
                    $data['style'] = 'alert-danger';
                    $data['titulo'] = 'NO SE ENCONTRO EL TRÁMITE O EL CÓDIGO DE SEGUIMIENTO ES ERRÓNEO';
                    $data['contenido'] = 'Lamentablemente no se tuvo un resultado con los datos proporcionados, puede aproximarse a la Oficina Nacional, Dirección y/o Regional que le corresponda para averiguar.';
                }
                $data['response'] = true;
            }
        }

        $data['accion'] = 'seguimiento_cam';
        $data['enlaces'] = view($this->carpeta.'enlaces');
        return view($this->carpeta.'seguimiento_cam', $data);
    }
    public function seguimientoMineriaIlegal(){

        if ($this->request->getPost()) {
            $validation = $this->validate([
                'correlativo' => [
                    'rules' => 'required',
                ],
                'codigo_seguimiento' => [
                    'rules' => 'required',
                ],
            ]);
            if(!$validation){
                $data['validation'] = $this->validator;
            }else{
                $db = \Config\Database::connect();
                $campos = array(
                    "dw.estado", "TO_CHAR(hr.ultimo_fecha_derivacion, 'DD/MM/YYYY') as fecha",
                    "CASE WHEN hr.id > 0 THEN CONCAT(ua.nombre_completo,'<br><b>',pa.nombre,'<b>') ELSE '' END as usuario_actual",
                    "ua.atencion",
                );
                $where = array(
                    'dw.deleted_at' => NULL,
                    'dw.correlativo' => trim(mb_strtoupper($this->request->getPost('correlativo'))),
                    'dw.codigo_seguimiento' => trim($this->request->getPost('codigo_seguimiento')),
                );
                $builder = $db->table('mineria_ilegal.denuncias_web as dw')
                ->select($campos)
                ->join('mineria_ilegal.denuncias as d', 'dw.fk_denuncia = d.id', 'left')
                ->join('mineria_ilegal.hoja_ruta as hr', 'd.id = hr.fk_denuncia', 'left')
                ->join('usuarios as ua', 'hr.fk_usuario_actual = ua.id', 'left')
                ->join('perfiles as pa', 'ua.fk_perfil = pa.id', 'left')
                ->where($where);
                if($tramite = $builder->get()->getRowArray()){
                    $data['style'] = 'alert-success';
                    $data['titulo'] = 'SE ENCONTRO EL TRÁMITE';
                    $data['estado'] = $tramite['estado'];
                    $data['fecha'] = $tramite['fecha'];
                    $data['usuario_actual'] = $tramite['usuario_actual'];
                    $data['atencion'] = $tramite['atencion'];
                }else{
                    $data['style'] = 'alert-danger';
                    $data['titulo'] = 'NO SE ENCONTRO EL FORMULARIO DE DENUNCIA O EL CÓDIGO DE SEGUIMIENTO ES ERRÓNEO';
                    $data['contenido'] = 'Lamentablemente no se tuvo un resultado con los datos proporcionados, puede aproximarse a la Oficina Nacional, Dirección y/o Regional que le corresponda para averiguar.';
                }
                $data['response'] = true;
            }
        }

        $data['accion'] = 'seguimiento_mineria_ilegal';
        $data['enlaces'] = view($this->carpeta.'enlaces');
        return view($this->carpeta.'seguimiento_mineria_ilegal', $data);
    }

    public function denunciaMineriaIlegal(){
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
                'documento_identidad' => [
                    'rules' => 'required',
                ],
                'telefonos' => [
                    'rules' => 'required',
                ],
                'direccion' => [
                    'rules' => 'required',
                ],
                'documento_identidad_digital' => [
                    'uploaded[documento_identidad_digital]',
                    'mime_in[documento_identidad_digital,application/pdf]',
                    'max_size[documento_identidad_digital,5120]',
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
                'coordenadas' => [
                    'rules' => 'required',
                ],
                'adjunto_uno' => [
                    'uploaded[adjunto_uno]',
                    'max_size[adjunto_uno,5120]',
                ],
                'codigo_seguridad' => [
                    'rules' => 'required',
                ],
            ]);
            if(!$validation){
                $provincias = $this->obtenerProvincias($this->request->getPost('departamento'));
                $municipios = $this->obtenerMunicipios($this->request->getPost('departamento'), $this->request->getPost('provincia'));
                $data['validation'] = $this->validator;
            }else{
                $informacion = $this->informacionAgente();
                $rutaArchivoDenunciante = 'archivos/mineria_ilegal/denunciante/';
                $rutaArchivo = 'archivos/mineria_ilegal/web/';
                $correlativoFormularioDenuncia = $this->obtenerCorrelativo('AJAM/FDMI-WEB/');

                $denunciasWebMineriaIlegalModel = new DenunciasWebMineriaIlegalModel();
                $coordenadasWebMineriaIlegalModel = new CoordenadasWebMineriaIlegalModel();

                $docDigital = $this->request->getFile('documento_identidad_digital');
                $nombreDocDigital = $docDigital->getRandomName();
                $docDigital->move($rutaArchivoDenunciante,$nombreDocDigital);
                $nombreDocDigital = $rutaArchivoDenunciante.$nombreDocDigital;

                $pruebaUno = $this->request->getFile('adjunto_uno');
                $nombrePruebaUno = $pruebaUno->getRandomName();
                $pruebaUno->move($rutaArchivo,$nombrePruebaUno);
                $nombrePruebaUno = $rutaArchivo.$nombrePruebaUno;

                $pruebaDos = $this->request->getFile('adjunto_dos');
                $nombrePruebaDos= '';
                if($pruebaDos->getBasename()){
                    $nombrePruebaDos = $pruebaDos->getRandomName();
                    $pruebaDos->move($rutaArchivo,$nombrePruebaDos);
                    $nombrePruebaDos = $rutaArchivo.$nombrePruebaDos;
                }

                $pruebaTres = $this->request->getFile('adjunto_tres');
                $nombrePruebaTres = '';
                if($pruebaTres->getBasename()){
                    $nombrePruebaTres = $pruebaTres->getRandomName();
                    $pruebaTres->move($rutaArchivo,$nombrePruebaTres);
                    $nombrePruebaTres = $rutaArchivo.$nombrePruebaTres;
                }

                $data = array(
                    'correlativo' => $correlativoFormularioDenuncia,
                    'nombres' => mb_strtoupper(trim($this->request->getPost('nombres'))),
                    'apellidos' => mb_strtoupper(trim($this->request->getPost('apellidos'))),
                    'documento_identidad' => trim($this->request->getPost('documento_identidad')),
                    'expedido' => $this->request->getPost('expedido'),
                    'telefonos' => mb_strtoupper(trim($this->request->getPost('telefonos'))),
                    'direccion' => mb_strtoupper($this->request->getPost('direccion')),
                    'email' => $this->request->getPost('email'),
                    'documento_identidad_digital' => $nombreDocDigital,
                    'fk_municipio' => $this->request->getPost('fk_municipio'),
                    'comunidad_localidad' => mb_strtoupper(trim($this->request->getPost('comunidad_localidad'))),
                    'descripcion_lugar' => mb_strtoupper(trim($this->request->getPost('descripcion_lugar'))),
                    'autores' => mb_strtoupper(trim($this->request->getPost('autores'))),
                    'persona_juridica' => mb_strtoupper(trim($this->request->getPost('persona_juridica'))),
                    'descripcion_materiales' => mb_strtoupper(trim($this->request->getPost('descripcion_materiales'))),
                    'areas_denunciadas' => mb_strtoupper(trim($this->request->getPost('areas_denunciadas'))),
                    'fotografia_uno' => $nombrePruebaUno,
                    'fotografia_dos' => $nombrePruebaDos,
                    'fotografia_tres' => $nombrePruebaTres,
                    'codigo_seguimiento' => substr(str_shuffle("0123456789"),0,5),
                    'ip' => $this->request->getIPAddress(),
                    'navegador' => $informacion['navegador'],
                    'so' => $informacion['so'],
                    'estado' => 'PRESENTADO'
                );
                if($denunciasWebMineriaIlegalModel->insert($data) === false){
                    session()->setFlashdata('fail', $denunciasWebMineriaIlegalModel->errors());
                }else{
                    $idDenuncia = $denunciasWebMineriaIlegalModel->getInsertID();
                    $coordenadas = $this->obtenerCoordenadas($this->request->getPost('coordenadas'));
                    $dataCoordenada['fk_denuncia_web'] = $idDenuncia;
                    foreach($coordenadas as $coordenada){
                        $dataCoordenada['latitud'] = $coordenada['latitud'];
                        $dataCoordenada['longitud'] = $coordenada['longitud'];
                        if($coordenadasWebMineriaIlegalModel->insert($dataCoordenada) === false)
                            session()->setFlashdata('fail', $coordenadasWebMineriaIlegalModel->errors());
                    }

                    $this->actualizarCorrelativo('AJAM/FDMI-WEB/');
                    session()->setFlashdata('success', '<h4 class="alert-heading">Se ha Guardado Correctamente la Denuncia.</h4><p>Puede descargar el comprobante haciendo <a href="'.base_url('pdf_formulario_denuncia/'.$idDenuncia).'" target="_blank" class="btn btn-outline-primary">Click Aquí</a></p>');
                }
                return redirect()->to('denuncia_mineria_ilegal');
            }
        }

        $data['expedidos'] = $this->expedidos;
        $data['departamentos'] = (array(''=>'SELECCIONE EL DEPARTAMENTO') + $this->obtenerDepartamentos());
        $data['provincias'] = (array(''=>'SELECCIONE LA PROVINCIA') + $provincias);
        $data['municipios'] = (array(''=>'SELECCIONE EL MUNICIPIO') + $municipios);
        $data['enlaces'] = view($this->carpeta.'enlaces');
        return view($this->carpeta.'denuncia_mineria_ilegal', $data);
    }

    public function pdfFormularioDenuncia($id_denuncia){
        $denunciasWebMineriaIlegalModel = new DenunciasWebMineriaIlegalModel();
        $campos = array(
            'id','fk_municipio','correlativo','codigo_seguimiento',"to_char(created_at, 'DD/MM/YYYY HH24:MI') as fecha_denuncia",
            'nombres','apellidos','documento_identidad','expedido','telefonos','email','direccion','documento_identidad_digital','comunidad_localidad',
            'descripcion_lugar','autores','persona_juridica','descripcion_materiales','areas_denunciadas','fotografia_uno','fotografia_dos','fotografia_tres',
        );
        if($denuncia = $denunciasWebMineriaIlegalModel->select($campos)->find($id_denuncia)){
            $municipiosModel = new MunicipiosModel();
            $coordenadasWebMineriaIlegalModel = new CoordenadasWebMineriaIlegalModel();
            $contenido['denuncia'] = $denuncia;
            $contenido['coordenadas'] = $coordenadasWebMineriaIlegalModel->where(array('fk_denuncia_web'=>$denuncia['id']))->findAll();
            $contenido['ubicacion'] = $municipiosModel->find($denuncia['fk_municipio']);
            $contenido['tipo_denuncias'] = $this->tipoDenuncias;
            $contenido['color'] = '#F7CECE';
            $html = view($this->carpeta.'pdf_formulario_denuncia', $contenido);
            $html_adjuntos = view($this->carpeta.'pdf_adjuntos', $contenido);

            $file_name = str_replace('/','-',$denuncia['correlativo']).'.pdf';
            $pdf = new DenunciaPdf('P', 'mm', array(216, 279), true, 'UTF-8', false);

            $pdf->SetCreator('GARNET');
            $pdf->SetAuthor('Desarrollo de UTIC');
            $pdf->SetTitle('Formulario Denuncia Mineria Ilegal');
            $pdf->SetKeywords('Mineria, Ilegal');

            //establecer margenes
            $pdf->SetMargins(10, 42, 10);
            $pdf->SetAutoPageBreak(true, 35); //Margin botto

            $pdf->AddPage();
            // Titulo de paginas
            $pdf->SetFont($this->fontPDF, 'B', 14);
            $pdf->Cell(0,0,$denuncia['correlativo'],0,0,'C');
            $pdf->Ln();
            $pdf->SetFont($this->fontPDF, 'B', 11);
            $pdf->Cell(0,0,"CÓDIGO DE SEGUIMIENTO: ".$denuncia['codigo_seguimiento'],0,0,'C');
            $pdf->SetFont($this->fontPDF, '', 8);
            $pdf->Ln(8);
            $pdf->writeHTML($html, true, false, false, false, '');

            $pdf->AddPage();
            $pdf->SetFont($this->fontPDF, 'B', 14);
            $pdf->Cell(0,0,$denuncia['correlativo'],0,0,'C');
            $pdf->SetFont($this->fontPDF, '', 8);
            $pdf->Ln(8);
            $pdf->writeHTML($html_adjuntos, true, false, false, false, '');

            $pdf->Output($file_name);
            exit();
        }else{
            return redirect()->to('https://www.autoridadminera.gob.bo/');
        }
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
                    $resultado[strval($municipio['id'])] = $municipio['municipio'];
            }
        }
        return $resultado;
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

    private function informacionAgente(){
        $result = array();
        $agent = $this->request->getUserAgent();

        if ($agent->isBrowser()) {
            $currentAgent = $agent->getBrowser() . ' ' . $agent->getVersion();
        } elseif ($agent->isRobot()) {
            $currentAgent = $agent->getRobot();
        } elseif ($agent->isMobile()) {
            $currentAgent = $agent->getMobile();
        } else {
            $currentAgent = 'Unidentified User Agent';
        }
        $result['navegador'] = $currentAgent;
        $result['so'] = $agent->getPlatform();

        return $result;
    }

}
