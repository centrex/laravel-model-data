<?php

declare(strict_types = 1);

namespace Centrex\ModelData;

use Illuminate\Database\Eloquent\Relations\{MorphOne, Relation};

/**
 * This trait lets you add a "data" column functionality to any Eloquent model.
 * It serializes attributes which don't exist as columns on the model's table
 * into a JSON column named data (customizable by overriding getDataColumn).
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasData
{
    /**
     * Encrypted castables have to be handled using a special approach that prevents the data from getting encrypted repeatedly.
     *
     * The default encrypted castables ('encrypted', 'encrypted:array', 'encrypted:collection', 'encrypted:json', 'encrypted:object')
     * are already handled, so you can use this array to add your own encrypted castables.
     */
    public static array $customEncryptedCastables = [];

    /**
     * We need this property, because both created & saved event listeners
     * decode the data (to take precedence before other created & saved)
     * listeners, but we don't want the data to be decoded twice.
     */
    public bool $dataEncoded = false;

    protected function decodeVirtualColumn(): void
    {
        if (!$this->dataEncoded) {
            return;
        }

        $encryptedCastables = array_merge(
            static::$customEncryptedCastables,
            ['encrypted', 'encrypted:array', 'encrypted:collection', 'encrypted:json', 'encrypted:object'], // Default encrypted castables
        );

        foreach ($this->getAttribute(static::getDataColumn()) ?? [] as $key => $value) {
            $attributeHasEncryptedCastable = in_array(data_get($this->getCasts(), $key), $encryptedCastables);

            if ($attributeHasEncryptedCastable && $this->valueEncrypted($value)) {
                $this->attributes[$key] = $value;
            } else {
                $this->setAttribute($key, $value);
            }

            $this->syncOriginalAttribute($key);
        }

        $this->setAttribute(static::getDataColumn(), null);

        $this->dataEncoded = false;
    }

    protected function encodeAttributes(): void
    {
        if ($this->dataEncoded) {
            return;
        }

        $dataColumn = static::getDataColumn();
        $customColumns = static::getCustomColumns();
        $attributes = array_filter($this->getAttributes(), fn ($key): bool => !in_array($key, $customColumns), ARRAY_FILTER_USE_KEY);

        // Remove data column from the attributes
        unset($attributes[$dataColumn]);

        foreach (array_keys($attributes) as $key) {
            // Remove attribute from the model
            unset($this->attributes[$key], $this->original[$key]);
        }

        // Add attribute to the data column
        $this->setAttribute($dataColumn, $attributes);

        $this->dataEncoded = true;
    }

    public function valueEncrypted(string $value): bool
    {
        try {
            Crypt::decryptString($value);

            return true;
        } catch (DecryptException) {
            return false;
        }
    }

    protected function decodeAttributes()
    {
        $this->dataEncoded = true;

        $this->decodeVirtualColumn();
    }

    protected function getAfterListeners(): array
    {
        return [
            'retrieved' => [
                function (): void {
                    // Always decode after model retrieval
                    $this->dataEncoded = true;

                    $this->decodeVirtualColumn();
                },
            ],
            'saving' => [
                [$this, 'encodeAttributes'],
            ],
            'creating' => [
                [$this, 'encodeAttributes'],
            ],
            'updating' => [
                [$this, 'encodeAttributes'],
            ],
        ];
    }

    protected function decodeIfEncoded()
    {
        if ($this->dataEncoded) {
            $this->decodeVirtualColumn();
        }
    }

    protected function fireModelEvent($event, $halt = true)
    {
        $this->decodeIfEncoded();

        $result = parent::fireModelEvent($event, $halt);

        $this->runAfterListeners($event, $halt);

        return $result;
    }

    public function runAfterListeners($event, $halt = true): void
    {
        $listeners = $this->getAfterListeners()[$event] ?? [];

        if (!$event) {
            return;
        }

        foreach ($listeners as $listener) {
            if (is_string($listener)) {
                $listener = app($listener);
                $handle = [$listener, 'handle'];
            } else {
                $handle = $listener;
            }

            $handle($this);
        }
    }

    public function getCasts(): array
    {
        return array_merge(parent::getCasts(), [
            static::getDataColumn() => 'array',
        ]);
    }

    /** Get the name of the column that stores additional data. */
    public static function getDataColumn(): string
    {
        // return 'data';
        return static::data()->data;
    }

    public static function getCustomColumns(): array
    {
        return [
            'id',
        ];
    }

    /**
     * Get a column name for an attribute that can be used in SQL queries.
     *
     * (`foo` or `data->foo` depending on whether `foo` is in custom columns)
     */
    public function getColumnForQuery(string $column): string
    {
        if (in_array($column, static::getCustomColumns(), true)) {
            return $column;
        }

        return static::getDataColumn() . '->' . $column;
    }

    /** Get Data */
    public function data(): MorphOne
    {
        return $this->morphOne($this->getDataModelClassName(), 'model', 'model_type', $this->getModelKeyColumnName());
    }

    protected function getDataTableName(): string
    {
        $modelClass = $this->getDataModelClassName();

        return (new $modelClass())->getTable();
    }

    protected function getModelKeyColumnName(): string
    {
        return config('model-data.model_primary_key_attribute') ?? 'model_id';
    }

    protected function getDataModelClassName(): string
    {
        return config('model-data.data_model');
    }

    protected function getDataAttributeName(): string
    {
        return config('model-data.data_attribute') ?? 'data';
    }

    protected function getDataModelType(): string
    {
        return array_search(static::class, Relation::morphMap()) ?: static::class;
    }
}
