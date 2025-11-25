#!/bin/bash

# Script para configurar correctamente el almacenamiento de Laravel

echo "=== Configurando almacenamiento de Laravel ==="

# Crear directorios necesarios
echo "1. Creando directorios necesarios..."
mkdir -p storage/app/public/services/croquis
mkdir -p storage/app/public/services/photos
mkdir -p storage/app/public/media

# Establecer permisos
echo "2. Estableciendo permisos..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Verificar si existe el symlink
if [ -L "public/storage" ]; then
    echo "3. El symlink public/storage ya existe"
    ls -la public/storage
elif [ -d "public/storage" ]; then
    echo "3. ADVERTENCIA: public/storage existe pero NO es un symlink"
    echo "   Respaldando y eliminando..."
    mv public/storage public/storage.backup
    ln -s ../storage/app/public public/storage
    echo "   Symlink creado exitosamente"
else
    echo "3. Creando symlink..."
    ln -s ../storage/app/public public/storage
    echo "   Symlink creado exitosamente"
fi

# Verificar que funciona
echo ""
echo "4. Verificando configuración..."
echo "   - Directorio storage/app/public/services/croquis:"
ls -la storage/app/public/services/croquis/ 2>&1 | head -3

echo "   - Symlink public/storage:"
ls -la public/ | grep storage

echo ""
echo "=== Configuración completada ==="
echo ""
echo "IMPORTANTE: Si ves 'Permission denied' al crear el symlink, ejecuta:"
echo "  sudo ./fix_storage_symlink.sh"
echo ""
echo "O manualmente:"
echo "  sudo ln -s $(pwd)/storage/app/public $(pwd)/public/storage"
