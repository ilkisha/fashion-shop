<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class CartService
{
    private const CART_SESSION_KEY = '_cart';
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly ProductRepository $products
    ) {}

    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }

    /**
     * Retrieves the items currently in the cart.
     *
     * @return array<int,int> productId => quantity
     */
    public function getItems(): array
    {
        $items = $this->getSession()->get(self::CART_SESSION_KEY, []);

        if (!is_array($items)) {
            $items = [];
        }

        $normalizedItems = [];
        foreach ($items as $productId => $quantity) {
            $productId = (int) $productId;
            $quantity = (int) $quantity;

            if ($productId > 0 && $quantity > 0) {
                $normalizedItems[$productId] = $quantity;
            }
        }

        return $normalizedItems;
    }

    public function addItem(int $productId, int $quantity = 1): void
    {
        if ($quantity <= 0) {
            return;
        }

        $items = $this->getItems();
        $items[$productId] = ($items[$productId] ?? 0) + $quantity;
        $this->getSession()->set(self::CART_SESSION_KEY, $items);
    }

    public function updateItem(int $productId, int $quantity): void
    {
        $items = $this->getItems();

        if ($quantity <= 0) {
            unset($items[$productId]);
        } else {
            $items[$productId] = $quantity;
        }

        $this->getSession()->set(self::CART_SESSION_KEY, $items);
    }

    public function removeItem(int $productId): void
    {
        $items = $this->getItems();
        unset($items[$productId]);
        $this->getSession()->set(self::CART_SESSION_KEY, $items);
    }

    public function clear(): void
    {
        $this->getSession()->remove(self::CART_SESSION_KEY);
    }

    /**
     * @return array{lines: array<int,array{product:Product, quantity:int, lineTotal: string}>, total: string}
     */
    public function getSummary(): array
    {
        $items = $this->getItems();
        if ($items === []) {
            return ['lines' => [], 'total' => '0.00'];
        }

        $products = $this->products->findBy(['id' => array_keys($items)]);
        $byId = [];
        foreach ($products as $p) {
            $byId[$p->getId()] = $p;
        }

        $lines = [];
        $total = '0.00';

        foreach ($items as $productId => $qty) {
            $product = $byId[$productId] ?? null;

            if (!$product || !$product->isActive()) {
                $this->removeItem($productId);
                continue;
            }

            $lineTotal = bcmul($product->getPrice(), (string)$qty, 2);
            $total = bcadd($total, $lineTotal, 2);

            $lines[] = [
                'product' => $product,
                'quantity' => $qty,
                'lineTotal' => $lineTotal,
            ];
        }

        return ['lines' => $lines, 'total' => $total];
    }
}
