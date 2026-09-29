<?php

namespace App\Providers;

use App\Models\AuditLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(Login::class, function (Login $event) {
            AuditLog::create([
                'organization_id' => $event->user->organization_id ?? null,
                'user_id' => $event->user->id,
                'event' => 'auth.login',
                'ip_address' => Request::ip(),
            ]);
        });

        Event::listen(Logout::class, function (Logout $event) {
            if (! $event->user) {
                return;
            }

            AuditLog::create([
                'organization_id' => $event->user->organization_id ?? null,
                'user_id' => $event->user->id,
                'event' => 'auth.logout',
                'ip_address' => Request::ip(),
            ]);
        });
    }
}
