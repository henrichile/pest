<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    private function checkSuperAdmin()
    {
        if (!auth()->user() || !auth()->user()->hasRole("super-admin")) {
            abort(403, "No tienes permisos para acceder a esta sección");
        }
    }

    public function index()
    {
        $this->checkSuperAdmin();
        $users = User::with("roles")->latest()->paginate(10);
        return view("users.index", compact("users"));
    }

    public function create()
    {
        $this->checkSuperAdmin();
        $roles = Role::all();
        return view("users.create", compact("roles"));
    }

    public function store(Request $request)
    {
        $this->checkSuperAdmin();
        
        $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|email|max:255|unique:users",
            "password" => "required|string|min:8|confirmed",
            "role" => "required|exists:roles,name",
        ]);

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => bcrypt($request->password),
            "email_verified_at" => now(),
        ]);

        $user->assignRole($request->role);

        return redirect()->route("users.index")->with("success", "Usuario creado exitosamente");
    }

    public function show(User $user)
    {
        $this->checkSuperAdmin();
        $user->load("roles");
        return view("users.show", compact("user"));
    }

    public function edit(User $user)
    {
        $this->checkSuperAdmin();
        $roles = Role::all();
        return view("users.edit", compact("user", "roles"));
    }

    public function update(Request $request, User $user)
    {
        $this->checkSuperAdmin();
        
        $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|email|max:255|unique:users,email," . $user->id,
            "password" => "nullable|string|min:8|confirmed",
            "role" => "required|exists:roles,name",
        ]);

        $data = [
            "name" => $request->name,
            "email" => $request->email,
        ];

        if ($request->password) {
            $data["password"] = bcrypt($request->password);
        }

        $user->update($data);
        $user->syncRoles([$request->role]);

        return redirect()->route("users.index")->with("success", "Usuario actualizado exitosamente");
    }

    public function destroy(User $user)
    {
        $this->checkSuperAdmin();
        
        if ($user->id === auth()->id()) {
            return redirect()->back()->with("error", "No puedes eliminar tu propio usuario");
        }

        $user->delete();
        return redirect()->route("users.index")->with("success", "Usuario eliminado exitosamente");
    }

    public function toggleStatus(User $user)
    {
        $this->checkSuperAdmin();
        
        if ($user->id === auth()->id()) {
            return redirect()->back()->with("error", "No puedes cambiar el estado de tu propio usuario");
        }

        $user->update([
            "email_verified_at" => $user->email_verified_at ? null : now(),
        ]);

        $status = $user->email_verified_at ? "activado" : "desactivado";
        return redirect()->back()->with("success", "Usuario {$status} exitosamente");
    }
}
