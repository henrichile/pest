<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionController extends Controller
{
    private function checkSuperAdmin()
    {
        if (!auth()->user() || !auth()->user()->hasRole('super-admin')) {
            abort(403, 'No tienes permisos para acceder a esta sección');
        }
    }

    public function index()
    {
        $this->checkSuperAdmin();
        
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        $users = User::with('roles')->get();
        
        return view('admin.roles-permissions', compact('roles', 'permissions', 'users'));
    }

    public function store(Request $request)
    {
        $this->checkSuperAdmin();
        
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $role = Role::create(['name' => $request->name]);
        
        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.roles-permissions')
            ->with('success', 'Rol creado exitosamente');
    }

    public function update(Request $request, Role $role)
    {
        $this->checkSuperAdmin();
        
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('admin.roles-permissions')
            ->with('success', 'Rol actualizado exitosamente');
    }

    public function destroy(Role $role)
    {
        $this->checkSuperAdmin();
        
        if ($role->name === 'super-admin') {
            return redirect()->back()
                ->with('error', 'No se puede eliminar el rol super-admin');
        }

        $role->delete();

        return redirect()->route('admin.roles-permissions')
            ->with('success', 'Rol eliminado exitosamente');
    }

    public function assignRole(Request $request)
    {
        $this->checkSuperAdmin();
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|exists:roles,name'
        ]);

        $user = User::find($request->user_id);
        $user->syncRoles([$request->role]);

        return redirect()->route('admin.roles-permissions')
            ->with('success', 'Rol asignado exitosamente al usuario');
    }

    public function createPermission(Request $request)
    {
        $this->checkSuperAdmin();
        
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name'
        ]);

        Permission::create(['name' => $request->name]);

        return redirect()->route('admin.roles-permissions')
            ->with('success', 'Permiso creado exitosamente');
    }

    public function deletePermission(Permission $permission)
    {
        $this->checkSuperAdmin();
        
        $permission->delete();

        return redirect()->route('admin.roles-permissions')
            ->with('success', 'Permiso eliminado exitosamente');
    }
}
