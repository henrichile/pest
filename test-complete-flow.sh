#!/bin/bash

# Script de prueba completa del flujo de imágenes

echo "=========================================="
echo "PRUEBA COMPLETA - FLUJO DE IMÁGENES"
echo "=========================================="
echo ""

# 1. Verificar estructura de directorios
echo "1. Verificando estructura de directorios..."
echo ""

dirs=(
    "storage/app/public/services/bait-stations"
    "storage/app/public/services/traps"
    "storage/app/public/services/photos"
    "storage/app/public/services/croquis"
)

for dir in "${dirs[@]}"; do
    if [ -d "$dir" ]; then
        count=$(find "$dir" -type f 2>/dev/null | wc -l)
        echo "✓ $dir ($count archivos)"
    else
        echo "✗ $dir (no existe)"
        mkdir -p "$dir"
        echo "  → Creado"
    fi
done

echo ""
echo "2. Verificando enlace simbólico..."
if [ -L "public/storage" ]; then
    echo "✓ public/storage → $(readlink public/storage)"
else
    echo "✗ public/storage no existe"
fi

echo ""
echo "3. Verificando permisos..."
ls -ld storage/app/public
ls -ld storage/app/public/services 2>/dev/null || echo "  (directorio services no existe)"

echo ""
echo "4. Últimas imágenes guardadas:"
echo ""
echo "Cebaderas:"
find storage/app/public/services/bait-stations -type f -printf "%T@ %Tc %p\n" 2>/dev/null | sort -n | tail -5 | cut -d' ' -f2-

echo ""
echo "Trampas:"
find storage/app/public/services/traps -type f -printf "%T@ %Tc %p\n" 2>/dev/null | sort -n | tail -5 | cut -d' ' -f2-

echo ""
echo "5. Verificando logs de procesamiento de imágenes:"
echo ""
grep -E "Processing bait station photos|Bait station photo saved|PDF - Processing bait station photo" storage/logs/laravel.log 2>/dev/null | tail -10

echo ""
echo "=========================================="
echo "FIN DE LA PRUEBA"
echo "=========================================="
