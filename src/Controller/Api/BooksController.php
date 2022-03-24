<?php

namespace App\Controller\Api;


use App\Model\Book\BookRepositoryCriteria;
use App\Model\Exception\Book\BookNotFound;
use App\Repository\BookRepository;
use App\Service\Book\BookFormProcessor;
use App\Service\Book\DeleteBook;
use App\Service\Book\GetBook;
use App\Service\Book\PatchBook;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\{Delete, Get, Post, Put};
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View as ViewAttribute;
use FOS\RestBundle\View\View;
use JsonException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @OA\Tag(name="Books")
 */
class BooksController extends AbstractFOSRestController
{
    public const BOOK_NOT_FOUND = 'Book not found';
    
    #[Get(path: "/books")]
    #[ViewAttribute(serializerGroups: ['book'], serializerEnableMaxDepthChecks: true)]
    /**
     * List all books.
     *
     * This call show all books data base
     *
     * @OA\Response(
     *     response=200,
     *     description="ok",
     *     content={
     *         @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *                  @OA\Property(
     *                    property="status",
     *                    type="string",
     *                    description="return status"
     *                  ),
     *            )
     *         )
     *      }
     * )
     */
    
    public function list(BookRepository $bookRepository, Request $request): array
    {
        $categoryId = $request->query->get('categoryId');
        $page = $request->query->get('page');
        $itemsPerPage = $request->query->get('itemsPerPage');
        $criteria = new BookRepositoryCriteria(
            $categoryId,
            $itemsPerPage !== null ? (int)$itemsPerPage : 10,
            $page !== null ? (int)$page : 1,);
        return $bookRepository->findByCriteria($criteria);
    }
    
    #[Post(path: "/books")]
    #[ViewAttribute(serializerGroups: ['book'], serializerEnableMaxDepthChecks: true)]
    /**
     * Add one book to data base.
     *
     * Add one boook to data base
     * @OA\Response(
     *     response=200,
     *     description="ok",
     *     content={
     *         @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *                  @OA\Property(
     *                    property="status",
     *                    type="string",
     *                    description="return status"
     *                  ),
     *            )
     *         )
     *      }
     * )
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
    
    #[Get(path: "/books/{id}")]
    #[ViewAttribute(serializerGroups: ['book'], serializerEnableMaxDepthChecks: true)]
    /**
     * Get single book
     *
     * This Get single book
     *
     */
    
    public function getSigleBook(string $id, GetBook $getBook)
    {
        $book = ($getBook)($id);
        if(!$book) {
            return View::create(self::BOOK_NOT_FOUND, Response::HTTP_BAD_REQUEST);
        }
        return $book;
    }
    
    #[Put(path: "/books/{id}")]
    #[ViewAttribute(serializerGroups: ['book'], serializerEnableMaxDepthChecks: true)]
    /**
     * Update single book
     *
     * This update book
     * @throws BookNotFound
     *
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
     *
     * Update one field into book
     *
     * This update one field into book
     * @Rest\Patch(path="/books/{id}")
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     * @throws BookNotFound|JsonException
     *
     * @OA\Tag(name="Books")
     */
    public function editColumnBook(string $id, PatchBook $patchBook, Request $request): View
    {
        $book = ($patchBook)($id, $request);
        return View::create($book, Response::HTTP_CREATED);
    }
    
    #[Delete(path: "/books/{id}")]
    #[ViewAttribute(serializerGroups: ['book'], serializerEnableMaxDepthChecks: true)]
    /**
     *
     * Delete book
     *
     * This delete book
     *
     */
    public function deleteBook(string $id, DeleteBook $deleteBook): View
    {
        try {
            ($deleteBook)($id);
        } catch(Throwable) {
            return View::create(self::BOOK_NOT_FOUND, Response::HTTP_BAD_REQUEST);
        }
        return View::create(null, Response::HTTP_NO_CONTENT);
    }
}