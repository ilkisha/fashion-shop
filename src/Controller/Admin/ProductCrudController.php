<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(), // Скрий ID при създаване/редактиране
            TextField::new('name')
                ->setRequired(true),
            TextField::new('slug')
                ->setRequired(true)
                ->hideOnIndex(), // Скрий slug в списъка
            TextEditorField::new('description')
                ->hideOnIndex(), // Скрий description в списъка (за по-компактен списък)
            MoneyField::new('price')
                ->setCurrency('EUR')
                ->setRequired(true),
            ChoiceField::new('gender')
                ->setChoices([
                    'Male' => 'male',
                    'Female' => 'female'
                ])
                ->setRequired(true),
            TextField::new('category')
                ->setRequired(true),
            IntegerField::new('stockQuantity')
                ->setRequired(true),
            BooleanField::new('isActive')
                ->setRequired(true),
        ];
    }
}
