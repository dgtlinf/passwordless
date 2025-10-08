<?php

namespace Dgtlinf\Passwordless\Facades;

use Illuminate\Support\Facades\Facade;

class Passwordless extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'passwordless';
    }
}
