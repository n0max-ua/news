<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('email'),
            TextField::new('name'),
            TextField::new('surname'),
            ChoiceField::new('roles')
                ->setChoices(User::getPossibleRoles())
                ->allowMultipleChoices(),
            ImageField::new('photo')
                ->setBasePath('/uploads')
                ->setUploadDir('public/uploads'),
            BooleanField::new('isVerified')
                ->onlyWhenCreating(),
            Field::new('password')
                ->setFormType(PasswordType::class)
                ->onlyWhenCreating()
        ];
    }
}
