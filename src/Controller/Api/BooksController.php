<?php

namespace App\Controller\Api;


use App\Repository\BookRepository;
use App\Service\BookFormProcessor;
use App\Service\BookManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BooksController extends AbstractFOSRestController
{
    public const BOOK_NOT_FOUND = 'Book not found';
    /**
     * @Rest\Get(path="/books")
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    public function list(BookManager $bookManager): array
    {
        return $bookManager->getRepository()->findAll();
    }

    /**
     * @Rest\Post(path="/books")
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    public function createBook(BookManager $bookManager, BookFormProcessor $bookFormProcessor, Request $request): View
    {
        $book = $bookManager->create();
        [$book, $error] = ($bookFormProcessor)($book, $request);
        $statusCode = $book ? Response::HTTP_CREATED : Response::HTTP_BAD_GATEWAY;
        $data = $book ?? $error;
        return View::create($data, $statusCode);
    }

    /**
     * @Rest\Get(path="/books/{id}", requirements={"id"="\d+"})
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    public function getSigleBook(int $id, BookManager $bookManager)
    {
        $book = $bookManager->find($id);
        if(!$book){
            return View::create(BOOK_NOT_FOUND, Response::HTTP_BAD_REQUEST);
        }
        return $book;
    }

    /**
     * @Rest\Post(path="/books/{id}", requirements={"id"="\d+"})
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    public function editBook(int $id, BookFormProcessor $bookFormProcessor, BookManager $bookManager, Request $request): View
    {
        $book = $bookManager->find($id);
        if(!$book){
            return View::create(BOOK_NOT_FOUND, Response::HTTP_BAD_REQUEST);
        }
        [$book, $error] = ($bookFormProcessor)($book, $request);

        $statusCode = $book ? Response::HTTP_CREATED : Response::HTTP_BAD_GATEWAY;
        $data = $book ?? $error;
        return View::create($data, $statusCode);
    }

    /**
     * @Rest\Delete(path="/books/{id}", requirements={"id"="\d+"})
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    public function deleteBook(int $id, BookManager $bookManager): View
    {
        $book = $bookManager->find($id);
        if(!$book){
            return View::create(BOOK_NOT_FOUND, Response::HTTP_BAD_REQUEST);
        }
       $bookManager->delete($book);

        return View::create(null, Response::HTTP_NO_CONTENT);
    }
}