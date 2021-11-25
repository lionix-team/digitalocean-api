<?php

namespace DigitaloceanApi\Facades;

use Illuminate\Support\Facades\Facade;
use Services\DomainService;

class DomainFacade extends Facade
{
    public static function getFacadeAccessor()
    {
        return DomainService::class;
    }
}