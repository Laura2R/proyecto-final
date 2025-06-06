<?php

namespace App\Providers;

use App\Services\ApiService;
use App\Services\ApiServiceInterface;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ApiServiceInterface::class,ApiService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Definir las reglas por defecto para contraseñas
        Password::defaults(function () {
            return Password::min(8)       // Mínimo 8 caracteres
                ->letters()                    // Debe contener letras
                ->mixedCase()                  // Mayúsculas y minúsculas
                ->numbers()                    // Debe contener números
                ->symbols()                    // Debe contener símbolos
                ->uncompromised();             // No debe estar comprometida
        });
    }
}
