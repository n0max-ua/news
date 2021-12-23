<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

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
            NumberField::new('status')->formatValue(function ($value) {
                switch ($value){
                    case 2 :
                        return 'posted';
                    case 3 :
                        return 'deleted';
                    default:
                        return 'created';
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

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE);
    }
}
