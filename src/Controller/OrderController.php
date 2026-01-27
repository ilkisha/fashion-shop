<?php

namespace App\Controller;

use App\Dto\OrderFilterDto;
use App\Repository\OrderRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;


#[IsGranted('ROLE_USER')]
#[Route('/account/orders', name: 'account_orders_')]
class OrderController extends AbstractController
{
    private const VALID_STATUSES = ['paid', 'pending'];

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(#[MapQueryString] OrderFilterDto $filter, OrderRepository $orders, LoggerInterface $logger): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        try {
            $orderList = $orders->findForUser($user, $filter->status);
        } catch (\Throwable $e) {
            $logger->error('Failed to load orders', [
                'userId' => $user->getId(),
                'exception' => $e,
            ]);

            $this->addFlash('error', 'Failed to load orders.');

            return $this->redirectToRoute('account_orders_index');
        }

        return $this->render('account/orders/index.html.twig', [
            'orders' => $orderList,
            'status' => $filter->status,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id, OrderRepository $orders, LoggerInterface $logger): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        try {
            $order = $orders->findOneForUser($id, $user);
        } catch (\Throwable $e) {
            $logger->error('Failed to load order', [
                'orderId' => $id,
                'userId' => $user->getId(),
                'exception' => $e,
            ]);

            $this->addFlash('error', 'There was a problem loading the order.');

            return $this->redirectToRoute('account_orders_index');
        }

        return $this->render('account/orders/show.html.twig', [
            'order' => $order,
            'currency' => $order->getCurrency(),
        ]);
    }
}
