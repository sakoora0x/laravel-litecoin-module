<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use sakoora0x\LaravelLitecoinModule\Enums\AddressType;
use sakoora0x\LaravelLitecoinModule\Models\LitecoinAddress;
use sakoora0x\LaravelLitecoinModule\Models\LitecoinDeposit;
use sakoora0x\LaravelLitecoinModule\Models\LitecoinNode;
use sakoora0x\LaravelLitecoinModule\Models\LitecoinWallet;
use sakoora0x\LaravelLitecoinModule\WebhookHandlers\EmptyWebhookHandler;
use sakoora0x\LaravelLitecoinModule\WebhookHandlers\WebhookHandlerInterface;

uses(RefreshDatabase::class);

describe('WebhookHandlerInterface', function () {
    test('EmptyWebhookHandler implements WebhookHandlerInterface', function () {
        $handler = new EmptyWebhookHandler();

        expect($handler)->toBeInstanceOf(WebhookHandlerInterface::class);
    });
});

describe('EmptyWebhookHandler', function () {
    test('it logs deposit information when handle is called', function () {
        Log::shouldReceive('error')
            ->once()
            ->with(\Mockery::on(function ($message) {
                return str_contains($message, 'Litecoin Wallet')
                    && str_contains($message, 'new transaction')
                    && str_contains($message, 'for address');
            }));

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

        $handler = new EmptyWebhookHandler();
        $handler->handle($wallet, $address, $deposit);
    });

    test('it includes correct information in log message', function () {
        $node = LitecoinNode::create([
            'name' => 'test-node',
            'title' => 'Test Node',
            'host' => 'localhost',
            'port' => 8332,
            'username' => 'testuser',
            'password' => 'testpass',
        ]);

        $wallet = $node->wallets()->create([
            'name' => 'my-wallet',
            'title' => 'My Wallet',
        ]);

        $address = $wallet->addresses()->create([
            'address' => 'ltc1qabcdef',
            'type' => AddressType::BECH32,
            'private_key' => 'privatekey123',
        ]);

        $deposit = LitecoinDeposit::create([
            'wallet_id' => $wallet->id,
            'address_id' => $address->id,
            'txid' => 'txid123',
            'amount' => '10.5',
        ]);

        Log::shouldReceive('error')
            ->once()
            ->with(\Mockery::on(function ($message) {
                return str_contains($message, 'my-wallet')
                    && str_contains($message, 'txid123')
                    && str_contains($message, 'ltc1qabcdef');
            }));

        $handler = new EmptyWebhookHandler();
        $handler->handle($wallet, $address, $deposit);
    });
});

describe('Custom WebhookHandler', function () {
    test('custom handler can implement interface', function () {
        $customHandler = new class implements WebhookHandlerInterface {
            public bool $called = false;

            public function handle(LitecoinWallet $wallet, LitecoinAddress $address, LitecoinDeposit $deposit): void
            {
                $this->called = true;
            }
        };

        expect($customHandler)->toBeInstanceOf(WebhookHandlerInterface::class);

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

        $customHandler->handle($wallet, $address, $deposit);

        expect($customHandler->called)->toBeTrue();
    });
});
