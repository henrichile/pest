<?php

// Prueba rápida de PDF con mapa
// Este archivo se puede ejecutar directamente para probar

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Service;
use App\Helpers\MapboxHelper;

// Simular un servicio con coordenadas para prueba
$testService = new stdClass();
$testService->id = 999;
$testService->latitude = 40.4168;
$testService->longitude = -3.7038;

echo "=== PRUEBA DE GENERACIÓN DE MAPA PARA PDF ===\n";
echo "Servicio de prueba: #{$testService->id}\n";
echo "Coordenadas: {$testService->latitude}, {$testService->longitude}\n\n";

try {
    // Generar mapa
    $mapUrl = MapboxHelper::generateMapboxImage(
        $testService->latitude,
        $testService->longitude,
        600,
        300,
        15
    );
    
    if ($mapUrl) {
        echo "✅ Mapa generado exitosamente\n";
        echo "URL: {$mapUrl}\n";
        
        // Convertir URL a ruta física para PDF
        $mapImagePath = public_path(str_replace(url('/'), '', $mapUrl));
        echo "Ruta física: {$mapImagePath}\n";
        
        if (file_exists($mapImagePath)) {
            echo "✅ Archivo de mapa existe y es accesible\n";
            $fileSize = round(filesize($mapImagePath) / 1024, 2);
            echo "Tamaño del archivo: {$fileSize} KB\n";
        } else {
            echo "❌ Archivo de mapa no encontrado\n";
        }
    } else {
        echo "❌ Error generando mapa\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== ESTADÍSTICAS DE ALMACENAMIENTO ===\n";
$info = MapboxHelper::getStorageInfo();
echo "Archivos totales: {$info['file_count']}\n";
echo "Tamaño total: {$info['total_size_mb']} MB\n";
echo "Directorio: {$info['directory']}\n";

echo "\n✅ PRUEBA COMPLETADA\n";
echo "Los mapas ahora se guardan en ubicación pública y son accesibles para PDFs\n";
