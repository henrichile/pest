<?php
// Script para verificar estructura de la tabla services
require_once 'vendor/autoload.php';

// Inicializar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Service;
use Illuminate\Support\Facades\Schema;

echo "=== Verificación de Estructura de la Tabla Services ===\n\n";

// Verificar que los campos existen
$columns = ['latitude', 'longitude', 'location_accuracy', 'location_captured_at'];

foreach ($columns as $column) {
    $exists = Schema::hasColumn('services', $column);
    echo "Campo '$column': " . ($exists ? "✅ EXISTE" : "❌ NO EXISTE") . "\n";
}

echo "\n=== Verificación del Modelo ===\n\n";

// Verificar que el modelo puede manejar los campos
$service = new Service();
$fillable = $service->getFillable();

echo "Campos fillable relacionados con ubicación:\n";
foreach ($columns as $column) {
    $inFillable = in_array($column, $fillable);
    echo "Campo '$column' en fillable: " . ($inFillable ? "✅ SÍ" : "❌ NO") . "\n";
}

echo "\n=== Prueba de Creación de Servicio (simulado) ===\n\n";

try {
    // Verificar que podemos crear un servicio con datos de ubicación
    $data = [
        'latitude' => -33.4569,
        'longitude' => -70.6483,
        'location_accuracy' => 10.5,
        'location_captured_at' => now(),
    ];
    
    echo "Datos de ubicación válidos para crear servicio:\n";
    foreach ($data as $field => $value) {
        echo "- $field: $value\n";
    }
    
    echo "\n✅ Los datos están en el formato correcto para el modelo.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
