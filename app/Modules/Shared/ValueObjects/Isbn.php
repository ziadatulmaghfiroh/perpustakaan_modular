<?php

namespace Modules\Shared\ValueObjects;

use InvalidArgumentException;

class Isbn
{
    private string $value;

    public function __construct(string $value)
    {
        $clean = str_replace(['-', ' '], '', $value);

        if (!$this->isValid($clean)) {
            throw new InvalidArgumentException("Format ISBN tidak valid: {$value}");
        }

        $this->value = $clean;
    }

    private function isValid(string $isbn): bool
    {
        return match (strlen($isbn)) {
            10 => $this->isValidIsbn10($isbn),
            13 => $this->isValidIsbn13($isbn),
            default => false,
        };
    }

    private function isValidIsbn10(string $isbn): bool
    {
        if (!preg_match('/^\d{9}[\dXx]$/', $isbn)) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $char = strtoupper($isbn[$i]);
            $digit = $char === 'X' ? 10 : (int) $char;
            $sum += $digit * (10 - $i);
        }

        return $sum % 11 === 0;
    }

    private function isValidIsbn13(string $isbn): bool
    {
        if (!preg_match('/^\d{13}$/', $isbn)) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $isbn[$i] * ($i % 2 === 0 ? 1 : 3);
        }

        $check = (10 - ($sum % 10)) % 10;

        return $check === (int) $isbn[12];
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(Isbn $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}