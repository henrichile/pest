<?php
// Ejemplo de uso del MapboxHelper en un controlador

namespace App\Http\Controllers;

use App\Models\Service;
use App\Helpers\MapboxHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MapController extends Controller
{
    /**
     * Generar imagen de mapa para un servicio
     */
    public function generateServiceMap(Service $service): JsonResponse
    {
        // Verificar que el servicio tenga coordenadas
        if (!$service->hasCoordinates()) {
            return response()->json([
                'error' => 'El servicio no tiene coordenadas disponibles'
            ], 400);
        }

        // Verificar que Mapbox esté configurado
        if (!MapboxHelper::isConfigured()) {
            return response()->json([
                'error' => 'Mapbox no está configurado correctamente'
            ], 500);
        }

        try {
            // Generar imagen de mapa
            $mapUrl = $service->generateMapImage(800, 600, 16, 'streets-v11');
            
            if (!$mapUrl) {
                return response()->json([
                    'error' => 'No se pudo generar la imagen del mapa'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'map_url' => $mapUrl,
                'coordinates' => $service->getCoordinatesString(),
                'service_id' => $service->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al generar el mapa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estilos disponibles de Mapbox
     */
    public function getMapStyles(): JsonResponse
    {
        $styles = MapboxHelper::getAvailableStyles();
        
        return response()->json([
            'styles' => $styles,
            'default' => 'streets-v11'
        ]);
    }

    /**
     * Limpiar imágenes de mapas antiguas
     */
    public function cleanOldMaps(Request $request): JsonResponse
    {
        $days = $request->input('days', 7);
        
        if ($days < 1) {
            return response()->json([
                'error' => 'El número de días debe ser mayor a 0'
            ], 400);
        }

        try {
            $deletedCount = MapboxHelper::cleanOldMapImages($days);
            
            return response()->json([
                'success' => true,
                'deleted_count' => $deletedCount,
                'message' => "Se eliminaron {$deletedCount} imágenes antiguas"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al limpiar imágenes: ' . $e->getMessage()
            ], 500);
        }
    }
}

// Ejemplo de rutas para el controlador (routes/web.php o routes/api.php)
/*
Route::middleware(['auth'])->group(function () {
    Route::get('/api/services/{service}/map', [MapController::class, 'generateServiceMap']);
    Route::get('/api/map/styles', [MapController::class, 'getMapStyles']);
    Route::post('/api/map/cleanup', [MapController::class, 'cleanOldMaps']);
});
*/
