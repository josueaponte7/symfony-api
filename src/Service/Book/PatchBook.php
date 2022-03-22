<?php

namespace App\Service\Book;

use App\Entity\Book;
use App\Model\Exception\Book\BookNotFound;
use App\Repository\BookRepository;
use JsonException;
use Symfony\Component\HttpFoundation\Request;

class PatchBook
{
    private BookRepository $bookRepository;
    private GetBook $getBook;

    
    public function __construct(BookRepository $bookRepository, GetBook $getBook)
    {
        $this->bookRepository = $bookRepository;
        $this->getBook = $getBook;

    }
    
    /**
     * @throws BookNotFound
     * @throws JsonException
     */
    public function __invoke(string $id, Request $request): ?Book
    {
        $book = ($this->getBook)($id);
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $book->patch($data);
        $this->bookRepository->add($book);
        return $book;
    }
}