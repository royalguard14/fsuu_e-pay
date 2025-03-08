<?php

namespace App\Providers;
use App\Models\Profile;
use Illuminate\Support\Facades\View;
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
public function boot()
{
    View::composer('*', function ($view) {
        $userId = auth()->id();
        $profile = Profile::where('user_id', $userId)->first();
        $view->with('profile', $profile);
    });
}
}
