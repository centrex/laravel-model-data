<?php

declare(strict_types = 1);

namespace Centrex\ModelData\Exceptions;

use Exception;

class InvalidData extends Exception
{
    public static function create(string $name): self
    {
        return new self("The data `{$name}` is an invalid data.");
    }
}
