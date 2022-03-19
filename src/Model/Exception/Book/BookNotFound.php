<?php

namespace App\Model\Exception\Book;

use Exception;

class BookNotFound extends Exception
{
    public static function throwException(): void
    {
        throw new self('Book not found');
    }
}