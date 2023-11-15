<?php

declare(strict_types=1);

namespace Centrex\ModelData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Data extends Model
{
    protected $guarded = [];

    protected $table = 'model_datas';

    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
