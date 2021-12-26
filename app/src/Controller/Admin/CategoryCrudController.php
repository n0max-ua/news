<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            BooleanField::new('is_deleted')
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(BooleanFilter::new('is_deleted'));
    }
}
