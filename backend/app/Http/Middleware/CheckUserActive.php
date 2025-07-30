<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth('api')->user();

        if (!$user || !$user->active) {
            return response()->json([
                'message' => 'Akun tidak aktif atau tidak ditemukan'
            ], 403);
        }

        return $next($request);
    }
}
