<?php

declare(strict_types = 1);

namespace Centrex\ModelData\Events;

use Centrex\ModelData\Data;
use Illuminate\Database\Eloquent\Model;

class DataUpdated
{
    /** @var \Centrex\ModelData\Data|null */
    public $oldData;

    /** @var \Centrex\ModelData\Data */
    public $newData;

    /** @var \Illuminate\Database\Eloquent\Model */
    public $model;

    public function __construct(?Data $oldData, Data $newData, Model $model)
    {
        $this->oldData = $oldData;

        $this->newData = $newData;

        $this->model = $model;
    }
}
