<?php

namespace App\Providers;

use App\Models\Beneficiary;
use App\Models\Returnee;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
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
        Relation::morphMap([
            'user' => User::class,
            'beneficiary' => Beneficiary::class,
            'returnee' => Returnee::class,
        ]);
    }
}
