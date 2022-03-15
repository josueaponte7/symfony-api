<?php

namespace App\Controller\Api;


use App\Entity\Book;

use App\Form\Model\BookDto;
use App\Form\Type\BookFormType;
use App\Repository\BookRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\Request;

class BooksController extends AbstractFOSRestController
{

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
     * @throws FilesystemException
     */
    public function create(EntityManagerInterface $em, Request $request, FileUploader $fileUploader)
    {
        $bookDto = new BookDto();
        $form = $this->createForm(BookFormType::class, $bookDto);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $filename = $fileUploader->uploaderBase64File($bookDto->base64Image);
            $book = new Book();
            $book->setTitle($bookDto->title);
            $book->setImage($filename);
            $em->persist($book);
            $em->flush();
            return $book;
        }
        return $form;
    }
}