<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    /**Pour configurer le CRUD au niveau titre, ordre affichage etc. */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')
            ->setPaginatorPageSize(5)
            // ...
        ;
    }

    /**
     * Pour configurer les champs de notre entité ex: retirer des champs desactiver la modification dans l'input
     *
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): array
    {
        return [
            IdField::new('ID')
                ->hideOnForm(), //hideForm pour dire quon affiche pas ce champs dans le fomulaire edit
            TextField::new('FullName'),
            TextField::new('Email')
                ->setFormTypeOptions(['disabled' => 'true']),
            TextField::new('Password')
                ->hideOnForm(),
            ArrayField::new('roles'),
            DateTimeField::new('createdAt')
                ->setFormTypeOptions(['disabled' => 'true']),
        ];
    }
}
