# 🗺️ MapboxHelper - Migración a Rutas Públicas

## 📝 Cambios Realizados (21/09/2025)

### ✅ **Problema Solucionado**
Los mapas generados por MapboxHelper se guardaban en `storage/app/maps/` (ubicación privada) y no eran accesibles para los PDFs generados por DomPDF.

### 🔧 **Modificaciones Implementadas**

#### 1. **MapboxHelper.php - Ubicación de Almacenamiento**

**Antes:**
```php
// Guardaba en ubicación privada
Storage::makeDirectory('maps');
Storage::put("maps/{$filename}", $response->body());
return Storage::url("maps/{$filename}");
```

**Después:**
```php
// Guarda en ubicación pública
Storage::disk('public')->makeDirectory('maps');
Storage::disk('public')->put("maps/{$filename}", $response->body());
return asset("storage/maps/{$filename}");
```

#### 2. **Métodos Actualizados**

- ✅ **`generateMapboxImage()`** - Ahora guarda en `storage/app/public/maps/`
- ✅ **`cleanOldMapImages()`** - Limpia archivos del disco público
- ✅ **`getStorageInfo()`** - Reporta estadísticas del disco público

#### 3. **Vista PDF Mejorada**

**Archivo:** `resources/views/technician/service-pdf.blade.php`

**Mejoras:**
- ✅ Conversión correcta de URL a ruta física para PDF
- ✅ Manejo de errores mejorado
- ✅ Verificación de existencia de archivo

### 🚀 **Resultado Final**

#### ✅ **Ubicaciones de Archivos**
```
storage/app/public/maps/          ← Archivos físicos
public/storage/maps/              ← Enlace simbólico (acceso web)
http://localhost/storage/maps/    ← URLs públicas
```

#### ✅ **Flujo de Trabajo**
1. **Generación**: MapboxHelper descarga y guarda mapa en ubicación pública
2. **URL Pública**: Retorna URL accesible: `http://localhost/storage/maps/archivo.png`
3. **PDF**: Convierte URL a ruta física: `/path/to/public/storage/maps/archivo.png`
4. **DomPDF**: Puede acceder al archivo físico para incrustar en PDF

### 🧪 **Pruebas Realizadas**

```bash
# ✅ Generación de mapas en ubicación pública
php artisan tinker --execute="MapboxHelper::generateMapboxImage(40.4168, -3.7038, 600, 400)"
# Resultado: http://localhost/storage/maps/mapbox_40.4168_-3.7038_timestamp.png

# ✅ Verificación de acceso público
ls -la public/storage/maps/
# Archivos visibles y accesibles

# ✅ Estadísticas actualizadas
php artisan tinker --execute="MapboxHelper::getStorageInfo()"
# Directorio: storage/app/public/maps

# ✅ Limpieza funcionando
php artisan maps:clean
# Funciona con la nueva ubicación
```

### 📋 **Comandos de Verificación**

```bash
# Verificar enlace simbólico
ls -la public/storage

# Crear directorio si no existe
mkdir -p storage/app/public/maps

# Verificar mapas generados
ls -la storage/app/public/maps/

# Acceso web directo
curl -I http://localhost/storage/maps/nombre_archivo.png
```

### 🎯 **Casos de Uso Ahora Funcionales**

#### **1. PDFs con Mapas**
```php
// En service-pdf.blade.php
@if($service->hasCoordinates())
    @php
        $mapUrl = MapboxHelper::generateMapboxImage($service->latitude, $service->longitude);
        $mapPath = public_path(str_replace(url('/'), '', $mapUrl));
    @endphp
    <img src="{{ $mapPath }}" alt="Mapa del servicio" />
@endif
```

#### **2. Vistas Web con Mapas**
```php
// En controladores
$mapUrl = MapboxHelper::generateMapboxImage($lat, $lng);
return view('service.show', compact('mapUrl'));

// En vistas Blade
<img src="{{ $mapUrl }}" alt="Mapa" />
```

#### **3. APIs con Mapas**
```php
// En API controllers
return response()->json([
    'map_url' => MapboxHelper::generateMapboxImage($lat, $lng),
    'coordinates' => "{$lat}, {$lng}"
]);
```

### 🔧 **Mantenimiento**

#### **Limpieza Automática**
```bash
# Limpiar mapas antiguos (ahora funciona con ubicación pública)
php artisan maps:clean --days=7
```

#### **Monitoreo de Espacio**
```php
$info = MapboxHelper::getStorageInfo();
echo "Mapas: {$info['file_count']}, Tamaño: {$info['total_size_mb']} MB";
```

### ✅ **Estado Final**

- ✅ **Mapas accesibles públicamente**
- ✅ **PDFs con mapas funcionando**
- ✅ **URLs públicas correctas**
- ✅ **Limpieza automática operativa**
- ✅ **Estadísticas actualizadas**
- ✅ **Documentación actualizada**

---

**¡MapboxHelper completamente operativo para PDFs!** 🚀🗺️📄
