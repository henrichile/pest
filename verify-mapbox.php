<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "==========================================\n";
echo "VERIFICACIÓN DE MAPBOX\n";
echo "==========================================\n\n";

// 1. Verificar Token
$token = config('services.mapbox.access_token') ?: env('MAPBOX_ACCESS_TOKEN');
echo "1. TOKEN:\n";
if ($token) {
    echo "   ✅ Configurado (" . substr($token, 0, 10) . "...)\n";
} else {
    echo "   ❌ NO CONFIGURADO. Agrega MAPBOX_ACCESS_TOKEN en .env\n";
}
echo "\n";

// 2. Verificar Servicio con Coordenadas
echo "2. SERVICIO DE PRUEBA:\n";
$service = \App\Models\Service::whereNotNull('latitude')
    ->whereNotNull('longitude')
    ->latest()
    ->first();

if ($service) {
    echo "   ✅ Servicio encontrado: ID {$service->id}\n";
    echo "   📍 Coordenadas: {$service->latitude}, {$service->longitude}\n";
    
    // 3. Probar Generación de Mapa
    echo "\n3. GENERACIÓN DE IMAGEN:\n";
    try {
        $url = $service->generateMapImage(600, 300, 15);
        if ($url) {
            echo "   ✅ URL generada: {$url}\n";
            
            // Verificar archivo local
            $filename = basename($url);
            $path = storage_path('app/public/maps/' . $filename);
            if (file_exists($path)) {
                echo "   ✅ Archivo local existe: {$path}\n";
                echo "   📦 Tamaño: " . round(filesize($path) / 1024, 2) . " KB\n";
            } else {
                echo "   ⚠️ Archivo local NO encontrado (puede ser URL remota)\n";
            }
        } else {
            echo "   ❌ No se pudo generar la URL (retornó null)\n";
        }
    } catch (\Exception $e) {
        echo "   ❌ Error generando imagen: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ❌ No se encontraron servicios con coordenadas para probar\n";
}

echo "\n==========================================\n";
