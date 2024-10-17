<?php

namespace App\Controllers;

use App\Models\ActoAdministrativoModel;
use App\Models\DerivacionMineriaIlegalModel;
use App\Models\HojaRutaMineriaIlegalModel;
use App\Models\TramitesModel;
use App\Models\UsuariosModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['form', 'url'];

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();
    }

    public function tramitesMenu($id='')
    {
        $resultado = array();
        $tramitesModel = new TramitesModel();
        $usuariosModel = new UsuariosModel();
        $usuarioInfo = $usuariosModel->find(session()->get('registroUser'));
        if($id){
            $resultado = $tramitesModel->find($id);
        }else{
            if($usuarioInfo['tramites'])
                $resultado = $tramitesModel->whereIn('id',explode(",", $usuarioInfo['tramites']))->findAll();
                //$resultado = $tramitesModel->whereIn('id',explode(",", $usuarioInfo['tramites']))->where('activo',true)->findAll();
        }
        return $resultado;
    }

    public function alertasTramites()
    {
        $resultado = array();
        $tramitesModel = new TramitesModel();
        $usuariosModel = new UsuariosModel();
        $actoAdministrativoModel = new ActoAdministrativoModel();
        $derivacionMineriaIlegalModel = new DerivacionMineriaIlegalModel();
        $usuarioInfo = $usuariosModel->find(session()->get('registroUser'));
        if($usuarioInfo['tramites']){
            //$tramites = $tramitesModel->whereIn('id',explode(",", $usuarioInfo['tramites']))->where('activo',true)->findAll();
            $tramites = $tramitesModel->whereIn('id',explode(",", $usuarioInfo['tramites']))->findAll();
            foreach($tramites as $tramite){
                switch($tramite['id']){
                    case 1:
                        /* Trámites Pendientes */
                        $where = array(
                            'deleted_at' => NULL,
                            'ultimo_fk_usuario_destinatario' => session()->get('registroUser'),
                        );
                        $total = $actoAdministrativoModel->select('COUNT(*) AS n')->where($where)->whereIn('ultimo_estado', array('MIGRADO', 'DERIVADO'))->first();
                        if($total['n'] > 0)
                            $resultado[] = array(
                                'title' => '<strong>Tiene '.$total['n'].' trámite(s) CAMs pendientes de recepción!</strong>',
                                'text' => '<a href="'.base_url($tramite['controlador'].'listado_recepcion').'" class="btn btn-danger">Recibir Trámites</a>',
                            );
                        /* Fin Trámites Pendientes */
                        /* Correspondencia Externa Pendiente */
                        $db = \Config\Database::connect();
                        $where = array(
                            'ce.fk_tramite' => 1,
                            'ce.estado' => 'INGRESADO',
                            'ce.deleted_at' => NULL,
                            'ac.fk_usuario_actual' => session()->get('registroUser'),
                            'ac.deleted_at' => NULL,
                        );
                        $builder = $db->table('public.correspondencia_externa AS ce')->select('count(*) as n')
                        ->join('public.acto_administrativo AS ac', "ce.fk_acto_administrativo = ac.id", 'left')
                        ->where($where);
                        $totalCorrespondencia = $builder->get()->getRowArray();
                        if($totalCorrespondencia['n'] > 0)
                            $resultado[] = array(
                                'title' => '<strong>Tiene correspondencia externa ('.$totalCorrespondencia['n'].') de CAMs pendientes de recepción!</strong>',
                                'text' => '<a href="'.base_url($tramite['controlador'].'mis_tramites').'" class="btn btn-danger">Recibir Trámites</a>',
                            );
                        /* Fin Correspondencia Externa Pendiente */
                        break;
                    case 2:
                        /* Trámites Pendientes */
                        $where = array(
                            "deleted_at" => null,
                            "fecha_recepcion" => null,
                            "fk_usuario_destinatario" => session()->get('registroUser'),
                            'estado' => 'DERIVADO',
                        );
                        $total = $derivacionMineriaIlegalModel->select('COUNT(*) AS n')->where($where)->first();
                        if($total['n'] > 0)
                            $resultado[] = array(
                                'title' => '<strong>Tiene '.$total['n'].' Hoja(s) de Ruta de Minería Ilegal pendientes de recepción!</strong>',
                                'text' => '<a href="'.base_url($tramite['controlador'].'listado_recepcion').'" class="btn btn-danger">Recibir Trámites</a>',
                            );
                        /* Fin Trámites Pendientes */
                        /* Correspondencia Externa Pendiente */
                        $db = \Config\Database::connect();
                        $where = array(
                            'ce.fk_tramite' => 2,
                            'ce.estado' => 'INGRESADO',
                            'ce.deleted_at' => NULL,
                            'hr.fk_usuario_actual' => session()->get('registroUser'),
                            'hr.deleted_at' => NULL,
                        );
                        $builder = $db->table('public.correspondencia_externa AS ce')->select('count(*) as n')
                        ->join('mineria_ilegal.hoja_ruta AS hr', "ce.fk_hoja_ruta = hr.id", 'left')
                        ->where($where);
                        $totalCorrespondencia = $builder->get()->getRowArray();
                        if($totalCorrespondencia['n'] > 0)
                            $resultado[] = array(
                                'title' => '<strong>Tiene correspondencia externa ('.$totalCorrespondencia['n'].') de Minería Ilegal pendientes de recepción!</strong>',
                                'text' => '<a href="'.base_url($tramite['controlador'].'mis_tramites').'" class="btn btn-danger">Recibir Trámites</a>',
                            );
                        /* Fin Correspondencia Externa Pendiente */
                        break;
                }
            }
        }
        return $resultado;
    }

}
