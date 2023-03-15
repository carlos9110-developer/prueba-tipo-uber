<?php

namespace Database\Seeders;

use App\Models\Mobile;
use App\Models\User;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MatanYadaev\EloquentSpatial\Objects\Point;

class DatabaseSeeder extends Seeder
{
    public $idUser;
    public $idDriver;
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->insertUser();
        $this->insertStatesServices();
        $this->insertServices();
        $this->insertMobile();

    }

    public function insertUser()
    {
        $user = [
            "nombre"  => 'Carlos',
            "tipo"  => User::TIPO_USUARIO,
            "celular" => '3207236603',
            "email" => "carlos_hincapie_1",
            "password" => hash("SHA256","12345"),
        ];

        $driver = [
            "nombre"  => 'Eduardo',
            "tipo"  => User::TIPO_CONDUCTOR,
            "celular" => '3106218044',
            "email" => "carlos_hincapie_2",
            "password" =>   hash("SHA256","12345"),
        ];
        $this->idUser =  DB::table("users")->insertGetId($user);
        $this->idDriver =  DB::table("users")->insertGetId($driver);
    }

    public function insertStatesServices()
    {
        $states = [
            ["estado" => "Solicitado"],
            ["estado" => "Asignado"],
            ["estado" => "Terminado"],
            ["estado" => "Cancelado"]
        ];
        $this->idUser =  DB::table("estados_servicios")->insert($states);
    }

    public function insertServices()
    {
        try {
            $service = [
                "user_id"  => $this->idUser,
                "precio" => 500000,
                "origen" => DB::raw('POINT(-0.14359, 51.50111)'),
                "destino" =>DB::raw('POINT(1,1)'),
            ];
            DB::table("servicios")->insert($service);
        } catch (Exception $ex){
            Log::error("Error ". $ex->getMessage());
        }
    }

    public function insertMobile()
    {
        $mobile = [
            "user_id"  => $this->idDriver,
            "placa"  => "SUX 56",
            "ubicacion" => DB::raw('POINT(-0.14359, 51.50111)'),
            "marca" => "KIA",
            "estado" =>  Mobile::ESTADO_ACTIVO,
            "color" =>  "ROJO",
        ];

        DB::table("moviles")->insert($mobile);
    }
}
