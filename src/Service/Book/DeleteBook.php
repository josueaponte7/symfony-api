<?php

namespace App\Service\Book;

use App\Repository\BookRepository;
use App\Service\FileDeleter;

class DeleteBook
{
    public function __construct(private BookRepository $bookRepository, private GetBook $getBook, private FileDeleter $fileDeleter)
    {
    }

    public function __invoke(string $id)
    {
        $book = ($this->getBook)($id);
        if ($book !== null) {
            $image = $book->getImage();
            if ($image !== null) {
                ($this->fileDeleter)($image);
            }
            $this->bookRepository->remove($book);
        }
    }
}
