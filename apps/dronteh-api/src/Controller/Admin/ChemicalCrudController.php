<?php

namespace App\Controller\Admin;

use App\Entity\Chemical;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Controller\Admin\AbstractUndeleteCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;

class ChemicalCrudController extends AbstractUndeleteCrudController
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public static function getEntityFqcn(): string
    {
        return Chemical::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return array_merge(parent::configureFields($pageName), [
            MoneyField::new('price_per_liter', 'admin.list.chemicals.price_per_liter')->setCurrency('RSD'),
            TextField::new('name_sr_Latn', 'admin.list.name_sr_Latn'),
            TextField::new('name_hu', 'admin.list.name_hu'),
            TextField::new('name_en', 'admin.list.name_en'),
            TextField::new('avgRating', 'admin.singular.rating')->hideOnForm(),
        ]);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('admin.singular.chemical')
            ->setEntityLabelInPlural('admin.plural.chemical')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return parent::configureFilters($filters)
            ->add(NumericFilter::new('price_per_liter', $this->translator->trans('admin.list.chemicals.price_per_liter', [], 'admin')))
        ;
    }
}
