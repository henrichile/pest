<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RedirectTechnicianRoutes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Solo redirigir si está autenticado, es super-admin y está en modo view_as_technician
        if (auth()->check()) {
            $user = auth()->user();
            
            // Verificar si es super-admin
            $isSuperAdmin = false;
            try {
                $isSuperAdmin = $user->hasRole('super-admin');
            } catch (\Exception $e) {
                // Continuar
            }
            
            if ($isSuperAdmin) {
                // Verificar si está en modo view_as_technician
                $isViewingAsTechnician = false;
                if ($request->hasSession()) {
                    $isViewingAsTechnician = $request->session()->get('view_as_technician', false);
                }
                if (!$isViewingAsTechnician) {
                    try {
                        $isViewingAsTechnician = session('view_as_technician', false);
                    } catch (\Exception $e) {
                        // Continuar
                    }
                }
                
                // Si está en modo view_as_technician y accede a rutas de technician, redirigir
                // IMPORTANTE: No redirigir rutas POST para evitar convertir POST en GET
                if ($isViewingAsTechnician && $request->is('technician/*') && $request->method() !== 'POST') {
                    // Mapear rutas de technician a technician-view
                    $path = $request->path();
                    $newPath = str_replace('technician/', 'admin/technician-view/', $path);
                    
                    // Preservar parámetros de consulta
                    $queryString = $request->getQueryString();
                    if ($queryString) {
                        $newPath .= '?' . $queryString;
                    }
                    
                    Log::info('RedirectTechnicianRoutes: Redirecting to admin route', [
                        'original_path' => $path,
                        'new_path' => $newPath,
                        'method' => $request->method(),
                        'user_id' => $user->id,
                        'is_viewing_as_technician' => $isViewingAsTechnician
                    ]);
                    
                    return redirect($newPath);
                }
            }
        }
        
        return $next($request);
    }
}

