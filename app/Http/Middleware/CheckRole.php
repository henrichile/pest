<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Log para verificar que el middleware se ejecuta
        \Log::info('CheckRole middleware EXECUTING', [
            'role' => $role,
            'path' => $request->path(),
            'authenticated' => auth()->check()
        ]);
        
        // PRIMERO: Verificar autenticación
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // SOLUCIÓN DEFINITIVA: Si es ruta de técnico, redirigir super-admins en modo view_as_technician
        // a las rutas de admin/technician-view
        if ($role === 'technician') {
            // Verificar si es super-admin
            $isSuperAdmin = false;
            
            // Método 1: isSuperAdmin() del modelo
            if (method_exists($user, 'isSuperAdmin')) {
                try {
                    $isSuperAdmin = $user->isSuperAdmin();
                } catch (\Exception $e) {
                    // Continuar
                }
            }
            
            // Método 2: hasRole('super-admin')
            if (!$isSuperAdmin) {
                try {
                    $isSuperAdmin = $user->hasRole('super-admin');
                } catch (\Exception $e) {
                    // Continuar
                }
            }
            
            // Método 3: Verificar en array de roles
            if (!$isSuperAdmin) {
                try {
                    $userRoles = $user->getRoleNames()->toArray();
                    $isSuperAdmin = in_array('super-admin', $userRoles);
                } catch (\Exception $e) {
                    // Continuar
                }
            }
            
            // Si es super-admin, verificar si está en modo view_as_technician
            if ($isSuperAdmin) {
                $viewAsTechnician = false;
                if ($request->hasSession()) {
                    $viewAsTechnician = $request->session()->get('view_as_technician', false);
                }
                if (!$viewAsTechnician) {
                    try {
                        $viewAsTechnician = session('view_as_technician', false);
                    } catch (\Exception $e) {
                        // Continuar
                    }
                }
                
                // Si está en modo view_as_technician, redirigir a la ruta de admin/technician-view
                if ($viewAsTechnician) {
                    $path = $request->path();
                    $newPath = str_replace('technician/', 'admin/technician-view/', $path);
                    
                    // Preservar parámetros de consulta
                    $queryString = $request->getQueryString();
                    if ($queryString) {
                        $newPath .= '?' . $queryString;
                    }
                    
                    \Log::info('CheckRole: Redirecting super-admin to technician-view route', [
                        'original_path' => $path,
                        'new_path' => $newPath,
                        'user_id' => $user->id
                    ]);
                    
                    return redirect($newPath);
                }
            }
            
            // Verificación normal de rol de técnico
            if (!$user->hasRole($role)) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Insufficient permissions'], 403);
                }
                abort(403, 'USER DOES NOT HAVE THE RIGHT ROLES.');
            }
            
            return $next($request);
        }
        
        // Verificación normal de roles para otros roles (no technician)
        if (!$user->hasRole($role)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Insufficient permissions'], 403);
            }
            abort(403, 'USER DOES NOT HAVE THE RIGHT ROLES.');
        }

        return $next($request);
    }
}
