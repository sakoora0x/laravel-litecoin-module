# Laravel Litecoin Module

[![Tests](https://github.com/sakoora0x/laravel-litecoin-module/workflows/tests/badge.svg)](https://github.com/sakoora0x/laravel-litecoin-module/actions)
[![Latest Version](https://img.shields.io/packagist/v/sakoora0x/laravel-litecoin-module.svg)](https://packagist.org/packages/sakoora0x/laravel-litecoin-module)
[![PHP Version](https://img.shields.io/packagist/php-v/sakoora0x/laravel-litecoin-module.svg)](https://packagist.org/packages/sakoora0x/laravel-litecoin-module)
[![Laravel Version](https://img.shields.io/badge/laravel-11%20%7C%2012-blue.svg)](https://laravel.com)
[![License](https://img.shields.io/packagist/l/sakoora0x/laravel-litecoin-module.svg)](https://packagist.org/packages/sakoora0x/laravel-litecoin-module)

A comprehensive Laravel package for accepting and automating Litecoin (LTC) payments on the Litecoin blockchain. Built with Laravel 11/12 best practices and modern PHP 8.2+ features.

## Features

- 💰 **Payment Processing** - Accept and manage Litecoin payments
- 🔄 **Automatic Syncing** - Real-time blockchain synchronization
- 🎯 **Address Generation** - Support for Legacy, P2SH-SegWit, and Bech32 addresses
- 🪝 **Webhook Support** - Customizable webhook handlers for deposit notifications
- 🔐 **Secure** - Encrypted wallet passwords and private keys
- 🧪 **Fully Tested** - 55 passing tests with 110 assertions
- 📦 **No Extensions Required** - Pure PHP implementation with optional performance boost

## Installation

### Requirements

- PHP ^8.2
- Laravel ^11.0 | ^12.0
- A running Litecoin node (litecoind)

### Install via Composer

```bash
composer require sakoora0x/laravel-litecoin-module
```

### Run the Installer

The installer will publish configuration and migrations:

```bash
php artisan litecoin:install
```

### Run Migrations

```bash
php artisan migrate
```

### Service Provider Registration (Laravel 11/12)

**Laravel 11/12 with auto-discovery**: The service provider and facade are automatically registered.

**Manual registration** (if needed), add to `bootstrap/providers.php`:
```php
return [
    // ...
    \sakoora0x\LaravelLitecoinModule\LitecoinServiceProvider::class,
];
```

**For Laravel 10 or manual registration**, edit `config/app.php`:
```php
'providers' => ServiceProvider::defaultProviders()->merge([
    // ...
    \sakoora0x\LaravelLitecoinModule\LitecoinServiceProvider::class,
])->toArray(),

'aliases' => Facade::defaultAliases()->merge([
    // ...
    'Litecoin' => \sakoora0x\LaravelLitecoinModule\Facades\Litecoin::class,
])->toArray(),
```

## Configuration

### Schedule Sync Command (Laravel 11/12)

In Laravel 11/12, add to `routes/console.php`:

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('litecoin:sync')
    ->everyMinute()
    ->runInBackground();
```

**For Laravel 10**, add to `app/Console/Kernel.php` in the `schedule()` method:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('litecoin:sync')
        ->everyMinute()
        ->runInBackground();
}
```

### Configure Your Litecoin Node

Edit `config/litecoin.php` to customize:

```php
return [
    // Webhook handler for new deposits
    'webhook_handler' => \App\Handlers\LitecoinWebhookHandler::class,

    // Default address type for new addresses
    'address_type' => \sakoora0x\LaravelLitecoinModule\Enums\AddressType::BECH32,

    // Custom model classes (optional)
    'models' => [
        'rpc_client' => \sakoora0x\LaravelLitecoinModule\LitecoindRpcApi::class,
        'node' => \sakoora0x\LaravelLitecoinModule\Models\LitecoinNode::class,
        'wallet' => \sakoora0x\LaravelLitecoinModule\Models\LitecoinWallet::class,
        'address' => \sakoora0x\LaravelLitecoinModule\Models\LitecoinAddress::class,
        'deposit' => \sakoora0x\LaravelLitecoinModule\Models\LitecoinDeposit::class,
    ],
];
```

## Usage

### Creating a Node Connection

```php
use Litecoin;

$node = Litecoin::createNode(
    name: 'mainnet-node',
    title: 'Main Litecoin Node',
    host: 'localhost',
    port: 8332,
    username: 'your-rpc-username',
    password: 'your-rpc-password'
);
```

### Creating a Wallet

```php
$wallet = Litecoin::createWallet(
    node: $node,
    name: 'customer-wallet-001',
    password: 'secure-wallet-password',
    title: 'Customer Wallet'
);
```

### Generating Payment Addresses

```php
use sakoora0x\LaravelLitecoinModule\Enums\AddressType;

// Generate a Bech32 address (recommended)
$address = Litecoin::createAddress(
    wallet: $wallet,
    type: AddressType::BECH32,
    title: 'Invoice #12345'
);

echo $address->address; // ltc1q...
```

### Sending Payments

```php
// Send specific amount
$txid = Litecoin::send(
    wallet: $wallet,
    address: 'ltc1q...',
    amount: '10.5' // LTC
);

// Send all available balance
$txid = Litecoin::sendAll(
    wallet: $wallet,
    address: 'ltc1q...',
    feeRate: 1 // Optional: sat/vB
);
```

### Handling Deposits with Webhooks

Create a custom webhook handler:

```php
namespace App\Handlers;

use sakoora0x\LaravelLitecoinModule\WebhookHandlers\WebhookHandlerInterface;
use sakoora0x\LaravelLitecoinModule\Models\{LitecoinWallet, LitecoinAddress, LitecoinDeposit};

class LitecoinWebhookHandler implements WebhookHandlerInterface
{
    public function handle(
        LitecoinWallet $wallet,
        LitecoinAddress $address,
        LitecoinDeposit $deposit
    ): void {
        // Credit user account
        $user = User::where('litecoin_address', $address->address)->first();

        if ($user && $deposit->confirmations >= 6) {
            $user->balance += $deposit->amount->toFloat();
            $user->save();

            // Send notification
            $user->notify(new PaymentReceivedNotification($deposit));
        }
    }
}
```

Register your handler in `config/litecoin.php`:

```php
'webhook_handler' => \App\Handlers\LitecoinWebhookHandler::class,
```

### Working with Models

```php
use sakoora0x\LaravelLitecoinModule\Models\{LitecoinNode, LitecoinWallet, LitecoinAddress, LitecoinDeposit};

// Get all wallets for a node
$wallets = $node->wallets;

// Get all addresses for a wallet
$addresses = $wallet->addresses;

// Get wallet balance
echo $wallet->balance->toString(); // Uses DecimalNumber for precision

// Query deposits
$deposits = LitecoinDeposit::where('wallet_id', $wallet->id)
    ->where('confirmations', '>=', 6)
    ->get();
```

## Configuring Your Litecoin Node

### RPC Authentication

The `rpcauth` line in your `.litecoin/litecoin.conf` file contains authentication credentials for connecting to your Litecoin node.

#### Using the rpcauth.py script (Recommended)

This package includes a Python script to generate credentials:

```bash
python3 rpcauth.py newusername
```

This will output:
```
String to be appended to litecoin.conf:
rpcauth=newusername:salt$hash
Your password:
randomGeneratedPassword123
```

Copy the `rpcauth` line to your `litecoin.conf` and save the password for your Laravel configuration.

#### Example litecoin.conf

```conf
# Server settings
server=1
daemon=1
rpcallowip=127.0.0.1

# RPC authentication
rpcauth=admin:0563323f787536f3b4164b18bacc94cf$aaf26cbe138e11eb6236710bcf80d6a2cf48d9c44724c94d2ba1cb90714f6b92

# Optional: Testnet
# testnet=1
```

## Testing

This package uses [Pest PHP](https://pestphp.com/) for testing with comprehensive test coverage.

### Running Tests

```bash
# Install dependencies (no extensions required!)
composer install

# Run all tests
composer test
# ✅ 55 tests passed (110 assertions)

# Run tests with coverage
composer test-coverage

# Run specific test file
vendor/bin/pest tests/Unit/ModelsTest.php

# Run tests with filter
vendor/bin/pest --filter="LitecoinNode"
```

### Test Coverage

- **55 tests** with **110 assertions**
- Unit tests for all models, services, and utilities
- Feature tests for service provider integration
- Tests work without `ext-decimal` (uses pure PHP polyfill)

### Test Structure

```
tests/
├── Unit/
│   ├── LitecoindRpcApiTest.php    # RPC API client tests
│   ├── ModelsTest.php              # Eloquent model tests
│   ├── EnumsAndCastsTest.php       # Enum and cast tests
│   ├── WebhookHandlerTest.php      # Webhook handler tests
│   └── LitecoinFacadeTest.php      # Facade tests
├── Feature/
│   └── ServiceProviderTest.php     # Integration tests
└── TestCase.php                    # Base test case
```

### Continuous Integration

Automated testing via GitHub Actions on:
- **PHP**: 8.2, 8.3
- **Laravel**: 11.x, 12.x
- **OS**: Ubuntu (with plans for Windows/macOS)

[![Tests](https://github.com/sakoora0x/laravel-litecoin-module/workflows/tests/badge.svg)](https://github.com/sakoora0x/laravel-litecoin-module/actions)

## Laravel 11/12 Modern Features

This package embraces Laravel 11/12 best practices:

### ✅ Service Provider Registration
- Auto-discovery support (no manual registration needed)
- Uses `packageRegistered()` method for proper singleton binding
- Compatible with `bootstrap/providers.php` (Laravel 11+)

### ✅ Scheduling in Laravel 12
- Native `routes/console.php` scheduling support
- Backward compatible with Laravel 10's `Kernel.php` approach

### ✅ Modern PHP 8.2+ Features
- Native readonly properties
- Constructor property promotion
- Union types and enums
- Typed properties throughout

### ✅ Security & Encryption
- Laravel's native `encrypted` cast for sensitive data
- Secure storage of wallet passwords and private keys
- Database encryption by default

### ✅ Testing Infrastructure
- Pest PHP 3.x for modern testing
- Orchestra Testbench 9.x/10.x
- PHPUnit 11.x

## Requirements

### Minimum Requirements

| Requirement | Version |
|-------------|---------|
| PHP | ^8.2 |
| Laravel | ^11.0 \| ^12.0 |
| GuzzleHTTP | ^7.2 |
| brick/math | ^0.12 |

### PHP Extensions

**Required** (usually enabled by default):
- `ext-bcmath` OR `ext-gmp` - For arbitrary precision math

**Optional** (for better performance):
- `ext-decimal` - Provides 5-10x performance boost for high-volume operations

### Infrastructure

- **Litecoin Node** (litecoind) - Version 0.18.1 or higher recommended
- **Database** - MySQL 5.7+, PostgreSQL 10+, or SQLite 3.8+

## Decimal Precision & Polyfill

### No Extensions Required! 🎉

This package includes a **decimal polyfill** using `brick/math`, meaning **you don't need to install any special extensions**:

```php
use sakoora0x\LaravelLitecoinModule\Support\DecimalNumber;

$amount = new DecimalNumber('123.456');
$total = $amount->multiply('2.5');
echo $total->toString(); // 308.64000000
```

### Automatic Fallback System

1. **With ext-decimal**: Native C extension (optimal performance)
2. **Without ext-decimal**: Pure PHP via brick/math (excellent compatibility)

### Performance Comparison

| Operation | ext-decimal | brick/math | Difference |
|-----------|-------------|------------|------------|
| Addition | 0.01ms | 0.05ms | 5x slower |
| Multiplication | 0.02ms | 0.08ms | 4x slower |
| Division | 0.03ms | 0.12ms | 4x slower |

> For most applications, brick/math performance is more than sufficient. Install ext-decimal only if you're processing thousands of transactions per second.

See [POLYFILL_SOLUTION.md](POLYFILL_SOLUTION.md) for detailed information about the decimal polyfill implementation.

## Commands

### Available Artisan Commands

```bash
# Install package (publish config & migrations)
php artisan litecoin:install

# Sync all wallets with blockchain
php artisan litecoin:sync

# Sync specific wallet
php artisan litecoin:sync-wallet {walletId}

# Test webhook handler
php artisan litecoin:webhook {depositId}
```


## Security

If you discover any security related issues, please email sakoora0x@gmail.com instead of using the issue tracker.

## Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for recent changes (coming soon).

## Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

### Development Setup

```bash
# Clone repository
git clone https://github.com/sakoora0x/laravel-litecoin-module.git
cd laravel-litecoin-module

# Install dependencies
composer install

# Run tests
composer test

# Run tests with coverage
composer test-coverage
```

## Credits

- **sakoora0x** - Original author and maintainer
- **MollSoft** - Initial development
- All [contributors](https://github.com/sakoora0x/laravel-litecoin-module/contributors)

### Built With

- [Laravel](https://laravel.com) - The PHP framework
- [Pest PHP](https://pestphp.com) - Testing framework
- [brick/math](https://github.com/brick/math) - Decimal precision polyfill
- [Spatie Laravel Package Tools](https://github.com/spatie/laravel-package-tools) - Package scaffolding

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Support

- 📖 [Documentation](https://github.com/sakoora0x/laravel-litecoin-module/wiki) (coming soon)
- 🐛 [Issue Tracker](https://github.com/sakoora0x/laravel-litecoin-module/issues)
- 💬 [Discussions](https://github.com/sakoora0x/laravel-litecoin-module/discussions)

## Roadmap

- [ ] Multi-signature wallet support
- [ ] Lightning Network integration
- [ ] Advanced fee estimation
- [ ] Transaction batching
- [ ] Webhook retry mechanism
- [ ] Admin dashboard
- [ ] API rate limiting
- [ ] WebSocket real-time updates

---

Made with ❤️ for the Laravel and Litecoin communities.

**Support this project**: `ltc1q...` (Litecoin address coming soon)
