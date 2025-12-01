#!/bin/bash

# Script de verificación completa de imágenes

echo "=========================================="
echo "VERIFICACIÓN COMPLETA DE IMÁGENES"
echo "=========================================="
echo ""

echo "1. VERIFICANDO DIRECTORIOS..."
echo ""

dirs=(
    "storage/app/public/services/croquis"
    "storage/app/public/services/bait-stations"
    "storage/app/public/services/traps"
    "storage/app/public/services/photos"
)

for dir in "${dirs[@]}"; do
    if [ -d "$dir" ]; then
        count=$(find "$dir" -type f 2>/dev/null | wc -l)
        size=$(du -sh "$dir" 2>/dev/null | cut -f1)
        echo "✓ $dir"
        echo "  Archivos: $count | Tamaño: $size"
        
        if [ $count -gt 0 ]; then
            echo "  Últimos 3 archivos:"
            find "$dir" -type f -printf "%T@ %Tc %p (%s bytes)\n" 2>/dev/null | sort -rn | head -3 | cut -d' ' -f2-
        fi
        echo ""
    else
        echo "✗ $dir (no existe)"
        echo ""
    fi
done

echo "2. VERIFICANDO ENLACE SIMBÓLICO..."
if [ -L "public/storage" ]; then
    target=$(readlink public/storage)
    echo "✓ public/storage → $target"
    
    if [ -d "public/storage/services" ]; then
        echo "  ✓ public/storage/services accesible"
    else
        echo "  ✗ public/storage/services NO accesible"
    fi
else
    echo "✗ public/storage no existe"
fi
echo ""

echo "3. VERIFICANDO PERMISOS..."
ls -ld storage/app/public/services 2>/dev/null
ls -ld public/storage 2>/dev/null
echo ""

echo "4. BUSCANDO EN LOGS (últimas 20 líneas)..."
echo ""
echo "=== Croquis ==="
grep -i "croquis" storage/logs/laravel.log 2>/dev/null | tail -10
echo ""
echo "=== Fotos de cebaderas ==="
grep -i "bait station photo" storage/logs/laravel.log 2>/dev/null | tail -5
echo ""
echo "=== PDF ==="
grep -i "PDF - Processing\|PDF - File found\|PDF - Image encoded" storage/logs/laravel.log 2>/dev/null | tail -10
echo ""

echo "5. VERIFICANDO DATOS EN BASE DE DATOS..."
echo "Ejecuta en tinker:"
echo "  php artisan tinker"
echo "  \$service = \\App\\Models\\Service::latest()->first();"
echo "  \$service->checklist_data['monitoreo_croquis']['croquis_file'];"
echo "  \$service->checklist_data['monitoreo_completo']['bait_stations'][0]['photos'];"
echo ""

echo "=========================================="
echo "FIN DE LA VERIFICACIÓN"
echo "=========================================="
