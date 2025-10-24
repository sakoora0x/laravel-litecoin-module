<?php

use sakoora0x\LaravelLitecoinModule\Support\DecimalNumber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use sakoora0x\LaravelLitecoinModule\Enums\AddressType;
use sakoora0x\LaravelLitecoinModule\Models\LitecoinAddress;
use sakoora0x\LaravelLitecoinModule\Models\LitecoinDeposit;
use sakoora0x\LaravelLitecoinModule\Models\LitecoinNode;
use sakoora0x\LaravelLitecoinModule\Models\LitecoinWallet;

uses(RefreshDatabase::class);

describe('LitecoinNode Model', function () {
    test('it can be created with valid attributes', function () {
        $node = LitecoinNode::create([
            'name' => 'test-node',
            'title' => 'Test Node',
            'host' => 'localhost',
            'port' => 8332,
            'username' => 'testuser',
            'password' => 'testpass',
        ]);

        expect($node)->toBeInstanceOf(LitecoinNode::class)
            ->and($node->name)->toBe('test-node')
            ->and($node->title)->toBe('Test Node')
            ->and($node->host)->toBe('localhost')
            ->and($node->port)->toBe(8332)
            ->and($node->username)->toBe('testuser');
    });

    test('it hides password attribute', function () {
        $node = LitecoinNode::create([
            'name' => 'test-node',
            'title' => 'Test Node',
            'host' => 'localhost',
            'port' => 8332,
            'username' => 'testuser',
            'password' => 'testpass',
        ]);

        $array = $node->toArray();

        expect($array)->not->toHaveKey('password');
    });

    test('it casts port to integer', function () {
        $node = LitecoinNode::create([
            'name' => 'test-node',
            'title' => 'Test Node',
            'host' => 'localhost',
            'port' => '8332',
            'username' => 'testuser',
            'password' => 'testpass',
        ]);

        expect($node->port)->toBeInt();
    });

    test('it has wallets relationship', function () {
        $node = LitecoinNode::create([
            'name' => 'test-node',
            'title' => 'Test Node',
            'host' => 'localhost',
            'port' => 8332,
            'username' => 'testuser',
            'password' => 'testpass',
        ]);

        expect($node->wallets())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
    });

    test('it can create an api instance', function () {
        $node = LitecoinNode::create([
            'name' => 'test-node',
            'title' => 'Test Node',
            'host' => 'localhost',
            'port' => 8332,
            'username' => 'testuser',
            'password' => 'testpass',
        ]);

        $api = $node->api();

        expect($api)->toBeInstanceOf(\sakoora0x\LaravelLitecoinModule\LitecoindRpcApi::class);
    });
});

describe('LitecoinWallet Model', function () {
    test('it can be created with valid attributes', function () {
        $node = LitecoinNode::create([
            'name' => 'test-node',
            'title' => 'Test Node',
            'host' => 'localhost',
            'port' => 8332,
            'username' => 'testuser',
            'password' => 'testpass',
        ]);

        $wallet = $node->wallets()->create([
            'name' => 'test-wallet',
            'title' => 'Test Wallet',
            'password' => 'walletpass',
        ]);

        expect($wallet)->toBeInstanceOf(LitecoinWallet::class)
            ->and($wallet->name)->toBe('test-wallet')
            ->and($wallet->title)->toBe('Test Wallet');
    });

    test('it hides password attribute', function () {
        $node = LitecoinNode::create([
            'name' => 'test-node',
            'title' => 'Test Node',
            'host' => 'localhost',
            'port' => 8332,
            'username' => 'testuser',
            'password' => 'testpass',
        ]);

        $wallet = $node->wallets()->create([
            'name' => 'test-wallet',
            'title' => 'Test Wallet',
            'password' => 'walletpass',
        ]);

        $array = $wallet->toArray();

        expect($array)->not->toHaveKey('password');
    });

    test('it casts balance to Decimal', function () {
        $node = LitecoinNode::create([
            'name' => 'test-node',
            'title' => 'Test Node',
            'host' => 'localhost',
            'port' => 8332,
            'username' => 'testuser',
            'password' => 'testpass',
        ]);

        $wallet = $node->wallets()->create([
            'name' => 'test-wallet',
            'title' => 'Test Wallet',
            'balance' => '100.50',
        ]);

        expect($wallet->balance)->toBeInstanceOf(DecimalNumber::class);
    });

    test('it has node relationship', function () {
        $node = LitecoinNode::create([
            'name' => 'test-node',
            'title' => 'Test Node',
            'host' => 'localhost',
            'port' => 8332,
            'username' => 'testuser',
            'password' => 'testpass',
        ]);

        $wallet = $node->wallets()->create([
            'name' => 'test-wallet',
            'title' => 'Test Wallet',
        ]);

        expect($wallet->node)->toBeInstanceOf(LitecoinNode::class)
            ->and($wallet->node->id)->toBe($node->id);
    });

    test('it has addresses relationship', function () {
        $node = LitecoinNode::create([
            'name' => 'test-node',
            'title' => 'Test Node',
            'host' => 'localhost',
            'port' => 8332,
            'username' => 'testuser',
            'password' => 'testpass',
        ]);

        $wallet = $node->wallets()->create([
            'name' => 'test-wallet',
            'title' => 'Test Wallet',
        ]);

        expect($wallet->addresses())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
    });

    test('it has deposits relationship', function () {
        $node = LitecoinNode::create([
            'name' => 'test-node',
            'title' => 'Test Node',
            'host' => 'localhost',
            'port' => 8332,
            'username' => 'testuser',
            'password' => 'testpass',
        ]);

        $wallet = $node->wallets()->create([
            'name' => 'test-wallet',
            'title' => 'Test Wallet',
        ]);

        expect($wallet->deposits())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
    });
});

describe('LitecoinAddress Model', function () {
    test('it can be created with valid attributes', function () {
        $node = LitecoinNode::create([
            'name' => 'test-node',
            'title' => 'Test Node',
            'host' => 'localhost',
            'port' => 8332,
            'username' => 'testuser',
            'password' => 'testpass',
        ]);

        $wallet = $node->wallets()->create([
            'name' => 'test-wallet',
            'title' => 'Test Wallet',
        ]);

        $address = $wallet->addresses()->create([
            'address' => 'ltc1qtest123',
            'type' => AddressType::BECH32,
            'title' => 'Test Address',
            'private_key' => 'privatekey123',
        ]);

        expect($address)->toBeInstanceOf(LitecoinAddress::class)
            ->and($address->address)->toBe('ltc1qtest123')
            ->and($address->type)->toBe(AddressType::BECH32)
            ->and($address->title)->toBe('Test Address');
    });

    test('it casts type to AddressType enum', function () {
        $node = LitecoinNode::create([
            'name' => 'test-node',
            'title' => 'Test Node',
            'host' => 'localhost',
            'port' => 8332,
            'username' => 'testuser',
            'password' => 'testpass',
        ]);

        $wallet = $node->wallets()->create([
            'name' => 'test-wallet',
            'title' => 'Test Wallet',
        ]);

        $address = $wallet->addresses()->create([
            'address' => 'ltc1qtest123',
            'type' => 'bech32',
            'private_key' => 'privatekey123',
        ]);

        expect($address->type)->toBeInstanceOf(AddressType::class)
            ->and($address->type)->toBe(AddressType::BECH32);
    });

    test('it casts balance to Decimal', function () {
        $node = LitecoinNode::create([
            'name' => 'test-node',
            'title' => 'Test Node',
            'host' => 'localhost',
            'port' => 8332,
            'username' => 'testuser',
            'password' => 'testpass',
        ]);

        $wallet = $node->wallets()->create([
            'name' => 'test-wallet',
            'title' => 'Test Wallet',
        ]);

        $address = $wallet->addresses()->create([
            'address' => 'ltc1qtest123',
            'type' => AddressType::BECH32,
            'private_key' => 'privatekey123',
            'balance' => '50.25',
        ]);

        expect($address->balance)->toBeInstanceOf(DecimalNumber::class);
    });

    test('it has wallet relationship', function () {
        $node = LitecoinNode::create([
            'name' => 'test-node',
            'title' => 'Test Node',
            'host' => 'localhost',
            'port' => 8332,
            'username' => 'testuser',
            'password' => 'testpass',
        ]);

        $wallet = $node->wallets()->create([
            'name' => 'test-wallet',
            'title' => 'Test Wallet',
        ]);

        $address = $wallet->addresses()->create([
            'address' => 'ltc1qtest123',
            'type' => AddressType::BECH32,
            'private_key' => 'privatekey123',
        ]);

        expect($address->wallet)->toBeInstanceOf(LitecoinWallet::class)
            ->and($address->wallet->id)->toBe($wallet->id);
    });

    test('it has deposits relationship', function () {
        $node = LitecoinNode::create([
            'name' => 'test-node',
            'title' => 'Test Node',
            'host' => 'localhost',
            'port' => 8332,
            'username' => 'testuser',
            'password' => 'testpass',
        ]);

        $wallet = $node->wallets()->create([
            'name' => 'test-wallet',
            'title' => 'Test Wallet',
        ]);

        $address = $wallet->addresses()->create([
            'address' => 'ltc1qtest123',
            'type' => AddressType::BECH32,
            'private_key' => 'privatekey123',
        ]);

        expect($address->deposits())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
    });
});

describe('LitecoinDeposit Model', function () {
    test('it can be created with valid attributes', function () {
        $node = LitecoinNode::create([
            'name' => 'test-node',
            'title' => 'Test Node',
            'host' => 'localhost',
            'port' => 8332,
            'username' => 'testuser',
            'password' => 'testpass',
        ]);

        $wallet = $node->wallets()->create([
            'name' => 'test-wallet',
            'title' => 'Test Wallet',
        ]);

        $address = $wallet->addresses()->create([
            'address' => 'ltc1qtest123',
            'type' => AddressType::BECH32,
            'private_key' => 'privatekey123',
        ]);

        $deposit = LitecoinDeposit::create([
            'wallet_id' => $wallet->id,
            'address_id' => $address->id,
            'txid' => 'abc123xyz',
            'amount' => '10.5',
            'block_height' => 100000,
            'confirmations' => 6,
        ]);

        expect($deposit)->toBeInstanceOf(LitecoinDeposit::class)
            ->and($deposit->txid)->toBe('abc123xyz')
            ->and($deposit->block_height)->toBe(100000)
            ->and($deposit->confirmations)->toBe(6);
    });

    test('it casts amount to Decimal', function () {
        $node = LitecoinNode::create([
            'name' => 'test-node',
            'title' => 'Test Node',
            'host' => 'localhost',
            'port' => 8332,
            'username' => 'testuser',
            'password' => 'testpass',
        ]);

        $wallet = $node->wallets()->create([
            'name' => 'test-wallet',
            'title' => 'Test Wallet',
        ]);

        $address = $wallet->addresses()->create([
            'address' => 'ltc1qtest123',
            'type' => AddressType::BECH32,
            'private_key' => 'privatekey123',
        ]);

        $deposit = LitecoinDeposit::create([
            'wallet_id' => $wallet->id,
            'address_id' => $address->id,
            'txid' => 'abc123xyz',
            'amount' => '10.5',
        ]);

        expect($deposit->amount)->toBeInstanceOf(DecimalNumber::class);
    });

    test('it casts confirmations to integer', function () {
        $node = LitecoinNode::create([
            'name' => 'test-node',
            'title' => 'Test Node',
            'host' => 'localhost',
            'port' => 8332,
            'username' => 'testuser',
            'password' => 'testpass',
        ]);

        $wallet = $node->wallets()->create([
            'name' => 'test-wallet',
            'title' => 'Test Wallet',
        ]);

        $address = $wallet->addresses()->create([
            'address' => 'ltc1qtest123',
            'type' => AddressType::BECH32,
            'private_key' => 'privatekey123',
        ]);

        $deposit = LitecoinDeposit::create([
            'wallet_id' => $wallet->id,
            'address_id' => $address->id,
            'txid' => 'abc123xyz',
            'amount' => '10.5',
            'confirmations' => '6',
        ]);

        expect($deposit->confirmations)->toBeInt();
    });

    test('it has wallet relationship', function () {
        $node = LitecoinNode::create([
            'name' => 'test-node',
            'title' => 'Test Node',
            'host' => 'localhost',
            'port' => 8332,
            'username' => 'testuser',
            'password' => 'testpass',
        ]);

        $wallet = $node->wallets()->create([
            'name' => 'test-wallet',
            'title' => 'Test Wallet',
        ]);

        $address = $wallet->addresses()->create([
            'address' => 'ltc1qtest123',
            'type' => AddressType::BECH32,
            'private_key' => 'privatekey123',
        ]);

        $deposit = LitecoinDeposit::create([
            'wallet_id' => $wallet->id,
            'address_id' => $address->id,
            'txid' => 'abc123xyz',
            'amount' => '10.5',
        ]);

        expect($deposit->wallet)->toBeInstanceOf(LitecoinWallet::class)
            ->and($deposit->wallet->id)->toBe($wallet->id);
    });

    test('it has address relationship', function () {
        $node = LitecoinNode::create([
            'name' => 'test-node',
            'title' => 'Test Node',
            'host' => 'localhost',
            'port' => 8332,
            'username' => 'testuser',
            'password' => 'testpass',
        ]);

        $wallet = $node->wallets()->create([
            'name' => 'test-wallet',
            'title' => 'Test Wallet',
        ]);

        $address = $wallet->addresses()->create([
            'address' => 'ltc1qtest123',
            'type' => AddressType::BECH32,
            'private_key' => 'privatekey123',
        ]);

        $deposit = LitecoinDeposit::create([
            'wallet_id' => $wallet->id,
            'address_id' => $address->id,
            'txid' => 'abc123xyz',
            'amount' => '10.5',
        ]);

        expect($deposit->address)->toBeInstanceOf(LitecoinAddress::class)
            ->and($deposit->address->id)->toBe($address->id);
    });
});
