<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LibraryController extends AbstractController
{
        private LoggerInterface $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("/library/list", name="library_list")
     */

    public function list(Request $request, LoggerInterface $logger): JsonResponse
    {
        $title = $request->get('title', 'Juan');
        $logger->error('Hola mundosss');
        $response = new JsonResponse();
        $response->setData([
            'success' => true,
            'data' => [
                'id' => 1,
                'title' => $title
            ]
        ]);
        return $response;
    }
}