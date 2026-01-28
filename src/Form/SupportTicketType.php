<?php

namespace App\Form;

use App\Entity\Order;
use App\Entity\SupportTicket;
use App\Entity\User;
use App\Repository\OrderRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\User\UserInterface;

class SupportTicketType extends AbstractType
{
    public function __construct(private readonly OrderRepository $orders) {}
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var UserInterface $user */
        $user = $options['user'];

        $builder
            ->add('subject', ChoiceType::class, [
                'choices' => [
                    'Order issue' => SupportTicket::SUBJECT_ORDER,
                    'Delivery issue' => SupportTicket::SUBJECT_DELIVERY,
                    'Product quality' => SupportTicket::SUBJECT_QUALITY,
                    'Other' => SupportTicket::SUBJECT_OTHER,
                ],
                'placeholder' => 'Choose a subject',
            ])
            ->add('order', EntityType::class, [
                'class' => Order::class,
                'required' => false,
                'placeholder' => 'No related order (optional)',
                'choices' => $this->orders->findForUser($user),
                'choice_label' => function (Order $order) {
                    return sprintf(
                        '#%d • %s • %s',
                        $order->getId(),
                        $order->getCreatedAt()->format('Y-m-d'),
                        $order->getStatus()
                    );
                },
            ])
            ->add('message', TextareaType::class, [
                'attr' => ['rows' => 6],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SupportTicket::class,
            'user' => null,
        ]);

        $resolver->setAllowedTypes('user', ['null', UserInterface::class]);
    }
}
