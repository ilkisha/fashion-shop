<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Service\CartService;
use App\Service\Money;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CheckoutController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/checkout', name: 'checkout', methods: ['GET'])]
    public function checkout(CartService $cart): Response
    {
        $summary = $cart->getSummary();

        if ($summary['lines'] === []) {
            return $this->redirectToRoute('cart_show');
        }

        return $this->render('checkout/index.html.twig', [
            'lines' => $summary['lines'],
            'total' => $summary['total'],
            'currency' => 'EUR',
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/checkout/create-order', name: 'checkout_create_order', methods: ['POST'])]
    public function createOrder(
        CartService $cart,
        EntityManagerInterface $em
    ): Response {
        $summary = $cart->getSummary();

        if ($summary['lines'] === []) {
            return $this->redirectToRoute('cart_show');
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in to place an order.');
        }

        $order = new Order();
        $order->setUser($user);
        $order->setCurrency('EUR');

        $totalCents = 0;

        foreach ($summary['lines'] as $line) {
            $product = $line['product'];
            $qty = (int) $line['quantity'];

            $unitCents = Money::eurToCents((string) $product->getPrice());
            $lineCents = $unitCents * $qty;

            $item = new OrderItem();
            $item->setOrder($order);
            $item->setProduct($product);
            $item->setProductName($product->getName());
            $item->setUnitPrice($unitCents);
            $item->setQuantity($qty);
            $item->setLineTotal($lineCents);

            $order->addOrderItem($item);

            $totalCents += $lineCents;
        }

        $order->setTotalAmount($totalCents);

        $em->persist($order);
        $em->flush();

        $cart->clear();

        $this->addFlash('success', 'Order created successfully!');

        return $this->redirectToRoute('order_success', ['id' => $order->getId()]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/order/success/{id}', name: 'order_success', methods: ['GET'])]
    public function success(int $id): Response
    {
        return $this->render('checkout/success.html.twig', [
            'orderId' => $id,
        ]);
    }
}
