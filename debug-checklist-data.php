<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "==========================================\n";
echo "DEBUG CHECKLIST DATA\n";
echo "==========================================\n\n";

// Buscar servicio por ID si se pasa argumento, o el último modificado
$serviceId = $argv[1] ?? null;
if ($serviceId) {
    $service = \App\Models\Service::find($serviceId);
} else {
    $service = \App\Models\Service::latest('updated_at')->first();
}

if ($service) {
    echo "Servicio ID: {$service->id}\n";
    echo "Tipo: {$service->service_type}\n";
    
    $data = $service->checklist_data;
    
    echo "\nESTRUCTURA DE DATOS:\n";
    if (is_array($data)) {
        echo "Claves principales:\n";
        foreach (array_keys($data) as $key) {
            echo "- $key\n";
        }
        
        echo "\nDETALLE DE ESTADÍSTICAS:\n";
        if (isset($data['monitoreo_estadisticas'])) {
            print_r($data['monitoreo_estadisticas']);
        } else {
            echo "❌ No existe la clave 'monitoreo_estadisticas'\n";
        }
        
        echo "\nDETALLE DE FIRMA:\n";
        if (isset($data['monitoreo_firma'])) {
            print_r($data['monitoreo_firma']);
        } else {
            echo "❌ No existe la clave 'monitoreo_firma'\n";
        }

        echo "\nDETALLE DE ANALISIS:\n";
        if (isset($data['monitoreo_analisis'])) {
            print_r($data['monitoreo_analisis']);
        } else {
            echo "❌ No existe la clave 'monitoreo_analisis'\n";
        }

    } else {
        echo "❌ checklist_data no es un array o es null\n";
    }
} else {
    echo "❌ No se encontraron servicios\n";
}
