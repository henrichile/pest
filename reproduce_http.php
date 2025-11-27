<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

require __DIR__.'/vendor/autoload.php';

// Load .env
if (file_exists(__DIR__.'/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

// Manually set APP_KEY to avoid missing key error in CLI
putenv('APP_KEY=base64:uxPvRzSd4RG9YY7/gQ+XOu5xT5NEtNljKJYU2tKGN34=');
$_ENV['APP_KEY'] = 'base64:uxPvRzSd4RG9YY7/gQ+XOu5xT5NEtNljKJYU2tKGN34=';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Bootstrap the application to load database connections
$kernel->bootstrap();

// Manually set APP_KEY in config
$app['config']->set('app.key', 'base64:uxPvRzSd4RG9YY7/gQ+XOu5xT5NEtNljKJYU2tKGN34=');

// Fix View Cache Path for CLI
$app['config']->set('view.compiled', '/tmp/storage/framework/views');

// 1. Setup Data
$service = Service::find(45);
if (!$service) {
    die("Service 45 not found. Please check database.\n");
}
// Ensure service is in progress
$service->status = 'en_progreso';
$service->save();

// Mock User (assuming ID 1 is admin/technician)
$user = User::find(1); 
if (!$user) {
    die("User 1 not found.\n");
}

// 2. Create Dummy File
$filePath = __DIR__ . '/test_croquis_http.png';
$image = imagecreatetruecolor(100, 100);
imagepng($image, $filePath);

$file = new UploadedFile(
    $filePath,
    'test_croquis_http.png',
    'image/png',
    null,
    true
);

// 3. Simulate HTTP Request
// Route: POST /technician/services/{service}/checklist/submit
// Name: technician.service.checklist.submit
$uri = "/technician/services/{$service->id}/checklist/submit";

echo "Simulating HTTP POST to: $uri\n";

// Assign technician role for testing
try {
    $user->assignRole('technician');
    echo "Assigned 'technician' role to user.\n";
} catch (\Exception $e) {
    echo "Could not assign role: " . $e->getMessage() . "\n";
}

$request = Request::create($uri, 'POST', [
    'checklist_stage' => 'monitoreo-croquis',
    'croquis_notes' => 'Test notes from HTTP script',
    'next_stage' => 'monitoreo-completo',
    '_token' => 'testing_token' // Middleware might check this, but we might bypass VerifyCsrfToken
], [], [
    'croquis_file' => $file
]);

// Bind request to container
$app->instance('request', $request);

// Start session and set CSRF token
$session = $app['session.store'];
$session->start();
$session->put('_token', 'testing_token');
$request->setLaravelSession($session);

// Mock Auth
$app['auth']->guard()->setUser($user);

echo "User ID: " . $user->id . "\n";
echo "Roles: " . json_encode($user->roles->pluck('name')) . "\n";

// Disable Exception Handling to see raw error
$app->instance(Illuminate\Contracts\Debug\ExceptionHandler::class, new class($app) extends Illuminate\Foundation\Exceptions\Handler {
    public function render($request, Throwable $e) {
        throw $e;
    }
});

// 4. Handle Request via Kernel
$response = $kernel->handle($request);

echo "Response Status: " . $response->getStatusCode() . "\n";
$content = $response->getContent();
// Extract title or exception message
if (preg_match('/<div class="exception-message">(.*?)<\/div>/s', $content, $matches)) {
    echo "Exception Message: " . trim($matches[1]) . "\n";
} else {
    echo "Response Content (stripped): " . substr(strip_tags($content), 0, 1000) . "\n";
}

if ($response->isRedirection()) {
    echo "Redirected to: " . $response->headers->get('Location') . "\n";
}

// Cleanup
@unlink($filePath);
