<?php

declare(strict_types=1);

namespace Centrex\ModelData\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Centrex\ModelData\Data
 */
class Data extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Centrex\ModelData\Data::class;
    }
}
