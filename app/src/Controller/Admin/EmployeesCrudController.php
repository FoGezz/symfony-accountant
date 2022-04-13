<?php

namespace App\Controller\Admin;

use App\Entity\Employees;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class EmployeesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Employees::class;
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
