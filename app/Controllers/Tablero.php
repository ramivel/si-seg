<?php

namespace App\Controllers;
use App\Models\UsuarioModel;

class Tablero extends BaseController
{
    public function index()
    {        
        //return view('welcome_message');
        //$this->helloWord('hola munod', 12);
        $data['variable1'] = 'Prueba 1';
        $data['variable2'] = 'Prueba 2';
        return view('index', $data);

        
        $this->loadViews('index');
    }

    public function loadViews($view = null){
        echo view('includes/header');
        echo view($view);
        echo view('includes/footer');
    }

    public function hello($lug = null, $id = null){
        echo "hello world slug=".$lug." id = ".$id;
    }

    protected function helloWord($slug = null, $id = null){
        echo "hello world slug=".$slug." id = ".$id;
    }

    public function template(){
        $parser = \Config\Services::parser();
        $data = [
            'titulo' => 'Titulo de la Pagina',
            'contenido' => 'prueba pruebaaaa',
            'footer' => 'Hasta Luego!!'
        ];

        echo $parser->setData($data)->render('template');
    }

    public function index1(){        
        $model = new UsuarioModel();        
        $id = $model->insert([
            'nombres' => 'Ramiro Velasco',
            'usuario' => 'ramiro.velasco',
            'contrasena' => '123456',
        ]);
        echo $id;
    }
}
