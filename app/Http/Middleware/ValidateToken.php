<?php

namespace App\Http\Middleware;

use App\BL\User\UserBL;
use App\Traits\Utils;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateToken
{
    use Utils;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $usersBL = new UserBL();
        $tokenRequest = $request->header('Authorization', null);
        if ($tokenRequest){
            $checkToken =   $usersBL->checkToken($tokenRequest);
            if ($checkToken) {
                return $next($request);
            }
        }
        return $this->responseApi(403, [], 'Error, su token de sesión ha vencido, intentelo de nuevo');
        //
    }
}
