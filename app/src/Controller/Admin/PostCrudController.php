<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;

class PostCrudController extends AbstractCrudController
{
    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    /**
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title'),
            TextEditorField::new('content')
                ->hideOnForm(),
            TextField::new('content')
                ->hideOnIndex(),
            AssociationField::new('user')
                ->hideOnForm(),
            AssociationField::new('category'),
            ImageField::new('image')
                ->setBasePath('/uploads')
                ->setUploadDir('public/uploads'),
            NumberField::new('status')->formatValue(function($value) {
                switch ($value) {
                    case 2 :
                        return 'Posted';
                    case 3 :
                        return 'Deleted';
                    default:
                        return 'Created';
                }
            }),
            DateTimeField::new('created_at')
                ->hideOnForm(),
            DateTimeField::new('posted_at')
                ->hideOnForm(),
            DateTimeField::new('deleted_at')
                ->hideOnForm()

        ];
    }

    /**
     * @param Actions $actions
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE);
    }

    /**
     * @param Filters $filters
     * @return Filters
     */
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('user'))
            ->add(EntityFilter::new('category'))
            ->add(NumericFilter::new('status'))
            ->add(DatetimeFilter::new('created_at'))
            ->add(DatetimeFilter::new('posted_at'))
            ->add(DatetimeFilter::new('deleted_at'));
    }
}
