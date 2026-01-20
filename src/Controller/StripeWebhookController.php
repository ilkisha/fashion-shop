<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Webhook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class StripeWebhookController extends AbstractController
{
    #[Route('/stripe/webhook', name: 'stripe_webhook', methods: ['POST'])]
    public function __invoke(
        Request $request,
        OrderRepository $orders,
        EntityManagerInterface $em,
    ): Response {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('Stripe-Signature');

        $endpointSecret = $_ENV['STRIPE_WEBHOOK_SECRET'] ?? null;
        if (!$endpointSecret || !$sigHeader) {
            return new Response('Missing webhook secret/signature', 400);
        }

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\Throwable $e) {
            return new Response('Invalid signature', 400);
        }

        // checkout.session.completed = успешен завършен Checkout Session
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            $stripeSessionId = $session->id ?? null;
            if ($stripeSessionId) {
                $order = $orders->findOneBy(['stripeSessionId' => $stripeSessionId]);
                if ($order && $order->getPaidAt() === null) {
                    $order->setStatus('paid');
                    $order->setPaidAt(new \DateTimeImmutable());
                    $em->flush();
                }
            }
        }

        return new Response('ok', 200);
    }
}
