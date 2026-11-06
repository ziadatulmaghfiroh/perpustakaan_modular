<?php

namespace Modules\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use SoftDeletes;

    protected $table = 'categories';

    protected $fillable = ['name', 'slug', 'description', 'parent_id'];

    // Self-referencing: kategori -> induk
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Self-referencing: kategori -> sub-kategori
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Many-to-many dengan pivot added_at
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'category_book')
            ->withPivot('added_at')
            ->withTimestamps();
    }
}