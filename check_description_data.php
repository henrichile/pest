<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Service;

echo "=== Verificación de datos de descripción en servicios de desratización ===\n\n";

// Buscar servicios de desratización
$services = Service::where('service_type', 'desratizacion')
    ->whereNotNull('checklist_data')
    ->orderBy('id', 'desc')
    ->limit(5)
    ->get();

if ($services->isEmpty()) {
    echo "No se encontraron servicios de desratización.\n";
    exit;
}

foreach ($services as $service) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Servicio ID: {$service->id}\n";
    echo "Cliente: " . ($service->client->name ?? 'N/A') . "\n";
    echo "Estado: {$service->status}\n";
    echo "Tipo: {$service->service_type}\n";
    echo "\n";

    $checklistData = $service->checklist_data;

    // Verificar estructura de description
    if (isset($checklistData['description'])) {
        echo "✓ Existe 'description' en checklist_data\n";
        echo "\nCampos en 'description':\n";

        foreach ($checklistData['description'] as $key => $value) {
            if (is_string($value)) {
                $preview = strlen($value) > 100 ? substr($value, 0, 100) . '...' : $value;
                echo "  - {$key}: " . ($value ? "'{$preview}'" : "(vacío)") . "\n";
            } else {
                echo "  - {$key}: " . gettype($value) . "\n";
            }
        }

        echo "\n";

        // Verificar campos específicos
        if (isset($checklistData['description']['service_description'])) {
            echo "✓ Campo 'service_description' EXISTE\n";
            $desc = $checklistData['description']['service_description'];
            echo "  Contenido: " . ($desc ? "SÍ tiene datos (" . strlen($desc) . " caracteres)" : "VACÍO") . "\n";
        } else {
            echo "✗ Campo 'service_description' NO EXISTE\n";
        }

        if (isset($checklistData['description']['service_sugerencia'])) {
            echo "✓ Campo 'service_sugerencia' EXISTE\n";
            $sug = $checklistData['description']['service_sugerencia'];
            echo "  Contenido: " . ($sug ? "SÍ tiene datos (" . strlen($sug) . " caracteres)" : "VACÍO") . "\n";
        } else {
            echo "✗ Campo 'service_sugerencia' NO EXISTE\n";
        }

        // Verificar si existe el campo antiguo 'content'
        if (isset($checklistData['description']['content'])) {
            echo "⚠ Campo antiguo 'content' también existe\n";
        }

    } else {
        echo "✗ NO existe 'description' en checklist_data\n";
    }

    echo "\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\nVerificación completada.\n";
