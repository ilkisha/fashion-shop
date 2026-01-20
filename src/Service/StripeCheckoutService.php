<?php

namespace App\Service;

use App\Entity\Order;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

final readonly class StripeCheckoutService
{
    public function __construct(
        private StripeClient $stripe,
        private string $successUrl,
        private string $cancelUrl,
    ) {}

    /**
     * @throws ApiErrorException
     */
    public function createCheckoutSession(Order $order): Session
    {
        if ($order->getOrderItems()->isEmpty()) {
            throw new \RuntimeException('Cannot create Stripe session for an empty order.');
        }

        $lineItems = [];
        foreach ($order->getOrderItems() as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => strtolower($order->getCurrency()),
                    'product_data' => [
                        'name' => $item->getProductName(),
                    ],
                    'unit_amount' => $item->getUnitPrice(),
                ],
                'quantity' => $item->getQuantity(),
            ];
        }

        return $this->stripe->checkout->sessions->create([
            'mode' => 'payment',
            'line_items' => $lineItems,
            'success_url' => $this->successUrl,
            'cancel_url' => $this->cancelUrl,
            'client_reference_id' => (string) $order->getId(),
            'metadata' => [
                'order_id' => (string) $order->getId(),
            ],
        ]);
    }
}
