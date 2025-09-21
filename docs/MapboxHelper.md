# MapboxHelper - Documentación

El `MapboxHelper` es una clase helper que facilita la generación de imágenes estáticas de mapas usando la API de Mapbox.

## Configuración

### 1. Obtener Access Token de Mapbox

1. Ve a [Mapbox Account](https://account.mapbox.com/access-tokens/)
2. Crea una cuenta o inicia sesión
3. Crea un nuevo access token
4. Copia el token y agrégalo a tu archivo `.env`:

```bash
MAPBOX_ACCESS_TOKEN=tu_access_token_aqui
```

### 2. Crear directorio de mapas

El helper creará automáticamente el directorio, pero puedes crearlo manualmente:

```bash
mkdir storage/app/maps
```

## Uso

### Importar la clase

```php
use App\Helpers\MapboxHelper;
```

### Métodos disponibles

#### 1. `generateMapboxImage()` - Generar imagen y obtener ruta absoluta

```php
// Uso básico
$path = MapboxHelper::generateMapboxImage(-33.4569, -70.6483);

// Uso con parámetros personalizados
$path = MapboxHelper::generateMapboxImage(
    lat: -33.4569,
    lng: -70.6483,
    width: 800,
    height: 600,
    zoom: 16,
    style: 'satellite-v9',
    markerColor: '00ff00'
);
```

#### 2. `generateMapboxImageRelative()` - Obtener ruta relativa

```php
$relativePath = MapboxHelper::generateMapboxImageRelative(-33.4569, -70.6483);
// Retorna: "maps/mapbox_-33.4569_-70.6483_1695252345_abc123.png"
```

#### 3. `generateMapboxImageUrl()` - Obtener URL pública

```php
$url = MapboxHelper::generateMapboxImageUrl(-33.4569, -70.6483);
// Retorna: "http://localhost/storage/maps/mapbox_-33.4569_-70.6483_1695252345_abc123.png"
```

#### 4. `cleanOldMapImages()` - Limpiar imágenes antiguas

```php
// Eliminar imágenes de más de 7 días (por defecto)
$deletedCount = MapboxHelper::cleanOldMapImages();

// Eliminar imágenes de más de 30 días
$deletedCount = MapboxHelper::cleanOldMapImages(30);
```

#### 5. `isConfigured()` - Verificar configuración

```php
if (MapboxHelper::isConfigured()) {
    // Mapbox está configurado correctamente
    $image = MapboxHelper::generateMapboxImage($lat, $lng);
} else {
    // Mostrar mensaje de error o usar alternativa
    echo "Mapbox no está configurado";
}
```

#### 6. `getAvailableStyles()` - Obtener estilos disponibles

```php
$styles = MapboxHelper::getAvailableStyles();
// Retorna array con estilos disponibles
foreach ($styles as $key => $name) {
    echo "$key: $name\n";
}
```

## Parámetros

### Estilos de mapa disponibles:
- `streets-v11` - Calles (por defecto)
- `outdoors-v11` - Exteriores
- `light-v10` - Claro
- `dark-v10` - Oscuro
- `satellite-v9` - Satélite
- `satellite-streets-v11` - Satélite con calles
- `navigation-day-v1` - Navegación día
- `navigation-night-v1` - Navegación noche

### Colores de marcador:
Usar códigos hexadecimales sin #:
- `ff0000` - Rojo (por defecto)
- `00ff00` - Verde
- `0000ff` - Azul
- `ffff00` - Amarillo
- `ff00ff` - Magenta
- `00ffff` - Cian

## Uso en Controladores

```php
<?php

namespace App\Http\Controllers;

use App\Helpers\MapboxHelper;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function generateServiceMap(Service $service)
    {
        if (!$service->latitude || !$service->longitude) {
            return response()->json(['error' => 'Service has no coordinates'], 400);
        }

        try {
            $mapImage = MapboxHelper::generateMapboxImageUrl(
                $service->latitude,
                $service->longitude,
                600,
                400,
                15,
                'streets-v11',
                'ff0000'
            );

            return response()->json(['map_url' => $mapImage]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
```

## Uso en Vistas Blade

```blade
@if(MapboxHelper::isConfigured() && $service->latitude && $service->longitude)
    @php
        $mapUrl = MapboxHelper::generateMapboxImageUrl(
            $service->latitude,
            $service->longitude,
            600,
            400
        );
    @endphp
    <img src="{{ $mapUrl }}" alt="Mapa del servicio" class="w-full rounded-lg">
@else
    <div class="bg-gray-200 p-4 rounded-lg text-center">
        <p>Mapa no disponible</p>
    </div>
@endif
```

## Manejo de Errores

El helper lanza excepciones que puedes capturar:

```php
try {
    $image = MapboxHelper::generateMapboxImage($lat, $lng);
    echo "Imagen generada: " . $image;
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

## Mantenimiento

### Limpiar imágenes antiguas automáticamente

Puedes crear un comando Artisan para limpiar imágenes antiguas:

```bash
php artisan make:command CleanOldMaps
```

Y usar el helper en el comando:

```php
public function handle()
{
    $deletedCount = MapboxHelper::cleanOldMapImages(7);
    $this->info("Se eliminaron {$deletedCount} imágenes antiguas.");
}
```

## Troubleshooting

### Error: "MAPBOX_ACCESS_TOKEN no está configurado"
- Verifica que el token esté en el archivo `.env`
- Ejecuta `php artisan config:clear` para limpiar la caché

### Error: "Error al descargar la imagen de Mapbox"
- Verifica tu conexión a internet
- Verifica que el access token sea válido
- Revisa los logs en `storage/logs/laravel.log`

### Error: "Latitud debe estar entre -90 y 90"
- Verifica que las coordenadas sean válidas
- Latitud: -90 a 90
- Longitud: -180 a 180
