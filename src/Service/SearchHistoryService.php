<?php

namespace App\Service;

use RuntimeException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SearchHistoryService
{
    private const KEY = 'search_history';
    private const LIMIT = 10;

    public function __construct(private readonly RequestStack $requestStack) {}

    public function add(string $query): void
    {
        $query = $this->normalizeQuery($query);
        if ($query === '') {
            return;
        }

        $session = $this->getSession();

        $items = $this->getCurrentHistory($session);
        $items = $this->removeDuplicate($items, $query);
        $items = $this->addToFront($items, $query);
        $items = $this->limitHistory($items);

        $session->set(self::KEY, $items);
    }

    /**
     * @return string[]
     */
    public function all(): array
    {
        $items = $this->getSession()->get(self::KEY, []);
        if (!is_array($items)) {
            return [];
        }

        return array_values(array_filter($items, 'is_string'));
    }

    public function clear(): void
    {
        $this->getSession()->remove(self::KEY);
    }

    private function normalizeQuery(string $query): string
    {
        return trim($query);
    }

    private function getCurrentHistory(SessionInterface $session): array
    {
        $items = $session->get(self::KEY, []);
        return is_array($items) ? $items : [];
    }

    private function removeDuplicate(array $items, string $query): array
    {
        $queryLower = mb_strtolower($query);
        $filtered = [];

        foreach ($items as $item) {
            if (is_string($item) && mb_strtolower($item) !== $queryLower) {
                $filtered[] = $item;
            }
        }

        return $filtered;
    }

    private function addToFront(array $items, string $query): array
    {
        array_unshift($items, $query);
        return $items;
    }

    private function limitHistory(array $items): array
    {
        return count($items) > self::LIMIT
            ? array_slice($items, 0, self::LIMIT)
            : $items;
    }

    private function getSession(): SessionInterface
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            throw new RuntimeException('No current request available.');
        }

        return $request->getSession();
    }
}
