<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Service;

$service = Service::find(93);

if (!$service) {
    echo "Servicio 93 NO ENCONTRADO\n";
    exit;
}

echo "=== SERVICIO 93 ===\n";
echo "Cliente: " . ($service->client->name ?? 'N/A') . "\n";
echo "Tipo: {$service->service_type}\n";
echo "Estado: {$service->status}\n\n";

if (!$service->checklist_data) {
    echo "NO tiene checklist_data\n";
    exit;
}

echo "✓ Tiene checklist_data\n\n";

if (isset($service->checklist_data['description'])) {
    echo "=== CAMPOS EN DESCRIPTION ===\n";
    foreach ($service->checklist_data['description'] as $key => $value) {
        if (is_string($value)) {
            $preview = strlen($value) > 100 ? substr($value, 0, 100) . '...' : $value;
            echo "$key: " . ($value ? "'$preview'" : '(vacío)') . "\n";
        } else {
            echo "$key: " . gettype($value) . "\n";
        }
    }

    echo "\n=== VERIFICACIÓN ESPECÍFICA ===\n";

    if (isset($service->checklist_data['description']['service_description'])) {
        echo "✓ service_description EXISTE\n";
        echo "  Longitud: " . strlen($service->checklist_data['description']['service_description']) . " caracteres\n";
        echo "  Contenido: '" . $service->checklist_data['description']['service_description'] . "'\n";
    } else {
        echo "✗ service_description NO EXISTE\n";
    }

    if (isset($service->checklist_data['description']['service_sugerencia'])) {
        echo "✓ service_sugerencia EXISTE\n";
        echo "  Longitud: " . strlen($service->checklist_data['description']['service_sugerencia']) . " caracteres\n";
        echo "  Contenido: '" . $service->checklist_data['description']['service_sugerencia'] . "'\n";
    } else {
        echo "✗ service_sugerencia NO EXISTE\n";
    }
} else {
    echo "✗ NO tiene 'description' en checklist_data\n";
}
