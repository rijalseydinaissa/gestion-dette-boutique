<?php

namespace App\Providers;

use App\Services\AuthentificationSanctum;
use Illuminate\Support\ServiceProvider;
use App\Services\AuthentificationServiceInterface;
use App\Services\AuthentificationPassport;

class AuthCustomServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Lié à l'implémentation par défaut AuthentificationPassport
        $this->app->bind(AuthentificationServiceInterface::class, AuthentificationPassport::class);
    }
    
    public function boot()
    {
        //
    }
}
