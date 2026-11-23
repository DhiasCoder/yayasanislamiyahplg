<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        ...$roles
    ): Response {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        $hasRole = false;

        if (method_exists($user, 'roles')) {
            $hasRole = $user->roles()
                ->whereIn('nama_role', $roles)
                ->exists();
        } else {
            $roleNames = [];

            if (isset($user->roles) && is_iterable($user->roles)) {
                $roleNames = collect($user->roles)->pluck('nama_role')->filter()->all();
            } elseif ($user->role ?? null) {
                if (is_object($user->role)) {
                    $roleNames[] = $user->role->nama_role ?? null;
                } else {
                    $roleNames[] = $user->role;
                }
            }

            $hasRole = !empty(array_intersect($roles, array_filter($roleNames)));
        }

        if (!$hasRole) {
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
