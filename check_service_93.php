<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Service;

$service = Service::find(94);

if (!$service) {
    echo "Servicio 94 NO ENCONTRADO\n";
    exit;
}

echo "=== SERVICIO 94 ===\n";
echo "Cliente: " . ($service->client->name ?? 'N/A') . "\n";
echo "Tipo: {$service->service_type}\n";
echo "Estado: {$service->status}\n\n";

if (!$service->checklist_data) {
    echo "NO tiene checklist_data\n";
    exit;
}

echo "✓ Tiene checklist_data\n\n";

// Verificar puntos de control
if (isset($service->checklist_data['points'])) {
    echo "=== PUNTOS DE CONTROL ===\n";
    echo "Tipo de datos: " . gettype($service->checklist_data['points']) . "\n";
    echo "Contenido:\n";
    print_r($service->checklist_data['points']);
    echo "\n";
} else {
    echo "✗ NO tiene 'points' en checklist_data\n\n";
}

// Mostrar todas las claves disponibles
echo "=== CLAVES DISPONIBLES EN CHECKLIST_DATA ===\n";
foreach (array_keys($service->checklist_data) as $key) {
    echo "- $key\n";
}
