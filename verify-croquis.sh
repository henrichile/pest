#!/bin/bash

# Script para verificar el croquis

echo "=========================================="
echo "VERIFICACIÓN DE CROQUIS"
echo "=========================================="
echo ""

echo "1. Verificando directorio de croquis..."
if [ -d "storage/app/public/services/croquis" ]; then
    echo "✓ Directorio existe"
    count=$(find storage/app/public/services/croquis -type f 2>/dev/null | wc -l)
    echo "  Archivos encontrados: $count"
    echo ""
    echo "  Últimos archivos:"
    find storage/app/public/services/croquis -type f -printf "%T@ %Tc %p (%s bytes)\n" 2>/dev/null | sort -n | tail -5 | cut -d' ' -f2-
else
    echo "✗ Directorio no existe"
    mkdir -p storage/app/public/services/croquis
    echo "  → Creado"
fi

echo ""
echo "2. Verificando permisos..."
ls -ld storage/app/public/services/croquis

echo ""
echo "3. Verificando enlace simbólico..."
if [ -L "public/storage" ]; then
    echo "✓ public/storage → $(readlink public/storage)"
    echo ""
    echo "  Verificando acceso a croquis desde public:"
    if [ -d "public/storage/services/croquis" ]; then
        echo "  ✓ public/storage/services/croquis accesible"
    else
        echo "  ✗ public/storage/services/croquis NO accesible"
    fi
else
    echo "✗ public/storage no existe"
fi

echo ""
echo "4. Buscando referencias a croquis en logs..."
grep -E "croquis|Croquis" storage/logs/laravel.log 2>/dev/null | tail -10

echo ""
echo "=========================================="
echo "FIN DE LA VERIFICACIÓN"
echo "=========================================="
