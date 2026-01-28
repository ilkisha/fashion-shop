<?php

namespace App\Controller;

use App\Service\SearchHistoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/account/search-history', name: 'account_search_history_')]
final class SearchHistoryController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(SearchHistoryService $history): Response
    {
        return $this->render('account/search_history/index.html.twig', [
            'items' => $history->all(),
        ]);
    }

    #[Route('/clear', name: 'clear', methods: ['POST'])]
    public function clear(SearchHistoryService $history): Response
    {
        $history->clear();
        return $this->redirectToRoute('account_search_history_index');
    }
}
