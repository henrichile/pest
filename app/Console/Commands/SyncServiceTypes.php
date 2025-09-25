<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Service;

class SyncServiceTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'service:sync-types {--dry-run : Show what would be changed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize service_type enum with service_type_id relationship';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('Searching for service type inconsistencies...');
        
        $services = Service::with('serviceType')->get();
        $inconsistencies = [];
        
        foreach ($services as $service) {
            if ($service->serviceType && $service->service_type !== $service->serviceType->slug) {
                $inconsistencies[] = [
                    'service' => $service,
                    'current' => $service->service_type,
                    'expected' => $service->serviceType->slug,
                    'name' => $service->serviceType->name
                ];
            }
        }
        
        if (empty($inconsistencies)) {
            $this->info('✅ No inconsistencies found. All services are properly synchronized.');
            return 0;
        }
        
        $this->warn("Found " . count($inconsistencies) . " inconsistencies:");
        
        $headers = ['Service ID', 'Client', 'Current Type', 'Expected Type', 'Service Name'];
        $rows = [];
        
        foreach ($inconsistencies as $inc) {
            $rows[] = [
                $inc['service']->id,
                $inc['service']->client->name ?? 'N/A',
                $inc['current'],
                $inc['expected'],
                $inc['name']
            ];
        }
        
        $this->table($headers, $rows);
        
        if ($dryRun) {
            $this->info('🔍 Dry run mode - no changes were made.');
            $this->info('Run without --dry-run flag to apply these changes.');
            return 0;
        }
        
        if (!$this->confirm('Do you want to synchronize these service types?')) {
            $this->info('Operation cancelled.');
            return 0;
        }
        
        $corrected = 0;
        foreach ($inconsistencies as $inc) {
            $inc['service']->update(['service_type' => $inc['expected']]);
            $corrected++;
        }
        
        $this->info("✅ Successfully synchronized {$corrected} service types.");
        
        return 0;
    }
}
