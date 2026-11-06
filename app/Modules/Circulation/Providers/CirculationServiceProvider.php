<?php

namespace Modules\Circulation\Providers;

use Illuminate\Support\Facades\Event;
use Modules\Circulation\Events\BookBorrowed;
use Modules\Circulation\Listeners\LogBookBorrowed;
use Illuminate\Support\ServiceProvider;
use Modules\Catalog\Services\BookService;
use Modules\Membership\Services\MemberService;
use Modules\Shared\Contracts\BookServiceInterface;
use Modules\Shared\Contracts\MemberServiceInterface;


class CirculationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(BookServiceInterface::class, BookService::class);
        $this->app->bind(MemberServiceInterface::class, MemberService::class);
    }

    public function boot(): void
    {
        Event::listen(BookBorrowed::class, LogBookBorrowed::class);
    }
}