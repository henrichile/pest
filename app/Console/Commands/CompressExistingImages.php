<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\ImageHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CompressExistingImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:compress-existing {--dry-run : Show what would be compressed without actually doing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compress existing images in the observations directory to reduce file sizes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        $this->info('🔍 Buscando imágenes existentes para comprimir...');

        // Buscar todas las rutas de imágenes en la base de datos
        $imagePaths = DB::table('services')
            ->whereNotNull('checklist_data')
            ->pluck('checklist_data')
            ->map(function ($data) {
                $decoded = json_decode($data, true);
                if (!$decoded) return [];

                $paths = [];

                // Buscar en observations
                if (isset($decoded['observations']) && is_array($decoded['observations'])) {
                    foreach ($decoded['observations'] as $observation) {
                        if (isset($observation['photo'])) {
                            $paths[] = $observation['photo'];
                        }
                    }
                }

                return $paths;
            })
            ->flatten()
            ->filter()
            ->unique()
            ->toArray();

        if (empty($imagePaths)) {
            $this->info('✅ No se encontraron imágenes para comprimir.');
            return 0;
        }

        $this->info("📸 Encontradas " . count($imagePaths) . " imágenes para procesar:");
        $this->table(['Ruta de la imagen'], collect($imagePaths)->map(fn($path) => [$path])->toArray());

        if ($dryRun) {
            $this->info('🔄 Modo dry-run: No se realizarán cambios reales.');
            return 0;
        }

        $successCount = 0;
        $errorCount = 0;

        $progressBar = $this->output->createProgressBar(count($imagePaths));
        $progressBar->start();

        foreach ($imagePaths as $imagePath) {
            try {
                // Verificar que la imagen existe
                $fullPath = storage_path('app/public/' . $imagePath);
                if (!file_exists($fullPath)) {
                    $this->warn("Imagen no encontrada: {$imagePath}");
                    $progressBar->advance();
                    continue;
                }

                // Obtener tamaño original
                $originalSize = filesize($fullPath);
                $originalSizeMB = round($originalSize / 1024 / 1024, 2);

                // Comprimir la imagen
                $compressedPath = ImageHelper::recompressExistingImage($imagePath);

                if ($compressedPath) {
                    $newSize = filesize(storage_path('app/public/' . $compressedPath));
                    $newSizeMB = round($newSize / 1024 / 1024, 2);
                    $savedMB = round(($originalSize - $newSize) / 1024 / 1024, 2);

                    $this->line("✅ Comprimida: {$imagePath} ({$originalSizeMB}MB → {$newSizeMB}MB, ahorrados: {$savedMB}MB)");
                    $successCount++;
                } else {
                    $this->warn("❌ No se pudo comprimir: {$imagePath}");
                    $errorCount++;
                }

            } catch (\Exception $e) {
                $this->error("❌ Error procesando {$imagePath}: " . $e->getMessage());
                $errorCount++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("📊 Resumen:");
        $this->table(
            ['Estado', 'Cantidad'],
            [
                ['✅ Comprimidas exitosamente', $successCount],
                ['❌ Errores', $errorCount],
                ['📈 Total procesadas', $successCount + $errorCount]
            ]
        );

        if ($errorCount > 0) {
            $this->warn("⚠️  {$errorCount} imágenes no pudieron ser comprimidas. Revisa los logs para más detalles.");
            return 1;
        }

        $this->info('🎉 ¡Compresión de imágenes completada exitosamente!');
        return 0;
    }

    private function count($array)
    {
        return count($array);
    }
}
