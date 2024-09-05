<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class QrCodeFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'qrcodefacade';
    }
}