<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\TechnicianController;
use App\Models\Service;
use Illuminate\Http\Request;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

// 1. Create a dummy service or use existing one
$service = Service::find(45); // Using the ID from previous logs
if (!$service) {
    echo "Service 45 not found. Creating dummy service...\n";
    $service = new Service();
    $service->id = 9999;
    $service->service_type = 'monitoreo-cebaderas';
    $service->checklist_data = [];
    // Mock save to avoid DB writes if possible, or just let it fail on save
}

// 2. Create a dummy file
$filePath = __DIR__ . '/test_croquis.png';
$image = imagecreatetruecolor(100, 100);
imagepng($image, $filePath);

$file = new UploadedFile(
    $filePath,
    'test_croquis.png',
    'image/png',
    null,
    true
);

// 3. Simulate Request
$request = Request::create('/technician/service/45/checklist', 'POST', [
    'checklist_stage' => 'monitoreo-croquis',
    'croquis_notes' => 'Test notes from script',
    'next_stage' => 'monitoreo-completo'
], [], [
    'croquis_file' => $file
]);

// 4. Call Controller Method directly (bypass routing to test logic)
$controller = new TechnicianController();

echo "Simulating upload for Service ID: " . $service->id . "\n";
echo "Stage: monitoreo-croquis\n";

try {
    // We need to mock the user authentication if the controller checks it
    // But let's try calling the specific processing method if possible, 
    // or the public submitChecklist method.
    // submitChecklist checks permissions, so we might need to actAs.
    
    // Reflection to call private method processMonitoreoCroquisData directly for granular testing
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('processMonitoreoCroquisData');
    $method->setAccessible(true);
    
    echo "Calling processMonitoreoCroquisData directly...\n";
    $result = $method->invoke($controller, $request);
    
    echo "Result:\n";
    print_r($result);
    
    if (isset($result['croquis_file'])) {
        echo "SUCCESS: File path returned: " . $result['croquis_file'] . "\n";
        // Verify file existence
        $path = str_replace('storage/', '', $result['croquis_file']);
        if (Storage::disk('public')->exists(str_replace('services/croquis/', 'services/croquis/', $path))) {
             echo "VERIFIED: File exists in storage.\n";
        } else {
             // The path returned is storage/services/croquis/..., disk root is app/public
             // So we check if 'services/croquis/filename' exists
             $relativePath = str_replace('storage/', '', $result['croquis_file']);
             if (Storage::disk('public')->exists($relativePath)) {
                 echo "VERIFIED: File exists in storage (relative path: $relativePath).\n";
             } else {
                 echo "FAILURE: File not found in storage at $relativePath\n";
             }
        }
    } else {
        echo "FAILURE: No croquis_file in result.\n";
    }

} catch (Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

// Cleanup
@unlink($filePath);
