<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Helpers\MapboxHelper;

class MapboxHelperTest extends TestCase
{
    public function test_is_configured_returns_false_when_no_token()
    {
        // Simular que no hay token
        $this->assertFalse(MapboxHelper::isConfigured());
    }

    public function test_get_available_styles_returns_array()
    {
        $styles = MapboxHelper::getAvailableStyles();
        
        $this->assertIsArray($styles);
        $this->assertArrayHasKey('streets-v11', $styles);
        $this->assertArrayHasKey('satellite-v9', $styles);
    }

    public function test_generate_mapbox_image_throws_exception_with_invalid_coordinates()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Latitud debe estar entre -90 y 90');
        
        MapboxHelper::generateMapboxImage(91, 0); // Latitud inválida
    }

    public function test_generate_mapbox_image_throws_exception_with_invalid_longitude()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Longitud debe estar entre -180 y 180');
        
        MapboxHelper::generateMapboxImage(0, 181); // Longitud inválida
    }

    public function test_generate_mapbox_image_throws_exception_without_token()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('MAPBOX_ACCESS_TOKEN no está configurado');
        
        // Esto fallará porque no hay token configurado en el test
        MapboxHelper::generateMapboxImage(-33.4569, -70.6483);
    }

    public function test_clean_old_map_images_returns_integer()
    {
        $result = MapboxHelper::cleanOldMapImages(30);
        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(0, $result);
    }
}
