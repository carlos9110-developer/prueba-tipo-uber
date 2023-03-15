<?php

namespace App\Http\Controllers\Service;

use App\BL\Service\ServiceBL;
use App\Http\Controllers\ApiController;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends ApiController
{
    public $request;
    private $serviceBL;


    public function __construct(Request $request, ServiceBL $serviceBL)
    {
        $this->request   =    $request;
        $this->serviceBL =    $serviceBL;
    }

    public function save()
    {
        return $this->serviceBL->save($this->request->all());
    }

    public function availableServicesByDriver()
    {
        return $this->serviceBL->availableServicesByDriver($this->request->user_id);
    }

    public function assignDriverToService()
    {
        return $this->serviceBL->assignDriverToService($this->request->all());
    }

    public function getLocationDriverService()
    {
        return $this->serviceBL->getLocationDriverService($this->request->servicio_id);
    }

    public function cancelService()
    {
        return $this->serviceBL->cancelService($this->request->servicio_id);
    }

    public function endService()
    {
        return $this->serviceBL->endService($this->request->servicio_id);
    }
   
}
