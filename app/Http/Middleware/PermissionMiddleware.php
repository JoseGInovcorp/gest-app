<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // Verificar se o utilizador está autenticado
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Super Admin tem acesso a tudo
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Verificar se o utilizador tem a permissão necessária
        if (!$user->can($permission)) {
            abort(403, 'Não tem permissão para aceder a esta funcionalidade.');
        }

        return $next($request);
    }
}
