<?php

namespace App\Controller\Account;

use App\Repository\ProductRepository;
use App\Service\ViewedProductsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class ViewedProductsController extends AbstractController
{
    #[Route('/account/viewed-products', name: 'account_viewed_products', methods: ['GET'])]
    public function index(ViewedProductsService $viewed, ProductRepository $products): Response
    {
        $ids = $viewed->all();

        return $this->render('account/viewed_products/index.html.twig', [
            'products' => $products->findActiveByIds($ids),
        ]);
    }

    #[Route('/account/viewed-products/clear', name: 'account_viewed_products_clear', methods: ['POST'])]
    public function clear(ViewedProductsService $viewed): Response
    {
        $viewed->clear();
        return $this->redirectToRoute('account_viewed_products');
    }
}
