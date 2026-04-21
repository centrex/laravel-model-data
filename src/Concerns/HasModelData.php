<?php

declare(strict_types=1);

namespace Centrex\ModelData\Concerns;

use Centrex\ModelData\Models\Data;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasModelData
{
    public function modelData(): MorphMany
    {
        return $this->morphMany(
            $this->getDataModelClassName(),
            'model',
            'model_type',
            $this->getModelKeyColumnName()
        );
    }

    public function dataRecord(string $dataType = 'data'): ?Data
    {
        return $this->modelData()
            ->where('data_type', $dataType)
            ->first();
    }

    public function defaultData(): MorphOne
    {
        return $this->morphOne(
            $this->getDataModelClassName(),
            'model',
            'model_type',
            $this->getModelKeyColumnName()
        )->where('data_type', 'data');
    }

    public function getData(string $dataType = 'data', array $default = []): array
    {
        return $this->dataRecord($dataType)?->data ?? $default;
    }

    public function putData(string $dataType, array $data): Data
    {
        $modelType = $this->getMorphClass();
        $modelId = $this->getKey();

        $key = Data::generateKey($modelType, $modelId, $dataType);

        return $this->modelData()->updateOrCreate(
            ['key' => $key],
            [
                'key' => $key,
                'model_type' => $modelType,
                'model_id' => $modelId,
                'data_type' => $dataType,
                'data' => $data,
            ]
        );
    }

    public function hasData(string $dataType = 'data'): bool
    {
        return $this->modelData()
            ->where('data_type', $dataType)
            ->exists();
    }

    public function forgetData(string $dataType = 'data'): int
    {
        return $this->modelData()
            ->where('data_type', $dataType)
            ->delete();
    }

    public function getDataValue(string $dataType, string $path, mixed $default = null): mixed
    {
        return data_get($this->getData($dataType), $path, $default);
    }

    protected function getModelKeyColumnName(): string
    {
        return config('model-data.model_primary_key_attribute') ?? 'model_id';
    }

    protected function getDataModelClassName(): string
    {
        return config('model-data.data_model') ?? Data::class;
    }
}