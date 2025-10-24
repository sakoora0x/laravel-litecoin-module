<?php

namespace sakoora0x\LaravelLitecoinModule\Support;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Decimal\Decimal;

/**
 * Wrapper class for decimal numbers that supports both ext-decimal and brick/math
 */
class DecimalNumber
{
    private Decimal|BigDecimal $value;
    private bool $usingExtDecimal;

    public function __construct(string|int|float $value, int $scale = 8)
    {
        $this->usingExtDecimal = extension_loaded('decimal');

        if ($this->usingExtDecimal) {
            $this->value = new Decimal((string)$value, $scale);
        } else {
            $this->value = BigDecimal::of((string)$value)->toScale($scale, RoundingMode::HALF_UP);
        }
    }

    public function toString(): string
    {
        return (string)$this->value;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function add(DecimalNumber|string|int|float $other): self
    {
        $otherValue = $other instanceof self ? $other->toString() : (string)$other;

        if ($this->usingExtDecimal) {
            $otherDecimal = new Decimal($otherValue);
            return new self($this->value->add($otherDecimal)->toString());
        }

        $result = $this->value->plus(BigDecimal::of($otherValue));
        return new self((string)$result);
    }

    public function subtract(DecimalNumber|string|int|float $other): self
    {
        $otherValue = $other instanceof self ? $other->toString() : (string)$other;

        if ($this->usingExtDecimal) {
            $otherDecimal = new Decimal($otherValue);
            return new self($this->value->sub($otherDecimal)->toString());
        }

        $result = $this->value->minus(BigDecimal::of($otherValue));
        return new self((string)$result);
    }

    public function multiply(DecimalNumber|string|int|float $other): self
    {
        $otherValue = $other instanceof self ? $other->toString() : (string)$other;

        if ($this->usingExtDecimal) {
            $otherDecimal = new Decimal($otherValue);
            return new self($this->value->mul($otherDecimal)->toString());
        }

        $result = $this->value->multipliedBy(BigDecimal::of($otherValue));
        return new self((string)$result);
    }

    public function divide(DecimalNumber|string|int|float $other): self
    {
        $otherValue = $other instanceof self ? $other->toString() : (string)$other;

        if ($this->usingExtDecimal) {
            $otherDecimal = new Decimal($otherValue);
            return new self($this->value->div($otherDecimal)->toString());
        }

        $result = $this->value->dividedBy(BigDecimal::of($otherValue), 8, RoundingMode::HALF_UP);
        return new self((string)$result);
    }

    public function isGreaterThan(DecimalNumber|string|int|float $other): bool
    {
        if (!$other instanceof self) {
            $other = new self($other);
        }

        if ($this->usingExtDecimal) {
            return $this->value->compareTo($other->value) > 0;
        }

        return $this->value->isGreaterThan($other->value);
    }

    public function isLessThan(DecimalNumber|string|int|float $other): bool
    {
        if (!$other instanceof self) {
            $other = new self($other);
        }

        if ($this->usingExtDecimal) {
            return $this->value->compareTo($other->value) < 0;
        }

        return $this->value->isLessThan($other->value);
    }

    public function equals(DecimalNumber|string|int|float $other): bool
    {
        if (!$other instanceof self) {
            $other = new self($other);
        }

        if ($this->usingExtDecimal) {
            return $this->value->compareTo($other->value) === 0;
        }

        return $this->value->isEqualTo($other->value);
    }

    public function toFloat(): float
    {
        return (float)$this->toString();
    }

    public static function isUsingExtDecimal(): bool
    {
        return extension_loaded('decimal');
    }
}
