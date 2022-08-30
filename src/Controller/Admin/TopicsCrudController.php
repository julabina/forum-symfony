<?php

namespace App\Controller\Admin;

use App\Entity\Topics;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TopicsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Topics::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Topics')
            ->setEntityLabelInSingular('Topic')

            ->setPaginatorPageSize(15)
            ->setEntityPermission('ROLE_ADMIN')
            ;

    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            AssociationField::new('user', 'Créateur')
                ->setCrudController(UsersCrudController::class)
                ->hideOnForm(),
            AssociationField::new('subCategory', 'Sous-catégorie')
                ->setCrudController(SubCategoriesCrudController::class),
            TextField::new('title', 'Titre'),
            TextEditorField::new('main_content', 'Contenu'),
            BooleanField::new('is_active', 'Validé'),
            BooleanField::new('is_pinned', 'Epinglé'),
            BooleanField::new('is_lock', 'Sujet lock'),
            DateTimeField::new('updated_at', 'dernière mise à jour')
                ->hideOnForm()
        ];
    }
}
