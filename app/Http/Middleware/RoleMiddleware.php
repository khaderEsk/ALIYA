<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Traits\GeneralTrait;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Spatie\Permission\Contracts\Role;

class RoleMiddleware
{
    use GeneralTrait;

    public function handle($request, Closure $next, $role)
    {
        $user = Auth::user();

        // تحقق إذا كان المستخدم موجودًا
        if (!$user) {
            return response()->json(['message' => 'Unauthorized: User not authenticated'], 401);
        }

        // تحقق من الدور
        if (!$user->hasRole($role)) {
            return response()->json(['message' => 'This action is unauthorized.'], 403);
        }

        return $next($request);
    }
}
