# Pasos para Verificar la Carga de Imágenes

## 1. Limpiar caché de Laravel
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 2. Verificar permisos de storage
```bash
chmod -R 775 storage
chmod -R 775 public/storage
```

## 3. Verificar configuración de PHP
Asegúrate de que en tu `php.ini` o `.htaccess` tengas:
```
upload_max_filesize = 20M
post_max_size = 25M
max_execution_time = 300
```

## 4. Probar el flujo completo
1. Inicia sesión como técnico
2. Ve a un servicio de tipo "Monitoreo de Cebaderas"
3. Completa el flujo hasta la etapa "Monitoreo Completo"
4. Agrega una cebadera con fotos
5. Envía el formulario
6. Verifica los logs en `storage/logs/laravel.log`

## 5. Verificar en los logs
Busca líneas como:
```
[INFO] Processing bait station photos
[INFO] Bait station photo saved
```

## 6. Verificar archivos guardados
```bash
ls -lh storage/app/public/services/bait-stations/
ls -lh storage/app/public/services/traps/
```

## Problemas Comunes

### Si las imágenes no se cargan:
1. Verifica que el formulario tenga `enctype="multipart/form-data"`
2. Verifica los permisos de storage
3. Revisa los logs de Laravel
4. Verifica el tamaño de las imágenes vs límites de PHP

### Si las imágenes se guardan pero no se ven en el PDF:
1. Verifica que las rutas en la base de datos sean correctas
2. Verifica que el enlace simbólico exista: `ls -l public/storage`
3. Verifica que las imágenes existan físicamente en storage
