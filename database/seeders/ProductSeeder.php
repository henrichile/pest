<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Productos para DESRATIZACIÓN
            [
                "name" => "DETIA PLUS BLOQUE",
                "active_ingredient" => "Brodifacoum 0.005%",
                "service_type" => "desratizacion",
                "sag_registration" => "P-426/15",
                "stock" => 50,
                "unit" => "kg",
                "description" => "Rodenticida anticoagulante en bloque para control de roedores",
            ],
            [
                "name" => "RASTOP MOLIENDA",
                "active_ingredient" => "Bromadolona 0.005%",
                "service_type" => "desratizacion",
                "sag_registration" => "P-446/16",
                "stock" => 40,
                "unit" => "kg",
                "description" => "Rodenticida en molienda para estaciones de cebo",
            ],
            [
                "name" => "PASTA RASTOP",
                "active_ingredient" => "Bromadolona 0.005%",
                "service_type" => "desratizacion",
                "sag_registration" => "P-498/16",
                "stock" => 35,
                "unit" => "kg",
                "description" => "Rodenticida en pasta de alta palatabilidad",
            ],
            [
                "name" => "KLERAT WAFLE",
                "active_ingredient" => "Difenacoum 0.005%",
                "service_type" => "desratizacion",
                "sag_registration" => "P-125/08",
                "stock" => 25,
                "unit" => "kg",
                "description" => "Rodenticida en forma de wafle para uso profesional",
            ],

            // Productos para DESINSECTACIÓN
            [
                "name" => "CYPERMETRINA 25 EC",
                "active_ingredient" => "Cipermetrina 25%",
                "service_type" => "desinsectacion",
                "sag_registration" => "I-891/18",
                "stock" => 30,
                "unit" => "litros",
                "description" => "Insecticida concentrado emulsionable para control de insectos",
            ],
            [
                "name" => "DELTAMETRINA SC",
                "active_ingredient" => "Deltametrina 2.5%",
                "service_type" => "desinsectacion",
                "sag_registration" => "I-1205/20",
                "stock" => 28,
                "unit" => "litros",
                "description" => "Insecticida suspensión concentrada de acción residual",
            ],
            [
                "name" => "GEL MAXFORCE",
                "active_ingredient" => "Hidrametilnón 2.15%",
                "service_type" => "desinsectacion",
                "sag_registration" => "I-678/15",
                "stock" => 20,
                "unit" => "unidades",
                "description" => "Cebo gel para control de cucarachas y hormigas",
            ],
            [
                "name" => "LAMBDA WG",
                "active_ingredient" => "Lambda-cialotrina 10%",
                "service_type" => "desinsectacion",
                "sag_registration" => "I-1456/21",
                "stock" => 15,
                "unit" => "kg",
                "description" => "Insecticida granulado dispersable en agua",
            ],
            [
                "name" => "IMIDACLOPRID SC",
                "active_ingredient" => "Imidacloprid 20%",
                "service_type" => "desinsectacion",
                "sag_registration" => "I-789/17",
                "stock" => 22,
                "unit" => "litros",
                "description" => "Insecticida sistémico para control de insectos chupadores",
            ],

            // Productos para DESINFECCIÓN
            [
                "name" => "VIREX TB",
                "active_ingredient" => "Cloruro de alquildimetilbencilamonio 0.2%",
                "service_type" => "desinfeccion",
                "isp_registration" => "ISP-F-3012/22",
                "stock" => 35,
                "unit" => "litros",
                "description" => "Desinfectante tuberculocida de nivel hospitalario",
            ],
            [
                "name" => "OXONIA ACTIVE",
                "active_ingredient" => "Peróxido de hidrógeno 15% + Ácido peracético 5%",
                "service_type" => "desinfeccion",
                "isp_registration" => "ISP-F-2890/21",
                "stock" => 28,
                "unit" => "litros",
                "description" => "Desinfectante sporicida de alta eficacia",
            ],
            [
                "name" => "CLORAMINE T",
                "active_ingredient" => "N-cloro-p-toluenosulfonamida sódica 99%",
                "service_type" => "desinfeccion",
                "isp_registration" => "ISP-F-2156/20",
                "stock" => 15,
                "unit" => "kg",
                "description" => "Desinfectante clorado en polvo de liberación lenta",
            ],
            [
                "name" => "FORMALDEHÍDO 37%",
                "active_ingredient" => "Formaldehído 37%",
                "service_type" => "desinfeccion",
                "isp_registration" => "ISP-F-1789/19",
                "stock" => 12,
                "unit" => "litros",
                "description" => "Desinfectante de alto nivel para fumigación",
            ],
            [
                "name" => "GLUTARALDEHÍDO 2%",
                "active_ingredient" => "Glutaraldehído 2%",
                "service_type" => "desinfeccion",
                "isp_registration" => "ISP-F-2456/21",
                "stock" => 20,
                "unit" => "litros",
                "description" => "Desinfectante esterilizante para equipos médicos",
            ],
            [
                "name" => "OZONO PLUS",
                "active_ingredient" => "Ozono estabilizado 3%",
                "service_type" => "desinfeccion",
                "isp_registration" => "ISP-F-3145/23",
                "stock" => 8,
                "unit" => "litros",
                "description" => "Desinfectante oxidante ecológico sin residuos",
            ],

            // Productos para SANITIZACIÓN
            [
                "name" => "DESINFECTANTE QUATERNARIO",
                "active_ingredient" => "Cloruro de benzalconio 10%",
                "service_type" => "sanitizacion",
                "isp_registration" => "ISP-F-2156/18",
                "stock" => 40,
                "unit" => "litros",
                "description" => "Desinfectante de cuarta generación de amplio espectro",
            ],
            [
                "name" => "CLORO LÍQUIDO INDUSTRIAL",
                "active_ingredient" => "Hipoclorito de sodio 5%",
                "service_type" => "sanitizacion",
                "isp_registration" => "ISP-F-1892/17",
                "stock" => 60,
                "unit" => "litros",
                "description" => "Desinfectante clorado para sanitización de superficies",
            ],
            [
                "name" => "PERÓXIDO DE HIDRÓGENO",
                "active_ingredient" => "Peróxido de hidrógeno 35%",
                "service_type" => "sanitizacion",
                "isp_registration" => "ISP-F-2301/19",
                "stock" => 18,
                "unit" => "litros",
                "description" => "Desinfectante oxidante de alto nivel",
            ],
            [
                "name" => "ALCOHOL ISOPROPÍLICO",
                "active_ingredient" => "Alcohol isopropílico 70%",
                "service_type" => "sanitizacion",
                "isp_registration" => "ISP-F-1654/16",
                "stock" => 45,
                "unit" => "litros",
                "description" => "Alcohol desinfectante para equipos y superficies",
            ],
            [
                "name" => "AMONIO CUATERNARIO PLUS",
                "active_ingredient" => "Cloruro de didecildimetilamonio 8%",
                "service_type" => "sanitizacion",
                "isp_registration" => "ISP-F-2478/20",
                "stock" => 32,
                "unit" => "litros",
                "description" => "Desinfectante bactericida, virucida y fungicida",
            ]
        ];

        // Eliminar productos existentes para evitar duplicados
        Product::query()->delete();

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
