<?php

namespace App\BL\Mobile;

use App\Models\Mobile;
use App\Traits\Utils;
use Exception;
use Illuminate\Support\Facades\DB;

class MobileBL
{
    use Utils;

    public function saveLocation(array $datos)
    {
        try {
            $result = DB::transaction(function () use(&$datos) {
                $datos['ubicacion'] = $this->getPointByCoordinates($datos['ubicacion']);
                Mobile::saveLocation($datos);
                Mobile::activeUser($datos);
                return true;
            });

            if($result){
                return $this->responseApi(201, [], 'Ubicación Ok');
            }else{
                return $this->responseApi(400, [], 'Error, al registrar la ubicación');
            }
            
        } catch (Exception $ex) {
            return $this->responseApi(500, [], "Se presento un error en el servidor, intentelo nuevamente");
        }
    }

    public function recordMobileInactivity()
    {
        Mobile::whereRaw('TIMESTAMPDIFF(MINUTE,fecha_reporte_coordenadas,NOW()) >= 1')
                   ->where('estado', '<>', Mobile::ESTADO_INACTIVO)
                   ->update(['estado' => Mobile::ESTADO_INACTIVO]);
    }

}

    