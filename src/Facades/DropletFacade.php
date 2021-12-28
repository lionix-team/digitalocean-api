<?php

namespace DigitaloceanApi\Facades;

use Illuminate\Support\Facades\Facade;
use Services\DropletService;

class DropletFacade extends Facade
{
    public static function getFacadeAccessor()
    {
        return DropletService::class;
    }
}