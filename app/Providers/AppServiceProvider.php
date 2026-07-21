<?php

namespace App\Providers;

use App\Models\StudentType;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Entitlement;
use App\Models\StudyProgram;
use App\Policies\EntitlementPolicy;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Route::bind('study_program', fn (string $value) => StudyProgram::findOrFail($value));

        Gate::policy(Entitlement::class, EntitlementPolicy::class);

        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

        View::composer('*', function ($view) {
            $view->with('studentTypes', StudentType::orderBy('kode')->get());
        });
    }
}
