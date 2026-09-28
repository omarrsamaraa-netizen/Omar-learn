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
        /**
         * Managing the collection: adding, editing, trashing and deleting pieces.
         * Views ask for it with @can('manage-collection'), which is false for
         * guests as well as customers.
         */
        Gate::define('manage-collection', fn (User $user): bool => $user->isAdmin());
    }
}
