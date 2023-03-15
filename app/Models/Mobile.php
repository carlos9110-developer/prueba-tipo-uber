<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MatanYadaev\EloquentSpatial\Objects\Point;

class Mobile extends Model
{
    use HasFactory;
    protected $table = "moviles";

    const ESTADO_ACTIVO   = "1";
    const ESTADO_OCUPADO  = "2";
    const ESTADO_INACTIVO = "3";

    protected $fillable = [
        'placa',
        'ubicacion',
        'marca',
        'color',
        'user_id'
    ];

    public static function saveLocation(array $datos)
    {
        Mobile::where('user_id', $datos['user_id'])
                ->update([
                    'ubicacion' => $datos['ubicacion'],
                    'fecha_reporte_coordenadas' => now()
                ]);
    }

    public static function activeUser(array $datos)
    {
        Mobile::where('user_id', $datos['user_id'])
                ->where('estado', Mobile::ESTADO_INACTIVO)
                ->update([
                    'estado' => Mobile::ESTADO_ACTIVO,
                ]);
    }

    public static function changeMobileStatus(int $user_id, string $state)
    {
        Mobile::where('user_id', $user_id)
                ->update([
                    'estado' => $state
                ]);
    }

    
    public static function validateMobileAvailability(int $user_id)
    {
        $stateMobile = Mobile::where('user_id', $user_id)->first()->estado;
        return ($stateMobile == self::ESTADO_ACTIVO) ? true : false;
    }

    protected $casts = [
        'ubicacion' => Point::class
    ];
}
