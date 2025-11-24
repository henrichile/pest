<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\Service;
use Illuminate\Support\Facades\DB;

echo "=== DIAGNÓSTICO DE IMÁGENES DE MONITOREO ===\n\n";

// 1. Buscar servicios de monitoreo-cebaderas
$services = Service::where('service_type', 'monitoreo-cebaderas')
    ->orderBy('id', 'desc')
    ->get();

if ($services->count() === 0) {
    echo "❌ No se encontraron servicios de tipo 'monitoreo-cebaderas'\n";
    echo "   Crea un servicio de este tipo para comenzar las pruebas.\n\n";
    exit(0);
}

echo "✓ Encontrados {$services->count()} servicio(s) de monitoreo-cebaderas\n\n";

foreach ($services as $service) {
    echo "═══════════════════════════════════════════════════════════\n";
    echo "SERVICIO ID: {$service->id}\n";
    echo "Cliente: " . ($service->client->name ?? 'N/A') . "\n";
    echo "Status: {$service->status}\n";
    echo "Etapa Actual: " . ($service->checklist_stage ?? 'NULL') . "\n";
    echo "Creado: {$service->created_at}\n";
    echo "Actualizado: {$service->updated_at}\n";
    echo "───────────────────────────────────────────────────────────\n";

    $data = $service->checklist_data;

    if (!$data || empty($data)) {
        echo "⚠ No hay datos de checklist para este servicio\n\n";
        continue;
    }

    // Verificar cada etapa
    $etapas = [
        'monitoreo_datos' => '1. Datos del Servicio',
        'monitoreo_croquis' => '2. Croquis de Cebaderas',
        'monitoreo_completo' => '3. Monitoreo Completo',
        'monitoreo_estadisticas' => '4. Estadísticas',
        'monitoreo_analisis' => '5. Análisis IA',
        'monitoreo_firma' => '6. Firma Final'
    ];

    foreach ($etapas as $key => $nombre) {
        if (isset($data[$key])) {
            echo "✓ {$nombre}\n";

            // Verificar imágenes según la etapa
            switch ($key) {
                case 'monitoreo_datos':
                    if (isset($data[$key]['service_photos']) && count($data[$key]['service_photos']) > 0) {
                        echo "  📸 Fotos del servicio: " . count($data[$key]['service_photos']) . "\n";
                        foreach ($data[$key]['service_photos'] as $index => $photo) {
                            echo "    " . ($index + 1) . ". {$photo}\n";
                            verificarArchivo($photo);
                        }
                    } else {
                        echo "  ⚠ Sin fotos del servicio\n";
                    }
                    break;

                case 'monitoreo_croquis':
                    if (isset($data[$key]['croquis_file'])) {
                        echo "  📐 Croquis: {$data[$key]['croquis_file']}\n";
                        verificarArchivo($data[$key]['croquis_file']);
                    } else {
                        echo "  ⚠ Sin archivo de croquis\n";
                    }

                    if (isset($data[$key]['croquis_notes'])) {
                        $notesLength = strlen($data[$key]['croquis_notes']);
                        echo "  📝 Notas: {$notesLength} caracteres\n";
                    }
                    break;

                case 'monitoreo_completo':
                    // Verificar fotos de cebaderas
                    if (isset($data[$key]['bait_stations']) && count($data[$key]['bait_stations']) > 0) {
                        $totalStations = count($data[$key]['bait_stations']);
                        echo "  🎯 Cebaderas monitoreadas: {$totalStations}\n";

                        $photosCount = 0;
                        foreach ($data[$key]['bait_stations'] as $station) {
                            if (isset($station['photos']) && count($station['photos']) > 0) {
                                $photosCount += count($station['photos']);
                            }
                        }

                        if ($photosCount > 0) {
                            echo "  📸 Total fotos de cebaderas: {$photosCount}\n";

                            // Mostrar detalle de cada cebadera con fotos
                            foreach ($data[$key]['bait_stations'] as $index => $station) {
                                if (isset($station['photos']) && count($station['photos']) > 0) {
                                    $code = $station['code'] ?? "Cebadera #" . ($index + 1);
                                    echo "    • {$code}: " . count($station['photos']) . " foto(s)\n";
                                    foreach ($station['photos'] as $photoIndex => $photo) {
                                        echo "      " . ($photoIndex + 1) . ". {$photo}\n";
                                        verificarArchivo($photo, '      ');
                                    }
                                }
                            }
                        } else {
                            echo "  ⚠ Sin fotos de cebaderas\n";
                        }
                    }

                    // Verificar fotos de trampas
                    if (isset($data[$key]['traps']) && count($data[$key]['traps']) > 0) {
                        $totalTraps = count($data[$key]['traps']);
                        echo "  🪤 Trampas monitoreadas: {$totalTraps}\n";

                        $photosCount = 0;
                        foreach ($data[$key]['traps'] as $trap) {
                            if (isset($trap['photos']) && count($trap['photos']) > 0) {
                                $photosCount += count($trap['photos']);
                            }
                        }

                        if ($photosCount > 0) {
                            echo "  📸 Total fotos de trampas: {$photosCount}\n";

                            foreach ($data[$key]['traps'] as $index => $trap) {
                                if (isset($trap['photos']) && count($trap['photos']) > 0) {
                                    $code = $trap['code'] ?? "Trampa #" . ($index + 1);
                                    echo "    • {$code}: " . count($trap['photos']) . " foto(s)\n";
                                    foreach ($trap['photos'] as $photoIndex => $photo) {
                                        echo "      " . ($photoIndex + 1) . ". {$photo}\n";
                                        verificarArchivo($photo, '      ');
                                    }
                                }
                            }
                        } else {
                            echo "  ⚠ Sin fotos de trampas\n";
                        }
                    }
                    break;

                case 'monitoreo_firma':
                    if (isset($data[$key]['technician_signature'])) {
                        $sigLength = strlen($data[$key]['technician_signature']);
                        if (strpos($data[$key]['technician_signature'], 'data:image') === 0) {
                            echo "  ✍️ Firma del técnico: Base64 ({$sigLength} bytes)\n";
                        } else {
                            echo "  ✍️ Firma del técnico: {$data[$key]['technician_signature']}\n";
                            verificarArchivo($data[$key]['technician_signature']);
                        }
                    } else {
                        echo "  ⚠ Sin firma del técnico\n";
                    }
                    break;
            }
        } else {
            echo "✗ {$nombre} - NO COMPLETADA\n";
        }
    }

    echo "\n";
}

echo "═══════════════════════════════════════════════════════════\n";
echo "RESUMEN DE DIRECTORIOS\n";
echo "═══════════════════════════════════════════════════════════\n";

$directories = [
    'Fotos de servicio' => storage_path('app/public/services/photos'),
    'Croquis' => storage_path('app/public/services/croquis'),
    'Fotos de cebaderas' => storage_path('app/public/services/bait-stations'),
    'Fotos de trampas' => storage_path('app/public/services/traps'),
    'Firmas' => storage_path('app/public/signatures'),
];

foreach ($directories as $label => $path) {
    echo "\n{$label}:\n";
    echo "  Ruta: {$path}\n";

    if (file_exists($path)) {
        $files = glob($path . '/*');
        $fileCount = count($files);
        echo "  ✓ Existe - {$fileCount} archivo(s)\n";

        if ($fileCount > 0 && $fileCount <= 5) {
            foreach ($files as $file) {
                $size = round(filesize($file) / 1024, 2);
                echo "    • " . basename($file) . " ({$size} KB)\n";
            }
        } elseif ($fileCount > 5) {
            echo "  (Mostrando primeros 5)\n";
            for ($i = 0; $i < 5; $i++) {
                $size = round(filesize($files[$i]) / 1024, 2);
                echo "    • " . basename($files[$i]) . " ({$size} KB)\n";
            }
        }
    } else {
        echo "  ✗ NO EXISTE\n";
    }
}

echo "\n=== FIN DEL DIAGNÓSTICO ===\n";

// Función auxiliar para verificar si un archivo existe físicamente
function verificarArchivo($ruta, $indent = '    ') {
    // Limpiar la ruta
    $cleanPath = $ruta;
    if (strpos($cleanPath, 'storage/') === 0) {
        $cleanPath = substr($cleanPath, 8);
    }

    $possiblePaths = [
        storage_path('app/public/' . $cleanPath),
        public_path('storage/' . $cleanPath),
        public_path($ruta),
    ];

    $found = false;
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            $size = round(filesize($path) / 1024, 2);
            echo "{$indent}  ✓ Archivo existe ({$size} KB)\n";
            $found = true;
            break;
        }
    }

    if (!$found) {
        echo "{$indent}  ✗ Archivo NO encontrado en disco\n";
    }
}
