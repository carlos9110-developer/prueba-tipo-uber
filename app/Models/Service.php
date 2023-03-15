<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use MatanYadaev\EloquentSpatial\Objects\Point;

class Service extends Model
{
    use HasFactory;
    protected $table = "servicios";

    const ESTADO_SOLICITADO = 1;
    const ESTADO_ASIGNADO = 2;
    const ESTADO_EN_CURSO = 3;
    const ESTADO_TERMINADO = 4;
    const ESTADO_CANCELADO = 5;
    
    protected $fillable = [
        'user_id',
        'precio',
        'origen',
        'destino',
        'estado_servicio_id'
    ];

    protected $casts = [
        'origen' => Point::class,
        'destino' => Point::class
    ];

    protected $hidden = [
        'user_id',
        'updated_at'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public static function availableServicesByDriver(int $userId)
    {
        return Service::with('user')
        ->whereRaw('ST_Distance_Sphere(origen, (SELECT ubicacion FROM moviles WHERE user_id = ?)) / 1000 <= 50',[$userId])
        ->where('estado_servicio_id', self::ESTADO_SOLICITADO)
        ->get();
    }

    public static function moveServiceToAssignedStatus(int $serviceId)
    {
        return Service::where('id', $serviceId)
                ->where('estado_servicio_id', self::ESTADO_SOLICITADO)
                ->update(['estado_servicio_id' => self::ESTADO_ASIGNADO]);
    }

    public static function assignDriverToService(array $datos)
    {
        DB::table('servicios_conductores')->insert($datos);
    }

    public static function getDriverIdByServiceId($serviceId)
    {
        return DB::table('servicios_conductores')
        ->select('user_id')
        ->where('servicio_id', $serviceId)
        ->first()->user_id;
    }
    
    public static function moveServiceToCancelStatus($serviceId)
    {
        return Service::where('id', $serviceId)
                ->where('estado_servicio_id', self::ESTADO_ASIGNADO)
                ->update(['estado_servicio_id' => self::ESTADO_CANCELADO]);
    }

    public static function moveServiceToFinishedStatus($serviceId)
    {
        return Service::where('id', $serviceId)
                ->where('estado_servicio_id', self::ESTADO_ASIGNADO)
                ->update(['estado_servicio_id' => self::ESTADO_TERMINADO]);
    }
}
