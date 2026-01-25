<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;


#[IsGranted('ROLE_USER')]
#[Route('/account/orders', name: 'account_orders_')]
class OrderController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(Request $request, OrderRepository $orders): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $status = $request->query->get('status');
        if (!in_array($status, ['paid', 'pending'], true)) {
            $status = null;
        }

        return $this->render('account/orders/index.html.twig', [
            'orders' => $orders->findForUser($user, $status),
            'status' => $status,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id, OrderRepository $orders): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $order = $orders->findOneForUser($id, $user);

        return $this->render('account/orders/show.html.twig', [
            'order' => $order,
            'currency' => $order->getCurrency(),
        ]);
    }
}
