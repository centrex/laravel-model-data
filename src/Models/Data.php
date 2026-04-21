<?php

declare(strict_types=1);

namespace Centrex\ModelData\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
                    $model->data_type
                );
            }
        });
    }

    public static function generateKey(string $modelType, mixed $modelId, ?string $dataType): string
    {
        $modelName = class_basename($modelType);
        $dataType ??= 'data';

        return md5(strtolower("{$modelName}|{$modelId}|{$dataType}"));
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}