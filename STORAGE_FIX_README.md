# Problema: Archivos de Croquis No Se Guardan

## Causa Raíz
El symlink `public/storage` no existe, lo que impide que los archivos guardados en `storage/app/public/` sean accesibles desde la web.

## Solución

### Opción 1: Crear el Symlink (Recomendado)
Ejecuta uno de estos comandos desde la raíz del proyecto:

```bash
# Con el script proporcionado
sudo ./fix_storage_symlink.sh

# O manualmente
sudo ln -s $(pwd)/storage/app/public $(pwd)/public/storage
```

### Opción 2: Usar php artisan (Alternativa)
```bash
# Si tienes permisos
php artisan storage:link

# Con sudo si es necesario
sudo php artisan storage:link
```

### Verificar que Funciona
```bash
# Debe mostrar el symlink
ls -la public/ | grep storage

# Debe mostrar: storage -> ../storage/app/public
```

## Estado Actual

✅ **Correcciones Aplicadas:**
1. Directorio `storage/app/public/services/croquis/` creado
2. Permisos 775 establecidos en storage/
3. Código mejorado para crear directorios automáticamente
4. Validación de archivos mejorada
5. Logging detallado agregado

❌ **Pendiente:**
1. Crear symlink `public/storage` → Requiere permisos sudo

## Después de Crear el Symlink

Prueba subir un archivo de croquis nuevamente. Los logs en `storage/logs/laravel.log` mostrarán:

```
[timestamp] Processing croquis data
[timestamp] Croquis file saved successfully
    - filename: [nombre].png
    - disk_path: /media/kike/Linux/pest/storage/app/public/services/croquis/[nombre].png
    - file_size: [tamaño]
```

## Monitorear Logs
```bash
tail -f storage/logs/laravel.log | grep -i croquis
```
