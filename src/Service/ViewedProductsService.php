<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class ViewedProductsService
{
    private const KEY = 'viewed_products';
    private const LIMIT = 10;

    public function __construct(private readonly RequestStack $requestStack) {}

    public function add(int $productId): void
    {
        if ($productId <= 0) {
            return;
        }

        $session = $this->getSession();
        $items = $session->get(self::KEY, []);

        if (!is_array($items)) {
            $items = [];
        }

        // normalize to ints
        $items = array_values(array_filter(array_map('intval', $items), fn ($id) => $id > 0));

        // remove duplicates
        $items = array_values(array_filter($items, fn ($id) => $id !== $productId));

        // add to front
        array_unshift($items, $productId);

        // limit
        $items = array_slice($items, 0, self::LIMIT);

        $session->set(self::KEY, $items);
    }

    /** @return int[] */
    public function all(): array
    {
        $items = $this->getSession()->get(self::KEY, []);
        if (!is_array($items)) {
            return [];
        }

        $items = array_values(array_filter(array_map('intval', $items), fn ($id) => $id > 0));
        return array_slice($items, 0, self::LIMIT);
    }

    public function clear(): void
    {
        $this->getSession()->remove(self::KEY);
    }

    private function getSession(): SessionInterface
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request || !$request->hasSession()) {
            throw new \RuntimeException('Session is not available.');
        }

        return $request->getSession();
    }
}
