<?php

namespace App\BL\User;

use App\Models\Mobile;
use App\Models\User;
use App\Traits\ApiResponser;
use App\Traits\Utils;
use Firebase\JWT\JWT;

use DateTime;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserBL
{
    use Utils;

    public function save(array $datos)
    {
        try {
            if (!User::consultUserExistence($datos['celular'], $datos['email'])) {
                $datos["password"] =   bcrypt($datos['password']);
                $datos["tipo"]     =   User::TIPO_USUARIO;
                $user = User::create($datos);
                return $this->responseApi(201, $user, 'Usuario Registrado');
            } else {
                $error = 'Error, no fue posible realizar el registro, su usuario o celular ya esta registrado';
                return $this->responseApi(403, [], $error);
            }
        } catch (Exception $ex) {
            return $this->responseApi(500, [], "Se presento un error en el servidor, intentelo de nuevo");
        }
    }

    public function signup($email, $password) {

        $key = "pruehsdkjsdjskdjsdjskdj";
        try {
           
            $user  =  User::login($email, $password);
            $date1 = new DateTime();
            $date2 =  new DateTime();
            $date2->modify('+2 hours');
            if (is_object($user)) {
               
                $infoToken      = $this->getInfoToken($user, $date1, $date2);
                $jwt            = JWT::encode($infoToken, $key, 'HS256');
                $insertToken    = User::saveTokenAndDateExpiration($user, $jwt, $infoToken['fecha_fin']);

                if ($insertToken) {
                    return $this->responseApi(200, $user, 'Login Correcto');
                }
            }
        } catch (\Throwable $th) {
            return $this->responseApi(403, $th->getMessage(), 'Error, problemas en el servidor');
        }
        
        return $this->responseApi(403, $user, 'Error, Credenciales Invalidas');
    }

    private function getInfoToken($user, $date1, $date2) {
        return array(
            'sub' => $user->id,
            'usuario' => $user->usuario,
            'id' => $user->id,
            'iat' => time(),
            'fecha_ini' =>   $date1->format('Y-m-d H:i:s'),
            'fecha_fin' => $date2->format('Y-m-d H:i:s'),
            //'exp'=> time() + (7 * 24 * 60 * 60 ) // aca es para una semana 7 días x 24 horas x 60 miutos x 60 segundos
            'exp'=> time() + (60 * 60),
        );
    }

    public function checkToken($jwt) {

        $resp = false;

        $date = new DateTime();
        $user = User::validateExpirationToken($jwt, $date);

        if (is_object($user)) {
            $date2 = new DateTime(User::returnDateExpiration($jwt));
            $date2->modify('+15 minute');
            User::refrescarToken($jwt, $date2->format('Y-m-d H:i:s'));
            
            $resp =  true;
        }else {
            User::removeToken($jwt);
        }
        return  $resp;

    }

    public function logout($userId) {

        try {
           
            $result = User::logout($userId);
            
            if($result){
                return $this->responseApi(403, [], 'Cierre de sesión exitoso');
            } else {
                return $this->responseApi(403, [], 'Error, al cerrar sesión. intentelo de nuevo');
            }

        } catch (\Throwable $th) {
            return $this->responseApi(403, $th->getMessage(), 'Error, problemas en el servidor');
        }
        
    }

    public function saveUserMobil(array $datos)
    {
        $dataUser = $datos['datosUsuario'];
        $dataMobile = $datos['datosMobile'];

        try {
            if (!User::consultUserExistence($dataUser['celular'], $dataUser['email'])) {

                $result = DB::transaction(function () use(&$dataUser, &$dataMobile) {
                    $dataUser["password"] =   bcrypt($dataUser['password']);
                    $dataUser["tipo"]     =   User::TIPO_CONDUCTOR;
                    $user = User::create($dataUser);

                    if($user) {
                        $dataMobile['ubicacion'] =  $this->getPointByCoordinates($dataMobile['ubicacion']);
                        $dataMobile['user_id'] =  $user->id;
                        Mobile::create( $dataMobile);
                        return $user;
                    }

                    return false;
                });

                if($result){
                    return $this->responseApi(200, $result, 'Usuario conductor registrado con exito');
                }else{
                    return $this->responseApi(200,[], 'Error al registrar el usuario por favor intentelo de nuevo');
                }
                

                
            } else {
                $error = 'Error, no fue posible realizar el registro, su usuario o celular ya esta registrado';
                return $this->responseApi(403, [], $error);
            }
        } catch (Exception $ex) {
            return $this->responseApi(500, $ex->getMessage(), "Se presento un error en el servidor, intentelo de nuevo");
        }
    }
    

    
}