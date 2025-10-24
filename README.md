# Laravel Litecoin Module

Organization of payment acceptance and automation of payments of LTC coins on the Litecoin blockchain.

### Installation

You can install the package via composer:
```bash
composer require sakoora0x/laravel-litecoin-module
```

After you can run installer using command:
```bash
php artisan litecoin:install
```

And run migrations:
```bash
php artisan migrate
```

Register Service Provider and Facade in app, edit `config/app.php`:
```php
'providers' => ServiceProvider::defaultProviders()->merge([
    ...,
    \sakoora0x\LaravelLitecoinModule\LitecoinServiceProvider::class,
])->toArray(),

'aliases' => Facade::defaultAliases()->merge([
    ...,
    'Litecoin' => \sakoora0x\LaravelLitecoinModule\Facades\Litecoin::class,
])->toArray(),
```

Add cron job, in file `app/Console/Kernel` in method `schedule(Schedule $schedule)` add
```
$schedule->command('litecoin:sync')
    ->everyMinute()
    ->runInBackground();
```

### Configuring RPC Authentication

The `rpcauth` line in your `.litecoin/litecoin.conf` file contains authentication credentials for connecting to your Litecoin node. To create a different login, you need to generate a new `rpcauth` string.

#### Using the rpcauth.py script (Recommended)

This package includes a Python script to generate these credentials:

1. Generate new credentials (replace `newusername` with your desired username):
```bash
python3 rpcauth.py newusername
```

This will output something like:
```
String to be appended to litecoin.conf:
rpcauth=newusername:salt$hash
Your password:
randomGeneratedPassword123
```

3. Copy the `rpcauth` line to your config file and save the password - you'll need it to connect.

#### Manual generation

If you want to generate it manually, the format is:
```
rpcauth=username:salt$hmac_sha256(salt, password)
```

## Testing

This package uses [Pest PHP](https://pestphp.com/) for testing. The test suite includes unit tests, feature tests, and integration tests.

### Requirements

- PHP 8.2 or higher
- ext-decimal extension

### Running Tests

```bash
# Install dependencies
composer install

# Run all tests
composer test

# Run tests with coverage
composer test-coverage

# Run specific test file
vendor/bin/pest tests/Unit/ModelsTest.php

# Run tests with filter
vendor/bin/pest --filter="LitecoinNode"
```

### Test Structure

- `tests/Unit/` - Unit tests for individual classes and components
  - `LitecoindRpcApiTest.php` - Tests for RPC API client
  - `ModelsTest.php` - Tests for Eloquent models
  - `EnumsAndCastsTest.php` - Tests for enums and custom casts
  - `WebhookHandlerTest.php` - Tests for webhook handlers
- `tests/Feature/` - Feature tests for package integration
  - `ServiceProviderTest.php` - Tests for service provider registration
- `tests/TestCase.php` - Base test case with Orchestra Testbench setup

### Continuous Integration

This package uses GitHub Actions for automated testing. Tests are run on:
- PHP 8.2 and 8.3
- Laravel 11.x and 12.x
- Multiple OS environments (Ubuntu)

View the test results in the [Actions tab](../../actions) of the GitHub repository.

## Laravel 11/12 Compatibility

This package is fully compatible with Laravel 11 and Laravel 12:

- Uses modern PHP 8.2+ features
- Follows Laravel 11/12 conventions and best practices
- Service provider uses `packageRegistered()` method for proper dependency injection
- Supports Laravel's native encrypted casting
- Compatible with Laravel's datetime casting
- Uses Spatie Laravel Package Tools for package scaffolding

## Requirements

- PHP ^8.2
- Laravel ^11.0 | ^12.0
- GuzzleHTTP ^7.2
- brick/math (automatically installed, for precise decimal calculations)
- ext-bcmath or ext-gmp (usually enabled by default)

### Optional Requirements

- ext-decimal - Provides better performance for decimal calculations (recommended for high-volume production use)

> **Note:** This package includes a decimal polyfill using `brick/math`, so `ext-decimal` is **not required**. The package will work perfectly without it using pure PHP mathematics. See [POLYFILL_SOLUTION.md](POLYFILL_SOLUTION.md) for more details.
