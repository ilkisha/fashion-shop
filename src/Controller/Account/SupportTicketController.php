<?php

namespace App\Controller\Account;

use App\Entity\SupportTicket;
use App\Entity\User;
use App\Form\SupportTicketType;
use App\Repository\SupportTicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/account/support', name: 'account_support_')]
final class SupportTicketController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(SupportTicketRepository $tickets): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('account/support/index.html.twig', [
            'tickets' => $tickets->findBy(
                ['user' => $user],
                ['createdAt' => 'DESC']
            ),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $ticket = new SupportTicket();
        $ticket->setUser($user);

        $form = $this->createForm(SupportTicketType::class, $ticket, [
            'user' => $user,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ticket);
            $em->flush();

            $this->addFlash('success', 'Your message has been sent. We will review it soon.');
            return $this->redirectToRoute('account_support_index');
        }

        return $this->render('account/support/new.html.twig', [
            'form' => $form,
        ]);
    }
}
