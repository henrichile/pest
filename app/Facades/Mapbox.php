<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade para el MapboxHelper
 * 
 * @method static string|null generateMapboxImage(float $lat, float $lng, int $width = 600, int $height = 400, int $zoom = 15, string $style = 'streets-v11', string $markerColor = 'ff0000')
 * @method static string|null generateMapboxImageRelative(float $lat, float $lng, int $width = 600, int $height = 400, int $zoom = 15, string $style = 'streets-v11', string $markerColor = 'ff0000')
 * @method static string|null generateMapboxImageUrl(float $lat, float $lng, int $width = 600, int $height = 400, int $zoom = 15, string $style = 'streets-v11', string $markerColor = 'ff0000')
 * @method static int cleanOldMapImages(int $daysOld = 7)
 * @method static bool isConfigured()
 * @method static array getAvailableStyles()
 */
class Mapbox extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'mapbox';
    }
}
