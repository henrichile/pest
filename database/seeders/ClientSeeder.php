<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            [
                "name" => "Restaurante El Buen Sabor",
                "rut" => "12.345.678-9",
                "email" => "contacto@buensabor.cl",
                "phone" => "+56 9 1234 5678",
                "address" => "Av. Principal 123, Santiago",
                "business_type" => "Restaurante",
                "contact_person" => "María González",
            ],
            [
                "name" => "Oficinas Corporativas ABC",
                "rut" => "98.765.432-1",
                "email" => "admin@abc.cl",
                "phone" => "+56 9 8765 4321",
                "address" => "Las Condes 456, Santiago",
                "business_type" => "Oficinas",
                "contact_person" => "Carlos Pérez",
            ],
            [
                "name" => "Fábrica Industrial XYZ",
                "rut" => "11.222.333-4",
                "email" => "produccion@xyz.cl",
                "phone" => "+56 9 1111 2222",
                "address" => "Zona Industrial, Maipú",
                "business_type" => "Manufactura",
                "contact_person" => "Ana Rodríguez",
            ],
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }
    }
}
