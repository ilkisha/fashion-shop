<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class StripeWebhookController extends AbstractController
{
    public function __construct(
        private readonly string $stripeWebhookSecret,
    ) {}

    #[Route('/stripe/webhook', name: 'stripe_webhook', methods: ['POST'])]
    public function __invoke(
        Request $request,
        OrderRepository $orders,
        EntityManagerInterface $em,
    ): Response {
        $payload = $request->getContent();
        $sigHeader = (string) $request->headers->get('Stripe-Signature');

        if ($sigHeader === '' || $this->stripeWebhookSecret === '') {
            return new Response('Missing webhook secret/signature', 400);
        }

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $this->stripeWebhookSecret);
        } catch (SignatureVerificationException) {
            return new Response('Invalid signature', 400);
        } catch (\UnexpectedValueException) {
            return new Response('Invalid payload', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            /** @var Session $session */
            $session = $event->data->object;

            $stripeSessionId = (string) ($session->id ?? '');

            if ($stripeSessionId !== '') {
                try {
                    $order = $orders->findOneBy(['stripeSessionId' => $stripeSessionId]);

                    // ✅ idempotent: if is paid, does nothing
                    if ($order && $order->getStatus() !== 'paid') {
                        $order->setStatus('paid');
                        $order->setPaidAt(new \DateTimeImmutable());
                        $em->flush();
                    }
                } catch (\Exception $e) {
                    return new Response('Database error', 500);
                }
            }
        }

        return new Response('OK', 200);
    }
}
