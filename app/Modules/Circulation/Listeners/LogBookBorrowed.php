<?php

namespace Modules\Circulation\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Circulation\Events\BookBorrowed;

class LogBookBorrowed
{
    public function handle(BookBorrowed $event): void
    {
        Log::info('Buku dipinjam', [
            'loan_id'   => $event->loan->id,
            'member_id' => $event->loan->member_id,
            'waktu'     => now()->toDateTimeString(),
        ]);
    }
}