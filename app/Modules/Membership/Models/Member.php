<?php

namespace Modules\Membership\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Member extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'member_number', 'name', 'email', 'phone',
        'status', 'joined_at', 'expired_at',
    ];

    protected $casts = [
        'joined_at' => 'date',
        'expired_at' => 'date',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'member_role');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}