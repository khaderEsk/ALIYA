<?php

namespace App\Http\Middleware;


use App\Traits\GeneralTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
{
    use GeneralTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {

            if (Auth::user()->role_id == 'superAdmin') {

                return $next($request);
            } else {

                return $this->returnError(404, 'Access Denied as you are not SuperAdmin');
                // return response()->json(['massage' => 'Access Denied as you are not Admin'], 403);
            }
        } else {
            return response()->json(['massage' => 'Access Denied as you are not SuperAdmin'], 403);
        }

        return $next($request);
    }
}
