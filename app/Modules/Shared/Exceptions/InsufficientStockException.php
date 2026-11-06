<?php

namespace Modules\Shared\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    public function __construct(string $title, int $available, int $requested)
    {
        parent::__construct(
            "Stok buku '{$title}' tidak mencukupi. Tersedia: {$available}, diminta: {$requested}"
        );
    }
}