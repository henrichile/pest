<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pest;

class PestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pests = [
            // Roedores
            [
                'name' => 'Rata Gris (Rattus norvegicus)',
                'category' => 'Roedores',
                'technical_notes' => 'También conocida como rata de alcantarilla. Especie más grande y agresiva.',
                'control_methods' => ['Cebos anticoagulantes', 'Trampas', 'Exclusión'],
                'is_active' => true,
            ],
            [
                'name' => 'Rata Negra (Rattus rattus)',
                'category' => 'Roedores',
                'technical_notes' => 'Rata de techo, excelente trepadora. Prefiere ambientes secos.',
                'control_methods' => ['Cebos anticoagulantes', 'Trampas adhesivas', 'Exclusión'],
                'is_active' => true,
            ],
            [
                'name' => 'Ratón Doméstico (Mus musculus)',
                'category' => 'Roedores',
                'technical_notes' => 'Especie más pequeña, alta capacidad reproductiva.',
                'control_methods' => ['Cebos anticoagulantes', 'Trampas múltiples', 'Exclusión'],
                'is_active' => true,
            ],

            // Cucarachas
            [
                'name' => 'Cucaracha Alemana (Blattella germanica)',
                'category' => 'Cucarachas',
                'technical_notes' => 'Especie más común en interiores. Portadora de enfermedades.',
                'control_methods' => ['Gel insecticida', 'Spray residual', 'Cebos'],
                'is_active' => true,
            ],
            [
                'name' => 'Cucaracha Americana (Periplaneta americana)',
                'category' => 'Cucarachas',
                'technical_notes' => 'Especie grande, común en alcantarillas y sótanos.',
                'control_methods' => ['Spray residual', 'Polvo insecticida', 'Exclusión'],
                'is_active' => true,
            ],
            [
                'name' => 'Cucaracha Oriental (Blatta orientalis)',
                'category' => 'Cucarachas',
                'technical_notes' => 'Prefiere ambientes húmedos y fríos.',
                'control_methods' => ['Spray residual', 'Polvo insecticida', 'Cebos'],
                'is_active' => true,
            ],

            // Moscas
            [
                'name' => 'Mosca Doméstica (Musca domestica)',
                'category' => 'Moscas',
                'technical_notes' => 'Vector de enfermedades, se reproduce en materia orgánica.',
                'control_methods' => ['Trampas de luz', 'Spray de contacto', 'Larvicidas'],
                'is_active' => true,
            ],
            [
                'name' => 'Mosca de la Fruta (Drosophila melanogaster)',
                'category' => 'Moscas',
                'technical_notes' => 'Pequeña, se reproduce en frutas y vegetales en descomposición.',
                'control_methods' => ['Trampas con cebo', 'Eliminación de fuentes', 'Spray'],
                'is_active' => true,
            ],

            // Termitas
            [
                'name' => 'Termita Subterránea (Reticulitermes flavipes)',
                'category' => 'Termitas',
                'technical_notes' => 'Especie más destructiva, forma colonias grandes.',
                'control_methods' => ['Cebos termiticidas', 'Barreras químicas', 'Tratamiento del suelo'],
                'is_active' => true,
            ],
            [
                'name' => 'Termita de Madera Seca (Cryptotermes brevis)',
                'category' => 'Termitas',
                'technical_notes' => 'Vive en madera seca, no requiere contacto con suelo.',
                'control_methods' => ['Fumigación', 'Inyección de insecticida', 'Reemplazo de madera'],
                'is_active' => true,
            ],

            // Hormigas
            [
                'name' => 'Hormiga Argentina (Linepithema humile)',
                'category' => 'Hormigas',
                'technical_notes' => 'Especie invasora, forma supercolonias.',
                'control_methods' => ['Cebos líquidos', 'Spray residual', 'Exclusión'],
                'is_active' => true,
            ],
            [
                'name' => 'Hormiga Carpintera (Camponotus spp.)',
                'category' => 'Hormigas',
                'technical_notes' => 'Excava galerías en madera, puede causar daños estructurales.',
                'control_methods' => ['Cebos específicos', 'Inyección de insecticida', 'Exclusión'],
                'is_active' => true,
            ],

            // Aves
            [
                'name' => 'Paloma Doméstica (Columba livia)',
                'category' => 'Aves',
                'technical_notes' => 'Puede transmitir enfermedades y causar daños estructurales.',
                'control_methods' => ['Barreras físicas', 'Repelentes', 'Reducción de alimento'],
                'is_active' => true,
            ],
            [
                'name' => 'Gorrión (Passer domesticus)',
                'category' => 'Aves',
                'technical_notes' => 'Especie invasora, puede competir con aves nativas.',
                'control_methods' => ['Exclusión', 'Repelentes', 'Reducción de hábitat'],
                'is_active' => true,
            ],

            // Arañas
            [
                'name' => 'Araña de Rincón (Loxosceles laeta)',
                'category' => 'Arañas',
                'technical_notes' => 'Veneno necrótico, peligrosa para humanos.',
                'control_methods' => ['Aspirado', 'Spray residual', 'Exclusión'],
                'is_active' => true,
            ],
            [
                'name' => 'Araña Pollito (Grammostola rosea)',
                'category' => 'Arañas',
                'technical_notes' => 'Especie grande pero no peligrosa para humanos.',
                'control_methods' => ['Captura y liberación', 'Exclusión'],
                'is_active' => true,
            ],

            // Otros
            [
                'name' => 'Pulga (Ctenocephalides spp.)',
                'category' => 'Otros',
                'technical_notes' => 'Parásito de mascotas, puede afectar humanos.',
                'control_methods' => ['Tratamiento de mascotas', 'Aspirado', 'Spray residual'],
                'is_active' => true,
            ],
            [
                'name' => 'Garrapata (Ixodes spp.)',
                'category' => 'Otros',
                'technical_notes' => 'Parásito que puede transmitir enfermedades graves.',
                'control_methods' => ['Tratamiento de mascotas', 'Control de vegetación', 'Repelentes'],
                'is_active' => true,
            ],
            [
                'name' => 'Chinche de Cama (Cimex lectularius)',
                'category' => 'Otros',
                'technical_notes' => 'Parásito nocturno, muy difícil de eliminar.',
                'control_methods' => ['Tratamiento térmico', 'Spray residual', 'Aspirado'],
                'is_active' => true,
            ]
        ];

        foreach ($pests as $pestData) {
            Pest::create($pestData);
        }
    }
}
