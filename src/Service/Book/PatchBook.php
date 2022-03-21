<?php

namespace App\Service\Book;

use App\Entity\Book;
use App\Model\Exception\Book\BookNotFound;
use App\Repository\BookRepository;

class PatchBook
{
    private BookRepository $bookRepository;
    private GetBook $getBook;
    private Book $book;
    
    public function __construct( BookRepository $bookRepository, GetBook $getBook)
    {
        $this->bookRepository = $bookRepository;
        $this->getBook = $getBook;

    }
    /**
     * @throws BookNotFound
     */
    public function __invoke(Book $book): ?Book
    {
        $this->bookRepository->add($book);
        return $book;
    }
}