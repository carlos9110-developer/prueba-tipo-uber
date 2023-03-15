<?php

namespace App\Http\Controllers\Mobile;

use App\BL\Mobile\MobileBL;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class MobileController extends ApiController
{
    public $request;
    private $mobileBL;


    public function __construct(Request $request, MobileBL $mobileBL)
    {
        $this->request   =    $request;
        $this->mobileBL =    $mobileBL;
    }

    public function saveLocation()
    {
        return $this->mobileBL->saveLocation($this->request->all());
    }

}
