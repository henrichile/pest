<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                "name" => "Rodenticida Plus",
                "active_ingredient" => "Brodifacoum",
                "service_type" => "desratizacion",
                "sag_registration" => "SAG-12345",
                "stock" => 50,
                "unit" => "kg",
                "description" => "Rodenticida anticoagulante para control de roedores",
            ],
            [
                "name" => "Insecticida Profesional",
                "active_ingredient" => "Deltametrina",
                "service_type" => "desinsectacion",
                "sag_registration" => "SAG-67890",
                "stock" => 30,
                "unit" => "litros",
                "description" => "Insecticida de amplio espectro para cucarachas y moscas",
            ],
            [
                "name" => "Desinfectante Hospitalario",
                "active_ingredient" => "Cloruro de benzalconio",
                "service_type" => "sanitizacion",
                "isp_registration" => "ISP-54321",
                "stock" => 25,
                "unit" => "litros",
                "description" => "Desinfectante de alto nivel para sanitización",
            ],
            [
                "name" => "Cebo para Hormigas",
                "active_ingredient" => "Hidrametilnón",
                "service_type" => "desinsectacion",
                "sag_registration" => "SAG-98765",
                "stock" => 15,
                "unit" => "unidades",
                "description" => "Cebo gel para control de hormigas",
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
