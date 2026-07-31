<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {

        $user = $request->user();

        if(!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // 1. Buat nama kunci unik di Redis untuk user ini
        $redisKey = "user_permissions_{$user->id}";

        // 2. Cek Redis. Jika kosong, ambil dari MySQL, lalu simpan ke Redis selama 24 jam
        $permissions = Cache::remember($redisKey, 86400, function () use ($user) {
            return $user->roles()
                ->with('permissions')
                ->get()
                ->pluck('permissions')
                ->flatten()
                ->pluck('name')
                ->unique()
                ->toArray();
        });

        // 3. Cek apakah permission yang diminta ada di dalam database Redis tadi
        if (!in_array($permission, $permissions)) {
            return response()->json([
                'message' => "Forbidden! Anda tidak punya hak akses: {$permission}"
            ], 403);
        }

        return $next($request);
    }
}
