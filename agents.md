# agents.md

## Agent Guidance — laravel-model-data

### Package Purpose
Adds arbitrary key-value data storage to any Eloquent model via the `HasData` trait. Think of it as virtual columns — store any extra data without schema changes.

### Before Making Changes
- Read `src/HasData.php` — the primary interface for host models
- Read `src/Data.php` — the underlying storage model
- Check `src/Events/` — events may fire on data set/delete
- Check `src/Exceptions/` — understand what exceptions are thrown and when

### Common Tasks

**Adding typed data retrieval**
- `getData('key')` returns raw stored value
- If adding a typed getter, add it to `HasData.php` and ensure it handles null gracefully
- Consider adding a `getDataOrDefault('key', $default)` variant if not already present

**Adding event hooks**
- Events in `src/Events/` fire when data is set or deleted
- When adding a new event, register its listener in the service provider
- Keep events serializable (no closures, only model IDs in payload)

**Improving storage performance**
- All data is stored in a single `model_data` table with `model_type`, `model_id`, `key`, `value`
- Add a composite index on `(model_type, model_id, key)` if not present — check migrations first
- Caching frequently read data keys should be opt-in via config, not default

### Testing
```sh
composer test:unit        # pest
composer test:types       # phpstan
composer test:lint        # pint
```

Test the trait on a test model:
```php
$model = TestModel::create();
$model->setData('color', 'blue');
expect($model->getData('color'))->toBe('blue');
$model->deleteData('color');
expect($model->getData('color'))->toBeNull();
```

### Safe Operations
- Adding new methods to `HasData.php`
- Adding nullable columns to the `model_data` migration
- Adding or updating events
- Adding tests

### Risky Operations — Confirm Before Doing
- Renaming `model_type` / `model_id` / `key` columns (polymorphic keys used by all host models)
- Changing how values are serialized/deserialized (breaks existing stored data)
- Removing the `deleteData()` method from the trait

### Do Not
- Store large binary blobs as data values — this table is for small scalars
- Auto-cast all values to JSON without an opt-in flag (breaks string comparisons)
- Skip `declare(strict_types=1)` in any new file
