<?php
// Script de prueba para verificar la lógica del PDF
require_once 'vendor/autoload.php';

// Inicializar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Simular los datos de observación como aparecen en el servicio
$observation = [
    'photo' => 'storage/observations/1757997411_Copilot_20250909_153239.png'
];

echo "Datos de la observación:\n";
echo "Foto original: " . $observation['photo'] . "\n\n";

// Simular la lógica del PDF
$photoFileName = basename($observation['photo']); // Extraer solo el nombre del archivo
echo "Nombre del archivo extraído: " . $photoFileName . "\n";

$photoPath = storage_path('app/public/observations/' . $photoFileName);
echo "Ruta completa construida: " . $photoPath . "\n";

echo "¿El archivo existe? " . (file_exists($photoPath) ? "SÍ" : "NO") . "\n";

if (file_exists($photoPath)) {
    echo "¡Perfecto! El archivo se encuentra en la ubicación correcta.\n";
    echo "Tamaño del archivo: " . filesize($photoPath) . " bytes\n";
} else {
    echo "ERROR: El archivo no se encuentra en la ubicación esperada.\n";
}
