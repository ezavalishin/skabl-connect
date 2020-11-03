<?php

namespace ezavalishin\SkablConnect\Facades;

use Illuminate\Support\Facades\Facade;

class SkablConnect extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'skabl-connect';
    }
}
