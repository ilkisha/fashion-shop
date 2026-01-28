<?php

namespace App\Controller\Admin;

use App\Entity\SupportTicket;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

final class SupportTicketCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SupportTicket::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        // Tickets are created by users; admin only reviews/updates status/admin note.
        return $actions
            ->disable(Action::NEW);
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id')->hideOnForm();

        $user = AssociationField::new('user'); // show on index/detail, not on form
        $order = AssociationField::new('order')->setRequired(false);

        $subject = ChoiceField::new('subject')->setChoices([
            'Order issue' => SupportTicket::SUBJECT_ORDER,
            'Delivery issue' => SupportTicket::SUBJECT_DELIVERY,
            'Product quality' => SupportTicket::SUBJECT_QUALITY,
            'Other' => SupportTicket::SUBJECT_OTHER,
        ]);

        $message = TextareaField::new('message');

        $status = ChoiceField::new('status')->setChoices([
            'Open' => SupportTicket::STATUS_OPEN,
            'In progress' => SupportTicket::STATUS_IN_PROGRESS,
            'Closed' => SupportTicket::STATUS_CLOSED,
        ]);

        $adminNote = TextareaField::new('adminNote')->setRequired(false);

        $createdAt = DateTimeField::new('createdAt')->hideOnForm();
        $updatedAt = DateTimeField::new('updatedAt')->hideOnForm();

        // EDIT PAGE: admin can only update status + admin note
        if ($pageName === Crud::PAGE_EDIT) {
            return [$id, $status, $adminNote];
        }

        // INDEX PAGE
        if ($pageName === Crud::PAGE_INDEX) {
            return [
                $id,
                $user,
                $order,
                $subject,
                $status,
                $createdAt,
                $updatedAt,
            ];
        }

        // DETAIL PAGE (view everything)
        return [
            $id,
            $user,
            $order,
            $subject,
            $message,
            $status,
            $adminNote,
            $createdAt,
            $updatedAt,
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof SupportTicket) {
            $entityInstance->setUpdatedAt(new \DateTimeImmutable());
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof SupportTicket) {
            $entityInstance->setUpdatedAt(new \DateTimeImmutable());
        }

        parent::updateEntity($entityManager, $entityInstance);
    }
}
