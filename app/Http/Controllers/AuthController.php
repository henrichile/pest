<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Si ya está autenticado, redirigir al dashboard
        if (Auth::check()) {
            // Redirigir según el rol del usuario
            $user = Auth::user();
            if ($user->hasRole("super-admin")) {
                return redirect("/admin/dashboard");
            } else {
                return redirect("/technician/dashboard");
            }
        }
        
        return view('auth.login');
    }
    
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Redirigir según el rol del usuario
            $user = Auth::user();
            if ($user->hasRole('super-admin')) {
                return redirect('/admin/dashboard');
            } else {
                return redirect('/technician/dashboard');
            }
        }
        
        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->withInput($request->only('email'));
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
}
