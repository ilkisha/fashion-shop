<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class NotFoundController extends AbstractController
{
    #[Route('/{path}', name: 'app_fallback', requirements: ['path' => '.+'], priority: -1000)]
    public function __invoke(string $path): Response
    {
        $response = new Response('', Response::HTTP_NOT_FOUND);

        return $this->render('error/404.html.twig', [], $response);
    }
}
