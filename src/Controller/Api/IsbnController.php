<?php

namespace App\Controller\Api;

use App\Service\Isbn\GetBookByIsbn;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IsbnController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(path="/isbn")
     * @Rest\View(serializerGroups={"book_isbn"}, serializerEnableMaxDepthChecks=true)
     *
     */
    public function getBookIsbn(GetBookByIsbn $getBookIsbn, Request $request): View
    {
        $isbn = $request->get('isbn', null);
        if($isbn === null) {
            return View::create('Book not found', Response::HTTP_BAD_REQUEST);
        }
        $json = ($getBookIsbn)($isbn);
        return View::create($json);
    }
    
}