<?php

namespace App\Twig;

use App\Service\CartService;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

/**
 * Provides global Twig variables for cart information.
 */
class CartExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(private readonly CartService $cart) {}

    public function getGlobals(): array
    {
        $items = $this->cart->getItems();
        $count = array_sum($items); // Total quantity of all items

        return [
            'cart_count' => $count,
        ];
    }
}
