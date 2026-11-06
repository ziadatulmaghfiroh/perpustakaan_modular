<?php

namespace Modules\Catalog\Services;

use Modules\Catalog\Models\Book;
use Modules\Shared\Contracts\BookServiceInterface;

class BookService implements BookServiceInterface
{
    public function getBookForLoan(int $bookId): ?array
    {
        $book = Book::find($bookId);

        if (!$book) {
            return null;
        }

        return [
            'id'    => $book->id,
            'title' => $book->title,
            'isbn'  => $book->isbn,
            'stock' => $book->stock,
        ];
    }

    public function isStockAvailable(int $bookId, int $quantity): bool
    {
        $book = Book::find($bookId);

        return $book && $book->isInStock($quantity);
    }

    public function reduceStock(int $bookId, int $quantity): bool
    {
        // lockForUpdate mencegah dua peminjaman bersamaan mengurangi stok yang sama
        $book = Book::lockForUpdate()->find($bookId);

        if (!$book || !$book->isInStock($quantity)) {
            return false;
        }

        $book->decrement('stock', $quantity);

        return true;
    }
}