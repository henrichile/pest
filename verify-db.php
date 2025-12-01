#!/usr/bin/env php
<?php

// Script para verificar datos en la base de datos

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "==========================================\n";
echo "VERIFICACIÓN DE DATOS EN BASE DE DATOS\n";
echo "==========================================\n\n";

// Obtener último servicio
$service = \App\Models\Service::latest()->first();

if (!$service) {
    echo "❌ No hay servicios en la base de datos\n";
    exit(1);
}

echo "✅ Servicio encontrado\n";
echo "   ID: {$service->id}\n";
echo "   Cliente: {$service->client->name}\n";
echo "   Fecha: {$service->scheduled_date}\n\n";

// Verificar checklist_data
if (!$service->checklist_data) {
    echo "❌ checklist_data está vacío\n";
    exit(1);
}

echo "📊 VERIFICANDO DATOS DEL CHECKLIST:\n\n";

// 1. Monitoreo Completo
echo "1. MONITOREO COMPLETO:\n";
if (isset($service->checklist_data['monitoreo_completo'])) {
    $mc = $service->checklist_data['monitoreo_completo'];
    $baitStations = $mc['bait_stations'] ?? [];
    echo "   ✅ Existe\n";
    echo "   Cebaderas: " . count($baitStations) . "\n";
    
    if (count($baitStations) > 0) {
        $firstStation = $baitStations[0];
        echo "   Primera cebadera:\n";
        echo "     - Código: " . ($firstStation['code'] ?? 'N/A') . "\n";
        echo "     - Consumo: " . ($firstStation['consumption'] ?? 'N/A') . "%\n";
        echo "     - Capturas: " . ($firstStation['captures'] ?? 'N/A') . "\n";
        
        if (isset($firstStation['photos']) && count($firstStation['photos']) > 0) {
            echo "     - Fotos: " . count($firstStation['photos']) . "\n";
            echo "       → " . $firstStation['photos'][0] . "\n";
            
            // Verificar si el archivo existe
            $photoPath = str_replace('storage/', '', $firstStation['photos'][0]);
            $fullPath = storage_path('app/public/' . $photoPath);
            if (file_exists($fullPath)) {
                $size = filesize($fullPath);
                echo "       ✅ Archivo existe (" . number_format($size / 1024, 2) . " KB)\n";
            } else {
                echo "       ❌ Archivo NO existe: {$fullPath}\n";
            }
        } else {
            echo "     - Fotos: ❌ NO HAY FOTOS\n";
        }
    }
} else {
    echo "   ❌ NO existe\n";
}
echo "\n";

// 2. Croquis
echo "2. CROQUIS:\n";
if (isset($service->checklist_data['monitoreo_croquis'])) {
    $croquis = $service->checklist_data['monitoreo_croquis'];
    echo "   ✅ Existe\n";
    
    if (isset($croquis['croquis_file'])) {
        echo "   Archivo: {$croquis['croquis_file']}\n";
        
        // Verificar si el archivo existe
        $croquisPath = str_replace('storage/', '', $croquis['croquis_file']);
        $fullPath = storage_path('app/public/' . $croquisPath);
        if (file_exists($fullPath)) {
            $size = filesize($fullPath);
            echo "   ✅ Archivo existe (" . number_format($size / 1024, 2) . " KB)\n";
        } else {
            echo "   ❌ Archivo NO existe: {$fullPath}\n";
        }
    } else {
        echo "   ❌ NO hay archivo de croquis\n";
    }
    
    if (isset($croquis['croquis_notes'])) {
        echo "   Notas: " . substr($croquis['croquis_notes'], 0, 50) . "...\n";
    }
} else {
    echo "   ❌ NO existe\n";
}
echo "\n";

// 3. Estadísticas
echo "3. ESTADÍSTICAS:\n";
if (isset($service->checklist_data['monitoreo_estadisticas'])) {
    $stats = $service->checklist_data['monitoreo_estadisticas'];
    echo "   ✅ Existe\n";
    echo "   Total monitoreadas: " . ($stats['total_monitored'] ?? 'N/A') . "\n";
    echo "   Consumo promedio: " . ($stats['average_consumption_percent'] ?? 'N/A') . "%\n";
    
    if (isset($stats['historical_data'])) {
        echo "   ✅ Historical data existe\n";
        echo "   Días: " . count($stats['historical_data']) . "\n";
        
        if (count($stats['historical_data']) > 0) {
            echo "   Últimos 3 días:\n";
            $lastDays = array_slice($stats['historical_data'], -3);
            foreach ($lastDays as $day) {
                echo "     - {$day['date']}: {$day['consumption_percent']}% consumo, {$day['captures']} capturas\n";
            }
        }
    } else {
        echo "   ❌ Historical data NO existe\n";
    }
} else {
    echo "   ❌ NO existe\n";
}
echo "\n";

echo "==========================================\n";
echo "FIN DE LA VERIFICACIÓN\n";
echo "==========================================\n";
