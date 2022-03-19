<?php

namespace App\Service\Book;

use App\Repository\BookRepository;

class DeleteBook
{
    private BookRepository $bookRepository;
    private GetBook $getBook;
    
    public function __construct(BookRepository $bookRepository, GetBook $getBook)
    {
        $this->bookRepository = $bookRepository;
        $this->getBook = $getBook;
    }
    
    public function __invoke(string $id)
    {
        $book = ($this->getBook)($id);
        if($book !== null) {
            $this->bookRepository->remove($book);
        }
    }
    
}