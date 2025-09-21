# 📚 MapboxHelper - Ejemplos de API

## 🔧 Métodos Principales

### 1. generateMapboxImage()

#### Ejemplo Básico
```php
use App\Helpers\MapboxHelper;

// Mapa básico de Madrid
$mapUrl = MapboxHelper::generateMapboxImage(40.4168, -3.7038, 600, 400);
```

#### Ejemplo Completo
```php
$mapUrl = MapboxHelper::generateMapboxImage(
    lat: 40.4168,           // Latitud de Madrid
    lng: -3.7038,           // Longitud de Madrid
    width: 800,             // Ancho en píxeles
    height: 600,            // Alto en píxeles
    zoom: 15,               // Nivel de zoom
    style: 'satellite-v9',  // Estilo satelital
    markerColor: '00ff00'   // Marcador verde
);
```

#### Casos de Uso
```php
// Para PDF de servicio técnico
$servicePdf = MapboxHelper::generateMapboxImage($service->lat, $service->lng, 800, 600, 16);

// Para thumbnail en lista
$thumbnail = MapboxHelper::generateMapboxImage($service->lat, $service->lng, 150, 100, 12);

// Para vista detallada
$detailView = MapboxHelper::generateMapboxImage($service->lat, $service->lng, 1200, 800, 18);
```

---

### 2. generateMapboxImageUrl()

#### Uso Directo en HTML
```php
$mapboxUrl = MapboxHelper::generateMapboxImageUrl(40.4168, -3.7038, 400, 300);
echo "<img src='{$mapboxUrl}' alt='Mapa directo de Mapbox' />";
```

#### Para Verificación
```php
// Verificar URL antes de descargar
$url = MapboxHelper::generateMapboxImageUrl($lat, $lng, 600, 400);
if ($url && filter_var($url, FILTER_VALIDATE_URL)) {
    // Proceder a usar la URL
    $downloadedMap = MapboxHelper::generateMapboxImage($lat, $lng, 600, 400);
}
```

---

### 3. cleanOldMapImages()

#### Limpieza Básica
```php
// Limpiar mapas de más de 7 días (por defecto)
$deletedCount = MapboxHelper::cleanOldMapImages();
echo "Eliminados: {$deletedCount} archivos";
```

#### Limpieza Personalizada
```php
// Limpiar mapas de más de 30 días
$deletedCount = MapboxHelper::cleanOldMapImages(30);

// Limpieza agresiva (1 día)
$deletedCount = MapboxHelper::cleanOldMapImages(1);

// En un job programado
class CleanOldMapsJob implements ShouldQueue
{
    public function handle()
    {
        $deleted = MapboxHelper::cleanOldMapImages(14); // 2 semanas
        Log::info("Mapas limpiados: {$deleted}");
    }
}
```

---

## 🔍 Métodos de Utilidad

### 4. isConfigured()

```php
// Verificar antes de usar
if (MapboxHelper::isConfigured()) {
    $map = MapboxHelper::generateMapboxImage($lat, $lng);
} else {
    return response()->json(['error' => 'Mapbox no configurado'], 500);
}

// En middleware
class EnsureMapboxConfigured
{
    public function handle($request, Closure $next)
    {
        if (!MapboxHelper::isConfigured()) {
            abort(500, 'Servicio de mapas no disponible');
        }
        return $next($request);
    }
}
```

### 5. validateCoordinates()

```php
// Validación simple
if (MapboxHelper::validateCoordinates($lat, $lng)) {
    $map = MapboxHelper::generateMapboxImage($lat, $lng);
}

// En un FormRequest
class CreateServiceRequest extends FormRequest
{
    public function rules()
    {
        return [
            'latitude' => ['required', 'numeric', function ($attribute, $value, $fail) {
                if (!MapboxHelper::validateCoordinates($value, $this->longitude)) {
                    $fail('Las coordenadas no son válidas.');
                }
            }],
            'longitude' => 'required|numeric',
        ];
    }
}
```

### 6. getAvailableStyles()

```php
// Para select en formulario
$styles = MapboxHelper::getAvailableStyles();
?>
<select name="map_style">
    <?php foreach ($styles as $key => $description): ?>
        <option value="<?= $key ?>"><?= $description ?></option>
    <?php endforeach; ?>
</select>

// En API
Route::get('/api/map-styles', function () {
    return response()->json([
        'styles' => MapboxHelper::getAvailableStyles(),
        'default' => 'streets-v11'
    ]);
});
```

### 7. getStorageInfo()

```php
// Dashboard de administración
$info = MapboxHelper::getStorageInfo();
?>
<div class="storage-stats">
    <h3>📊 Estadísticas de Mapas</h3>
    <p>📁 Archivos: <?= $info['file_count'] ?></p>
    <p>💾 Tamaño: <?= $info['total_size_mb'] ?> MB</p>
    <p>📂 Directorio: <?= $info['directory'] ?></p>
</div>

// En comando Artisan
class MapStorageInfo extends Command
{
    protected $signature = 'maps:info';
    
    public function handle()
    {
        $info = MapboxHelper::getStorageInfo();
        $this->info("Archivos: {$info['file_count']}");
        $this->info("Tamaño: {$info['total_size_mb']} MB");
    }
}
```

---

## 🎨 Ejemplos por Estilo

### Mapas de Calles
```php
// Estilo clásico de calles
$streetMap = MapboxHelper::generateMapboxImage($lat, $lng, 600, 400, 15, 'streets-v11');

// Versión clara
$lightMap = MapboxHelper::generateMapboxImage($lat, $lng, 600, 400, 15, 'light-v10');

// Versión oscura
$darkMap = MapboxHelper::generateMapboxImage($lat, $lng, 600, 400, 15, 'dark-v10');
```

### Mapas Satelitales
```php
// Solo satélite
$satelliteMap = MapboxHelper::generateMapboxImage($lat, $lng, 600, 400, 15, 'satellite-v9');

// Satélite con calles
$hybridMap = MapboxHelper::generateMapboxImage($lat, $lng, 600, 400, 15, 'satellite-streets-v11');
```

### Mapas de Navegación
```php
// Navegación diurna
$navDay = MapboxHelper::generateMapboxImage($lat, $lng, 600, 400, 15, 'navigation-day-v1');

// Navegación nocturna
$navNight = MapboxHelper::generateMapboxImage($lat, $lng, 600, 400, 15, 'navigation-night-v1');
```

---

## 🎯 Ejemplos por Caso de Uso

### En Modelos Eloquent

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
            $zoom,
            'streets-v11',
            'ff0000' // Marcador rojo para servicios
        );
    }
    
    public function generateSatelliteMap(): ?string
    {
        return MapboxHelper::generateMapboxImage(
            $this->latitude,
            $this->longitude,
            800,
            600,
            16,
            'satellite-streets-v11'
        );
    }
}

// app/Models/Client.php
class Client extends Model
{
    public function getLocationMapUrl(): ?string
    {
        if (!$this->address_lat || !$this->address_lng) {
            return null;
        }

        return MapboxHelper::generateMapboxImage(
            $this->address_lat,
            $this->address_lng,
            400,
            300,
            14,
            'light-v10',
            '0066ff' // Marcador azul para clientes
        );
    }
}
```

### En Controladores

```php
// Controlador de Servicios
class ServiceController extends Controller
{
    public function map(Service $service)
    {
        if (!MapboxHelper::isConfigured()) {
            return redirect()->back()->with('error', 'Servicio de mapas no disponible');
        }

        if (!$service->hasCoordinates()) {
            return redirect()->back()->with('error', 'Servicio sin coordenadas');
        }

        $maps = [
            'street' => $service->generateMapImage(600, 400, 15),
            'satellite' => $service->generateSatelliteMap(),
            'overview' => MapboxHelper::generateMapboxImage(
                $service->latitude,
                $service->longitude,
                600,
                400,
                12,
                'outdoors-v11'
            )
        ];

        return view('services.map', compact('service', 'maps'));
    }
}

// API Controller
class MapApiController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'width' => 'integer|min:100|max:1280',
            'height' => 'integer|min:100|max:1280',
            'zoom' => 'integer|min:0|max:22',
            'style' => 'string|in:' . implode(',', array_keys(MapboxHelper::getAvailableStyles())),
            'color' => 'string|regex:/^[0-9a-fA-F]{6}$/'
        ]);

        try {
            $mapUrl = MapboxHelper::generateMapboxImage(
                $request->lat,
                $request->lng,
                $request->width ?? 600,
                $request->height ?? 400,
                $request->zoom ?? 15,
                $request->style ?? 'streets-v11',
                $request->color ?? 'ff0000'
            );

            return response()->json([
                'success' => true,
                'map_url' => $mapUrl,
                'coordinates' => "{$request->lat}, {$request->lng}"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
```

### En Jobs de Cola

```php
// Job para generar mapas en background
class GenerateServiceMapJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Service $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    public function handle()
    {
        if (!$this->service->hasCoordinates()) {
            Log::warning("Servicio {$this->service->id} sin coordenadas para mapa");
            return;
        }

        try {
            // Generar múltiples versiones del mapa
            $maps = [
                'thumbnail' => MapboxHelper::generateMapboxImage(
                    $this->service->latitude,
                    $this->service->longitude,
                    200,
                    150,
                    12
                ),
                'medium' => MapboxHelper::generateMapboxImage(
                    $this->service->latitude,
                    $this->service->longitude,
                    600,
                    400,
                    15
                ),
                'high_res' => MapboxHelper::generateMapboxImage(
                    $this->service->latitude,
                    $this->service->longitude,
                    1200,
                    800,
                    16
                )
            ];

            // Guardar URLs en la base de datos
            $this->service->update([
                'map_thumbnail_url' => $maps['thumbnail'],
                'map_medium_url' => $maps['medium'],
                'map_high_res_url' => $maps['high_res']
            ]);

            Log::info("Mapas generados para servicio {$this->service->id}");

        } catch (\Exception $e) {
            Log::error("Error generando mapas para servicio {$this->service->id}: " . $e->getMessage());
            throw $e;
        }
    }
}

// Dispatching del job
GenerateServiceMapJob::dispatch($service);
```

### En Vistas Blade

```php
<!-- Vista de lista de servicios -->
@foreach($services as $service)
    <div class="service-card">
        <h3>Servicio #{{ $service->id }}</h3>
        
        @if($service->hasCoordinates())
            @php
                $thumbMap = MapboxHelper::generateMapboxImage(
                    $service->latitude, 
                    $service->longitude, 
                    200, 
                    150, 
                    12
                );
            @endphp
            
            @if($thumbMap)
                <img src="{{ $thumbMap }}" alt="Ubicación" class="map-thumbnail" />
            @else
                <div class="map-placeholder">🗺️ Mapa no disponible</div>
            @endif
        @else
            <div class="map-placeholder">📍 Sin ubicación</div>
        @endif
        
        <p>{{ $service->description }}</p>
    </div>
@endforeach

<!-- Vista detallada de servicio -->
<div class="service-detail">
    @if($service->hasCoordinates())
        <div class="maps-section">
            <h3>📍 Ubicación del Servicio</h3>
            
            <!-- Pestañas de diferentes tipos de mapa -->
            <div class="map-tabs">
                <button onclick="showMap('street')" class="tab-button active">🗺️ Calles</button>
                <button onclick="showMap('satellite')" class="tab-button">🛰️ Satélite</button>
                <button onclick="showMap('hybrid')" class="tab-button">🔗 Híbrido</button>
            </div>
            
            <div id="map-street" class="map-container active">
                @php
                    $streetMap = MapboxHelper::generateMapboxImage(
                        $service->latitude, 
                        $service->longitude, 
                        800, 
                        600, 
                        15, 
                        'streets-v11'
                    );
                @endphp
                <img src="{{ $streetMap }}" alt="Mapa de calles" />
            </div>
            
            <div id="map-satellite" class="map-container">
                @php
                    $satelliteMap = MapboxHelper::generateMapboxImage(
                        $service->latitude, 
                        $service->longitude, 
                        800, 
                        600, 
                        15, 
                        'satellite-v9'
                    );
                @endphp
                <img src="{{ $satelliteMap }}" alt="Mapa satelital" />
            </div>
            
            <div id="map-hybrid" class="map-container">
                @php
                    $hybridMap = MapboxHelper::generateMapboxImage(
                        $service->latitude, 
                        $service->longitude, 
                        800, 
                        600, 
                        15, 
                        'satellite-streets-v11'
                    );
                @endphp
                <img src="{{ $hybridMap }}" alt="Mapa híbrido" />
            </div>
            
            <div class="coordinates">
                <strong>Coordenadas:</strong> {{ $service->getCoordinatesString() }}
            </div>
        </div>
    @endif
</div>
```

---

## 🛠️ Integración con Otros Componentes

### Con Sistema de Notificaciones

```php
// Notificación con mapa
class ServiceAssignedNotification extends Notification
{
    private Service $service;

    public function toMail($notifiable)
    {
        $mapUrl = null;
        if ($this->service->hasCoordinates()) {
            $mapUrl = MapboxHelper::generateMapboxImage(
                $this->service->latitude,
                $this->service->longitude,
                600,
                400,
                15
            );
        }

        return (new MailMessage)
            ->subject('Nuevo Servicio Asignado')
            ->line('Se te ha asignado un nuevo servicio.')
            ->when($mapUrl, function ($mail) use ($mapUrl) {
                return $mail->line("Ubicación: <img src='{$mapUrl}' style='width:100%;max-width:600px;' />");
            })
            ->action('Ver Servicio', route('services.show', $this->service));
    }
}
```

### Con Sistema de Reportes

```php
// Reporte con mapas
class ServiceReportController extends Controller
{
    public function generate(Request $request)
    {
        $services = Service::whereHas('coordinates')
                          ->whereBetween('created_at', [$request->from, $request->to])
                          ->get();

        $serviceData = [];
        foreach ($services as $service) {
            $serviceData[] = [
                'service' => $service,
                'map_url' => MapboxHelper::generateMapboxImage(
                    $service->latitude,
                    $service->longitude,
                    400,
                    300,
                    14
                )
            ];
        }

        $pdf = PDF::loadView('reports.services', compact('serviceData'));
        return $pdf->download('reporte-servicios.pdf');
    }
}
```

---

¡Con estos ejemplos tienes una guía completa para usar MapboxHelper en cualquier situación! 🚀
