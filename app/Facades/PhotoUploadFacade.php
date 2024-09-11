<?php

// app/Facades/Upload.php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class PhotoUploadFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'photouploadservice';
    }
}
