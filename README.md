# Add virtual columns in any model of laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/centrex/laravel-model-data.svg?style=flat-square)](https://packagist.org/packages/centrex/laravel-model-data)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/centrex/laravel-model-data/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/centrex/laravel-model-data/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/centrex/laravel-model-data/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/centrex/laravel-model-data/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/centrex/laravel-model-data?style=flat-square)](https://packagist.org/packages/centrex/laravel-model-data)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Contents

- [Installation](#installation)
- [Usage Examples](#usage)
- [Testing](#testing)
- [Changelog](#changelog)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)

## Installation

You can install the package via composer:

```bash
composer require centrex/laravel-model-data
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="model-data-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="model-data-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="model-data-views"
```

## Usage

```php
$laravelModelData = new Centrex\ModelData();
echo $laravelModelData->echoPhrase('Hello, Centrex!');
```

## Testing

🧹 Keep a modern codebase with **Pint**:
```bash
composer lint
```

✅ Run refactors using **Rector**
```bash
composer refacto
```

⚗️ Run static analysis using **PHPStan**:
```bash
composer test:types
```

✅ Run unit tests using **PEST**
```bash
composer test:unit
```

🚀 Run the entire test suite:
```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [centrex](https://github.com/centrex)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
