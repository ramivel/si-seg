<?php

namespace App\Controllers;
use App\Models\UsuariosModel;
use App\Libraries\Hash;

class Autenticacion extends BaseController
{
    public function index()
    {
        //var_dump(Hash::make('123456')); exit();
        /*$usuariosModel = new UsuariosModel();
        $data = array(
            'usuario' => 'admin',
            'pass' => Hash::make('123456'),
            'fk_persona' => 1
        );
        if($usuariosModel->save($data) === false){
            var_dump($usuariosModel->errors());
        }*/
        return view('autenticacion/index');
    }
    public function login(){
        $validation = $this->validate([
            'usuario' => [
                'rules' => 'required',
                /*'errors' => [
                    'required' => 'Debe introducir el Usuario'
                ]*/
            ],
            'pass' => [
                'rules' => 'required|min_length[5]|max_length[12]',
                /*'errors' => [
                    'required' => 'Debe introducir la Contraseña',
                    'min_length' => 'La contraseña debe tener al menos 5 caracteres',
                    'max_length' => 'La contraseña debe tener maximo 12 caracteres',
                ]*/
            ]
        ]);
        if(!$validation){
            return view('autenticacion/index', ['validation'=>$this->validator]);
        }else{
            $usuario = trim($this->request->getPost('usuario'));
            $pass = $this->request->getPost('pass');
            $usuariosModel = new UsuariosModel();
            if($usuarioInfo = $usuariosModel->where('usuario', $usuario)->first()){
                if(!Hash::check($pass, $usuarioInfo['pass'])){
                    session()->setFlashdata('fail', 'La Contraseña es Incorrecta.');
                    return redirect()->to('autenticacion')->withInput();
                }else{
                    session()->set('registroUser', $usuarioInfo['id']);
                    session()->set('registroPerfil', $usuarioInfo['fk_perfil']);
                    session()->set('registroUserName', $usuarioInfo['nombre_completo']);
                    session()->set('registroModulos', $usuarioInfo['tramites']);
                    session()->set('registroOficina', $usuarioInfo['fk_oficina']);
                    session()->set('registroPermisos', $usuarioInfo['permisos'] ? explode(',',$usuarioInfo['permisos']) : array());
                    return redirect()->to('dashboard');
                }
            }else{
                session()->setFlashdata('fail', 'El usuario no existe.');
                return redirect()->to('autenticacion')->withInput();
            }
        }
    }

    public function logout(){        
        if(session()->has('registroUser')){
            session()->remove('registroUser');
        }
        return redirect()->to('autenticacion')->with('fail', 'Se salio del sistema, Vuelva Pronto!!');
    }

    /*public function login(){
        $request = \Config\Services::request();
        $data = array(
            'name' => $request->getPostGet('name');
        );



        $usuariosModel = new UsuariosModel();
        $usuario = $usuariosModel->find([1,2]);
        $usuarios = $usuariosModel->findAll();
        $usuarios = $usuariosModel->where('nombre', 'maria')
        ->orderBy('id', 'ASC')
        ->findAll();
        $usuarios = $usuariosModel->asObject()->asArray()->where('nombre', 'maria')
        ->orderBy('id', 'ASC')
        ->findAll();
        $usuarios = $usuariosModel->findAll(2,3); //Limit
        $usuarios = $usuariosModel->withDelete()->findAll();
        $usuarios = $usuariosModel->onlyDelete()->findAll();

        $data = [
            'name' => 'programador1',
            'email' => 'programador1@hotmail.com'
        ];

        $id = $usuariosModel->insert($data);

        $data = [
            'name' => 'programador2'
        ];

        $usuariosModel->update(10,$data);
        $usuariosModel->whereIn('id', [4,5,10])->set(['name'=>'yo tambien'])->update();

        $usuariosModel->save($data); //actualiza y guarda

        if($usuariosModel->save($data) === false){
            $usuariosModel->errors();
        }

        $usuariosModel->delete([2,4]);
        $usuariosModel->where('id',10)->delete();
        $usuariosModel->purgeDeleted(); //borra todos los borrados



        // Validacion
        $validation = $this->validate([
            'usuario' => 'required',
            'pass' => 'required'
        ]);

        if(!$validation){
            return view('autenticacion/index', ['validation'=>$this->validator]);
        }else{
            echo "comprobado con exito";
        }
    }*/

    public function loadViews($view = null){
        echo view('includes/header');
        echo view($view);
        echo view('includes/footer');
    }
}
