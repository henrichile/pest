<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ViewController extends Controller
{
    /**
     * Cambiar a vista de técnico
     */
    public function switchToTechnicianView()
    {
        if (auth()->user()->hasRole("super-admin")) {
            session(["technician_view" => true]);
            return redirect()->back()->with("success", "Cambiado a vista de técnico");
        }
        
        return redirect()->back()->with("error", "No tienes permisos para cambiar de vista");
    }

    /**
     * Volver a vista de administrador
     */
    public function switchToAdminView()
    {
        if (session("technician_view", false)) {
            session()->forget(["technician_view", "original_user_role", "technician_view_user"]);
            return redirect()->back()->with("success", "Cambiado a vista de administrador");
        }
        
        return redirect()->back()->with("error", "No estás en vista de técnico");
    }

    /**
     * Obtener la vista actual
     */
    public function getCurrentView()
    {
        return response()->json([
            "is_technician_view" => session("technician_view", false),
            "user_role" => session("technician_view", false) ? "technician" : auth()->user()->roles->first()->name ?? "user"
        ]);
    }
}
