<?php
namespace App\Facades;

use App\Repository\ClientRepositoryInterface;
use Illuminate\Support\Facades\Facade;

class ClientRepositoryFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ClientRepositoryInterface::class;
    }
}