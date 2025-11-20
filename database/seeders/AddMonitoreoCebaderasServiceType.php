<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceType;

class AddMonitoreoCebaderasServiceType extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si ya existe
        $existing = ServiceType::where('slug', 'monitoreo-cebaderas')
            ->orWhere('name', 'LIKE', '%Monitoreo%')
            ->orWhere('name', 'LIKE', '%Cebadera%')
            ->first();

        if (!$existing) {
            ServiceType::create([
                'name' => 'Monitoreo de Cebaderas',
                'slug' => 'monitoreo-cebaderas',
                'description' => 'Servicio de monitoreo y control de cebaderas para roedores con checklist de 6 etapas: Datos del Servicio, Croquis de Cebaderas, Monitoreo Completo, Estadísticas, Análisis IA y Firma Final.',
                'is_active' => true,
            ]);
            
            $this->command->info('Tipo de servicio "Monitoreo de Cebaderas" creado exitosamente.');
        } else {
            $this->command->info('El tipo de servicio ya existe: ' . $existing->name);
            
            // Actualizar si existe pero tiene un slug diferente
            if ($existing->slug !== 'monitoreo-cebaderas') {
                $existing->update([
                    'slug' => 'monitoreo-cebaderas',
                    'is_active' => true,
                ]);
                $this->command->info('Slug actualizado a "monitoreo-cebaderas".');
            }
        }
    }
}

