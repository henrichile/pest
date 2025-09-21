# 🚀 MapboxHelper - Guía de Inicio Rápido

## ⚡ Configuración en 3 Pasos

### 1. Configurar Token
```bash
# Agregar a tu archivo .env
MAPBOX_ACCESS_TOKEN=pk.tu_token_de_mapbox_aqui

# Limpiar cache de configuración
php artisan config:cache
```

### 2. Verificar Configuración
```php
use App\Helpers\MapboxHelper;

if (MapboxHelper::isConfigured()) {
    echo "✅ ¡Listo para usar!";
}
```

### 3. Generar Tu Primer Mapa
```php
// Coordenadas de Madrid, España
$mapUrl = MapboxHelper::generateMapboxImage(40.4168, -3.7038, 600, 400);

if ($mapUrl) {
    echo "<img src='{$mapUrl}' alt='Mi primer mapa' />";
}
```

## 🎯 Casos de Uso Comunes

### En PDFs
```php
// En tu vista de PDF
@if($service->hasCoordinates())
    @php
        $mapUrl = $service->generateMapImage(800, 600);
    @endphp
    <img src="{{ $mapUrl }}" style="width: 100%;" />
@endif
```

### En Controladores
```php
public function showMap(Service $service)
{
    $mapUrl = MapboxHelper::generateMapboxImage(
        $service->latitude,
        $service->longitude,
        800,
        600,
        15
    );
    
    return view('map.show', compact('mapUrl'));
}
```

### Diferentes Estilos
```php
// Mapa de calles (por defecto)
$streetMap = MapboxHelper::generateMapboxImage($lat, $lng, 600, 400, 15, 'streets-v11');

// Mapa satelital
$satelliteMap = MapboxHelper::generateMapboxImage($lat, $lng, 600, 400, 15, 'satellite-v9');

// Mapa oscuro
$darkMap = MapboxHelper::generateMapboxImage($lat, $lng, 600, 400, 15, 'dark-v10');
```

## 🎨 Personalización

### Colores de Marcadores
```php
$redMarker = MapboxHelper::generateMapboxImage($lat, $lng, 600, 400, 15, 'streets-v11', 'ff0000');
$blueMarker = MapboxHelper::generateMapboxImage($lat, $lng, 600, 400, 15, 'streets-v11', '0066ff');
$greenMarker = MapboxHelper::generateMapboxImage($lat, $lng, 600, 400, 15, 'streets-v11', '00ff00');
```

### Diferentes Tamaños
```php
// Thumbnail pequeño
$thumb = MapboxHelper::generateMapboxImage($lat, $lng, 200, 150);

// Imagen mediana
$medium = MapboxHelper::generateMapboxImage($lat, $lng, 600, 400);

// Alta resolución
$hd = MapboxHelper::generateMapboxImage($lat, $lng, 1200, 800);
```

## 🧹 Mantenimiento

### Limpiar Mapas Antiguos
```bash
# Limpiar mapas de más de 7 días
php artisan maps:clean

# Limpiar mapas de más de 30 días
php artisan maps:clean --days=30
```

### Verificar Espacio Usado
```php
$info = MapboxHelper::getStorageInfo();
echo "Tienes {$info['file_count']} mapas usando {$info['total_size_mb']} MB";
```

## ❗ Solución Rápida de Problemas

| Problema | Solución |
|----------|----------|
| "Token no configurado" | Agregar `MAPBOX_ACCESS_TOKEN` a `.env` |
| "Coordenadas inválidas" | Verificar lat: -90 a 90, lng: -180 a 180 |
| "Error al guardar" | Verificar permisos de `storage/app/maps/` |
| Imagen no se muestra | Verificar `php artisan storage:link` |

## 📞 Comandos Útiles

```bash
# Configuración automática
php artisan mapbox:setup tu_token --test

# Limpiar cache
php artisan config:cache

# Crear enlace simbólico de storage
php artisan storage:link

# Ver logs de errores
tail -f storage/logs/laravel.log | grep Mapbox
```

## 🎯 Ejemplos Avanzados

### Mapa con Zoom Alto para Detalles
```php
$detailMap = MapboxHelper::generateMapboxImage(
    lat: 40.4168,
    lng: -3.7038,
    width: 800,
    height: 600,
    zoom: 18,  // Zoom muy alto
    style: 'satellite-streets-v11'
);
```

### Mapa de Área Amplia
```php
$areaMap = MapboxHelper::generateMapboxImage(
    lat: 40.4168,
    lng: -3.7038,
    width: 1000,
    height: 600,
    zoom: 10,  // Zoom bajo para área amplia
    style: 'outdoors-v11'
);
```

¡Eso es todo! Con estos ejemplos ya puedes empezar a usar MapboxHelper en tu aplicación. 🚀
