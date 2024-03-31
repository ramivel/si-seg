<?php

namespace App\Controllers;

use App\Models\TramitesModel;
use App\Models\UsuariosModel;

class Dashboard extends BaseController
{
    protected $controlador = 'dashboard/';
    protected $carpeta = 'dashboard/';

    public function index()
    {
        $usuariosModel = new UsuariosModel();
        $tramitesModel = new TramitesModel();
        $usuarioInfo = $usuariosModel->find(session()->get('registroUser'));
        $resultados = array();
        $graficas = array();
        if($usuarioInfo['tramites']){
            $tramites = $tramitesModel->whereIn('id',explode(",", $usuarioInfo['tramites']))->findAll();
            foreach($tramites as $tramite){
                $resultados[$tramite['id']]['titulo'] = $tramite['nombre'];
                $resultados[$tramite['id']]['controlador'] = $tramite['controlador'];
                switch($tramite['id']){
                    case 1:

                        $db = \Config\Database::connect();
                        $campos = array("CONCAT(etp.orden,'. ',etp.nombre) as estado_tramite", "COUNT(ac.id) as n");
                        $where = array(
                            'ac.deleted_at' => NULL,
                            'ac.fk_usuario_actual' => session()->get('registroUser'),
                        );
                        $builder = $db->table('public.acto_administrativo as ac')
                        ->select($campos)
                        ->join('estado_tramite as etp', 'ac.ultimo_fk_estado_tramite_padre = etp.id', 'left')
                        ->whereIn('ultimo_estado', array('RECIBIDO', 'EN ESPERA', 'DERIVADO', 'MIGRADO', 'DEVUELTO'))
                        ->where($where)
                        ->groupBY('estado_tramite')
                        ->orderBY('estado_tramite', 'ASC');
                        $resumenEstadosTramite = $builder->get()->getResultArray();

                        $totalMisTramites = 0;
                        if(count($resumenEstadosTramite) > 0){
                            foreach($resumenEstadosTramite as $row)
                                $totalMisTramites += $row['n'];
                            $graficas[$tramite['id']]['resumen'] = $resumenEstadosTramite;
                            $graficas[$tramite['id']]['id_mapa'] = 'dashcam';
                            $resultados[$tramite['id']]['id_mapa'] = 'dashcam';
                        }
                        $resultados[$tramite['id']]['total_mis_tramites'] = $totalMisTramites;
                        $resultados[$tramite['id']]['nombre_total_mis_tramites'] = 'MI(S) TRÁMITE(S)';
                        $resultados[$tramite['id']]['url_total_mis_tramites'] = $tramite['controlador'].'mis_tramites';

                        $where = array(
                            'deleted_at' => NULL,
                            'ultimo_fk_usuario_destinatario' => session()->get('registroUser'),
                        );
                        $builder = $db->table('public.acto_administrativo')->where($where)->whereIn('ultimo_estado', array('MIGRADO', 'DERIVADO'));
                        $totalRecepcion = count($builder->get()->getResult('array'));
                        $resultados[$tramite['id']]['total_recepcion'] = $totalRecepcion;
                        $resultados[$tramite['id']]['nombre_total_recepcion'] = 'MI(S) TRÁMITE(S) PARA RECEPCIÓN';
                        $resultados[$tramite['id']]['url_total_recepcion'] = $tramite['controlador'].'listado_recepcion';

                        $resultados[$tramite['id']]['total_estados_bandeja'] = $totalMisTramites+$totalRecepcion;
                        $resultados[$tramite['id']]['nombre_total_estados_bandeja'] = 'TOTAL TRÁMITE(S)';
                        $resultados[$tramite['id']]['url_total_estados_bandeja'] = $tramite['controlador'].'mis_tramites';

                        break;
                    case 2:
                        $db = \Config\Database::connect();
                        $campos = array("CONCAT(etp.orden,'. ',etp.nombre) as estado_tramite", "COUNT(hr.id) as n");
                        $where = array(
                            'hr.deleted_at' => NULL,
                            'hr.fk_usuario_actual' => session()->get('registroUser'),
                        );
                        $builder = $db->table('mineria_ilegal.hoja_ruta as hr')
                        ->select($campos)
                        ->join('public.estado_tramite as etp', 'hr.ultimo_fk_estado_tramite_padre = etp.id', 'left')
                        ->whereIn('ultimo_estado', array('RECIBIDO', 'EN ESPERA', 'DERIVADO', 'MIGRADO', 'DEVUELTO'))
                        ->where($where)
                        ->groupBY('estado_tramite')
                        ->orderBY('estado_tramite', 'ASC');
                        $resumenEstadosTramite = $builder->get()->getResultArray();

                        $totalMisTramites = 0;
                        if(count($resumenEstadosTramite) > 0){
                            foreach($resumenEstadosTramite as $row)
                                $totalMisTramites += $row['n'];
                            $graficas[$tramite['id']]['resumen'] = $resumenEstadosTramite;
                            $graficas[$tramite['id']]['id_mapa'] = 'dashmin';
                            $resultados[$tramite['id']]['id_mapa'] = 'dashmin';
                        }
                        $resultados[$tramite['id']]['total_mis_tramites'] = $totalMisTramites;
                        $resultados[$tramite['id']]['nombre_total_mis_tramites'] = 'MIS HOJA(S) DE RUTA';
                        $resultados[$tramite['id']]['url_total_mis_tramites'] = $tramite['controlador'].'mis_tramites';

                        $where = array(
                            'deleted_at' => NULL,
                            'ultimo_fk_usuario_destinatario' => session()->get('registroUser'),
                        );
                        $builder = $db->table('mineria_ilegal.hoja_ruta')->where($where)->whereIn('ultimo_estado', array('MIGRADO', 'DERIVADO'));
                        $totalRecepcion = count($builder->get()->getResult('array'));
                        $resultados[$tramite['id']]['total_recepcion'] = $totalRecepcion;
                        $resultados[$tramite['id']]['nombre_total_recepcion'] = 'HOJA(S) DE RUTA PARA RECEPCIÓN';
                        $resultados[$tramite['id']]['url_total_recepcion'] = $tramite['controlador'].'listado_recepcion';

                        $resultados[$tramite['id']]['total_estados_bandeja'] = $totalMisTramites+$totalRecepcion;
                        $resultados[$tramite['id']]['nombre_total_estados_bandeja'] = 'TOTAL HOJAS(S) DE RUTA';
                        $resultados[$tramite['id']]['url_total_estados_bandeja'] = $tramite['controlador'].'mis_tramites';

                        break;
                }
            }
        }

        $cabera['titulo'] = 'Bienvenid@ al Sistema';
        $cabera['sub_titulo'] = 'Resumen de los Tramites';
        $cabera['navegador'] = false;
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['resultados'] = $resultados;
        $data['content'] = view($this->carpeta.'index', $contenido);
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['alertas'] = $this->alertasTramites();
        $data['graficas'] = $graficas;
        echo view('templates/template', $data);
    }

    public function VideoTutorial()
    {
        $cabera['titulo'] = 'Video Tutoriales';
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Video Tutoriales';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['subtitulo'] = 'Video Tutoriales';        
        $data['content'] = view($this->carpeta.'video_tutorial', $contenido);
        $data['menu_actual'] = 'video_tutorial';
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['alertas'] = $this->alertasTramites();        
        echo view('templates/template', $data);
    }

}
