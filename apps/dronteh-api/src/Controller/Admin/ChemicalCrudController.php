<?php

namespace App\Controller\Admin;

use App\Entity\Chemical;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ChemicalCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Chemical::class;
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
