# Laravel Model Data

[![Latest Version on Packagist](https://img.shields.io/packagist/v/centrex/laravel-model-data.svg?style=flat-square)](https://packagist.org/packages/centrex/laravel-model-data)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/centrex/laravel-model-data/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/centrex/laravel-model-data/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/centrex/laravel-model-data/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/centrex/laravel-model-data/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/centrex/laravel-model-data?style=flat-square)](https://packagist.org/packages/centrex/laravel-model-data)

Serialize arbitrary attributes that don’t have dedicated columns into a JSON `data` field stored in a polymorphic `model_datas` table.

This package allows you to:

- Store flexible, schema-less data per model
- Access virtual attributes like native Eloquent properties
- Keep your core tables clean while supporting dynamic fields
- Use deterministic 32-character keys for efficient indexing

## ✨ Features
- Transparent virtual attributes ($model->color)
- External storage via polymorphic model_datas table
- Deterministic 32-char hashed keys (md5)
- Supports multiple data namespaces via data_type
- Fully compatible with Eloquent casting
- Query support for JSON attributes

## 📦 Installation

```bash
composer require centrex/laravel-model-data
php artisan vendor:publish --tag="model-data-migrations"
php artisan migrate
```

## Usage

### 1. Add the trait to your model

```php
use Centrex\ModelData\Concerns\HasModelData;

class Product extends Model
{
    use HasModelData;
}
```

### 2. Virtual Attributes (Transparent Mode)

```php
$product = Product::create(['name' => 'Widget']);

// Assign attributes that don't exist as columns
$product->color = 'red';
$product->weight = 1.5;
$product->is_featured = true;

$product->save();
```

// Retrieve — fully transparent, works like regular attributes
```php
$product = Product::find(1);

echo $product->color;       // red
echo $product->weight;      // 1.5
echo $product->is_featured; // true
```

### 3. Structured Data (Explicit Mode)

You can also store grouped data explicitly:
```php
$product->putData('settings', [
    'currency' => 'USD',
    'stock_alert' => true,
]);

$product->putData('seo', [
    'title' => 'Best Product',
    'description' => 'Top quality item',
]);
```
Retrieve
```php
$settings = $product->getData('settings');

$currency = $product->getDataValue('settings', 'currency');
```
### 4. Check / Delete
```php
$product->hasData('settings');     // true
$product->forgetData('settings');  // deletes record
```
### 5. Query Virtual Attributes
```php
$column = (new Product())->getColumnForQuery('color');

Product::whereRaw("{$column} = ?", ['red'])->get();
```
### 6. Casting Support

Works seamlessly with Laravel casts:
```php
protected $casts = [
    'is_featured' => 'boolean',
    'tags' => 'array',
];
```
## ⚙️ How It Works
### Storage Model

Each record in `model_datas` represents:
```
model_type + model_id + data_type → unique key (md5)
```
Example:
```
Product|15|settings → md5 → 32-char key
```
### Data Structure
```json
{
  "color": "red",
  "weight": 1.5,
  "is_featured": true
}
```
## 🧠 Design Modes
### 1. Transparent Mode (default)
- Works like native attributes
- Best for dynamic fields
```php
$product->color = 'red';
```
### 2. Explicit Mode (recommended for structure)
- Namespaced data storage
- Better for large systems (ERP, SaaS)
```php
$product->putData('settings', [...]);
```
## 🏗️ Advanced Usage
### Multiple Data Types
```php
$product->putData('pricing', [...]);
$product->putData('inventory', [...]);
$product->putData('analytics', [...]);
```
Each stored separately but linked to the same model.

### Access Nested Values
```php
$product->getDataValue('settings', 'notifications.email');
```
## ⚡ Performance Notes
- Primary key is fixed 32-char string
- Indexed for fast lookup
- No joins required for simple access
- JSON queries supported natively (MySQL / PostgreSQL)
## ⚠️ Best Practices
- Prefer explicit mode for complex systems
- Avoid overloading with deeply nested JSON
- Keep frequently queried fields as real columns
## 🧪 Testing

```bash
composer test        # full suite
composer test:unit   # pest only
composer test:types  # phpstan
composer lint        # pint
```
## 🔄 Migration Strategy (from inline JSON)

If you're migrating from a data column:

1. Move JSON to model_datas
2. Drop old column
3. Add trait
4. Done

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [centrex](https://github.com/centrex)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
