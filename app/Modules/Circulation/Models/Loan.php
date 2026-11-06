<?php

namespace Modules\Circulation\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Shared\ValueObjects\Denda;

class Loan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'member_id', 'loan_number', 'borrowed_at', 'due_date',
        'returned_at', 'status', 'fine_amount',
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_date' => 'date',
        'returned_at' => 'datetime',
        'fine_amount' => 'decimal:2',
    ];

    // HasMany: Peminjaman -> DetailPeminjaman
    public function details(): HasMany
    {
        return $this->hasMany(LoanDetail::class);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', 'borrowed')
                     ->where('due_date', '<', now()->toDateString());
    }

    // Value Object Denda: hitung otomatis
    public function hitungDenda(): Denda
    {
        $kembali = $this->returned_at ?? now();

        return Denda::hitung($this->due_date, $kembali);
    }
}