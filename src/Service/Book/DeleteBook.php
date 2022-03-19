<?php

namespace App\Service\Book;

use App\Repository\BookRepository;
use Exception;
use Ramsey\Uuid\Uuid;

class DeleteBook
{
    private BookRepository $bookRepository;
    
    
    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }
    
    public function __invoke(string $id)
    {
        $book = $this->bookRepository->find(Uuid::fromString($id));
        if (!$book) {
            throw new Exception('That book does not exists');
        }
        //$this->bookRepository->remove($book);
        $this->bookRepository->delete($book);
    }
    
}