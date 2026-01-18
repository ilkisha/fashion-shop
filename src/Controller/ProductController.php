<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

final class ProductController extends AbstractController
{
    #[Route('/products', name: 'catalog_index', methods: ['GET'])]
    public function index(Request $request, ProductRepository $products): Response
    {
        $gender = $request->query->get('gender');
        $category = $request->query->get('category');

        $allowed = ['men', 'women', 'unisex'];

        if (!is_string($gender) || !in_array($gender, $allowed, true)) {
            $gender = null;
        }

        $categories = $products->findActiveCategories($gender);

        if (!is_string($category) || $category === '' || !in_array($category, $categories, true)) {
            $category = null;
        }

        return $this->render('catalog/index.html.twig', [
            'products' => $products->findActive($gender, $category),
            'gender' => $gender,
            'category' => $category,
            'categories' => $categories,
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
