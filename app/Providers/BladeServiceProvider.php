<?php
namespace App\Providers;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
    }
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Blade::if('developer', function () {
            return Auth::check() && Auth::user()->role->role_name == 'Developer';
        });
        Blade::if('user', function () {
            return Auth::check() && Auth::user()->role->role_name == 'User';
        });
        Blade::if('admin', function () {
            return Auth::check() && Auth::user()->role->role_name == 'Admin';
        });
        Blade::if('cashier', function () {
            return Auth::check() && Auth::user()->role->role_name == 'Cashier';
        });
        Blade::if('student', function () {
            return Auth::check() && Auth::user()->role->role_name == 'Student';
        });
        Blade::if('parent', function () {
            return Auth::check() && Auth::user()->role->role_name == 'Parent';
        });
    }
}