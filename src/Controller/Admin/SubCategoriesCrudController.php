<?php

namespace App\Controller\Admin;

use App\Entity\SubCategories;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SubCategoriesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SubCategories::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Sous-catégories')
            ->setEntityLabelInSingular('Sous-catégorie')

            ->setPaginatorPageSize(15)
            ->setEntityPermission('ROLE_ADMIN')
            ;

    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('title', 'Titre'),
            AssociationField::new('category', 'Catégorie')
                ->setCrudController(CategoriesCrudController::class),
            TextEditorField::new('description'),
        ];
    }
}
