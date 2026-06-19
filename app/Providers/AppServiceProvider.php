<?php

namespace App\Providers;

use App\Models\KnowledgeResource;
use App\Policies\KnowledgeResourcePolicy;
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
        Gate::policy(KnowledgeResource::class, KnowledgeResourcePolicy::class);
    }
}
