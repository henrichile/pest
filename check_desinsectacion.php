<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Service;

// Buscar el servicio de desinsectación más reciente
$service = Service::where('service_type', 'desinsectacion')->latest()->first();

if ($service) {
    echo "Service ID: " . $service->id . "\n";
    echo "Service Type: " . $service->service_type . "\n\n";

    if ($service->checklist_data) {
        echo "Checklist Data completa:\n";
        print_r($service->checklist_data);
        echo "\n\n";

        if (isset($service->checklist_data['products'])) {
            echo "Sección de PRODUCTOS:\n";
            print_r($service->checklist_data['products']);
            echo "\n";
        } else {
            echo "NO HAY sección de productos\n";
        }
    } else {
        echo "NO HAY checklist_data\n";
    }
} else {
    echo "No se encontró ningún servicio de desinsectación\n";
}
