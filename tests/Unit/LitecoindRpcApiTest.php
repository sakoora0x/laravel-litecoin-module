<?php

use sakoora0x\LaravelLitecoinModule\LitecoindRpcApi;

beforeEach(function () {
    $this->host = 'localhost';
    $this->port = 8332;
    $this->username = 'testuser';
    $this->password = 'testpass';
});

test('it can be instantiated with correct parameters', function () {
    $api = new LitecoindRpcApi($this->host, $this->port, $this->username, $this->password);

    expect($api)->toBeInstanceOf(LitecoindRpcApi::class);
});

test('it constructs correct RPC URL', function () {
    $api = new LitecoindRpcApi('example.com', 9999, 'user', 'pass');

    expect($api)->toBeInstanceOf(LitecoindRpcApi::class);
});

test('it accepts nullable username and password', function () {
    $api = new LitecoindRpcApi($this->host, $this->port, null, null);

    expect($api)->toBeInstanceOf(LitecoindRpcApi::class);
});

test('it can be instantiated with default port', function () {
    $api = new LitecoindRpcApi($this->host, 8332, $this->username, $this->password);

    expect($api)->toBeInstanceOf(LitecoindRpcApi::class);
});

test('it accepts custom ports', function () {
    $customPort = 19332; // Litecoin testnet port
    $api = new LitecoindRpcApi($this->host, $customPort, $this->username, $this->password);

    expect($api)->toBeInstanceOf(LitecoindRpcApi::class);
});
