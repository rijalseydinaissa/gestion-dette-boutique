<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class CloudinaryFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'cloudinaryservice'; // L'alias que vous allez enregistrer dans le conteneur de services
    }
}
