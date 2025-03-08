<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Profile;
use App\Models\Module;
use Illuminate\Support\Facades\Auth;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Pass profile and modules to master layout
    View::composer('layouts.master', function ($view) {
        if (Auth::check()) {
            $profile = Profile::where('user_id', Auth::id())->first();
            $view->with('profile', $profile);

            $role = Auth::user()->role;
            $modules = $role->modules;  

            
            $modules = Module::whereIn('id', $modules)->get();
            $view
            ->with('modules', $modules);
        }
    });
    }
}
