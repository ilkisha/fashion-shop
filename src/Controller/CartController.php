<?php

namespace App\Controller;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

final class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart_show', methods: ['GET'])]
    public function show(CartService $cart): Response
    {
        $summary = $cart->getSummary();

        return $this->render('cart/show.html.twig', [
            'lines' => $summary['lines'],
            'total' => $summary['total'],
            'currency' => 'EUR'
        ]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add', methods: ['POST'])]
    public function add(int $id, CartService $cart, Request $request, \App\Repository\ProductRepository $productRepo): RedirectResponse
    {
        $product = $productRepo->find($id);

        // Validate product exists and is active
        if (!$product || !$product->isActive()) {
            $this->addFlash('danger', 'Product not found or unavailable.');
            return $this->redirectToRoute('catalog_index');
        }

        $quantity = (int) $request->request->get('quantity', 1);
        $quantity = max(1, $quantity);

        // Check current cart quantity for this product
        $currentCartQty = $cart->getItems()[$id] ?? 0;
        $totalRequested = $currentCartQty + $quantity;

        // Validate stock availability
        if ($product->getStockQuantity() < $totalRequested) {
            $available = $product->getStockQuantity() - $currentCartQty;
            if ($available <= 0) {
                $this->addFlash('danger', 'Sorry, this product is out of stock.');
            } else {
                $this->addFlash('warning', "Only {$available} more available. You already have {$currentCartQty} in your cart.");
            }
            return $this->redirectToRoute('catalog_show', ['slug' => $product->getSlug()]);
        }

        $cart->addItem($id, $quantity);

        $this->addFlash('success', 'Product added to cart successfully.');
        return $this->redirectToRoute('cart_show');
    }

    #[Route('/cart/update/{id}', name: 'cart_update', methods: ['POST'])]
    public function update(int $id, CartService $cart, Request $request): Response
    {
        $quantity = (int) $request->request->get('qty', 1);
        $cart->updateItem($id, $quantity);

        $wantsJson =
            $request->isXmlHttpRequest()
            || str_contains((string) $request->headers->get('Accept'), 'application/json');

        if ($wantsJson) {
            $summary = $cart->getSummary();

            $lineTotal = '0.00';
            foreach ($summary['lines'] as $line) {
                if ($line['product']->getId() === $id) {
                    $lineTotal = $line['lineTotal'];
                    break;
                }
            }

            return new JsonResponse([
                'ok' => true,
                'productId' => $id,
                'qty' => $quantity,
                'lineTotal' => $lineTotal,
                'cartTotal' => $summary['total'],
                'isRemoved' => $quantity <= 0,
            ]);
        }

        // fallback if JS is not available
        return $this->redirectToRoute('cart_show');
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove', methods: ['POST'])]
    public function remove(int $id, CartService $cart, Request $request): RedirectResponse
    {
        $cart->removeItem($id);

        return $this->redirectToRoute('cart_show');
    }

    #[Route('/cart/clear', name: 'cart_clear', methods: ['POST'])]
    public function clear(CartService $cart): RedirectResponse
    {
        $cart->clear();

        return $this->redirectToRoute('cart_show');
    }
}
