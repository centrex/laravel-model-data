<?php

declare(strict_types = 1);

namespace Centrex\ModelData\Models;

use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string $key
 * @property string $data_type
 * @property string $model_type
 * @property int|string $model_id
 * @property array<string, mixed> $data
 */
class Data extends Model
{
    protected $table = 'model_datas';

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'data_type',
        'model_type',
        'model_id',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (empty($model->key)) {
                $model->key = static::generateKey(
                    $model->model_type,
                    $model->model_id,
                    $model->data_type,
                );
            }
        });
    }

    public static function generateKey(string $modelType, int|string $modelId, ?string $dataType): string
    {
        $modelName = class_basename($modelType);
        $dataType ??= 'data';

        return md5(strtolower("{$modelName}|{$modelId}|{$dataType}"));
    }

    /** @return MorphTo<Model, $this> */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to the polymorphic data record(s) belonging to a given model instance.
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeForModel(Builder $query, Model $model, string $dataType = 'data'): Builder
    {
        return $query
            ->where('model_type', $model->getMorphClass())
            ->where('model_id', $model->getKey())
            ->where('data_type', $dataType);
    }

    /**
     * Fetch the decoded data array for a given model instance, or [] if none exists.
     *
     * @return array<string, mixed>
     */
    public static function getForModel(Model $model, string $dataType = 'data'): array
    {
        return static::query()->forModel($model, $dataType)->first()?->data ?? [];
    }

    /**
     * Upsert the polymorphic data record for a given model instance.
     *
     * @param  array<string, mixed>  $data
     */
    public static function putForModel(Model $model, array $data, string $dataType = 'data'): self
    {
        $modelType = $model->getMorphClass();
        /** @var int|string $modelId */
        $modelId = $model->getKey();
        $key = static::generateKey($modelType, $modelId, $dataType);

        return static::query()->updateOrCreate(
            ['key' => $key],
            [
                'key'        => $key,
                'data_type'  => $dataType,
                'model_type' => $modelType,
                'model_id'   => $modelId,
                'data'       => $data,
            ],
        );
    }
}
