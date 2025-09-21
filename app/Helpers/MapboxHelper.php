<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Exception;

/**
 * MapboxHelper - Clase helper para integración con Mapbox API
 * 
 * Esta clase proporciona métodos para generar mapas estáticos usando la API de Mapbox,
 * gestionar imágenes de mapas locales y proporcionar funcionalidades relacionadas con
 * la visualización geográfica en la aplicación.
 * 
 * @package App\Helpers
 * @author Sistema de Gestión de Servicios
 * @version 1.0.0
 * @since 2025-09-21
 * 
 * @example
 * // Uso básico para generar mapa
 * $mapUrl = MapboxHelper::generateMapboxImage(40.4168, -3.7038, 800, 600);
 * 
 * // Uso con parámetros personalizados
 * $mapUrl = MapboxHelper::generateMapboxImage(
 *     latitude: 40.4168,
 *     longitude: -3.7038,
 *     width: 1200,
 *     height: 800,
 *     zoom: 15,
 *     style: 'satellite-v9',
 *     markerColor: 'blue'
 * );
 * 
 * @see https://docs.mapbox.com/api/maps/static-images/
 */
class MapboxHelper
{
    /**
     * Generar imagen estática de mapa usando Mapbox API
     * 
     * Este método principal genera una imagen estática de mapa usando la API de Mapbox
     * y la guarda localmente para uso posterior. Es la función principal para crear
     * mapas que se usarán en PDFs, reportes y vistas.
     *
     * @param float $lat Latitud del punto central del mapa (-90 a 90)
     * @param float $lng Longitud del punto central del mapa (-180 a 180)
     * @param int $width Ancho de la imagen en píxeles (máx. 1280, por defecto 600)
     * @param int $height Alto de la imagen en píxeles (máx. 1280, por defecto 400)
     * @param int $zoom Nivel de zoom del mapa (0-22, por defecto 15)
     * @param string $style Estilo del mapa Mapbox (por defecto 'streets-v11')
     * @param string $markerColor Color del marcador en hex sin # (por defecto 'ff0000' - rojo)
     * 
     * @return string|null URL pública del archivo de imagen generado, null si falla
     * 
     * @throws Exception Si el token de Mapbox no está configurado
     * @throws Exception Si las coordenadas están fuera del rango válido
     * @throws Exception Si hay errores al descargar o guardar la imagen
     * 
     * @example
     * // Generar mapa básico
     * $url = MapboxHelper::generateMapboxImage(40.4168, -3.7038, 600, 400);
     * 
     * // Mapa satelital con zoom alto
     * $url = MapboxHelper::generateMapboxImage(
     *     lat: 40.4168, 
     *     lng: -3.7038, 
     *     width: 800, 
     *     height: 600, 
     *     zoom: 18, 
     *     style: 'satellite-v9'
     * );
     */
    public static function generateMapboxImage(
        float $lat, 
        float $lng, 
        int $width = 600, 
        int $height = 400, 
        int $zoom = 15, 
        string $style = 'streets-v11',
        string $markerColor = 'ff0000'
    ): ?string {
        
        // Validar configuración
        $accessToken = config('services.mapbox.access_token') ?: env('MAPBOX_ACCESS_TOKEN');
        if (!$accessToken) {
            throw new Exception('MAPBOX_ACCESS_TOKEN no está configurado en el archivo .env');
        }

        // Validar coordenadas
        if ($lat < -90 || $lat > 90) {
            throw new Exception('Latitud debe estar entre -90 y 90');
        }
        if ($lng < -180 || $lng > 180) {
            throw new Exception('Longitud debe estar entre -180 y 180');
        }

        try {
            // Generar URL de la imagen
            $url = self::generateMapboxImageUrl($lat, $lng, $width, $height, $zoom, $style, $markerColor);
            
            if (!$url) {
                return null;
            }

            // Crear directorio si no existe
            if (!Storage::disk('public')->exists('maps')) {
                Storage::disk('public')->makeDirectory('maps');
            }

            // Descargar imagen
            $response = Http::timeout(30)->get($url);
            
            if (!$response->successful()) {
                throw new Exception('Error al descargar la imagen de Mapbox: ' . $response->status());
            }

            // Generar nombre único para el archivo
            $filename = "mapbox_{$lat}_{$lng}_" . time() . '_' . uniqid() . '.png';
            
            // Guardar imagen usando Storage público
            if (!Storage::disk('public')->put("maps/{$filename}", $response->body())) {
                throw new Exception('Error al guardar la imagen en el almacenamiento');
            }

            // Retornar URL pública usando asset()
            return asset("storage/maps/{$filename}");

        } catch (Exception $e) {
            // Log del error para debugging
            Log::error('Error generando imagen de Mapbox: ' . $e->getMessage(), [
                'lat' => $lat,
                'lng' => $lng,
                'url' => $url ?? 'URL no generada'
            ]);
            
            throw $e;
        }
    }

    /**
     * Generar URL de imagen de mapa estático sin descargar
     * 
     * Este método genera la URL directa de la API de Mapbox para una imagen estática
     * sin descargarla al servidor. Útil para mostrar mapas directamente desde Mapbox
     * o para verificar URLs antes de descargar.
     *
     * @param float $lat Latitud del punto central del mapa (-90 a 90)
     * @param float $lng Longitud del punto central del mapa (-180 a 180)
     * @param int $width Ancho de la imagen en píxeles (máx. 1280, por defecto 600)
     * @param int $height Alto de la imagen en píxeles (máx. 1280, por defecto 400)
     * @param int $zoom Nivel de zoom del mapa (0-22, por defecto 15)
     * @param string $style Estilo del mapa Mapbox (por defecto 'streets-v11')
     * @param string $markerColor Color del marcador en hex sin # (por defecto 'ff0000' - rojo)
     * 
     * @return string|null URL directa de la imagen en Mapbox, null si falla
     * 
     * @throws Exception Si el token de Mapbox no está configurado
     * @throws Exception Si las coordenadas están fuera del rango válido
     * 
     * @example
     * // Generar URL para uso directo en HTML
     * $url = MapboxHelper::generateMapboxImageUrl(40.4168, -3.7038, 400, 300);
     * echo "<img src='{$url}' alt='Mapa' />";
     * 
     * @see generateMapboxImage() Para descargar la imagen al servidor
     */
    public static function generateMapboxImageUrl(
        float $lat, 
        float $lng, 
        int $width = 600, 
        int $height = 400, 
        int $zoom = 15, 
        string $style = 'streets-v11',
        string $markerColor = 'ff0000'
    ): ?string {
        
        // Validar configuración
        $accessToken = config('services.mapbox.access_token') ?: env('MAPBOX_ACCESS_TOKEN');
        if (!$accessToken) {
            return null;
        }

        try {
            // Construir URL de la API de Mapbox
            $baseUrl = 'https://api.mapbox.com/styles/v1/mapbox';
            $marker = "pin-s-circle+{$markerColor}({$lng},{$lat})";
            
            return "{$baseUrl}/{$style}/static/{$marker}/{$lng},{$lat},{$zoom}/{$width}x{$height}@2x?access_token={$accessToken}";
            
        } catch (Exception $e) {
            Log::error('Error generando URL de Mapbox: ' . $e->getMessage(), [
                'lat' => $lat,
                'lng' => $lng
            ]);
            return null;
        }
    }

    /**
     * Limpiar imágenes de mapas antiguos del almacenamiento
     * 
     * Este método elimina imágenes de mapas que tengan más de la cantidad especificada
     * de días. Útil para mantener el espacio de almacenamiento controlado.
     *
     * @param int $daysOld Número de días de antigüedad para considerar archivos como antiguos (por defecto 7)
     * 
     * @return int Número de archivos eliminados
     * 
     * @example
     * // Limpiar mapas de más de 7 días
     * $deleted = MapboxHelper::cleanOldMapImages();
     * 
     * // Limpiar mapas de más de 30 días
     * $deleted = MapboxHelper::cleanOldMapImages(30);
     */
    public static function cleanOldMapImages(int $daysOld = 7): int
    {
        try {
            $deletedCount = 0;
            $cutoffTime = time() - ($daysOld * 24 * 60 * 60);
            
            // Obtener todos los archivos del directorio maps público
            $files = Storage::disk('public')->files('maps');
            
            foreach ($files as $file) {
                // Verificar que sea un archivo de mapa
                if (preg_match('/mapbox_.*\.png$/', basename($file))) {
                    $fileTime = Storage::disk('public')->lastModified($file);
                    
                    if ($fileTime && $fileTime < $cutoffTime) {
                        Storage::disk('public')->delete($file);
                        $deletedCount++;
                    }
                }
            }
            
            Log::info("Limpieza de mapas completada: {$deletedCount} archivos eliminados");
            return $deletedCount;
            
        } catch (Exception $e) {
            Log::error('Error limpiando imágenes de mapas: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Verificar si Mapbox está configurado correctamente
     * 
     * Comprueba si el token de acceso de Mapbox está disponible y configurado.
     *
     * @return bool True si está configurado, false en caso contrario
     * 
     * @example
     * if (MapboxHelper::isConfigured()) {
     *     $url = MapboxHelper::generateMapboxImage($lat, $lng);
     * } else {
     *     echo "Mapbox no está configurado";
     * }
     */
    public static function isConfigured(): bool
    {
        $accessToken = config('services.mapbox.access_token') ?: env('MAPBOX_ACCESS_TOKEN');
        return !empty($accessToken);
    }

    /**
     * Validar coordenadas geográficas
     * 
     * Verifica que las coordenadas estén dentro de los rangos válidos.
     *
     * @param float $lat Latitud a validar
     * @param float $lng Longitud a validar
     * 
     * @return bool True si las coordenadas son válidas, false en caso contrario
     * 
     * @example
     * if (MapboxHelper::validateCoordinates($lat, $lng)) {
     *     $url = MapboxHelper::generateMapboxImage($lat, $lng);
     * }
     */
    public static function validateCoordinates(float $lat, float $lng): bool
    {
        return ($lat >= -90 && $lat <= 90) && ($lng >= -180 && $lng <= 180);
    }

    /**
     * Obtener estilos de mapa disponibles en Mapbox
     * 
     * Retorna una lista de los estilos de mapa más comunes disponibles en Mapbox.
     *
     * @return array Array asociativo con clave => descripción de estilos disponibles
     * 
     * @example
     * $styles = MapboxHelper::getAvailableStyles();
     * foreach ($styles as $key => $description) {
     *     echo "<option value='{$key}'>{$description}</option>";
     * }
     */
    public static function getAvailableStyles(): array
    {
        return [
            'streets-v11' => 'Calles (por defecto)',
            'outdoors-v11' => 'Aire libre',
            'light-v10' => 'Claro',
            'dark-v10' => 'Oscuro',
            'satellite-v9' => 'Satélite',
            'satellite-streets-v11' => 'Satélite con calles',
            'navigation-day-v1' => 'Navegación día',
            'navigation-night-v1' => 'Navegación noche'
        ];
    }

    /**
     * Obtener información del uso de almacenamiento de mapas
     * 
     * Retorna estadísticas sobre el almacenamiento utilizado por las imágenes de mapas.
     *
     * @return array Array con información del almacenamiento
     * 
     * @example
     * $info = MapboxHelper::getStorageInfo();
     * echo "Archivos: {$info['file_count']}, Tamaño: {$info['total_size_mb']}MB";
     */
    public static function getStorageInfo(): array
    {
        try {
            $files = Storage::disk('public')->files('maps');
            $totalSize = 0;
            $fileCount = 0;
            
            foreach ($files as $file) {
                if (preg_match('/mapbox_.*\.png$/', basename($file))) {
                    $totalSize += Storage::disk('public')->size($file);
                    $fileCount++;
                }
            }
            
            return [
                'file_count' => $fileCount,
                'total_size_bytes' => $totalSize,
                'total_size_mb' => round($totalSize / 1024 / 1024, 2),
                'directory' => 'storage/app/public/maps'
            ];
            
        } catch (Exception $e) {
            Log::error('Error obteniendo información de almacenamiento: ' . $e->getMessage());
            return [
                'file_count' => 0,
                'total_size_bytes' => 0,
                'total_size_mb' => 0,
                'directory' => 'storage/app/public/maps',
                'error' => $e->getMessage()
            ];
        }
    }
}
