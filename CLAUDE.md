# CLAUDE.md

## Package Overview

`centrex/laravel-model-data` — Polymorphic key-value data storage for any Eloquent model (virtual columns).

Namespace: `Centrex\ModelData\`  
Service Provider: `ModelDataServiceProvider`  
Facade: `Facades/Data`  
Trait: `HasData`

## Commands

Run from inside this directory (`cd laravel-model-data`):

```sh
composer install          # install dependencies
composer test             # full suite: rector dry-run, pint check, phpstan, pest
composer test:unit        # pest tests only
composer test:lint        # pint style check (read-only)
composer test:types       # phpstan static analysis
composer test:refacto     # rector refactor check (read-only)
composer lint             # apply pint formatting
composer refacto          # apply rector refactors
composer analyse          # phpstan (alias)
composer build            # prepare testbench workbench
composer start            # build + serve testbench dev server
```

Run a single test:
```sh
vendor/bin/pest tests/ExampleTest.php
vendor/bin/pest --filter "test name"
```

## Structure

```
src/
  Data.php
  ModelDataServiceProvider.php
  HasData.php                     # Trait — add to any model
  Facades/
  Commands/
  Events/
  Exceptions/
config/config.php
database/migrations/
tests/
workbench/
```

## Usage

```php
use Centrex\ModelData\HasData;

class User extends Model
{
    use HasData;
}

// Store arbitrary key-value data on the model
$user->setData('theme', 'dark');
$value = $user->getData('theme');
```

## Conventions

- PHP 8.2+, `declare(strict_types=1)` in all files
- Pest for tests, snake_case test names
- Pint with `laravel` preset
- Rector targeting PHP 8.3 with `CODE_QUALITY`, `DEAD_CODE`, `EARLY_RETURN`, `TYPE_DECLARATION`, `PRIVATIZATION` sets
- PHPStan at level `max` with Larastan
