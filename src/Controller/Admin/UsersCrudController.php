<?php

namespace App\Controller\Admin;

use App\Entity\Users;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UsersCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Users::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Utilisateurs')
            ->setEntityLabelInSingular('Utilisateur')

            ->setPaginatorPageSize(15)
            ->setEntityPermission('ROLE_ADMIN')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ;

    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->HideOnForm(),
            TextField::new('pseudo'),
            EmailField::new('email'),
            ArrayField::new('roles')
                ->setHelp('Ne pas effacer ROLE_USER')
                ->hideWhenCreating(),
            DateTimeField::new('created_at', 'Date de création')
                ->hideOnForm(),
            
        ];
    }

    public function createEntity(string $entityFqcn)
    {
        $newPwd = 'Azerty123';
        /* $newPwd = $this->createPassword(); */

        $user = new Users();
            $user 
                ->setPlainPassword($newPwd)
                ->setRoles(['ROLE_USER']);
        

        return $user;

    }

    private function createPassword() {

        $pwdLength = rand(8, 15);
        $char = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charLength = strlen($char);    
        
        $pwd = "";

        for($i = 0; $i < $pwdLength; $i++) {
            $pwd .= $char[rand(0, $charLength - 1)];
        }

        return $pwd;

    }
   
}
