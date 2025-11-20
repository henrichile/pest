<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\RedirectTechnicianRoutes::class,
        ],

        'api' => [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's middleware aliases.
     *
     * Aliases may be used instead of class names to conveniently assign middleware to routes and groups.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        
        // Custom middleware for Pest Controller
        'role' => \App\Http\Middleware\CheckRole::class,
        'permission' => \App\Http\Middleware\CheckPermission::class,
        'log.api' => \App\Http\Middleware\LogApiActivity::class,
        'rate.limit.api' => \App\Http\Middleware\RateLimitApi::class,
        'validate.api.key' => \App\Http\Middleware\ValidateApiKey::class,
        'maintenance.mode' => \App\Http\Middleware\CheckMaintenanceMode::class,
        'offline.mode' => \App\Http\Middleware\CheckOfflineMode::class,
        'technician.location' => \App\Http\Middleware\CheckTechnicianLocation::class,
        
        // Resource access middleware
        'work.order.access' => \App\Http\Middleware\CheckWorkOrderAccess::class,
        'client.access' => \App\Http\Middleware\CheckClientAccess::class,
        'site.access' => \App\Http\Middleware\CheckSiteAccess::class,
        'material.access' => \App\Http\Middleware\CheckMaterialAccess::class,
        'checklist.access' => \App\Http\Middleware\CheckChecklistAccess::class,
        'media.access' => \App\Http\Middleware\CheckMediaAccess::class,
        'rating.access' => \App\Http\Middleware\CheckRatingAccess::class,
        'notification.access' => \App\Http\Middleware\CheckNotificationAccess::class,
        'report.access' => \App\Http\Middleware\CheckReportAccess::class,
        'audit.access' => \App\Http\Middleware\CheckAuditAccess::class,
        'backup.access' => \App\Http\Middleware\CheckBackupAccess::class,
        'maintenance.access' => \App\Http\Middleware\CheckMaintenanceAccess::class,
        'user.access' => \App\Http\Middleware\CheckUserAccess::class,
        'role.permission.access' => \App\Http\Middleware\CheckRolePermissionAccess::class,
        'dashboard.access' => \App\Http\Middleware\CheckDashboardAccess::class,
        
        // Work order specific middleware
        'work.order.status' => \App\Http\Middleware\CheckWorkOrderStatus::class,
        'material.stock' => \App\Http\Middleware\CheckMaterialStock::class,
        'checklist.completion' => \App\Http\Middleware\CheckChecklistCompletion::class,
        'photo.requirements' => \App\Http\Middleware\CheckPhotoRequirements::class,
        'signature.requirements' => \App\Http\Middleware\CheckSignatureRequirements::class,
        'work.order.deadline' => \App\Http\Middleware\CheckWorkOrderDeadline::class,
        'work.order.priority' => \App\Http\Middleware\CheckWorkOrderPriority::class,
        'work.order.sla' => \App\Http\Middleware\CheckWorkOrderSLA::class,
        'work.order.geofence' => \App\Http\Middleware\CheckWorkOrderGeofence::class,
        'work.order.weather' => \App\Http\Middleware\CheckWorkOrderWeather::class,
        'work.order.equipment' => \App\Http\Middleware\CheckWorkOrderEquipment::class,
        'work.order.safety' => \App\Http\Middleware\CheckWorkOrderSafety::class,
        'work.order.compliance' => \App\Http\Middleware\CheckWorkOrderCompliance::class,
        'work.order.quality' => \App\Http\Middleware\CheckWorkOrderQuality::class,
        'work.order.performance' => \App\Http\Middleware\CheckWorkOrderPerformance::class,
        'work.order.efficiency' => \App\Http\Middleware\CheckWorkOrderEfficiency::class,
        'work.order.sustainability' => \App\Http\Middleware\CheckWorkOrderSustainability::class,
        'work.order.innovation' => \App\Http\Middleware\CheckWorkOrderInnovation::class,
        'work.order.excellence' => \App\Http\Middleware\CheckWorkOrderExcellence::class,
        'work.order.reliability' => \App\Http\Middleware\CheckWorkOrderReliability::class,
        'work.order.integrity' => \App\Http\Middleware\CheckWorkOrderIntegrity::class,
        'work.order.transparency' => \App\Http\Middleware\CheckWorkOrderTransparency::class,
        'work.order.accountability' => \App\Http\Middleware\CheckWorkOrderAccountability::class,
        'work.order.responsibility' => \App\Http\Middleware\CheckWorkOrderResponsibility::class,
        'work.order.ethics' => \App\Http\Middleware\CheckWorkOrderEthics::class,
        'work.order.values' => \App\Http\Middleware\CheckWorkOrderValues::class,
        'work.order.culture' => \App\Http\Middleware\CheckWorkOrderCulture::class,
        'work.order.mission' => \App\Http\Middleware\CheckWorkOrderMission::class,
        'work.order.vision' => \App\Http\Middleware\CheckWorkOrderVision::class,
        'work.order.purpose' => \App\Http\Middleware\CheckWorkOrderPurpose::class,
        'work.order.impact' => \App\Http\Middleware\CheckWorkOrderImpact::class,
        'work.order.legacy' => \App\Http\Middleware\CheckWorkOrderLegacy::class,
        'work.order.future' => \App\Http\Middleware\CheckWorkOrderFuture::class,
    ];
}



