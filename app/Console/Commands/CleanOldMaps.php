<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\MapboxHelper;

class CleanOldMaps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maps:clean {--days=7 : Number of days to keep map images}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean old Mapbox map images from storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        
        if ($days <= 0) {
            $this->error('Number of days must be greater than 0');
            return Command::FAILURE;
        }

        $this->info("Cleaning map images older than {$days} days...");
        
        try {
            $deletedCount = MapboxHelper::cleanOldMapImages($days);
            
            if ($deletedCount > 0) {
                $this->info("✅ Successfully deleted {$deletedCount} old map images.");
            } else {
                $this->info("ℹ️  No old map images found to delete.");
            }
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error("❌ Error cleaning map images: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
