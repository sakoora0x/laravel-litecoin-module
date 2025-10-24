<?php

use sakoora0x\LaravelLitecoinModule\Facades\Litecoin as LitecoinFacade;
use sakoora0x\LaravelLitecoinModule\Litecoin;

describe('Litecoin Facade', function () {
    test('it resolves to Litecoin instance', function () {
        $instance = LitecoinFacade::getFacadeRoot();

        expect($instance)->toBeInstanceOf(Litecoin::class);
    });

    test('it returns the same instance on multiple calls', function () {
        $instance1 = LitecoinFacade::getFacadeRoot();
        $instance2 = LitecoinFacade::getFacadeRoot();

        expect($instance1)->toBe($instance2);
    });

    test('it can access Litecoin methods through facade', function () {
        expect(method_exists(Litecoin::class, 'createNode'))->toBeTrue()
            ->and(method_exists(Litecoin::class, 'createWallet'))->toBeTrue()
            ->and(method_exists(Litecoin::class, 'createAddress'))->toBeTrue()
            ->and(method_exists(Litecoin::class, 'validateAddress'))->toBeTrue()
            ->and(method_exists(Litecoin::class, 'send'))->toBeTrue()
            ->and(method_exists(Litecoin::class, 'sendAll'))->toBeTrue()
            ->and(method_exists(Litecoin::class, 'hasWallet'))->toBeTrue()
            ->and(method_exists(Litecoin::class, 'loadWallet'))->toBeTrue();
    });
});

describe('Litecoin Class', function () {
    test('it is a singleton in the container', function () {
        $instance1 = app(Litecoin::class);
        $instance2 = app(Litecoin::class);

        expect($instance1)->toBe($instance2);
    });

    test('it can be instantiated', function () {
        $litecoin = new Litecoin();

        expect($litecoin)->toBeInstanceOf(Litecoin::class);
    });
});
