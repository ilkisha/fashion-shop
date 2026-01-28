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

        // Admins see all tickets, regular users see only their own
        if ($this->isGranted('ROLE_ADMIN')) {
            $ticketsList = $tickets->findBy([], ['createdAt' => 'DESC']);
        } else {
            $ticketsList = $tickets->findBy(['user' => $user], ['createdAt' => 'DESC']);
        }

        return $this->render('account/support/index.html.twig', [
            'tickets' => $ticketsList,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        // Prevent admins from creating tickets - they should use admin panel to manage tickets
        if ($this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('warning', 'Admins cannot create support tickets. Please use the admin panel to manage tickets.');
            return $this->redirectToRoute('admin');
        }

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

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id, SupportTicketRepository $tickets): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // Find ticket and ensure it belongs to current user (unless admin)
        $ticket = $tickets->find($id);
        
        if (!$ticket || (!$this->isGranted('ROLE_ADMIN') && $ticket->getUser() !== $user)) {
            throw $this->createNotFoundException('Ticket not found.');
        }

        return $this->render('account/support/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }
}
