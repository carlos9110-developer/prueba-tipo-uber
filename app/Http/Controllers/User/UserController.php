<?php

namespace App\Http\Controllers\User;

use App\BL\User\UserBL;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class UserController extends ApiController
{
    public $request;
    private $userBL;


    public function __construct(Request $request, UserBL $userBL)
    {
        $this->request  =    $request;
        $this->userBL   =    $userBL;
    }


    public function store()
    {
        return $this->userBL->save($this->request->all());
    }

    public function saveLocationDriver()
    {
        return $this->userBL->save($this->request->all());
    }

    public function login()
    {
        return $this->userBL->signup($this->request->email, $this->request->password);
    }

    public function logout()
    {
        return $this->userBL->logout($this->request->userId);
    }

    public function saveUserMobil()
    {
        return $this->userBL->saveUserMobil($this->request->all());
    }

}
