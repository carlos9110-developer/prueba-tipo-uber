<?php

namespace App\BL\Service;

use App\Models\Mobile;
use App\Models\Service;

use App\Traits\Utils;
use Exception;
use Illuminate\Support\Facades\DB;

class ServiceBL
{
    use Utils;

    public function save(array $datos)
    {
        try {
            $datos["origen"] = $this->getPointByCoordinates($datos["origen"]);
            $datos["destino"] = $this->getPointByCoordinates($datos["destino"]);
            $datos["estado_servicio_id"] = Service::ESTADO_SOLICITADO;
            $service = Service::create($datos);
            return $this->responseApi(201, $service, 'Servicio Registrado');
        } catch (Exception $ex) {
            return $this->responseApi(500, [], "Se presento un error en el servidor, intentelo nuevamente");
        }
    }

    public function availableServicesByDriver(int $userId)
    {
        try {
            $availableServices = Service::availableServicesByDriver($userId);
            return $this->responseApi(200, $availableServices, 'Servicios Disponibles');
        } catch (Exception $ex) {
            return $this->responseApi(500, [], "Se presento un error en el servidor, al obtener los servicios");
        }
    }

    public function assignDriverToService(array $datos)
    {
        try {

            if (Mobile::validateMobileAvailability($datos['user_id']) ) {

                $result = DB::transaction(function () use(&$datos) {
                    $update = Service::moveServiceToAssignedStatus($datos['servicio_id']);
                    if ($update == 1) {
                        Service::assignDriverToService($datos);
                        Mobile::changeMobileStatus($datos['user_id'], Mobile::ESTADO_OCUPADO);
                        return true;
                    }
                    return false;
                });
    
                if ($result) {
                    return $this->responseApi(200, [], "Servicio Tomado Con Exito");
                } else {
                    return $this->responseApi(500, [], "Error, el servicio ya fue tomado, prueba con otro");
                }
            } else {
                return $this->responseApi(403, [], "Error, en estos momentos no esta disponible para tomar servicios");
            }


        } catch (Exception $ex) {
            return $this->responseApi(500, [], "Se presento un error en el servidor, al tomar el servicio");
        }
    }
    
    public function getLocationDriverService(int $serviceId)
    {
        try {
            $userId = Service::getDriverIdByServiceId($serviceId);
            $location = Mobile::where('user_id', $userId)->first()->ubicacion;
            $response = ['longitud' => $location->longitude, 'latitud' => $location->latitude ];
            return $this->responseApi(200, $response, 'Coordenadas conductor servicio');
        } catch (Exception $ex) {
           return $this->responseApi(500, [], "Error al obtener las coordenadas del conductor");
        }
    }
    
    public function cancelService(int $serviceId)
    {
        try {
            $result = DB::transaction(function () use(&$serviceId) {
                $update = Service::moveServiceToCancelStatus($serviceId);
                if ($update == 1) {
                    $userId = Service::getDriverIdByServiceId($serviceId);
                    Mobile::changeMobileStatus($userId, Mobile::ESTADO_ACTIVO);
                    return true;
                }
                return false;
            });
            if ($result) {
                return $this->responseApi(200, [], "Servicio Cancelado Con Exito");
            } else {
                return $this->responseApi(500, [], "Error, al cancelar el servicio");
            }
        } catch (Exception $ex) {
           return $this->responseApi(500, [], "Error, intentelo de nuevo");
        }
    }
    
    public function endService(int $serviceId)
    {
        try {
            $result = DB::transaction(function () use(&$serviceId) {
                $update = Service::moveServiceToFinishedStatus($serviceId);
                if ($update == 1) {
                    $userId = Service::getDriverIdByServiceId($serviceId);
                    Mobile::changeMobileStatus($userId, Mobile::ESTADO_ACTIVO);
                    return true;
                }
                return false;
            });
            if ($result) {
                return $this->responseApi(200, [], "Servicio Finalizado Con Exito");
            } else {
                return $this->responseApi(500, [], "Error, al finalizar el servicio");
            }
        } catch (Exception $ex) {
           return $this->responseApi(500, [], "Error, intentelo de nuevo");
        }
    }

    


}

    