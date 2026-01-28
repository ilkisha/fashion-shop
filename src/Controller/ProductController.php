<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\SearchHistoryService;
use App\Dto\ProductFilterDto;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Service\ViewedProductsService;

final class ProductController extends AbstractController
{
    #[Route('/products', name: 'catalog_index', methods: ['GET'])]
    public function index(
        Request $request,
        ProductRepository $products,
        SearchHistoryService $searchHistory,
        ValidatorInterface $validator
    ): Response {
        $filter = new ProductFilterDto();
        $filter->gender = $request->query->get('gender');
        $filter->category = $request->query->get('category');
        $filter->q = $request->query->get('q');

        $violations = $validator->validate($filter);
        if (count($violations) > 0) {
            throw new BadRequestHttpException((string) $violations);
        }

        $q = trim((string) ($filter->q ?? ''));

        // log only for authenticated users
        if ($this->getUser()) {
            $searchHistory->add($q);
        }

        $categories = $products->findActiveCategories($filter->gender);

        $category = $filter->category;
        if (!is_string($category) || $category === '' || !in_array($category, $categories, true)) {
            $category = null;
        }

        return $this->render('catalog/index.html.twig', [
            'products' => $products->findActive($filter->gender, $category, $q),
            'gender' => $filter->gender,
            'category' => $category,
            'categories' => $categories,
            'q' => $q,
        ]);
    }

    #[Route('/products/{slug}', name: 'catalog_show', methods: ['GET'])]
    public function show(string $slug, ProductRepository $products, ViewedProductsService $viewed): Response
    {
        $product = $products->findOneActiveBySlug($slug);

        if (!$product) {
            throw $this->createNotFoundException();
        }

        // track viewed product
        $viewed->add((int) $product->getId());

        return $this->render('catalog/show.html.twig', [
            'product' => $product,
        ]);
    }
}
