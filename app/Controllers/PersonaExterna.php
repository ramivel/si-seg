<?php

namespace App\Controllers;

use App\Models\PersonaExternaModel;

class PersonaExterna extends BaseController
{
    protected $titulo = 'Personas Externas';
    protected $controlador = 'persona_externa/';
    protected $carpeta = 'persona_externa/';
    protected $menuActual = 'persona_externa';

    /*public function index()
    {
        $oficinasModel = new OficinasModel();
        $datos = $oficinasModel->orderBy('nombre', 'asc')->findAll();
        $campos_listar=array('Nombre','Departamento','Correlativo','Dirección','Teléfonos','Estado');
        $campos_reales=array('nombre','departamento','correlativo','direccion','telefonos','activo');
        $cabera['titulo'] = $this->titulo;
        $cabera['navegador'] = true;
        $cabera['subtitulo'] = 'Listado';
        $contenido['title'] = view('templates/title',$cabera);
        $contenido['datos'] = $datos;
        $contenido['campos_listar'] = $campos_listar;
        $contenido['campos_reales'] = $campos_reales;
        $contenido['subtitulo'] = 'Listado';
        $contenido['controlador'] = $this->controlador;
        $data['content'] = view($this->carpeta.'index', $contenido);
        $data['menu_actual'] = $this->menuActual;
        $data['tramites_menu'] = $this->tramitesMenu();
        echo view('templates/template', $data);
    }*/

    public function agregarAjax(){        

        $personaExternaModel = new PersonaExternaModel();
        $data = array(
            'nombres' => mb_strtoupper(trim($this->request->getPost('nombres'))),
            'apellidos' => mb_strtoupper(trim($this->request->getPost('apellidos'))),
            'documento_identidad' => mb_strtoupper(trim($this->request->getPost('documento_identidad'))),
            'expedido' => $this->request->getPost('expedido'),
            'telefonos' => mb_strtoupper(trim($this->request->getPost('telefonos'))),
            'email' => trim($this->request->getPost('email')),
            'direccion' => mb_strtoupper(trim($this->request->getPost('direccion'))),
            'institucion' => mb_strtoupper(trim($this->request->getPost('institucion'))),
            'cargo' => mb_strtoupper(trim($this->request->getPost('cargo'))),
            'fk_usuario_creador' => session()->get('registroUser'),
        );
        if($personaExternaModel->save($data) === false)
            session()->setFlashdata('fail', $personaExternaModel->errors());
        $idPersonaExterna = $personaExternaModel->getInsertID();
        if($persona = $personaExternaModel->find($idPersonaExterna)){
            $resultado = array(
                'id' => $idPersonaExterna,
                'text' => $persona['documento_identidad'].' '.$persona['expedido'].' - '.$persona['nombres'].' '.$persona['apellidos'].' ('.$persona['institucion'].' - '.$persona['cargo'].')',
            );
        }
        
        echo json_encode($resultado);

    }

    public function buscarPersonaExternaAjax(){
        $cadena = mb_strtoupper(trim($this->request->getPost('texto')));
        if(!empty($cadena) && session()->get('registroUser')){
            $data = array();
            $personaExternaModel = new PersonaExternaModel();
            $campos = array('id', "CONCAT(documento_identidad, ' ', expedido, ' - ', nombres, ' ', apellidos, ' (', institucion, ' - ',cargo,')') as nombre");
            $datos = $personaExternaModel->select($campos)
            ->like("CONCAT(documento_identidad, ' ', expedido, ' - ', nombres, ' ', apellidos, ' (', institucion, ' - ',cargo,')')", $cadena)
            ->limit(10)
            ->findAll();
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
                    'text' => 'No se encontró a ningún resultado.'
                );
            }
            echo json_encode($data);
        }
    }

}