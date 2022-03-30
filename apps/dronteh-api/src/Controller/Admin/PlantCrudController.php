<?php

namespace App\Controller\Admin;

use App\Entity\Plant;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use App\Controller\Admin\AbstractUndeleteCrudController;

class PlantCrudController extends AbstractUndeleteCrudController
{
    public static function getEntityFqcn(): string
    {
        return Plant::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return array_merge(parent::configureFields($pageName), [
            TextField::new('name_sr_Latn', 'admin.list.name_sr_Latn'),
            TextField::new('name_hu', 'admin.list.name_hu'),
            TextField::new('name_en', 'admin.list.name_en'),
        ]);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('admin.singular.plant')
            ->setEntityLabelInPlural('admin.plural.plant')
        ;
    }
}
