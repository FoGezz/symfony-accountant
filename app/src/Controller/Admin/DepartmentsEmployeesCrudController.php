<?php

namespace App\Controller\Admin;

use App\Entity\DepartmentsEmployees;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class DepartmentsEmployeesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DepartmentsEmployees::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
