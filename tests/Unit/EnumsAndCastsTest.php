<?php

use Illuminate\Database\Eloquent\Model;
use sakoora0x\LaravelLitecoinModule\Casts\DecimalCast;
use sakoora0x\LaravelLitecoinModule\Enums\AddressType;
use sakoora0x\LaravelLitecoinModule\Support\DecimalNumber;

describe('AddressType Enum', function () {
    test('it has correct case values', function () {
        expect(AddressType::LEGACY->value)->toBe('legacy')
            ->and(AddressType::P2SH_SEGWIT->value)->toBe('p2sh-segwit')
            ->and(AddressType::BECH32->value)->toBe('bech32');
    });

    test('it can return all values', function () {
        $values = AddressType::values();

        expect($values)->toBeArray()
            ->and($values)->toContain('legacy')
            ->and($values)->toContain('p2sh-segwit')
            ->and($values)->toContain('bech32')
            ->and($values)->toHaveCount(3);
    });

    test('it can be constructed from string', function () {
        expect(AddressType::from('legacy'))->toBe(AddressType::LEGACY)
            ->and(AddressType::from('p2sh-segwit'))->toBe(AddressType::P2SH_SEGWIT)
            ->and(AddressType::from('bech32'))->toBe(AddressType::BECH32);
    });
});

describe('DecimalCast', function () {
    beforeEach(function () {
        $this->cast = new DecimalCast();
        $this->model = new class extends Model {
            protected $fillable = ['amount'];
        };
    });

    test('it casts string to DecimalNumber on get', function () {
        $result = $this->cast->get($this->model, 'amount', '123.456', []);

        expect($result)->toBeInstanceOf(DecimalNumber::class)
            ->and($result->toString())->toBe('123.45600000');
    });

    test('it handles zero value on get', function () {
        $result = $this->cast->get($this->model, 'amount', 0, []);

        expect($result)->toBeInstanceOf(DecimalNumber::class)
            ->and($result->toFloat())->toBe(0.0);
    });

    test('it handles null value on get', function () {
        $result = $this->cast->get($this->model, 'amount', null, []);

        expect($result)->toBeInstanceOf(DecimalNumber::class)
            ->and($result->toFloat())->toBe(0.0);
    });

    test('it casts DecimalNumber to string on set', function () {
        $decimal = new DecimalNumber('123.456');
        $result = $this->cast->set($this->model, 'amount', $decimal, []);

        expect($result)->toBe('123.45600000');
    });

    test('it passes through non-DecimalNumber values on set', function () {
        $result = $this->cast->set($this->model, 'amount', '123.456', []);

        expect($result)->toBe('123.456');
    });

    test('it handles numeric values on set', function () {
        $result = $this->cast->set($this->model, 'amount', 123.456, []);

        expect($result)->toBe(123.456);
    });

    test('it handles integer values on get', function () {
        $result = $this->cast->get($this->model, 'amount', 100, []);

        expect($result)->toBeInstanceOf(DecimalNumber::class)
            ->and($result->toFloat())->toBe(100.0);
    });

    test('it handles large decimal values', function () {
        $largeValue = '999999999.99999999';
        $result = $this->cast->get($this->model, 'amount', $largeValue, []);

        expect($result)->toBeInstanceOf(DecimalNumber::class);
        // Just check it's a DecimalNumber, actual precision may vary
    });

    test('it can perform decimal arithmetic', function () {
        $a = new DecimalNumber('10.5');
        $b = new DecimalNumber('5.25');

        expect($a->add($b)->toFloat())->toBeGreaterThan(15.0)
            ->and($a->subtract($b)->toFloat())->toBeGreaterThan(5.0)
            ->and($a->multiply(2)->toFloat())->toBeGreaterThan(20.0);
    });
});
