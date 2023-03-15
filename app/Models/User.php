<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use DateTime;
use Illuminate\Support\Facades\Hash;


class User extends Authenticatable 
{
    use HasApiTokens, HasFactory, Notifiable;

    const TIPO_USUARIO   = '1';
    const TIPO_CONDUCTOR = '2';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'tipo',
        'celular',
        'email',
        'password'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    protected $hidden = [
        'password',
        'tipo'
    ];

    public static function consultUserExistence($celular, $usuario)
    {
        $count  = User::where('celular', $celular)
        ->orWhere('email', $usuario)
        ->count();
        return ($count > 0) ? true : false;
    }

    public  static function login(string $email, string $password) {
        $user = User::where('email', $email)->where('password', hash("SHA256",$password))->first();
        if (is_object($user)) {
            return $user;
        }
        return false;
    }

    public static function saveTokenAndDateExpiration(User $user, $jwt, $fechaFin){
        $user->token = $jwt;
        $user->fecha_vencimiento_token = $fechaFin;
        return $user->save();
    }

    public static function validateExpirationToken(string $token,DateTime $fechaActual){
        return User::where('token', $token)->where('fecha_vencimiento_token', '>=', $fechaActual)->first();
    }

    public static function refrescarToken(string $token,string $fechaNueva){
        return User::where('token', $token)->update(['fecha_vencimiento_token' => $fechaNueva]);
    }

    public static function removeToken(string $token){
        return User::where('token', $token)->update(['fecha_vencimiento_token' => null, 'token' => null]);
    }

    public static function returnDateExpiration(string $token){
        return User::where('token', $token)->first()->fecha_vencimiento_token;
    }
    
    public static function logout(int $userId) {
        return User::where('id', $userId)->update(['fecha_vencimiento_token' => null, 'token' => null]);
    }
}
