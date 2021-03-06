<?php

namespace App\Model\Exception\Book;

use Exception;

class BookNotFound extends Exception
{
    /**
     * @throws BookNotFound
     */
    public static function throwException(): void
    {
        throw new self('Book not found');
    }
}
