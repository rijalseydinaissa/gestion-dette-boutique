<?php

namespace App\Facades;
use Illuminate\Support\Facades\Facade;


class DetteFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'dette-service';
    }
}

