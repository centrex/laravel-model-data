<?php

declare(strict_types = 1);

namespace Centrex\ModelData\Exceptions;

use Exception;

class InvalidDataModel extends Exception
{
    public static function create(string $model): self
    {
        return new self("The model `{$model}` is invalid. A valid model must extend the model \Centrex\ModelData\Data.");
    }
}
