# 🗺️ MapboxHelper - Documentación Técnica

## Descripción General

`MapboxHelper` es una clase helper desarrollada para la integración con la API de Mapbox en aplicaciones Laravel. Proporciona funcionalidades completas para generar mapas estáticos, gestionar imágenes de mapas y ofrecer herramientas de visualización geográfica.

## 📋 Tabla de Contenidos

- [Instalación y Configuración](#instalación-y-configuración)
- [Métodos Principales](#métodos-principales)
- [Ejemplos de Uso](#ejemplos-de-uso)
- [Configuración Avanzada](#configuración-avanzada)
- [Mantenimiento](#mantenimiento)
- [Solución de Problemas](#solución-de-problemas)
- [API Reference](#api-reference)

## 🚀 Instalación y Configuración

### 1. Requisitos Previos

- Laravel 11+
- PHP 8.1+
- Token de acceso de Mapbox
- Configuración de Storage en Laravel

### 2. Configuración del Token

```bash
# Agregar a .env
MAPBOX_ACCESS_TOKEN=pk.tu_token_aqui
```

### 3. Configuración Automática

```bash
# Usar comando Artisan para configuración automática
php artisan mapbox:setup tu_token_aqui --test

# O configurar manualmente
php artisan config:cache
```

### 4. Verificar Configuración

```php
use App\Helpers\MapboxHelper;

if (MapboxHelper::isConfigured()) {
    echo "✅ Mapbox configurado correctamente";
} else {
    echo "❌ Mapbox no configurado";
}
```

## 🔧 Métodos Principales

### `generateMapboxImage()`

Genera una imagen estática de mapa y la guarda localmente.

```php
public static function generateMapboxImage(
    float $lat,           // Latitud (-90 a 90)
    float $lng,           // Longitud (-180 a 180)
    int $width = 600,     // Ancho en píxeles (máx. 1280)
    int $height = 400,    // Alto en píxeles (máx. 1280)
    int $zoom = 15,       // Nivel de zoom (0-22)
    string $style = 'streets-v11',  // Estilo del mapa
    string $markerColor = 'ff0000'  // Color del marcador (hex sin #)
): ?string
```

**Retorna:** URL pública del archivo de imagen generado o `null` si falla.

### `generateMapboxImageUrl()`

Genera la URL directa de Mapbox sin descargar la imagen.

```php
public static function generateMapboxImageUrl(
    float $lat,
    float $lng,
    int $width = 600,
    int $height = 400,
    int $zoom = 15,
    string $style = 'streets-v11',
    string $markerColor = 'ff0000'
): ?string
```

**Retorna:** URL directa de la imagen en Mapbox o `null` si falla.

### `cleanOldMapImages()`

Elimina imágenes de mapas antiguos para mantener el almacenamiento.

```php
public static function cleanOldMapImages(int $daysOld = 7): int
```

**Retorna:** Número de archivos eliminados.

## 💻 Ejemplos de Uso

### Uso Básico

```php
use App\Helpers\MapboxHelper;

// Generar mapa básico
$mapUrl = MapboxHelper::generateMapboxImage(40.4168, -3.7038, 600, 400);

if ($mapUrl) {
    echo "<img src='{$mapUrl}' alt='Mapa de Madrid' />";
}
```

### Uso Avanzado

```php
// Mapa satelital con alta resolución
$mapUrl = MapboxHelper::generateMapboxImage(
    lat: 40.4168,
    lng: -3.7038,
    width: 1200,
    height: 800,
    zoom: 18,
    style: 'satellite-v9',
    markerColor: '00ff00'  // Verde
);
```

### En un Modelo

```php
// app/Models/Service.php
class Service extends Model
{
    public function generateMapImage(int $width = 600, int $height = 400, int $zoom = 15): ?string
    {
        if (!$this->hasCoordinates()) {
            return null;
        }

        return MapboxHelper::generateMapboxImage(
            $this->latitude,
            $this->longitude,
            $width,
            $height,
            $zoom
        );
    }

    public function hasCoordinates(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }
}
```

### En una Vista Blade

```php
<!-- resources/views/service-pdf.blade.php -->
@if($service->hasCoordinates())
    @php
        $mapUrl = $service->generateMapImage(800, 600, 16);
    @endphp
    
    @if($mapUrl)
        <div class="map-section">
            <h3>📍 Ubicación del Servicio</h3>
            <img src="{{ $mapUrl }}" alt="Mapa del servicio" style="width: 100%; height: auto;" />
            <p>Coordenadas: {{ $service->latitude }}, {{ $service->longitude }}</p>
        </div>
    @endif
@endif
```

### En un Controlador

```php
use App\Helpers\MapboxHelper;

class ServiceController extends Controller
{
    public function generateMap(Service $service)
    {
        if (!$service->hasCoordinates()) {
            return response()->json(['error' => 'Sin coordenadas'], 400);
        }

        try {
            $mapUrl = MapboxHelper::generateMapboxImage(
                $service->latitude,
                $service->longitude,
                800,
                600,
                15
            );

            return response()->json([
                'success' => true,
                'map_url' => $mapUrl,
                'coordinates' => "{$service->latitude}, {$service->longitude}"
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
```

## ⚙️ Configuración Avanzada

### Estilos de Mapa Disponibles

```php
$styles = MapboxHelper::getAvailableStyles();

/*
Retorna:
[
    'streets-v11' => 'Calles (por defecto)',
    'outdoors-v11' => 'Aire libre',
    'light-v10' => 'Claro',
    'dark-v10' => 'Oscuro',
    'satellite-v9' => 'Satélite',
    'satellite-streets-v11' => 'Satélite con calles',
    'navigation-day-v1' => 'Navegación día',
    'navigation-night-v1' => 'Navegación noche'
]
*/
```

### Colores de Marcadores

Los colores se especifican en formato hexadecimal **sin el símbolo #**:

```php
// Colores comunes
'ff0000'  // Rojo
'00ff00'  // Verde
'0000ff'  // Azul
'ffff00'  // Amarillo
'ff00ff'  // Magenta
'00ffff'  // Cian
'000000'  // Negro
'ffffff'  // Blanco
```

### Validación de Coordenadas

```php
if (MapboxHelper::validateCoordinates($lat, $lng)) {
    $mapUrl = MapboxHelper::generateMapboxImage($lat, $lng);
} else {
    echo "Coordenadas inválidas";
}
```

## 🧹 Mantenimiento

### Comando Artisan para Limpieza

```bash
# Limpiar mapas de más de 7 días (por defecto)
php artisan maps:clean

# Limpiar mapas de más de 30 días
php artisan maps:clean --days=30

# Limpiar mapas de más de 1 día (para testing)
php artisan maps:clean --days=1
```

### Información de Almacenamiento

```php
$info = MapboxHelper::getStorageInfo();

echo "Archivos: {$info['file_count']}\n";
echo "Tamaño total: {$info['total_size_mb']} MB\n";
echo "Directorio: {$info['directory']}\n";
```

### Programar Limpieza Automática

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // Limpiar mapas antiguos diariamente a las 2:00 AM
    $schedule->command('maps:clean --days=7')
             ->dailyAt('02:00')
             ->description('Limpiar imágenes de mapas antiguos');
}
```

## 🔍 Solución de Problemas

### Token No Configurado

```
Error: MAPBOX_ACCESS_TOKEN no está configurado en el archivo .env
```

**Solución:**
1. Agregar `MAPBOX_ACCESS_TOKEN=tu_token` en `.env`
2. Ejecutar `php artisan config:cache`
3. Verificar con `MapboxHelper::isConfigured()`

### Coordenadas Inválidas

```
Error: Latitud debe estar entre -90 y 90
Error: Longitud debe estar entre -180 y 180
```

**Solución:**
```php
// Validar antes de usar
if (MapboxHelper::validateCoordinates($lat, $lng)) {
    // Proceder con la generación
}
```

### Errores de Almacenamiento

```
Error: Error al guardar la imagen en el almacenamiento
```

**Verificar:**
1. Permisos de directorio `storage/app/maps/`
2. Espacio disponible en disco
3. Configuración de Storage en Laravel

### API de Mapbox No Responde

```
Error: Error al descargar la imagen de Mapbox
```

**Verificar:**
1. Conexión a Internet
2. Token de Mapbox válido
3. Límites de la API no excedidos

## 📖 API Reference

### Métodos Públicos

| Método | Parámetros | Retorno | Descripción |
|--------|------------|---------|-------------|
| `generateMapboxImage()` | lat, lng, width, height, zoom, style, markerColor | ?string | Genera y guarda imagen de mapa |
| `generateMapboxImageUrl()` | lat, lng, width, height, zoom, style, markerColor | ?string | Genera URL directa de Mapbox |
| `cleanOldMapImages()` | daysOld | int | Elimina imágenes antiguas |
| `isConfigured()` | - | bool | Verifica configuración |
| `validateCoordinates()` | lat, lng | bool | Valida coordenadas |
| `getAvailableStyles()` | - | array | Lista estilos disponibles |
| `getStorageInfo()` | - | array | Información de almacenamiento |

### Constantes y Límites

```php
// Límites de coordenadas
Latitud: -90.0 a 90.0
Longitud: -180.0 a 180.0

// Límites de imagen
Ancho/Alto: 1 a 1280 píxeles
Zoom: 0 a 22

// Formatos soportados
Imagen: PNG (por defecto)
Token: Debe comenzar con 'pk.'
```

### Excepciones

| Exception | Causa | Solución |
|-----------|-------|----------|
| `MAPBOX_ACCESS_TOKEN no está configurado` | Token faltante | Configurar en .env |
| `Latitud debe estar entre -90 y 90` | Coordenada inválida | Validar coordenadas |
| `Longitud debe estar entre -180 y 180` | Coordenada inválida | Validar coordenadas |
| `Error al descargar la imagen de Mapbox` | Problema de API | Verificar token y conexión |
| `Error al guardar la imagen` | Problema de storage | Verificar permisos |

## 🏗️ Arquitectura

### Dependencias

```php
use Illuminate\Support\Facades\Storage;  // Gestión de archivos
use Illuminate\Support\Facades\Log;      // Logging
use Illuminate\Support\Facades\Http;     // Cliente HTTP
use Exception;                           // Manejo de errores
```

### Flujo de Trabajo

1. **Validación** → Verificar token y coordenadas
2. **Generación URL** → Construir URL de API Mapbox
3. **Descarga** → Obtener imagen de Mapbox
4. **Almacenamiento** → Guardar en storage/app/maps/
5. **Retorno** → URL pública del archivo

### Estructura de Archivos

```
storage/app/maps/
├── mapbox_40.4168_-3.7038_1695312345_64f1a2b3c4d5e.png
├── mapbox_41.3851_2.1734_1695312456_64f1a2b3c4d5f.png
└── ...
```

### Logging

Todos los errores se registran en `storage/logs/laravel.log` con contexto completo:

```php
Log::error('Error generando imagen de Mapbox: Token inválido', [
    'lat' => 40.4168,
    'lng' => -3.7038,
    'url' => 'https://api.mapbox.com/...'
]);
```

---

## 📝 Changelog

### v1.0.0 (2025-09-21)
- ✅ Implementación inicial completa
- ✅ Generación de mapas estáticos
- ✅ Gestión de almacenamiento
- ✅ Comandos Artisan
- ✅ Documentación completa
- ✅ Tests unitarios
- ✅ Sistema de logging
- ✅ Validación de coordenadas
- ✅ Múltiples estilos de mapa
- ✅ Marcadores personalizables

---

## 🤝 Contribución

Para contribuir al desarrollo del MapboxHelper:

1. Fork del repositorio
2. Crear branch de feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit de cambios (`git commit -am 'Agregar nueva funcionalidad'`)
4. Push al branch (`git push origin feature/nueva-funcionalidad`)
5. Crear Pull Request

---

## 📄 Licencia

Este código es parte del Sistema de Gestión de Servicios y está disponible bajo los términos de la licencia del proyecto.

---

**Documentación generada el:** 21 de septiembre de 2025  
**Versión:** 1.0.0  
**Autor:** Sistema de Gestión de Servicios
