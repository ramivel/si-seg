<?php

namespace App\Controllers;
use App\Models\UsuariosModel;

class Dashboard extends BaseController
{
    protected $controlador = 'dashboard/';
    protected $carpeta = 'dashboard/';

    public function index()
    {
        $estados = array('TRÁMITES PARA RECEPCIÓN','MIS TRÁMITES');
        $urlEstadosBandeja = array();
        $resumenEstadosBandeja = array();

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
            $data['graficar'] = 1;
            $data['resumen'] = $resumenEstadosTramite;
        }
        $urlEstadosBandeja['MIS TRÁMITES'] = 'cam/mis_tramites';
        $resumenEstadosBandeja['MIS TRÁMITES'] = $totalMisTramites;

        $where = array(
            'deleted_at' => NULL,
            'ultimo_fk_usuario_destinatario' => session()->get('registroUser'),
        );
        $builder = $db->table('public.acto_administrativo')->where($where)->whereIn('ultimo_estado', array('MIGRADO', 'DERIVADO'));
        $totalRecepcion = count($builder->get()->getResult('array'));
        $urlEstadosBandeja['TRÁMITES PARA RECEPCIÓN'] = 'cam/listado_recepcion';
        $resumenEstadosBandeja['TRÁMITES PARA RECEPCIÓN'] = $totalRecepcion;        

        $cabera['titulo'] = 'Bienvenid@ al Sistema';
        $cabera['sub_titulo'] = 'Resumen de los Tramites';
        $cabera['navegador'] = false;
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['total'] = $totalMisTramites;
        $contenido['estados_bandeja'] = $estados;
        $contenido['url_estados_bandeja'] = $urlEstadosBandeja;
        $contenido['resumen_estados_bandeja'] = $resumenEstadosBandeja;
        $contenido['total_estados_bandeja'] = ($totalMisTramites+$totalRecepcion);
        $data['content'] = view($this->carpeta.'index', $contenido);
        $data['tramites_menu'] = $this->tramitesMenu();
        $data['alertas'] = $this->alertasTramites();
        echo view('templates/template', $data);
    }
}
