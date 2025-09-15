<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Crear permisos
        $permissions = [
            "view-dashboard",
            "view-services",
            "create-services",
            "edit-services",
            "delete-services",
            "start-services",
            "complete-services",
            "view-clients",
            "create-clients",
            "edit-clients",
            "delete-clients",
            "view-products",
            "create-products",
            "edit-products",
            "delete-products",
            "view-statistics",
            "view-users",
            "create-users",
            "edit-users",
            "delete-users",
            "manage-roles",
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(["name" => $permission]);
        }

        // Crear roles
        $superAdminRole = Role::firstOrCreate(["name" => "super-admin"]);
        $technicianRole = Role::firstOrCreate(["name" => "technician"]);

        // Asignar permisos al super admin
        $superAdminRole->givePermissionTo(Permission::all());

        // Asignar permisos al técnico
        $technicianRole->givePermissionTo([
            "view-dashboard",
            "view-services",
            "start-services",
            "complete-services",
            "view-clients",
            "view-products",
        ]);

        // Crear usuario super admin si no existe
        $admin = User::firstOrCreate(
            ["email" => "admin@pestcontroller.cl"],
            [
                "name" => "Super Admin",
                "password" => bcrypt("admin123"),
                "email_verified_at" => now(),
            ]
        );

        $admin->assignRole("super-admin");

        // Crear usuario técnico de ejemplo
        $technician = User::firstOrCreate(
            ["email" => "tecnico@pestcontroller.cl"],
            [
                "name" => "Técnico Demo",
                "password" => bcrypt("tecnico123"),
                "email_verified_at" => now(),
            ]
        );

        $technician->assignRole("technician");
    }
}
