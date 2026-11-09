<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
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
        Gate::define('manage-patients', function (?User $user): bool {
            $email = (string) config('clinic.admin_email');

            return session('admin_authenticated') === true && $email !== ''
                && hash_equals(mb_strtolower(trim($email)), (string) session('admin_email'));
        });
        Gate::define('patient-notes', fn (?User $user): bool => Gate::allows('manage-patients'));
    }
}
