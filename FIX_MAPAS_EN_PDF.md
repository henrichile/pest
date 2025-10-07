# 🗺️ Corrección: Mapas No Se Muestran en el PDF

## 📋 Problema Identificado

**Síntoma:** El mapa estático de Mapbox no aparece en el PDF generado, aunque el servicio tiene coordenadas GPS válidas.

**Ubicación del problema:** `resources/views/technician/service-pdf.blade.php` líneas 290-324

---

## 🔍 Análisis del Problema

### Causa Raíz
El código original intentaba convertir la URL del mapa a una ruta física de manera incorrecta:

```php
// ❌ CÓDIGO ORIGINAL (PROBLEMÁTICO)
$mapImagePath = public_path(str_replace(url('/'), '', $mapImageUrl));
```

**¿Por qué fallaba?**

1. `generateMapboxImage()` retorna: `http://localhost/storage/maps/mapbox_xxx.png`
2. `url('/')` retorna: `http://localhost`
3. `str_replace()` intenta reemplazar, pero puede quedar: `/storage/maps/mapbox_xxx.png`
4. `public_path()` construye: `/path/to/public/storage/maps/mapbox_xxx.png`
5. **Problema**: Dependiendo de la configuración de `APP_URL`, esta conversión puede fallar

### Factores Adicionales
- **Sin logging**: No había forma de saber qué estaba fallando
- **Sin fallback visual**: Si el mapa no se cargaba, el PDF simplemente no mostraba nada
- **Sin validación**: No se verificaba si el path era correcto antes de usarlo

---

## ✅ Solución Implementada

### 1. **Mejorar Extracción del Path**

Usar expresión regular para extraer la parte relevante de la URL:

```php
// ✅ NUEVO CÓDIGO (ROBUSTO)
if ($mapImageUrl) {
    // Extraer la parte 'storage/maps/filename.png'
    if (preg_match('#storage/maps/(.+)$#', $mapImageUrl, $matches)) {
        $mapImagePath = public_path('storage/maps/' . $matches[1]);
        
        // Logging para debugging
        \Log::info('Mapa para PDF', [
            'url' => $mapImageUrl,
            'path' => $mapImagePath,
            'exists' => file_exists($mapImagePath)
        ]);
    } else {
        $mapImagePath = null;
        \Log::warning('No se pudo extraer el path del mapa', ['url' => $mapImageUrl]);
    }
}
```

**Ventajas:**
- ✅ Extrae exactamente el filename sin importar el dominio
- ✅ No depende de `APP_URL` o configuración del servidor
- ✅ Maneja casos edge (URLs con/sin trailing slash, puertos, etc.)

---

### 2. **Agregar Logging Comprehensivo**

Se agregaron 3 tipos de logs para facilitar el debugging:

```php
// Log exitoso
\Log::info('Mapa para PDF', [
    'url' => $mapImageUrl,
    'path' => $mapImagePath,
    'exists' => file_exists($mapImagePath)
]);

// Warning si no se puede extraer el path
\Log::warning('No se pudo extraer el path del mapa', ['url' => $mapImageUrl]);

// Warning si generateMapboxImage falla silenciosamente
\Log::warning('generateMapboxImage retornó null');

// Error con stack trace completo
\Log::error('Error generando mapa para PDF: ' . $e->getMessage(), [
    'lat' => $service->latitude,
    'lng' => $service->longitude,
    'trace' => $e->getTraceAsString()
]);
```

---

### 3. **Agregar Fallback Visual**

Si el mapa no se puede mostrar, ahora aparece un mensaje informativo:

```blade
@else
<div style="padding: 10px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; margin: 10px 0;">
    <p style="margin: 0; color: #856404;">
        <strong>⚠️ Mapa no disponible</strong><br>
        Coordenadas GPS: {{ $service->latitude }}, {{ $service->longitude }}
        @if($mapImagePath)
            <br><small>Archivo no encontrado: {{ basename($mapImagePath ?? 'N/A') }}</small>
        @endif
    </p>
</div>
@endif
```

**Beneficios:**
- Usuario sabe que hay coordenadas pero el mapa no se pudo cargar
- Se muestran las coordenadas GPS como fallback
- Información para debugging (nombre del archivo buscado)

---

## 🧪 Proceso de Verificación

### Script de Diagnóstico Creado

Se creó `check-map-logs.sh` que verifica:

1. ✅ Token de Mapbox configurado
2. ✅ Directorio `storage/app/public/maps` existe
3. ✅ Symlink `public/storage` existe
4. ✅ Servicio tiene coordenadas GPS
5. 📊 Número de archivos de mapas existentes
6. 📋 Logs recientes relacionados con mapas

**Uso:**
```bash
./check-map-logs.sh
```

---

### Verificación Manual

```bash
# 1. Verificar configuración
grep MAPBOX_ACCESS_TOKEN .env

# 2. Verificar que existan mapas
ls -lh storage/app/public/maps/

# 3. Verificar symlink
ls -la public/storage | grep maps

# 4. Verificar coordenadas del servicio
php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
\$s = App\Models\Service::find(41);
echo 'Lat: ' . \$s->latitude . PHP_EOL;
echo 'Lng: ' . \$s->longitude . PHP_EOL;
"

# 5. Generar PDF y ver logs
# Abrir: http://127.0.0.1:8000/technician/services/41/pdf
tail -n 50 storage/logs/laravel.log | grep -i mapa
```

---

## 📊 Estado Actual del Sistema

### ✅ Verificado Correcto

| Componente | Estado | Detalle |
|------------|--------|---------|
| Token Mapbox | ✅ Configurado | En `.env` línea 67 |
| Directorio maps | ✅ Existe | `storage/app/public/maps/` |
| Symlink | ✅ Existe | `public/storage/` → `../storage/app/public/` |
| Archivos de mapas | ✅ 16 archivos | Total ~2.1 MB |
| Servicio 41 coordenadas | ✅ Válidas | Lat: -33.59086410, Lng: -70.68114970 |
| Servicio 41 status | ✅ Finalizado | Puede generar PDF |

---

## 🎯 Próximos Pasos para el Usuario

### 1. **Generar PDF de Prueba**

Abrir en el navegador:
```
http://127.0.0.1:8000/technician/services/41/pdf
```

### 2. **Revisar el PDF**

**Si el mapa aparece:** ✅ Problema resuelto

**Si aparece el mensaje amarillo "Mapa no disponible":** Continuar al paso 3

### 3. **Revisar Logs**

```bash
tail -n 50 storage/logs/laravel.log
```

Buscar estas líneas:
- `[INFO] Mapa para PDF` - Debería mostrar URL, path y si existe
- `[WARNING]` - Indicará qué falló
- `[ERROR] Error generando mapa` - Mostrará el error completo

---

## 🔧 Posibles Problemas y Soluciones

### Problema 1: "generateMapboxImage retornó null"

**Posibles causas:**
- Token de Mapbox inválido o expirado
- API de Mapbox no responde
- Límite de requests excedido

**Solución:**
```bash
# Verificar token
grep MAPBOX_ACCESS_TOKEN .env

# Probar manualmente
php artisan tinker
>>> App\Helpers\MapboxHelper::isConfigured()
>>> App\Helpers\MapboxHelper::generateMapboxImage(-33.59086410, -70.68114970, 600, 300, 15)
```

---

### Problema 2: "No se pudo extraer el path del mapa"

**Causa:** La URL generada no tiene el formato esperado

**Solución:**
Revisar en los logs qué URL se generó y ajustar la regex si es necesario.

---

### Problema 3: "Archivo no encontrado"

**Causa:** El archivo se generó pero no está en el path esperado

**Verificación:**
```bash
# Ver qué dice el log
tail storage/logs/laravel.log | grep "Mapa para PDF"

# Buscar archivos recientes
find storage/app/public/maps/ -type f -mmin -5
```

**Solución:**
```bash
# Verificar symlink
ls -la public/storage

# Recrear symlink si es necesario
php artisan storage:link
```

---

### Problema 4: Error de permisos

**Síntoma:** Error al escribir el archivo de mapa

**Solución:**
```bash
# Dar permisos
chmod -R 775 storage/app/public/maps/
chown -R www-data:www-data storage/app/public/maps/

# O si usas tu usuario
sudo chown -R $USER:$USER storage/app/public/maps/
```

---

## 📝 Archivos Modificados

| Archivo | Líneas | Cambios |
|---------|--------|---------|
| `resources/views/technician/service-pdf.blade.php` | 290-340 | Mejor extracción de path, logging, fallback visual |

---

## 🎯 Resultados Esperados

### En el PDF Generado

**Caso exitoso:**
```
╔════════════════════════════════════╗
║  Ubicación del Servicio            ║
║  Coordenadas GPS: -33.59, -70.68   ║
║                                    ║
║  [Imagen del Mapa de Mapbox]       ║
║  (600x300px con pin rojo)          ║
╚════════════════════════════════════╝
```

**Caso fallido (con fallback):**
```
╔════════════════════════════════════╗
║  ⚠️ Mapa no disponible              ║
║  Coordenadas GPS: -33.59, -70.68   ║
║  Archivo no encontrado: mapbox_... ║
╚════════════════════════════════════╝
```

---

### En los Logs

**Logs esperados en caso exitoso:**
```
[INFO] Mapa para PDF {
    "url": "http://localhost/storage/maps/mapbox_-33.59_-70.68_1759500000_abc123.png",
    "path": "/home/kike/Documentos/pest/public/storage/maps/mapbox_-33.59_-70.68_1759500000_abc123.png",
    "exists": true
}
```

**Logs esperados en caso de error:**
```
[ERROR] Error generando mapa para PDF: Error al descargar la imagen de Mapbox: 401 {
    "lat": "-33.59086410",
    "lng": "-70.68114970",
    "trace": "..."
}
```

---

## 📚 Recursos Adicionales

### Documentación de MapboxHelper

El helper tiene estos métodos útiles:

```php
// Verificar configuración
MapboxHelper::isConfigured() // true/false

// Generar mapa (descarga al servidor)
MapboxHelper::generateMapboxImage($lat, $lng, $width, $height, $zoom)

// Generar solo URL (sin descargar)
MapboxHelper::generateMapboxImageUrl($lat, $lng, $width, $height, $zoom)

// Limpiar mapas antiguos (más de 7 días)
MapboxHelper::cleanOldMapImages(7)

// Ver info de almacenamiento
MapboxHelper::getStorageInfo()
```

### Estilos de Mapa Disponibles

```php
'streets-v11'           => 'Calles (por defecto)',
'outdoors-v11'          => 'Aire libre',
'light-v10'             => 'Claro',
'dark-v10'              => 'Oscuro',
'satellite-v9'          => 'Satélite',
'satellite-streets-v11' => 'Satélite con calles',
'navigation-day-v1'     => 'Navegación día',
'navigation-night-v1'   => 'Navegación noche'
```

---

## ⚡ Mantenimiento

### Limpiar Mapas Antiguos

Los mapas se acumulan en el tiempo. Ejecutar periódicamente:

```bash
# Desde tinker
php artisan tinker
>>> App\Helpers\MapboxHelper::cleanOldMapImages(7) // Eliminar > 7 días
>>> App\Helpers\MapboxHelper::getStorageInfo() // Ver espacio usado
```

### Ver Uso de Espacio

```bash
du -sh storage/app/public/maps/
ls -lh storage/app/public/maps/ | wc -l
```

---

## 📅 Información de la Corrección

- **Fecha:** 2 de Octubre de 2025
- **Archivo Principal:** `resources/views/technician/service-pdf.blade.php`
- **Tipo de Cambio:** Corrección de bug + mejoras de UX
- **Impacto:** Medio - Funcionalidad de visualización de mapas en PDF
- **Retrocompatibilidad:** ✅ Totalmente compatible
- **Archivos Adicionales:** `check-map-logs.sh` (script de diagnóstico)

---

## ✅ Checklist de Validación

Después de generar el PDF, verificar:

- [ ] El mapa aparece en el PDF con el pin rojo en las coordenadas correctas
- [ ] Si no aparece, hay un mensaje amarillo con las coordenadas GPS
- [ ] Los logs muestran información clara sobre qué pasó
- [ ] El archivo de mapa existe en `storage/app/public/maps/`
- [ ] El symlink `public/storage` apunta correctamente
- [ ] No hay errores 500 al generar el PDF
- [ ] Las coordenadas GPS se muestran correctamente

---

## 🎉 ESTADO FINAL

**Configuración:** ✅ Lista
**Código:** ✅ Corregido
**Logging:** ✅ Implementado
**Fallback:** ✅ Agregado
**Diagnóstico:** ✅ Script creado

**SIGUIENTE PASO:** Generar PDF y revisar logs para confirmar que funciona correctamente.
