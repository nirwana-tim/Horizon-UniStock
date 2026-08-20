<?php

namespace App\Providers;

use App\Models\Entitlement;
use App\Models\StudentLevel;
use App\Models\StudyProgram;
use App\Policies\EntitlementPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('web', function ($request) {
            return Limit::perMinute(120)->by(
                $request->user()?->id ?: $request->ip()
            );
        });

        $proxies = config('trustproxies.proxies');
        if (in_array('*', $proxies, true) || in_array('**', $proxies, true)) {
            TrustProxies::at('*');
        } elseif ($proxies !== []) {
            TrustProxies::at($proxies);
        }

        Route::bind('study_program', fn (string $value) => StudyProgram::findOrFail($value));

        Gate::policy(Entitlement::class, EntitlementPolicy::class);

        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

        View::composer([
            'finance.size-events.create',
            'distribution.entitlement.edit',
            'distribution.entitlement.create',
            'distribution.distribution-schedule.edit',
            'distribution.distribution-schedule.create',
            'master.student-level.index',
            'master.student.create',
            'master.student.edit',
        ], function ($view) {
            $view->with('studentLevels', StudentLevel::orderBy('kode')->get());
        });
    }
}
