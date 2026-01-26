<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SearchHistoryService
{
    private const KEY = 'search_history';
    private const LIMIT = 10;

    public function __construct(private readonly RequestStack $requestStack) {}

    public function add(string $query): void
    {
        $query = trim($query);
        if ($query === '') {
            return;
        }

        $session = $this->getSession();
        $items = $session->get(self::KEY, []);

        if (!is_array($items)) {
            $items = [];
        }

        $queryLower = mb_strtolower($query);

        $items = array_values(array_filter($items, static function ($item) use ($queryLower) {
            return is_string($item) && mb_strtolower($item) !== $queryLower;
        }));

        array_unshift($items, $query);

        $items = array_slice($items, 0, self::LIMIT);

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

    private function getSession(): SessionInterface
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            throw new \RuntimeException('No current request available.');
        }

        return $request->getSession();
    }
}
