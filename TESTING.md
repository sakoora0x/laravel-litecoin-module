# Testing Guide

This document provides comprehensive information about testing the Laravel Litecoin Module.

## Table of Contents

- [Overview](#overview)
- [Setup](#setup)
- [Running Tests](#running-tests)
- [Test Structure](#test-structure)
- [Writing Tests](#writing-tests)
- [Continuous Integration](#continuous-integration)

## Overview

This package uses [Pest PHP](https://pestphp.com/), a delightful testing framework with a focus on simplicity. The test suite includes:

- **Unit Tests**: Test individual classes and methods in isolation
- **Feature Tests**: Test package integration with Laravel
- **Model Tests**: Test Eloquent models and relationships
- **Integration Tests**: Test RPC API communication

## Setup

### Requirements

- PHP 8.2 or higher
- Composer
- ext-decimal PHP extension

### Installation

```bash
# Clone the repository
git clone https://github.com/sakoora0x/laravel-litecoin-module.git
cd laravel-litecoin-module

# Install dependencies
composer install
```

## Running Tests

### Basic Usage

```bash
# Run all tests
composer test

# Or use Pest directly
vendor/bin/pest
```

### With Coverage

```bash
# Run tests with coverage report
composer test-coverage

# Or with Pest directly
vendor/bin/pest --coverage
```

### Filtering Tests

```bash
# Run specific test file
vendor/bin/pest tests/Unit/ModelsTest.php

# Run tests matching a pattern
vendor/bin/pest --filter="LitecoinNode"

# Run tests in a directory
vendor/bin/pest tests/Unit/
```

### Verbose Output

```bash
# Show more details
vendor/bin/pest -v

# Show even more details
vendor/bin/pest -vv
```

## Test Structure

```
tests/
├── Pest.php                      # Pest configuration
├── TestCase.php                  # Base test case
├── Unit/                         # Unit tests
│   ├── LitecoindRpcApiTest.php  # RPC API tests
│   ├── ModelsTest.php           # Model tests
│   ├── EnumsAndCastsTest.php    # Enum and Cast tests
│   ├── WebhookHandlerTest.php   # Webhook handler tests
│   └── LitecoinFacadeTest.php   # Facade tests
└── Feature/                      # Feature tests
    └── ServiceProviderTest.php  # Service provider tests
```

## Writing Tests

### Test Anatomy

Pest uses a simple, expressive syntax:

```php
test('it can create a node', function () {
    $node = LitecoinNode::create([
        'name' => 'test-node',
        'host' => 'localhost',
        'port' => 8332,
    ]);

    expect($node)->toBeInstanceOf(LitecoinNode::class)
        ->and($node->name)->toBe('test-node');
});
```

### Using Describe Blocks

Group related tests:

```php
describe('LitecoinNode Model', function () {
    test('it can be created', function () {
        // test implementation
    });

    test('it has wallets relationship', function () {
        // test implementation
    });
});
```

### Using Hooks

```php
beforeEach(function () {
    // Runs before each test
    $this->user = User::factory()->create();
});

afterEach(function () {
    // Runs after each test
    Mockery::close();
});
```

### Database Testing

Use RefreshDatabase trait:

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it can create a wallet', function () {
    $node = LitecoinNode::create([...]);
    $wallet = $node->wallets()->create([...]);

    expect($wallet)->toBeInstanceOf(LitecoinWallet::class);
});
```

### Mocking

Use Mockery for mocking:

```php
use Mockery as m;

test('it makes API request', function () {
    $mockClient = m::mock(Client::class);
    $mockClient->shouldReceive('post')
        ->once()
        ->andReturn($mockResponse);

    // test implementation
});
```

### Expectations

Pest provides many expectations:

```php
expect($value)
    ->toBe($expected)           // ===
    ->toEqual($expected)        // ==
    ->toBeTrue()               // === true
    ->toBeFalse()              // === false
    ->toBeNull()               // === null
    ->toBeInstanceOf(Class::class)
    ->toHaveCount(3)
    ->toContain('value')
    ->toHaveKey('key')
    ->toBeArray()
    ->toBeString()
    ->toBeInt();
```

## Test Coverage

### Running Coverage Reports

```bash
# Text coverage report
vendor/bin/pest --coverage

# Minimum coverage threshold
vendor/bin/pest --coverage --min=80

# HTML coverage report
vendor/bin/pest --coverage-html coverage
```

### Coverage Goals

- Overall: 80% minimum
- Unit Tests: 90% minimum
- Feature Tests: 80% minimum

## Continuous Integration

### GitHub Actions

Tests run automatically on:

- Every push to main branch
- Every pull request
- Multiple PHP versions (8.2, 8.3)
- Multiple Laravel versions (11.x, 12.x)

View workflow: `.github/workflows/tests.yml`

### Local CI Testing

Test against different Laravel versions:

```bash
# Laravel 11
composer require "laravel/framework:^11.0" --dev --no-update
composer update
vendor/bin/pest

# Laravel 12
composer require "laravel/framework:^12.0" --dev --no-update
composer update
vendor/bin/pest
```

## Best Practices

1. **Write Descriptive Test Names**: Use natural language
   ```php
   test('it validates litecoin address format')
   ```

2. **One Assertion Per Test**: Keep tests focused
   ```php
   test('it casts balance to Decimal', function () {
       $wallet = Wallet::create(['balance' => '100.50']);
       expect($wallet->balance)->toBeInstanceOf(Decimal::class);
   });
   ```

3. **Use Factories**: Create test data consistently
   ```php
   $node = LitecoinNode::factory()->create();
   ```

4. **Test Edge Cases**: Don't just test the happy path
   ```php
   test('it handles null password', function () {
       $node = LitecoinNode::create(['password' => null]);
       expect($node->password)->toBeNull();
   });
   ```

5. **Keep Tests Fast**: Use mocks for external services
   ```php
   $mockApi = m::mock(LitecoindRpcApi::class);
   ```

6. **Clean Up After Tests**: Use transactions or RefreshDatabase
   ```php
   uses(RefreshDatabase::class);
   ```

## Troubleshooting

### Common Issues

**Issue**: Tests fail with "Class not found"
```bash
# Solution: Regenerate autoload
composer dump-autoload
```

**Issue**: Database errors
```bash
# Solution: Ensure migrations are running
php artisan migrate:fresh --env=testing
```

**Issue**: ext-decimal not found
```bash
# Solution: Install decimal extension
pecl install decimal
```

## Contributing

When contributing tests:

1. Ensure all tests pass
2. Maintain or improve coverage
3. Follow existing test patterns
4. Add tests for new features
5. Update this guide if needed

## Resources

- [Pest Documentation](https://pestphp.com/docs)
- [PHPUnit Documentation](https://phpunit.de/)
- [Laravel Testing](https://laravel.com/docs/testing)
- [Mockery Documentation](http://docs.mockery.io/)
