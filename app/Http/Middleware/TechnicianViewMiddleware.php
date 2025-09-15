<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class TechnicianViewMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // Configurar la vista de técnico basada en la ruta
            if ($request->is('technician/*')) {
                session(['technician_view' => true]);
            } else {
                session(['technician_view' => false]);
            }
            
            // Verificar permisos si está en vista de técnico
            if (session("technician_view", false)) {
                if (!Auth::user()->hasRole("technician")) {
                    if (Auth::user()->hasRole("super-admin")) {
                        // Allow super-admin to view as technician
                    } else {
                        abort(403, "Unauthorized action in technician view.");
                    }
                }
            }
        }
        
        return $next($request);
    }
}
