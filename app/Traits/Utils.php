<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

trait Utils
{
    private function responseApi($code = 200, $data  = [], $message = '')
	{
		return response(['code' => $code, 'data' => $data, 'message' => $message], $code);
	}

	private function getPointByCoordinates(array $coordinates)
    {
        $latitud = $coordinates['latitud'];
        $longitud = $coordinates['longitud'];
        return DB::raw("POINT( {$latitud} , {$longitud} )");
    }





}
