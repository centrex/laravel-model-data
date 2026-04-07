# Add virtual columns to any Eloquent model

[![Latest Version on Packagist](https://img.shields.io/packagist/v/centrex/laravel-model-data.svg?style=flat-square)](https://packagist.org/packages/centrex/laravel-model-data)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/centrex/laravel-model-data/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/centrex/laravel-model-data/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/centrex/laravel-model-data/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/centrex/laravel-model-data/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/centrex/laravel-model-data?style=flat-square)](https://packagist.org/packages/centrex/laravel-model-data)

Serialize arbitrary attributes that don't have a dedicated column into a single JSON `data` column via a polymorphic `model_datas` table. Attributes are transparently encoded on save and decoded on retrieval — models behave as if the virtual columns exist natively.

## Installation

```bash
composer require centrex/laravel-model-data
php artisan vendor:publish --tag="model-data-migrations"
php artisan migrate
```

## Usage

### 1. Add the trait to your model

```php
use Centrex\ModelData\HasData;

class Product extends Model
{
    use HasData;
}
```

### 2. Set and get virtual attributes

```php
$product = Product::create(['name' => 'Widget']);

// Set virtual attributes (stored in model_datas table as JSON)
$product->color  = 'red';
$product->weight = 1.5;
$product->save();

// Retrieve — fully transparent, works like regular attributes
$product = Product::find($product->id);
echo $product->color;   // 'red'
echo $product->weight;  // 1.5
```

### 3. Query virtual attributes

```php
// getColumnForQuery() returns the correct SQL expression
$column = $product->getColumnForQuery('color'); // → 'data->color'

Product::whereRaw("{$column} = ?", ['red'])->get();
```

### 4. Customize the real columns

Override `getCustomColumns()` to list columns that exist as real database columns (not stored in the JSON blob):

```php
class Product extends Model
{
    use HasData;

    public static function getCustomColumns(): array
    {
        return ['id', 'name', 'price', 'created_at', 'updated_at'];
    }
}
```

Any attribute not in this list is automatically routed through the `data` JSON column.

### 5. Data types

Supported cast types via `DataType` enum: `string`, `integer`, `float`, `boolean`, `array`, `json`.

```php
protected $casts = [
    'is_featured' => 'boolean',
    'tags'        => 'array',
];
```

Casts work normally — the trait intercepts encode/decode at the Eloquent event level.

## Testing

```bash
composer test        # full suite
composer test:unit   # pest only
composer test:types  # phpstan
composer lint        # pint
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [centrex](https://github.com/centrex)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
