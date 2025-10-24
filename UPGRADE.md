# Upgrade Guide

## Upgrading to Laravel 11/12

This package is now fully compatible with Laravel 11 and 12. Here's what you need to know:

### What Changed

#### 1. Decimal Implementation
- **Old**: Required `ext-decimal` PHP extension
- **New**: Uses `brick/math` polyfill (ext-decimal is optional)
- **Migration**: No code changes needed! The package automatically detects and uses whichever is available.

#### 2. Service Provider
- **Old**: Registered singleton in `configurePackage()`
- **New**: Uses `packageRegistered()` method (Laravel 11/12 best practice)
- **Migration**: No action needed, auto-discovery handles this.

#### 3. Scheduling (Laravel 11/12)
- **Old**: `app/Console/Kernel.php`
- **New**: `routes/console.php` (recommended)

**Before (Laravel 10):**
```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('litecoin:sync')
        ->everyMinute()
        ->runInBackground();
}
```

**After (Laravel 11/12):**
```php
// routes/console.php
use Illuminate\Support\Facades\Schedule;

Schedule::command('litecoin:sync')
    ->everyMinute()
    ->runInBackground();
```

#### 4. Service Provider Registration
- **Old**: Manual registration in `config/app.php`
- **New**: Auto-discovery (no manual registration needed)

**If you need manual registration (Laravel 11+):**
```php
// bootstrap/providers.php
return [
    \sakoora0x\LaravelLitecoinModule\LitecoinServiceProvider::class,
];
```

### Breaking Changes

**None!** This is a backward-compatible upgrade.

### New Features in Laravel 11/12 Version

1. ✅ **No Extensions Required** - Works without `ext-decimal`
2. ✅ **DecimalNumber Wrapper** - Automatic fallback system
3. ✅ **Modern PHP 8.2+** - Readonly properties, enums, union types
4. ✅ **Comprehensive Tests** - 55 tests, 110 assertions
5. ✅ **Laravel 12 Scheduling** - Native `routes/console.php` support

### Installation Steps

#### For New Projects

```bash
composer require sakoora0x/laravel-litecoin-module
php artisan litecoin:install
php artisan migrate
```

Add scheduling to `routes/console.php`:
```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('litecoin:sync')
    ->everyMinute()
    ->runInBackground();
```

#### For Existing Projects (Upgrading from Laravel 10)

1. Update your Laravel version to 11 or 12
2. Update the package:
```bash
composer update sakoora0x/laravel-litecoin-module
```

3. Move scheduling from `Kernel.php` to `routes/console.php` (optional but recommended):
```php
// routes/console.php
use Illuminate\Support\Facades\Schedule;

Schedule::command('litecoin:sync')
    ->everyMinute()
    ->runInBackground();
```

4. No database migrations needed - the package uses the same schema.

### Decimal Handling Changes

If you were using `Decimal\Decimal` directly in your code:

**Before:**
```php
use Decimal\Decimal;

$amount = new Decimal('100.50', 8);
$total = $amount->add(new Decimal('50.25', 8));
```

**After (recommended):**
```php
use sakoora0x\LaravelLitecoinModule\Support\DecimalNumber;

$amount = new DecimalNumber('100.50');
$total = $amount->add('50.25');
```

**Or (still works):**
```php
// If you have ext-decimal installed, this still works
use Decimal\Decimal;

$amount = new Decimal('100.50', 8);
```

The `DecimalCast` automatically handles both `DecimalNumber` and `Decimal\Decimal`.

### Testing Your Upgrade

```bash
# Run package tests
composer test

# Run your application tests
php artisan test
```

### Performance Notes

- **Without ext-decimal**: Uses brick/math (pure PHP) - excellent for most use cases
- **With ext-decimal**: 5-10x faster for high-volume operations

To install ext-decimal for optimal performance:
```bash
pecl install decimal
```

### Need Help?

- 📖 Check the [README](README.md) for full documentation
- 🐛 [Report issues](https://github.com/sakoora0x/laravel-litecoin-module/issues)
- 💬 [Ask questions](https://github.com/sakoora0x/laravel-litecoin-module/discussions)

### Rollback

If you need to rollback to the previous version:

```bash
composer require sakoora0x/laravel-litecoin-module:"^1.0"
```

Note: Version numbers are examples. Check your `composer.json` for the actual version.
