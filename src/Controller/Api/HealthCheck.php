<?php

namespace App\Controller\Api;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @OA\Tag(name="Health Check")
 */
class HealthCheck extends AbstractFOSRestController
{
    /**
     * List the rewards of the specified user.
     *
     * This call takes into account all confirmed awards, but not pending or refused awards.
     *
     * @Route("/healthcheck", methods={"GET"})
     * @OA\Get(
     *     path="/healthcheck",
     *     @OA\Response(
     *          response=200,
     *          description="ok",
     *          content={
     *              @OA\MediaType(
     *                  mediaType="application/json",
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="status",
     *                          type="string",
     *                          description="return status"
     *                      ),
     *                  )
     *              )
     *          }
     *      )
     * )
     * @Security(
     *   name="X-AUTH-TOKEN"
     * )
     */
    public function index(): Response
    {
        return new JsonResponse(['status' => 'ok']);
    }
}