<?php

use App\Models\Service;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

// Create dummy client and user if needed
$client = Client::first() ?? Client::factory()->create();
$user = User::first() ?? User::factory()->create();

// Create dummy image
$dummyImageContent = base64_decode("iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==");
Storage::disk('public')->put('services/bait-stations/test_image.png', $dummyImageContent);
$imagePath = 'storage/services/bait-stations/test_image.png';

echo "Created dummy image at: " . storage_path('app/public/services/bait-stations/test_image.png') . "\n";

// Create service
$service = Service::create([
    'client_id' => $client->id,
    'assigned_to' => $user->id,
    'service_type' => 'monitoreo-cebaderas',
    'status' => 'finalizado',
    'scheduled_date' => now(),
    'address' => 'Test Address 123',
    'checklist_stage' => 'monitoreo-firma',
    'checklist_data' => [
        'monitoreo_completo' => [
            'bait_stations' => [
                [
                    'code' => 'CE-001',
                    'photos' => [$imagePath]
                ]
            ]
        ]
    ]
]);

echo "Created service ID: " . $service->id . "\n";
