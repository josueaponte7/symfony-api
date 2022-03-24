<?php

namespace App\Model\Exception\Category;

use Exception;

class CategoryNotFound extends Exception
{
    /**
     * @throws CategoryNotFound
     */
    public static function throwException(): void
    {
        throw new self('Book not found');
    }
}
