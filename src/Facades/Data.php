<?php

declare(strict_types = 1);

namespace Centrex\ModelData\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Centrex\ModelData\Models\Data
 */
class Data extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'model-data';
    }
}
