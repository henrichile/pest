<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Mostrar el formulario de edición del perfil
     */
    public function edit()
    {
        $user = Auth::user();
        return view("profile.edit", compact("user"));
    }

    /**
     * Actualizar el perfil del usuario
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            "name" => ["required", "string", "max:255"],
            "email" => ["required", "string", "email", "max:255", Rule::unique("users")->ignore($user->id)],
            "current_password" => ["nullable", "string"],
            "password" => ["nullable", "string", "min:8", "confirmed"],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        // Actualizar contraseña si se proporciona
        if ($request->filled("password")) {
            if (!$request->filled("current_password") || !Hash::check($request->current_password, $user->password)) {
                return redirect()->back()->withErrors(["current_password" => "La contraseña actual es incorrecta."]);
            }
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route("profile.edit")->with("success", "Perfil actualizado correctamente.");
    }
}
