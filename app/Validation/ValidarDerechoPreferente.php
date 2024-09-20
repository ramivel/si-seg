<?php

namespace App\Validation;

class ValidarDerechoPreferente {

    public function mismo_titular($value):bool
    {
        $request = \Config\Services::request();
        $id_areas_mineras = $request->getPost('id_areas_mineras');
        if(count($id_areas_mineras)>1){
            $titular = '';
            $clasificacion = '';
            foreach($id_areas_mineras as $i=>$id_area_minera){
                $area_minera = $this->informacionAreaMineraSiReg($id_area_minera);
                if($i==0){
                    $titular = trim($area_minera['titular']);
                    $clasificacion = trim($area_minera['clasificacion']);
                }else{
                    if($titular != trim($area_minera['titular']) || $clasificacion != trim($area_minera['clasificacion']))
                        return false;
                }
            }
        }
        return true;
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

}