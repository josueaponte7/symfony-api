<?php

namespace App\Controller\Api;


use App\Model\Exception\Book\BookNotFound;
use App\Repository\BookRepository;
use App\Service\Book\BookFormProcessor;
use App\Service\Book\DeleteBook;
use App\Service\Book\GetBook;
use App\Service\Book\PatchBook;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class BooksController extends AbstractFOSRestController
{
    public const BOOK_NOT_FOUND = 'Book not found';
    
    /**
     * @Rest\Get(path="/books")
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    public function list(BookRepository $bookRepository): array
    {
        return $bookRepository->findAll();
    }
    
    /**
     * @Rest\Post(path="/books")
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    public function createBook(
        BookFormProcessor $bookFormProcessor,
        Request $request
    ): View {
        [$book, $error] = ($bookFormProcessor)($request);
        $statusCode = $book ? Response::HTTP_CREATED : Response::HTTP_BAD_GATEWAY;
        $data = $book ?? $error;
        return View::create($data, $statusCode);
    }
    
    /**
     * @Rest\Get(path="/books/{id}")
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    public function getSigleBook(string $id, GetBook $getBook)
    {
        $book = ($getBook)($id);
        if(!$book) {
            return View::create(self::BOOK_NOT_FOUND, Response::HTTP_BAD_REQUEST);
        }
        return $book;
    }
    
    /**
     * @Rest\Put(path="/books/{id}")
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    public function editBook(string $id, BookFormProcessor $bookFormProcessor, GetBook $getBook, Request $request): View
    {
        $book = ($getBook)($id);
        if(!$book) {
            return View::create(self::BOOK_NOT_FOUND, Response::HTTP_BAD_REQUEST);
        }
        [$book, $error] = ($bookFormProcessor)($request, $id);
        
        $statusCode = $book ? Response::HTTP_CREATED : Response::HTTP_BAD_GATEWAY;
        $data = $book ?? $error;
        return View::create($data, $statusCode);
    }
    
    /**
     * @Rest\Patch(path="/books/{id}")
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     * @throws BookNotFound
     * @throws JsonException
     */
    public function editColumnBook(string $id, PatchBook $patchBook, GetBook $getBook, Request $request): View
    {
        $book = ($getBook)($id);
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $book->patch($data);
        ($patchBook)($book);
        return View::create($book, Response::HTTP_CREATED);
    
    }
        /**
     * @Rest\Delete(path="/books/{id}")
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    public function deleteBook(string $id, DeleteBook $deleteBook): View
    {
        try {
            ($deleteBook)($id);
        } catch(Throwable $e) {
            return View::create(self::BOOK_NOT_FOUND, Response::HTTP_BAD_REQUEST);
        }
        return View::create(null, Response::HTTP_NO_CONTENT);
    }
}