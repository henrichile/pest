#!/bin/bash

# Script para probar la carga de imágenes en el flujo de monitoreo de cebaderas

echo "=== Test de carga de imágenes - Monitoreo de Cebaderas ==="
echo ""

# Verificar que exista el directorio de storage
echo "1. Verificando directorios de storage..."
if [ -d "storage/app/public/services/bait-stations" ]; then
    echo "✓ Directorio de cebaderas existe"
    ls -lh storage/app/public/services/bait-stations/ | tail -5
else
    echo "✗ Directorio de cebaderas NO existe"
    echo "  Creando directorio..."
    mkdir -p storage/app/public/services/bait-stations
fi

echo ""

if [ -d "storage/app/public/services/traps" ]; then
    echo "✓ Directorio de trampas existe"
    ls -lh storage/app/public/services/traps/ | tail -5
else
    echo "✗ Directorio de trampas NO existe"
    echo "  Creando directorio..."
    mkdir -p storage/app/public/services/traps
fi

echo ""
echo "2. Verificando enlace simbólico de storage..."
if [ -L "public/storage" ]; then
    echo "✓ Enlace simbólico existe"
    ls -l public/storage
else
    echo "✗ Enlace simbólico NO existe"
    echo "  Ejecutando: php artisan storage:link"
    php artisan storage:link
fi

echo ""
echo "3. Verificando permisos..."
echo "Permisos de storage/app/public:"
ls -ld storage/app/public

echo ""
echo "4. Últimas 20 líneas del log relacionadas con fotos:"
grep -i "photo\|bait_station\|trap" storage/logs/laravel.log | tail -20

echo ""
echo "=== Fin del test ==="
