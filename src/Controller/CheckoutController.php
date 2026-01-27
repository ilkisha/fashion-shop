<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\User;
use App\Service\CartService;
use App\Service\Money;
use App\Service\StripeCheckoutService;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Exception\ApiErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * @throws ApiErrorException
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/checkout/create-order', name: 'checkout_create_order', methods: ['POST'])]
    public function createOrder(
        CartService $cart,
        EntityManagerInterface $em,
        StripeCheckoutService $stripeCheckout
    ): Response {
        $summary = $cart->getSummary();

        if ($summary['lines'] === []) {
            return $this->redirectToRoute('cart_show');
        }

        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in to place an order.');
        }

        // Validate stock for all items before creating order
        $stockErrors = [];
        foreach ($summary['lines'] as $line) {
            $product = $line['product'];
            $qty = (int) $line['quantity'];
            if ($product->getStockQuantity() < $qty) {
                $stockErrors[] = "{$product->getName()} (only {$product->getStockQuantity()} available, you requested {$qty})";
            }
        }

        if (!empty($stockErrors)) {
            $this->addFlash('danger', 'Some items are out of stock: ' . implode(', ', $stockErrors));
            return $this->redirectToRoute('cart_show');
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

        $session = $stripeCheckout->createCheckoutSession($order);
        $order->setStripeSessionId($session->id);

        $em->flush();
        return $this->redirect($session->url);

//        $cart->clear();
//
//        $this->addFlash('success', 'Order created successfully!');
//
//        return $this->redirectToRoute('order_success', ['id' => $order->getId()]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/order/success/{id}', name: 'order_success', methods: ['GET'])]
    public function success(int $id): Response
    {
        return $this->render('checkout/success.html.twig', [
            'orderId' => $id,
        ]);
    }

    #[Route('/checkout/success', name: 'checkout_success', methods: ['GET'])]
    public function stripeSuccess(Request $request, OrderRepository $orders, CartService $cart): Response
    {
        $sessionId = $request->query->get('session_id');
        if (!is_string($sessionId) || $sessionId === '') {
            throw $this->createNotFoundException();
        }

        $order = $orders->findOneBy(['stripeSessionId' => $sessionId]);
        if (!$order) {
            throw $this->createNotFoundException();
        }

        // Clear the cart after successful payment
        $cart->clear();

        return $this->render('checkout/stripe_success.html.twig', [
            'order' => $order,
            'sessionId' => $sessionId,
        ]);
    }

    #[Route('/checkout/cancel', name: 'checkout_cancel', methods: ['GET'])]
    public function stripeCancel(): Response
    {
        return $this->render('checkout/stripe_cancel.html.twig');
    }
}
