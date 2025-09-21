<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\MapboxHelper;
use Illuminate\Support\Facades\Storage;

class SetupMapbox extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'mapbox:setup {token?} {--test}';

    /**
     * The description of the console command.
     */
    protected $description = 'Configurar Mapbox para la generación de mapas';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🗺️  Configurando Mapbox Helper...');

        // Obtener token
        $token = $this->argument('token');
        
        if (!$token) {
            $token = $this->ask('Ingresa tu token de Mapbox:');
        }

        if (!$token) {
            $this->error('❌ Token de Mapbox requerido');
            return Command::FAILURE;
        }

        // Validar formato básico del token
        if (!str_starts_with($token, 'pk.')) {
            $this->error('❌ El token debe comenzar con "pk."');
            return Command::FAILURE;
        }

        // Actualizar archivo .env
        $this->updateEnvFile($token);

        // Crear directorio de mapas si no existe
        if (!Storage::exists('maps')) {
            Storage::makeDirectory('maps');
            $this->info('✅ Directorio de mapas creado');
        }

        // Verificar configuración
        if ($this->option('test')) {
            $this->testConfiguration();
        }

        $this->info('✅ Mapbox configurado correctamente');
        $this->line('');
        $this->line('📋 Próximos pasos:');
        $this->line('   1. Ejecuta: php artisan config:cache');
        $this->line('   2. Prueba generar un mapa con coordenadas válidas');
        $this->line('   3. Programa limpieza automática: php artisan maps:clean');

        return Command::SUCCESS;
    }

    /**
     * Actualizar archivo .env
     */
    private function updateEnvFile(string $token): void
    {
        $envPath = base_path('.env');
        
        if (!file_exists($envPath)) {
            $this->error('❌ Archivo .env no encontrado');
            return;
        }

        $envContent = file_get_contents($envPath);
        
        // Verificar si ya existe MAPBOX_ACCESS_TOKEN
        if (str_contains($envContent, 'MAPBOX_ACCESS_TOKEN=')) {
            $envContent = preg_replace(
                '/MAPBOX_ACCESS_TOKEN=.*/',
                "MAPBOX_ACCESS_TOKEN={$token}",
                $envContent
            );
            $this->info('✅ Token de Mapbox actualizado en .env');
        } else {
            $envContent .= "\n# Mapbox Configuration\n";
            $envContent .= "MAPBOX_ACCESS_TOKEN={$token}\n";
            $this->info('✅ Token de Mapbox agregado a .env');
        }

        file_put_contents($envPath, $envContent);
    }

    /**
     * Probar configuración de Mapbox
     */
    private function testConfiguration(): void
    {
        $this->info('🧪 Probando configuración...');

        try {
            // Coordenadas de prueba (Madrid, España)
            $testLat = 40.4168;
            $testLng = -3.7038;

            $this->line("   📍 Coordenadas de prueba: {$testLat}, {$testLng}");

            // Generar URL de prueba
            $url = MapboxHelper::generateMapboxImageUrl($testLat, $testLng, 400, 300, 12);
            
            if ($url) {
                $this->info('✅ URL de mapa generada correctamente');
                $this->line("   🔗 URL: {$url}");
                
                // Verificar que la API responde
                $headers = @get_headers($url);
                if ($headers && str_contains($headers[0], '200')) {
                    $this->info('✅ API de Mapbox responde correctamente');
                } else {
                    $this->warn('⚠️  No se pudo verificar la respuesta de la API');
                }
            } else {
                $this->error('❌ No se pudo generar URL de mapa');
            }

        } catch (\Exception $e) {
            $this->error('❌ Error al probar configuración: ' . $e->getMessage());
        }
    }
}
