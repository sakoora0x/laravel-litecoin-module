# Decimal Polyfill Solution

## Problem

The package originally required the `ext-decimal` PHP extension, which is not available by default and can be difficult to install, especially on Laravel Herd and other development environments.

## Solution

We've implemented a **Decimal Polyfill** using [brick/math](https://github.com/brick/math), which is a pure PHP library for arbitrary precision mathematics. This allows the package to work without requiring any PHP extensions beyond the commonly available `bcmath` or `gmp`.

## How It Works

### DecimalNumber Wrapper Class

We created a `DecimalNumber` class that automatically detects and uses whichever decimal implementation is available:

```php
use sakoora0x\LaravelLitecoinModule\Support\DecimalNumber;

// Works with or without ext-decimal!
$amount = new DecimalNumber('123.456');
$balance = $amount->add('50.5');
echo $balance->toString(); // 173.95600000
```

### Automatic Fallback

The `DecimalNumber` class will:
1. **Use `ext-decimal`** if it's installed (best performance)
2. **Fall back to `brick/math`** (uses bcmath/gmp) if ext-decimal is not available

### Supported Operations

- `add()` - Addition
- `subtract()` - Subtraction
- `multiply()` - Multiplication
- `divide()` - Division
- `isGreaterThan()` - Comparison
- `isLessThan()` - Comparison
- `equals()` - Equality check
- `toString()` - Convert to string
- `toFloat()` - Convert to float

### Example Usage

```php
use sakoora0x\LaravelLitecoinModule\Support\DecimalNumber;

// Create decimal numbers
$price = new DecimalNumber('99.99');
$quantity = new DecimalNumber('3');

// Perform calculations
$total = $price->multiply($quantity);
echo $total->toString(); // 299.97000000

// Comparisons
if ($total->isGreaterThan('250')) {
    echo "High value transaction!";
}

// Works with models automatically via DecimalCast
$wallet = LitecoinWallet::find(1);
echo $wallet->balance->toString(); // Uses DecimalNumber automatically
```

## Benefits

1. **No Extension Required** - Works out of the box without ext-decimal
2. **Automatic Detection** - Uses ext-decimal if available for better performance
3. **Precise Calculations** - Maintains 8 decimal places for cryptocurrency amounts
4. **Compatible** - Drop-in replacement for the original Decimal class
5. **Pure PHP** - Uses brick/math which only requires bcmath or gmp (usually enabled by default)

## Performance

- **With ext-decimal**: Optimal performance (native C extension)
- **With brick/math**: Good performance (pure PHP using bcmath/gmp)

For production environments handling high transaction volumes, installing ext-decimal is still recommended for best performance.

## Installation

### Dependencies Are Automatic

When you install the package, `brick/math` is automatically installed:

```bash
composer require sakoora0x/laravel-litecoin-module
```

### Optional: Install ext-decimal for Better Performance

If you want optimal performance, you can install the extension:

```bash
# Using PECL
pecl install decimal

# Or via your system's package manager
# Ubuntu/Debian:
sudo apt-get install php-decimal

# macOS (Homebrew):
brew install php-decimal
```

## Testing

All tests pass with or without ext-decimal:

```bash
composer test
# ✅ 55 tests passed (110 assertions)
```

## Migration from ext-decimal

If you were previously using ext-decimal directly in your code:

### Before:
```php
use Decimal\Decimal;

$amount = new Decimal('100.50', 8);
```

### After:
```php
use sakoora0x\LaravelLitecoinModule\Support\DecimalNumber;

$amount = new DecimalNumber('100.50', 8);
// API is mostly the same!
```

## Technical Details

### Why brick/math?

- **Well-maintained**: Active development and updates
- **Comprehensive**: Supports all operations needed for financial calculations
- **Reliable**: Used by many production Laravel applications
- **Automatic**: Uses bcmath or gmp extensions (usually enabled by default)
- **Type-safe**: Full PHP 8.2+ type hints

### Precision

All decimal operations maintain 8 decimal places by default, which is perfect for cryptocurrency amounts (Litecoin uses 8 decimal places).

## Backward Compatibility

The `DecimalCast` has been updated to work with both:
- `DecimalNumber` (new)
- `Decimal\Decimal` (legacy, if ext-decimal is installed)

This ensures backward compatibility if you have existing code using the extension directly.
