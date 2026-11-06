<?php

namespace Modules\Catalog\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Shared\ValueObjects\Isbn;

class Book extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'author_id', 'publisher_id', 'title', 'isbn',
        'description', 'published_year', 'stock',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    // Many-to-many dengan pivot added_at
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_book')
            ->withPivot('added_at')
            ->withTimestamps();
    }

    // ---------- Query Scopes ----------

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('title', 'like', "%{$keyword}%")
              ->orWhere('isbn', 'like', "%{$keyword}%")
              ->orWhere('description', 'like', "%{$keyword}%");
        });
    }

    public function scopeInCategory(Builder $query, int $categoryId): Builder
    {
        return $query->whereHas('categories', function ($q) use ($categoryId) {
            $q->where('categories.id', $categoryId);
        });
    }

    // ---------- Helper ----------

    public function isInStock(int $quantity = 1): bool
    {
        return $this->stock >= $quantity;
    }

    // Value Object ISBN (validasi format)
    public function isbnValue(): Isbn
    {
        return new Isbn($this->isbn);
    }
}