# Presets

Presets are an opinionated coding style for your Laravel application.


## Installation

You may install Presets via Composer:

```bash
composer require zephyrphp/laravel-presets --dev
```

You can install Presets by invoking the `install` command that is available via `artisan`:

```bash
php artisan presets:install
```


## Usage

### Configuration

Presets publish some configuration using the `app/Providers/AppServiceProvider.php` configuration file :

```php
$this->configureCommands();
$this->configureDates();
$this->configureModels();
$this->configurePasswordValidation();
$this->configureUrl();
```

### 


### Tooling

Presets uses a few tools to ensure the code quality and consistency :
- [Pest](https://pestphp.com) is the testing framework,
- [PHPStan](https://phpstan.org) for static analysis, 
- [Laravel Pint](https://laravel.com/docs/11.x/pint) to ensure the code is consistent and follows the Laravel conventions.
- [Rector](https://getrector.org) to ensure the code is up to date with the latest PHP version.

You run these tools using the following commands:

```bash
# Lint using Pint
composer code:lint

# Refactor using Rector
composer code:refactor

# Analyse using PHPStan
composer code:analyse

# Test using Pest
composer code:test

# Run all the test using Pint, Rector, PHPStan & Pest
composer test
```


## Authors

- [Fabrice Planchette](https://www.github.com/fabpl)

