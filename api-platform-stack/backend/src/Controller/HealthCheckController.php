<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HealthCheckController extends AbstractController
{
    /**
     * @Route("/health-check", methods={"GET"})
     *
     * @return Response
     */
    public function healthCheck(): Response
    {
        return new Response(Response::HTTP_OK);
    }
}
