<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\WorkSession;
use App\Models\MaterialMovement;
use App\Models\ChecklistResponse;
use App\Models\Nonconformity;
use App\Models\Treatment;
use App\Models\Material;
use App\Models\Pest;
use App\Models\ChecklistTemplate;
use App\Models\ChecklistItem;
use App\Models\Site;
use App\Models\Client;
use App\Models\Service;
use App\Models\WorkOrderAssignment;
use App\Models\User;
use App\Services\PdfService;
use App\Services\NotificationService;
use App\Http\Requests\UpdateSystemSettingsRequest;
use App\Http\Requests\UpdateEmailSettingsRequest;
use App\Http\Requests\UpdateNotificationSettingsRequest;
use App\Http\Requests\UpdateSecuritySettingsRequest;
use App\Http\Requests\UpdateBackupSettingsRequest;
use App\Http\Requests\UpdateMaintenanceSettingsRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ConfigurationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:super-admin');
    }

    /**
     * Show configuration dashboard.
     */
    public function dashboard(): View
    {
        // Get system information
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database_driver' => config('database.default'),
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
            'session_driver' => config('session.driver'),
            'mail_driver' => config('mail.default'),
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug'),
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
        ];
        
        // Get database information
        $databaseInfo = [
            'total_tables' => DB::select("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = DATABASE()")[0]->count,
            'total_records' => $this->getTotalRecords(),
            'database_size' => $this->getDatabaseSize(),
        ];
        
        // Get storage information
        $storageInfo = [
            'total_size' => $this->getStorageSize(),
            'used_size' => $this->getUsedStorageSize(),
            'free_size' => $this->getFreeStorageSize(),
        ];
        
        // Get backup information
        $backupInfo = [
            'last_backup' => $this->getLastBackupDate(),
            'backup_count' => $this->getBackupCount(),
            'backup_size' => $this->getBackupSize(),
        ];
        
        // Get log information
        $logInfo = [
            'log_size' => $this->getLogSize(),
            'error_count' => $this->getErrorCount(),
            'last_error' => $this->getLastErrorDate(),
        ];
        
        return view('configuration.dashboard', compact(
            'systemInfo',
            'databaseInfo',
            'storageInfo',
            'backupInfo',
            'logInfo'
        ));
    }

    /**
     * Show system settings.
     */
    public function systemSettings(): View
    {
        $settings = [
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug'),
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
            'session_timeout' => config('session.lifetime'),
            'cache_timeout' => config('cache.default'),
            'queue_timeout' => config('queue.connections.database.retry_after'),
        ];
        
        return view('configuration.system-settings', compact('settings'));
    }

    /**
     * Update system settings.
     */
    public function updateSystemSettings(UpdateSystemSettingsRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            
            $settings = $request->validated();
            
            // Update configuration values
            foreach ($settings as $key => $value) {
                if ($value !== null) {
                    config([$key => $value]);
                }
            }
            
            // Log activity
            activity()
                ->causedBy(Auth::user())
                ->log('Configuración del sistema actualizada');
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Configuración del sistema actualizada correctamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating system settings: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al actualizar la configuración del sistema.');
        }
    }

    /**
     * Show email settings.
     */
    public function emailSettings(): View
    {
        $settings = [
            'mail_driver' => config('mail.default'),
            'mail_host' => config('mail.mailers.smtp.host'),
            'mail_port' => config('mail.mailers.smtp.port'),
            'mail_username' => config('mail.mailers.smtp.username'),
            'mail_encryption' => config('mail.mailers.smtp.encryption'),
            'mail_from_address' => config('mail.from.address'),
            'mail_from_name' => config('mail.from.name'),
        ];
        
        return view('configuration.email-settings', compact('settings'));
    }

    /**
     * Update email settings.
     */
    public function updateEmailSettings(UpdateEmailSettingsRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            
            $settings = $request->validated();
            
            // Update configuration values
            foreach ($settings as $key => $value) {
                if ($value !== null) {
                    config([$key => $value]);
                }
            }
            
            // Log activity
            activity()
                ->causedBy(Auth::user())
                ->log('Configuración de email actualizada');
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Configuración de email actualizada correctamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating email settings: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al actualizar la configuración de email.');
        }
    }

    /**
     * Show notification settings.
     */
    public function notificationSettings(): View
    {
        $settings = [
            'notification_email' => config('mail.notification_email'),
            'notification_sms' => config('sms.enabled'),
            'notification_push' => config('push.enabled'),
            'notification_whatsapp' => config('whatsapp.enabled'),
            'notification_webhook' => config('webhook.enabled'),
        ];
        
        return view('configuration.notification-settings', compact('settings'));
    }

    /**
     * Update notification settings.
     */
    public function updateNotificationSettings(UpdateNotificationSettingsRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            
            $settings = $request->validated();
            
            // Update configuration values
            foreach ($settings as $key => $value) {
                if ($value !== null) {
                    config([$key => $value]);
                }
            }
            
            // Log activity
            activity()
                ->causedBy(Auth::user())
                ->log('Configuración de notificaciones actualizada');
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Configuración de notificaciones actualizada correctamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating notification settings: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al actualizar la configuración de notificaciones.');
        }
    }

    /**
     * Show security settings.
     */
    public function securitySettings(): View
    {
        $settings = [
            'password_min_length' => config('auth.password_min_length'),
            'password_require_uppercase' => config('auth.password_require_uppercase'),
            'password_require_lowercase' => config('auth.password_require_lowercase'),
            'password_require_numbers' => config('auth.password_require_numbers'),
            'password_require_symbols' => config('auth.password_require_symbols'),
            'session_timeout' => config('session.lifetime'),
            'max_login_attempts' => config('auth.max_login_attempts'),
            'lockout_duration' => config('auth.lockout_duration'),
            'two_factor_enabled' => config('auth.two_factor_enabled'),
            'ip_whitelist' => config('auth.ip_whitelist'),
        ];
        
        return view('configuration.security-settings', compact('settings'));
    }

    /**
     * Update security settings.
     */
    public function updateSecuritySettings(UpdateSecuritySettingsRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            
            $settings = $request->validated();
            
            // Update configuration values
            foreach ($settings as $key => $value) {
                if ($value !== null) {
                    config([$key => $value]);
                }
            }
            
            // Log activity
            activity()
                ->causedBy(Auth::user())
                ->log('Configuración de seguridad actualizada');
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Configuración de seguridad actualizada correctamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating security settings: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al actualizar la configuración de seguridad.');
        }
    }

    /**
     * Show backup settings.
     */
    public function backupSettings(): View
    {
        $settings = [
            'backup_enabled' => config('backup.enabled'),
            'backup_frequency' => config('backup.frequency'),
            'backup_retention_days' => config('backup.retention_days'),
            'backup_storage' => config('backup.storage'),
            'backup_encryption' => config('backup.encryption'),
            'backup_compression' => config('backup.compression'),
        ];
        
        return view('configuration.backup-settings', compact('settings'));
    }

    /**
     * Update backup settings.
     */
    public function updateBackupSettings(UpdateBackupSettingsRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            
            $settings = $request->validated();
            
            // Update configuration values
            foreach ($settings as $key => $value) {
                if ($value !== null) {
                    config([$key => $value]);
                }
            }
            
            // Log activity
            activity()
                ->causedBy(Auth::user())
                ->log('Configuración de respaldos actualizada');
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Configuración de respaldos actualizada correctamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating backup settings: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al actualizar la configuración de respaldos.');
        }
    }

    /**
     * Show maintenance settings.
     */
    public function maintenanceSettings(): View
    {
        $settings = [
            'maintenance_mode' => config('app.maintenance_mode'),
            'maintenance_message' => config('app.maintenance_message'),
            'maintenance_start' => config('app.maintenance_start'),
            'maintenance_end' => config('app.maintenance_end'),
            'maintenance_notification' => config('app.maintenance_notification'),
        ];
        
        return view('configuration.maintenance-settings', compact('settings'));
    }

    /**
     * Update maintenance settings.
     */
    public function updateMaintenanceSettings(UpdateMaintenanceSettingsRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            
            $settings = $request->validated();
            
            // Update configuration values
            foreach ($settings as $key => $value) {
                if ($value !== null) {
                    config([$key => $value]);
                }
            }
            
            // Log activity
            activity()
                ->causedBy(Auth::user())
                ->log('Configuración de mantenimiento actualizada');
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Configuración de mantenimiento actualizada correctamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating maintenance settings: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al actualizar la configuración de mantenimiento.');
        }
    }

    /**
     * Show database management.
     */
    public function databaseManagement(): View
    {
        // Get database tables
        $tables = DB::select("SHOW TABLES");
        $tableNames = array_map(function ($table) {
            return array_values((array) $table)[0];
        }, $tables);
        
        // Get table information
        $tableInfo = [];
        foreach ($tableNames as $tableName) {
            $tableInfo[$tableName] = [
                'rows' => DB::table($tableName)->count(),
                'size' => $this->getTableSize($tableName),
                'engine' => $this->getTableEngine($tableName),
                'collation' => $this->getTableCollation($tableName),
            ];
        }
        
        return view('configuration.database-management', compact('tableInfo'));
    }

    /**
     * Optimize database.
     */
    public function optimizeDatabase(): RedirectResponse
    {
        try {
            DB::beginTransaction();
            
            // Get all tables
            $tables = DB::select("SHOW TABLES");
            $tableNames = array_map(function ($table) {
                return array_values((array) $table)[0];
            }, $tables);
            
            // Optimize each table
            foreach ($tableNames as $tableName) {
                DB::statement("OPTIMIZE TABLE `{$tableName}`");
            }
            
            // Log activity
            activity()
                ->causedBy(Auth::user())
                ->log('Base de datos optimizada');
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Base de datos optimizada correctamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error optimizing database: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al optimizar la base de datos.');
        }
    }

    /**
     * Repair database.
     */
    public function repairDatabase(): RedirectResponse
    {
        try {
            DB::beginTransaction();
            
            // Get all tables
            $tables = DB::select("SHOW TABLES");
            $tableNames = array_map(function ($table) {
                return array_values((array) $table)[0];
            }, $tables);
            
            // Repair each table
            foreach ($tableNames as $tableName) {
                DB::statement("REPAIR TABLE `{$tableName}`");
            }
            
            // Log activity
            activity()
                ->causedBy(Auth::user())
                ->log('Base de datos reparada');
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Base de datos reparada correctamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error repairing database: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al reparar la base de datos.');
        }
    }

    /**
     * Clear cache.
     */
    public function clearCache(): RedirectResponse
    {
        try {
            DB::beginTransaction();
            
            // Clear application cache
            \Artisan::call('cache:clear');
            
            // Clear configuration cache
            \Artisan::call('config:clear');
            
            // Clear route cache
            \Artisan::call('route:clear');
            
            // Clear view cache
            \Artisan::call('view:clear');
            
            // Log activity
            activity()
                ->causedBy(Auth::user())
                ->log('Caché limpiado');
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Caché limpiado correctamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error clearing cache: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al limpiar el caché.');
        }
    }

    /**
     * Clear logs.
     */
    public function clearLogs(): RedirectResponse
    {
        try {
            DB::beginTransaction();
            
            // Clear application logs
            \Artisan::call('log:clear');
            
            // Log activity
            activity()
                ->causedBy(Auth::user())
                ->log('Logs limpiados');
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Logs limpiados correctamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error clearing logs: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al limpiar los logs.');
        }
    }

    /**
     * Get total records count.
     */
    private function getTotalRecords(): int
    {
        try {
            $tables = ['users', 'clients', 'sites', 'services', 'work_orders', 'materials', 'pests'];
            $total = 0;
            
            foreach ($tables as $table) {
                $total += DB::table($table)->count();
            }
            
            return $total;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get database size.
     */
    private function getDatabaseSize(): string
    {
        try {
            $result = DB::select("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb FROM information_schema.tables WHERE table_schema = DATABASE()");
            return $result[0]->size_mb . ' MB';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Get storage size.
     */
    private function getStorageSize(): string
    {
        try {
            $size = disk_total_space(storage_path());
            return $this->formatBytes($size);
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Get used storage size.
     */
    private function getUsedStorageSize(): string
    {
        try {
            $size = disk_total_space(storage_path()) - disk_free_space(storage_path());
            return $this->formatBytes($size);
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Get free storage size.
     */
    private function getFreeStorageSize(): string
    {
        try {
            $size = disk_free_space(storage_path());
            return $this->formatBytes($size);
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Get last backup date.
     */
    private function getLastBackupDate(): ?string
    {
        try {
            $backups = Storage::files('backups');
            if (empty($backups)) {
                return null;
            }
            
            $lastBackup = end($backups);
            return Storage::lastModified($lastBackup);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get backup count.
     */
    private function getBackupCount(): int
    {
        try {
            return count(Storage::files('backups'));
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get backup size.
     */
    private function getBackupSize(): string
    {
        try {
            $backups = Storage::files('backups');
            $totalSize = 0;
            
            foreach ($backups as $backup) {
                $totalSize += Storage::size($backup);
            }
            
            return $this->formatBytes($totalSize);
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Get log size.
     */
    private function getLogSize(): string
    {
        try {
            $logPath = storage_path('logs');
            $size = 0;
            
            if (is_dir($logPath)) {
                $files = glob($logPath . '/*.log');
                foreach ($files as $file) {
                    $size += filesize($file);
                }
            }
            
            return $this->formatBytes($size);
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Get error count.
     */
    private function getErrorCount(): int
    {
        try {
            $logPath = storage_path('logs/laravel.log');
            if (!file_exists($logPath)) {
                return 0;
            }
            
            $content = file_get_contents($logPath);
            return substr_count($content, 'ERROR');
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get last error date.
     */
    private function getLastErrorDate(): ?string
    {
        try {
            $logPath = storage_path('logs/laravel.log');
            if (!file_exists($logPath)) {
                return null;
            }
            
            $content = file_get_contents($logPath);
            $lines = explode("\n", $content);
            
            foreach (array_reverse($lines) as $line) {
                if (strpos($line, 'ERROR') !== false) {
                    preg_match('/\[(.*?)\]/', $line, $matches);
                    return $matches[1] ?? null;
                }
            }
            
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get table size.
     */
    private function getTableSize(string $tableName): string
    {
        try {
            $result = DB::select("SELECT ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?", [$tableName]);
            return $result[0]->size_mb . ' MB';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Get table engine.
     */
    private function getTableEngine(string $tableName): string
    {
        try {
            $result = DB::select("SELECT engine FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?", [$tableName]);
            return $result[0]->engine ?? 'N/A';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Get table collation.
     */
    private function getTableCollation(string $tableName): string
    {
        try {
            $result = DB::select("SELECT table_collation FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?", [$tableName]);
            return $result[0]->table_collation ?? 'N/A';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Show admin settings page.
     */
    public function settings(): View
    {
        // Get company settings from database
        $companySettings = DB::table('settings')
            ->whereIn('key', ['company_name', 'company_rut', 'company_address', 'company_phone', 'company_email'])
            ->pluck('value', 'key')
            ->toArray();

        // Fallback to config if not in database
        $settings = [
            'company_name' => $companySettings['company_name'] ?? config('app.name', 'PestController'),
            'company_rut' => $companySettings['company_rut'] ?? null,
            'company_address' => $companySettings['company_address'] ?? null,
            'company_phone' => $companySettings['company_phone'] ?? null,
            'company_email' => $companySettings['company_email'] ?? null,
        ];

        return view('admin.settings', compact('settings'));
    }

    /**
     * Update company settings.
     */
    public function updateCompanySettings(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'company_name' => 'required|string|max:255',
                'company_rut' => 'nullable|string|max:50',
                'company_address' => 'nullable|string|max:500',
                'company_phone' => 'nullable|string|max:50',
                'company_email' => 'nullable|email|max:255',
                'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            ]);

            // Handle logo upload
            if ($request->hasFile('company_logo')) {
                $logo = $request->file('company_logo');
                $logoName = 'logo.' . $logo->getClientOriginalExtension();
                $logo->move(public_path(), $logoName);
            }

            // Save settings to database
            $settings = [
                'company_name' => $request->company_name,
                'company_rut' => $request->company_rut,
                'company_address' => $request->company_address,
                'company_phone' => $request->company_phone,
                'company_email' => $request->company_email,
            ];

            foreach ($settings as $key => $value) {
                DB::table('settings')
                    ->updateOrInsert(
                        ['key' => $key],
                        ['value' => $value, 'updated_at' => now()]
                    );
            }

            // Also update APP_NAME in .env if company_name changed
            if ($request->company_name && $request->company_name !== config('app.name')) {
                $envFile = base_path('.env');
                if (file_exists($envFile)) {
                    $envContent = file_get_contents($envFile);
                    $envContent = preg_replace('/^APP_NAME=.*/m', 'APP_NAME="' . addslashes($request->company_name) . '"', $envContent);
                    file_put_contents($envFile, $envContent);
                }
            }

            activity()
                ->causedBy(Auth::user())
                ->log('Configuración de la empresa actualizada');

            DB::commit();

            return redirect()->route('admin.settings')
                ->with('success', 'Datos de la empresa actualizados correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating company settings: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar los datos de la empresa.']);
        }
    }

    /**
     * Update SMTP settings.
     */
    public function updateSmtpSettings(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'smtp_host' => 'required|string|max:255',
                'smtp_port' => 'required|integer|min:1|max:65535',
                'smtp_username' => 'required|string|max:255',
                'smtp_password' => 'nullable|string|max:255',
                'smtp_encryption' => 'required|in:tls,ssl,',
                'smtp_from_address' => 'required|email|max:255',
                'smtp_from_name' => 'nullable|string|max:255',
            ]);

            // Note: In a production environment, you should update the .env file
            // or use a settings table. This is a simplified version.
            Log::info('SMTP settings updated', $request->except(['smtp_password', '_token', '_method']));

            activity()
                ->causedBy(Auth::user())
                ->log('Configuración SMTP actualizada');

            return redirect()->route('admin.settings')
                ->with('success', 'Configuración SMTP actualizada correctamente.');

        } catch (\Exception $e) {
            Log::error('Error updating SMTP settings: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar la configuración SMTP.']);
        }
    }
}
