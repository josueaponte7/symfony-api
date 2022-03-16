<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LibraryController extends AbstractController
{

    /**
     * @Route("/books", name="books_get")
     */

    public function list(Request $request, BookRepository $bookRepository): JsonResponse
    {

        $books = $bookRepository->findAll();
        $booksAsArray = [];
        foreach ($books as $book){
            $booksAsArray[] = [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'image' => $book->getImage(),
            ];
        }
        $response = new JsonResponse();
        $response->setData([
            'success' => true,
            'data' => $booksAsArray
        ]);
        return $response;
    }

    /**
     * @Route("/book/create", name="create_book")
     */
    public function createBook(Request $request, Entitymanagerinterface $em): JsonResponse
    {
        $response = new JsonResponse();
        $title = $request->get('title');
        if(empty($title)){
            $response->setData([
                'success' => false,
                'error' => 'Title cannot be empty',
                'data' => []
            ]);
            return $response;
        }
        $book = new Book();
        $book->setTitle($title);
        $em->persist($book);
        $em->flush();
        $response->setData([
            'success' => true,
            'data' => [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
            ]
        ]);
        return $response;
    }
}