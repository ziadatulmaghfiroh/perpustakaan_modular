<?php

namespace Modules\Shared\ValueObjects;

use Carbon\Carbon;
use InvalidArgumentException;

class Denda
{
    public const TARIF_PER_HARI = 1000;

    private int $hariTerlambat;
    private float $jumlah;

    public function __construct(int $hariTerlambat, float $tarifPerHari = self::TARIF_PER_HARI)
    {
        if ($hariTerlambat < 0) {
            throw new InvalidArgumentException('Hari terlambat tidak boleh negatif');
        }

        $this->hariTerlambat = $hariTerlambat;
        $this->jumlah = $hariTerlambat * $tarifPerHari;
    }

    // Perhitungan otomatis dari tanggal jatuh tempo & tanggal kembali
    public static function hitung(Carbon $jatuhTempo, Carbon $tanggalKembali): self
    {
        $hari = $tanggalKembali->greaterThan($jatuhTempo)
            ? (int) $jatuhTempo->diffInDays($tanggalKembali)
            : 0;

        return new self($hari);
    }

    public function getHariTerlambat(): int
    {
        return $this->hariTerlambat;
    }

    public function getJumlah(): float
    {
        return $this->jumlah;
    }

    public function isTerlambat(): bool
    {
        return $this->hariTerlambat > 0;
    }

    public function __toString(): string
    {
        return 'Rp ' . number_format($this->jumlah, 0, ',', '.');
    }
}