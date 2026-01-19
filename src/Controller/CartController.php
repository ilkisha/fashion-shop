<?php

namespace App\Controller;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
    public function add(int $id, CartService $cart, Request $request): RedirectResponse
    {
        $quantity = (int) $request->request->get('quantity', 1);
        $cart->addItem($id, max(1, $quantity));

        $this->addFlash('success', 'Product added to cart successfully.');
        return $this->redirectToRoute('cart_show');
    }

    #[Route('/cart/update/{id}', name: 'cart_update', methods: ['POST'])]
    public function update(int $id, CartService $cart, Request $request): RedirectResponse
    {
        $quantity = (int) $request->request->get('quantity', 1);
        $cart->updateItem($id, $quantity);

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
