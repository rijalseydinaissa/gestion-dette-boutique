<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class SmsServiceFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'SmsService'; // Le nom enregistré dans le service provider
    }
}
