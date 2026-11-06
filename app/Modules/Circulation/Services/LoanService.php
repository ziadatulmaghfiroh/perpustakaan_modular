<?php

namespace Modules\Circulation\Services;

use Illuminate\Support\Facades\DB;
use Modules\Circulation\Events\BookBorrowed;
use Modules\Circulation\Models\Loan;
use Modules\Circulation\Models\LoanDetail;
use Modules\Shared\Contracts\BookServiceInterface;
use Modules\Shared\Contracts\MemberServiceInterface;
use Modules\Shared\Exceptions\InsufficientStockException;

class LoanService
{
    public function __construct(
        private BookServiceInterface $bookService,
        private MemberServiceInterface $memberService
    ) {}

    /**
     * @param array $items contoh: [['book_id' => 1, 'quantity' => 2]]
     */
    public function borrow(int $memberId, array $items, int $days = 7): Loan
    {
        $loan = DB::transaction(function () use ($memberId, $items, $days) {
            if (!$this->memberService->isMemberActive($memberId)) {
                throw new \DomainException("Anggota tidak aktif atau tidak ditemukan: {$memberId}");
            }

            $loan = Loan::create([
                'member_id'   => $memberId,
                'loan_number' => 'LN-' . strtoupper(uniqid()),
                'borrowed_at' => now(),
                'due_date'    => now()->addDays($days)->toDateString(),
                'status'      => 'borrowed',
                'fine_amount' => 0,
            ]);

            foreach ($items as $item) {
                $book = $this->bookService->getBookForLoan($item['book_id']);

                if (!$book) {
                    throw new \DomainException("Buku tidak ditemukan: {$item['book_id']}");
                }

                if (!$this->bookService->isStockAvailable($item['book_id'], $item['quantity'])) {
                    throw new InsufficientStockException(
                        $book['title'],
                        $book['stock'],
                        $item['quantity']
                    );
                }

                // Snapshot judul & ISBN agar riwayat tidak berubah kalau buku diedit
                LoanDetail::create([
                    'loan_id'    => $loan->id,
                    'book_id'    => $book['id'],
                    'book_title' => $book['title'],
                    'book_isbn'  => $book['isbn'],
                    'quantity'   => $item['quantity'],
                ]);

                $this->bookService->reduceStock($item['book_id'], $item['quantity']);
            }

            $this->memberService->updateStatus($memberId, 'borrowing');

            return $loan->load('details');
        });

        // Event hanya ditembakkan kalau transaction sukses (sudah commit)
        event(new BookBorrowed($loan));

        return $loan;
    }
}