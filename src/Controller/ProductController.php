<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    #[Route('/products', name: 'catalog_index', methods: ['GET'])]
    public function index(ProductRepository $products): Response
    {
        return $this->render('catalog/index.html.twig', [
            'products' => $products->findActive(),
        ]);
    }

    #[Route('/products/{slug}', name: 'catalog_show', methods: ['GET'])]
    public function show(string $slug, ProductRepository $products): Response
    {
        $product = $products->findOneActiveBySlug($slug);

        if (!$product) {
            throw $this->createNotFoundException();
        }

        return $this->render('catalog/show.html.twig', [
            'product' => $product,
        ]);
    }
}
