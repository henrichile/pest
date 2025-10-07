#!/bin/bash

echo "=========================================="
echo "🗺️  VERIFICACIÓN DE MAPAS EN PDF"
echo "=========================================="
echo ""

# Verificar configuración de Mapbox
echo "1. Verificando configuración de Mapbox..."
if grep -q "MAPBOX_ACCESS_TOKEN=" .env; then
    echo "   ✅ Token de Mapbox configurado"
else
    echo "   ❌ Token de Mapbox NO configurado"
fi
echo ""

# Verificar directorio de mapas
echo "2. Verificando directorio de mapas..."
if [ -d "storage/app/public/maps" ]; then
    MAP_COUNT=$(ls -1 storage/app/public/maps/*.png 2>/dev/null | wc -l)
    echo "   ✅ Directorio existe"
    echo "   📊 Archivos de mapas: $MAP_COUNT"
else
    echo "   ❌ Directorio NO existe"
fi
echo ""

# Verificar symlink
echo "3. Verificando symlink público..."
if [ -L "public/storage" ]; then
    echo "   ✅ Symlink existe"
else
    echo "   ❌ Symlink NO existe - ejecutar: php artisan storage:link"
fi
echo ""

# Verificar coordenadas del servicio 41
echo "4. Verificando servicio 41..."
php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
\$s = App\Models\Service::find(41);
if (\$s) {
    echo '   Lat: ' . \$s->latitude . PHP_EOL;
    echo '   Lng: ' . \$s->longitude . PHP_EOL;
    echo '   Status: ' . \$s->status . PHP_EOL;
    if (\$s->latitude && \$s->longitude) {
        echo '   ✅ Tiene coordenadas' . PHP_EOL;
    } else {
        echo '   ❌ NO tiene coordenadas' . PHP_EOL;
    }
} else {
    echo '   ❌ Servicio no encontrado' . PHP_EOL;
}
"
echo ""

echo "5. Instrucciones para verificar el mapa en el PDF:"
echo "   1. Genera el PDF del servicio 41 desde: http://127.0.0.1:8000/technician/services/41/pdf"
echo "   2. Luego ejecuta: tail -n 50 storage/logs/laravel.log"
echo "   3. Busca líneas que contengan 'Mapa para PDF' o 'Error generando mapa'"
echo ""

echo "=========================================="
echo "📋 LOGS RECIENTES (últimas 30 líneas):"
echo "=========================================="
if [ -f "storage/logs/laravel.log" ]; then
    tail -n 30 storage/logs/laravel.log | grep -E "(Mapa|mapa|Map|ERROR|WARNING)" || echo "   (No hay logs relacionados con mapas aún)"
else
    echo "   ⚠️  Archivo de log no existe"
fi
echo ""

echo "✅ Verificación completada"
