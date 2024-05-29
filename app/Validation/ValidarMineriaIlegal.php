<?php

namespace App\Validation;

use App\Models\DenunciasMineriaIlegalModel;
use App\Models\HojaRutaMineriaIlegalModel;
use App\Models\MunicipiosModel;
use App\Models\OficinasModel;

class ValidarMineriaIlegal {

    public function existe_correlativo_hr($value):bool
    {
        $request = \Config\Services::request();
        $oficinaModel = new OficinasModel();
        $hojaRutaMineriaIlegalModel = new HojaRutaMineriaIlegalModel();
        $oficina = $oficinaModel->find($request->getPost('fk_oficina'));
        $correlativoHR = $oficina['correlativo'].'MIN-ILEGAL/'.$value.'/2024';

        if($hojaRutaMineriaIlegalModel->where(array('correlativo' => $correlativoHR))->first())
            return false;

        return true;
    }

    public function existe_correlativo_fmi($value):bool
    {
        $request = \Config\Services::request();
        $municipiosModel = new MunicipiosModel();
        $oficinaModel = new OficinasModel();
        $denunciasMineriaIlegalModel = new DenunciasMineriaIlegalModel();
        $ubicacion = $municipiosModel->find($request->getPost('fk_municipio'));
        $oficina = $oficinaModel->where(array('departamento' => $ubicacion['regional'], 'desconcentrado' => 'true'))->first();
        $correlativoDenuncia = $oficina['correlativo'].'FMI/'.$value.'/2024';

        if($denunciasMineriaIlegalModel->where(array('correlativo' => $correlativoDenuncia))->first())
            return false;

        return true;

    }

}