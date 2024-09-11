<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class QrCodeMailFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'qrcodemailfacade';
    }
}