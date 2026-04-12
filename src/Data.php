<?php

declare(strict_types = 1);

namespace Centrex\ModelData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{MorphTo};

class Data extends Model
{
    protected $guarded = [];

    protected $table = 'model_datas';

    protected $casts = [
        'data' => 'array',
    ];

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForModel($query, Model $model)
    {
        return $query
            ->where('model_type', $model->getMorphClass())
            ->where('model_id', $model->getKey());
    }

    public static function putForModel(Model $model, array $data): self
    {
        return static::updateOrCreate(
            [
                'model_type' => $model->getMorphClass(),
                'model_id'   => $model->getKey(),
            ],
            ['data' => $data],
        );
    }
}
